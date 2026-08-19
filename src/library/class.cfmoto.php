<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.config.php');
require_once('class.clientes.php');
require_once('class.clientecontactos.php');
require_once('class.tallerunidades.php');
require_once('class.ordenestrabajo.php');
require_once('class.ordenestrabajotareas.php');
require_once('class.ordenestrabajotareasarticulos.php');
require_once('class.articulos.php');
require_once('class.turnos.php');
require_once('class.usuarios.php');
require_once('class.marcas.php');
require_once('class.colores.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.estadoorden.php');
require_once('class.tipoventa.php');
require_once('class.clavefiscaltipos.php');
require_once('class.personatipos.php');
require_once('class.ordentrabajocomentarios.php');
require_once('class.ordentrabajoimagenes.php');

/**
 * Integracion con el CRM de CFMOTO.
 *
 * - Arma los payloads de Clientes+Motos y de Turnos de Taller segun la spec
 *   cfmoto-api-sincronizacion.md (v1).
 * - Dispara los webhooks salientes (contacto creado/actualizado, orden creada/
 *   estado actualizado) hacia el CRM.
 * - Reglas de datos: lo que no tenemos va como null; lo derivable se infiere
 *   (fecha_alta = hoy, version = modelo, garantia = inicio + 24 meses, etc).
 * - La base es latin1: todo string saliente se convierte a UTF-8 y todo
 *   string entrante se convierte a latin1 (metodos Utf8 / Latin1).
 */
class CFMoto
{
	const OrigenSistema			= 'DMS_CONCESIONARIA';
	const UrlWebhookContactos	= 'https://backend-production-3df97.up.railway.app/webhook/contactos';
	const UrlWebhookTaller		= 'https://backend-production-3df97.up.railway.app/webhook/taller';

	/* Garantia: solo tenemos fecha de inicio; el fin se calcula con el plazo estandar */
	const GarantiaMeses			= 24;

	/* Timeout corto para no colgar los redirects de los ABMs */
	const TimeoutConexion		= 3;
	const TimeoutTotal			= 5;

	/* Carpeta de logs y de webhooks pendientes de reintento (relativa a library/) */
	const PathBase				= '/../_recursos/cfmoto/';


	/* ==================================================================== */
	/* Helpers de encoding / formato                                        */
	/* ==================================================================== */

	/* Convierte recursivamente strings latin1 (BD) a UTF-8 (JSON) */
	public static function Utf8($valor)
	{
		if (is_array($valor))
		{
			$arr = array();
			foreach ($valor as $k => $v)
				$arr[$k] = self::Utf8($v);
			return $arr;
		}
		if (is_string($valor))
		{
			if (function_exists('mb_check_encoding') && mb_check_encoding($valor, 'UTF-8') && preg_match('/[\x80-\xFF]/', $valor))
				return $valor; /* ya es UTF-8 valido con multibyte, no re-encodear */
			return utf8_encode($valor);
		}
		return $valor;
	}

	/* Convierte recursivamente strings UTF-8 (JSON entrante) a latin1 (BD) */
	public static function Latin1($valor)
	{
		if (is_array($valor))
		{
			$arr = array();
			foreach ($valor as $k => $v)
				$arr[$k] = self::Latin1($v);
			return $arr;
		}
		if (is_string($valor))
			return utf8_decode($valor);
		return $valor;
	}

	/* Devuelve un string latin1 listo para BD desde un campo del payload, o null */
	public static function Campo($arr, $clave)
	{
		if (!is_array($arr) || !isset($arr[$clave]) || $arr[$clave] === null || $arr[$clave] === '')
			return null;
		return is_string($arr[$clave]) ? trim(utf8_decode($arr[$clave])) : $arr[$clave];
	}

	/* Parsea fechas legacy ('d-m-Y', 'd/m/Y', 'Y-m-d', con o sin hora) a timestamp */
	public static function Timestamp($fecha)
	{
		if (!$fecha)
			return null;
		$fecha = trim($fecha);
		if ($fecha == '' || substr($fecha, 0, 10) == '0000-00-00')
			return null;
		/* con barras strtotime asume formato yanqui m/d/Y: pasamos a guiones (d-m-Y) */
		if (strpos($fecha, '/') !== false)
			$fecha = str_replace('/', '-', $fecha);
		$ts = strtotime($fecha);
		return ($ts === false) ? null : $ts;
	}

	/* Fecha 'Y-m-d' o null */
	public static function Fecha($fecha)
	{
		$ts = self::Timestamp($fecha);
		return $ts ? date('Y-m-d', $ts) : null;
	}

	/* Fecha-hora ISO 8601 con timezone o null */
	public static function FechaHoraIso($fecha)
	{
		$ts = self::Timestamp($fecha);
		return $ts ? date('c', $ts) : null;
	}

