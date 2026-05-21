<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Venta
{
	public $IdVenta;
	public $IdUnidad;
	public $IdUsuario;
	public $IdCliente;
	public $FechaVenta;
	public $FechaFactura;
	public $NumeroFactura;
	public $EntregaUsado;
	public $PrecioVenta;
	public $Circular;
	public $Anticipo;
	public $FinanciacionCapital;
	
	
	public function __construct()
	{
		$this->IdVenta 				= '';
		$this->IdUnidad				= '';
		$this->IdUsuario 			= '';
		$this->IdCliente 			= '';
		$this->FechaVenta 			= '';
		$this->FechaFactura 		= '';
		$this->NumeroFactura 		= '';
		$this->EntregaUsado 		= '';
		$this->PrecioVenta 			= '';
		$this->Circular 			= '';
		$this->Anticipo 			= '';
		$this->FinanciacionCapital 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdVenta 				= $arr['IdVenta'];
		$this->IdUnidad				= $arr['IdUnidad'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->FechaVenta 			= $arr['FechaVenta'];
		$this->FechaFactura 		= $arr['FechaFactura'];
		$this->NumeroFactura 		= $arr['NumeroFactura'];
		$this->EntregaUsado 		= $arr['EntregaUsado'];
		$this->PrecioVenta 			= $arr['PrecioVenta'];
		$this->Circular 			= $arr['Circular'];
		$this->Anticipo 			= $arr['Anticipo'];
		$this->FinanciacionCapital 	= $arr['FinanciacionCapital'];
	}
}

?>