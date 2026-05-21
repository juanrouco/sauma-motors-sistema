<?php

require_once('../library/class.misc.php');
require_once('../library/class.minutasusados.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

$filter['Entregado'] = '1';
$oMinutas = new MinutasUsados();

$oMinutas->ExportComisionesXls($filter);

exit();

?>