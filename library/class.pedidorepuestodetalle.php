<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');

class PedidoRepuestoDetalle extends DBAccess 
{
	public $IdPedidoRepuestoDetalle;
	public $IdPedidoRepuesto;
	public $IdArticulo;
	public $Precio;	
	public $Cantidad;	
	public $IdCargo;	
	public $NumeroSap;
	public $Recibido;
	public $FechaPedido;
	public $FechaVencimiento;

	public function __construc()
	{
		$this->IdPedidoRepuestoDetalle	= '';
		$this->IdPedidoRepuesto			= '';
		$this->IdArticulo 				= '';
		$this->Precio					= '';
		$this->Cantidad 				= '';		
		$this->IdCargo 					= '';
		$this->NumeroSap		 		= '';
		$this->Recibido			 		= '';
		$this->FechaPedido		 		= '';
		$this->FechaVencimiento	 		= '';
	}
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedidoRepuestoDetalle	= $arr['IdPedidoRepuestoDetalle'];
		$this->IdPedidoRepuesto			= $arr['IdPedidoRepuesto'];
		$this->IdArticulo 				= $arr['IdArticulo'];
		$this->Precio 					= $arr['Precio'];
		$this->Cantidad 				= $arr['Cantidad'];
		$this->IdCargo 					= $arr['IdCargo'];
		$this->NumeroSap 				= $arr['NumeroSap'];
		$this->Recibido	 				= $arr['Recibido'];
		$this->FechaPedido	 			= $arr['FechaPedido'];
		$this->FechaVencimiento			= $arr['FechaVencimiento'];
	}
}

?>
