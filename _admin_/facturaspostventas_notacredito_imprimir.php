<?php
require_once('../inc_library.php'); 
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdFacturaPostVenta			= intval($_REQUEST['IdFacturaPostVenta']);

$oFacturasPostVentas					= new FacturasPostVentas();
$oComprobantes							= new Comprobantes();
$oNotasCredito							= new NotasCredito();
$oGeneradorFacturaNotaCreditoPostVenta	= new GeneradorNotasCreditoFacturasPostVentas();

$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta);
$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);


/* obtenemos los datos del comprobante */
if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobanteNC = $oComprobantes->GetById($oNotaCredito->IdComprobante))
	exit();

if ($oComprobanteNC->Archivo)
{
	header('Content-Disposition: attachment; filename="nota de credito.pdf"');
	readfile(Comprobante::PathFile . $oComprobanteNC->Archivo);
}
else
{
	$oGeneradorFacturaNotaCreditoPostVenta->Imprimir($oFacturaPostVenta);


?>
<script type="text/javascript">window.close();</script>
<?php
}
?>