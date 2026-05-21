<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Remito extends DBAccess 
{
	public $IdRemito;
	public $IdMinuta;
	public $IdComprobante;
	public $NumeroComprobante;
	public $Fecha;
	public $Transporte;
	public $TransporteClaveFiscalTipo;
	public $TransporteClaveFiscalNumero;
	
	public function __construct()
	{
		$this->IdRemito						= '';
		$this->IdMinuta 					= '';
		$this->IdComprobante				= '';
		$this->NumeroComprobante			= '';
		$this->Fecha 						= '';
		$this->Transporte 					= '';
		$this->TransporteClaveFiscalTipo 	= '';
		$this->TransporteClaveFiscalNumero 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdRemito						= $arr['IdRemito'];
		$this->IdMinuta 					= $arr['IdMinuta'];
		$this->IdComprobante				= $arr['IdComprobante'];
		$this->NumeroComprobante			= $arr['NumeroComprobante'];
		$this->Fecha 						= $arr['Fecha'];
		$this->Transporte 					= $arr['Transporte'];
		$this->TransporteClaveFiscalTipo 	= $arr['TransporteClaveFiscalTipo'];
		$this->TransporteClaveFiscalNumero 	= $arr['TransporteClaveFiscalNumero'];
	}
}
?>
