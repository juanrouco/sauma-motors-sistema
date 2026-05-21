<?php

require_once('../library/class.profesiones.php');


class ModuleProfesiones
{
	function GetAll(array $array)
	{
		$Profesiones = new Profesiones();
		
		$filter = array();		
		$filter['Nombre'] = $array['FilterNombre'];
		
		return $Profesiones->GetAll($filter, NULL);
	}
	
	
	function GetById(array $array)
	{
		$Profesiones = new Profesiones();

		return $Profesiones->GetById($array['IdProfesion']);
	}
}

?>