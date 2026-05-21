<?php

class PresupuestoEstados
{ 		
	const Pendiente		= 1;
	const Ganado		= 2;
	const Perdido		= 3;
	const Anulada		= 4;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case PresupuestoEstados::Pendiente:
				return "Pendiente";
				
			case PresupuestoEstados::Ganado:
				return "Ganado";
				
			case PresupuestoEstados::Perdido:
				return "Perdido";
			
			default:
				return "No Asignado";
		}
	}
	
	static function GetColorById($IdEstado)
	{
		switch($IdEstado)
		{
			case PresupuestoEstados::Pendiente:
				return "<span style=\"color:#FF0000;\">Pendiente</span>";
				
			case PresupuestoEstados::Ganado:
				return "<span style=\"color:#009800;\">Ganado</span>";

			case PresupuestoEstados::Perdido:
				return "<span style=\"color:#B811F0;\">Perdido</span>";				

			default:
				return "No Asignado";
		}
	}
	
		static function GetOnlyColorById($IdEstado)
	{
		switch($IdEstado)
		{
			case PresupuestoEstados::Pendiente:
				return "#FF0000";
				
			case PresupuestoEstados::Ganado:
				return "#009800";

			case PresupuestoEstados::Perdido:
				return "#B811F0";				

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => PresupuestoEstados::Pendiente, 	"Descripcion" => "Pendiente"),
			array("IdEstado" => PresupuestoEstados::Ganado, 	"Descripcion" => "Ganado"),
			array("IdEstado" => PresupuestoEstados::Perdido,	"Descripcion" => "Perdido")
		);
	}
}

?>