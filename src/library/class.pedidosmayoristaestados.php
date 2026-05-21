<?php

abstract class PedidosMayoristaEstados
{ 		
	const Pendiente = 1;
	const Aprobado 	= 2;
	const Anulado 	= 3;

	static function GetById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Pendiente:
				return "Pendiente";
				
			case self::Aprobado:
				return "Aprobado";

			case self::Anulado:
				return "Anulado";

			default:
				return "No Asignado";
		}
	}
	

	static function GetColorById($IdEstado)
	{
		switch($IdEstado)
		{
			case self::Pendiente:
				return '#FCFAA2';

			case self::Aprobado:
				return '#99FF99';

			case self::Anulado:
				return '#FF0000';

			default:
				return '#FFFFFF';
		}
	}

	
	static function GetAll()
	{
		return array
		(
			array("IdEstado" => self::Pendiente, 	"Descripcion" => "Pendiente"),
			array("IdEstado" => self::Aprobado, 	"Descripcion" => "Aprobado")//,
			//array("IdEstado" => self::Anulado, 		"Descripcion" => "Anulado")
		);
	}
}

?>