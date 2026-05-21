<?php

require_once('../library/class.tiposiva.php');


class ModuleTiposIva
{
	function GetAll(array $array)
	{
		$TiposIva = new TiposIva();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposIva->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposIva = new TiposIva();

		return $TiposIva->GetById($array['IdTipoIva']);
	}
}

?>