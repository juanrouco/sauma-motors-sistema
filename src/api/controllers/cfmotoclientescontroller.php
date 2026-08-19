<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.cfmoto.php';

/**
 * Endpoints CFMOTO - Clientes / Contactos.
 *
 * GET  /sync/clientes?orden_id=X   -> cliente (con sus motos) de esa orden de trabajo
 * GET  /sync/clientes?id=X         -> cliente por IdCliente (acepta id_externo CLI-...)
 * POST /sync/clientes              -> alta/actualizacion de clientes (formato spec seccion 1)
 * POST /webhook/contactos          -> mismo procesamiento, con bloque evento (spec seccion 4)
 *
 * El POST es idempotente: si el cliente existe (por id_externo, documento o
 * email) se actualizan los datos que cambiaron; si no existe se crea.
 */
class CfmotoClientesController
{
	/* usuario autenticado del token, usado como vendedor por defecto en las altas */
	private $idUsuarioApi = 0;

	/**
	 * GET: cliente por orden de trabajo o por id.
	 */
	public function get($body, $query, $params)
	{
		try
		{
			$token = JWT::fromHeader();
			JWT::validate($token);
		}
		catch (JWTException $e)
		{
			return Response::forGiven($e->getCode(), false, $e->getMessage());
		}

		$oClientes = new Clientes();
		$oCliente = false;

		/* por orden de trabajo */
		$ordenParam = '';
		if (!empty($query['orden_id']))
			$ordenParam = $query['orden_id'];
		elseif (!empty($query['id_externo']) && stripos($query['id_externo'], 'OT') === 0)
			$ordenParam = $query['id_externo'];

		if ($ordenParam !== '')
		{
			$IdOrdenTrabajo = CFMoto::IdDesdeExterno($ordenParam);
			$oOrdenesTrabajo = new OrdenesTrabajo();
			$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
			if (!$oOrdenTrabajo)
				return Response::forGiven(404, false, 'No existe la orden de trabajo ' . $ordenParam . '.');

			$oTallerUnidades = new TallerUnidades();
			$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
			if (!$oTallerUnidad || !$oTallerUnidad->IdCliente)
				return Response::forGiven(404, false, 'La orden ' . $ordenParam . ' no tiene un cliente asociado.');

			$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
		}
		else
		{
			/* por id de cliente (numerico o CLI-...) */
			$idParam = '';
			if (!empty($query['id']))
				$idParam = $query['id'];
			elseif (!empty($query['id_externo']))
				$idParam = $query['id_externo'];

			if ($idParam === '')
				return Response::forGiven(422, false, 'Debe indicar ?orden_id=X (orden de trabajo) o ?id=X (cliente).');

			$oCliente = $oClientes->GetById(CFMoto::IdDesdeExterno($idParam));
		}

		if (!$oCliente)
			return Response::forGiven(404, false, 'Cliente no encontrado.');

		return array(200, array(
			'origen_sistema' => CFMoto::OrigenSistema,
			'generado_en'    => CFMoto::AhoraIso(),
			'clientes'       => array(CFMoto::PayloadCliente($oCliente)),
		));
	}

	/**
	 * POST: alta / actualizacion de clientes (y sus motos).
	 * Body: { origen_sistema, [evento], clientes: [...] }
	 * Respuesta: formato spec seccion 3.
	 */
	public function post($body, $query, $params)
	{
		try
		{
			$token = JWT::fromHeader();
			$payload = JWT::validate($token);
		}
		catch (JWTException $e)
		{
			return Response::forGiven($e->getCode(), false, $e->getMessage());
		}

		$this->idUsuarioApi = isset($payload['id_usuario']) ? (int)$payload['id_usuario'] : 0;

		if (!isset($body['clientes']) || !is_array($body['clientes']))
			return Response::forGiven(400, false, 'El body debe incluir el array "clientes".');

		if (count($body['clientes']) > 200)
			return Response::forGiven(400, false, 'Maximo 200 registros por request.');

		/* idempotencia de eventos (webhooks): un evento repetido se ignora */
		if (isset($body['evento']['id']))
		{
			if (CFMoto::EventoYaProcesado($body['evento']['id']))
			{
				return array(200, array(
					'ok'           => true,
					'recibidos'    => count($body['clientes']),
					'creados'      => 0,
					'actualizados' => 0,
					'errores'      => array(),
					'mensaje'      => 'Evento ya procesado anteriormente (ignorado).',
					'procesado_en' => CFMoto::AhoraIso(),
				));
			}
			CFMoto::RegistrarEvento($body['evento']['id']);
		}

		$creados = 0;
		$actualizados = 0;
		$errores = array();

		foreach ($body['clientes'] as $cliente)
		{
			try
			{
				$resultado = $this->procesarCliente($cliente, $errores);
				if ($resultado === 'creado')
					$creados++;
				elseif ($resultado === 'actualizado')
					$actualizados++;
			}
			catch (Exception $e)
			{
				$errores[] = array(
					'id_externo' => isset($cliente['id_externo']) ? $cliente['id_externo'] : null,
					'codigo'     => 'ERROR_INTERNO',
					'mensaje'    => $e->getMessage(),
				);
			}
		}

		return array(200, array(
			'ok'           => true,
			'recibidos'    => count($body['clientes']),
			'creados'      => $creados,
			'actualizados' => $actualizados,
			'errores'      => $errores,
			'procesado_en' => CFMoto::AhoraIso(),
		));
	}

