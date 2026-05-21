<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Profesion
{
	public $IdProfesion;
	public $Codigo;
	public $Nombre;
	
	
	public function __construct()
	{
		$this->IdProfesion 	= '';
		$this->Codigo		= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdProfesion 	= $arr['IdProfesion'];
		$this->Codigo		= $arr['Codigo'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
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
		
		return $Clientes->GetAllByProfesion($this);
	}	


	public function GetAllPrendaFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByProfesion($this);
	}	
}

?>