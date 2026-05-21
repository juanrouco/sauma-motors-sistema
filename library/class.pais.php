<?php
require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.provincias.php');
require_once('class.usuarios.php');

class Pais
{
	public $IdPais;
	public $Codigo;
	public $Nombre;
	public $Current;
	public $Nacionalidad;

	
	public function __construct()
	{
		$this->IdPais 	= '';
		$this->Codigo	= '';
		$this->Nombre 	= '';
		$this->Current 	= '';
		$this->Nacionalidad 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPais 	= $arr['IdPais'];
		$this->Codigo 	= stripslashes($arr['Codigo']);
		$this->Nombre 	= stripslashes($arr['Nombre']);
		$this->Current 	= (bool)$arr['Current'];
		$this->Nacionalidad 	= stripslashes($arr['Nacionalidad']);
	}
	

	public function CanDelete()
	{
		if ($this->GetAllProvincias())
			return false;

		if ($this->GetAllDatosEmpresa())
			return false;

		if ($this->GetAllClientes())
			return false;

		if ($this->GetAllPrendaFiadores())
			return false;
		
		return true;
	}

	
	public function GetAllProvincias()
	{
		$Provincias = new Provincias();
		
		return $Provincias->GetAllByPais($this);
	}
	
	
	public function GetAllDatosEmpresa()
	{
		$DatosEmpresa = new DatosEmpresa();
		
		return $DatosEmpresa->GetAllByPais($this);
	}	


	public function GetAllClientes()
	{
		$Clientes = new Clientes();
		
		return $Clientes->GetAllByPais($this);
	}


	public function GetAllPrendaFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByPais($this);
	}
}

?>