	public static function AhoraIso()
	{
		return date('c');
	}

	/* Fecha-hora ISO entrante -> formato legacy 'd-m-Y H:i:s' (o null) */
	public static function IsoALegacy($iso, $conHora = true)
	{
		if (!$iso)
			return null;
		$ts = strtotime($iso);
		if ($ts === false)
			return null;
		return $conHora ? date('d-m-Y H:i:s', $ts) : date('d-m-Y', $ts);
	}

	/* Normaliza telefono a E.164 (+54...) o null */
	public static function TelefonoE164($codigoArea, $numero)
	{
		$digitos = preg_replace('/[^0-9]/', '', trim($codigoArea) . trim($numero));
		if ($digitos == '')
			return null;
		$digitos = ltrim($digitos, '0');
		if ($digitos == '')
			return null;
		if (substr($digitos, 0, 2) == '54')
			return '+' . $digitos;
		return '+54' . $digitos;
	}

	/* ==================================================================== */
	/* Identificadores externos                                             */
	/* ==================================================================== */

	public static function IdExternoCliente($IdCliente)
	{
		return 'CLI-' . str_pad((int)$IdCliente, 6, '0', STR_PAD_LEFT);
	}

	public static function IdExternoMoto($IdTallerUnidad)
	{
		return 'UNI-' . str_pad((int)$IdTallerUnidad, 6, '0', STR_PAD_LEFT);
	}

	public static function IdExternoOrden($IdOrdenTrabajo)
	{
		return 'OT-' . str_pad((int)$IdOrdenTrabajo, 6, '0', STR_PAD_LEFT);
	}

	/* 'CLI-000123', 'OT-2026-004512' o '123' -> 123 (toma los digitos finales) */
	public static function IdDesdeExterno($idExterno)
	{
		if ($idExterno === null || $idExterno === '')
			return 0;
		if (preg_match('/([0-9]+)\s*$/', trim($idExterno), $m))
			return (int)ltrim($m[1], '0');
		return 0;
	}

	/* ==================================================================== */
	/* Mapeos de estado                                                     */
	/* ==================================================================== */

	/* EstadoOrden interno -> estado del CRM */
	public static function EstadoOrdenACrm(OrdenTrabajo $oOrdenTrabajo)
	{
		switch ((int)$oOrdenTrabajo->IdEstadoOrden)
		{
			case EstadoOrden::Presupuesto:
				return 'pendiente';
			case EstadoOrden::Aceptada:
				/* aceptada con ingreso registrado y sin entrega = en proceso */
				if (self::Timestamp($oOrdenTrabajo->FechaInicio) && !self::Timestamp($oOrdenTrabajo->FechaFin))
					return 'en_proceso';
				return 'confirmado';
			case EstadoOrden::Auditoria:
				return 'en_proceso';
			case EstadoOrden::Finalizado:
				return 'finalizado';
			case EstadoOrden::Rechazado:
				return 'cancelado';
			default:
				return 'pendiente';
		}
	}

	/* estado del CRM -> EstadoOrden interno (null = no mapear / no tocar) */
	public static function EstadoCrmAOrden($estado)
	{
		switch ($estado)
		{
			case 'pendiente':
				return EstadoOrden::Presupuesto;
			case 'confirmado':
			case 'en_proceso':
			case 'esperando_repuesto':
				return EstadoOrden::Aceptada;
			case 'finalizado':
			case 'entregado':
				return EstadoOrden::Finalizado;
			case 'cancelado':
			case 'no_asistio':
				return EstadoOrden::Rechazado;
			default:
				return null;
		}
	}

	/* ==================================================================== */
	/* Busquedas auxiliares                                                 */
	/* ==================================================================== */

	/*
	 * Lookup exacto de TallerUnidad por VIN. El filtro NumeroVin de la clase
	 * es un LIKE, asi que la igualdad exacta se resuelve aca (contra
	 * PrefijoVin+NumeroVin o NumeroVin solo).
	 */
	public static function BuscarTallerUnidadPorVin($vin)
	{
		$vin = strtoupper(trim(utf8_decode($vin)));
		if ($vin == '')
			return false;

		$oTallerUnidades = new TallerUnidades();

		/* el LIKE necesita un fragmento: usamos los ultimos 8 caracteres */
		$fragmento = (strlen($vin) > 8) ? substr($vin, -8) : $vin;
		$arr = $oTallerUnidades->GetAll(array('NumeroVin' => $fragmento));

		if (!$arr)
			return false;

		foreach ($arr as $oTallerUnidad)
		{
			$vinCompleto = strtoupper(trim($oTallerUnidad->PrefijoVin . $oTallerUnidad->NumeroVin));
			$vinSolo     = strtoupper(trim($oTallerUnidad->NumeroVin));
			if ($vinCompleto == $vin || $vinSolo == $vin)
				return $oTallerUnidad;
		}

		return false;
	}

