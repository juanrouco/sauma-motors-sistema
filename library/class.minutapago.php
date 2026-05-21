<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class MinutaPago
{
	public $IdMinutaPago;
	public $Fecha;
	public $IdEstado;
	public $MontoDisponible;
	public $Observaciones;
	
	
	public function __construct()
	{
		$this->IdMinutaPago 	= '';
		$this->Fecha			= '';
		$this->IdEstado			= '';
		$this->MontoDisponible	= '';
		$this->Observaciones	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinutaPago 	= $arr['IdMinutaPago'];
		$this->Fecha			= $arr['Fecha'];
		$this->IdEstado			= $arr['IdEstado'];
		$this->MontoDisponible	= $arr['MontoDisponible'];
		$this->Observaciones	= $arr['Observaciones'];
	}
}

?>