<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class EstadoCivil
{
	public $IdEstadoCivil;
	public $Codigo;
	public $Nombre;
	public $Predeterminado;
	
	const Soltero 		= 1;
	const Casado 		= 2;
	const Viudo 		= 3;
	const Divorciado 	= 4;

	
	public function __construct()
	{
		$this->IdEstadoCivil 	= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
		$this->Predeterminado 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdEstadoCivil 	= $arr['IdEstadoCivil'];
		$this->Codigo			= $arr['Codigo'];
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Predeterminado	= (bool)$arr['Predeterminado'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllClientes())
			return false;

		if ($this->GetAllPrendaFiadores())
			return false;
		
		return true;
	}

	
	public function GetAllClientes()
	{
		$Clientes = new Clientes();
		
		return $Clientes->GetAllByEstadoCivil($this);
	}	


	public function GetAllPrendaFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByEstadoCivil($this);
	}	
}

?>