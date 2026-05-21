<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class UnidadFacturaCompra extends DBAccess 
{
	public $IdUnidadFacturaCompra;
	public $IdUnidad;
	public $IdFacturaCompra;

	public function __construct()
	{
		$this->IdUnidadFacturaCompra	= '';
		$this->IdUnidad					= '';
		$this->IdFacturaCompra			= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdUnidadFacturaCompra	= $arr['IdUnidadFacturaCompra'];
		$this->IdUnidad					= $arr['IdUnidad'];
		$this->IdFacturaCompra			= $arr['IdFacturaCompra'];
	}
}
?>