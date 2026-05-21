<?php

require_once('../library/class.paises.php');

class ModulePaises
{
	function GetById(array $array)
	{
		$Paises = new Paises();

		return $Paises->GetById($array['IdPais']);
	}

	
	function GetAll(array $array)
	{
		$Paises = new Paises();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Nombre'];
		
		return $Paises->GetAll($filter, $oPage);
	}


	function Create(array $array)
	{
		$Paises = new Paises();
		$oPais 	= new Pais();

		$oPais->Nombre = $array['Nombre'];
			
		$oPais = $Paises->Create($oPais);
		if (!$oPais)
			return false;
		
		return $oPais;
	}


	function Update(array $array)
	{
		$Paises = new Paises();

		/* obtiene los datos del registro */
		$oPais = $Paises->GetById($array['IdPais']);
		if (!$oPais)
			return false;
		
		$oPais->Nombre = $array['Nombre'];
		
		return $Paises->Update($oPais);
	}


	function Delete(array $array)
	{
		$Paises = new Paises();

		/* obtiene los datos del registro */
		$oPais = $Paises->GetById($array['IdPais']);
		if (!$oPais)
			return false;
		
		return $Paises->Delete($oPais->IdPais);
	}
	
	
	function GetAllUsed(array $array)
	{
		$Paises = new Paises();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Nombre'];
		
		return $Paises->GetAllUsed($filter, $oPage);
	}
}

?>