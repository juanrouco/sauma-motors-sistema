<?php

class SeguimientoEstados
{ 		
	const Llamada		= 1;
	const Email			= 2;
	const Reunion		= 3;
	const Otro			= 4;
	const Perdido		= 5;

	static function GetById($IdAccion)
	{
		switch($IdAccion)
		{
			case SeguimientoEstados::Llamada:
				return "Llamada";
				
			case SeguimientoEstados::Email:
				return "Email";
			
			case SeguimientoEstados::Reunion:
				return "Reunion";
			
			case SeguimientoEstados::Otro:
				return "Otro";	
			
			case SeguimientoEstados::Perdido:
				return "Perdido";		
			
			default:
				return "No Asignado";
		}
	}
	
	static function GetColorById($IdAccion)
	{
		switch($IdAccion)
		{
			case SeguimientoEstados::Llamada:
				return "<span style=\"color:#FF0000;\">Llamada</span>";
				
			case SeguimientoEstados::Email:
				return "<span style=\"color:#009800;\">Email</span>";	
			
			case SeguimientoEstados::Reunion:
				return "<span style=\"color:#009800;\">Reunión</span>";		
			
			case SeguimientoEstados::Perdido:
				return "<span style=\"color:#009800;\">Perdido</span>";			
	
			default:
				return "No Asignado";
		}
	}
	
	static function GetOnlyColorById($IdAccion)
	{
		switch($IdAccion)
		{
			case SeguimientoEstados::Llamada:
				return "#FF0000";
				
			case SeguimientoEstados::Email:
				return "#009800";	
			
			case SeguimientoEstados::Reunion:
				return "#009800";		
			
			case SeguimientoEstados::Perdido:
				return "#009800";					

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdAccion" => SeguimientoEstados::Llamada, 	"Descripcion" => "Llamada"),
			array("IdAccion" => SeguimientoEstados::Email, 		"Descripcion" => "Email"),
			array("IdAccion" => SeguimientoEstados::Reunion, 	"Descripcion" => "Reunion"),
			array("IdAccion" => SeguimientoEstados::Otro, 		"Descripcion" => "Otro"),
			array("IdAccion" => SeguimientoEstados::Perdido, 	"Descripcion" => "Perdido")
		);
	}
}

?>