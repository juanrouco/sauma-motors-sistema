<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee PERM_CMPB_LIST */
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
	$filter['Numero'] 	= trim($_REQUEST['FilterNumero']);
	$filter['Proveedor'] = trim($_REQUEST['FilterProveedor']);
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['IdTipoComprobante'] = trim($_REQUEST['FilterIdTipoComprobante']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 		= array();
$oFacturasCompras 	= new FacturasCompras();

$oFacturasCompras->ExportReporteCsv($filter);
?>