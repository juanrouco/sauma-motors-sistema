<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CajaMovimientoPago
{
	public $IdCajaMovimientoPago;
	public $IdCajaMovimiento;
	public $IdPago;
	
	public function __construct()
	{
		$this->IdCajaMovimientoPago = '';
		$this->IdCajaMovimiento 	= '';
		$this->IdPago			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaMovimientoPago 	= $arr['IdCajaMovimientoPago'];
		$this->IdCajaMovimiento 			= $arr['IdCajaMovimiento'];
		$this->IdPago					= $arr['IdPago'];
	}
}

?>