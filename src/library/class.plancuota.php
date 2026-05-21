<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PlanCuota
{
	public $IdPlanCuota;
	public $IdFormaPago;
	public $Nombre;
	public $Interes;
	public $Coeficiente;
	public $Disponible;
	
	public function __construct()
	{
		$this->IdPlanCuota 	= '';
		$this->IdFormaPago	= '';
		$this->Nombre 		= '';
		$this->Interes 		= '';
		$this->Coeficiente 	= '';
		$this->Disponible 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPlanCuota 	= $arr['IdPlanCuota'];
		$this->IdFormaPago	= $arr['IdFormaPago'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
		$this->Interes		= $arr['Interes'];
		$this->Coeficiente	= $arr['Coeficiente'];
		$this->Disponible	= $arr['Disponible'];
	}
}

?>