<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Periodo
{
	public $IdPeriodo;
	public $FechaInicio;
	public $FechaFin;
	public $Cerrado;
	public $Nombre;
	
	
	public function __construct()
	{
		$this->IdPeriodo 	= '';
		$this->FechaInicio	= '';
		$this->FechaFin 	= '';
		$this->Cerrado		= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPeriodo 	= $arr['IdPeriodo'];
		$this->FechaInicio 	= $arr['FechaInicio'];
		$this->FechaFin 	= $arr['FechaFin'];
		$this->Cerrado		= $arr['Cerrado'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
	}
}

?>