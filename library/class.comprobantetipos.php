<?php

abstract class ComprobanteTipos
{ 		
	const Remito 		= 1;
	const FacturaA		= 2;
	const FacturaB		= 3;
	const NotaCreditoA	= 4;
	const NotaCreditoB	= 5;
	const FacturaC		= 6;
	const NotaCreditoC	= 7;
	const NotaDebitoA	= 8;
	const NotaDebitoB	= 9;
	const NotaDebitoC	= 10;
	const ReciboA		= 11;
	const ReciboB		= 12;
	const ReciboC		= 13;
	const FacturaM		= 14;
	const NotaCreditoM	= 15;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Remito:
				return "REMITO";
				
			case self::FacturaA:
				return "FACTURA A";

			case self::FacturaB:
				return "FACTURA B";
			
			case self::FacturaC:
				return "FACTURA C";
			
			case self::NotaCreditoA:
				return "NOTA DE CREDITO A";
				
			case self::NotaCreditoB:
				return "NOTA DE CREDITO B";
			
			case self::NotaCreditoC:
				return "NOTA DE CREDITO C";
				
			case self::NotaDebitoA:
				return "NOTA DE DEBITO A";
				
			case self::NotaDebitoB:
				return "NOTA DE DEBITO B";
			
			case self::NotaDebitoC:
				return "NOTA DE DEBITO C";
			
			case self::FacturaM:
				return "FACTURA M";
			
			case self::NotaCreditoM:
				return "NOTA DE CREDITO M";
			
