<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Formulario
{
	public $IdFormulario;
	public $IdGestoria;
	public $IdDeclaracion;
	public $IdTipoFormulario;
	public $Numero;
	public $Fecha;
	public $IdEstado;
	
	
	public function __construct()
	{
		$this->IdFormulario 	= '';
		$this->IdGestoria		= '';
		$this->IdDeclaracion 	= '';
		$this->IdTipoFormulario	= '';
		$this->Numero 			= '';
		$this->Fecha 			= '';
		$this->IdEstado 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFormulario 	= $arr['IdFormulario'];
		$this->IdGestoria 		= $arr['IdGestoria'];
		$this->IdDeclaracion 	= $arr['IdDeclaracion'];
		$this->IdTipoFormulario	= $arr['IdTipoFormulario'];
		$this->Numero 			= stripslashes($arr['Numero']);
		$this->Fecha 			= $arr['Fecha'];
		$this->IdEstado 		= $arr['IdEstado'];
	}
}

?>