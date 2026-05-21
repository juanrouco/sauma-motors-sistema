<?php

require_once('../library/class.colores.php');


class ModuleColores
{
	function GetAll(array $array)
	{
		$Colores = new Colores();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Colores->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Colores = new Colores();

		return $Colores->GetById($array['IdColor']);
	}
}

?>