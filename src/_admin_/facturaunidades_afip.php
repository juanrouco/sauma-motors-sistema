<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

set_time_limit (100000);

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUnidades			= new FacturaUnidades();
$oComprobantes				= new Comprobantes();
$oComprobantesAfip			= new ComprobantesAfip();
$oMinutas					= new Minutas();

if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
{
	print_r('La factura a enviar no ha sido encontrada.');
	exit;
}

$oFacturaElectronicaService = new FacturaElectronicaService($oFacturaUnidad);
if ($oFacturaElectronicaService->Procesar())
{	
	$oComprobante		= $oFacturaUnidad->ObtenerComprobante();
	$oComprobanteAfip	= $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
	$oMinuta			= $oMinutas->GetById($oFacturaUnidad->IdMinuta);
	try 
	{
		$oFacturaElectronicaPDF	= new FacturaElectronicaPDF($oComprobanteAfip);
		
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