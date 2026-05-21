<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Pago extends DBAccess 
{
	public $IdPago;
	public $IdMinuta;
	public $Fecha;
	public $NumeroCheque;
	public $BancoDesde;
	public $BancoDestino;
	public $IdCajaDetalle;
	public $Cliente;
	public $FechaEmision;
	public $FechaDeposito;
	public $Importe;
	public $IdTipoPago;
	public $Observaciones;
	public $IdPagoMayorista;
	public $IdMinutaUsado;
	public $Pago;
	public $NumeroRecibo;
	public $IdAcreedor;
	public $Cuotas;
	public $IdFacturaPostVenta;
	
	public function __construct()
	{
		$this->IdPago				= '';
		$this->IdMinuta 			= '';
		$this->Fecha				= '';
		$this->NumeroCheque			= '';
		$this->BancoDesde			= '';
		$this->BancoDestino 		= '';
		$this->Cliente 				= '';
		$this->FechaEmision 		= '';
		$this->FechaDeposito		= '';
		$this->Importe 				= '';
		$this->IdTipoPago 			= '';
		$this->Observaciones 		= '';
		$this->IdPagoMayorista 		= '';
		$this->IdMinutaUsado 		= '';
		$this->Pago			 		= '';
		$this->NumeroRecibo	 		= '';
		$this->IdAcreedor	 		= '';
		$this->Cuotas		 		= '';
		$this->IdFacturaPostVenta	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPago				= $arr['IdPago'];
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->Fecha				= $arr['Fecha'];
		$this->NumeroCheque			= $arr['NumeroCheque'];
		$this->BancoDesde			= $arr['BancoDesde'];
		$this->BancoDestino 		= $arr['BancoDestino'];
		$this->IdCajaDetalle 		= $arr['IdCajaDetalle'];
		$this->Cliente 				= $arr['Cliente'];
		$this->FechaEmision 		= $arr['FechaEmision'];
		$this->FechaDeposito		= $arr['FechaDeposito'];
		$this->Importe 				= $arr['Importe'];
		$this->IdTipoPago 			= $arr['IdTipoPago'];
		$this->Observaciones 		= $arr['Observaciones'];
		$this->IdPagoMayorista 		= $arr['IdPagoMayorista'];
		$this->IdMinutaUsado 		= $arr['IdMinutaUsado'];
		$this->Pago			 		= $arr['Pago'];
		$this->NumeroRecibo	 		= $arr['NumeroRecibo'];
		$this->IdAcreedor	 		= $arr['IdAcreedor'];
		$this->Cuotas		 		= $arr['Cuotas'];
		$this->IdFacturaPostVenta	= $arr['IdFacturaPostVenta'];
	}
}
?>
