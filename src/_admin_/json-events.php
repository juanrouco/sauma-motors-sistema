<?php
require_once('../inc_library_includes.php');

$FechaDesde = date('d-m-Y', intval($_REQUEST['start']));
$FechaHasta = date('d-m-Y', intval($_REQUEST['end']));

$oTurnos 			= new Turnos();
$oOrdenesTrabajo 	= new OrdenesTrabajo();
$oEstadosOrden 		= new EstadosOrden();

$filter = array();
$filter['FechaInicioDesde'] = $FechaDesde;
$filter['FechaInicioHasta'] = $FechaHasta;
$arrTurnos = $oTurnos->GetAll($filter);

$ordenes = array();

foreach ($arrTurnos as $oTurno)
{
	//if ($oTurno->Estado ==)
	
	$fechaFin = new DateTime($oTurno->FechaInicio);
	$fechaFin->modify("+15 minutes");
	
	$oEstadoOrden = $oEstadosOrden->GetById($oTurno->IdEstadoOrden);
	if ($oTurno->IdOrdenTrabajo)
	{
		$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oTurno->IdOrdenTrabajo);
		$oEstadoOrden = $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
	}
	$Id = $oTurno->IdOrdenTrabajo ? $oTurno->IdOrdenTrabajo : $oTurno->IdTurno;
	$Title = $oTurno->IdOrdenTrabajo ? 'OT N° ' : 'Turno N° ';
	$Url = $oTurno->IdOrdenTrabajo ? 'ordenestrabajo_detail.php?IdOrdenTrabajo=' : 'turnos_detail.php?IdTurno=';
	$ordenes[] = array(
			'id' => $oTurno->IdTurno,
			'title' => $Title . $Id,
			'start' => $oTurno->FechaInicio,
			'end' => $fechaFin->format('Y-m-d H:i:s'),
			'allDay' => false,
			'url' => $Url . $Id,
			'color' => $oEstadoOrden->IdEstado == EstadoOrden::Presupuesto && $oTurno->Bahia ? '#574432' : $oEstadoOrden->Color,
			'borderColor' => '#ffffff'
	);
}
	

echo json_encode($ordenes);

?>
