<?php

//require_once('ssi_errores.php');
require_once('../library/class.cheques.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$oCheques = new Cheques();

$oCheques->ExportCsv($filter);
exit();

?>