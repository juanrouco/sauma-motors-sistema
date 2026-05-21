<?php

require_once('../library/class.misc.php');
require_once('../library/class.modelos.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oModelos = new Modelos();

$oModelos->ExportXls($filter);

exit();

?>