<?php

require_once('../library/class.misc.php');
require_once('../library/class.usados.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oUsados = new Usados();

$oUsados->ExportXls($filter);

exit();

?>