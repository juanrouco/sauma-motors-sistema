<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PagoMayorista extends DBAccess 
{
	public $IdPagoMayorista;
	public $IdPedidoMayorista;
	public $Fecha;
	public $NumeroCheque;
	public $BancoDesde;
	public $BancoDestino;
	public $Cliente;
	public $FechaEmision;
	public $FechaDeposito;
	public $Importe;
	public $IdTipoPago;
	public $Observaciones;
	public $ImporteAsignado;
	
	public function __construct()
	{
		$this->IdPagoMayorista		= '';
		$this->IdPedidoMayorista 	= '';
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
		$this->ImporteAsignado 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPagoMayorista		= $arr['IdPagoMayorista'];
		$this->IdPedidoMayorista 	= $arr['IdPedidoMayorista'];
		$this->Fecha				= $arr['Fecha'];
		$this->NumeroCheque			= $arr['NumeroCheque'];
		$this->BancoDesde			= $arr['BancoDesde'];
		$this->BancoDestino 		= $arr['BancoDestino'];
		$this->Cliente 				= $arr['Cliente'];
		$this->FechaEmision 		= $arr['FechaEmision'];
		$this->FechaDeposito		= $arr['FechaDeposito'];
		$this->Importe 				= $arr['Importe'];
		$this->IdTipoPago 			= $arr['IdTipoPago'];
		$this->Observaciones 		= $arr['Observaciones'];
		$this->ImporteAsignado 		= $arr['ImporteAsignado'];
	}
}
?>
