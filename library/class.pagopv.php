<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PagoPV extends DBAccess 
{
	public $IdPago;
	public $IdFacturaPostVenta;
	public $Fecha;
	public $Importe;
	
	public function __construct()
	{
		$this->IdPago				= '';
		$this->IdFacturaPostVenta 	= '';
		$this->Fecha				= '';
		$this->Importe 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPago				= $arr['IdPago'];
		$this->IdFacturaPostVenta 	= $arr['IdFacturaPostVenta'];
		$this->Fecha				= $arr['Fecha'];
		$this->Importe 				= $arr['Importe'];
	}
}
?>
