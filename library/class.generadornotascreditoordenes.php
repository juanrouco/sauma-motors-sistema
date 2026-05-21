<?php
require_once("WSpooler/WSpooler.php");

class GeneradorNotasCreditoOrdenes
{
	protected $Host;
	protected $Port;
	protected $Separador;
	protected $WSpooler;	
	protected $OrdenTrabajo;
	protected $Ret;
	protected $Iva21;
	protected $Iva10;
	protected $arrComprasDetalles;
	protected $Factura;
	
	public function __construct()
	{
		$this->Host = '192.168.1.47';
		$this->Port = 1000;
		$this->Separador = chr(10);
		$this->WSpooler = new CWSpooler();
		$this->Iva21	= 0;
		$this->Iva10	= 0;
	}
	
	private function ImprimirDatosCliente($oCliente)
	{
		$oTiposIva 			= new TiposIva();
		$oLocalidades 		= new Localidades();
		$oPartidos 			= new Partidos();
		$oProvincias 		= new Provincias();
		$oPaises 			= new Paises();
		
		if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
			throw new Exception('Condicion de IVA del cliente no existente.');
			
		$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
		$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);
		$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);
		
		$comando = "@SetCustomerData|" . $oCliente->RazonSocial . "|";
		if ($oCliente->ClaveFiscalNumero)
			$comando .= str_replace('-', '', $oCliente->ClaveFiscalNumero) . "|";
		else
			$comando .= $oCliente->DocumentoNumero . "|";
		
		switch($oCliente->IdTipoIva)
		{
			case TipoIva::RI:
				$comando .= "I|";
				break;
			case TipoIva::RNI:
				$comando .= "N|";
				break;
			case TipoIva::MO:			
				$comando .= "M|";
				break;
			case TipoIva::EX:
				$comando .= "E|";
				break;
			default:
				$comando .= "C|";
				break;
		}
		if ($oCliente->ClaveFiscalNumero)
		{
			if ($oCliente->ClaveFiscalTipo == ClaveFiscalTipos::Cuit)
				$comando .= "C|";
			else
				$comando .= "L|";
		}
		else
		{
			switch($oCliente->DocumentoTipo)
			{
				case TipoDocumento::LE:
					$comando .= "0|";
					break;
				case TipoDocumento::LC:
					$comando .= "1|";
					break;
				case TipoDocumento::DNI:
					$comando .= "2|";
					break;
				case TipoDocumento::PA:
					$comando .= "3|";
					break;
				default:
					$comando .= "4|";
					break;
			}
		}
		
		$comando .= substr($oCliente->GetDomicilio(), 0, 50) . $this->Separador;

		$this->Ret = $this->WSpooler->if_write($comando);
		
	}
	
	private function AbrirRecibo($oCliente, $Factura)
	{
		$comando = "@SetEmbarkNumber|1|" . $Factura . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);print_r($comando);
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenDNFH|R|T" . $this->Separador;
		else
			$comando = "@OpenDNFH|S|T" . $this->Separador;

		$this->Ret = $this->WSpooler->if_write($comando);print_r($this->Ret);
		
	}
	
	private function ImprimirItem($oCompraDetalle)
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
		
print_r($comando);
		$this->Ret = $this->WSpooler->if_write($comando);print_r($this->Ret);
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

		$this->Ret = $this->WSpooler->if_write($comando);		
	}
	
	private function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	private function ImprimirTotal()
	{
		$comando = "@TotalTender|Efectivo|" . $this->Factura->Importe . "|T|0" . $this->Separador;

		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	private function CerrarRecibo()
	{
		$comando = "@CloseDNFH" . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);

		return $this->WSpooler->if_read(3);
	}
	
	private function ImprimirDescuento($oCompra, $oCuponDescuento)
	{
		$descuentoNeto = $oCompra->GetSubtotal() * ($oCuponDescuento->Descuento / 100);
		$comando = "@GeneralDiscount|Descuento aplicado por cupon: " . $oCuponDescuento->Numero . " del " . $oCuponDescuento->Descuento . "%|";
		$comando .= number_format($descuentoNeto, 2) . "|m|0|T" . $this->Separador;

		$this->Ret = $this->WSpooler->if_write($comando);

	}
	
	private function Cancelar()
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
	
	public function Imprimir($oOrdenTrabajo, $oNotaCredito)
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
		$oNotasCredito					= new NotasCredito();
		
		try
		{
			if (!$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad))
				throw new Exception('Taller Unidad no existente.');			
		
			if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
				throw new Exception('Cliente no existente.');
				
			if (!$oFactura = $oComprobantes->GetById($oOrdenTrabajo->IdComprobante))
				throw new Exception('Factura no existente.');
			
			$this->Factura = $oFactura;
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente, $oFactura->Prefijo . '-' . $oFactura->Numero);
			
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
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
			else
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
			$oComprobante->Prefijo = '0002';
			$oComprobante->Numero = $NumeroFactura;
			$oComprobante->Fecha = date('d-m-Y');
			$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
			$oComprobante->Importe = $this->OrdenTrabajo->ImporteTotal();
			$oComprobante->Importe = str_replace(',', '', $oComprobante->Importe);
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');
			$oComprobante->IdOrdenTrabajo = $this->OrdenTrabajo->IdOrdenTrabajo;

			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oNotaCredito->Iva21 = number_format($this->Iva21, 2, '.', '');
			$oNotaCredito->Iva10 = number_format($this->Iva10, 2, '.', '');
			
			$oNotaCredito->IdComprobante = $oComprobante->IdComprobante;
			$oNotasCredito->Update($oNotaCredito);
		}
		catch(Exception $ex)
		{
			$this->Cancelar();
		}
		$this->WSpooler->if_close();exit;	
	}
}

?>