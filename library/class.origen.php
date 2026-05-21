<?php

abstract class Origen
{ 		
	const Nacional		= 1;
	const Importado		= 2;
	const Indistinto	= 3;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Nacional:
				return "NACIONAL";

			case self::Importado:
				return "IMPORTADO";

			default:
				return "NO ESPECIFICA";
		}
	}


	static function GetDescripcionById($IdTipo)
	{
		switch($IdTipo)
		{
			case self::Nacional:
				return "Nacional";

			case self::Importado:
				return "Importado";
		}
	}
}

?>