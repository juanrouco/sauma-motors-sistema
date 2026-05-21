<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutas.php');

/* obtenems el filtro */
$filter	= array();
//$filter['FechaMinutaDesde'] = date('d-m-Y');
$filter['FechaMinutaHasta'] = date('d-m-Y');

$oMinutas = new Minutas();

$oMinutas->ExportXlsReporte($filter);

exit();

?>