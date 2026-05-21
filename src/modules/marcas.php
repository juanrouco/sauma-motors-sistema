<?php

require_once('../library/class.marca.php');
require_once('../library/class.marcas.php');


class ModuleMarcas
{
	function GetAll(array $array)
	{
		$Marcas = new Marcas();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Marcas->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Marcas = new Marcas();

		return $Marcas->GetById($array['IdMarca']);
	}
}

?>