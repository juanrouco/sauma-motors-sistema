<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

set_time_limit (100000);

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUnidades		= new FacturaUnidades();
$oComprobantesAfip		= new ComprobantesAfip();
$oComprobantes			= new Comprobantes();
$oClientes				= new Clientes();
$oNotasCredito			= new NotasCredito();
$oMinutas				= new Minutas();

if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
{
	print_r('La factura a enviar no ha sido encontrada.');
	exit;
}

if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaUnidad->IdComprobante))
{
	print_r('La nota de credito a enviar no ha sido encontrada.');
	exit;
}

$oFacturaElectronicaService = new FacturaElectronicaService($oNotaCredito);
if ($oFacturaElectronicaService->Procesar())
{	
	$oComprobante		= $oFacturaUnidad->ObtenerComprobante();
	$oComprobanteNc		= $oComprobantes->GetById($oNotaCredito->IdComprobante);
	$oComprobanteAfip			= $oComprobantesAfip->GetByIdComprobante($oComprobanteNc->IdComprobante);
	$oComprobanteAfipAsociado	= $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
	$oCliente			= $oClientes->GetById($oComprobante->IdCliente);
	$oMinuta			= $oMinutas->GetById($oFacturaUnidad->IdMinuta);
	try 
	{
		$oFacturaElectronicaPDF	= new FacturaElectronicaPDF($oComprobanteAfip, $oComprobanteAfipAsociado);
		
		$oFacturaElectronicaPDF->CrearFacturaMinuta($oMinuta, $oFacturaUnidad);
		header('Location: facturaunidades.php');
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