<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ReporteFacturacion
{
	public $IdReporteFacturacion;
	public $FechaReporte;
	
	
	public function __construct()
	{
		$this->IdReporteFacturacion = '';
		$this->FechaReporte			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdReporteFacturacion = $arr['IdReporteFacturacion'];
		$this->FechaReporte			= $arr['FechaReporte'];
	}
	
	
	public function GetCountUnidades()
	{
		return count($this->GetAllUnidades());
	}

	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByReporteFacturacion($this);
	}
}

?>