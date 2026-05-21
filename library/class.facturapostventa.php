<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');
require_once('class.cuponesdescuento.php');
require_once('class.facturasitems.php');
require_once('class.facturaspostventas.php');

class FacturaPostVenta implements IFacturaElectronica
{
	public $IdFacturaPostVenta;
	public $IdOrdenTrabajo;
	public $IdCompra;
	public $ImporteNeto;
	public $ImporteBruto;
	public $Iva21;
	public $Iva10;
	public $Descuentos;
	public $Percepciones;
	public $IdCliente;
	public $Fecha;
	public $IdComprobante;
	public $NumeroFactura;
	public $Comentarios;
	public $TotalPago;
	public $FechaPago;
	public $IdFormaPago;
	public $IdPlanCuota;
	
	public function __construct()
	{
		$this->IdFacturaPostVenta		= '';
		$this->IdOrdenTrabajo			= '';
		$this->IdCompra					= '';
		$this->ImporteNeto 				= '';		
		$this->ImporteBruto				= '';
		$this->Iva21 					= '';
		$this->Iva10 					= '';
		$this->Descuentos				= '';
		$this->PercepcionIIBB			= '';
		$this->IdCliente				= '';
		$this->Fecha					= '';
		$this->IdComprobante			= '';
		$this->NumeroFactura			= '';
		$this->Comentarios				= '';
		$this->TotalPago				= '';
		$this->FechaPago				= '';
		$this->IdFormaPago				= '';
		$this->IdPlanCuota				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFacturaPostVenta		= $arr['IdFacturaPostVenta'];
		$this->IdOrdenTrabajo 			= $arr['IdOrdenTrabajo'];
		$this->IdCompra 				= $arr['IdCompra'];
		$this->ImporteNeto 				= $arr['ImporteNeto'];
		$this->ImporteBruto 			= $arr['ImporteBruto'];
		$this->Iva21 					= $arr['Iva21'];
		$this->Iva10 					= $arr['Iva10'];
		$this->Descuentos				= $arr['Descuentos'];
		$this->PercepcionIIBB			= $arr['PercepcionIIBB'];
		$this->IdCliente				= $arr['IdCliente'];
		$this->Fecha 					= $arr['Fecha'];
		$this->IdComprobante 			= $arr['IdComprobante'];
		$this->NumeroFactura 			= $arr['NumeroFactura'];
		$this->Comentarios	 			= $arr['Comentarios'];
		$this->TotalPago	 			= $arr['TotalPago'];
		$this->FechaPago	 			= $arr['FechaPago'];
		$this->IdFormaPago	 			= $arr['IdFormaPago'];
		$this->IdPlanCuota	 			= $arr['IdPlanCuota'];
	}
	
	public function GetAllItems()
	{
		$oFacturasItems = new FacturasItems();
		
		return $oFacturasItems->GetAllByFactura($this);
	}
	
	public function SetNumeroComprobante($NumeroComprobante)
	{
		$this->NumeroFactura = $NumeroComprobante;
	}
	
	public function SetFechaComprobante($FechaComprobante)
	{
		$this->Fecha = $FechaComprobante;
	}
	
	public function ActualizarFactura()
	{
		$oFacturasPostVentas = new FacturasPostVentas();
		$oFacturasPostVentas->Update($this);
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