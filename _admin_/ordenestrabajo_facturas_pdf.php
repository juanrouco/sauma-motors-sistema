<?php 

require_once('../inc_library.php');

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

switch ($oComprobante->IdTipoComprobante)
{
	case ComprobanteTipos::FacturaA:
		$File = 'ordenestrabajo_facturas_pdf_a.php?IdFacturaPostVenta=' . $IdFacturaPostVenta;
		break;
	
	case ComprobanteTipos::FacturaB:
		$File = 'ordenestrabajo_facturas_pdf_b.php?IdFacturaPostVenta=' . $IdFacturaPostVenta;
		break;
}

header('Location: ' . $File);
exit;

?>