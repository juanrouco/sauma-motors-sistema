<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

set_time_limit (100000);

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturasPostVentas		= new FacturasPostVentas();
$oComprobantes				= new Comprobantes();
$oComprobantesAfip			= new ComprobantesAfip();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oClientes					= new Clientes();

if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFactura))
{
	print_r('La factura a enviar no ha sido encontrada.');
	exit;
}
?>
<a href="ordenestrabajo_factura_afip.php?IdFactura=<?= $IdFactura ?>">Ha ocurrido un error al procesar con AFIP. Reintente</a>
<?php
try {
$oFacturaElectronicaService = new FacturaElectronicaService($oFacturaPostVenta);
if ($oFacturaElectronicaService->Procesar())
{	
	$oComprobante		= $oFacturaPostVenta->ObtenerComprobante();
	$oComprobanteAfip	= $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
	$oOrdenTrabajo		= $oOrdenesTrabajo->GetById($oFacturaPostVenta->IdOrdenTrabajo);
	$oCliente			= $oClientes->GetById($oFacturaPostVenta->IdCliente);
	try 
	{
		$oFacturaElectronicaPDF	= new FacturaElectronicaPDF($oComprobanteAfip);
		
		$oFacturaElectronicaPDF->CrearFacturaPostVenta($oCliente, $oFacturaPostVenta);
		header('Location: ordenestrabajo_facturacion.php?IdOrdenTrabajo=' . $oOrdenTrabajo->IdOrdenTrabajo);
		exit;
		
	} catch (Exception $e) {
		echo 'Excepción: ',  $e->getMessage(), "\n";
		if (isset($PyFEPDF)) {
			echo "PyFEPDF.Excepcion: $PyFEPDF->Excepcion \n";
			echo "PyFEPDF.Traceback: $PyFEPDF->Traceback \n";
		}
	}
}
} catch (Exception $e) {}

?>