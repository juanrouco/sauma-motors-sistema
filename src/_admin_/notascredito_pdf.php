<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdNotaCredito = intval($_REQUEST['IdNotaCredito']);

$oNotasCredito = new NotasCredito();
$oComprobantes 	= new Comprobantes();

$File = '';

/* obtenemos los datos de la factura */
if (!$oNotaCredito = $oNotasCredito->GetById($IdNotaCredito))
	exit();

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante))
	exit();

switch ($oComprobante->IdTipoComprobante)
{
	case ComprobanteTipos::NotaCreditoA:
		$File = 'notascredito_pdf_a.php?IdNotaCredito=' . $IdNotaCredito;
		break;
	
	case ComprobanteTipos::NotaCreditoB:
		$File = 'notascredito_pdf_b.php?IdNotaCredito=' . $IdNotaCredito;
		break;
}

header('Location: ' . $File);
exit;

?>