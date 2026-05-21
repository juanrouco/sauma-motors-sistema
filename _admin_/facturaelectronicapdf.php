<?php

require_once('ssi_errores.php');
require_once('../inc_library.php');

$oFacturaUnidades		= new FacturasPostVentas();
$oComprobantes		= new Comprobantes();
$oComprobantesAfip	= new ComprobantesAfip();
$oClientes			= new Clientes();
$oMinutas			= new Minutas();
$oFacturaUnidad		= $oFacturaUnidades->GetById(2794);
$oCliente			= $oClientes->GetById($oFacturaPostVenta->IdCliente);
$oComprobante		= $oComprobantes->GetById(7262);
$oComprobanteAfip	= $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
$oCliente			= $oClientes->GetById($oComprobante->IdCliente);
try 
{
	$oFacturaElectronicaPDF	= new FacturaElectronicaPDF($oComprobanteAfip);
	
    $oFacturaElectronicaPDF->CrearFacturaPostVentaAnulada($oCliente, $oFacturaUnidad);
	
} catch (Exception $e) {
	echo 'Excepción: ',  $e->getMessage(), "\n";
	if (isset($PyFEPDF)) {
	    echo "PyFEPDF.Excepcion: $PyFEPDF->Excepcion \n";
	    echo "PyFEPDF.Traceback: $PyFEPDF->Traceback \n";
	}
}
?>