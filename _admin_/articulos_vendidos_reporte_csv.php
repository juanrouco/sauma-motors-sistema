<?php 
set_time_limit(10000);

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
}

/* declaracion de variables */
$arrData 		= array();
$oStockMovimientos 	= new StockMovimientos();

$arrUnidades = $oStockMovimientos->ExportReporteVentasCsv($filter);
?>