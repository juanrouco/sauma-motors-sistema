<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ModeloPV
{
	
	public $IdModeloPV;
	public $Modelo;
	public $Disponible;
	public $Imagen;
	
	public function __construct()
	{
		$this->IdModeloPV 					= '';
		$this->Modelo						= '';
		$this->Disponible					= '';
		$this->Imagen						= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdModeloPV 					= $arr['IdModeloPV'];
		$this->Modelo						= $arr['Modelo'];
		$this->Disponible					= $arr['Disponible'];
		$this->Imagen						= $arr['Imagen'];
	}
}

?>