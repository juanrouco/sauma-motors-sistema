<?php

require_once('../library/class.tipovehiculo.php');
require_once('../library/class.tiposvehiculo.php');


class ModuleTiposVehiculo
{
	function GetAll(array $array)
	{
		$TiposVehiculo = new TiposVehiculo();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $TiposVehiculo->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$TiposVehiculo = new TiposVehiculo();

		return $TiposVehiculo->GetById($array['IdTipoVehiculo']);
	}
}

?>