<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

try 
{
	$oComprobantesAfip = new ComprobantesAfip();
	$oComprobanteAfip = $oComprobantesAfip->GetById(8041);
	$oFacturaElectronica	= new FacturaElectronica($oComprobanteAfip);
	
	if ($oFacturaElectronica->AutenticarAfip())
	{	
		$oFacturaElectronica->InicializarWSFacturacion(true);
		$res = $oFacturaElectronica->ConsultarComprobante(6, 4, 2020);
		print_R($res);exit;
	}

} catch (Exception $e) {
	print_r($e);
	//$oFacturaElectronica->LogError($e);
}

?>