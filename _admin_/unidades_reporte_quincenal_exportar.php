<?php

require_once('../library/class.modelos.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */

$filter	= array();
$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);	
$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);	
$oModelos = new Modelos();

$oModelos->ExportReporteQuincenalCsv($filter);

exit();

?>