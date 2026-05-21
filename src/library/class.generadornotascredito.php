<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorNotasCredito extends GeneradorDocumentos
{	
	protected function AbrirRecibo($oCliente, $Factura)
	{
		$comando = "@SetEmbarkNumber|1|" . $Factura . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);print_r($comando);
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenDNFH|R|T" . $this->Separador;
		else
			$comando = "@OpenDNFH|S|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);		
		
	}
	
	protected function ImprimirItem($Comentario, $Importe)
	{
		$oArticulos			= new Articulos();
		$oIvas				= new Ivas();
		
		$oIva = $oIvas->GetById(1);
		
		$comando = "@PrintLineItem|";		
		$comando .= substr($Comentario, 0, 50) . "|";
		$comando .= "1.0|";
		$comando .= number_format(($Importe / (1 + $oIva->Alicuota)), 2, '.', '') . "|";
		$comando .= number_format($oIva->Alicuota * 100, 2) . "|";
		$comando .= "M|0.0|0|B" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirSubtotal);
	}
	
	protected function ImprimirTotal($oNotaCredito)
	{
		$comando = "@TotalTender|Efectivo|" . $oNotaCredito->Importe . "|T|0" . $this->Separador;

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
	
	public function Imprimir($oNotaCredito)
	{
		$oNotaCredito->Importe = number_format($oNotaCredito->Importe, 2, '.', '');
		//print_r($oNotaCredito->Importe);exit;
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oNotasCredito	 	= new NotasCredito();
		$oClientes			= new Clientes();
		$oComprobantes		= new Comprobantes();
		$oNumber			= new Number();
		$oCompras			= new Compras();
		
		try
		{			
			if (!$oCliente = $oClientes->GetById($oNotaCredito->IdCliente))
				throw new Exception('Cliente no existente.');
				
			$oFactura = $oComprobantes->GetById($oNotaCredito->IdFactura);
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente, $oFactura->Prefijo . '-' . $oFactura->Numero);
			
			$this->ImprimirItem($oNotaCredito->Comentarios, $oNotaCredito->Importe);
						
			$this->ImprimirSubtotal();
			$this->ImprimirTotal($oNotaCredito);
			
			$NumeroFactura = $this->CerrarRecibo();
			
			$oComprobante = new Comprobante();
			if ($oCliente->IdTipoIva == TipoIva::RI)
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
			else
				$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
			$oComprobante->Prefijo = '0002';
			$oComprobante->Numero = $NumeroFactura;
			$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
			$oComprobante->Fecha = date('d-m-Y');
			$oComprobante->Importe = $oNotaCredito->Importe;
			$oComprobante->Importe = str_replace(',', '', $oComprobante->Importe);
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');
			
			$oComprobante = $oComprobantes->Create($oComprobante);
			
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