<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.cfmoto.php';

/**
 * Endpoints CFMOTO - Taller / Ordenes de trabajo ("turnos" en la spec:
 * el id_externo del turno es nuestro IdOrdenTrabajo).
 *
 * GET  /sync/taller/turnos?id=X  -> esa orden puntual (cualquier estado)
 * GET  /sync/taller/turnos       -> todas las ordenes FINALIZADAS
 * POST /sync/taller/turnos       -> alta/actualizacion de ordenes (formato spec seccion 2)
 * POST /webhook/taller           -> mismo procesamiento, con bloque evento (spec seccion 4)
 *
 * Validacion: toda orden entrante debe traer moto.vin, y el VIN debe existir
 * como taller unidad (errores VIN_OBLIGATORIO / VIN_NO_ENCONTRADO).
 */
class CfmotoOrdenesController
{
	/**
	 * GET: una orden especifica, o todas las finalizadas.
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

		$oOrdenesTrabajo = new OrdenesTrabajo();

		/* pedido puntual: ?id=X, ?orden_id=X o ?id_externo=OT-... */
		$idParam = '';
		if (!empty($query['id']))
			$idParam = $query['id'];
		elseif (!empty($query['orden_id']))
			$idParam = $query['orden_id'];
		elseif (!empty($query['id_externo']))
			$idParam = $query['id_externo'];

		if ($idParam !== '')
		{
			$oOrdenTrabajo = $oOrdenesTrabajo->GetById(CFMoto::IdDesdeExterno($idParam));
			if (!$oOrdenTrabajo)
				return Response::forGiven(404, false, 'No existe la orden de trabajo ' . $idParam . '.');

			$turnos = array(CFMoto::PayloadTurno($oOrdenTrabajo));
		}
		else
		{
			/* listado de finalizadas, con filtros por fecha y paginacion
			   opcionales (pedido CFMOTO 2026-09-07):
			     ?fecha_desde=AAAA-MM-DD  ?fecha_hasta=AAAA-MM-DD (fecha de la orden)
			     ?pagina=N  ?por_pagina=M (max 200; por defecto 100 al paginar) */
			$filter = array('IdEstadoOrden' => EstadoOrden::Finalizado);
			if (!empty($query['fecha_desde']))
				$filter['FechaDesde'] = trim($query['fecha_desde']);
			if (!empty($query['fecha_hasta']))
				$filter['FechaHasta'] = trim($query['fecha_hasta']);

			$pagina    = isset($query['pagina'])     ? (int)$query['pagina']     : 0;
			$porPagina = isset($query['por_pagina']) ? (int)$query['por_pagina'] : 0;

			$oPage = null;
			if ($pagina > 0 || $porPagina > 0)
			{
				if ($pagina <= 0)
					$pagina = 1;
				if ($porPagina <= 0)
					$porPagina = 100;
				if ($porPagina > 200)
					$porPagina = 200;
				$oPage = new Page($pagina, $porPagina);
			}

			$arrOrdenes = $oOrdenesTrabajo->GetAll($filter, $oPage);
			if ($arrOrdenes === false)
				return Response::forGiven(500, false, 'Error al obtener las ordenes de trabajo.');

			$turnos = array();
			if ($arrOrdenes)
			{
				foreach ($arrOrdenes as $oOrdenTrabajo)
					$turnos[] = CFMoto::PayloadTurno($oOrdenTrabajo);
			}
		}

		$respuesta = array(
			'origen_sistema' => CFMoto::OrigenSistema,
			'generado_en'    => CFMoto::AhoraIso(),
			'turnos'         => $turnos,
		);

		/* al paginar informamos tambien el total, para que el CRM sepa
		   cuantas paginas le quedan */
		if (isset($oPage) && $oPage)
		{
			$total = $oOrdenesTrabajo->GetCountRows($filter);
			$respuesta['paginacion'] = array(
				'pagina'     => $pagina,
				'por_pagina' => $porPagina,
				'total'      => ($total !== false) ? (int)$total : null,
			);
		}

