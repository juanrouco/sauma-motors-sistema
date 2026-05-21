<?php

abstract class ConceptosCajas
{ 		
	const Combustible		= 1;
	const AFIP				= 2;
	const CargasSociales	= 3;
	const Alquileres		= 4;
	const Servicios			= 5;
	const Honorarios		= 6;
	const Sueldos			= 7;
	const Varios			= 8;

	static function GetById($IdConcepto)
	{
		switch($IdConcepto)
		{
			case self::Combustible:
				return "COMBUSTIBLE";

			case self::AFIP:
				return "AFIP";

			case self::CargasSociales:
				return "CARGAS SOCIALES";
				
			case self::Alquileres:
				return "ALQUILERES";
				
			case self::Servicios:
				return "SERVICIOS";
				
			case self::Honorarios:
				return "HONORARIOS";
				
			case self::Sueldos:
				return "SUELDOS";
				
			case self::Varios:
				return "VARIOS";

			default:
				return "NO ESPECIFICA";
		}
	}
	

	static function GetAll()
	{
		return array
		(
			array("IdConcepto" => self::Combustible, 			"Descripcion" => "COMBUSTIBLE"),
			array("IdConcepto" => self::AFIP, 					"Descripcion" => "AFIP"),
			array("IdConcepto" => self::CargasSociales,			"Descripcion" => "CARGAS SOCIALES"),
			array("IdConcepto" => self::Alquileres,				"Descripcion" => "ALQUILERES"),
			array("IdConcepto" => self::Servicios,				"Descripcion" => "SERVICIOS"),
			array("IdConcepto" => self::Honorarios,				"Descripcion" => "HONORARIOS"),
			array("IdConcepto" => self::Sueldos,				"Descripcion" => "SUELDOS"),
			array("IdConcepto" => self::Varios,					"Descripcion" => "VARIOS")
		);
	}
}

?>