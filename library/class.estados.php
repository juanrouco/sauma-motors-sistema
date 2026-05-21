<?php

abstract class Estados
{ 		
	const Activo 	= 1;
	const Inactivo 	= 2;
	const Eliminado = 3;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Inactivo:
				return "Activo";
				
			case self::Activo:
				return "Inactivo";

			case self::Eliminado:
				return "Eliminado";

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => self::Activo, 		"Descripcion" => "Activo"),
			array("IdEstado" => self::Inactivo, 	"Descripcion" => "Inactivo"),
			array("IdEstado" => self::Eliminado, 	"Descripcion" => "Eliminado")
		);
	}
}

?>