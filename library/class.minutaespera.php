<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class MinutaEspera
{
	public $IdMinutaEspera;
	public $IdUsuario;
	public $IdCliente;
	public $IdModelo;
	public $IdColor;
	public $IdColor2;
	public $IdColor3;
	public $NumeroPedido;
	public $NumeroVin;
	public $IdEstado;
	public $FechaMinuta;
	public $Anticipo;
	public $Reportado;
	public $Financia;
	public $FinanciacionCapital;
	public $FinanciacionCuotas;
	public $IdAcreedor;
	public $FinanciacionValorCuota;
	public $EntregaUsado;
	public $UsadoIdMarca;
	public $UsadoModelo;
	public $UsadoAnio;
	public $UsadoKm;
	public $UsadoDominio;
	public $UsadoPrecioTomado;
	public $IdMinuta;
	public $Precio;
	public $GastosFlete;
	public $GastosPatentamiento;
	public $GastosOtorgamiento;
	public $GastosPrenda;
	public $Circular;
	public $DepositoGarantia;
	public $Rentas;
	public $Observaciones;
	
	public function __construct()
	{
		$this->IdMinutaEspera			= '';
		$this->IdUsuario 				= '';
		$this->IdCliente 				= '';
		$this->IdModelo					= '';
		$this->IdColor	 				= '';
		$this->IdColor2	 				= '';
		$this->IdColor3	 				= '';
		$this->NumeroPedido				= '';
		$this->NumeroVin 				= '';
		$this->IdEstado 				= '';
		$this->FechaMinuta 				= '';
		$this->Anticipo 				= '';
		$this->Reportado				= '';
		
		$this->Financia					= '';
		$this->FinanciacionCapital		= '';
		$this->FinanciacionCuotas		= '';
		$this->IdAcreedor				= '';
		$this->FinanciacionValorCuota	= '';
		$this->EntregaUsado				= '';
		$this->UsadoIdMarca				= '';
		$this->UsadoModelo				= '';
		$this->UsadoAnio				= '';
		$this->UsadoKm					= '';
		$this->UsadoDominio				= '';
		$this->UsadoPrecioTomado		= '';
		$this->IdMinuta					= '';
		$this->Precio					= '';
		$this->GastosFlete				= '';
		$this->GastosPatentamiento		= '';
		$this->GastosOtorgamiento		= '';
		$this->GastosPrenda				= '';
		$this->Circular					= '';
		$this->DepositoGarantia			= '';
		$this->Rentas					= '';
		$this->Observaciones			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinutaEspera			= $arr['IdMinutaEspera'];
		$this->IdUsuario 				= $arr['IdUsuario'];
		$this->IdCliente 				= $arr['IdCliente'];
		$this->IdModelo					= $arr['IdModelo'];
		$this->IdColor					= $arr['IdColor'];
		$this->IdColor2					= $arr['IdColor2'];
		$this->IdColor3					= $arr['IdColor3'];
		$this->FechaMinuta 				= $arr['FechaMinuta'];
		$this->NumeroPedido				= $arr['NumeroPedido'];
		$this->NumeroVin 				= $arr['NumeroVin'];
		$this->IdEstado			 		= $arr['IdEstado'];
		$this->Anticipo 				= $arr['Anticipo'];
		$this->Reportado				= $arr['Reportado'];
		
		$this->Financia					= ((ord($arr['Financia']) == 1) || ($arr['Financia'] == 1));
		$this->FinanciacionCapital		= $arr['FinanciacionCapital'];
		$this->FinanciacionCuotas		= $arr['FinanciacionCuotas'];
		$this->IdAcreedor				= $arr['IdAcreedor'];
		$this->FinanciacionValorCuota	= $arr['FinanciacionValorCuota'];
		$this->EntregaUsado				= ((ord($arr['EntregaUsado']) == 1) || ($arr['EntregaUsado'] == 1));
		$this->UsadoIdMarca				= $arr['UsadoIdMarca'];
		$this->UsadoModelo				= $arr['UsadoModelo'];
		$this->UsadoAnio				= $arr['UsadoAnio'];
		$this->UsadoKm					= $arr['UsadoKm'];
		$this->UsadoDominio				= $arr['UsadoDominio'];
		$this->UsadoPrecioTomado		= $arr['UsadoPrecioTomado'];
		$this->IdMinuta					= $arr['IdMinuta'];
		$this->Precio					= $arr['Precio'];
		$this->GastosFlete				= $arr['GastosFlete'];
		$this->GastosPatentamiento		= $arr['GastosPatentamiento'];
		$this->GastosOtorgamiento		= $arr['GastosOtorgamiento'];
		$this->GastosPrenda				= $arr['GastosPrenda'];
		$this->Circular					= $arr['Circular'];
		$this->DepositoGarantia			= $arr['DepositoGarantia'];
		$this->Rentas					= $arr['Rentas'];
		$this->Observaciones			= $arr['Observaciones'];
	}
}

?>