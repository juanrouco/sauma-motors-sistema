<?php 

require_once('../inc_library_includes.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();

/* obtenemos los datos del formulario */
if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante))
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
			$File = 'facturaunidades_pdf_a.php?IdFactura=' . $IdFactura;
			break;
		
		case ComprobanteTipos::FacturaB:
			$File = 'facturaunidades_pdf_b.php?IdFactura=' . $IdFactura;
			break;
	}

	header('Location: ' . $File);
	exit;
}

?>