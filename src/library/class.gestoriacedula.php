<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class GestoriaCedula
{
	public $IdCedula;
	public $IdGestoria;
	public $Nombre;
	public $Apellido;
	public $DocumentoTipo;
	public $DocumentoNumero;


	public function __construct()
	{
		$this->IdCedula 		= '';
		$this->IdGestoria 		= '';
		$this->Nombre 			= '';
		$this->Apellido 		= '';
		$this->DocumentoTipo 	= '';
		$this->DocumentoNumero 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCedula 		= $arr['IdCedula'];
		$this->IdGestoria 		= $arr['IdGestoria'];
		$this->Nombre 			= $arr['Nombre'];
		$this->Apellido 		= $arr['Apellido'];
		$this->DocumentoTipo 	= $arr['DocumentoTipo'];
		$this->DocumentoNumero 	= $arr['DocumentoNumero'];
	}
}

?>