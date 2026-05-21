<?php

//require_once('ssi_errores.php');
require_once('../library/class.usados.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);
$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);

$oUsados = new Usados();
if ($_REQUEST['EnStock'])
{
	$oUsados->ExportReporteStockCsv($filter);
}
else
{
	$oUsados->ExportReporteVendidasCsv($filter);
}

exit();

?>