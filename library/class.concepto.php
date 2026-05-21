<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Concepto
{
	const TallerTerceros = 19;
	
	public $IdConcepto;
	public $Nombre;
	
	public function __construct()
	{
		$this->IdConcepto 	= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdConcepto 	= $arr['IdConcepto'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
	}
		
	public function GetAllFacturasCompras()
	{
		$oFacturasCompras = new FacturasCompras();
		
		return $oFacturasCompras->GetAllByConcepto($this);
	}
	
	public function CanDelete()
	{
		if ($this->GetAllFacturasCompras())
			return false;
		
		return true;
	}
}

?>