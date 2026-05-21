<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutasusados.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oMinutas = new MinutasUsados();

$oMinutas->ExportXls($filter);

exit();

?>