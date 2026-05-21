<?php
require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.usuarios.php');

class Partido
{
	public $IdPartido;
	public $IdProvincia;
	public $IdPais;
	public $Nombre;

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPartido	= $arr['IdPartido'];
		$this->IdProvincia	= $arr['IdProvincia'];
		$this->IdPais		= $arr['IdPais'];
		$this->Nombre		= stripslashes($arr['Nombre']);
	}
	

	public function CanDelete()
	{
		if ($this->GetAllLocalidades())
			return false;

		if ($this->GetAllDatosEmpresa())
			return false;
		
		return true;
	}

	
	public function GetAllLocalidades()
	{
		$Localidades = new Localidades();
		
		return $Localidades->GetAllByPartido($this);
	}
	
	
	public function GetAllDatosEmpresa()
	{
		$DatosEmpresa = new DatosEmpresa();
		
		return $DatosEmpresa->GetAllByPartido($this);
	}	
}

?>