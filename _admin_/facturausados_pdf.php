<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUsados 	= new FacturaUsados();
$oComprobantes 		= new Comprobantes();

/* obtenemos los datos del formulario */
if (!$oFacturaUsado = $oFacturaUsados->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUsado->IdComprobante))
	exit();

switch ($oComprobante->IdTipoComprobante)
{
	case ComprobanteTipos::FacturaA:
		$File = 'facturausados_pdf_a.php?IdFactura=' . $IdFactura;
		break;
	
	case ComprobanteTipos::FacturaB:
		$File = 'facturausados_pdf_b.php?IdFactura=' . $IdFactura;
		break;
}

header('Location: ' . $File);
exit;

?>