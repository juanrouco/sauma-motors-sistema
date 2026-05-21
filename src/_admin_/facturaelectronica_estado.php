<?php
//require_once('ssi_errores.php');
require_once('../inc_library.php');

try 
{
	$oFacturaElectronica	= new FacturaElectronica(null);
	
	if ($oFacturaElectronica->AutenticarAfip())
	{	
		$oFacturaElectronica->InicializarWSFacturacion(true);
	}

} catch (Exception $e) {
	//$oFacturaElectronica->LogError($e);
}

?>