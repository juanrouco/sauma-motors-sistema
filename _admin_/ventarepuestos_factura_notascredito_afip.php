<?php
require_once('../inc_library.php');

set_time_limit (100000);

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturasPostVentas		= new FacturasPostVentas();
$oComprobantes				= new Comprobantes();
$oComprobantesAfip			= new ComprobantesAfip();
$oCompras			= new Compras();
$oClientes					= new Clientes();
$oNotasCredito				= new NotasCredito();

if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFactura))
{
	print_r('La factura a enviar no ha sido encontrada.');
	exit;
}

if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaPostVenta->IdComprobante))
{
	print_r('La nota de credito a enviar no ha sido encontrada.');
	exit;
}

$oFacturaElectronicaService = new FacturaElectronicaService($oNotaCredito);
if ($oFacturaElectronicaService->Procesar())
{	
	$oComprobante		= $oFacturaPostVenta->ObtenerComprobante();
	$oComprobanteNc		= $oComprobantes->GetById($oNotaCredito->IdComprobante);
	$oComprobanteAfip			= $oComprobantesAfip->GetByIdComprobante($oComprobanteNc->IdComprobante);
	$oComprobanteAfipAsociado	= $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
	$oCompra			= $oCompras->GetById($oFacturaPostVenta->IdCompra);
	$oCliente			= $oClientes->GetById($oFacturaPostVenta->IdCliente);
	try 
	{
		$oFacturaElectronicaPDF	= new FacturaElectronicaPDF($oComprobanteAfip, $oComprobanteAfipAsociado);
		
		$oFacturaElectronicaPDF->CrearFacturaPostVenta($oCliente, $oFacturaPostVenta);
		header('Location: ventarepuestos.php');
		exit;
		
	} catch (Exception $e) {
		echo 'Excepción: ',  $e->getMessage(), "\n";
		if (isset($PyFEPDF)) {
			echo "PyFEPDF.Excepcion: $PyFEPDF->Excepcion \n";
			echo "PyFEPDF.Traceback: $PyFEPDF->Traceback \n";
		}
	}
}

?>