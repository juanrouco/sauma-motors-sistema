<?php

abstract class Jurisdicciones
{ 		
	const ProvinciaBuenosAires	= 1;
	const CapitalFederal		= 2;
	const Indistinto			= 3;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::CapitalFederal:
				return "CAPITAL FEDERAL";

			case self::ProvinciaBuenosAires:
				return "PROVINCIA BUENOS AIRES";

			default:
				return "NO ESPECIFICA";
		}
	}


	static function GetDescripcionById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::CapitalFederal:
				return "Capital Federal";

			case self::ProvinciaBuenosAires:
				return "Provincia Buenos Aires";
				
			case self::Indistinto:
				return "Indistinto";
		}
	}
}

?>