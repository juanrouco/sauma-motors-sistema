<?php

abstract class CombustibleTipos
{ 		
	const Nafta		= 1;
	const Gasoil	= 2;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Nafta:
				return "NAFTA";

			case self::Gasoil:
				return "GASOIL";

			default:
				return "NO ESPECIFICA";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Nafta, 	"Descripcion" => "NAFTA"),
			array("IdTipo" => self::Gasoil, "Descripcion" => "GASOIL")
		);
	}
}

?>