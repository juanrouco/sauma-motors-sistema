<?php

class Meses
{ 		
	const Enero 		= 1;
	const Febrero		= 2;
	const Marzo			= 3;
	const Abril			= 4;
	const Mayo			= 5;
	const Junio			= 6;
	const Julio			= 7;
	const Agosto		= 8;
	const Septiembre	= 9;
	const Octubre		= 10;
	const Noviembre		= 11;
	const Diciembre		= 12;

	static function GetById($IdMes)
	{
		switch($IdMes)
		{
			case Meses::Enero:
				return "Enero";

			case Meses::Febrero:
				return "Febrero";

			case Meses::Marzo:
				return "Marzo";

			case Meses::Abril:
				return "Abril";

			case Meses::Mayo:
				return "Mayo";

			case Meses::Junio:
				return "Junio";

			case Meses::Julio:
				return "Julio";

			case Meses::Agosto:
				return "Agosto";

			case Meses::Septiembre:
				return "Septiembre";

			case Meses::Octubre:
				return "Octubre";

			case Meses::Noviembre:
				return "Noviembre";

			case Meses::Diciembre:
				return "Diciembre";
				
			default:
				return "No Asignado";
		}
	}
	

	static function GetCountDias($IdMes)
	{
		switch($IdMes)
		{
			case Meses::Febrero:
				return 28;

			case Meses::Abril:
			case Meses::Junio:
			case Meses::Septiembre:
			case Meses::Noviembre:
				return 30;

			case Meses::Enero:
			case Meses::Marzo:
			case Meses::Mayo:
			case Meses::Julio:
			case Meses::Agosto:
			case Meses::Octubre:
			case Meses::Diciembre:
				return 31;

			default:
				return "No Especificado";
		}
	}
	
		
	static function GetAll()
	{
		return array
		(			
			array("IdMes" => Meses::Enero, 		"Descripcion" => "Enero"),
			array("IdMes" => Meses::Febrero, 	"Descripcion" => "Febrero"),
			array("IdMes" => Meses::Marzo, 		"Descripcion" => "Marzo"),
			array("IdMes" => Meses::Abril, 		"Descripcion" => "Abril"),
			array("IdMes" => Meses::Mayo, 		"Descripcion" => "Mayo"),
			array("IdMes" => Meses::Junio, 		"Descripcion" => "Junio"),
			array("IdMes" => Meses::Julio, 		"Descripcion" => "Julio"),
			array("IdMes" => Meses::Agosto, 	"Descripcion" => "Agosto"),
			array("IdMes" => Meses::Septiembre, "Descripcion" => "Septiembre"),
			array("IdMes" => Meses::Octubre, 	"Descripcion" => "Octubre"),
			array("IdMes" => Meses::Noviembre, 	"Descripcion" => "Noviembre"),
			array("IdMes" => Meses::Diciembre, 	"Descripcion" => "Diciembre")
		);
	}
}

?>