<?php

abstract class TiposMovimientosCaja
{ 		
	const Inicio			= 1;
	const Ingreso			= 2;
	const CuentaCorriente	= 3;
	const Rendicion			= 4;
	const Egreso			= 5;
	const TransferenciaCaja	= 6;
	const MovimientoCajaEntrada	= 2;
	const Pago				= 7;
	const Apertura			= 8;
	const Cierre			= 9;
	const Gastos			= 10;
	const EgresosRemesas	= 11;
	const CuentaCorrienteUsado	= 12;
	const RendicionUsado		= 13;
	const PagoPV			= 14;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Inicio:
				return "INICIO";

			case self::Ingreso:
				return "INGRESO";

			case self::CuentaCorriente:
				return "EGRESO GESTORIA";
				
			case self::Rendicion:
				return "RENDICION GESTORIA";
				
			case self::Egreso:
				return "EGRESO";
				
			case self::TransferenciaCaja:
				return "TRANSFERENCIA ENTRE CAJAS";
				
			case self::Pago:
				return "PAGO";
				
			case self::Apertura:
				return "APERTURA";
				
			case self::Cierre:
				return "CIERRE";
				
			case self::Gastos:
				return "GASTOS";
				
			case self::EgresosRemesas:
				return "EGRESOS DE REMESAS";
				
			case self::CuentaCorrienteUsado:
				return "EGRESOS GESTORIA USADOS";
				
			case self::RendicionUsado:
				return "RENDICION GESTORIA USADOS";
				
			case self::PagoPV:
				return "PAGO POSVENTA";

			default:
				return "NO ESPECIFICA";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Inicio, 				"Descripcion" => "INICIO"),
			array("IdTipo" => self::Ingreso, 				"Descripcion" => "INGRESO"),
			array("IdTipo" => self::CuentaCorriente,		"Descripcion" => "CUENTA CORRIENTE"),
			array("IdTipo" => self::Rendicion,				"Descripcion" => "RENDICION"),
			array("IdTipo" => self::Egreso,					"Descripcion" => "EGRESO"),
			array("IdTipo" => self::TransferenciaCaja,		"Descripcion" => "TRANSFERENCIA ENTRE CAJAS"),
			array("IdTipo" => self::Gastos,					"Descripcion" => "GASTOS"),
			array("IdTipo" => self::EgresosRemesas,			"Descripcion" => "EGRESOS DE REMESAS"),
			array("IdTipo" => self::CuentaCorrienteUsado,	"Descripcion" => "EGRESOS GESTORIA USADOS"),
			array("IdTipo" => self::RendicionUsado,			"Descripcion" => "RENDICION GESTORIA USADOS"),
			array("IdTipo" => self::PagoPV,					"Descripcion" => "PAGO POSVENTA")
		);
	}
	

	static function GetAllEditable()
	{
		return array
		(
			array("IdTipo" => self::Ingreso, 			"Descripcion" => "INGRESO"),
			array("IdTipo" => self::Egreso,				"Descripcion" => "EGRESO"),
			array("IdTipo" => self::Gastos,				"Descripcion" => "GASTOS"),
			array("IdTipo" => self::EgresosRemesas,		"Descripcion" => "EGRESOS DE REMESAS"),
			array("IdTipo" => self::TransferenciaCaja,	"Descripcion" => "TRANSFERENCIA ENTRE CAJAS")
		);
	}
}

?>