	/* ==================================================================== */
	/* Payload: Cliente + Motos (seccion 1 de la spec)                      */
	/* ==================================================================== */

	public static function PayloadCliente(Cliente $oCliente)
	{
		$oUsuarios = new Usuarios();

		/* nombre / razon social segun tipo de persona */
		$esJuridica  = ((int)$oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica);
		$nombre      = trim($oCliente->RazonSocial);
		$razonSocial = $esJuridica ? $nombre : null;

		/* documento: prioridad CUIT/CUIL, sino DNI */
		$documento = array('tipo' => null, 'numero' => null);
		if ($oCliente->ClaveFiscalNumero)
		{
			$tipoClave = ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo);
			$documento = array(
				'tipo'   => in_array($tipoClave, array('CUIT', 'CUIL')) ? $tipoClave : 'CUIT',
				'numero' => preg_replace('/[^0-9]/', '', $oCliente->ClaveFiscalNumero),
			);
		}
		elseif ($oCliente->DocumentoNumero)
		{
			$documento = array(
				'tipo'   => 'DNI',
				'numero' => preg_replace('/[^0-9]/', '', $oCliente->DocumentoNumero),
			);
		}

		/* telefono alternativo: primer contacto con telefono propio */
		$telefonoAlternativo = null;
		$arrContactos = $oCliente->GetAllContactos();
		if ($arrContactos)
		{
			foreach ($arrContactos as $oContacto)
			{
				$tel = self::TelefonoE164($oContacto->TelefonoCodigoArea, $oContacto->Telefono);
				if ($tel)
				{
					$telefonoAlternativo = $tel;
					break;
				}
			}
		}

		/* direccion: ciudad/provincia via joins de localidad */
		$ciudad = null;
		$provincia = null;
		if ($oCliente->DomicilioIdLocalidad)
		{
			$oLocalidades = new Localidades();
			$oLocalidad   = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
			if ($oLocalidad)
			{
				$ciudad = $oLocalidad->Nombre;
				if ($oLocalidad->IdProvincia)
				{
					$oProvincias = new Provincias();
					$oProvincia  = $oProvincias->GetById($oLocalidad->IdProvincia);
					if ($oProvincia)
						$provincia = $oProvincia->Nombre;
				}
			}
		}

		$pisoDepto = trim($oCliente->DomicilioPiso . ' ' . $oCliente->DomicilioDpto);

		/* vendedor asignado */
		$vendedor = null;
		if ($oCliente->IdVendedor)
		{
			$oVendedor = $oUsuarios->GetById($oCliente->IdVendedor);
			if ($oVendedor)
			{
				$vendedor = array(
					'id_externo' => 'VEN-' . str_pad((int)$oVendedor->IdUsuario, 3, '0', STR_PAD_LEFT),
					'nombre'     => trim($oVendedor->Nombre . ' ' . $oVendedor->Apellido),
					'email'      => ($oVendedor->Email) ? $oVendedor->Email : null,
				);
			}
		}

		/* motos del cliente (taller unidades: incluye las no vendidas por nosotros) */
		$motos = array();
		$marcasInteres = array();
		$arrTallerUnidades = $oCliente->GetAllTallerUnidades();
		if ($arrTallerUnidades)
		{
			foreach ($arrTallerUnidades as $oTallerUnidad)
			{
				$moto = self::PayloadMoto($oTallerUnidad);
				$motos[] = $moto;
				if ($moto['marca'] && !in_array($moto['marca'], $marcasInteres))
					$marcasInteres[] = $moto['marca'];
			}
		}

		$payload = array(
			'id_externo'           => self::IdExternoCliente($oCliente->IdCliente),
			/* no manejamos semantica lead/prospecto: todos salen como cliente */
			'tipo'                 => 'cliente',
			'nombre'               => ($nombre !== '') ? $nombre : null,
			'razon_social'         => $razonSocial,
			'documento'            => $documento,
			'telefono'             => self::TelefonoE164($oCliente->TelefonoCodigoArea, $oCliente->Telefono),
			'telefono_alternativo' => $telefonoAlternativo,
			'email'                => ($oCliente->Email) ? trim($oCliente->Email) : null,
			'direccion'            => array(
				'calle'         => trim($oCliente->DomicilioCalle . ' ' . $oCliente->DomicilioNumero) ?: null,
				'piso_depto'    => ($pisoDepto !== '') ? $pisoDepto : null,
				'ciudad'        => $ciudad,
				'provincia'     => $provincia,
				'codigo_postal' => ($oCliente->DomicilioCodigoPostal) ? trim($oCliente->DomicilioCodigoPostal) : null,
				'pais'          => 'AR',
			),
			/* sin campo origen en TB_Clientes */
			'origen'               => null,
			/* sin fecha de alta en TB_Clientes: se informa la fecha actual */
			'fecha_alta'           => date('Y-m-d'),
			'vendedor_asignado'    => $vendedor,
			'marcas_interes'       => $marcasInteres,
			'etiquetas'            => array(),
			'notas'                => null,
			'acepta_marketing'     => null,
			'activo'               => true,
			/* sin timestamp de modificacion: se informa el momento del envio */
			'actualizado_en'       => self::AhoraIso(),
			'motos'                => $motos,
		);

