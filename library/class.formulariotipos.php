<?php

abstract class FormularioTipos
{ 		
	const Fomulario01Nacional		= 1;
	const Fomulario01Importado		= 2;
	const TituloAutomotor			= 3;
	const Fomulario12				= 4;
	const Fomulario13ACapital		= 5;
	const Fomulario13AProvincia		= 6;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Fomulario01Nacional:
				return "FORMULARIO 01 NACIONAL";

			case self::Fomulario01Importado:
				return "FORMULARIO 01 IMPORTADO";

			case self::TituloAutomotor:
				return "TITULO AUTOMOTOR";

			case self::Fomulario12:
				return "FORMULARIO 12";

			case self::Fomulario13ACapital:
				return "FORMULARIO 13A CAPITAL";

			case self::Fomulario13AProvincia:
				return "FORMULARIO 13A PROVINCIA";

			default:
				return "NO ESPECIFICA";
		}
	}
	

	static function GetDescripcionById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Fomulario01Nacional:
				return "Formulario 01 Nacional";

			case self::Fomulario01Importado:
				return "Formulario 01 Importado";

			case self::TituloAutomotor:
				return "Titulo Automotor";

			case self::Fomulario12:
				return "Formulario 12";

			case self::Fomulario13ACapital:
				return "Formulario 13A Capital";

			case self::Fomulario13AProvincia:
				return "Formulario 13A Provincia";

			default:
				return "No Especifica";
		}
	}


	static function GetLongitudMaxima($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Fomulario01Importado:
				return 7;

			case self::Fomulario01Nacional:
			case self::Fomulario12:
			case self::Fomulario13ACapital:
			case self::Fomulario13AProvincia:
				return 8;

			default:
				return 0;
		}
	}


	static function GetAllMenu()
	{
		return array
		(
			array("IdTipo" => self::Fomulario01Nacional, 	"Descripcion" => "Formulario 01 Nacional"),
			array("IdTipo" => self::Fomulario01Importado, 	"Descripcion" => "Formulario 01 Importado"),
			array("IdTipo" => self::Fomulario12, 			"Descripcion" => "Formulario 12"),
			array("IdTipo" => self::Fomulario13ACapital, 	"Descripcion" => "Formulario 13A Capital"),
			array("IdTipo" => self::Fomulario13AProvincia, 	"Descripcion" => "Fformulario 13A Provincia")
		);
	}


	static function GetAll()
	{
		return array
		(
			array("IdTipo" => self::Fomulario01Nacional, 	"Descripcion" => "Formulario 01 Nacional"),
			array("IdTipo" => self::Fomulario01Importado, 	"Descripcion" => "Formulario 01 Importado"),
			array("IdTipo" => self::TituloAutomotor, 		"Descripcion" => "Titulo Automotor"),
			array("IdTipo" => self::Fomulario12, 			"Descripcion" => "Formulario 12"),
			array("IdTipo" => self::Fomulario13ACapital, 	"Descripcion" => "Formulario 13A Capital"),
			array("IdTipo" => self::Fomulario13AProvincia, 	"Descripcion" => "Fformulario 13A Provincia")
		);
	}
	

	static function GetAllForCreate($Origen, $IdProvincia)
	{
		if ($Origen == Origen::Nacional)
			$Formulario01 = array("IdTipo" => self::Fomulario01Nacional, "Descripcion" => "Formulario 01 Nacional");
		elseif ($Origen == Origen::Importado)
			$Formulario01 = array("IdTipo" => self::Fomulario01Importado, "Descripcion" => "Formulario 01 Importado");

		if ($IdProvincia == 1)
			$Formulario13 = array("IdTipo" => self::Fomulario13AProvincia, "Descripcion" => "Fformulario 13A Provincia");
		elseif ($IdProvincia == 2)
			$Formulario13 = array("IdTipo" => self::Fomulario13ACapital, "Descripcion" => "Formulario 13A Capital");

		if ($IdProvincia == 1 || $IdProvincia == 2)
		{
			return array
			(
				$Formulario01,
				array("IdTipo" => self::TituloAutomotor, "Descripcion" => "Titulo Automotor"),
				array("IdTipo" => self::Fomulario12, "Descripcion" => "Formulario 12"),
				$Formulario13
			);
		}
		else
		{
			return array
			(
				$Formulario01,
				array("IdTipo" => self::TituloAutomotor, "Descripcion" => "Titulo Automotor"),
				array("IdTipo" => self::Fomulario12, "Descripcion" => "Formulario 12")
			);
		}		
	}
}

?>