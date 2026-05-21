<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.articulos.php';
require_once __DIR__ . '/../../library/class.proveedores.php';
require_once __DIR__ . '/../../library/class.ivas.php';
require_once __DIR__ . '/../../library/class.articulostocks.php';

class RepuestosController
{
    /**
     * GET /repuestos
     * Requiere: Authorization: Bearer {token}
     *
     * Por defecto devuelve solo repuestos con stock_actual > 0.
     *
     * Query params opcionales:
     *   ?codigo=XX           -> filtra por codigo
     *   ?id=XX               -> filtra por IdArticulo (trae aunque no tenga stock)
     *   ?incluir_sin_stock=1 -> incluye tambien los que tienen stock 0 o negativo
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

        $oArticulos      = new Articulos();
        $oProveedores    = new Proveedores();
        $oIvas           = new Ivas();
        $oArticuloStocks = new ArticuloStocks();

        // Flags de comportamiento
        $incluirSinStock = !empty($query['incluir_sin_stock']);
        $busquedaPorId   = !empty($query['id']);

        // Filtros
        $filter = array();
        if (!empty($query['codigo'])) $filter['Codigo'] = trim($query['codigo']);

        if ($busquedaPorId) {
            // Busqueda puntual: devolvemos el articulo aunque no tenga stock
            $oArticulo = $oArticulos->GetById((int)$query['id']);
            $arrArticulos = $oArticulo ? array($oArticulo) : array();
        } else {
            $arrArticulos = $oArticulos->GetAll($filter ?: null);
        }

        if ($arrArticulos === false) {
            return Response::forGiven(500, false, 'Error al obtener los repuestos.');
        }

        if (empty($arrArticulos)) {
            return Response::forGiven(200, true, 'No se encontraron repuestos.', array(
                'datos' => array(),
            ));
        }

        // Pre-cargamos todos los stocks en una sola query y los agrupamos por
        // IdArticulo. Asi evitamos hacer una query por cada repuesto (N+1).
        $stockPorArticulo = array();
        $arrTodosLosStocks = $oArticuloStocks->GetAll();
        if (is_array($arrTodosLosStocks)) {
            foreach ($arrTodosLosStocks as $oStock) {
                if (!isset($stockPorArticulo[$oStock->IdArticulo])) {
                    $stockPorArticulo[$oStock->IdArticulo] = 0;
                }
                $stockPorArticulo[$oStock->IdArticulo] += (float)$oStock->StockActual;
            }
        }

        // Cache en memoria para proveedores e IVAs
        $cacheProveedores = array();
        $cacheIvas        = array();

        $result = array();

        foreach ($arrArticulos as $oArticulo) {

            // Stock actual (suma de todas las ubicaciones)
            $stockActual = isset($stockPorArticulo[$oArticulo->IdArticulo])
                ? (float)$stockPorArticulo[$oArticulo->IdArticulo]
                : 0;

            // Si no se pide incluir sin stock y no es busqueda por id, saltamos
            // los que no tienen stock disponible.
            if (!$incluirSinStock && !$busquedaPorId && $stockActual <= 0) {
                continue;
            }

            // Proveedor
            $proveedor = null;
            if ($oArticulo->IdProveedor) {
                if (!array_key_exists($oArticulo->IdProveedor, $cacheProveedores)) {
                    $cacheProveedores[$oArticulo->IdProveedor] = $oProveedores->GetById($oArticulo->IdProveedor);
                }
                $oProveedor = $cacheProveedores[$oArticulo->IdProveedor];
                if ($oProveedor) $proveedor = $oProveedor->Empresa;
            }

            // IVA
            $ivaNombre    = null;
            $precioConIva = null;
            if ($oArticulo->IdIva) {
                if (!array_key_exists($oArticulo->IdIva, $cacheIvas)) {
                    $cacheIvas[$oArticulo->IdIva] = $oIvas->GetById($oArticulo->IdIva);
                }
                $oIva = $cacheIvas[$oArticulo->IdIva];
                if ($oIva) {
                    $ivaNombre    = ($oIva->Alicuota * 100) . '%';
                    $precioConIva = round($oArticulo->PrecioLista * (1 + $oIva->Alicuota), 2);
                }
            }

            $result[] = array(
                'codigo'                => $oArticulo->Codigo,
                'descripcion'           => html_entity_decode($oArticulo->Descripcion),
                'reemplazo'             => $oArticulo->Reemplazo,
                'precio_sugerido_c_iva' => $precioConIva,
                'precio_sugerido_s_iva' => (float)$oArticulo->PrecioLista,
                'precio_costo'          => (float)$oArticulo->PrecioCompra,
                'iva'                   => $ivaNombre,
                'proveedor'             => $proveedor,
                'unidad_de_venta'       => (int)$oArticulo->UnidadVenta,
                'stock_actual'          => $stockActual,
                'stock_maximo'          => (int)$oArticulo->StockMaximo,
                'stock_minimo'          => (int)$oArticulo->StockMinimo,
                'utilidad'              => $oArticulo->Utilidad,
            );
        }

        return Response::forGiven(200, true, 'Repuestos obtenidos correctamente.', array(
            'datos' => $result,
        ));
    }
}