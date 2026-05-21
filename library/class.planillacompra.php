<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PlanillaCompra
{
	public $IdPlanillaCompra;
	public $FechaCarga;
	
	
	public function __construct()
	{
		$this->IdPlanillaCompra = '';
		$this->FechaCarga 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPlanillaCompra = $arr['IdPlanillaCompra'];
		$this->FechaCarga 		= $arr['FechaCarga'];
	}
	

	public function GetCountUnidades()
	{
		return count($this->GetAllUnidades());
	}

	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByPlanillaCompra($this);
	}
}

?>