<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PedidoMayoristaDetalle extends DBAccess 
{
	public $IdPedidoMayoristaDetalle;
	public $IdPedidoMayorista;
	public $IdMinuta;	
	

	public function __construc()
	{
		$this->IdPedidoMayoristaDetalle	= '';
		$this->IdPedidoMayorista		= '';
		$this->IdMinuta 				= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedidoMayoristaDetalle	= $arr['IdPedidoMayoristaDetalle'];
		$this->IdPedidoMayorista		= $arr['IdPedidoMayorista'];
		$this->IdMinuta 				= $arr['IdMinuta'];
	}
}

?>
