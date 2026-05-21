<?php

require_once('../library/class.modelos.php');

class ModuleModelos
{		
	function GetById(array $array)
	{
		$Modelos = new Modelos();

		return $Modelos->GetById($array['IdModelo']);
	}

	function GetAll(array $array)
	{
		$Modelos = new Modelos();
		
		$filter = array();		
		$filter['DenominacionComercial'] 	= $array['FilterDenominacionComercial'];
		$filter['CodigoComercial'] 			= $array['FilterCodigoComercial'];
		$filter['NumeroVinPrefijo'] 		= $array['FilterNumeroVinPrefijo'];
		$filter['IdTipoModelo'] 			= $array['FilterIdTipoModelo'];
		$filter['IdMarcaMotor'] 			= $array['FilterIdMarcaMotor'];
		$filter['IdMarcaChasis'] 			= $array['FilterIdMarcaChasis'];
		$filter['IdMarcaVehiculo'] 			= $array['FilterIdMarcaVehiculo'];
		
		$oPage 				= new Page(0, 10);		
		return $Modelos->GetAll($filter, $oPage);
	}

	function GetAllModelos(array $array)
	{
		$Modelos = new Modelos();
		
		$filter = array();		
		$filter['DenominacionComercial'] 	= $array['FilterDenominacionComercial'];
		$filter['CodigoComercial'] 			= $array['FilterCodigoComercial'];
		$filter['NumeroVinPrefijo'] 		= $array['FilterNumeroVinPrefijo'];
		$filter['IdTipoModelo'] 			= $array['FilterIdTipoModelo'];
		$filter['IdMarcaMotor'] 			= $array['FilterIdMarcaMotor'];
		$filter['IdMarcaChasis'] 			= $array['FilterIdMarcaChasis'];
		$filter['IdMarcaVehiculo'] 			= $array['FilterIdMarcaVehiculo'];
		
		$oPage 				= new Page(0, 10);		
		return $Modelos->GetAllModelos($filter, $oPage);
	}
	
	function GetAllNumeroLista(array $array)
	{
		$Modelos = new Modelos();
		
		$filter = array();		
		$filter['DenominacionComercial'] 	= $array['FilterDenominacionComercial'];
		$filter['CodigoComercial'] 			= $array['FilterCodigoComercial'];
		$filter['NumeroVinPrefijo'] 		= $array['FilterNumeroVinPrefijo'];
		$filter['IdTipoModelo'] 			= $array['FilterIdTipoModelo'];
		$filter['IdMarcaMotor'] 			= $array['FilterIdMarcaMotor'];
		$filter['IdMarcaChasis'] 			= $array['FilterIdMarcaChasis'];
		$filter['IdMarcaVehiculo'] 			= $array['FilterIdMarcaVehiculo'];

		return $Modelos->GetAllNumeroLista($filter, NULL);
	}
	
}

?>