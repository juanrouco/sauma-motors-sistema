<?php

require_once('../library/class.destinovehiculo.php');
require_once('../library/class.destinosvehiculo.php');


class ModuleDestinosVehiculo
{
	function GetAll(array $array)
	{
		$DestinosVehiculo = new DestinosVehiculo();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $DestinosVehiculo->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$DestinosVehiculo = new DestinosVehiculo();

		return $DestinosVehiculo->GetById($array['IdDestinoVehiculo']);
	}
}

?>