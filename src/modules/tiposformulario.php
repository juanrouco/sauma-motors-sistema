<?php

require_once('../library/class.tiposformulario.php');


class ModuleTiposFormulario
{
	function GetAll(array $array)
	{
		$TiposFormulario = new TiposFormulario();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposFormulario->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposFormulario = new TiposFormulario();

		return $TiposFormulario->GetById($array['IdTipoFormulario']);
	}
}

?>