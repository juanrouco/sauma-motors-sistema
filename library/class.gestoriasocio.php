<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class GestoriaSocio
{
	public $IdGestoriaSocio;
	public $IdGestoria;
	public $IdCliente;
	public $Porcentaje;


	public function __construct()
	{
		$this->IdGestoriaSocio	= '';
		$this->IdCliente 		= '';
		$this->IdGestoria 		= '';
		$this->Porcentaje		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdGestoriaSocio	= $arr['IdGestoriaSocio'];
		$this->IdCliente		= $arr['IdCliente'];
		$this->IdGestoria 		= $arr['IdGestoria'];
		$this->Porcentaje		= $arr['Porcentaje'];
	}
}

?>