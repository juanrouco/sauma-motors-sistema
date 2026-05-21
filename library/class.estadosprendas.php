<?php

abstract class EstadosPrendas
{ 		
	const Preaprobado 	= 1;
	const Aprobado 	= 2;
	const Facturado = 3;
	const Gestoria = 4;
	const EnvioPrenda 	= 5;
	const Liquidado 	= 6;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Preaprobado:
				return "Pre Aprobado";

			case self::Aprobado:
				return "Aprobado";
				
			case self::Facturado:
				return "Facturado";
			
			case self::Gestoria:
				return "Gestoria";
			
			case self::EnvioPrenda:
				return "Envio Prenda";
				
			case self::Liquidado:
				return "Liquidado";

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => self::Preaprobado, 		"Descripcion" => "Pre Aprobado"),
			array("IdEstado" => self::Aprobado, 		"Descripcion" => "Aprobado"),
			array("IdEstado" => self::Facturado, 		"Descripcion" => "Facturado"),
			array("IdEstado" => self::Gestoria, 			"Descripcion" => "Gestoria"),
			array("IdEstado" => self::EnvioPrenda, 	"Descripcion" => "Envio Prenda"),
			array("IdEstado" => self::Liquidado, 	"Descripcion" => "Liquidado")
			
		);
	}
}

?>