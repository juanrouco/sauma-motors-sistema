<?php 

require_once('../inc_library_includes.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaVarias = new FacturaVarias();
$oComprobantes 	= new Comprobantes();
$oNotasCredito 	= new NotasCredito();

/* obtenemos los datos de la factura */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaVaria->IdComprobante))
	exit();

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
	switch ($oComprobante->IdTipoComprobante)
	{
		case ComprobanteTipos::FacturaA:
			$File = 'facturavarias_notacredito_pdf_a.php?IdFactura=' . $IdFactura;
			break;
		
		case ComprobanteTipos::FacturaB:
			$File = 'facturavarias_notacredito_pdf_b.php?IdFactura=' . $IdFactura;
			break;
	}

	header('Location: ' . $File);
	exit;
}

?>