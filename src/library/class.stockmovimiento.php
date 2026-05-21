<?php

require_once('class.db.php');
require_once('class.dbaccess.php');


class StockMovimiento
{
	public $IdStockMovimiento;
	public $IdArticulo;
	public $IdUbicacion;
	public $Remito;
	public $Fecha;
	public $Cantidad;	
	public $Observaciones;
	public $IdCompra;

	public function __construct()
	{
		
	}	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdStockMovimiento 	= $arr['IdStockMovimiento'];
		$this->IdArticulo			= $arr['IdArticulo'];
		$this->IdUbicacion			= $arr['IdUbicacion'];
		$this->Remito 				= stripslashes($arr['Remito']);
		$this->Fecha				= $arr['Fecha'];
		$this->Cantidad				= $arr['Cantidad'];	
		$this->Observaciones		= $arr['Observaciones'];
		$this->IdCompra				= $arr['IdCompra'];
	}
}

?>