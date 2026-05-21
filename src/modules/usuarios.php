<?php

require_once('../library/class.usuarios.php');

class ModuleUsuarios
{	
	function GetById(array $array)
	{
		$Usuarios = new Usuarios();

		return $Usuarios->GetById($array['IdUsuario']);
	}


	function GetAll(array $array)
	{
		$Usuarios = new Usuarios();
		
		$filter = array();		
		$filter['Usuario'] 	= $array['FilterUsuario'];
		$filter['Nombre'] 	= $array['FilterNombre'];
		$filter['Apellido'] = $array['FilterApellido'];
		$filter['IdSector'] = $array['FilterIdSector'];
		$filter['IdPerfil'] = $array['FilterIdPerfil'];

		return $Usuarios->GetAll($filter, NULL);
	}
	

	function GetAllSuggest(array $array)
	{
		$Usuarios = new Usuarios();
		
		$filter = array();		
		$filter['Usuario'] 	= $array['FilterUsuario'];
		$filter['Nombre'] 	= $array['FilterNombre'];
		$filter['Apellido'] = $array['FilterApellido'];
		$filter['IdSector'] = $array['FilterIdSector'];
		$filter['IdPerfil'] = $array['FilterIdPerfil'];
		$oPage 				= new Page(0, 10);
		$arr = $Usuarios->GetAll($filter, NULL);
		
		foreach ($arr as $oUsuario)
		{
			$oUsuario->Nombre = $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
		}
		
		return $arr;
	}
}

?>