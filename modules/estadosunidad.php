<?php

require_once('../library/class.estadosunidad.php');


class ModuleEstadosUnidad
{
	function GetAll(array $array)
	{
		$EstadosUnidad = new EstadosUnidad();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $EstadosUnidad->GetAll($filter, NULL);
	}
	
	function GetAllPredeterminado(array $array)
	{
		$EstadosUnidad = new EstadosUnidad();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		//$filter['Predeterminado'] = '1';
		
		return $EstadosUnidad->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$EstadosUnidad = new EstadosUnidad();

		return $EstadosUnidad->GetById($array['IdEstado']);
	}
}

?>