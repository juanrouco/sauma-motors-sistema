<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Tarea
{
	public $IdTarea;
	public $IdTipo;
	public $FechaInicio;
	public $FechaFin;	
	public $Nombre;
	public $IdUsuarioFrom;
	public $IdUsuarioTo;	
	public $IdEstado;
	public $Descripcion;
	public $Hora;
	public $IdCliente;
	public $IdPresupuesto;
	
	public function __construct()
	{
		$this->Grupos = array();
	}
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTarea 				= $arr['IdTarea'];
		$this->IdTipo 				= $arr['IdTipo'];
		$this->FechaInicio 			= stripslashes($arr['FechaInicio']);
		$this->FechaFin				= stripslashes($arr['FechaFin']);		
		$this->Nombre				= stripslashes($arr['Nombre']);
		$this->IdUsuarioFrom		= $arr['IdUsuarioFrom'];
		$this->IdUsuarioTo 			= $arr['IdUsuarioTo'];
		$this->IdEstado 			= $arr['IdEstado'];
		$this->Descripcion 			= stripslashes($arr['Descripcion']);
		$this->Hora		 			= $arr['Hora'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->IdPresupuesto		= $arr['IdPresupuesto'];
	}
}

?>