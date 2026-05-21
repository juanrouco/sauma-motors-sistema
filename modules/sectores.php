<?php

require_once('../library/class.sectores.php');


class ModuleSectores
{
	function GetAll(array $array)
	{
		$Sectores = new Sectores();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Sectores->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Sectores = new Sectores();

		return $Sectores->GetById($array['IdProfesion']);
	}
}

?>