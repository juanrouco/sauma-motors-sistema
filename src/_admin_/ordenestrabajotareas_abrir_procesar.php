<?php

require_once('../inc_library.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

$IdOrdenTrabajoTarea	= intval($_REQUEST['IdOrdenTrabajoTarea']);
$IdTipoVenta			= intval($_REQUEST['IdTipoVenta']);
$Importe				= floatval($_REQUEST['Importe']);
$HorasEstimadas			= floatval($_REQUEST['HorasEstimadas']);
$Submit					= (isset($_REQUEST['Submitted']));

$err					= 0;
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
$oOrdenesTrabajo		= new OrdenesTrabajo();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajoTarea	= $oOrdenesTrabajoTareas->GetByIdIncrement($IdOrdenTrabajoTarea))
{
	header('Location: ordenestrabajotareas.php' . $strParams);
	exit;
}

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oOrdenTrabajoTarea->IdOrdenTrabajo);

/* si no hay errores... */
if ($err == 0)
{
	$oOrdenTrabajoTarea->IdEstado	= OrdenTrabajoTarea::IdEstadoReabierto;
	
	$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Update($oOrdenTrabajoTarea);
	
	$oOrdenTrabajo->IdEstadoOrden	= EstadoOrden::Aceptada;
	
	$oOrdenesTrabajo->Update($oOrdenTrabajo);

	header('Location: ordenestrabajo_detail.php' . $strParams);
	exit();
}


?>
