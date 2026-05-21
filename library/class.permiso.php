<?php

require_once('class.dbaccess.php');

class Permiso extends DBAccess
{	
	public $IdPermiso;
	public $Nombre;
	public $Descripcion;
	public $Visible;
	

	public function __construct()
	{
		$this->IdPermiso	= '';
		$this->Nombre		= '';
		$this->Descripcion	= '';
		$this->Visible		= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPermiso	= $arr['IdPermiso'];
		$this->Nombre		= $arr['Nombre'];
		$this->Descripcion	= stripslashes($arr['Descripcion']);
		$this->Visible		= $arr['Visible'];
	}
}

?>