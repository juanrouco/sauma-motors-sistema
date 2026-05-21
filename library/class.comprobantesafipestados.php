<?php

abstract class ComprobantesAfipEstados
{ 		
	const Pendiente	= 1;
	const Procesado	= 2;
	const Rechazado	= 3;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Pendiente:
				return "Pendiente";
				
			case self::Procesado:
				return "Procesado";

			case self::Rechazado:
				return "Rechazado";
			
			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => self::Pendiente, 	"Descripcion" => "Pendiente"),
			array("IdEstado" => self::Procesado, 	"Descripcion" => "Procesado"),
			array("IdEstado" => self::Rechazado, 	"Descripcion" => "Rechazado")
		);
	}
}

?>