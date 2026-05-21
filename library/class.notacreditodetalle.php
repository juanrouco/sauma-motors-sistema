<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class NotaCreditoDetalle extends DBAccess 
{
	public $IdNotaCreditoDetalle;
	public $IdNotaCredito;
	public $Detalle;
	public $IdIva;
	public $Importe;
	

	public function __construc()
	{
		$this->IdNotaCreditoDetalle	= '';
		$this->IdNotaCredito 		= '';
		$this->Detalle 				= '';
		$this->IdIva		 		= '';
		$this->Importe 				= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdNotaCreditoDetalle	= $arr['IdNotaCreditoDetalle'];
		$this->IdNotaCredito		= $arr['IdNotaCredito'];
		$this->Detalle 				= stripslashes($arr['Detalle']);
		$this->IdIva			 	= $arr['IdIva'];
		$this->Importe 				= $arr['Importe'];
	}
}

?>
