<?php

require_once('../library/class.unidades.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
$filter['FechaArriboDesde'] = trim($_REQUEST['FilterFechaArriboDesde']);
$filter['FechaArriboHasta'] = trim($_REQUEST['FilterFechaArriboHasta']);
$filter['IdEstado'] 		= array();
$filter['IdEstado'][0] 		= EstadoUnidad::PreVenta;
$filter['IdEstado'][1] 		= EstadoUnidad::PreVentaReservado;

$oUnidades = new Unidades();

$oUnidades->ExportReportePreventaCsv($filter);

exit();

?>