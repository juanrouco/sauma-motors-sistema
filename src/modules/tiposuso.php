<?php

require_once('../library/class.tipouso.php');
require_once('../library/class.tiposuso.php');


class ModuleTiposUso
{
	function GetAll(array $array)
	{
		$TiposUso = new TiposUso();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposUso->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposUso = new TiposUso();

		return $TiposUso->GetById($array['IdTipoUso']);
	}
}

?>