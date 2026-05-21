<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoVehiculo
{
	public $IdTipoVehiculo;
	public $Codigo;
	
	public function __construct()
	{
		$this->IdTipoVehiculo 	= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoVehiculo 	= $arr['IdTipoVehiculo'];
		$this->Codigo			= stripslashes($arr['Codigo']);
		$this->Nombre 			= stripslashes($arr['Nombre']);
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllModelos())
			return false;
		
		return true;
	}
	
	
	public function GetAllModelos()
	{
		$Modelos = new Modelos();
		
		return false;//$Modelos->GetAllByTipoVehiculo($this);
	}

}

?>