<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class FacturaCompraUnidad
{
	public $IdFacturaCompraUnidad;
	public $IdFacturaCompra;
	public $IdUnidad;
	
	public function __construct()
	{
		$this->IdFacturaCompraUnidad = '';
		$this->IdFacturaCompra 	= '';
		$this->IdUnidad			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFacturaCompraUnidad 	= $arr['IdFacturaCompraUnidad'];
		$this->IdFacturaCompra 			= $arr['IdFacturaCompra'];
		$this->IdUnidad					= $arr['IdUnidad'];
	}
}

?>