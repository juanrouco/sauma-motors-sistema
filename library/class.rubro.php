<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Rubro
{
	const IdVehiculo = 220;
	
	public $IdRubro;
	public $Nombre;
	
	public function __construct()
	{
		$this->IdRubro 	= '';
		$this->Nombre 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdRubro 	= $arr['IdRubro'];
		$this->Nombre 	= stripslashes($arr['Nombre']);
	}
	
	
	public function GetAllProveedores()
	{
		$Proveedores = new Proveedores();
		
		return $Proveedores->GetAllByRubro($this);
	}
	
	public function CanDelete()
	{
		if ($this->GetAllProveedores())
			return false;
		
		return true;
	}
}

?>