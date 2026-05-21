<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Service
{
	
	public $IdService;
	public $Nombre;
	public $Disponible;
	
	public function __construct()
	{
		$this->IdService 					= '';
		$this->Nombre						= '';
		$this->Disponible					= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdService 					= $arr['IdService'];
		$this->Nombre						= $arr['Nombre'];
		$this->Disponible					= $arr['Disponible'];
	}
}

?>