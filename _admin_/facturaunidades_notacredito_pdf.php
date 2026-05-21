<?php 
require_once('../inc_library_includes.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oNotasCredito 		= new NotasCredito();

/* obtenemos los datos del formulario */
if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante))
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
			$File = 'facturaunidades_notacredito_pdf_a.php?IdFactura=' . $IdFactura;
			break;
		
		case ComprobanteTipos::FacturaB:
			$File = 'facturaunidades_notacredito_pdf_b.php?IdFactura=' . $IdFactura;
			break;
	}

	header('Location: ' . $File);
	exit;
}

?>