		return self::Utf8($payload);
	}

	/* Payload de una moto (TallerUnidad) segun la spec */
	public static function PayloadMoto(TallerUnidad $oTallerUnidad)
	{
		$oMarcas         = new Marcas();
		$oColores        = new Colores();
		$oOrdenesTrabajo = new OrdenesTrabajo();

		$marca = null;
		if ($oTallerUnidad->IdMarca)
		{
			$oMarca = $oMarcas->GetById($oTallerUnidad->IdMarca);
			if ($oMarca)
				$marca = $oMarca->Nombre;
		}

		$color = null;
		if ($oTallerUnidad->IdColor)
		{
			$oColor = $oColores->GetById($oTallerUnidad->IdColor);
			if ($oColor)
				$color = $oColor->Nombre;
		}

		/* kilometraje derivado de la ultima orden de trabajo (cualquier estado) */
		$kilometraje = null;
		$kilometrajeFecha = null;
		$oUltimaOrden = $oOrdenesTrabajo->GetUltimaByIdTallerUnidad($oTallerUnidad->IdTallerUnidad);
		if ($oUltimaOrden && $oUltimaOrden->Kilometros)
		{
			$kilometraje = (int)$oUltimaOrden->Kilometros;
			$kilometrajeFecha = self::Fecha($oUltimaOrden->Fecha);
		}

		$modelo = ($oTallerUnidad->Modelo) ? trim($oTallerUnidad->Modelo) : null;

		return array(
			'id_externo'                => self::IdExternoMoto($oTallerUnidad->IdTallerUnidad),
			'vin'                       => trim($oTallerUnidad->PrefijoVin . $oTallerUnidad->NumeroVin) ?: null,
			'vin_motor'                 => ($oTallerUnidad->NumeroMotor) ? trim($oTallerUnidad->NumeroMotor) : null,
			'marca'                     => $marca,
			'modelo'                    => $modelo,
			/* no tenemos version separada: se repite el modelo */
			'version'                   => $modelo,
			'anio'                      => ($oTallerUnidad->ModeloAnio) ? (int)$oTallerUnidad->ModeloAnio : null,
			'color'                     => $color,
			'patente'                   => ($oTallerUnidad->Dominio) ? trim($oTallerUnidad->Dominio) : null,
			'kilometraje'               => $kilometraje,
			'kilometraje_actualizado_en'=> $kilometrajeFecha,
			'propiedad'                 => array(
				/* 0km solo si salio de nuestro stock (tiene IdUnidad) */
				'tipo_operacion' => ($oTallerUnidad->IdUnidad) ? '0km' : 'desconocido',
				'fecha_compra'   => null,
				'numero_factura' => null,
				'concesionaria'  => ($oTallerUnidad->Concesionario) ? trim($oTallerUnidad->Concesionario) : null,
			),
			'garantia'                  => self::PayloadGarantia($oTallerUnidad->FechaInicioGarantia),
			'estado'                    => 'activa',
			'actualizado_en'            => self::AhoraIso(),
		);
	}

	/* Garantia: solo tenemos inicio; fin = inicio + plazo estandar */
	public static function PayloadGarantia($fechaInicio)
	{
		$inicio = self::Fecha($fechaInicio);

		if (!$inicio)
		{
			return array(
				'inicio'        => null,
				'fin'           => null,
				'meses'         => null,
				'km_limite'     => null,
				'vigente'       => null,
				'extendida'     => null,
				'observaciones' => null,
			);
		}

		$fin = date('Y-m-d', strtotime($inicio . ' +' . self::GarantiaMeses . ' months'));

		return array(
			'inicio'        => $inicio,
			'fin'           => $fin,
			'meses'         => self::GarantiaMeses,
			'km_limite'     => null,
			'vigente'       => ($fin >= date('Y-m-d')),
			'extendida'     => false,
			'observaciones' => null,
		);
	}

	/* ==================================================================== */
	/* Payload: Turno / Orden de Trabajo (seccion 2 de la spec)             */
	/* ==================================================================== */

	public static function PayloadTurno(OrdenTrabajo $oOrdenTrabajo)
	{
		$oTallerUnidades = new TallerUnidades();
		$oClientes       = new Clientes();
		$oUsuarios       = new Usuarios();
		$oTurnos         = new Turnos();
		$oArticulos      = new Articulos();
		$oTareasArticulos = new OrdenesTrabajoTareasArticulos();

		$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
		$oCliente = ($oTallerUnidad && $oTallerUnidad->IdCliente) ? $oClientes->GetById($oTallerUnidad->IdCliente) : false;

		/* cliente resumido */
		$cliente = null;
		if ($oCliente)
		{
			$cliente = array(
				'id_externo' => self::IdExternoCliente($oCliente->IdCliente),
				'nombre'     => trim($oCliente->RazonSocial) ?: null,
				'telefono'   => self::TelefonoE164($oCliente->TelefonoCodigoArea, $oCliente->Telefono),
				'email'      => ($oCliente->Email) ? trim($oCliente->Email) : null,
			);
		}

		/* moto resumida */
		$moto = null;
		if ($oTallerUnidad)
		{
			$oMarcas = new Marcas();
			$marca = null;
			if ($oTallerUnidad->IdMarca)
			{
				$oMarca = $oMarcas->GetById($oTallerUnidad->IdMarca);
				if ($oMarca)
					$marca = $oMarca->Nombre;
			}
			$moto = array(
				'id_externo'          => self::IdExternoMoto($oTallerUnidad->IdTallerUnidad),
				'vin'                 => trim($oTallerUnidad->PrefijoVin . $oTallerUnidad->NumeroVin) ?: null,
				'vin_motor'           => ($oTallerUnidad->NumeroMotor) ? trim($oTallerUnidad->NumeroMotor) : null,
				'marca'               => $marca,
				'modelo'              => ($oTallerUnidad->Modelo) ? trim($oTallerUnidad->Modelo) : null,
				'anio'                => ($oTallerUnidad->ModeloAnio) ? (int)$oTallerUnidad->ModeloAnio : null,
				'patente'             => ($oTallerUnidad->Dominio) ? trim($oTallerUnidad->Dominio) : null,
				'kilometraje_ingreso' => ($oOrdenTrabajo->Kilometros) ? (int)$oOrdenTrabajo->Kilometros : null,
			);
		}

		/* tareas: trabajos realizados, garantia y repuestos */
		$trabajos = array();
		$repuestos = array();
		$aplicaGarantia = false;
		$arrTareas = $oOrdenTrabajo->GetAllTareas();
		if ($arrTareas)
		{
			foreach ($arrTareas as $oTarea)
			{
				$esGarantia = ((int)$oTarea->IdTipoVenta == TipoVenta::Garantia);
				if ($esGarantia)
					$aplicaGarantia = true;

				$titulo = trim($oTarea->Titulo);
				if ($titulo == '')
					$titulo = trim($oTarea->Descripcion);
				if ($titulo != '')
					$trabajos[] = html_entity_decode($titulo, ENT_QUOTES, 'ISO-8859-1');

				$arrArticulos = $oTareasArticulos->GetAllByOrdenTrabajoTarea($oTarea);
				if ($arrArticulos)
				{
					foreach ($arrArticulos as $oTareaArticulo)
					{
						$codigo = null;
						$descripcion = null;
						$oArticulo = $oArticulos->GetById($oTareaArticulo->IdArticulo);
						if ($oArticulo)
						{
							$codigo = $oArticulo->Codigo;
							$descripcion = html_entity_decode($oArticulo->Descripcion, ENT_QUOTES, 'ISO-8859-1');
						}
						$cantidad = (float)$oTareaArticulo->Cantidad;
						$precioTotal = (float)$oTareaArticulo->PrecioTotal;
						$repuestos[] = array(
							'codigo'               => $codigo,
							'descripcion'          => $descripcion,
							'cantidad'             => $cantidad,
							'precio_unitario'      => ($cantidad > 0) ? round($precioTotal / $cantidad, 2) : $precioTotal,
							'cubierto_por_garantia'=> $esGarantia,
						);
					}
				}
			}
		}

		/* servicio: sin campos categoricos propios, se derivan */
		$esPreentrega = ((int)$oOrdenTrabajo->IdTipoVenta == TipoVenta::PreEntrega);
		$servicio = array(
			'tipo'                => $aplicaGarantia ? 'garantia' : 'otro',
			'razon_ingreso'       => $aplicaGarantia ? 'garantia' : ($esPreentrega ? 'preentrega' : 'otro'),
			'descripcion'         => ($oOrdenTrabajo->Comentarios) ? trim($oOrdenTrabajo->Comentarios) : null,
			/* sin campos separados de falla y diagnostico */
			'falla_reportada'     => null,
			'diagnostico'         => null,
			'trabajos_realizados' => $trabajos,
		);

		/* programacion: turno de agenda si la OT nacio de un turno */
		$fechaTurno = null;
		$horaTurno = null;
		$oTurno = $oTurnos->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
		if ($oTurno)
		{
			$fechaTurno = self::Fecha($oTurno->Fecha);
			$tsInicio = self::Timestamp($oTurno->FechaInicio);
			if ($tsInicio)
				$horaTurno = date('H:i', $tsInicio);
		}
		if (!$fechaTurno)
			$fechaTurno = self::Fecha($oOrdenTrabajo->Fecha);

		$finalizada = ((int)$oOrdenTrabajo->IdEstadoOrden == EstadoOrden::Finalizado);

		$mecanico = null;
		if ($oOrdenTrabajo->IdUsuarioAsignado)
		{
			$oMecanico = $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
			if ($oMecanico)
			{
				$mecanico = array(
					'id_externo' => 'MEC-' . str_pad((int)$oMecanico->IdUsuario, 3, '0', STR_PAD_LEFT),
					'nombre'     => trim($oMecanico->Nombre . ' ' . $oMecanico->Apellido),
				);
			}
		}

		$programacion = array(
			'fecha_turno'            => $fechaTurno,
			'hora_turno'             => $horaTurno,
			'fecha_ingreso'          => self::FechaHoraIso($oOrdenTrabajo->FechaInicio),
			/* FechaFin es la salida estimada mientras la OT esta abierta,
			   y la entrega real cuando se finaliza */
			'fecha_entrega_estimada' => $finalizada ? null : self::FechaHoraIso($oOrdenTrabajo->FechaFin),
			'fecha_entrega_real'     => $finalizada ? self::FechaHoraIso($oOrdenTrabajo->FechaFin) : null,
			'mecanico'               => $mecanico,
		);

		/* importes: ImporteRepuestos() esta roto en la clase legacy
		   (accede a la propiedad en vez de llamar al metodo): usamos
		   los metodos *Calculado() */
		$total          = round((float)$oOrdenTrabajo->ImporteTotalCalculado(), 2);
		$totalRepuestos = round((float)$oOrdenTrabajo->ImporteRepuestosCalculado(), 2);
		$manoObra       = round($total - $totalRepuestos, 2);
		$facturado      = (float)str_replace(',', '', $oOrdenTrabajo->ImporteFacturado());

		if ($total <= 0)
			$estadoPago = 'sin_cargo';
		elseif ($facturado <= 0)
			$estadoPago = 'pendiente';
		elseif ($facturado + 0.01 >= $total)
			$estadoPago = 'pagado';
		else
			$estadoPago = 'parcial';

		$importes = array(
			'moneda'      => 'ARS',
			'mano_obra'   => $manoObra,
			'repuestos'   => $totalRepuestos,
			'descuento'   => 0.00,
			'subtotal'    => $total,
			/* los impuestos se conocen recien al facturar */
			'impuestos'   => null,
			'total'       => $total,
			'estado_pago' => $estadoPago,
		);

		/* notas internas: comentarios de la OT + tabla de comentarios */
		$notas = array();
		if ($oOrdenTrabajo->Comentarios && trim($oOrdenTrabajo->Comentarios) != '')
			$notas[] = trim($oOrdenTrabajo->Comentarios);
		$oComentarios = new OrdenTrabajoComentarios();
		$arrComentarios = $oComentarios->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
		if ($arrComentarios)
		{
			foreach ($arrComentarios as $oComentario)
			{
				if ($oComentario->Comentarios && trim($oComentario->Comentarios) != '')
					$notas[] = trim($oComentario->Comentarios);
			}
		}

		/* adjuntos: imagenes de la OT servidas desde _recursos.
		   Nota: se usa GetAllByOrdenTrabajo porque GetAllByIdOrdenTrabajo
		   joinea contra tblOrdenesTrabajo, tabla que no existe en la base. */
		$adjuntos = array();
		$oImagenes = new OrdenTrabajoImagenes();
		$arrImagenes = $oImagenes->GetAllByOrdenTrabajo($oOrdenTrabajo);
		if ($arrImagenes)
		{
			foreach ($arrImagenes as $oImagen)
			{
				if (!$oImagen->Imagen)
					continue;
				$adjuntos[] = array(
					'tipo'        => 'foto',
					'url'         => rtrim(Config::UrlSitio, '/') . '/_recursos/ordentrabajo/imagenes/big/' . $oImagen->Imagen,
					'descripcion' => ($oImagen->Epigrafe) ? trim($oImagen->Epigrafe) : null,
					'subido_en'   => null,
				);
			}
		}

		/* garantia de la unidad + si aplica a este ingreso */
		$garantia = $oTallerUnidad ? self::PayloadGarantia($oTallerUnidad->FechaInicioGarantia) : self::PayloadGarantia(null);
		$garantia = array(
			'inicio'                => $garantia['inicio'],
			'fin'                   => $garantia['fin'],
			'vigente'               => $garantia['vigente'],
			'aplica_a_este_ingreso' => $aplicaGarantia,
			'numero_reclamo'        => null,
		);

		$payload = array(
			'id_externo'     => self::IdExternoOrden($oOrdenTrabajo->IdOrdenTrabajo),
			'numero_orden'   => 'OT-' . (int)$oOrdenTrabajo->IdOrdenTrabajo,
			'estado'         => self::EstadoOrdenACrm($oOrdenTrabajo),
			'cliente'        => $cliente,
			'moto'           => $moto,
			'garantia'       => $garantia,
			'servicio'       => $servicio,
			'programacion'   => $programacion,
			'repuestos'      => $repuestos,
			'importes'       => $importes,
			'notas_internas' => !empty($notas) ? implode(' | ', $notas) : null,
			'adjuntos'       => $adjuntos,
			'creado_en'      => self::FechaHoraIso($oOrdenTrabajo->Fecha),
			/* sin timestamp de modificacion: se informa el momento del envio */
			'actualizado_en' => self::AhoraIso(),
		);

		return self::Utf8($payload);
	}

	/* ==================================================================== */
	/* Webhooks salientes                                                   */
	/* ==================================================================== */

	/*
	 * Notifica al CRM el alta o actualizacion de un cliente.
	 * $tipoEvento: 'contacto.creado' | 'contacto.actualizado'
	 * No lanza excepciones: cualquier falla queda logueada y encolada.
	 */
	public static function NotificarCliente($IdCliente, $tipoEvento)
	{
		try
		{
			$oClientes = new Clientes();
			$oCliente = $oClientes->GetById($IdCliente);
			if (!$oCliente)
			{
				self::Log('NotificarCliente: cliente ' . $IdCliente . ' inexistente');
				return false;
			}

			$data = array(
				'evento'         => array(
					'id'          => uniqid('evt_'),
					'tipo'        => $tipoEvento,
					'ocurrido_en' => self::AhoraIso(),
				),
				'origen_sistema' => self::OrigenSistema,
				'generado_en'    => self::AhoraIso(),
				'clientes'       => array(self::PayloadCliente($oCliente)),
			);

			return self::Enviar(self::UrlWebhookContactos, $data);
		}
		catch (Exception $e)
		{
			self::Log('NotificarCliente EXCEPCION: ' . $e->getMessage());
			return false;
		}
	}

	/*
	 * Notifica al CRM la creacion o cambio de estado de una orden de trabajo.
	 * $tipoEvento: 'taller.orden_creada' | 'taller.estado_actualizado' | 'taller.orden_actualizada'
	 */
	public static function NotificarOrdenTrabajo($IdOrdenTrabajo, $tipoEvento)
	{
		try
		{
			$oOrdenesTrabajo = new OrdenesTrabajo();
			$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
			if (!$oOrdenTrabajo)
			{
				self::Log('NotificarOrdenTrabajo: orden ' . $IdOrdenTrabajo . ' inexistente');
				return false;
			}

			$data = array(
				'evento'         => array(
					'id'          => uniqid('evt_'),
					'tipo'        => $tipoEvento,
					'ocurrido_en' => self::AhoraIso(),
				),
				'origen_sistema' => self::OrigenSistema,
				'generado_en'    => self::AhoraIso(),
				'turnos'         => array(self::PayloadTurno($oOrdenTrabajo)),
			);

			return self::Enviar(self::UrlWebhookTaller, $data);
		}
		catch (Exception $e)
		{
			self::Log('NotificarOrdenTrabajo EXCEPCION: ' . $e->getMessage());
			return false;
		}
	}

	/* POST JSON al CRM; si falla, encola el envio para reintento por cron.
	   Todo el intercambio (que mandamos, a donde, que respondieron) queda
	   registrado en _recursos/cfmoto/api-enviados-AAAAMMDD.log */
	public static function Enviar($url, $data, $encolarSiFalla = true)
	{
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);

		$codigoHttp = 0;
		$errorCurl = '';
		$respuesta = '';
		$inicio = microtime(true);

		if (function_exists('curl_init'))
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TimeoutConexion);
			curl_setopt($ch, CURLOPT_TIMEOUT, self::TimeoutTotal);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			$respuesta = curl_exec($ch);
			if ($respuesta === false)
				$respuesta = '';
			$codigoHttp = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$errorCurl = curl_error($ch);
			curl_close($ch);
		}
		else
		{
			$contexto = stream_context_create(array('http' => array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/json',
				'content' => $json,
				'timeout' => self::TimeoutTotal,
			)));
			$respuesta = @file_get_contents($url, false, $contexto);
			if ($respuesta !== false)
				$codigoHttp = 200;
			else
			{
				$respuesta = '';
				$errorCurl = 'file_get_contents fallo';
			}
		}

		$ok = ($codigoHttp >= 200 && $codigoHttp < 300);
		$ms = round((microtime(true) - $inicio) * 1000);

		/* log detallado del intercambio completo */
		self::LogEnviado($url, $json, $codigoHttp, $respuesta, $errorCurl, $ms);

		if ($ok)
		{
			self::Log('OK POST ' . $url . ' (' . $codigoHttp . ')');
		}
		else
		{
			self::Log('FALLO POST ' . $url . ' (http ' . $codigoHttp . ') ' . $errorCurl);
			if ($encolarSiFalla)
				self::GuardarPendiente($url, $json);
		}

		return $ok;
	}

	/* Registro completo de cada webhook saliente: URL, body enviado,
	   codigo HTTP y respuesta textual del otro lado */
	public static function LogEnviado($url, $jsonEnviado, $codigoHttp, $respuesta, $errorCurl, $ms)
	{
		$maxBody = 500000;
		if (strlen($jsonEnviado) > $maxBody)
			$jsonEnviado = substr($jsonEnviado, 0, $maxBody) . ' ...[TRUNCADO]';
		if (strlen($respuesta) > $maxBody)
			$respuesta = substr($respuesta, 0, $maxBody) . ' ...[TRUNCADO]';

		$linea  = '==== ' . date('Y-m-d H:i:s') . ' ====' . "\n";
		$linea .= '>> POST ' . $url . "\n";
		$linea .= 'ENVIADO: ' . $jsonEnviado . "\n";
		$linea .= '<< HTTP ' . (int)$codigoHttp . ' (' . $ms . ' ms)' . ($errorCurl ? ' ERROR: ' . $errorCurl : '') . "\n";
		$linea .= 'RESPUESTA: ' . ($respuesta !== '' ? $respuesta : '(vacia)') . "\n\n";

		$archivo = self::PathRecursos() . 'api-enviados-' . date('Ymd') . '.log';
		@file_put_contents($archivo, $linea, FILE_APPEND | LOCK_EX);
	}

	/* ==================================================================== */
	/* Cola de reintentos e idempotencia de eventos entrantes               */
	/* ==================================================================== */

	private static function PathRecursos()
	{
		$path = dirname(__FILE__) . self::PathBase;
		if (!is_dir($path))
			@mkdir($path, 0777, true);
		return $path;
	}

	public static function GuardarPendiente($url, $json)
	{
		$path = self::PathRecursos() . 'pendientes/';
		if (!is_dir($path))
			@mkdir($path, 0777, true);
		$archivo = $path . date('YmdHis') . '_' . uniqid() . '.json';
		@file_put_contents($archivo, json_encode(array('url' => $url, 'body' => $json)));
	}

	/* Reintenta los webhooks encolados (pensado para correr por cron) */
	public static function ReenviarPendientes()
	{
		$path = self::PathRecursos() . 'pendientes/';
		if (!is_dir($path))
			return 0;

		$enviados = 0;
		$archivos = glob($path . '*.json');
		if (!$archivos)
			return 0;

		foreach ($archivos as $archivo)
		{
			$contenido = json_decode(file_get_contents($archivo), true);
			if (!$contenido || empty($contenido['url']) || empty($contenido['body']))
			{
				@unlink($archivo);
				continue;
			}
			/* no volver a encolar: el archivo ya es la cola */
			if (self::Enviar($contenido['url'], json_decode($contenido['body'], true), false))
			{
				@unlink($archivo);
				$enviados++;
			}
		}

		return $enviados;
	}

	/* Idempotencia de webhooks entrantes: eventos ya procesados se ignoran */
	public static function EventoYaProcesado($eventoId)
	{
		if (!$eventoId)
			return false;
		$archivo = self::PathRecursos() . 'eventos-recibidos.log';
		if (!file_exists($archivo))
			return false;
		/* archivo acotado: se rota al superar 1 MB */
		if (filesize($archivo) > 1048576)
		{
			@rename($archivo, $archivo . '.' . date('YmdHis'));
			return false;
		}
		$contenido = file_get_contents($archivo);
		return (strpos($contenido, '[' . $eventoId . ']') !== false);
	}

	public static function RegistrarEvento($eventoId)
	{
		if (!$eventoId)
			return;
		$archivo = self::PathRecursos() . 'eventos-recibidos.log';
		@file_put_contents($archivo, date('Y-m-d H:i:s') . ' [' . $eventoId . ']' . "\n", FILE_APPEND);
	}

	public static function Log($mensaje)
	{
		$archivo = self::PathRecursos() . 'cfmoto-' . date('Ym') . '.log';
		@file_put_contents($archivo, date('Y-m-d H:i:s') . ' ' . $mensaje . "\n", FILE_APPEND);
	}
}

?>
