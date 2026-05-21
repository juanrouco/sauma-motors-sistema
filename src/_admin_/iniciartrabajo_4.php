<?php

require_once('../inc_library.php'); 

/* obtiene datos enviados */
$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$IdOrdenTrabajoTarea	= intval($_REQUEST['IdOrdenTrabajoTarea']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);
$MainAction				= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

$err		= 0;
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
$oUsuarios				= new Usuarios();
$oOrdenTrabajoHitos		= new OrdenTrabajoHitos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajo	= $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header("Location: iniciartrabajo.php?Mensaje=1");
	exit;
}

if (!$oUsuario = $oUsuarios->GetById($IdUsuario))
{
	header("Location: iniciartrabajo.php?Mensaje=6");
	exit;
}
$oOrdenTrabajoHitos->Begin();
try
{
	$oOrdenTrabajoHito = $oOrdenTrabajoHitos->GetLastByIdOrdenTrabajoAndIdUsuario($oOrdenTrabajo->IdOrdenTrabajo, $IdOrdenTrabajoTarea, $IdUsuario);
	if (!$oOrdenTrabajoHito || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Detener || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Finalizar || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::FinalizarSistema)
	{
		$oOrdenTrabajoHito = new OrdenTrabajoHito();
		$oOrdenTrabajoHito->IdOrdenTrabajo 		= $oOrdenTrabajo->IdOrdenTrabajo;
		$oOrdenTrabajoHito->IdOrdenTrabajoTarea	= $IdOrdenTrabajoTarea;
		$oOrdenTrabajoHito->IdUsuario			= $IdUsuario;
		$oOrdenTrabajoHito->FechaHora			= date('d/m/Y H:i:s');
		$oOrdenTrabajoHito->FechaHoraFin		= date('d/m/Y') . ' 19:00:00';
		$oOrdenTrabajoHito->TipoHito			= OrdenTrabajoHito::Iniciar;
		
		$oOrdenTrabajoHitos->Create($oOrdenTrabajoHito);

		header("Location: iniciartrabajo.php?Mensaje=4");
		exit;
	}
	else
	{
		$arrFechaFin = explode(' ', $oOrdenTrabajoHito->FechaHoraFin);
		
		//if ($arrFechaFin[0] > date('Y-m-d'))
		$oOrdenTrabajoHito->FechaHoraFin = date('d-m-Y H:i:s');
		if ($MainAction == 'finalizar')
		{
			$arrOrdenesTrabajoHitos = $oOrdenTrabajoHitos->GetLastByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo, $IdOrdenTrabajoTarea);
			if ($arrOrdenesTrabajoHitos)
			{
				foreach ($arrOrdenesTrabajoHitos as $oOrdenTrabajoHitoExistente)
				{
					if ($oOrdenTrabajoHitoExistente->IdUsuario != $IdUsuario)
					{
						
						//if ($oOrdenTrabajoHitoExistente->FechaHoraFin > date('Y-m-d H:i:s'))
						$oOrdenTrabajoHitoExistente->FechaHoraFin = date('d-m-Y H:i:s');
						$oOrdenTrabajoHitoExistente->TipoHito = OrdenTrabajoHito::Finalizar;
						
						$oOrdenTrabajoHitos->Update($oOrdenTrabajoHitoExistente);
					}
				}
			}
			
			$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($IdOrdenTrabajoTarea);
			$oOrdenTrabajoTarea->IdEstado = OrdenTrabajoTarea::IdEstadoFinalizado;
			$oOrdenesTrabajoTareas->Update($oOrdenTrabajoTarea);
			
			$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);
			
			$finalizado = true;
			foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
			{
				if ($oOrdenTrabajoTarea->IdEstado != OrdenTrabajoTarea::IdEstadoFinalizado)
				{
					$finalizado = false;
				}
			}
			
			if ($finalizado)
			{
				$oOrdenTrabajo->IdEstadoOrden = EstadoOrden::Auditoria;
				$oOrdenesTrabajo->Update($oOrdenTrabajo);
			}
			
			$oOrdenTrabajoHito->TipoHito			= OrdenTrabajoHito::Finalizar;
		}
		else
			$oOrdenTrabajoHito->TipoHito			= OrdenTrabajoHito::Detener;
		
		if ($oOrdenTrabajoHitos->Update($oOrdenTrabajoHito))
		{			
			$oOrdenTrabajoHitos->Commit();
			header("Location: iniciartrabajo.php?Mensaje=5");
			exit;
		}
	}
}
catch (Exception $ex)
{
	$oOrdenTrabajoHitos->Rollback();
}


?>
