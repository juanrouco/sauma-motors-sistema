<?php

require_once('../library/class.misc.php');
require_once('../library/class.cuentasgestoria.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);
$filter['Rendido'] = '1';
$filter['SinRendir'] = false;

$oCuentasGestoriaUsados = new CuentasGestoria();

$oCuentasGestoriaUsados->ExportXls($filter, false);

exit();

?>