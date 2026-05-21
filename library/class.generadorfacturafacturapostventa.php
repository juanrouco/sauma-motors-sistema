<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorFacturaFacturaPostVenta extends GeneradorDocumentos
{
	private $FacturaPostVenta;
	
	protected function AbrirRecibo($oCliente)
	{
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenFiscalReceipt|A|T" . $this->Separador;
		else
			$comando = "@OpenFiscalReceipt|B|T" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);	
		return $this->WSpooler->if_read(3);	
	}
	
	protected function ImprimirItem($oFacturaItem)
	{
		$oIvas				= new Ivas();
		
		$oIva = $oIvas->GetById($oFacturaItem->IdIva);
		if ($oFacturaItem->Cantidad % 0.1 != 0)
			$oFacturaItem->Cantidad -= $oFacturaItem->Cantidad % 0.1;
		$ImporteUnidad = $oFacturaItem->ImporteNeto / $oFacturaItem->Cantidad;
		
		
		if ($ImporteUnidad > 0)
		{
		//print_r(sanear_string($oFacturaItem->Descripcion);
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
		$comando = "@CloseFiscalReceipt" . $this->Separador;
		
		try
		{
			$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}	
		return $this->WSpooler->if_read(3);
	}
	
	protected function ImprimirPercepciones($oFacturaPostVenta, $oCliente)
	{
		if ($oFacturaPostVenta->PercepcionIIBB && $oCliente->PercepcionIIBB)
		{
			$comando = "@Perceptions|**.**|Percep. IIBB Bs. As.|" . $oFacturaPostVenta->PercepcionIIBB . $this->Separador;

			$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirPercepciones);
		}
	}
	
	protected function ImprimirTexto($oFacturaPostVenta)
	{	
		$Comentarios = wordwrap($oFacturaPostVenta->Comentarios, 30, '\n');
		$arrComentarios = explode('\n', $Comentarios);
		foreach ($arrComentarios as $Comentario)
		{
			$comando = "@PrintFiscalText|" . $Comentario . '|0' . $this->Separador;

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
		
		print_r($this->Ret);
		if ($this->Ret == -1)
		{
			print_r('Error de conexion');
			exit;
		}
		$oClientes					= new Clientes();
		$oComprobantes				= new Comprobantes();
		$oNumber					= new Number();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oOrdenesTrabajoFranquicias = new OrdenesTrabajoFranquicias();
		
		try
		{
			$arrItems = $oFacturaPostVenta->GetAllItems();
		
			if (!$oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente))
				throw new Exception('Cliente no existente.');
			
			$this->ImprimirDatosCliente($oCliente);
			$NumeroFactura = $this->AbrirRecibo($oCliente);
			
			foreach ($arrItems as $oFacturaItem)
			{
				$this->ImprimirItem($oFacturaItem, $oCuponDescuento);
			}
			
			if ($oFacturaPostVenta->Comentario && $oFacturaPostVenta->Comentario != '')
				$this->ImprimirTexto($oFacturaPostVenta);
			
			$this->ImprimirSubtotal();
			$this->ImprimirPercepciones($oFacturaPostVenta, $oCliente);
			$this->ImprimirTotal();
			
			$this->CerrarRecibo();
			
			$oComprobante = new Comprobante();
			if ($oCliente->IdTipoIva == TipoIva::RI)
				$oComprobante->IdTipoComprobante = ComprobanteTipos::FacturaA;
			else
				$oComprobante->IdTipoComprobante = ComprobanteTipos::FacturaB;
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
			
			$oFacturaPostVenta->IdComprobante = $oComprobante->IdComprobante;
			$oFacturaPostVenta->NumeroFactura = $oComprobante->Prefijo . '-' . $oComprobante->Numero;
			$oFacturasPostVentas->Update($oFacturaPostVenta);
			
			if ($oFranquicia = $oOrdenesTrabajoFranquicias->GetByIdFactura($oFacturaPostVenta->IdFactura))
			{
				$oFranquicia->IdComprobante = $oFacturaPostVenta->IdComprobante;
				$oOrdenesTrabajoFranquicias->Update($oFranquicia);
			}
		}
		catch(Exception $ex)
		{
			$this->WSpooler->if_close();
			$this->CancelarPorError($ex->getMessage());
		}	
		$this->WSpooler->if_close();//exit;		
	}
}

?>