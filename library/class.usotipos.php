<?php

abstract class UsoTipos
{ 		
	const Privado	= 1;
	const Publico	= 2;
	const Taxi		= 3;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Privado:
				return "PRIVADO";

			case self::Publico:
				return "PUBLICO";

			case self::Taxi:
				return "TAXI";

			default:
				return "NO ESPECIFICA";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Privado, 	"Descripcion" => "PRIVADO"),
			array("IdTipo" => self::Publico, 	"Descripcion" => "PUBLICO"),
			array("IdTipo" => self::Taxi, 		"Descripcion" => "TAXI")
		);
	}
}

?>