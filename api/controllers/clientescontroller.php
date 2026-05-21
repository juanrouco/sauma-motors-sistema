<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.clientes.php';
require_once __DIR__ . '/../../library/class.usuarios.php';
require_once __DIR__ . '/../../library/class.tiposiva.php';
require_once __DIR__ . '/../../library/class.profesiones.php';
require_once __DIR__ . '/../../library/class.localidades.php';
require_once __DIR__ . '/../../library/class.personatipos.php';

class ClientesController
{
    /**
     * GET /clientes
     * Requiere: Authorization: Bearer {token}
     *
     * Query params opcionales:
     *   ?cuit=XX   -> filtra por ClaveFiscalNumero
     *   ?email=XX  -> filtra por Email
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

        $filter = array();
        if (!empty($query['cuit']))  $filter['ClaveFiscalNumero'] = trim($query['cuit']);
        if (!empty($query['email'])) $filter['Email']             = trim($query['email']);

        $oClientes    = new Clientes();
        $oUsuarios    = new Usuarios();
        $oTiposIva    = new TiposIva();
        $oProfesiones = new Profesiones();
        $oLocalidades = new Localidades();

        $arrClientes = $oClientes->GetAll($filter ?: null);

        if ($arrClientes === false) {
            return Response::forGiven(500, false, 'Error al obtener los clientes.');
        }

        if (empty($arrClientes)) {
            return Response::forGiven(200, true, 'No se encontraron clientes.', array(
                'datos' => array(),
            ));
        }

        // Cache en memoria para evitar N+1
        $cacheUsuarios    = array();
        $cacheTiposIva    = array();
        $cacheProfesiones = array();
        $cacheLocalidades = array();

        $result = array();

        foreach ($arrClientes as $oCliente) {

            // Vendedor
            $vendedor = array('id' => null, 'nombre' => null);
            if ($oCliente->IdVendedor) {
                if (!array_key_exists($oCliente->IdVendedor, $cacheUsuarios)) {
                    $cacheUsuarios[$oCliente->IdVendedor] = $oUsuarios->GetById($oCliente->IdVendedor);
                }
                $oVendedor = $cacheUsuarios[$oCliente->IdVendedor];
                if ($oVendedor) {
                    $vendedor = array(
                        'id'     => (int)$oVendedor->IdUsuario,
                        'nombre' => trim($oVendedor->Nombre . ' ' . $oVendedor->Apellido),
                    );
                }
            }

            // Condicion IVA
            $condicionIva = array('descripcion' => null, 'codigo' => null);
            if ($oCliente->IdTipoIva) {
                if (!array_key_exists($oCliente->IdTipoIva, $cacheTiposIva)) {
                    $cacheTiposIva[$oCliente->IdTipoIva] = $oTiposIva->GetById($oCliente->IdTipoIva);
                }
                $oTipoIva = $cacheTiposIva[$oCliente->IdTipoIva];
                if ($oTipoIva) {
                    $condicionIva = array(
                        'descripcion' => $oTipoIva->Nombre,
                        'codigo'      => (int)$oTipoIva->IdTipoIva,
                    );
                }
            }

            // Profesion
            $profesion = array('descripcion' => null, 'codigo' => null);
            if ($oCliente->IdProfesion) {
                if (!array_key_exists($oCliente->IdProfesion, $cacheProfesiones)) {
                    $cacheProfesiones[$oCliente->IdProfesion] = $oProfesiones->GetById($oCliente->IdProfesion);
                }
                $oProfesion = $cacheProfesiones[$oCliente->IdProfesion];
                if ($oProfesion) {
                    $profesion = array(
                        'descripcion' => $oProfesion->Nombre,
                        'codigo'      => (int)$oProfesion->IdProfesion,
                    );
                }
            }

            // Localidad fiscal
            $localidadFiscal = null;
            if ($oCliente->DomicilioIdLocalidad) {
                if (!array_key_exists($oCliente->DomicilioIdLocalidad, $cacheLocalidades)) {
                    $cacheLocalidades[$oCliente->DomicilioIdLocalidad] = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
                }
                $oLocalidad = $cacheLocalidades[$oCliente->DomicilioIdLocalidad];
                if ($oLocalidad) $localidadFiscal = $oLocalidad->Nombre;
            }

            // Localidad postal
            $localidadPostal = null;
            if ($oCliente->DomicilioIdLocalidadPostal) {
                if (!array_key_exists($oCliente->DomicilioIdLocalidadPostal, $cacheLocalidades)) {
                    $cacheLocalidades[$oCliente->DomicilioIdLocalidadPostal] = $oLocalidades->GetById($oCliente->DomicilioIdLocalidadPostal);
                }
                $oLocalidad = $cacheLocalidades[$oCliente->DomicilioIdLocalidadPostal];
                if ($oLocalidad) $localidadPostal = $oLocalidad->Nombre;
            }

            // Tipo persona
            $tipoPersona = PersonaTipos::GetById($oCliente->IdTipoPersona);

            $result[] = array(
                'tipo_persona'                  => $tipoPersona,
                'empresa'                       => $oCliente->Empresa,
                'razon_social_apellido_nombres' => $oCliente->RazonSocial,
                'cuit_cuil'                     => $oCliente->ClaveFiscalNumero,
                'telefono'                      => array(
                    'cod_area' => $oCliente->TelefonoCodigoArea,
                    'numero'   => $oCliente->Telefono,
                ),
                'email'                         => $oCliente->Email,
                'fax'                           => array(
                    'cod_area' => $oCliente->FaxCodigoArea,
                    'numero'   => $oCliente->Fax,
                ),
                'vendedor'                      => $vendedor,
                'condicion_iva'                 => $condicionIva,
                'profesion'                     => $profesion,
                'domicilio_fiscal'              => array(
                    'calle'     => $oCliente->DomicilioCalle,
                    'numero'    => $oCliente->DomicilioNumero,
                    'piso'      => $oCliente->DomicilioPiso,
                    'dpto'      => $oCliente->DomicilioDpto,
                    'localidad' => $localidadFiscal,
                    'codigo'    => $oCliente->DomicilioCodigoPostal,
                ),
                'domicilio_postal'              => array(
                    'calle'     => $oCliente->DomicilioCallePostal,
                    'numero'    => $oCliente->DomicilioNumeroPostal,
                    'piso'      => $oCliente->DomicilioPisoPostal,
                    'dpto'      => $oCliente->DomicilioDptoPostal,
                    'localidad' => $localidadPostal,
                    'codigo'    => $oCliente->DomicilioCodigoPostalPostal,
                ),
            );
        }

        return Response::forGiven(200, true, 'Clientes obtenidos correctamente.', array(
            'datos' => $result,
        ));
    }
}