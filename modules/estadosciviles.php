<?php

require_once('../library/class.estadosciviles.php');


class ModuleEstadosCiviles
{
	function GetAll(array $array)
	{
		$EstadosCiviles = new EstadosCiviles();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $EstadosCiviles->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$EstadosCiviles = new EstadosCiviles();

		return $EstadosCiviles->GetById($array['IdEstadoCivil']);
	}
}

?>