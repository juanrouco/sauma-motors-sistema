<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PedidoAccesorioItem extends DBAccess 
{
	public $IdPedidoAccesorioItem;
	public $IdPedidoAccesorio;
	public $Detalle;
	public $Importe;
	public $IdArticulo;

	public function __construc()
	{
		$this->IdPedidoAccesorioItem	= '';
		$this->IdPedidoAccesorio 		= '';
		$this->Detalle 					= '';
		$this->Importe 					= '';
		$this->IdArticulo				= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedidoAccesorioItem	= $arr['IdPedidoAccesorioItem'];
		$this->IdPedidoAccesorio	 	= $arr['IdPedidoAccesorio'];
		$this->Detalle 					= stripslashes($arr['Detalle']);
		$this->Importe 					= $arr['Importe'];
		$this->IdArticulo				= $arr['IdArticulo'];
	}
}

?>
