<?php
require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.usuarios.php');

class Localidad
{
	public $IdLocalidad;
	public $IdPartido;
	public $IdProvincia;
	public $IdPais;
	public $Nombre;
	public $CodigoPostal;
	public $Jurisdiccion;	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdLocalidad	= $arr['IdLocalidad'];
		$this->IdPartido	= $arr['IdPartido'];
		$this->IdProvincia	= $arr['IdProvincia'];
		$this->IdPais		= $arr['IdPais'];
		$this->Nombre		= stripslashes($arr['Nombre']);
		$this->CodigoPostal	= stripslashes($arr['CodigoPostal']);
		$this->Jurisdiccion	= $arr['Jurisdiccion'];
	}
	

	public function CanDelete()
	{
		if ($this->GetAllClientes())
			return false;

		if ($this->GetAllGestorias())
			return false;

		if ($this->GetAllPrendaFiadores())
			return false;

		if ($this->GetAllDatosEmpresa())
			return false;
		
		return true;
	}

	
	public function GetAllClientes()
	{
		$Clientes = new Clientes();
		
		return $Clientes->GetAllByLocalidad($this);
	}	


	public function GetAllGestorias()
	{
		$Gestorias = new Gestorias();
		
		return $Gestorias->GetAllByLocalidad($this);
	}	


	public function GetAllPrendaFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByLocalidad($this);
	}	
	
	
	public function GetAllDatosEmpresa()
	{
		$DatosEmpresa = new DatosEmpresa();
		
		return $DatosEmpresa->GetAllByLocalidad($this);
	}	
}

?>