	/**
	 * Crea o actualiza un cliente a partir del payload del CRM.
	 * Devuelve 'creado', 'actualizado' o false (fue a $errores).
	 */
	private function procesarCliente($cliente, &$errores)
	{
		$oClientes = new Clientes();

		/* -- matching: id_externo -> documento -> email ------------------ */
		$oCliente = false;

		$idNumerico = isset($cliente['id_externo']) ? CFMoto::IdDesdeExterno($cliente['id_externo']) : 0;
		if ($idNumerico > 0)
			$oCliente = $oClientes->GetById($idNumerico);

		$docNumero = null;
		if (isset($cliente['documento']) && is_array($cliente['documento']))
			$docNumero = CFMoto::Campo($cliente['documento'], 'numero');

		if (!$oCliente && $docNumero)
			$oCliente = $oClientes->GetByDocumentoNumero($docNumero);

		$email = CFMoto::Campo($cliente, 'email');
		if (!$oCliente && $email)
		{
			$arrPorEmail = $oClientes->GetAll(array('Email' => $email));
			if ($arrPorEmail)
			{
				foreach ($arrPorEmail as $oCandidato)
				{
					if (strtolower(trim($oCandidato->Email)) == strtolower($email))
					{
						$oCliente = $oCandidato;
						break;
					}
				}
			}
		}

		$esNuevo = !$oCliente;
		if ($esNuevo)
		{
			$oCliente = new Cliente();
			$oCliente->IdTipoPersona = (CFMoto::Campo($cliente, 'razon_social'))
				? PersonaTipos::PersonaJuridica
				: PersonaTipos::PersonaFisica;
			/* columnas NOT NULL de TB_Clientes que el CRM no informa */
			$oCliente->Empresa    = '';
			$oCliente->Email      = '';
			$oCliente->IdVendedor = $this->idUsuarioApi;
			$oCliente->IdTipoIva  = TipoIva::CF;
		}

		$cambios = false;

		/* -- nombre / razon social --------------------------------------- */
		$nombre = CFMoto::Campo($cliente, 'razon_social');
		if (!$nombre)
			$nombre = CFMoto::Campo($cliente, 'nombre');
		if ($nombre && $nombre != $oCliente->RazonSocial)
		{
			$oCliente->RazonSocial = $nombre;
			$cambios = true;
		}

		/* -- documento ---------------------------------------------------- */
		if ($docNumero)
		{
			$docTipo = strtoupper((string)CFMoto::Campo($cliente['documento'], 'tipo'));
			$soloDigitos = preg_replace('/[^0-9]/', '', $docNumero);
			if ($docTipo == 'CUIT' || $docTipo == 'CUIL')
			{
				$idTipoClave = ($docTipo == 'CUIL') ? ClaveFiscalTipos::Cuil : ClaveFiscalTipos::Cuit;
				if ($oCliente->ClaveFiscalNumero != $soloDigitos || $oCliente->ClaveFiscalTipo != $idTipoClave)
				{
					$oCliente->ClaveFiscalTipo = $idTipoClave;
					$oCliente->ClaveFiscalNumero = $soloDigitos;
					$cambios = true;
				}
			}
			else
			{
				/* DNI / PASAPORTE / OTRO: solo el numero (el tipo es un
				   catalogo interno que no tocamos desde afuera) */
				if ($oCliente->DocumentoNumero != $soloDigitos)
				{
					$oCliente->DocumentoNumero = $soloDigitos;
					$cambios = true;
				}
			}
		}

		/* -- telefono ------------------------------------------------------ */
		$telefono = CFMoto::Campo($cliente, 'telefono');
		if ($telefono)
		{
			$actual = CFMoto::TelefonoE164($oCliente->TelefonoCodigoArea, $oCliente->Telefono);
			$entrante = CFMoto::TelefonoE164('', $telefono);
			if ($entrante && $entrante != $actual)
			{
				/* guardamos el numero completo sin prefijo de pais */
				$digitos = preg_replace('/[^0-9]/', '', $entrante);
				if (substr($digitos, 0, 2) == '54')
					$digitos = substr($digitos, 2);
				$oCliente->TelefonoCodigoArea = '';
				$oCliente->Telefono = $digitos;
				$cambios = true;
			}
		}

		/* -- email --------------------------------------------------------- */
		if ($email && strtolower(trim($oCliente->Email)) != strtolower($email))
		{
			$oCliente->Email = $email;
			$cambios = true;
		}

		/* -- direccion ------------------------------------------------------ */
		if (isset($cliente['direccion']) && is_array($cliente['direccion']))
		{
			$direccion = $cliente['direccion'];

			$calle = CFMoto::Campo($direccion, 'calle');
			if ($calle)
			{
				/* separamos la altura si viene al final de la calle */
				$nuevaCalle = $calle;
				$nuevoNumero = '';
				if (preg_match('/^(.*?)[\s,]+([0-9]+)\s*$/', $calle, $m))
				{
					$nuevaCalle = trim($m[1]);
					$nuevoNumero = $m[2];
				}
				$actualCalle = trim($oCliente->DomicilioCalle . ' ' . $oCliente->DomicilioNumero);
				if ($actualCalle != trim($nuevaCalle . ' ' . $nuevoNumero))
				{
					$oCliente->DomicilioCalle = $nuevaCalle;
					$oCliente->DomicilioNumero = $nuevoNumero;
					$cambios = true;
				}
			}

			$pisoDepto = CFMoto::Campo($direccion, 'piso_depto');
			if ($pisoDepto && trim($oCliente->DomicilioPiso . ' ' . $oCliente->DomicilioDpto) != $pisoDepto)
			{
				$oCliente->DomicilioPiso = $pisoDepto;
				$oCliente->DomicilioDpto = '';
				$cambios = true;
			}

			$codigoPostal = CFMoto::Campo($direccion, 'codigo_postal');
			if ($codigoPostal && $oCliente->DomicilioCodigoPostal != $codigoPostal)
			{
				$oCliente->DomicilioCodigoPostal = $codigoPostal;
				$cambios = true;
			}
		}

		/* -- persistencia --------------------------------------------------- */
		if ($esNuevo)
		{
			if (!$oCliente->RazonSocial)
			{
				$errores[] = array(
					'id_externo' => isset($cliente['id_externo']) ? $cliente['id_externo'] : null,
					'codigo'     => 'NOMBRE_OBLIGATORIO',
					'mensaje'    => 'No se puede crear un cliente sin nombre o razon social.',
				);
				return false;
			}

			$oCliente = $oClientes->Create($oCliente);
			if (!$oCliente)
			{
				$errores[] = array(
					'id_externo' => isset($cliente['id_externo']) ? $cliente['id_externo'] : null,
					'codigo'     => 'ERROR_CREACION',
					'mensaje'    => 'No se pudo crear el cliente en la base.',
				);
				return false;
			}
			CFMoto::Log('API: cliente creado #' . $oCliente->IdCliente . ' (' . (isset($cliente['id_externo']) ? $cliente['id_externo'] : 's/id') . ')');
		}
		elseif ($cambios)
		{
			if (!$oClientes->Update($oCliente))
			{
				$errores[] = array(
					'id_externo' => isset($cliente['id_externo']) ? $cliente['id_externo'] : null,
					'codigo'     => 'ERROR_ACTUALIZACION',
					'mensaje'    => 'No se pudo actualizar el cliente #' . $oCliente->IdCliente . '.',
				);
				return false;
			}
			CFMoto::Log('API: cliente actualizado #' . $oCliente->IdCliente);
		}

		/* -- motos del cliente ---------------------------------------------- */
		if (isset($cliente['motos']) && is_array($cliente['motos']))
		{
			foreach ($cliente['motos'] as $moto)
				$this->procesarMoto($moto, $oCliente->IdCliente, $errores);
		}

		return $esNuevo ? 'creado' : 'actualizado';
	}

