<?php

require_once('../library/class.misc.php');
require_once('../library/class.compras.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter		= ReceiveArray($_REQUEST['filter']);

$oCompras = new Compras();

$oCompras->ExportsVICsv($filter);


exit();

?>