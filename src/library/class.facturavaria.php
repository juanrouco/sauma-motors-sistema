<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.facturavarias.php');
require_once('class.ifacturaelectronica.php');
require_once('class.comprobantes.php');

class FacturaVaria extends DBAccess implements IFacturaElectronica
{
	public $IdFactura;
	public $IdCliente;
	public $IdComprobante;
	public $NumeroComprobante;
	public $Fecha;
	public $Detalle;
	public $Subtotal;
	public $Iva10;
	public $Iva21;
	public $Total;
	
	public function __construct()
	{
		$this->IdFactura			= '';
		$this->IdCliente 			= '';
		$this->IdComprobante		= '';
		$this->NumeroComprobante	= '';
		$this->Fecha 				= '';
		$this->Detalle 				= '';
		$this->Subtotal 			= '';
		$this->Iva10 				= '';
		$this->Iva21 				= '';
		$this->Total 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFactura			= $arr['IdFactura'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->IdComprobante		= $arr['IdComprobante'];
		$this->NumeroComprobante	= $arr['NumeroComprobante'];
		$this->Fecha 				= $arr['Fecha'];
		$this->Detalle 				= stripslashes($arr['Detalle']);
		$this->Subtotal 			= $arr['Subtotal'];
		$this->Iva10 				= $arr['Iva10'];
		$this->Iva21 				= $arr['Iva21'];
		$this->Total 				= $arr['Total'];
	}
	
	
	public function GetAllDetalles()
	{
		$FacturaVariaDetalles = new FacturaVariaDetalles();
		
		return $FacturaVariaDetalles->GetAllByFacturaVaria($this);
	}
	
	public function SetNumeroComprobante($NumeroComprobante)
	{
		$this->NumeroComprobante = $NumeroComprobante;
	}
	
	public function SetFechaComprobante($FechaComprobante)
	{
		$this->Fecha = $FechaComprobante;
	}
	
	public function ActualizarFactura()
	{
		$oFacturaVarias = new FacturaVarias();
		$oFacturaVarias->Update($this);
	}
	
	public function ObtenerComprobante()
	{
		$oComprobantes = new Comprobantes();
		return $oComprobantes->GetById($this->IdComprobante);
	}
	
	public function ObtenerComprobanteAfipAsociado()
	{
		return false;
	}
}
?>
