<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutasespera.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oMinutasEspera = new MinutasEspera();

$oMinutasEspera->ExportXls($filter);

exit();

?>