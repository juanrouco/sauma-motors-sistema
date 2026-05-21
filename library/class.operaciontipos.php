<?php

abstract class OperacionTipos
{ 		
	const RemitoUnidad 		= 1;
	const FacturacionUnidad	= 2;
	const FacturacionVaria	= 3;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::RemitoUnidad:
				return "REMITO UNIDAD";
				
			case self::FacturacionUnidad:
				return "FACTURACION UNIDAD";

			case self::FacturacionVaria:
				return "FACTURACION VARIA";
			
			default:
				return "No Asignado";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::RemitoUnidad, 		"Descripcion" => "REMITO UNIDAD"),
			array("IdTipo" => self::FacturacionUnidad, 	"Descripcion" => "FACTURACION UNIDAD"),
			array("IdTipo" => self::FacturacionVaria, 	"Descripcion" => "FACTURACION VARIA")
		);
	}
}

?>