<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACTPV_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroComprobante'] 	= trim($_REQUEST['FilterNumeroComprobante']);
	$filter['FechaHasta'] 			= trim($_REQUEST['FilterFechaHasta']);
	$filter['FechaDesde'] 			= trim($_REQUEST['FilterFechaDesde']);
	$filter['Cliente'] 				= trim($_REQUEST['FilterCliente']);
	$filter['Cuil'] 				= trim($_REQUEST['FilterCuil']);
	$filter['IdFormaPago'] 			= trim($_REQUEST['FilterIdFormaPago']);
	$filter['IdPlanCuota'] 			= trim($_REQUEST['FilterIdPlanCuota']);
}
$filter['Tarjeta'] = true;



/* declaracion de variables */
$oFacturasPostVentas 	= new FacturasPostVentas();

$arr = $oFacturasPostVentas->ExportReporteCuotasCsv($filter);
?>