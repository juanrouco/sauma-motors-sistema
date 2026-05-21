<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Prenda
{
	public $IdPrenda;
	public $IdGestoria;
	public $IdAcreedor;
	public $FinanciacionCapital;
	public $CantidadCuotas;
	public $ImporteCuota;
	public $FechaVencimientoPrimerCuota;
	public $TasaNominal;
	public $TasaEfectiva;
	public $CostoFinancieroTotal;
	public $Observaciones;


	public function __construct()
	{
		$this->IdPrenda 					= '';
		$this->IdGestoria 					= '';
		$this->IdAcreedor 					= '';
		$this->FinanciacionCapital 			= '';
		$this->CantidadCuotas 				= '';
		$this->ImporteCuota 				= '';
		$this->FechaVencimientoPrimerCuota 	= '';
		$this->TasaNominal 					= '';
		$this->TasaEfectiva 				= '';
		$this->CostoFinancieroTotal 		= '';
		$this->Observaciones 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPrenda 					= $arr['IdPrenda'];
		$this->IdGestoria 					= $arr['IdGestoria'];
		$this->IdAcreedor 					= $arr['IdAcreedor'];
		$this->FinanciacionCapital 			= $arr['FinanciacionCapital'];
		$this->CantidadCuotas 				= $arr['CantidadCuotas'];
		$this->ImporteCuota 				= $arr['ImporteCuota'];
		$this->FechaVencimientoPrimerCuota 	= $arr['FechaVencimientoPrimerCuota'];
		$this->TasaNominal 					= $arr['TasaNominal'];
		$this->TasaEfectiva 				= $arr['TasaEfectiva'];
		$this->CostoFinancieroTotal 		= $arr['CostoFinancieroTotal'];
		$this->Observaciones 				= $arr['Observaciones'];
	}
	
	
	public function GetAllFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByPrenda($this);
	}


	public function GetAllConyuges()
	{
		$PrendaConyuges = new PrendaConyuges();
		
		return $PrendaConyuges->GetAllByPrenda($this);
	}
}

?>