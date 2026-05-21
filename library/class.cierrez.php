<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CierreZ
{
	public $IdCierreZ;
	public $Fecha;
	public $IdUsuario;
	
	public function __construct()
	{
		$this->IdCierreZ 		= '';
		$this->Fecha			= '';
		$this->IdUsuario			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCierreZ 		= $arr['IdCierreZ'];
		$this->Fecha			= $arr['Fecha'];
		$this->IdUsuario 		= $arr['IdUsuario'];
	}
}

?>