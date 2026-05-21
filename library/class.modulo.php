<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.modulopermisos.php');

class Modulo extends DBAccess 
{
	public $IdModulo;
	public $Codigo;
	public $Nombre;
	public $Permisos;
	
	public function __construct()
	{
		$this->IdModulo	= '';
		$this->Codigo	= '';
		$this->Nombre 	= '';
		
		$this->Permisos = array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdModulo	= $arr['IdModulo'];
		$this->Codigo	= $arr['Codigo'];
		$this->Nombre 	= $arr['Nombre'];
	}


	public function GetAllPermisos()
	{
		$ModuloPermisos = new ModuloPermisos();
		
		return $ModuloPermisos->GetAllByModulo($this);
	}	


	public function DeleteAllPermisos()
	{
		$ModuloPermisos = new ModuloPermisos();
		
		return $ModuloPermisos->DeleteByModulo($this);
	}	
}
?>
