<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Marca
{
	const PathImageBigBack		= '../_recursos/marcas/imagenes/big/';
	const PathImageBigFront		= '_recursos/marcas/imagenes/big/';
	const PathImageThumbBack	= '../_recursos/marcas/imagenes/thumb/';
	const PathImageThumbFront	= '_recursos/marcas/imagenes/thumb/';

	public $IdMarca;
	public $Codigo;
	public $Nombre;
	public $Imagen;
	
	
	public function __construct()
	{
		$this->IdMarca 	= '';
		$this->Codigo	= '';
		$this->Nombre 	= '';
		$this->Imagen 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMarca 	= $arr['IdMarca'];
		$this->Codigo	= $arr['Codigo'];
		$this->Nombre 	= stripslashes($arr['Nombre']);
		$this->Imagen 	= $arr['Imagen'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllModelos())
			return false;

		if ($this->GetAllUsados())
			return false;
		
		return true;
	}
	
	
	public function GetAllModelos()
	{
		$Modelos = new Modelos();
		
		return $Modelos->GetAllByMarca($this);
	}


	public function GetAllUsados()
	{
		$Usados = new Usados();
		
		return $Usados->GetAllByMarca($this);
	}
}

?>