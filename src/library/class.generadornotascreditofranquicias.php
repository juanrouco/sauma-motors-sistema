<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorNotasCreditoFranquicias extends GeneradorDocumentos
{
	protected function AbrirRecibo($oCliente, $Factura)
	{
		$comando = "@SetEmbarkNumber|1|" . $Factura . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
		
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenDNFH|R|T" . $this->Separador;
		else
			$comando = "@OpenDNFH|S|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);
		
	}
	
	private function ImprimirFranquicia($oOrdenTrabajoFranquicia)
	{
		$oIvas							= new Ivas();
		
		$oIva = $oIvas->GetById(1);
		
		$comando = "@PrintLineItem|";		
		$comando .= substr($oOrdenTrabajoFranquicia->Descripcion, 0, 50) . "|";
		$comando .= "1.0|";
		$comando .= str_replace(',', '', number_format(($oOrdenTrabajoFranquicia->Importe / (1 + $oIva->Alicuota)), 2)) . "|";
		$comando .= number_format($oIva->Alicuota * 100, 2) . "|";
		$comando .= "M|0.0|0|B" . $this->Separador;
		
		$this->Iva21 += ($oOrdenTrabajoFranquicia->Importe / (1 + $oIva->Alicuota)) * $oIva->Alicuota;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirSubtotal);
	}
	
	protected function ImprimirTotal()
	{
		$comando = "@TotalTender|Efectivo|" . $this->Factura->Importe . "|T|0" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirTotal);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseDNFH" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);

		return $this->WSpooler->if_read(3);
	}
	
	protected function Cancelar()
	{
		$comando = "@Cancel|" . chr(152) . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	public function Imprimir($oOrdenTrabajo, $IdOrdenTrabajoFranquicia, $oNotaCredito)
	{
	
		$this->OrdenTrabajo = $oOrdenTrabajo;
		
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oOrdenesTrabajoFranquicias		= new OrdenesTrabajoFranquicias();
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
			if (!$oOrdenTrabajoFranquicia = $oOrdenesTrabajoFranquicias->GetById($IdOrdenTrabajoFranquicia))
				throw new Exception('Franquicia no existente.');				
			if ($oOrdenTrabajoFranquicia->Anulado)
				throw new Exception('Franquicia anulada.');
			
			if (!$oCliente = $oClientes->GetById($oOrdenTrabajoFranquicia->IdCliente))
				throw new Exception('Cliente no existente.');
				
			if (!$oFactura = $oComprobantes->GetById($oOrdenTrabajoFranquicia->IdComprobante))
				throw new Exception('Factura no existente.');
			
			$this->Factura = $oFactura;
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente, $oFactura->Prefijo . '-' . $oFactura->Numero);
			
			$this->ImprimirFranquicia($oOrdenTrabajoFranquicia);
			
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
			$oComprobante->Importe = $oOrdenTrabajoFranquicia->Importe;
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
			
			$oOrdenTrabajoFranquicia->Anulado = '1';
			$oOrdenesTrabajoFranquicias->Update($oOrdenTrabajoFranquicia);
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}
		$this->WSpooler->if_close();
	}
}

?>