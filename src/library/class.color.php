<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Color
{
	const PathImageBigBack		= '../_recursos/colores/imagenes/big/';
	const PathImageBigFront		= '_recursos/colores/imagenes/big/';
	const PathImageThumbBack	= '../_recursos/colores/imagenes/thumb/';
	const PathImageThumbFront	= '_recursos/colores/imagenes/thumb/';
	
	public $IdColor;
	public $Codigo;
	public $Nombre;
	public $Imagen;
	
	
	public function __construct()
	{
		$this->IdColor 	= '';
		$this->Codigo	= '';
		$this->Nombre 	= '';
		$this->Imagen 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdColor 	= $arr['IdColor'];
		$this->Codigo	= $arr['Codigo'];
		$this->Nombre 	= stripslashes($arr['Nombre']);
		$this->Imagen 	= $arr['Imagen'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllUnidades())
			return false;

		if ($this->GetAllUsados())
			return false;
		
		return true;
	}
	
	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByColor($this);
	}


	public function GetAllUsados()
	{
		$Usados = new Usados();
		
		return $Usados->GetAllByColor($this);
	}
}

?>