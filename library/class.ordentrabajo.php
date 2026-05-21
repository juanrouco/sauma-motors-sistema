<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.ordenestrabajotareas.php');
require_once('class.articulos.php');
require_once('class.compras.php');
require_once('class.ivas.php');
require_once('class.tipoventa.php');
require_once('class.facturaspostventas.php');
require_once('class.notascredito.php');

class OrdenTrabajo
{
	const PathImageBig		= '../_recursos/ordentrabajo/imagenes/big/';
	const PathImageThumb	= '../_recursos/ordentrabajo/imagenes/thumb/';
	const PathFile			= '../_recursos/ordentrabajo/archivos/';
	
	public $IdOrdenTrabajo;
	public $IdEstadoOrden;
	public $IdTallerUnidad;
	public $Fecha;
	public $FechaInicio;
	public $FechaFin;
	public $IdUsuarioCreacion;
	public $IdUsuarioAsignado;
	public $Kilometros;
	public $Comentarios;
	public $IdTipoVenta;
	public $IdComprobante;
	public $Bahia;
	
	public function __construct()
	{
		$this->IdOrdenTrabajo 		= '';
		$this->IdEstadoOrden		= '';
		$this->IdTallerUnidad 		= '';
		$this->Fecha 				= '';
		$this->FechaInicio	 		= '';
		$this->FechaFin			 	= '';
		$this->IdUsuarioCreacion	= '';
		$this->IdUsuarioAsignado 	= '';
		$this->Kilometros			= '';
		$this->Comentarios			= '';
		$this->IdTipoVenta			= '';
		$this->IdComprobante		= '';
		$this->Bahia				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajo 		= $arr['IdOrdenTrabajo'];
		$this->IdEstadoOrden		= $arr['IdEstadoOrden'];
		$this->IdTallerUnidad		= $arr['IdTallerUnidad'];
		$this->Fecha				= $arr['Fecha'];
		$this->FechaInicio	 		= $arr['FechaInicio'];
		$this->FechaFin				= $arr['FechaFin'];
		$this->IdUsuarioCreacion	= $arr['IdUsuarioCreacion'];
		$this->IdUsuarioAsignado	= $arr['IdUsuarioAsignado'];
		$this->Kilometros			= $arr['Kilometros'];
		$this->Comentarios			= $arr['Comentarios'];
		$this->IdTipoVenta			= $arr['IdTipoVenta'];
		$this->IdComprobante		= $arr['IdComprobante'];
		$this->Bahia				= $arr['Bahia'];
	}
		
	public function ImporteTotal()
	{
		return number_format($this->ImporteTotalCalculado(), 2);
	}
		
