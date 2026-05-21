<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorFactura extends GeneradorDocumentos
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
		$comando .= substr($oArticulo->Descripcion, 0, 50) . "|";
		$comando .= $oCompraDetalle->Cantidad . ".0|";
		$comando .= number_format(($oCompraDetalle->ImporteUnidad / (1 + $oIva->Alicuota)), 2, '.', '') . "|";
		$comando .= number_format($oIva->Alicuota * 100, 2) . "|";
		$comando .= "M|0.0|0|B" . $this->Separador;

		if ($oIva->IdIva == Iva::Iva21)
			$this->Iva21 += ($oCompraDetalle->ImporteCompraNeto / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
		else
			$this->Iva10 += ($oCompraDetalle->ImporteCompraNeto / (1 + $oIva->Alicuota)) * $oIva->Alicuota;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function ImprimirSubtotal()
	{
		$comando = "@Subtotal|P|Subtotal|0|" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirSubtotal);
	}
	
	protected function ImprimirTotal()
	{
		$comando = "@TotalTender|Efectivo|" . $this->Compra->Total() . "|T|0" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirTotal);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseFiscalReceipt" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);

		return $this->WSpooler->if_read(3);
	}
	
	protected function ImprimirDescuento($oCompra, $oCuponDescuento)
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
	
	public function Imprimir($oCompra)
	{
		$this->Compra = $oCompra;
		
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oCuponesDescuento 	= new CuponesDescuento();
		$oClientes			= new Clientes();
		$oComprobantes		= new Comprobantes();
		$oNumber			= new Number();
		$oCompras			= new Compras();
		
		try
		{
			$oCompra->LoadAllDetalles();
		
			if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
				throw new Exception('Cliente no existente.');
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente);
			
			foreach ($oCompra->CompraDetalles as $oCompraDetalle)
			{
				$this->ImprimirItem($oCompraDetalle);
			}
			
			if ($oCompra->IdCuponDescuento)
			{
				$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
				$this->ImprimirDescuento($oCompra, $oCuponDescuento);
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
			$oComprobante->Importe = $oCompra->Total();
			$oComprobante->IdCliente = $oCliente->IdCliente;
			$oComprobante->ImporteIva21 = number_format($this->Iva21, 2, '.', '');
			$oComprobante->ImporteIva10 = number_format($this->Iva10, 2, '.', '');
			
			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oCompra->IdFactura = $oComprobante->IdComprobante;
			$oCompras->Update($oCompra);
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}	
		$this->WSpooler->if_close();		
	}
}

?>