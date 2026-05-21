<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PlanillaRecepcion
{
	public $IdPlanillaRecepcion;
	public $NumeroCartaPorte;
	public $FechaRecepcion;
	public $Observaciones;
	public $IdEstado;
	
	
	public function __construct()
	{
		$this->IdPlanillaRecepcion 	= '';
		$this->NumeroCartaPorte		= '';
		$this->FechaRecepcion 		= '';
		$this->Observaciones 		= '';
		$this->IdEstado 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPlanillaRecepcion 	= $arr['IdPlanillaRecepcion'];
		$this->NumeroCartaPorte		= $arr['NumeroCartaPorte'];
		$this->FechaRecepcion 		= $arr['FechaRecepcion'];
		$this->Observaciones 		= $arr['Observaciones'];
		$this->IdEstado 			= $arr['IdEstado'];
	}
	

	public function GetCountUnidades()
	{
		return count($this->GetAllUnidades());
	}

	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByPlanillaRecepcion($this);
	}
}

?>