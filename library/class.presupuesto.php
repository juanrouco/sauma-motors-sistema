<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Presupuesto
{
	public $IdPresupuesto;
	public $IdModelo;
	public $IdColor;
	public $IdUsuario;
	public $IdCliente;
	public $Financia;
	public $FinanciacionCapital;
	public $FinanciacionCuotas;
	public $FinanciacionAcreedor;
	public $FinanciacionValorCuota;
	public $EntregaUsado;
	public $UsadoIdMarca;
	public $UsadoModelo;
	public $UsadoAnio;
	public $UsadoKm;
	public $UsadoPrecioTomado;
	public $Fecha;
	public $IdEstado;
	public $FechaVencimiento;
	public $IdMinuta;
	public $Precio;
	public $GastosFlete;
	public $GastosPatentamiento;
	public $GastosOtorgamiento;
	public $GastosPrenda;
	public $Circular;
	public $Anticipo;
	public $DepositoGarantia;
	public $Rentas;
	public $Observaciones;
	public $IdCausaPerdida;
	public $IdOrigenCliente;
	
	public function __construct()
	{
		$this->IdPresupuesto 			= '';
		$this->IdModelo					= '';
		$this->IdColor					= '';
		$this->IdUsuario 				= '';
		$this->IdCliente 				= '';
		$this->Financia					= '';
		$this->FinanciacionCapital 		= '';
		$this->FinanciacionCuotas		= '';
		$this->FinanciacionAcreedor		= '';
		$this->FinanciacionValorCuota	= '';
		$this->EntregaUsado		 		= '';
		$this->UsadoIdMarca 			= '';
		$this->UsadoModelo	 			= '';
		$this->UsadoAnio			 	= '';
		$this->UsadoKm		 			= '';
		$this->UsadoPrecioTomado 		= '';
		$this->Fecha	 				= '';
		$this->IdEstado					= '';
		$this->FechaVencimiento			= '';
		$this->IdMinuta					= '';
		$this->Precio					= '';
		$this->GastosFlete 				= '';
		$this->GastosPatentamiento 		= '';
		$this->GastosOtorgamiento 		= '';
		$this->GastosPrenda 			= '';
		$this->Circular 				= '';
		$this->Anticipo 				= '';
		$this->DepositoGarantia			= '';
		$this->Rentas					= '';
		$this->Observaciones			= '';
		$this->IdCausaPerdida			= '';
		$this->IdOrigenCliente			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPresupuesto 			= $arr['IdPresupuesto'];
		$this->IdModelo					= $arr['IdModelo'];
		$this->IdColor					= $arr['IdColor'];
		$this->IdUsuario 				= $arr['IdUsuario'];
		$this->IdCliente 				= $arr['IdCliente'];
		$this->Financia		 			= ((ord($arr['Financia']) == 1) || ($arr['Financia'] == 1));
		$this->FinanciacionCapital		= $arr['FinanciacionCapital'];
		$this->FinanciacionCuotas		= $arr['FinanciacionCuotas'];
		$this->FinanciacionAcreedor 	= $arr['FinanciacionAcreedor'];
		$this->FinanciacionValorCuota	= $arr['FinanciacionValorCuota'];
		$this->EntregaUsado 			= ((ord($arr['EntregaUsado']) == 1) || ($arr['EntregaUsado'] == 1));
		$this->UsadoIdMarca			 	= $arr['UsadoIdMarca'];
		$this->UsadoModelo			 	= $arr['UsadoModelo'];
		$this->UsadoAnio		 		= $arr['UsadoAnio'];
		$this->UsadoKm					= $arr['UsadoKm'];
		$this->UsadoPrecioTomado		= $arr['UsadoPrecioTomado'];
		$this->Fecha		 			= $arr['Fecha'];
		$this->IdEstado				 	= $arr['IdEstado'];
		$this->FechaVencimiento			= $arr['FechaVencimiento'];
		$this->IdMinuta					= $arr['IdMinuta'];
		$this->Precio					= $arr['Precio'];
		$this->GastosFlete 				= $arr['GastosFlete'];
		$this->GastosPatentamiento 		= $arr['GastosPatentamiento'];
		$this->GastosOtorgamiento 		= $arr['GastosOtorgamiento'];
		$this->GastosPrenda 			= $arr['GastosPrenda'];
		$this->Circular 				= $arr['Circular'];
		$this->Anticipo 				= $arr['Anticipo'];
		$this->DepositoGarantia			= $arr['DepositoGarantia'];
		$this->Rentas					= $arr['Rentas'];
		$this->Observaciones			= $arr['Observaciones'];
		$this->IdCausaPerdida			= $arr['IdCausaPerdida'];
		$this->IdOrigenCliente			= $arr['IdOrigenCliente'];
	}
	
	public function CanDelete()
	{
		return true;
	}
}

?>