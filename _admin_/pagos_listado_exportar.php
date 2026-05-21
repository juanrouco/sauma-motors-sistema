<?php

//require_once('ssi_errores.php');
require_once('../library/class.pagos.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['FechaDesde'] 	= trim($_REQUEST['FilterFechaDesde']);
$filter['FechaHasta'] 	= trim($_REQUEST['FilterFechaHasta']);
$filter['Interno'] 		= trim($_REQUEST['FilterInterno']);
$filter['Pago'] 		= trim($_REQUEST['FilterPago']);
$filter['IdTipoPago'] 	= trim($_REQUEST['IdTipoPago']);

$oPagos = new Pagos();

$oPagos->ExportCsv($filter);
exit();

?>