	public function ImporteFacturado()
	{
		$oFacturasPostVentas	= new FacturasPostVentas();
		$oNotasCredito			= new NotasCredito();
		
		$arrFacturasPostVentas = $oFacturasPostVentas->GetByOrdenTrabajo($this);
		$Total = 0;
		
		foreach ($arrFacturasPostVentas as $oFacturaPostVenta)
		{
			if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaPostVenta->IdComprobante))
				$Total += $oFacturaPostVenta->ImporteBruto;
		}
		return number_format($Total, 2);
	}
	
	public function GetAllTareas()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		
		return $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
	}
	
	public function GetListoFinalizar()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		
		$arrTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		
		foreach($arrTareas as $oTarea)
		{
			if ($oTarea->Tarea == '' && $oTarea->IdTipoVenta != TipoVenta::VentaInterna && $oTarea->IdTipoVenta != TipoVenta::Garantia)
				return false;
		}
		
		return true;
	}
	
	public function GetTareaNegativa()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		
		$arrTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		
		foreach($arrTareas as $oTarea)
		{
			$ImporteManoObra = $oTarea->ImporteSinRepuestos();
			
			if ($ImporteManoObra < 0 && $oTarea->IdTipoVenta != TipoVenta::Garantia && $oTarea->IdTipoVenta != TipoVenta::VentaInterna)
				return false;
		}
		
		return true;
	}
	
	public function ImporteTotalCalculado()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$oFacturasPostVentas	= new FacturasPostVentas();
		$arrIdOrdenesTrabajoTareas= array();
		
		$oFacturaPostVenta = $oFacturasPostVentas->GetByOrdenTrabajo($this);
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
			{
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
			}
		}
		
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)
				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Mostrador || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar)
							{
								if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
								{
									$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
									$total+= $oOrdenTrabajoTarea->TotalRepuestos;
								}
							}
							else
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21% 
		
		$subtotal = ($total / 1.21);
		$percepcionIIBB = 0;
		if ($oCliente->PercepcionIIBB && $oCliente->PercepcionIIBB > 0)
			$percepcionIIBB = $subtotal * $oCliente->PercepcionIIBB / 100;
			
		$total+= $percepcionIIBB;
		if ($oFacturaPostVenta && false)
			$Descuentos = $oFacturaPostVenta[count($oFacturaPostVenta) - 1]->Descuentos;
		
		if ($Descuentos)
			$total  = $total * (100 - $Descuentos) / 100;
		return $total;
	}
	
	public function ObtenerDescuento()
	{
		$oFacturasPostVentas = new FacturasPostVentas();
		$arrFacturasPostVentas = $oFacturasPostVentas->GetByOrdenTrabajo($this);
		
		if (!$arrFacturasPostVentas)
			return 0;
		
		$oFacturaPostVenta = $arrFacturasPostVentas[count($arrFacturasPostVentas) - 1];
		return $oFacturaPostVenta->Descuentos;
	}
	
	public function ImporteTotalFacturado()
	{
		$oFacturasPostVentas = new FacturasPostVentas();
		$arrFacturasPostVentas = $oFacturasPostVentas->GetByOrdenTrabajo($this);
		
		if (!$arrFacturasPostVentas)
			return 0;
		
		$oFacturaPostVenta = $arrFacturasPostVentas[count($arrFacturasPostVentas) - 1];
		return $oFacturaPostVenta->ImporteBruto;
	}
	
	public function ImporteIva()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$arrIdOrdenesTrabajoTareas = array();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
		}
		
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)
				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Mostrador || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
							{
								$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$total+= $oOrdenTrabajoTarea->TotalRepuestos;
							}
							elseif (!$oOrdenTrabajoTarea->Agrupar)
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21% 
		
		$subtotal = ($total / 1.21);
		
		return $subtotal * 0.21;
	}
	
	public function ImportePercepcionIIBB()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$arrIdOrdenesTrabajoTareas = array();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
		}
		
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)

				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Mostrador || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
							{
								$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$total+= $oOrdenTrabajoTarea->TotalRepuestos;
							}
							elseif (!$oOrdenTrabajoTarea->Agrupar)
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21%
		$subtotal = ($total / 1.21);
		$percepcionIIBB = 0;
		if ($oCliente->PercepcionIIBB && $oCliente->PercepcionIIBB > 0)
			$percepcionIIBB = $subtotal * $oCliente->PercepcionIIBB / 100;
			
		return $percepcionIIBB;
	}
	
	public function ImporteNetoCalculado()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$arrIdOrdenesTrabajoTareas= array();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
		}
		
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)

				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
							{
								$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$total+= $oOrdenTrabajoTarea->TotalRepuestos;
							}
							elseif (!$oOrdenTrabajoTarea->Agrupar)
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21%
		$subtotal = ($total / 1.21);
		
		return $subtotal;
	}
	
	public function ImporteNetoChapaYPintura()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$arrIdOrdenesTrabajoTareas= array();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdCategoria == Categorias::ChapaYPintura && ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios))
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
		}
		
		if ($arrCompras && false)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)

				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdCategoria == Categorias::ChapaYPintura && ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios))
						{
							if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
							{
								$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$total+= $oOrdenTrabajoTarea->TotalRepuestos;
							}
							elseif (!$oOrdenTrabajoTarea->Agrupar)
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21%
		$subtotal = ($total / 1.21);
		
		return $subtotal;
	}
	
	public function ImporteNeto()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		$oCompras 				= new Compras();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$arrIdOrdenesTrabajoTareas = array();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$arrCompras = $oCompras->GetByOrdenTrabajo($this);
		
		$oTallerUnidad = $oTallerUnidades->GetById($this->IdTallerUnidad);
		$oCliente	= $oClientes->GetById($oTallerUnidad->IdCliente);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteSinRepuestos();
		}
		
		if ($arrCompras)
		{
			foreach ($arrCompras as $oCompra)
			{
				if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Mostrador || $oCompra->TipoOperacion == TipoVenta::OrdenReparacion || $oCompra->TipoOperacion == TipoVenta::ChapaYPintura || $oCompra->TipoOperacion == TipoVenta::Accesorios)

				{
					if ($oCompra->IdOrdenTrabajoTarea)
					{
						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
						{
							if ($oOrdenTrabajoTarea->Agrupar && !in_array($oOrdenTrabajoTarea->IdOrdenTrabajoTarea, $arrIdOrdenesTrabajoTareas))
							{
								$arrIdOrdenesTrabajoTareas[] = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$total+= $oOrdenTrabajoTarea->TotalRepuestos;
							}
							elseif (!$oOrdenTrabajoTarea->Agrupar)
							{
								$oCompra->LoadAllDetalles();
								if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
									$total -= $oCompra->Total();
								else
									$total += $oCompra->Total();
							}
						}
					}
					else
					{
						$oCompra->LoadAllDetalles();
						if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
							$total -= $oCompra->Total();
						else
							$total += $oCompra->Total();
					}
				}
			}
		}
		
		//Asumo siempre IVa del 21%
		$subtotal = ($total / 1.21);
		
			
		return $subtotal;
	}
	
	public function ImporteEstimado()
	{
		$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->Importe;
		}
		
		return number_format($total, 2);
	}
	
	public function ImporteManoObraCalculado()
	{
		$total = $this->ImporteTotalCalculado();
		$repuestos = $this->ImporteRepuestosCalculado();
		return $total - $repuestos;
	}
	
	public function ImporteManoObraNetoCalculado()
	{
		$total = $this->ImporteNetoCalculado();
		$repuestos = $this->ImporteRepuestosCalculado();
		$repuestos = $repuestos / 1.21;
		$Descuento = $this->ObtenerDescuento();
		return ($total - $repuestos) * (100 - $Descuento) / 100;
	}
	
	public function ImporteManosObraNetoCalculadoPorCategoria($IdTipoVenta, $IdCategoria)
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == $IdTipoVenta && $oOrdenTrabajoTarea->IdCategoria == $IdCategoria)
			{
				$totalOT = $oOrdenTrabajoTarea->ImporteSinRepuestos();
				$total+= $totalOT / 1.21;
			}
		}
		$Descuento = $this->ObtenerDescuento();
		
		return $total * (100 - $Descuento) / 100;
	}
	
	public function ImporteManoObra()
	{
		$total = $this->ImporteTotalCalculado();
		$repuestos = $this->ImporteRepuestosCalculado();
		return number_format($total - $repuestos, 2);
	}
	
	public function ImporteRepuestosCalculado()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteRepuestosReal();
		}
		return $total;
	}
	
	public function CostoRepuestosCalculado()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->CostoRepuestosReal();
		}
		return $total;
	}
	
	public function ImporteRepuestosNetoCalculado()
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oOrdenTrabajoTarea->ImporteRepuestosReal();
		}
		$Descuento = $this->ObtenerDescuento();
		
		return ($total / 1.21) * (100 - $Descuento) / 100;
	}
	
	public function ImporteRepuestosNetoCalculadoPorCategoria($IdTipoVenta, $IdCategoria)
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($this);
		$total = 0;
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			if ($oOrdenTrabajoTarea->IdTipoVenta == $IdTipoVenta && $oOrdenTrabajoTarea->IdCategoria == $IdCategoria)
				$total += $oOrdenTrabajoTarea->ImporteRepuestosReal();
		}
		$Descuento = $this->ObtenerDescuento();
		return ($total / 1.21) * (100 - $Descuento) / 100;
	}
	
	
	
	public function ImporteRepuestos()
	{
		return number_format($this->ImporteRepuestosCalculado, 2);
	}
}

?>