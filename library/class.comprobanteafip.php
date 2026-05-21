<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.comprobante.php');
require_once('class.comprobantesafipestados.php');
require_once('class.clientes.php');
require_once('class.tiposiva.php');

class ComprobanteAfip
{
	public $IdComprobanteAfip;
	public $IdComprobante;
	public $IdTipoComprobanteAfip;
	public $PuntoVenta;
	public $Numero;
	public $Cae;
	public $Fecha;
	public $IdConcepto;
	public $TipoDocumento;
	public $NumeroDocumento;
	public $Total;
	public $TotalNoGravado;
	public $TotalGravado;
	public $ImporteIva;
	public $ImporteIva10;
	public $ImporteIva21;
	public $ImportePercepcionIIBB;
	public $ImporteImpuestoInterno;
	public $ImporteImpuestos;
	public $ImporteExento;
	public $FechaVencimiento;
	public $IdComprobanteAsociado;
	public $IdEstado;
	public $VencimientoCae;
	public $CodigoTipoIva;
	
	public function __construct()
	{
		$this->IdComprobanteAfip 		= '';
		$this->IdComprobante 			= '';
		$this->IdTipoComprobanteAfip	= '';
		$this->PuntoVenta 				= '';
		$this->Numero 					= '';
		$this->Cae			 			= '';
		$this->Fecha		 			= '';
		$this->IdConcepto	 			= '';
		$this->TipoDocumento	 		= '';
		$this->NumeroDocumento	 		= '';
		$this->Total			 		= '';
		$this->TotalNoGravado 			= '';
		$this->TotalGravado 			= '';
		$this->ImporteIva10		 		= '';
		$this->ImporteIva21		 		= '';
		$this->ImporteIva		 		= '';
		$this->ImportePercepcionIIBB	= '';
		$this->ImporteImpuestoInterno	= '';
		$this->ImporteImpuestos	 		= '';
		$this->ImporteExento	 		= '';
		$this->FechaVencimiento	 		= '';
		$this->IdComprobanteAsociado	= '';
		$this->IdEstado 				= '';
		$this->VencimientoCae 			= '';
		$this->CodigoTipoIva			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdComprobanteAfip		= $arr['IdComprobanteAfip'];
		$this->IdComprobante 			= $arr['IdComprobante'];
		$this->IdTipoComprobanteAfip	= $arr['IdTipoComprobanteAfip'];
		$this->PuntoVenta 				= stripslashes($arr['PuntoVenta']);
		$this->Numero 					= stripslashes($arr['Numero']);
		$this->Cae			 			= $arr['Cae'];
		$this->Fecha		 			= $arr['Fecha'];
		$this->IdConcepto 				= $arr['IdConcepto'];
		$this->TipoDocumento 			= $arr['TipoDocumento'];
		$this->NumeroDocumento 			= $arr['NumeroDocumento'];
		$this->Total		 			= $arr['Total'];
		$this->TotalNoGravado			= $arr['TotalNoGravado'];
		$this->TotalGravado				= $arr['TotalGravado'];
		$this->ImporteIva10				= $arr['ImporteIva10'];
		$this->ImporteIva21				= $arr['ImporteIva21'];
		$this->ImporteIva				= $arr['ImporteIva'];
		$this->ImportePercepcionIIBB	= $arr['ImportePercepcionIIBB'];
		$this->ImporteImpuestoInterno	= $arr['ImporteImpuestoInterno'];
		$this->ImporteImpuestos			= $arr['ImporteImpuestos'];
		$this->ImporteExento			= $arr['ImporteExento'];
		$this->FechaVencimiento			= $arr['FechaVencimiento'];
		$this->IdComprobanteAsociado	= $arr['IdComprobanteAsociado'];
		$this->IdEstado 				= $arr['IdEstado'];
		$this->VencimientoCae 			= $arr['VencimientoCae'];
		$this->CodigoTipoIva		 	= $arr['CodigoTipoIva'];
	}
	
	
	public function CreateFromComprobante(Comprobante $oComprobante)
	{
		$oClientes = new Clientes();
		$oTiposIva = new TiposIva();
		$oCliente = $oClientes->GetById($oComprobante->IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
		
		$this->IdComprobante 			= $oComprobante->IdComprobante;
		$this->IdTipoComprobanteAfip	= $this->ObtenerComprobanteAfip($oComprobante->IdTipoComprobante);
		$this->PuntoVenta 				= $oComprobante->Prefijo;
		$this->Numero 					= $oComprobante->Numero;
		$this->Fecha		 			= date('Ymd');
		$this->IdConcepto 				= 1;
		
		$this->TipoDocumento 			= $oCliente->ObtenerTipoDocumentoAfip();
		$this->NumeroDocumento 			= $oCliente->ObtenerNumeroDocumentoAfip();
		
		$this->Total					= $oComprobante->Importe;
		$this->TotalGravado				= ($oComprobante->ImporteIva10 * 100 / 10.5) + ($oComprobante->ImporteIva21 * 100 / 21);
		$this->ImporteIva10				= $oComprobante->ImporteIva10;
		$this->ImporteIva21				= $oComprobante->ImporteIva21;
		$this->ImporteIva				= $oComprobante->ImporteIva10 + $oComprobante->ImporteIva21;
		$this->ImportePercepcionIIBB	= $oComprobante->PercepcionIIBB;
		$this->ImporteImpuestoInterno	= $oComprobante->ImpuestoInterno;
		$this->ImporteImpuestos			= $oComprobante->ImpuestoInterno + $oComprobante->PercepcionIIBB;
		$this->ImporteExento			= 0;
		$this->TotalNoGravado			= $oComprobante->Importe - $this->TotalGravado - $this->ImporteIva - $this->ImporteImpuestos - $this->ImporteExento;
		$this->TotalGravado				= number_format($this->TotalGravado, 2, '.', '');
		if ($this->TotalNoGravado < 0)
		{
			$this->Total -= $this->TotalNoGravado;
			$this->TotalNoGravado = 0;
		}
		$this->TotalNoGravado			= number_format($this->TotalNoGravado, 2, '.', '');
		$this->FechaVencimiento			= '';
		
		$this->IdComprobanteAsociado	= null;
		$this->IdEstado 				= ComprobantesAfipEstados::Pendiente;
		$this->CodigoTipoIva 			= $oTipoIva->CodigoAfip;
	}
	
	public function ObtenerComprobanteAfip($IdTipoComprobante)
	{
		switch($IdTipoComprobante)
		{
			case ComprobanteTipos::FacturaA:
				return ConstantesFacturaElectronica::FacturaA;
			case ComprobanteTipos::NotaDebitoA:
				return ConstantesFacturaElectronica::NotaDebitoA;
			case ComprobanteTipos::NotaCreditoA:
				return ConstantesFacturaElectronica::NotaCreditoA;
				
			case ComprobanteTipos::FacturaB:
				return ConstantesFacturaElectronica::FacturaB;
			case ComprobanteTipos::NotaDebitoB:
				return ConstantesFacturaElectronica::NotaDebitoB;
			case ComprobanteTipos::NotaCreditoB:
				return ConstantesFacturaElectronica::NotaCreditoB;
		}
	}
	
	public function GetComprobante()
	{
		$oComprobantes	= new Comprobantes();
		
		return $oComprobantes->GetById($this->IdComprobante);
	}
}

?>