<?php
require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.usuarios.php');

class Provincia
{
	public $IdProvincia;
	public $IdPais;
	public $Nombre;
	public $Codigo;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdProvincia	= $arr['IdProvincia'];
		$this->IdPais		= $arr['IdPais'];
		$this->Nombre		= stripslashes($arr['Nombre']);
		$this->Codigo		= $arr['Codigo'];
	}
	

	public function CanDelete()
	{
		if ($this->GetAllPartidos())
			return false;

		if ($this->GetAllDatosEmpresa())
			return false;

		return true;
	}

	
	public function GetAllPartidos()
	{
		$Partidos = new Partidos();
		
		return $Partidos->GetAllByProvincia($this);
	}	
	

	public function GetAllDatosEmpresa()
	{
		$DatosEmpresa = new DatosEmpresa();
		
		return $DatosEmpresa->GetAllByProvincia($this);
	}	
}

?>