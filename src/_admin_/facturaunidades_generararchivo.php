<?php
require_once('../library/class.misc.php');
require_once('../library/class.facturaunidades.php');

$IdFacturaUnidad = $_REQUEST['IdFactura'];

$oFacturaUnidades = new FacturaUnidades();
$oFactura = $oFacturaUnidades->GetById($IdFacturaUnidad);

$oFacturaUnidades->GenerarArchivoFacturacion($oFactura);
exit();

?>