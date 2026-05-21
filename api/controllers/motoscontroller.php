<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.unidades.php';
require_once __DIR__ . '/../../library/class.modelos.php';
require_once __DIR__ . '/../../library/class.colores.php';
require_once __DIR__ . '/../../library/class.ubicaciones.php';
require_once __DIR__ . '/../../library/class.estadosunidad.php';
require_once __DIR__ . '/../../library/class.minutas.php';
require_once __DIR__ . '/../../library/class.clientes.php';
require_once __DIR__ . '/../../library/class.marcas.php';

class MotosController
{
    /**
     * Nombre exacto de la marca a filtrar (como aparece en TB_Marcas.Nombre).
     * Verificado contra la BD: IdMarca 40, Codigo CFM, Nombre "CF MOTO".
     */
    const MARCA_FILTRO = 'CF MOTO';

    /**
     * GET /motos
     * Requiere: Authorization: Bearer {token}
     *
     * Por defecto devuelve solo motos de la marca configurada en MARCA_FILTRO
     * y que esten en estado STOCK.
     *
     * Query params opcionales:
     *   ?interno=XX  -> filtra por IdUnidad (ignora filtros por defecto)
     *   ?id=XX       -> filtra por IdUnidad (alias de interno)
     *
     * Formato de respuesta:
     *   { datos: [...] }
     */
    public function index($body, $query, $params)
    {
        try {
            $token = JWT::fromHeader();
            JWT::validate($token);
        } catch (JWTException $e) {
            return Response::forGiven($e->getCode(), false, $e->getMessage());
        }

        $oUnidades    = new Unidades();
        $oModelos     = new Modelos();
        $oColores     = new Colores();
        $oUbicaciones = new Ubicaciones();
        $oEstados     = new EstadosUnidad();
        $oMinutas     = new Minutas();
        $oClientes    = new Clientes();
        $oMarcas      = new Marcas();

        // Filtro por interno o id (alias) - si viene, se ignoran los filtros por defecto
        $idFiltro = null;
        if (!empty($query['interno'])) $idFiltro = (int)$query['interno'];
        if (!empty($query['id']))      $idFiltro = (int)$query['id'];

        if ($idFiltro) {
            // Busqueda puntual: devolvemos la unidad sea cual sea su marca/estado
            $oUnidad = $oUnidades->GetById($idFiltro);
            $arrUnidades = $oUnidad ? array($oUnidad) : array();
        } else {
            // Listado general: aplicamos filtros por defecto (CF MOTO + Stock)
            $filter = array();

            // Buscamos la marca: primero por nombre exacto, luego aproximado
            $oMarcaCF = $oMarcas->GetByNombreExacto(self::MARCA_FILTRO);
            if (!$oMarcaCF) {
                $oMarcaCF = $oMarcas->GetByNombre(self::MARCA_FILTRO);
            }

            if ($oMarcaCF && $oMarcaCF->IdMarca) {
                $filter['IdMarca'] = $oMarcaCF->IdMarca;
            } else {
                // Si no se encuentra la marca, devolvemos lista vacia
                // (mejor que traer todas las marcas por error)
                return Response::forGiven(200, true,
                    'No se encontro la marca "' . self::MARCA_FILTRO . '" en el sistema.',
                    array('datos' => array())
                );
            }

            // Filtro por estado STOCK
            $filter['IdEstado'] = EstadoUnidad::Stock;

            $arrUnidades = $oUnidades->GetAll($filter);
        }

        if ($arrUnidades === false) {
            return Response::forGiven(500, false, 'Error al obtener las motos.');
        }

        if (empty($arrUnidades)) {
            return Response::forGiven(200, true, 'No se encontraron motos.', array(
                'datos' => array(),
            ));
        }

        // Cache en memoria para evitar N+1
        $cacheModelos     = array();
        $cacheColores     = array();
        $cacheUbicaciones = array();
        $cacheEstados     = array();
        $cacheClientes    = array();

        $result = array();

        foreach ($arrUnidades as $oUnidad) {

            // Modelo (necesario tambien para fallback de precio)
            $oModelo = null;
            $denominacion = null;
            if ($oUnidad->IdModelo) {
                if (!array_key_exists($oUnidad->IdModelo, $cacheModelos)) {
                    $cacheModelos[$oUnidad->IdModelo] = $oModelos->GetById($oUnidad->IdModelo);
                }
                $oModelo = $cacheModelos[$oUnidad->IdModelo];
                if ($oModelo) $denominacion = $oModelo->DenominacionComercial;
            }

            // Color
            $color = null;
            if ($oUnidad->IdColor) {
                if (!array_key_exists($oUnidad->IdColor, $cacheColores)) {
                    $cacheColores[$oUnidad->IdColor] = $oColores->GetById($oUnidad->IdColor);
                }
                $oColor = $cacheColores[$oUnidad->IdColor];
                if ($oColor) $color = $oColor->Nombre;
            }

            // Ubicacion
            $ubicacion = null;
            if ($oUnidad->IdUbicacion) {
                if (!array_key_exists($oUnidad->IdUbicacion, $cacheUbicaciones)) {
                    $cacheUbicaciones[$oUnidad->IdUbicacion] = $oUbicaciones->GetById($oUnidad->IdUbicacion);
                }
                $oUbicacion = $cacheUbicaciones[$oUnidad->IdUbicacion];
                if ($oUbicacion) $ubicacion = $oUbicacion->Nombre;
            }

            // Estado
            $estado = null;
            if ($oUnidad->IdEstado) {
                if (!array_key_exists($oUnidad->IdEstado, $cacheEstados)) {
                    $cacheEstados[$oUnidad->IdEstado] = $oEstados->GetById($oUnidad->IdEstado);
                }
                $oEstado = $cacheEstados[$oUnidad->IdEstado];
                if ($oEstado) $estado = $oEstado->Nombre;
            }

            // Cliente (via Minuta)
            $cliente = null;
            try {
                $oMinuta = $oMinutas->GetByUnidad($oUnidad);
                if ($oMinuta && $oMinuta->IdCliente) {
                    if (!array_key_exists($oMinuta->IdCliente, $cacheClientes)) {
                        $cacheClientes[$oMinuta->IdCliente] = $oClientes->GetById($oMinuta->IdCliente);
                    }
                    $oCliente = $cacheClientes[$oMinuta->IdCliente];
                    if ($oCliente) $cliente = $oCliente->RazonSocial;
                }
            } catch (Exception $e) {
                $cliente = null;
            }

            // Precio con fallback:
            //   1) Si la unidad tiene PrecioUnidad > 0, se usa ese.
            //   2) Si no, se usa Precio1 (contado) del modelo.
            //   3) Si tampoco, 0.
            $precio = 0;
            if ($oUnidad->PrecioUnidad && (float)$oUnidad->PrecioUnidad > 0) {
                $precio = (float)$oUnidad->PrecioUnidad;
            } elseif ($oModelo && $oModelo->Precio1 && (float)$oModelo->Precio1 > 0) {
                $precio = (float)$oModelo->Precio1;
            }

            $result[] = array(
                'interno'      => (int)$oUnidad->IdUnidad,
                'nro_vin'      => $oUnidad->NumeroVinPrefijo . $oUnidad->NumeroVin,
                'denominacion' => $denominacion,
                'color'        => $color,
                'ubicacion'    => $ubicacion,
                'año'          => (int)$oUnidad->Anio,
                'nro_pedido'   => $oUnidad->NumeroPedido,
                'cliente'      => $cliente,
                'estado'       => $estado,
                'precio'       => $precio,
                'acreditado'   => (bool)$oUnidad->Cancelada,
            );
        }

        return Response::forGiven(200, true, 'Motos obtenidas correctamente.', array(
            'datos' => $result,
        ));
    }
}