		return array(200, $respuesta);
	}

	/**
	 * POST: alta / actualizacion de ordenes de trabajo desde el CRM.
	 * Body: { origen_sistema, [evento], turnos: [...] }
	 * Respuesta: formato spec seccion 3 (+ asignaciones con los ids creados).
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

		if (!isset($body['turnos']) || !is_array($body['turnos']))
			return Response::forGiven(400, false, 'El body debe incluir el array "turnos".');

		if (count($body['turnos']) > 200)
			return Response::forGiven(400, false, 'Maximo 200 registros por request.');

		/* idempotencia de eventos (webhooks) */
		if (isset($body['evento']['id']))
		{
			if (CFMoto::EventoYaProcesado($body['evento']['id']))
			{
				return array(200, array(
					'ok'           => true,
					'recibidos'    => count($body['turnos']),
					'creados'      => 0,
					'actualizados' => 0,
					'errores'      => array(),
					'mensaje'      => 'Evento ya procesado anteriormente (ignorado).',
					'procesado_en' => CFMoto::AhoraIso(),
				));
			}
			CFMoto::RegistrarEvento($body['evento']['id']);
		}

		$idUsuarioApi = isset($payload['id_usuario']) ? (int)$payload['id_usuario'] : 0;

		$creados = 0;
		$actualizados = 0;
		$errores = array();
		$asignaciones = array();

		foreach ($body['turnos'] as $turno)
		{
			try
			{
				$resultado = $this->procesarTurno($turno, $idUsuarioApi, $errores, $asignaciones);
				if ($resultado === 'creado')
					$creados++;
				elseif ($resultado === 'actualizado')
					$actualizados++;
			}
			catch (Exception $e)
			{
				$errores[] = array(
					'id_externo' => isset($turno['id_externo']) ? $turno['id_externo'] : null,
					'codigo'     => 'ERROR_INTERNO',
					'mensaje'    => $e->getMessage(),
				);
			}
		}

		return array(200, array(
			'ok'           => true,
			'recibidos'    => count($body['turnos']),
			'creados'      => $creados,
			'actualizados' => $actualizados,
			'errores'      => $errores,
			'asignaciones' => $asignaciones,
			'procesado_en' => CFMoto::AhoraIso(),
		));
	}

	/**
	 * Crea o actualiza una orden de trabajo a partir del payload del CRM.
	 * El VIN es obligatorio y debe existir como taller unidad.
	 */
	private function procesarTurno($turno, $idUsuarioApi, &$errores, &$asignaciones)
	{
		$idExterno = isset($turno['id_externo']) ? $turno['id_externo'] : null;

		/* -- validacion de VIN (obligatorio) ------------------------------ */
		$vin = null;
		if (isset($turno['moto']) && is_array($turno['moto']))
			$vin = CFMoto::Campo($turno['moto'], 'vin');

		if (!$vin)
		{
			$errores[] = array(
				'id_externo' => $idExterno,
				'codigo'     => 'VIN_OBLIGATORIO',
				'mensaje'    => 'El turno no incluye moto.vin: es obligatorio para abrir o actualizar una orden.',
			);
			return false;
		}

		$oTallerUnidad = CFMoto::BuscarTallerUnidadPorVin($vin);
		if (!$oTallerUnidad)
		{
			$errores[] = array(
				'id_externo' => $idExterno,
				'codigo'     => 'VIN_NO_ENCONTRADO',
				'mensaje'    => 'No existe una moto con VIN ' . strtoupper(trim($vin)),
			);
			return false;
		}

		$oOrdenesTrabajo = new OrdenesTrabajo();

		/* -- matching por id_externo (= IdOrdenTrabajo nuestro) ----------- */
		$oOrdenTrabajo = false;
		$idNumerico = CFMoto::IdDesdeExterno($idExterno);
		if ($idNumerico > 0)
			$oOrdenTrabajo = $oOrdenesTrabajo->GetById($idNumerico);

		$estadoCrm = CFMoto::Campo($turno, 'estado');
		$idEstadoMapeado = CFMoto::EstadoCrmAOrden($estadoCrm);

		$kilometraje = null;
		if (isset($turno['moto']) && is_array($turno['moto']))
			$kilometraje = CFMoto::Campo($turno['moto'], 'kilometraje_ingreso');

		$programacion = (isset($turno['programacion']) && is_array($turno['programacion'])) ? $turno['programacion'] : array();
		$servicio = (isset($turno['servicio']) && is_array($turno['servicio'])) ? $turno['servicio'] : array();

		if ($oOrdenTrabajo)
		{
			/* ------------------- actualizacion ---------------------------- */
			$cambios = false;

			if ($idEstadoMapeado !== null && (int)$oOrdenTrabajo->IdEstadoOrden != $idEstadoMapeado)
			{
				$oOrdenTrabajo->IdEstadoOrden = $idEstadoMapeado;
				$cambios = true;

				/* si desde el CRM la finalizan y no tiene fecha de entrega, la seteamos */
				if ($idEstadoMapeado == EstadoOrden::Finalizado)
				{
					$entregaReal = CFMoto::Campo($programacion, 'fecha_entrega_real');
					$oOrdenTrabajo->FechaFin = $entregaReal
						? CFMoto::IsoALegacy($entregaReal)
						: date('d-m-Y H:i:s');
				}
			}

			if ($kilometraje && (int)$oOrdenTrabajo->Kilometros != (int)$kilometraje)
			{
				$oOrdenTrabajo->Kilometros = (int)$kilometraje;
				$cambios = true;
			}

			if ($cambios)
				$oOrdenesTrabajo->Update($oOrdenTrabajo);

			CFMoto::Log('API: orden actualizada #' . $oOrdenTrabajo->IdOrdenTrabajo . ' (' . ($estadoCrm ? $estadoCrm : 'sin estado') . ')');
			return 'actualizado';
		}

		/* --------------------- alta de orden nueva ------------------------ */
		$oOrdenTrabajo = new OrdenTrabajo();
		$oOrdenTrabajo->IdTallerUnidad = $oTallerUnidad->IdTallerUnidad;
		$oOrdenTrabajo->IdEstadoOrden = ($idEstadoMapeado !== null) ? $idEstadoMapeado : EstadoOrden::Presupuesto;
		$oOrdenTrabajo->Fecha = date('d-m-Y H:i:s');
		$oOrdenTrabajo->IdUsuarioCreacion = $idUsuarioApi;
		$oOrdenTrabajo->IdUsuarioAsignado = $idUsuarioApi;
		$oOrdenTrabajo->Kilometros = $kilometraje ? (int)$kilometraje : 0;
		$oOrdenTrabajo->Bahia = 0;

		/* fecha de ingreso: fecha_ingreso, o fecha_turno + hora_turno */
		$fechaIngreso = CFMoto::Campo($programacion, 'fecha_ingreso');
		if (!$fechaIngreso)
		{
			$fechaTurno = CFMoto::Campo($programacion, 'fecha_turno');
			$horaTurno = CFMoto::Campo($programacion, 'hora_turno');
			if ($fechaTurno)
				$fechaIngreso = $fechaTurno . ($horaTurno ? ' ' . $horaTurno : '');
		}
		if ($fechaIngreso)
			$oOrdenTrabajo->FechaInicio = CFMoto::IsoALegacy($fechaIngreso);

		$entregaEstimada = CFMoto::Campo($programacion, 'fecha_entrega_estimada');
		if ($entregaEstimada)
			$oOrdenTrabajo->FechaFin = CFMoto::IsoALegacy($entregaEstimada);

		/* comentarios: descripcion + falla reportada del CRM */
		$comentarios = array();
		$descripcion = CFMoto::Campo($servicio, 'descripcion');
		if ($descripcion)
			$comentarios[] = $descripcion;
		$falla = CFMoto::Campo($servicio, 'falla_reportada');
		if ($falla)
			$comentarios[] = 'Falla reportada: ' . $falla;
		$oOrdenTrabajo->Comentarios = implode(' | ', $comentarios);

		/* tipo de venta: garantia si el servicio lo indica */
		$tipoServicio = CFMoto::Campo($servicio, 'tipo');
		$razonIngreso = CFMoto::Campo($servicio, 'razon_ingreso');
		if ($tipoServicio == 'garantia' || $razonIngreso == 'garantia')
			$oOrdenTrabajo->IdTipoVenta = TipoVenta::Garantia;

		$oOrdenTrabajo = $oOrdenesTrabajo->Create($oOrdenTrabajo);
		if (!$oOrdenTrabajo)
		{
			$errores[] = array(
				'id_externo' => $idExterno,
				'codigo'     => 'ERROR_CREACION',
				'mensaje'    => 'No se pudo crear la orden de trabajo en la base.',
			);
			return false;
		}

		/* devolvemos el id que le asignamos, para que el CRM re-mapee su id_externo */
		$asignaciones[] = array(
			'id_externo'  => $idExterno,
			'id_asignado' => CFMoto::IdExternoOrden($oOrdenTrabajo->IdOrdenTrabajo),
		);

		CFMoto::Log('API: orden creada #' . $oOrdenTrabajo->IdOrdenTrabajo . ' VIN ' . strtoupper(trim($vin)) . ' (externo: ' . ($idExterno ? $idExterno : 's/id') . ')');
		return 'creado';
	}
}
