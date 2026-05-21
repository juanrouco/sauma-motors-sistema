<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutas.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

//$filter['Facturado'] = '1';
$oMinutas = new Minutas();


$oMinutas->ExportComisionesXls($filter);

exit();

?>