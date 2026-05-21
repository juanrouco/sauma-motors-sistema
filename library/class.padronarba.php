<?php
require_once('class.db.php');
require_once('class.dbaccess.php');

class PadronArba
{
	public $IdPadronArba;
	public $CUIL;
	public $Fecha;
	public $FechaDesde;
	public $FechaHasta;
	public $Percepcion;
	public $Retencion;
	public $IdCliente;
	
	public function __construct()
	{
		$this->IdPadronArba	= '';
		$this->CUIL			= '';
		$this->Fecha 		= '';
		$this->FechaDesde 	= '';
		$this->FechaHasta 	= '';
		$this->Percepcion 	= '';
		$this->Retencion 	= '';
		$this->IdCliente 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPadronArba = $arr['IdPadronArba'];
		$this->CUIL 		= stripslashes($arr['CUIL']);
		$this->Fecha	 	= $arr['Fecha'];
		$this->FechaDesde 	= $arr['FechaDesde'];
		$this->FechaHasta 	= $arr['FechaHasta'];
		$this->Percepcion 	= $arr['Percepcion'];
		$this->Retencion 	= $arr['Retencion'];
		$this->IdCliente 	= $arr['IdCliente'];
	}
}

?>