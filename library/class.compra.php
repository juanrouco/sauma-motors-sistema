<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');
require_once('class.cuponesdescuento.php');

class Compra
{
	public $IdCompra;
	public $IdUbicacion;
	public $TipoOperacion;
	public $FechaCarga;
	public $IdFactura;
	public $IdRemito;
	public $IdCliente;
	public $IdTallerUnidad;
	public $IdOrdenTrabajo;
	public $CompraDetalles;
	public $Transporte;
	public $TransporteClaveFiscalTipo;
	public $TransporteClaveFiscalNumero;
	public $IdCuponDescuento;
	public $IdOrdenTrabajoTarea;
	public $IdNotaCredito;
	public $IdTipoMovimiento;
	public $Total;
	public $Iva21;
	public $Iva10;
	public $PercepcionIIBB;
	public $NumeroVale;
	
	public function __construct()
	{
		$this->IdCompra 					= '';
		$this->IdUbicacion 					= '';
		$this->TipoOperacion				= '';
		$this->FechaCarga 					= '';		
		$this->IdFactura 					= '';
		$this->IdRemito 					= '';
		$this->IdCliente 					= '';
		$this->IdTallerUnidad				= '';
		$this->IdOrdenTrabajo				= '';
		$this->Transporte					= '';
		$this->TransporteClaveFiscalTipo	= '';
		$this->TransporteClaveFiscalNumero	= '';
		$this->IdCuponDescuento				= '';
		$this->IdOrdenTrabajoTarea			= '';
		$this->IdNotaCredito				= '';
		$this->IdTipoMovimiento				= '';
		$this->Total						= '';
		$this->Iva21						= '';
		$this->Iva10						= '';
		$this->PercepcionIIBB				= '';
		$this->NumeroVale					= '';
		$this->CompraDetalles				= array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCompra 					= $arr['IdCompra'];
		$this->IdUbicacion 					= $arr['IdUbicacion'];
		$this->TipoOperacion 				= $arr['TipoOperacion'];
		$this->FechaCarga 					= $arr['FechaCarga'];
		$this->IdFactura 					= $arr['IdFactura'];
		$this->IdCliente 					= $arr['IdCliente'];
		$this->IdTallerUnidad				= $arr['IdTallerUnidad'];
		$this->IdOrdenTrabajo				= $arr['IdOrdenTrabajo'];
		$this->IdRemito 					= $arr['IdRemito'];
		$this->Transporte 					= $arr['Transporte'];
		$this->TransporteClaveFiscalTipo 	= $arr['TransporteClaveFiscalTipo'];
		$this->TransporteClaveFiscalNumero 	= $arr['TransporteClaveFiscalNumero'];
		$this->IdCuponDescuento				= $arr['IdCuponDescuento'];
		$this->IdOrdenTrabajoTarea			= $arr['IdOrdenTrabajoTarea'];
		$this->IdNotaCredito				= $arr['IdNotaCredito'];
		$this->Total						= $arr['Total'];
		$this->Iva21						= $arr['Iva21'];
		$this->Iva10						= $arr['Iva10'];
		$this->IdTipoMovimiento				= $arr['IdTipoMovimiento'];
		$this->PercepcionIIBB				= $arr['PercepcionIIBB'];
		$this->NumeroVale					= $arr['NumeroVale'];
	}
	
	
	public function LoadAllDetalles()
	{
		$CompraDetalles = new CompraDetalles();
		
		$this->CompraDetalles = $CompraDetalles->GetAllByCompra($this);
	}
	
	public function CantidadRepuestos()
	{
		$total = 0;
		$this->LoadAllDetalles();
		
		if ($this->CompraDetalles)
		{
			foreach ($this->CompraDetalles as $ocd)
			{
				$total+= $ocd->Cantidad;
			}
		}
		
		return $total;
	}
	
	public function Costo()
	{
		$total = 0;
		$this->LoadAllDetalles();
		
		if ($this->CompraDetalles)
		{
			foreach ($this->CompraDetalles as $ocd)
			{
				$total+= $ocd->Cantidad * $ocd->PrecioCompra;
			}
		}
		
		return $total;
	}
	
	public function Total()
	{
		$total = 0;
		if ($this->IdCuponDescuento)
		{
			$oCuponesDescuento = new CuponesDescuento();
			$oCuponDescuento = $oCuponesDescuento->GetById($this->IdCuponDescuento);
			$total = $this->GetSubtotal() * (1 - ($oCuponDescuento->Descuento / 100));
			$total += $this->GetTotalIva();			
		}
		else
		{
			$total = $this->Total;	
		}
		return $total;
	}
	
	public function GetSubtotal()
	{
		return $this->Total - $this->Iva21 - $this->Iva10 - $this->PercepcionIIBB;
		/*$oArticulos = new Articulos();
		$oIvas = new Ivas();
		$subtotal = 0;
		foreach ($this->CompraDetalles as $oCompraDetalle)
		{
			$oArticulo 	= $oArticulos->GetById($oCompraDetalle->IdArticulo);
			$oIva		= $oIvas->GetById($oArticulo->IdIva);
			$div 		= $oIva->Alicuota + 1;
			$subtotal += number_format(($oCompraDetalle->ImporteCompraNeto / $div), 2);			
		}
		return $subtotal;*/
	}
	
	public function GetSubtotalIva($IdIva)
	{
		if ($IdIva == Iva::Iva21)
			return $this->Iva21;
		else
			return $this->Iva10;
		/*$oArticulos = new Articulos();
		$oIvas = new Ivas();
		$oIva = $oIvas->GetById($IdIva);
		$subtotal = 0;
		foreach ($this->CompraDetalles as $oCompraDetalle)
		{
			$oArticulo 	= $oArticulos->GetById($oCompraDetalle->IdArticulo);
			if ($oIva->IdIva == $oArticulo->IdIva)
			{
				$div 		= $oIva->Alicuota + 1;
				$subtotal += number_format(($oCompraDetalle->ImporteCompraNeto - ($oCompraDetalle->ImporteCompraNeto / $div)), 2);
			}
		}
		return $subtotal;*/
	}
	
	public function GetTotalIva()
	{
		return $this->Iva21 + $this->Iva10;
		/*$oArticulos = new Articulos();
		$oIvas = new Ivas();		
		$subtotal = 0;
		foreach ($this->CompraDetalles as $oCompraDetalle)
		{
			$oArticulo 	= $oArticulos->GetById($oCompraDetalle->IdArticulo);
			$oIva = $oIvas->GetById($oArticulo->IdIva);			
			$div 		= $oIva->Alicuota + 1;
			$subtotal += number_format(($oCompraDetalle->ImporteCompraNeto - ($oCompraDetalle->ImporteCompraNeto / $div)), 2);			
		}
		return $subtotal;*/
	}
}

?>