<?php

require_once('../library/class.misc.php');
require_once('../library/class.articulos.php');
require_once('../library/class.session.php');

/* obtenems el filtro */
$filter = array();
$filter['Codigo'] 		= $_REQUEST['FilterCodigo'];
$filter['Descripcion']	= $_REQUEST['FilterDescripcion'];
$filter['IdProveedor']	= $_REQUEST['FilterIdProveedor'];
$filter['ClasePieza']	= $_REQUEST['FilterClasePieza'];
$filter['Industria']	= $_REQUEST['FilterIndustria'];
$filter['Catalogo']		= $_REQUEST['FilterCatalogo'];
$filter['ConStock']		= $_REQUEST['FilterConStock'];

$oArticulos = new Articulos();

$oArticulos->ExportCsv($filter);


exit();

?>