	/**
	 * Crea o actualiza una taller unidad a partir de una moto del payload.
	 * El VIN es obligatorio: es la llave maestra de matching.
	 */
	private function procesarMoto($moto, $IdCliente, &$errores)
	{
		$idExterno = isset($moto['id_externo']) ? $moto['id_externo'] : null;

		$vin = CFMoto::Campo($moto, 'vin');
		if (!$vin)
		{
			$errores[] = array(
				'id_externo' => $idExterno,
				'codigo'     => 'VIN_OBLIGATORIO',
				'mensaje'    => 'La moto no tiene VIN: es obligatorio para sincronizar.',
			);
			return false;
		}
		$vin = strtoupper($vin);

		$oTallerUnidades = new TallerUnidades();
		$oTallerUnidad = CFMoto::BuscarTallerUnidadPorVin($vin);

		$esNueva = !$oTallerUnidad;
		if ($esNueva)
		{
			$oTallerUnidad = new TallerUnidad();
			$oTallerUnidad->PrefijoVin = '';
			$oTallerUnidad->NumeroVin = $vin;
			$oTallerUnidad->IdColor = 0;
			/* columnas NOT NULL de TB_TallerUnidades que el CRM puede no informar */
			$oTallerUnidad->NumeroMotor = '';
			$oTallerUnidad->FechaInicioGarantia = '';
			$oTallerUnidad->Concesionario = '';
			$oTallerUnidad->Dominio = '';
			$oTallerUnidad->Modelo = '';
			$oTallerUnidad->ModeloAnio = 0;

			/* marca por nombre; si no existe, queda sin asignar */
			$oTallerUnidad->IdMarca = 0;
			$marcaNombre = CFMoto::Campo($moto, 'marca');
			if ($marcaNombre)
			{
				$oMarcas = new Marcas();
				$oMarca = $oMarcas->GetByNombreExacto($marcaNombre);
				if (!$oMarca)
					$oMarca = $oMarcas->GetByNombre($marcaNombre);
				if ($oMarca && $oMarca->IdMarca)
					$oTallerUnidad->IdMarca = $oMarca->IdMarca;
			}
		}

		$oTallerUnidad->IdCliente = $IdCliente;

		$modelo = CFMoto::Campo($moto, 'modelo');
		if ($modelo)
			$oTallerUnidad->Modelo = $modelo;

		$anio = CFMoto::Campo($moto, 'anio');
		if ($anio)
			$oTallerUnidad->ModeloAnio = (int)$anio;

		$patente = CFMoto::Campo($moto, 'patente');
		if ($patente)
			$oTallerUnidad->Dominio = strtoupper($patente);

		$vinMotor = CFMoto::Campo($moto, 'vin_motor');
		if ($vinMotor)
			$oTallerUnidad->NumeroMotor = $vinMotor;

		if (isset($moto['garantia']) && is_array($moto['garantia']))
		{
			$inicioGarantia = CFMoto::Campo($moto['garantia'], 'inicio');
			if ($inicioGarantia)
				$oTallerUnidad->FechaInicioGarantia = CFMoto::IsoALegacy($inicioGarantia, false);
		}

		if (isset($moto['propiedad']) && is_array($moto['propiedad']))
		{
			$concesionaria = CFMoto::Campo($moto['propiedad'], 'concesionaria');
			if ($concesionaria)
				$oTallerUnidad->Concesionario = $concesionaria;
		}

		if ($esNueva)
		{
			$oTallerUnidad = $oTallerUnidades->Create($oTallerUnidad);
			if ($oTallerUnidad)
				CFMoto::Log('API: taller unidad creada #' . $oTallerUnidad->IdTallerUnidad . ' VIN ' . $vin);
		}
		else
		{
			$oTallerUnidades->Update($oTallerUnidad);
		}

		return true;
	}
}
