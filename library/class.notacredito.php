<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.notascreditodetalles.php');
require_once('class.notascredito.php');
require_once('class.ifacturaelectronica.php');
require_once('class.comprobantes.php');
require_once('class.comprobantesafip.php');

class NotaCredito implements IFacturaElectronica
{
	public $IdNotaCredito;
	public $IdComprobante;
	public $IdCliente;
	public $Importe;
	public $Comentarios;
	public $Fecha;
	public $IdFactura;
	public $Iva10;
	public $Iva21;
	public $ImpuestoInterno;
	public $IdMinuta;
	public $Subtotal;
	public $PercepcionIIBB;
	
	public function __construct()
	{
		$this->IdNotaCredito 		= '';
		$this->IdComprobante		= '';
		$this->IdCliente 			= '';
		$this->Importe 				= '';
		$this->Comentarios 			= '';
		$this->Fecha		 		= '';
		$this->IdFactura	 		= '';
		$this->Iva10		 		= '';
		$this->Iva21		 		= '';
		$this->ImpuestoInterno		= '';
		$this->IdMinuta		 		= '';
		$this->Subtotal		 		= '';
		$this->PercepcionIIBB 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdNotaCredito 		= $arr['IdNotaCredito'];
		$this->IdComprobante		= $arr['IdComprobante'];
		$this->IdCliente 			= $arr['IdCliente'];		
		$this->Importe	 			= $arr['Importe'];
		$this->Comentarios 			= stripslashes($arr['Comentarios']);
		$this->Fecha		 		= $arr['Fecha'];
		$this->IdFactura	 		= $arr['IdFactura'];
		$this->Iva10		 		= $arr['Iva10'];
		$this->Iva21		 		= $arr['Iva21'];
		$this->ImpuestoInterno		= $arr['ImpuestoInterno'];
		$this->IdMinuta		 		= $arr['IdMinuta'];
		$this->Subtotal		 		= $arr['Subtotal'];
		$this->PercepcionIIBB 		= $arr['PercepcionIIBB'];
	}
	
	public function GetAllDetalles()
	{
		$NotasCreditoDetalles = new NotasCreditoDetalles();
		
		return $NotasCreditoDetalles->GetAllByNotaCredito($this);
	}
	
	public function SetNumeroComprobante($NumeroComprobante)
	{
		//$this->NumeroComprobante = $NumeroComprobante;
	}
	
	public function SetFechaComprobante($FechaComprobante)
	{
		$this->Fecha = $FechaComprobante;
	}
	
	public function ActualizarFactura()
	{
		$oNotasCredito = new NotasCredito();
		$oNotasCredito->Update($this);
	}
	
	public function ObtenerComprobante()
	{
		$oComprobantes = new Comprobantes();
		return $oComprobantes->GetById($this->IdComprobante);
	}
	
	public function ObtenerComprobanteAfipAsociado()
	{
		if (!$this->IdFactura)
			return false;
		
		$oComprobantesAfip = new ComprobantesAfip();
		return $oComprobantesAfip->GetByIdComprobante($this->IdFactura);
	}
}

?>