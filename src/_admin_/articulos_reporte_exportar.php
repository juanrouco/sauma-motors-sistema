<?php

require_once('../library/class.articulos.php');
require_once('../library/class.misc.php');

/* obtenems el filtro */
$filter = array();
$filter['Codigo'] 		= $_REQUEST['FilterCodigo'];
$filter['Descripcion']	= $_REQUEST['FilterDescripcion'];
$filter['IdProveedor']	= $_REQUEST['FilterIdProveedor'];
$filter['ClasePieza']	= $_REQUEST['FilterClasePieza'];
$filter['IdUbicacion']	= $_REQUEST['FilterIdUbicacion'];
$filter['Catalogo']		= $_REQUEST['FilterCatalogo'];
$filter['ConStock']		= '1';

$oArticulos = new Articulos();

$oArticulos->ExportReporteCsv($filter);

exit();

?>