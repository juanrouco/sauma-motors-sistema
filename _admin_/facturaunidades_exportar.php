<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_LIST))
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
	$filter['IdMinuta'] 			= trim($_REQUEST['FilterIdMinuta']);
	$filter['NumeroComprobante'] 	= trim($_REQUEST['FilterNumeroComprobante']);
	$filter['FechaDesde'] 			= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 			= trim($_REQUEST['FilterFechaHasta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 		= array();
$oFacturaUnidades 	= new FacturaUnidades();
$oClientes 		= new Clientes();
$oPage 			= new Page($Page, $PageSize);

$arrUnidades = $oFacturaUnidades->ExportReporteCsv($filter);
?>