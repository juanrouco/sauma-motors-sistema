<?php

require_once('../library/class.perfiles.php');


class ModulePerfiles
{
	function GetAll(array $array)
	{
		$Perfiles = new Perfiles();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Perfiles->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Perfiles = new Perfiles();

		return $Perfiles->GetById($array['IdProfesion']);
	}
}

?>