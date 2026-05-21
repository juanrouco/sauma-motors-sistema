<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ClienteContacto
{
	public $IdContacto;
	public $IdCliente;
	public $Nombre;
	public $Apellido;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $DocumentoTipo;
	public $DocumentoNumero;
	public $FechaNacimiento;
	public $Email;
	
	
	public function __construct()
	{
		$this->IdContacto 			= '';
		$this->IdCliente			= '';
		$this->Nombre 				= '';
		$this->Apellido 			= '';
		$this->TelefonoCodigoArea 	= '';
		$this->Telefono 			= '';
		$this->DocumentoTipo 		= '';
		$this->DocumentoNumero 		= '';
		$this->FechaNacimiento 		= '';
		$this->Email 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdContacto 			= $arr['IdContacto'];
		$this->IdCliente			= $arr['IdCliente'];
		$this->Nombre 				= $arr['Nombre'];
		$this->Apellido 			= $arr['Apellido'];
		$this->TelefonoCodigoArea 	= $arr['TelefonoCodigoArea'];
		$this->Telefono 			= $arr['Telefono'];
		$this->DocumentoTipo 		= $arr['DocumentoTipo'];
		$this->DocumentoNumero 		= $arr['DocumentoNumero'];
		$this->FechaNacimiento 		= $arr['FechaNacimiento'];
		$this->Email 				= $arr['Email'];
	}
}

?>