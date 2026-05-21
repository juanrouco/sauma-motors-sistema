<?php

abstract class FilterTypes
{ 		
	const Text 		= 1;
	const Select 	= 2;
	const Checkbox	= 3;
	const Radio		= 4;

	static function GetById($IdTipo)
	{
		switch($IdTipo)
		{
			case FilterTypes::Text:
				return "Text";
				
			case FilterTypes::Select:
				return "Select";
				
			case FilterTypes::Checkbox:
				return "Checkbox";

			case FilterTypes::Radio:
				return "Radio";				

			default:
				return "Unknown";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdTipo" => FilterTypes::Text, 		"Descripcion" => "Text"),
			array("IdTipo" => FilterTypes::Select, 		"Descripcion" => "Select"),
			array("IdTipo" => FilterTypes::Checkbox, 	"Descripcion" => "Checkbox"),
			array("IdTipo" => FilterTypes::Radio, 		"Descripcion" => "Radio")
		);
	}
}

?>
