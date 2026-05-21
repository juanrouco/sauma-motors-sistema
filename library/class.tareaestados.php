<?php

class TareaEstados
{ 		
	const Pendiente		= 1;
	const EnProceso		= 2;
	const Finalizada	= 3;
	const Anulada		= 4;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case TareaEstados::Pendiente:
				return "Pendiente";
				
			case TareaEstados::EnProceso:
				return "En Proceso";
				
			case TareaEstados::Finalizada:
				return "Finalizada";
			
			case TareaEstados::Anulada:
				return "Anulada";
			
			default:
				return "No Asignado";
		}
	}
	
	static function GetColorById($IdEstado)
	{
		switch($IdEstado)
		{
			case TareaEstados::Pendiente:
				return "<span style=\"color:#FF0000;\">Pendiente</span>";
				
			case TareaEstados::EnProceso: 
				return "<span style=\"color:#FFC600;\">En Proceso</span>";
				
			case TareaEstados::Finalizada:
				return "<span style=\"color:#009800;\">Finalizada</span>";

			case TareaEstados::Anulada:
				return "<span style=\"color:#B811F0;\">Anulada</span>";				

			default:
				return "No Asignado";
		}
	}
	
		static function GetOnlyColorById($IdEstado)
	{
		switch($IdEstado)
		{
			case TareaEstados::Pendiente:
				return "#FF0000";
				
			case TareaEstados::EnProceso:
				return "#FFC600";
				
			case TareaEstados::Finalizada:
				return "#009800";

			case TareaEstados::Anulada:
				return "#B811F0";				

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => TareaEstados::Pendiente, 	"Descripcion" => "Pendiente"),
			array("IdEstado" => TareaEstados::EnProceso, 	"Descripcion" => "En Proceso"),
			array("IdEstado" => TareaEstados::Finalizada,	"Descripcion" => "Finalizada"),
			array("IdEstado" => TareaEstados::Anulada, 		"Descripcion" => "Anulada")
		);
	}
}

?>