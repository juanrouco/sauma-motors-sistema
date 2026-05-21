<?php

require_once('../library/class.tiposdocumento.php');


class ModuleTiposDocumento
{
	function GetAll(array $array)
	{
		$TiposDocumento = new TiposDocumento();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposDocumento->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposDocumento = new TiposDocumento();

		return $TiposDocumento->GetById($array['IdTipoDocumento']);
	}
}

?>