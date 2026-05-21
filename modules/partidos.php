<?php

require_once('../library/class.partidos.php');


class ModulePartidos
{
	function GetById(array $array)
	{
		$Partidos = new Partidos();

		return $Partidos->GetById($array['IdPartido']);
	}

	
	function GetAll(array $array)
	{
		$Partidos = new Partidos();
		
		if ($array['CurrentPage'])
			$oPage = new Page($array['CurrentPage']);
		else
			$oPage = NULL;
		
		$filter = array();

		$filter['Nombre'] = $array['Nombre'];
		$filter['IdProvincia'] = $array['IdProvincia'];
		
		return $Partidos->GetAll($filter, $oPage);
	}
}

?>