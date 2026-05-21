<?php

require_once('../library/class.misc.php');
require_once('../library/class.unidades.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oUnidades = new Unidades();
if ($_REQUEST['fullpermisos'])
	$oUnidades->ExportXls($filter, true);
else
	$oUnidades->ExportXls($filter, false);

exit();

?>