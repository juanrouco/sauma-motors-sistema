<?php

//require_once('ssi_errores.php');
require_once('../library/class.unidades.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);
$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);

$oUnidades = new Unidades();
if ($_REQUEST['EnStock'])
{
	$oUnidades->ExportReporteStockCsv($filter);
}
else
{
	$oUnidades->ExportReporteVendidasCsv($filter);
}

exit();

?>