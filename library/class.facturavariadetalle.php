<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class FacturaVariaDetalle extends DBAccess 
{
	public $IdDetalle;
	public $IdFactura;
	public $Detalle;
	public $IvaGravado;
	public $Importe;
	

	public function __construc()
	{
		$this->IdDetalle	= '';
		$this->IdFactura 	= '';
		$this->Detalle 		= '';
		$this->IvaGravado 	= '';
		$this->Importe 		= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdDetalle	= $arr['IdDetalle'];
		$this->IdFactura 	= $arr['IdFactura'];
		$this->Detalle 		= stripslashes($arr['Detalle']);
		$this->IvaGravado 	= $arr['IvaGravado'];
		$this->Importe 		= $arr['Importe'];
	}
}

?>
