<?php

class CompraFactura
{
	public $IdCompra;
	public $IdCliente;
	private $arrComprasDetalles;
	public $arrItems;
	private $arrItemsDescuento;
	private $arrItemsFranquicias;
	
	public function GenerarFacturaSinGuardar($oCompra, $oCliente, $oComprobante, $IdPlanCuota = 0, $Descuento = 0, $Comentarios, $Interes = 0)
	{
		$oCompras					= new Compras();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oFacturasItems				= new FacturasItems();
		$oFacturaPostVenta			= new FacturaPostVenta();
		$oPlanesCuotas				= new PlanesCuotas();
		
		$this->IdCompra = $oCompra->IdCompra;
		$oFacturaPostVenta->IdCompra = $oCompra->IdCompra;
		$oFacturaPostVenta->IdCliente = $oCompra->IdCliente;
		$oFacturaPostVenta->IdComprobante = $oComprobante->IdComprobante;
		$oFacturaPostVenta->NumeroFactura = $oComprobante->Prefijo .'-' . $oComprobante->Numero;
		$oFacturaPostVenta->Fecha = date('d-m-Y');
		$oFacturaPostVenta->Comentarios = $Comentarios;
		$oFacturaPostVenta->IdPlanCuota = $IdPlanCuota;
		
		$IndiceDescuento = 1;//(100 - $Descuento) / 100;

		$this->arrComprasDetalles = array();
		$this->arrItems = array();
		
		$this->ResumirCompras($oCompra, $IndiceDescuento);
		
		
		$oFacturaPostVenta->ImporteNeto = $this->ObtenerTotales('ImporteNeto');
		$oFacturaPostVenta->Iva21 = $this->ObtenerTotales('Iva21');
		$oFacturaPostVenta->Iva10 = $this->ObtenerTotales('Iva10');
		
		if ($Descuento > 0)
		{
			$IndiceDescuento = (100 - $Descuento) / 100;
			$IndiceDescuento2 = ($Descuento) / 100;
			$oItemD = new FacturaItem();
			$oItemD->Descripcion = 'DESCUENTO DEL ' . $Descuento . "%";
			$oItemD->Cantidad = 1;
			$oItemD->ImporteNeto = $oFacturaPostVenta->ImporteNeto * $IndiceDescuento2 * -1;
			$oItemD->Iva21 =  $oFacturaPostVenta->Iva21 * $IndiceDescuento2 * -1;
			$oItemD->Iva10 = $oFacturaPostVenta->Iva10 * $IndiceDescuento2 * -1;
			$oItemD->ImporteBruto = $oItemD->ImporteNeto + $oItemD->Iva21 + $oItemD->Iva10;
			$oItemD->IdIva = 1;
			$oItemD->IvaAlicuota = 21;
			
			$oFacturaPostVenta->ImporteNeto = $oFacturaPostVenta->ImporteNeto * $IndiceDescuento;
			$oFacturaPostVenta->Iva21 = $oFacturaPostVenta->Iva21 * $IndiceDescuento;
			$oFacturaPostVenta->Iva10 = $oFacturaPostVenta->Iva10 * $IndiceDescuento;
			
			$this->arrItems[] = $oItemD;
		}

		$oFacturaPostVenta->Descuentos = $Descuento;
		$oFacturaPostVenta->PercepcionIIBB = $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
		
		$oFacturaPostVenta->ImporteBruto = $oFacturaPostVenta->ImporteNeto + $oFacturaPostVenta->Iva21 + $oFacturaPostVenta->Iva10 + $oFacturaPostVenta->PercepcionIIBB - $DescuentoBruto;
		
		if ($Interes)
		{
			$oFacturaPostVenta->IdFormaPago = $IdFormaPago;
			$Interes = $oFacturaPostVenta->ImporteBruto * (1 + $Interes / 100);
			$Interes = $Interes - $oFacturaPostVenta->ImporteBruto;
			if ($Interes > 0)
			{
				$oItemInteres = new FacturaItem();
				$oItemInteres->Descripcion = 'INTERES PAGO';
				$oItemInteres->Cantidad = 1;
				$oItemInteres->ImporteNeto = $Interes / (1.21);
				$oItemInteres->ImporteBruto = $Interes;
				$oItemInteres->IdIva = 1;
				$oItemInteres->IvaAlicuota = 21;
				$oItemInteres->Iva21 = $oItemInteres->ImporteNeto * 0.21;
				$oItemInteres->Iva10 = 0;
				$oItemInteres->Interes = 1;
				
				$this->arrItems[] = $oItemInteres;
				
				$oFacturaPostVenta->ImporteNeto = $this->ObtenerTotales('ImporteNeto');
				$oFacturaPostVenta->Iva21 = $this->ObtenerTotales('Iva21');
				$oFacturaPostVenta->Iva10 = $this->ObtenerTotales('Iva10');
				

				$oFacturaPostVenta->Descuentos = $DescuentoBruto;
				$oFacturaPostVenta->PercepcionIIBB = $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
				
				$oFacturaPostVenta->ImporteBruto = $oFacturaPostVenta->ImporteNeto + $oFacturaPostVenta->Iva21 + $oFacturaPostVenta->Iva10 + $oFacturaPostVenta->PercepcionIIBB - $DescuentoBruto;
			}
		}
		/*
		$oFacturaPostVenta = $oFacturasPostVentas->Create($oFacturaPostVenta);
		
		
		foreach ($this->arrItems as $oItem)
		{
			$oItem->IdFactura = $oFacturaPostVenta->IdFacturaPostVenta;
			$oFacturasItems->Create($oItem);
		}*/
		
		return $oFacturaPostVenta;
	}
	
