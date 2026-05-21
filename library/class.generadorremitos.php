<?php
require_once("WSpooler/WSpooler.php");
require_once("class.generadordocumentos.php");

class GeneradorRemitos extends GeneradorDocumentos
{
	protected function AbrirRecibo($oCliente, $Factura)
	{
		/*$comando = "@SetEmbarkNumber|1|" . $Factura . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);print_r($comando);*/
		$comando = "@OpenDNFH|r|T|" . $Factura . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::AbrirRecibo);
	}
	
	protected function ImprimirItem($Comentario, $Cantidad)
	{
		$oArticulos			= new Articulos();
		$oIvas				= new Ivas();
		
		$oIva = $oIvas->GetById(1);
		
		$comando = "@PrintEmbarkItem|";		
		$comando .= substr($Comentario, 0, 50) . "|";
		$comando .= $Cantidad . ".0|";
		$comando .= "1" . $this->Separador;

		$this->ProcesarComando($comando, GeneradorDocumentos::ImprimirItem);
	}
	
	protected function CerrarRecibo()
	{
		$comando = "@CloseDNFH" . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::CerrarRecibo);
		return $this->WSpooler->if_read(3);
	}
		
	
	public function Imprimir($oCompra)
	{
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		$oClientes			= new Clientes();
		$oComprobantes		= new Comprobantes();
		$oNumber			= new Number();
		$oCompras			= new Compras();
		$oArticulos			= new Articulos();
		$oStockMovimientos	= new StockMovimientos();
		$oOrdenesTrabajo 	= new OrdenesTrabajo();
		$oTallerUnidades	= new TallerUnidades();
		
		try
		{
			$NumeroFactura = '0000';
			if ($oCompra->IdCliente)
			{
				if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
					throw new Exception('Error: Cliente no existente.');
				if (!$oFactura = $oComprobantes->GetById($oCompra->IdFactura))
					throw new Exception('Error: Factura no existente.');
					
				$NumeroFactura = $oFactura->Prefijo . '-' . $oFactura->Numero;
			}
			else
			{
				if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oCompra->IdOrdenTrabajo))
					throw new Exception('Error: OT no existente.');
					
				if (!$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad))
					throw new Exception('Error: Taller unidad no existente.');
					
				if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
					throw new Exception('Error: Cliente no existente.');
			}
			
			$this->ImprimirDatosCliente($oCliente);
			$this->AbrirRecibo($oCliente, $NumeroFactura);
			
			$oCompra->LoadAllDetalles();
			foreach ($oCompra->CompraDetalles as $oCompraDetalle)
			{
				$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
				$this->ImprimirItem($oArticulo->Descripcion, $oCompraDetalle->Cantidad);
			}			
			
						
			$NumeroRemito = $this->CerrarRecibo();
			
			$oComprobante = new Comprobante();
			$oComprobante->IdTipoComprobante = ComprobanteTipos::Remito;
			$oComprobante->Prefijo = '0002';
			$oComprobante->Numero = $NumeroRemito;
			$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
			$oComprobante->Fecha = date('d/m/Y');
			$oComprobante->IdCliente = $oCliente->IdCliente;
			
			$oComprobante = $oComprobantes->Create($oComprobante);
			
			$oCompra->IdRemito = $oComprobante->IdComprobante;
			$oCompras->Update($oCompra);
			
			$arrStockMovimientos = $oStockMovimientos->GetAllByCompra($oCompra);
			foreach ($arrStockMovimientos as $oStockMovimiento)
			{
				$oStockMovimiento->Remito = $oComprobante->Numero;
				$oStockMovimientos->Update($oStockMovimiento);
			}
		}
		catch(Exception $ex)
		{
			$this->CancelarPorError($ex->getMessage());
		}	
		$this->WSpooler->if_close();
	}
}

?>