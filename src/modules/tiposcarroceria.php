<?php

require_once('../library/class.tipocarroceria.php');
require_once('../library/class.tiposcarroceria.php');


class ModuleTiposCarroceria
{
	function GetAll(array $array)
	{
		$TiposCarroceria = new TiposCarroceria();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposCarroceria->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposCarroceria = new TiposCarroceria();

		return $TiposCarroceria->GetById($array['IdTipoCarroceria']);
	}
}

?>