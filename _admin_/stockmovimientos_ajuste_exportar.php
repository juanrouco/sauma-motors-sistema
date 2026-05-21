<?php

require_once('../library/class.stockmovimientos.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['FechaDesde'] 		= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']		= $_REQUEST['FilterFechaHasta'];
$filter['IdUbicacion']		= $_REQUEST['FilterIdUbicacion'];
$filter['TipoOperacion']	= $_REQUEST['FilterTipoOperacion'];
$filter['IdArticulo']		= $_REQUEST['IdArticulo'];

$IdArticulo 	= $_REQUEST['IdArticulo'] ;
$IdUbicacion 	= $_REQUEST['FilterIdUbicacion'] ;

$oStockMovimientos = new StockMovimientos();

$oStockMovimientos->ExportAjustesCsv($filter);

exit();

?>