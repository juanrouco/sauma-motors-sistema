<?php 


require_once('../inc_library_includes.php');
/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFacturaPostVenta = intval($_REQUEST['IdFacturaPostVenta']);

$oFacturasPostVentas 	= new FacturasPostVentas();
$oComprobantes 			= new Comprobantes();

/* obtenemos los datos del formulario */
if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta))
	exit();
	
/* obtenemos los datos del formulario */
if (!$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante))
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
			$File = 'facturaspostventas_pdf_a.php?IdFacturaPostVenta=' . $IdFacturaPostVenta;
			break;
		
		case ComprobanteTipos::FacturaB:
			$File = 'facturaspostventas_pdf_b.php?IdFacturaPostVenta=' . $IdFacturaPostVenta;
			break;
		
		case ComprobanteTipos::FacturaC:
			$File = 'facturaspostventas_pdf_c.php?IdFacturaPostVenta=' . $IdFacturaPostVenta;
			break;
	}

	header('Location: ' . $File);
	exit;
}

?>