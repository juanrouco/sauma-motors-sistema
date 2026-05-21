<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TareaSeguimiento
{
	public $IdSeguimiento;
	public $IdTarea;
	public $IdUsuario;
	public $IdAccion;
	public $Detalle;
	public $Fecha;
	public $FechaAccion;
	public $Resultado;
	public $SeguimientoRealizado;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdSeguimiento 		= $arr['IdSeguimiento'];
		$this->IdTarea 				= $arr['IdTarea'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->IdAccion 			= $arr['IdAccion'];
		$this->Detalle 				= stripslashes($arr['Detalle']);
		$this->Fecha				= stripslashes($arr['Fecha']);
		$this->FechaAccion			= stripslashes($arr['FechaAccion']);
		$this->Resultado			= stripslashes($arr['Resultado']);
		$this->SeguimientoRealizado	= $arr['SeguimientoRealizado'];
	}
}

?>