<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CuponDescuento
{
	public $IdCuponDescuento;	
	public $Numero;
	public $IdEstado;
	public $Descuento;
	
	public function __construct()
	{
		$this->IdCuponDescuento 	= '';
		$this->Numero 				= '';
		$this->IdEstado 			= '';
		$this->Descuento 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCuponDescuento 	= $arr['IdCuponDescuento'];
		$this->Numero 				= stripslashes($arr['Numero']);
		$this->IdEstado 			= $arr['IdEstado'];
		$this->Descuento	 		= $arr['Descuento'];
	}
}

?>