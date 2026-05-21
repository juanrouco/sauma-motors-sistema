<?php 

require_once('../inc_library_includes.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaVarias = new FacturaVarias();
$oComprobantes 	= new Comprobantes();

/* obtenemos los datos de la factura */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaVaria->IdComprobante))
	exit();

if ($oComprobante->Archivo)
{
	header('Content-Disposition: attachment; filename="factura.pdf"');
	readfile(Comprobante::PathFile . $oComprobante->Archivo);
}
else
{
	switch ($oComprobante->IdTipoComprobante)
	{
		case ComprobanteTipos::FacturaA:
			$File = 'facturavarias_pdf_a.php?IdFactura=' . $IdFactura;
			break;
		
		case ComprobanteTipos::FacturaB:
			$File = 'facturavarias_pdf_b.php?IdFactura=' . $IdFactura;
			break;
	}

	header('Location: ' . $File);
	exit;
}

?>