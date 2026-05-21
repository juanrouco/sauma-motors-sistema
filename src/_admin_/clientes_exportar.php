<?php

require_once('../library/class.clientes.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oClientes = new Clientes();

$oClientes->ExportXls($filter);

exit();

?>