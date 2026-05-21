<?php

abstract class FormularioEstados
{ 		
	const Libre 	= 1;
	const Utilizado	= 2;
	const Anulado	= 3;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Libre:
				return "LIBRE";
				
			case self::Utilizado:
				return "UTILIZADO";

			case self::Anulado:
				return "ANULADO";
			
			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => self::Libre, 		"Descripcion" => "LIBRE"),
			array("IdEstado" => self::Utilizado, 	"Descripcion" => "UTILIZADO"),
			array("IdEstado" => self::Anulado, 		"Descripcion" => "ANULADO")
		);
	}
}

?>