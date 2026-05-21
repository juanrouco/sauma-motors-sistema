<?php
require_once("WSpooler/WSpooler.php");
require_once("class.formapago.php");
require_once("class.generadordocumentos.php");

class GeneradorNotasCreditoFacturasPostVentas extends GeneradorDocumentos
{	
	private $oFacturaPostVenta;
	
	protected function AbrirRecibo($oCliente, $Factura)
	{
		$comando = "@SetEmbarkNumber|1|" . $Factura . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
		
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenDNFH|R|T|" . $Factura . $this->Separador;
		else
			$comando = "@OpenDNFH|S|T|" . $Factura . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);	
		
		return $this->WSpooler->if_read(3);
	}
	
	protected function ImprimirItem($oFacturaItem)
	{
		$oIvas				= new Ivas();
		
		$oIva = $oIvas->GetById($oFacturaItem->IdIva);
		$ImporteUnidad = $oFacturaItem->ImporteNeto / $oFacturaItem->Cantidad;
		
		
		if ($ImporteUnidad > 0)
		{
			$comando = "@PrintLineItem|";
			$comando .= substr(sanear_string($oFacturaItem->Descripcion), 0, 50) . "|";
			$comando .= number_format(floatval($oFacturaItem->Cantidad), 1, '.', '') . "|";
			$comando .= number_format(abs($ImporteUnidad), 2, '.', '') . "|";
			$comando .= number_format($oIva->Alicuota * 100, 2) . "|";
			$comando .= "M|0.0|0|B" . $this->Separador;
		}
		else
		{
			$comando = "@GeneralDiscount|";		
			$comando .= substr(sanear_string($oFacturaItem->Descripcion), 0, 50) . "|";
			$comando .= number_format(abs($oFacturaItem->ImporteBruto), 2, '.', '') . "|";
			$comando .= "m|0|T" . $this->Separador;
		}

		if ($oIva->IdIva == Iva::Iva21)
			$this->Iva21 += $oFacturaItem->Iva21;
		else
			$this->Iva10 += $oFacturaItem->Iva10;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirSubtotal);
	}
	
	protected function ImprimirTotal()
	{
		if ($this->FacturaPostVenta->IdFormaPago == FormaPago::CtaCte)
			$comando = "@TotalTender|Cuenta Corriente|" . $this->FacturaPostVenta->ImporteBruto . "|T|0" . $this->Separador;
		else
			$comando = "@TotalTender|Efectivo|" . $this->FacturaPostVenta->ImporteBruto . "|T|0" . $this->Separador;

		
		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirTotal);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseDNFH" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);

	}
	
	/*protected function ImprimirDescuento($oCompra, $oCuponDescuento)
	{
		//$descuentoNeto = $oCompra->GetSubtotal() * ($oCuponDescuento->Descuento / 100);
		$descuentoNeto = $oCuponDescuento->Descuento;
		$comando = "@GeneralDiscount|Descuento aplicado por cupon: " . $oCuponDescuento->Numero . " del " . $oCuponDescuento->Descuento . "%|";
		$comando .= number_format($descuentoNeto, 2) . "|m|0|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirDescuento);

	}*/
	
	protected function ImprimirPercepciones($oFacturaPostVenta, $oCliente)
	{
		if ($oFacturaPostVenta->PercepcionIIBB && $oCliente->PercepcionIIBB)
		{
			$comando = "@Perceptions|**.**|Percep. IIBB Bs. As.|" . $oFacturaPostVenta->PercepcionIIBB . $this->Separador;

			$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirPercepciones);
		}
	}
	
	protected function Cancelar()
	{
		$comando = "@Cancel|" . chr(152) . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	public function Imprimir($oFacturaPostVenta)
	{
		$this->FacturaPostVenta = $oFacturaPostVenta;
		
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oNotasCredito	 		= new NotasCredito();
		$oCuponesDescuento 		= new CuponesDescuento();
		$oClientes				= new Clientes();
		$oComprobantes			= new Comprobantes();
		$oNumber				= new Number();
		$oFacturasPostVentas	= new FacturasPostVentas();
		$oCuponDescuento	= false;
		
		try
		{
			if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaPostVenta->IdComprobante))
			{
				$this->WSpooler->if_close();
				return;
			}
			$arrItems = $oFacturaPostVenta->GetAllItems();
		
			if (!$oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente))
				throw new Exception('Cliente no existente.');
			
			$this->ImprimirDatosCliente($oCliente);
			
			$oFactura = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
			$NumeroFactura = $this->AbrirRecibo($oCliente, $oFactura->Prefijo . '-' . $oFactura->Numero);
			
			foreach ($arrItems as $oFacturaItem)
			{
				$this->ImprimirItem($oFacturaItem, $oCuponDescuento);
			}
			
			$this->ImprimirSubtotal();
			$this->ImprimirPercepciones($oFacturaPostVenta, $oCliente);
			$this->ImprimirTotal($oFacturaPostVenta);
			
			$this->CerrarRecibo();
			
			$oComprobante = new Comprobante();
			if ($oCliente->IdTipoIva == TipoIva::RI)
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
			else
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
			$oComprobante->Prefijo = '0003';
			$oComprobante->Numero = $NumeroFactura;
			$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
			$oComprobante->Fecha = date('d-m-Y');
			$oComprobante->Importe = $oFacturaPostVenta->ImporteBruto;
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');
			$oComprobante->PercepcionIIBB = number_format($oFacturaPostVenta->PercepcionIIBB, 2, '.', '');
			
			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oNotaCredito->Iva21 = number_format($this->Iva21, 2, '.', '');
			$oNotaCredito->Iva10 = number_format($this->Iva10, 2, '.', '');
			$oNotaCredito->Importe = number_format($oFacturaPostVenta->ImporteBruto, 2, '.', '');
			$oNotaCredito->PercepcionIIBB = number_format($oFacturaPostVenta->PercepcionIIBB, 2, '.', '');
			
			$oNotaCredito->IdComprobante = $oComprobante->IdComprobante;
			$oNotasCredito->Update($oNotaCredito);
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}	
		$this->WSpooler->if_close();		
	}
}

?>