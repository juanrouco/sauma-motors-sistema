<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class MinutaPagoItem
{
	public $IdMinutaPagoItem;
	public $IdMinutaPago;
	public $IdUnidad;
	public $Neto;
	public $Retencion;
	public $Importe;
	public $Saldo;
	public $PagoParcial;
	public $NumeroRetencion;
	public $IdFacturaCompra;
	public $Fecha;
	public $Cuit;
	public $IdProveedor;
	
	public function __construct()
	{
		$this->IdMinutaPagoItem = '';
		$this->IdMinutaPago 	= '';
		$this->IdUnidad			= '';
		$this->Neto				= '';
		$this->Retencion		= '';
		$this->Importe			= '';
		$this->Saldo			= '';
		$this->PagoParcial		= '';
		$this->NumeroRetencion	= '';
		$this->IdFacturaCompra	= '';
		$this->Fecha			= '';
		$this->Cuit				= '';
		$this->IdProveedor		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinutaPagoItem = $arr['IdMinutaPagoItem'];
		$this->IdMinutaPago 	= $arr['IdMinutaPago'];
		$this->IdUnidad			= $arr['IdUnidad'];
		$this->Neto				= $arr['Neto'];
		$this->Retencion		= $arr['Retencion'];
		$this->Importe			= $arr['Importe'];
		$this->Saldo			= $arr['Saldo'];
		$this->PagoParcial		= $arr['PagoParcial'];
		$this->NumeroRetencion	= $arr['NumeroRetencion'];
		$this->IdFacturaCompra	= $arr['IdFacturaCompra'];
		$this->Fecha			= $arr['Fecha'];
		$this->Cuit				= $arr['Cuit'];
		$this->IdProveedor		= $arr['IdProveedor'];
	}
}

?>