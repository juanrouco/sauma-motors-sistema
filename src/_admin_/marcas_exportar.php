<?php

require_once('../library/class.marcas.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oMarcas = new Marcas();

$oMarcas->ExportXls($filter);

exit();

?>