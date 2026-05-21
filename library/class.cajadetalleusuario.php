<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CajaDetalleUsuario extends DBAccess 
{
	public $IdCajaDetalle;
	public $IdUsuario;
	
	public function __construct()
	{
		$this->IdCajaDetalle	= '';
		$this->IdUsuario 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaDetalle	= $arr['IdCajaDetalle'];
		$this->IdUsuario 		= $arr['IdUsuario'];
	}
}

?>