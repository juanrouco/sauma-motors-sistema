<?php
require_once('../library/class.misc.php');
require_once('../library/class.ordenestrabajo.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter		= ReceiveArray($_REQUEST['filter']);

$oOrdenesTrabajo = new OrdenesTrabajo();

$oOrdenesTrabajo->ExportCsv($filter);


exit();

?>