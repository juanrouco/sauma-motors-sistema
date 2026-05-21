<?php

require_once('../library/class.unidades.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);

$oUnidades = new Unidades();
$oUnidades->ExportReporteMinutasCsv($filter);

exit();

?>