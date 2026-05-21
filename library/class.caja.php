<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Caja extends DBAccess 
{
	public $IdCaja;
	public $FechaUltimoMovimiento;
	public $TotalRendir;
	public $TotalDeudas;
	public $TotalDetalles;
	
	
	public function __construct()
	{
		$this->IdCaja					= '';
		$this->FechaUltimoMovimiento	= '';	
		$this->TotalRendir				= '';
		$this->TotalDeudas				= '';
		$this->TotalDetalles			= '';
	}
		
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCaja					= $arr['IdCaja'];
		$this->FechaUltimoMovimiento	= $arr['FechaUltimoMovimiento'];
		$this->TotalRendir				= $arr['TotalRendir'];
		$this->TotalDeudas				= $arr['TotalDeudas'];
		$this->TotalDetalles			= $arr['TotalDetalles'];
	}
	
	
	public function GetAllDetalles()
	{
		$oCajasDetalles = new CajasDetalles();
		
		return $oCajasDetalles->GetAllByIdCaja($this->IdCaja);
	}		
}
?>