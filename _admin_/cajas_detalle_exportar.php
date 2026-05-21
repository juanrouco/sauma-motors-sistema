<?php

require_once('../library/class.misc.php');
require_once('../library/class.cajasmovimientos.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oCajasMovimientos = new CajasMovimientos();

$oCajasMovimientos->ExportXls($filter);

exit();

?>