<?php

abstract class ClaveFiscalTipos
{ 		
	const Cuit = 1;
	const Cuil = 2;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Cuit:
				return "CUIT";
				
			case self::Cuil:
				return "CUIL";
			
			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Cuit, "Descripcion" => "CUIT"),
			array("IdTipo" => self::Cuil, "Descripcion" => "CUIL")
		);
	}
}

?>