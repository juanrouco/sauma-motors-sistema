<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Sector
{
	public $IdSector;
	public $Codigo;
	public $Nombre;
	
	
	public function __construct()
	{
		$this->IdSector = '';
		$this->Codigo	= '';
		$this->Nombre 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdSector = $arr['IdSector'];
		$this->Codigo	= $arr['Codigo'];
		$this->Nombre 	= stripslashes($arr['Nombre']);
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllUsuarios())
			return false;
		
		return true;
	}
	
	
	public function GetAllUsuarios()
	{
		$Usuarios = new Usuarios();
		
		return $Usuarios->GetAllBySector($this);
	}	
}

?>