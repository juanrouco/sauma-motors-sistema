<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorFacturaFranquicias extends GeneradorDocumentos
{
	protected function AbrirRecibo($oCliente)
	{
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$comando = "@OpenFiscalReceipt|A|T" . $this->Separador;
		else
			$comando = "@OpenFiscalReceipt|B|T" . $this->Separador;

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
		$comando = "@TotalTender|Efectivo|" . $this->OrdenTrabajo->ImporteTotal() . "|T|0" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirTotal);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseFiscalReceipt" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);

		return $this->WSpooler->if_read(3);
	}
	
	protected function Cancelar()
	{
		$comando = "@Cancel|" . chr(152) . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	public function Imprimir($oOrdenTrabajo, $IdOrdenTrabajoFranquicia)
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
			if ($oOrdenTrabajoFranquicia->IdComprobante)
			{
				if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oOrdenTrabajoFranquicia->IdComprobante))
					throw new Exception('Franquicia anulada.');
			}
			
			if (!$oCliente = $oClientes->GetById($oOrdenTrabajoFranquicia->IdCliente))
				throw new Exception('Cliente no existente.');
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente);
			
			$this->ImprimirFranquicia($oOrdenTrabajoFranquicia);
			
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
			$oComprobante->Importe = $oOrdenTrabajoFranquicia->Importe;
			$oComprobante->Importe = str_replace(',', '', $oComprobante->Importe);
			//$oComprobante->Importe = str_replace('.', '', $oComprobante->Importe);
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');

			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oOrdenTrabajoFranquicia->IdComprobante = $oComprobante->IdComprobante;
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