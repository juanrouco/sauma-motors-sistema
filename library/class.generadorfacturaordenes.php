<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorFacturaOrdenes extends GeneradorDocumentos
{
	protected function AbrirRecibo($oCliente)
	{
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenFiscalReceipt|A|T" . $this->Separador;
		else
			$comando = "@OpenFiscalReceipt|B|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);		
	}
	
	
	protected function ImprimirItem($oCompraDetalle)
	{
		$oArticulos			= new Articulos();
		$oIvas				= new Ivas();
		
		if (!$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo))
			throw new Exception('Articulo no existente.');
		$oIva = $oIvas->GetById($oArticulo->IdIva);
		
		$comando = "@PrintLineItem|";		
		$comando .= substr(trim($oArticulo->Descripcion), 0, 50) . "|";
		$comando .= $oCompraDetalle->Cantidad . ".0|";
		$comando .= number_format(($oCompraDetalle->ImporteUnidad / (1 + $oIva->Alicuota)), 2, '.', '') . "|";
		$comando .= number_format($oIva->Alicuota * 100, 2, '.', '') . "|";
		$comando .= "M|0.0|0|B" . $this->Separador;
		
		if ($oIva->IdIva == Iva::Iva21)
			$this->Iva21 += ($oCompraDetalle->ImporteUnidad * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
		else
			$this->Iva10 += ($oCompraDetalle->ImporteUnidad * $oCompraDetalle->Cantidad / (1 + $oIva->Alicuota)) * $oIva->Alicuota;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	private function ImprimirItemTarea($oTarea)
	{
		$oIvas							= new Ivas();
		$oArticulos						= new Articulos();
		$oOrdenesTrabajoTareasArticulos = new OrdenesTrabajoTareasArticulos();
		
		$arrOrdenesTrabajoTareasArticulos = $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($oTarea);
		
		$precioArticulos = 0;
		if ($arrOrdenesTrabajoTareasArticulos)
		{
			foreach ($arrOrdenesTrabajoTareasArticulos as $oOrdenTrabajoTareaArticulo)
			{
				$precioArticulos += $oOrdenTrabajoTareaArticulo->PrecioTotal;
			}			
		}
		
		$oIva = $oIvas->GetById(1);
		
		$precioSinRepuestos = $oTarea->Importe - $precioArticulos;
		
		$comando = "@PrintLineItem|";		
		$comando .= substr($oTarea->Titulo, 0, 50) . "|";
		$comando .= "1.0|";
		$comando .= str_replace(',', '', number_format(($precioSinRepuestos / (1 + $oIva->Alicuota)), 2)) . "|";
		$comando .= number_format($oIva->Alicuota * 100, 2) . "|";
		$comando .= "M|0.0|0|B" . $this->Separador;
		
		$this->Iva21 += ($precioSinRepuestos / (1 + $oIva->Alicuota)) * $oIva->Alicuota;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirSubtotal);
	}
	
	protected function ImprimirTotal()
	{
		$comando = "@TotalTender|Efectivo|" . $this->OrdenTrabajo->ImporteTotal() . "|T|0" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirTotal);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseFiscalReceipt" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);

		return $this->WSpooler->if_read(3);
	}
	
	private function ImprimirDescuento($oCompra, $oCuponDescuento)
	{
		$descuentoNeto = $oCompra->GetSubtotal() * ($oCuponDescuento->Descuento / 100);
		$comando = "@GeneralDiscount|Descuento aplicado por cupon: " . $oCuponDescuento->Numero . " del " . $oCuponDescuento->Descuento . "%|";
		$comando .= number_format($descuentoNeto, 2) . "|m|0|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirDescuento);

	}
	
	protected function Cancelar()
	{
		$comando = "@Cancel|" . chr(152) . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
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
	
	public function Imprimir($oOrdenTrabajo)
	{
	
		$this->OrdenTrabajo = $oOrdenTrabajo;
		
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oTallerUnidades				= new TallerUnidades();
		$oCuponesDescuento 				= new CuponesDescuento();
		$oClientes						= new Clientes();
		$oComprobantes					= new Comprobantes();
		$oNumber						= new Number();
		$oCompras						= new Compras();
		$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
		$oOrdenesTrabajo				= new OrdenesTrabajo();
		$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
		$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
		
		try
		{
			if (!$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad))
				throw new Exception('Taller Unidad no existente.');			
		
			if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
				throw new Exception('Cliente no existente.');
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente);
			
			$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);
			if ($arrOrdenesTrabajoTareas)
			{
				foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
				{
					if ($oRelacion->IdTipoVenta == TipoVenta::OrdenReparacion || $oRelacion->IdTipoVenta == TipoVenta::ChapaYPintura || $oRelacion->IdTipoVenta == TipoVenta::Accesorios)
						$this->ImprimirItemTarea($oRelacion);
				}
			}
			
			$arrCompras = $oCompras->GetByOrdenTrabajo($oOrdenTrabajo);
			if ($arrCompras)
			{
				$this->arrComprasDetalles = array();
				foreach ($arrCompras as $oCompra)
				{
					if ($oCompra->TipoOperacion == TipoVenta::OrdenReparacion)
					{
						if ($oCompra->IdOrdenTrabajoTarea)
						{
							$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
							if ($oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oOrdenTrabajoTarea->IdTipoVenta == TipoVenta::Accesorios)
							{
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
					$this->ImprimirItem($oCompraDetalle);
				}
				
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
								$this->ImprimirDescuento($oCompra, $oCuponDescuento);
							}
						}
						else
						{
							$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
							$this->ImprimirDescuento($oCompra, $oCuponDescuento);
						}
					}
				}
			}
			
			$this->ImprimirSubtotal();
			$this->ImprimirTotal();
			
			$NumeroFactura = $this->CerrarRecibo();
			
			$oComprobante = new Comprobante();
			if ($oCliente->IdTipoIva == TipoIva::RI)
				$oComprobante->IdTipoComprobante = ComprobanteTipos::FacturaA;
			else
				$oComprobante->IdTipoComprobante = ComprobanteTipos::FacturaB;
			$oComprobante->Prefijo = '0002';
			$oComprobante->Numero = $NumeroFactura;
			$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
			$oComprobante->Fecha = date('d-m-Y');
			$oComprobante->Importe = $oOrdenTrabajo->ImporteTotal();
			$oComprobante->Importe = str_replace(',', '', $oComprobante->Importe);
			//$oComprobante->Importe = str_replace('.', '', $oComprobante->Importe);
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');

			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oOrdenTrabajo->IdComprobante = $oComprobante->IdComprobante;
			$oOrdenesTrabajo->Update($oOrdenTrabajo);
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}
		$this->WSpooler->if_close();
	}
}

?>