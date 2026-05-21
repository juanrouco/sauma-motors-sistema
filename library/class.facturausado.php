<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class FacturaUsado extends DBAccess 
{
	public $IdFactura;
	public $IdMinuta;
	public $IdComprobante;
	public $NumeroComprobante;
	public $Fecha;
	public $Subtotal;
	public $Iva10;
	public $Iva21;
	public $ImpuestoInterno;
	public $Total;
	public $OtrosTitulares;
	public $Observaciones;
	
	public function __construct()
	{
		$this->IdFactura			= '';
		$this->IdMinuta 			= '';
		$this->IdComprobante		= '';
		$this->NumeroComprobante	= '';
		$this->Fecha 				= '';
		$this->Subtotal 			= '';
		$this->Iva10 				= '';
		$this->Iva21 				= '';
		$this->ImpuestoInterno		= '';
		$this->Total 				= '';
		$this->OtrosTitulares 		= '';
		$this->Observaciones 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFactura			= $arr['IdFactura'];
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->IdComprobante		= $arr['IdComprobante'];
		$this->NumeroComprobante	= $arr['NumeroComprobante'];
		$this->Fecha 				= $arr['Fecha'];
		$this->Subtotal 			= $arr['Subtotal'];
		$this->Iva10 				= $arr['Iva10'];
		$this->Iva21 				= $arr['Iva21'];
		$this->ImpuestoInterno		= $arr['ImpuestoInterno'];
		$this->Total 				= $arr['Total'];
		$this->OtrosTitulares 		= $arr['OtrosTitulares'];
		$this->Observaciones 		= $arr['Observaciones'];
	}
}
?>
