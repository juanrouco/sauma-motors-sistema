<?php

abstract class DocumentoTipos
{ 		
	const DNI 					= 1;
	const Pasaporte				= 2;
	const Cedula				= 3;
	const LibretaEnrolamiento	= 4;
	const LibretaCivica			= 5;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::DNI:
				return "D.N.I.";
				
			case self::Pasaporte:
				return "Pasaporte";
			
			case self::Cedula:
				return "Cédula de Identidad";
			
			case self::LibretaEnrolamiento:
				return "Libreta de Enrolamiento";
			
			case self::LibretaCivica:
				return "Libreta Cívica";
			
			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::DNI, 					"Descripcion" => "D.N.I."),
			array("IdTipo" => self::Pasaporte, 				"Descripcion" => "Pasaporte"),
			array("IdTipo" => self::Cedula, 				"Descripcion" => "Cédula de Identidad"),
			array("IdTipo" => self::LibretaEnrolamiento, 	"Descripcion" => "libreta de Enrolamiento"),
			array("IdTipo" => self::LibretaCivica, 			"Descripcion" => "Libreta Cívica")
		);
	}
}

?>