			default:
				return "--";
		}
	}
	

	static function GetDescripcionById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Remito:
				return "Remito";
				
			case self::FacturaA:
				return "Factura A";

			case self::FacturaB:
				return "Factura B";
				
			case self::FacturaC:
				return "Factura C";
				
			case self::NotaCreditoA:
				return "Nota de Credito A";
				
			case self::NotaCreditoB:
				return "Nota de Credito B";
			
			case self::NotaCreditoC:
				return "Nota de Credito C";
				
			case self::NotaDebitoA:
				return "Nota de Debito A";
				
			case self::NotaDebitoB:
				return "Nota de Debito B";
			
			case self::NotaDebitoC:
				return "Nota de Debito C";
			
			case self::FacturaM:
				return "Factura M";
			
			case self::NotaCreditoM:
				return "Nota de Credito M";
			
			default:
				return "--";
		}
	}

	static function GetLetraById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "A";

			case self::FacturaB:
				return "B";
				
			case self::FacturaC:
				return "C";
				
			case self::NotaCreditoA:
				return "A";
				
			case self::NotaCreditoB:
				return "B";
			
			case self::NotaCreditoC:
				return "C";
				
			case self::NotaDebitoA:
				return "A";
				
			case self::NotaDebitoB:
				return "B";
			
			case self::NotaDebitoC:
				return "C";
			
			case self::FacturaM:
				return "M";
			
			case self::NotaCreditoM:
				return "M";
			
			default:
				return "--";
		}
	}
	
	static function GetTipoCitiById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "001";

			case self::FacturaB:
				return "006";
				
			case self::FacturaC:
				return "011";
				
			case self::NotaCreditoA:
				return "003";
				
			case self::NotaCreditoB:
				return "008";
			
			case self::NotaCreditoC:
				return "013";
				
			case self::NotaDebitoA:
				return "002";
				
			case self::NotaDebitoB:
				return "007";
			
			case self::NotaDebitoC:
				return "012";
				
			case self::ReciboA:
				return "004";
				
			case self::ReciboB:
				return "009";
			
			case self::ReciboC:
				return "015";
			
			case self::FacturaM:
				return "051";
			
			case self::NotaCreditoM:
				return "053";
			
			default:
				return "--";
		}
	}
	
	static function GetTipoById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "FC";

			case self::FacturaB:
				return "FC";
				
			case self::FacturaC:
				return "FC";
				
			case self::NotaCreditoA:
				return "NC";
				
			case self::NotaCreditoB:
				return "NC";
			
			case self::NotaCreditoC:
				return "NC";
				
			case self::NotaDebitoA:
				return "ND";
				
			case self::NotaDebitoB:
				return "ND";
			
			case self::NotaDebitoC:
				return "ND";
			
			case self::FacturaM:
				return "FC";
			
			case self::NotaCreditoM:
				return "NC";
			
			default:
				return "--";
		}
	}
	
	static function GetSignoById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "";

			case self::FacturaB:
				return "";
				
			case self::FacturaC:
				return "";
				
			case self::NotaCreditoA:
				return "-";
				
			case self::NotaCreditoB:
				return "-";
			
			case self::NotaCreditoC:
				return "-";
				
			case self::NotaDebitoA:
				return "";
				
			case self::NotaDebitoB:
				return "";
			
			case self::NotaDebitoC:
				return "";
			
			case self::FacturaM:
				return "";
			
			case self::NotaCreditoM:
				return "-";
			
			default:
				return "--";
		}
	}
	
	static function GetSignoById2($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "0";

			case self::FacturaB:
				return "0";
				
			case self::FacturaC:
				return "0";
				
			case self::NotaCreditoA:
				return "-";
				
			case self::NotaCreditoB:
				return "-";
			
			case self::NotaCreditoC:
				return "-";
				
			case self::NotaDebitoA:
				return "0";
				
			case self::NotaDebitoB:
				return "0";
			
			case self::NotaDebitoC:
				return "0";
				
			case self::ReciboA:
				return "0";
				
			case self::ReciboB:
				return "0";
			
			case self::ReciboC:
				return "0";
			
			case self::FacturaM:
				return "0";
			
			case self::NotaCreditoM:
				return "-";
			
			default:
				return "--";
		}
	}
	
	static function GetTipoById4($IdTipo)
	{
		switch($IdTipo)
		{
			case self::FacturaA:
				return "F";

			case self::FacturaB:
				return "F";
				
			case self::FacturaC:
				return "F";
				
			case self::NotaCreditoA:
				return "C";
				
			case self::NotaCreditoB:
				return "C";
			
			case self::NotaCreditoC:
				return "C";
				
			case self::NotaDebitoA:
				return "D";
				
			case self::NotaDebitoB:
				return "D";
			
			case self::NotaDebitoC:
				return "D";
		
			case self::ReciboA:
				return "R";
				
			case self::ReciboB:
				return "R";
			
			case self::ReciboC:
				return "R";
			
			case self::FacturaM:
				return "F";
			
			case self::NotaCreditoM:
				return "C";
			
			default:
				return "--";
		}
	}

	static function GetAllMenu()
	{
		return array
		(
			array("IdTipo" => self::Remito, 		"Descripcion" => "Remitos"),
			array("IdTipo" => self::FacturaA, 		"Descripcion" => "Facturas A"),
			array("IdTipo" => self::FacturaB, 		"Descripcion" => "Facturas B"),
			array("IdTipo" => self::NotaCreditoA, 	"Descripcion" => "Notas de Cr&eacute;dito A"),
			array("IdTipo" => self::NotaCreditoB, 	"Descripcion" => "Notas de Cr&eacute;dito B"),
			array("IdTipo" => self::FacturaM,	 	"Descripcion" => "Facturas M"),
			array("IdTipo" => self::NotaCreditoM, 	"Descripcion" => "Notas de Cr&eacute;dito M")
		);
	}

	
	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Remito, 	"Descripcion" => "REMITO"),
			array("IdTipo" => self::FacturaA, 	"Descripcion" => "FACTURA A"),
			array("IdTipo" => self::FacturaB, 	"Descripcion" => "FACTURA B"),
			array("IdTipo" => self::FacturaC, 	"Descripcion" => "FACTURA C"),
			array("IdTipo" => self::NotaCreditoA, 	"Descripcion" => "NOTA DE CREDITO A"),
			array("IdTipo" => self::NotaCreditoB, 	"Descripcion" => "NOTA DE CREDITO B"),
			array("IdTipo" => self::NotaCreditoC, 	"Descripcion" => "NOTA DE CREDITO C"),
			array("IdTipo" => self::NotaDebitoA, 	"Descripcion" => "NOTA DE DEBITO A"),
			array("IdTipo" => self::NotaDebitoB, 	"Descripcion" => "NOTA DE DEBITO B"),
			array("IdTipo" => self::NotaDebitoC, 	"Descripcion" => "NOTA DE DEBITO C"),
			array("IdTipo" => self::FacturaM,	 	"Descripcion" => "FACTURA M"),
			array("IdTipo" => self::NotaCreditoM, 	"Descripcion" => "NOTA DE CREDITO M")
		);
	}
	
	static function GetAllCompras()
	{
		return array
		(
			array("IdTipo" => self::FacturaA, 	"Descripcion" => "FACTURA A"),
			array("IdTipo" => self::FacturaB, 	"Descripcion" => "FACTURA B"),
			array("IdTipo" => self::FacturaC, 	"Descripcion" => "FACTURA C"),
			array("IdTipo" => self::NotaCreditoA, 	"Descripcion" => "NOTA DE CREDITO A"),
			array("IdTipo" => self::NotaCreditoB, 	"Descripcion" => "NOTA DE CREDITO B"),
			array("IdTipo" => self::NotaCreditoC, 	"Descripcion" => "NOTA DE CREDITO C"),
			array("IdTipo" => self::NotaDebitoA, 	"Descripcion" => "NOTA DE DEBITO A"),
			array("IdTipo" => self::NotaDebitoB, 	"Descripcion" => "NOTA DE DEBITO B"),
			array("IdTipo" => self::NotaDebitoC, 	"Descripcion" => "NOTA DE DEBITO C"),
			array("IdTipo" => self::FacturaM,	 	"Descripcion" => "FACTURA M"),
			array("IdTipo" => self::NotaCreditoM, 	"Descripcion" => "NOTA DE CREDITO M")
		);
	}
	
	static function GetAllVentas()
	{
		return array
		(
			array("IdTipo" => self::FacturaA, 	"Descripcion" => "FACTURA A"),
			array("IdTipo" => self::FacturaB, 	"Descripcion" => "FACTURA B")			
		);
	}
}

?>