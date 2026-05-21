<?php

require_once('../library/class.localidades.php');
require_once('../library/class.partidos.php');
require_once('../library/class.provincias.php');


class ModuleLocalidades
{
	function GetAll(array $array)
	{
		$Localidades = new Localidades();
		
		$filter = array();		
		$filter['Nombre'] 		= $array['FilterNombre'];
		$filter['CodigoPostal'] = $array['FilterCodigoPostal'];
		$filter['IdPais'] 		= $array['FilterIdPais'];
		$filter['IdProvincia'] 	= $array['FilterIdProvincia'];
		$filter['IdPartido'] 	= $array['FilterIdPartido'];
		
		return $Localidades->GetAll($filter, NULL);
	}


	function GetAllSuggest(array $array)
	{
		$Localidades 	= new Localidades();
		
		$filter = array();		
		$filter['Nombre'] 		= $array['FilterNombre'];

		$arr = $Localidades->GetAllSuggest($filter);
		
		return $arr;
	}
	
	
	function GetById(array $array)
	{
		$Localidades = new Localidades();

		return $Localidades->GetById($array['IdLocalidad']);
	}
}

?>