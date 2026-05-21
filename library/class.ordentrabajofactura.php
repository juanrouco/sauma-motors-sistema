<?php

class OrdenTrabajoFactura
{
	public $IdOrdenTrabajo;
	public $IdCliente;
	private $arrComprasDetalles;
	private $arrMO;
	public $arrItems;
	private $arrItemsDescuento;
	private $arrItemsFranquicias;
	
	public function GenerarFacturaSinGuardar($oOrdenTrabajo, $oCliente, $oComprobante, $IdPlanCuota = 0,$Descuento = 0, $Comentarios, $Interes = 0)
	{
		$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
		$oOrdenesTrabajoTareas 		= new OrdenesTrabajoTareas();
		$oCompras					= new Compras();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oFacturasItems				= new FacturasItems();
		$oFacturaPostVenta			= new FacturaPostVenta();
		$oPlanesCuotas				= new PlanesCuotas();
		
		$IndiceDescuento =1;// (100 - $Descuento) / 100;
		
		$this->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
		$oFacturaPostVenta->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
		$oFacturaPostVenta->IdCliente = $oCliente->IdCliente;
		$oFacturaPostVenta->IdComprobante = $oComprobante->IdComprobante;
		$oFacturaPostVenta->NumeroFactura = $oComprobante->Prefijo .'-' . $oComprobante->Numero;
		$oFacturaPostVenta->Fecha = date('d-m-Y');
		$oFacturaPostVenta->Comentarios = $Comentarios;
		$oFacturaPostVenta->IdPlanCuota = $IdPlanCuota;
		
		$this->arrComprasDetalles = array();
		$this->arrItems = array();
		$this->arrItemsDescuento = array();
		$this->arrItemsFranquicias = array();
		$this->arrMO = array();
		
		$this->CalcularTotalFranquicia();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);
		if ($arrOrdenesTrabajoTareas)
		{
			foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
			{
				if ($oRelacion->IdTipoVenta == TipoVenta::OrdenReparacion || $oRelacion->IdTipoVenta == TipoVenta::ChapaYPintura || $oRelacion->IdTipoVenta == TipoVenta::Accesorios)
				{
					$oItemMO = $this->ObtenerItemDesdeTarea($oRelacion, $IndiceDescuento);
					if ($oItemMO)
					{
						$this->arrMO[] = $oItemMO;
					}
				}
			}
		}
		
		$this->ResumirCompras($oOrdenTrabajo, $IndiceDescuento);
		foreach ($this->arrMO as $oItem)
		{
			$this->arrItems[] = $oItem;
		}
		$this->ResumirDescuentos($oOrdenTrabajo);
		
		
		if (count($this->arrItemsFranquicias) > 0)
		{
			$NetoF = 0;
			$BrutoF = 0;
			$IvaF = 0;
			foreach ($this->arrItemsFranquicias as $oItemFranquicia)
			{
				$NetoF+= $oItemFranquicia->ImporteNeto;
				$BrutoF+= $oItemFranquicia->ImporteBruto;
				$IvaF+= $oItemFranquicia->Iva21;
			}
			
			$oItemMO = new FacturaItem();
			$oItemMO->Descripcion = 'MANO DE OBRA';
			$oItemMO->Cantidad = 1;
			$oItemMO->ImporteNeto = 0;
			$oItemMO->ImporteBruto = 0;
			$oItemMO->IdIva = 1;
			$oItemMO->IvaAlicuota = 21;
			$oItemMO->Iva21 = 0;
			$oItemMO->Iva10 = 0;
		
			$oItemR = new FacturaItem();
			$oItemR->Descripcion = 'REPUESTOS';
			$oItemR->Cantidad = 1;
			$oItemR->ImporteNeto = 0;
			$oItemR->ImporteBruto = 0;
			$oItemR->IdIva = 1;
			$oItemR->IvaAlicuota = 21;
			$oItemR->Iva21 = 0;
			$oItemR->Iva10 = 0;
		
			foreach ($this->arrItems as $oItem)
			{
				if ($oItem->IdArticulo)
				{
					$oItemR->Descripcion = 'REPUESTOS';
					$oItemR->ImporteNeto+= $oItem->ImporteNeto;
					$oItemR->ImporteBruto+= $oItem->ImporteBruto;
					$oItemR->Iva21+= $oItem->Iva21;
				}
				else
				{
					$oItemMO->Descripcion = $oItem->Descripcion;
					$oItemMO->ImporteNeto+= $oItem->ImporteNeto;
					$oItemMO->ImporteBruto+= $oItem->ImporteBruto;
					$oItemMO->Iva21+= $oItem->Iva21;
				}
			}
			
			if ($oItemMO->ImporteBruto >= $BrutoF)
			{
				$oItemMO->ImporteNeto-= $NetoF;
				$oItemMO->ImporteBruto-= $BrutoF;
				$oItemMO->Iva21-= $IvaF;
				if ($oItemMO->ImporteBruto > 0)
					$this->arrItems = array($oItemMO);
				else
					$this->arrItems = array();
			}
			else
			{
				$this->arrItems = array();
				$NetoF-= $oItemMO->ImporteNeto;
				$BrutoF-= $oItemMO->ImporteBruto;
				$IvaF-= $oItemMO->Iva21;
				
				$oItemMO->ImporteNeto = 0;
				$oItemMO->ImporteBruto = 0;
				$oItemMO->Iva21 = 0;
				
				$oItemR->ImporteNeto-= $NetoF;
				$oItemR->ImporteBruto-= $BrutoF;
				$oItemR->Iva21-= $IvaF;
			}
			if ($oItemR->ImporteBruto > 0)
				$this->arrItems[] = $oItemR;
		}
		
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
		
