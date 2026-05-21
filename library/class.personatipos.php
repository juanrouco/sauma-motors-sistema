<?php

abstract class PersonaTipos
{ 		
	const PersonaFisica 	= 1;
	const PersonaJuridica	= 2;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::PersonaFisica:
				return "PERSONA FISICA";
				
			case self::PersonaJuridica:
				return "PERSONA JURIDICA";
			
			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::PersonaFisica, 		"Descripcion" => "PERSONA FISICA"),
			array("IdTipo" => self::PersonaJuridica, 	"Descripcion" => "PERSONA JURIDICA"),
		);
	}
}

?>