	public function GenerarFactura($oCompra, $oCliente, $oComprobante, $IdPlanCuota = 0, $Descuento = 0, $Comentarios, $Interes = 0)
	{
		$oCompras					= new Compras();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oFacturasItems				= new FacturasItems();
		$oFacturaPostVenta			= new FacturaPostVenta();
		$oPlanesCuotas				= new PlanesCuotas();
	
		$oFacturaPostVenta = $this->GenerarFacturaSinGuardar($oCompra, $oCliente, $oComprobante, $IdPlanCuota,$Descuento, $Comentarios, $Interes);
		$oFacturaPostVenta = $oFacturasPostVentas->Create($oFacturaPostVenta);
		
		foreach ($this->arrItems as $oItem)
		{
			$oItem->IdFactura = $oFacturaPostVenta->IdFacturaPostVenta;
			$oFacturasItems->Create($oItem);
		}
		
		return $oFacturaPostVenta;
	}
	
	private function ObtenerTotales($Campo)
	{
		$Total = 0;
		
		foreach ($this->arrItems as $oItem)
		{
			$Total+= $oItem->$Campo;
		}
		
		return $Total - $Descuentos;
	}
	
	private function ResumirCompras($oCompra, $IndiceDescuento = 1)
	{
		$oCompras 			= new Compras();
		$oArticulos			= new Articulos();
		$oIvas				= new Ivas();
		$oCuponesDescuento 	= new CuponesDescuento();
		
		$this->AgregarCompraDetalle($oCompra);
		$CoeficienteDescuento = $IndiceDescuento;
		
		/*if ($oCompra->IdCuponDescuento)
		{
			$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
			$CoeficienteDescuento = 1 - $oDescuento->Descuento / 100;
		}*/
			
		foreach ($this->arrComprasDetalles as $oCompraDetalle)
		{
			if ($oCompraDetalle->Cantidad > 0)
			{
				if (!$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo))
					throw new Exception('Articulo no existente.');
				$oIva = $oIvas->GetById($oArticulo->IdIva);
				
				$oItem = new FacturaItem();
				$oItem->Descripcion = $oArticulo->Codigo . ' - ' . $oArticulo->Descripcion;
				$oItem->Cantidad = $oCompraDetalle->Cantidad;
				$oItem->ImporteNeto = $oCompraDetalle->ImporteUnidad * $CoeficienteDescuento / (1 + $oIva->Alicuota) * $oCompraDetalle->Cantidad;
				$oItem->ImporteBruto = $oCompraDetalle->ImporteUnidad * $CoeficienteDescuento * $oCompraDetalle->Cantidad;
				$oItem->IdIva = $oIva->IdIva;
				$oItem->IdArticulo = $oArticulo->IdArticulo;
				$oItem->IvaAlicuota = $oIva->Alicuota * 100;
				
				if ($oIva->IdIva == Iva::Iva21)
				{
					$oItem->Iva21 = ($oCompraDetalle->ImporteUnidad * $CoeficienteDescuento * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
					$oItem->Iva10 = 0;
				}
				elseif ($oIva->IdIva == Iva::Iva10)
				{
					$oItem->Iva21 = 0;
					$oItem->Iva10 = ($oCompraDetalle->ImporteUnidad * $CoeficienteDescuento * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
				}
				else
				{
					$oItem->Iva21 = 0;
					$oItem->Iva10 = 0;
				}
					
				$this->arrItems[] = $oItem;
			}
		}
	}
	
	private function AgregarCompraDetalle($oCompra)
	{
		$oCompra->LoadAllDetalles();
		foreach ($oCompra->CompraDetalles as $oCompraDetalle)
		{
			$encontrado = false;
			foreach ($this->arrComprasDetalles as $cd)
			{
				if ($cd->IdArticulo == $oCompraDetalle->IdArticulo)
				{
					if ($oCompra->IdTipoMovimiento == TipoMovimiento::Venta)
						$cd->Cantidad += $oCompraDetalle->Cantidad;
					else
						$cd->Cantidad -= $oCompraDetalle->Cantidad;
					$cd->ImporteUnidad = $oCompraDetalle->ImporteUnidad;
					$encontrado = true;
				}
			}
			
			if (!$encontrado)
			{
				if ($oCompra->IdTipoMovimiento != TipoMovimiento::Venta)					
					$oCompraDetalle->Cantidad = $oCompraDetalle->Cantidad * -1;
				array_push($this->arrComprasDetalles, $oCompraDetalle);
			}
		}
	}
}

?>