		$DescuentoNeto = $this->ObtenerDescuentos('ImporteNeto');
		$DescuentoBruto = $this->ObtenerDescuentos('ImporteBruto');
		$DescuentoIva21 = $this->ObtenerDescuentos('Iva21');
		$DescuentoIva10 = $this->ObtenerDescuentos('Iva10');
		
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
				
				$DescuentoNeto = $this->ObtenerDescuentos('ImporteNeto');
				$DescuentoBruto = $this->ObtenerDescuentos('ImporteBruto');
				$DescuentoIva21 = $this->ObtenerDescuentos('Iva21');
				$DescuentoIva10 = $this->ObtenerDescuentos('Iva10');
				
				$oFacturaPostVenta->Descuentos = $DescuentoBruto;
				$oFacturaPostVenta->PercepcionIIBB = $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
				
				$oFacturaPostVenta->ImporteBruto = $oFacturaPostVenta->ImporteNeto + $oFacturaPostVenta->Iva21 + $oFacturaPostVenta->Iva10 + $oFacturaPostVenta->PercepcionIIBB - $DescuentoBruto;
			}
		}
		
		/*$oFacturaPostVenta = $oFacturasPostVentas->Create($oFacturaPostVenta);
		
		
		foreach ($this->arrItems as $oItem)
		{
			$oItem->IdFactura = $oFacturaPostVenta->IdFacturaPostVenta;
			$oFacturasItems->Create($oItem);
		}*/
		
		return $oFacturaPostVenta;
	}
	
	public function GenerarFactura($oOrdenTrabajo, $oCliente, $oComprobante, $IdPlanCuota = 0,$Descuento = 0, $Comentarios, $Interes = 0)
	{
		$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
		$oOrdenesTrabajoTareas 		= new OrdenesTrabajoTareas();
		$oCompras					= new Compras();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oFacturasItems				= new FacturasItems();
		$oFacturaPostVenta			= new FacturaPostVenta();
		$oPlanesCuotas				= new PlanesCuotas();
		
		$oFacturaPostVenta = $this->GenerarFacturaSinGuardar($oOrdenTrabajo, $oCliente, $oComprobante, $IdPlanCuota,$Descuento, $Comentarios, $Interes);
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
		
		$Descuentos = $this->ObtenerDescuentos($Campo);
		
		return $Total - $Descuentos;
	}
	
	private function ObtenerDescuentos($Campo)
	{
		$Total = 0;
		
		foreach ($this->arrItemsDescuento as $oItem)
		{
			$Total+= $oItem->$Campo;
		}
		
		return $Total;
	}
	
	private function ResumirCompras($oOrdenTrabajo, $IndiceDescuento = 1)
	{
		$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oArticulos				= new Articulos();
		$oIvas					= new Ivas();
		$arrOrdenesTrabajoTareas= array();
		
		$arrCompras = $oCompras->GetByOrdenTrabajo($oOrdenTrabajo);
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion)
				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar)
							{
								if (!in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrOrdenesTrabajoTareas))
								{
									$arrOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
									$this->AgregarRepuestoAgrupar($oOrdenTrabajoTarea->TotalRepuestos);
								}
							}
							else
								$this->AgregarCompraDetalle($oCompra);
						}
					}
					else
					{
						$this->AgregarCompraDetalle($oCompra);
					}
				}
			}
			
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
					$oItem->ImporteNeto = $oCompraDetalle->ImporteUnidad * $IndiceDescuento / (1 + $oIva->Alicuota) * $oCompraDetalle->Cantidad;
					$oItem->ImporteBruto = $oCompraDetalle->ImporteUnidad * $IndiceDescuento * $oCompraDetalle->Cantidad;
					$oItem->IdIva = $oIva->IdIva;
					$oItem->IdArticulo = $oArticulo->IdArticulo;
					$oItem->IvaAlicuota = $oIva->Alicuota * 100;
					
					if ($oIva->IdIva == Iva::Iva21)
					{
						$oItem->Iva21 = ($oCompraDetalle->ImporteUnidad * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota * $IndiceDescuento;
						$oItem->Iva10 = 0;
					}
					else
					{
						$oItem->Iva21 = 0;
						$oItem->Iva10 = ($oCompraDetalle->ImporteUnidad * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota * $IndiceDescuento;
					}
					
					$this->arrItems[] = $oItem;
				}
				
			}
		}
	}
	
	private function ResumirDescuentos($oOrdenTrabajo)
	{
		$oCompras 				= new Compras();
		$oArticulos				= new Articulos();
		$oIvas					= new Ivas();
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCuponesDescuento 		= new CuponesDescuento();
		
		$arrCompras = $oCompras->GetByOrdenTrabajo($oOrdenTrabajo);
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->IdCuponDescuento)
				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
							$this->arrItemsDescuento[] = $this->ObtenerDescuento($oCompra, $oCuponDescuento);
						}
					}
					else
					{
						$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
						$this->arrItemsDescuento[] = $this->ObtenerDescuento($oCompra, $oCuponDescuento);
					}
				}
			}
		}
	}
	
	private function CalcularTotalFranquicia()
	{
		$oOrdenesTrabajoFranquicias = new OrdenesTrabajoFranquicias();
		$oComprobantes 				= new Comprobantes();
		$oNotasCredito				= new NotasCredito();
		$oFacturasPostVentas		= new FacturasPostVentas();
		
		$arrFranquicias = $oOrdenesTrabajoFranquicias->GetByIdOrdenTrabajo($this->IdOrdenTrabajo);
		
		$oIvas = new Ivas();
		
		$oIva = $oIvas->GetById(Iva::Iva21);
		
		foreach ($arrFranquicias as $oOrdenTrabajoFranquicia)
		{	
			$oFacturaPostVenta = $oFacturasPostVentas->GetById($oOrdenTrabajoFranquicia->IdFactura);
			$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
			$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaPostVenta->IdComprobante);
			if (!$oNotaCredito)
			{
				$arrItems = $oFacturaPostVenta->GetAllItems();
				$Interes = 0;
				foreach ($arrItems as $oItem)
				{
					if ($oItem->Interes)
						$Interes+= $oItem->ImporteBruto;
				}
				
				$TotalFranquicia += $oOrdenTrabajoFranquicia->Importe;
				$oItem = new FacturaItem();
				$oItem->Descripcion = $oOrdenTrabajoFranquicia->Descripcion;
				$oItem->Cantidad = 1;
				$oItem->ImporteNeto = ($oOrdenTrabajoFranquicia->Importe - $oComprobante->PercepcionIIBB - $Interes) / (1 + $oIva->Alicuota);
				$oItem->ImporteBruto = $oOrdenTrabajoFranquicia->Importe - $oComprobante->PercepcionIIBB - $Interes;
				$oItem->IdIva = $oIva->IdIva;
				$oItem->IvaAlicuota = $oIva->Alicuota * 100;
				$oItem->Iva21 = (($oOrdenTrabajoFranquicia->Importe - $oComprobante->PercepcionIIBB - $Interes)  / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
				$oItem->Iva10 = 0;
			
				$this->arrItemsFranquicias[] = $oItem;
			}
		}
	}
	
	private function ObtenerItemDesdeTarea($oTarea, $IndiceDescuento = 1)
	{
		$oIvas = new Ivas();
		$oIva = $oIvas->GetById(Iva::Iva21);
		
		if ($oTarea->Agrupar)
			$PrecioManoObra = $oTarea->TotalMO;
		else
			$PrecioManoObra = $oTarea->ImporteSinRepuestos();
		
		if ($PrecioManoObra == 0)
			return false;
			
		$PrecioManoObra*= $IndiceDescuento;
			
		$oItem = new FacturaItem();
		if ($oTarea->IdTareaTrabajo)
		{
			if ($oTarea->Tarea)
				$oItem->Descripcion = $oTarea->Tarea;
			else
				$oItem->Descripcion = 'SERVICIO DE MANTENIMIENTO';//$oTarea->Titulo;
		}
		else
			$oItem->Descripcion = $oTarea->Tarea;
		
		$oItem->Cantidad = 1;
		$oItem->ImporteNeto = $PrecioManoObra / (1 + $oIva->Alicuota);
		$oItem->ImporteBruto = $PrecioManoObra;
		$oItem->IdIva = $oIva->IdIva;
		$oItem->IvaAlicuota = $oIva->Alicuota * 100;
		$oItem->Iva21 = $oItem->ImporteNeto * $oIva->Alicuota;
		$oItem->Iva10 = 0;
			/*
		if ($oTarea->IdTipoVenta == TipoVenta::ChapaYPintura)
		{
			$PrecioManoObra = $oTarea->Importe;
			$oItem->Descripcion = 'REPARACION DE CHAPA Y PINTURA';//$oTarea->Titulo;
			$oItem->ImporteNeto = $PrecioManoObra / (1 + $oIva->Alicuota);
			$oItem->ImporteBruto = $PrecioManoObra;
			$oItem->IdIva = $oIva->IdIva;
			$oItem->IvaAlicuota = $oIva->Alicuota * 100;
			$oItem->Iva21 = $oItem->ImporteNeto * $oIva->Alicuota;
			$oItem->Iva10 = 0;
		}*/
		
		return $oItem;
	}
	
	private function ObtenerDescuento($oCompra, $oCuponDescuento)
	{
		$oIvas = new Ivas();
		$DescuentoBruto = $oCompra->GetTotal() * ($oCuponDescuento->Descuento / 100);
		
		$oIva = $oIvas->GetById(Iva::Iva21);
		
		$oItem = new FacturaItem();
		$oItem->Descripcion = "Descuento aplicado por cupon: " . $oCuponDescuento->Numero . " del " . $oCuponDescuento->Descuento . "%";
		$oItem->Cantidad = 1;
		$oItem->ImporteNeto = $DescuentoBruto / (1 + $oIva->Alicuota);
		$oItem->ImporteBruto = $DescuentoBruto;
		$oItem->IdIva = $oIva->IdIva;
		$oItem->IvaAlicuota = $oIva->Alicuota * 100;
		$oItem->Iva21 = $oItem->ImporteNeto * $oIva->Alicuota;
		$oItem->Iva10 = 0;
		
		return $oItem;
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
	
	private function AgregarRepuestoAgrupar($TotalRepuestos)
	{
		$oCompraDetalle = new CompraDetalle();
		$oCompraDetalle->IdArticulo = Articulo::IdRepuestoAgrupar;
		$oCompraDetalle->Cantidad = 1;
		$oCompraDetalle->ImporteUnidad = $TotalRepuestos;
		$oCompraDetalle->ImporteCompraNeto = $TotalRepuestos;
		$oCompraDetalle->PrecioCompra = 0;
		array_push($this->arrComprasDetalles, $oCompraDetalle);
	}
	
	
	
	public function GenerarFacturaFromFranquicia($oOrdenTrabajoFranquicia, $oCliente, $oComprobante, $IdPlanCuota = 0, $Descuento = 0)
	{
		$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
		$oOrdenesTrabajoTareas 		= new OrdenesTrabajoTareas();
		$oCompras					= new Compras();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oFacturasItems				= new FacturasItems();
		$oFacturaPostVenta			= new FacturaPostVenta();
		$oPlanesCuotas				= new PlanesCuotas();
		
		$this->IdOrdenTrabajo = $oOrdenTrabajoFranquicia->IdOrdenTrabajo;
		$oFacturaPostVenta->IdOrdenTrabajo = $oOrdenTrabajoFranquicia->IdOrdenTrabajo;
		$oFacturaPostVenta->IdCliente = $oCliente->IdCliente;
		$oFacturaPostVenta->IdComprobante = $oComprobante->IdComprobante;
		$oFacturaPostVenta->NumeroFactura = $oComprobante->Prefijo .'-' . $oComprobante->Numero;
		$oFacturaPostVenta->Fecha = date('d-m-Y');
		
		$this->arrComprasDetalles = array();
		$this->arrItems = array();
		$this->arrItemsDescuento = array();
		

		$oItem = new FacturaItem();
		$oItem->Descripcion = $oOrdenTrabajoFranquicia->Descripcion;
		$oItem->Cantidad = 1;
		$oItem->ImporteNeto = $oOrdenTrabajoFranquicia->Importe / (1.21);
		$oItem->ImporteBruto = $oOrdenTrabajoFranquicia->Importe;
		$oItem->IdIva = 1;
		$oItem->IvaAlicuota = 21;
		$oItem->Iva21 = $oItem->ImporteNeto * 0.21;
		$oItem->Iva10 = 0;
		
		$this->arrItems[] = $oItem;
		
		$oFacturaPostVenta->ImporteNeto = $this->ObtenerTotales('ImporteNeto');
		$oFacturaPostVenta->Iva21 = $this->ObtenerTotales('Iva21');
		$oFacturaPostVenta->Iva10 = $this->ObtenerTotales('Iva10');
		
		$DescuentoNeto = $this->ObtenerDescuentos('ImporteNeto');
		$DescuentoBruto = $this->ObtenerDescuentos('ImporteBruto');
		$DescuentoIva21 = $this->ObtenerDescuentos('Iva21');
		$DescuentoIva10 = $this->ObtenerDescuentos('Iva10');
		
		$oFacturaPostVenta->Descuentos = $DescuentoBruto;
		$oFacturaPostVenta->PercepcionIIBB = $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
		
		$oFacturaPostVenta->ImporteBruto = $oFacturaPostVenta->ImporteNeto + $oFacturaPostVenta->Iva21 + $oFacturaPostVenta->Iva10 + $oFacturaPostVenta->PercepcionIIBB - $DescuentoBruto;
		//$oFacturaPostVenta->TotalPago = $oFacturaPostVenta->ImporteBruto;
		
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
				
				$DescuentoNeto = $this->ObtenerDescuentos('ImporteNeto');
				$DescuentoBruto = $this->ObtenerDescuentos('ImporteBruto');
				$DescuentoIva21 = $this->ObtenerDescuentos('Iva21');
				$DescuentoIva10 = $this->ObtenerDescuentos('Iva10');
				
				$oFacturaPostVenta->Descuentos = $DescuentoBruto;
				$oFacturaPostVenta->PercepcionIIBB = $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
				
				$oFacturaPostVenta->ImporteBruto = $oFacturaPostVenta->ImporteNeto + $oFacturaPostVenta->Iva21 + $oFacturaPostVenta->Iva10 + $oFacturaPostVenta->PercepcionIIBB - $DescuentoBruto;
			}
		}
		
		$oFacturaPostVenta = $oFacturasPostVentas->Create($oFacturaPostVenta);
		
		foreach ($this->arrItems as $oItem)
		{
			$oItem->IdFactura = $oFacturaPostVenta->IdFacturaPostVenta;
			$oFacturasItems->Create($oItem);
		}
		
		return $oFacturaPostVenta;
	}
}

?>