<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class AcreedorCuota
{
	public $IdAcreedorCuota;
	public $IdAcreedor;
	public $Cuotas;
	public $Interes;
	public $Coeficiente;
	public $Disponible;
	
	public function __construct()
	{
		$this->IdAcreedorCuota	= '';
		$this->IdAcreedor		= '';
		$this->Cuotas 			= '';
		$this->Interes 			= '';
		$this->Coeficiente 		= '';
		$this->Disponible 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdAcreedorCuota	= $arr['IdAcreedorCuota'];
		$this->IdAcreedor		= $arr['IdAcreedor'];
		$this->Cuotas 			= $arr['Cuotas'];
		$this->Interes			= $arr['Interes'];
		$this->Coeficiente		= $arr['Coeficiente'];
		$this->Disponible		= $arr['Disponible'];
	}
}

?>