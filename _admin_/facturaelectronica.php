<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

try {
	$oComprobantes			= new Comprobantes();
	$oComprobantesAfip		= new ComprobantesAfip();
	
	$oComprobante = $oComprobantes->GetById(5372);
		
	$oComprobanteAfip = new ComprobanteAfip();
	$oComprobanteAfip->CreateFromComprobante($oComprobante);
	$oComprobanteAfip->PuntoVenta = ConfiguracionFactura::PuntoVenta;
	//$oComprobanteAfip->Numero = 2;
	$oComprobantesAfip->Create($oComprobanteAfip);
	
	$oFacturaElectronica	= new FacturaElectronica($oComprobanteAfip);
	
	if ($oFacturaElectronica->AutenticarAfip())
	{	
		$oFacturaElectronica->InicializarWSFacturacion();
		$oFacturaElectronica->AsignarNumero();
		$oComprobanteAfip = $oFacturaElectronica->AsignarCae();
		
		
		$oComprobantesAfip->Update($oComprobanteAfip);
		print_r('todo ok');
	}

} catch (Exception $e) {
	$oFacturaElectronica->LogError($e);
}

?>