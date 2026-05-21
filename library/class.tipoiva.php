<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoIva
{
	public $IdTipoIva;
	public $Codigo;
	public $Nombre;
	public $FacturaTipo;
	public $CodigoAfip;
	
	const RI = 1;
	const RNI = 2;
	const CF = 3;
	const MO = 4;
	const EX = 5;
	
	public function __construct()
	{
		$this->IdTipoIva 	= '';
		$this->Codigo		= '';
		$this->Nombre 		= '';
		$this->FacturaTipo 	= '';
		$this->CodigoAfip 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoIva 	= $arr['IdTipoIva'];
		$this->Codigo		= $arr['Codigo'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
		$this->FacturaTipo	= $arr['FacturaTipo'];
		$this->CodigoAfip	= $arr['CodigoAfip'];
	}


	public function CanDelete()
	{
		if ($this->GetAllClientes())
			return false;

		return true;
	}

	
	public function GetAllClientes()
	{
		$Clientes = new Clientes();
		
		return $Clientes->GetAllByTipoIva($this);
	}	
}

?>