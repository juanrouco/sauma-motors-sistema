<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class DestinoVehiculo
{
	public $IdDestinoVehiculo;
	public $Codigo;
	public $Nombre;	
	
	public function __construct()
	{
		$this->IdDestinoVehiculo 	= '';
		$this->Codigo				= '';
		$this->Nombre 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdDestinoVehiculo 	= $arr['IdDestinoVehiculo'];
		$this->Codigo				= stripslashes($arr['Codigo']);
		$this->Nombre 				= stripslashes($arr['Nombre']);
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
		
		return false;//$Modelos->GetAllByDestinoVehiculos($this);
	}
}

?>