<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CajaDetalleDefault
{
	public $IdTipoPago;
	public $IdUbicacion;
	public $IdCajaAdministracion;
	public $IdCajaTaller;
	public $IdCajaRepuestos;
	
	public function __construct()
	{
		$this->IdTipoPago			= '';
		$this->IdUbicacion 			= '';
		$this->IdCajaAdministracion = '';
		$this->IdCajaTaller 		= '';
		$this->IdCajaRepuestos 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoPago			= $arr['IdTipoPago'];
		$this->IdUbicacion 			= $arr['IdUbicacion'];
		$this->IdCajaAdministracion = $arr['IdCajaAdministracion'];
		$this->IdCajaTaller 		= $arr['IdCajaTaller'];
		$this->IdCajaRepuestos 		= $arr['IdCajaRepuestos'];
	}
}

?>