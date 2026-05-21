<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CajaGestoria
{
	public $IdCajaGestoria;
	public $Fecha;
	public $Monto;
	public $Disponible;
	public $IdTipoMovimiento;
	public $IdUsuario;
	public $IdEntidad;
	public $Observaciones;
	
	public function __construct()
	{
		$this->IdCajaGestoria	= '';
		$this->Fecha 			= '';
		$this->Monto			= '';
		$this->Disponible		= '';
		$this->IdTipoMovimiento	= '';
		$this->IdUsuario		= '';
		$this->IdEntidad		= '';
		$this->Observaciones	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaGestoria	= $arr['IdCajaGestoria'];
		$this->Fecha 			= $arr['Fecha'];
		$this->Monto			= $arr['Monto'];
		$this->Disponible		= $arr['Disponible'];
		$this->IdTipoMovimiento	= $arr['IdTipoMovimiento'];
		$this->IdUsuario		= $arr['IdUsuario'];
		$this->IdEntidad		= $arr['IdEntidad'];
		$this->Observaciones	= $arr['Observaciones'];
	}
}

?>