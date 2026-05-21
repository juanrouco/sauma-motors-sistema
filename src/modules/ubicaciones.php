<?php

require_once('../library/class.ubicaciones.php');


class ModuleUbicaciones
{
	function GetAll(array $array)
	{
		$Ubicaciones = new Ubicaciones();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Ubicaciones->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Ubicaciones = new Ubicaciones();

		return $Ubicaciones->GetById($array['IdUbicacion']);
	}
}

?>