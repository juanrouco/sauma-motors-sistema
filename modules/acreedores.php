<?php

require_once('../library/class.acreedores.php');

class ModuleAcreedores
{	
	function GetAll(array $array)
	{
		$Acreedores = new Acreedores();

		$filter = array();		
		$filter['RazonSocial'] 			= $array['FilterRazonSocial'];
		$filter['Email'] 				= $array['FilterEmail'];
		$filter['ClaveFiscalNumero'] 	= $array['FilterFiscalNumero'];
		$filter['IdTipoPersona'] 		= $array['FilterIdTipoPersona'];
		
		return $Acreedores->GetAll($filter, NULL);
	}


	function GetById(array $array)
	{
		$Acreedores = new Acreedores();

		return $Acreedores->GetById($array['IdAcreedor']);
	}
}

?>