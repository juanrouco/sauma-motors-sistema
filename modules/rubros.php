<?php

require_once('../library/class.rubro.php');
require_once('../library/class.rubros.php');


class ModuleRubros
{
	function GetById(array $array)
	{
		$Rubros = new Rubros();

		return $Rubros->GetById($array['IdRubro']);
	}
	
	function Update(array $array)
	{
		$Rubros = new Rubros();

		/* obtiene los datos del registro */
		if (!$oRubro = $Rubros->GetById($array['IdRubro']))
			return false;
		
		$oRubro->IdEstado = $array['IdEstado'];
		
		return $Rubros->UpdateChecks($oRubro);
	}
	
	function GetAll(array $array)
	{
		$Rubros = new Rubros();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Nombre'];
		
		return $Rubros->GetAll($filter, $oPage);
	}
}

?>