<?php

require_once('../library/class.provincias.php');


class ModuleProvincias
{
	function GetById(array $array)
	{
		$Provincias = new Provincias();

		return $Provincias->GetById($array['IdProvincia']);
	}

	
	function GetAll(array $array)
	{
		$Provincias = new Provincias();
		
		if ($array['CurrentPage'])
			$oPage = new Page($array['CurrentPage']);
		else
			$oPage = NULL;
		
		$filter = array();

		$filter['Nombre'] = $array['Nombre'];
		$filter['IdPais'] = $array['IdPais'];
		
		return $Provincias->GetAll($filter, $oPage);
	}


	function Create(array $array)
	{
		$Provincias = new Provincias();
		$oProvincia = new Provincia();

		$oProvincia->Nombre = $array['Nombre'];
		$oProvincia->IdPais = $array['IdPais'];
			
		$oProvincia = $Provincias->Create($oProvincia);
		if (!$oProvincia)
			return false;
		
		return $oProvincia;
	}


	function Update(array $array)
	{
		$Provincias = new Provincias();

		/* obtiene los datos del registro */
		$oProvincia = $Provincias->GetById($array['IdProvincia']);
		if (!$oProvincia)
			return false;
		
		$oProvincia->Nombre = $array['Nombre'];
		$oProvincia->IdPais = $array['IdPais'];
		
		return $Provincias->Update($oProvincia);
	}


	function Delete(array $array)
	{
		$Provincias = new Provincias();

		/* obtiene los datos del registro */
		$oProvincia = $Provincias->GetById($array['IdProvincia']);
		if (!$oProvincia)
			return false;
		
		return $Provincias->Delete($oProvincia->IdProvincia);
	}
	
	
	function GetAllUsed(array $array)
	{
		$Provincias = new Provincias();
		
		if ($array['CurrentPage'])
			$oPage = new Page($array['CurrentPage']);
		else
			$oPage = NULL;
		
		$filter = array();

		$filter['Nombre'] = $array['Nombre'];
		$filter['IdPais'] = $array['IdPais'];
		
		return $Provincias->GetAllUsedByPais($filter);
	}
}

?>