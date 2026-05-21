<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutas.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oMinutas = new Minutas();

$oMinutas->ExportXls($filter);

exit();

?>