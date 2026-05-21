<?php

abstract class OrdenSalidaDestinatarios
{ 		
	const Cliente 		= 1;
	const Transporte 	= 2;
	const Tercero 		= 3;

	static function GetById($IdTipoDestinatario)
	{
		switch($IdTipoDestinatario)
		{
			case self::Cliente:
				return "CLIENTE";
				
			case self::Transporte:
				return "TRANSPORTE";

			/*case self::Tercero:
				return "TERCERO (OTRA PERSONA)";*/

			default:
				return "No Asignado";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdTipoDestinatario" => self::Cliente, 	"Descripcion" => "CLIENTE"),
			array("IdTipoDestinatario" => self::Transporte, "Descripcion" => "TRANSPORTE")/*,
			array("IdTipoDestinatario" => self::Tercero, 	"Descripcion" => "TERCERO (OTRA PERSONA)")*/
		);
	}
}

?>