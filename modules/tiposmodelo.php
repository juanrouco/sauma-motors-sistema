<?php

require_once('../library/class.tiposmodelo.php');

class ModuleTiposModelo
{
	function GetAll(array $array)
	{
		$TiposModelo = new TiposModelo();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposModelo->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposModelo = new TiposModelo();

		return $TiposModelo->GetById($array['IdTipoModelo']);
	}
}

?>