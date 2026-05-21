<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CajaApertura extends DBAccess 
{
	public $IdCajaApertura;
	public $IdUsuario;
	public $IdTipoApertura;
	public $IdTurno;
	public $IdCajaDetalle;
	public $TotalRendir;
	public $TotalReal;
	public $Diferencia;
	public $Fecha;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaApertura			= $arr['IdCajaApertura'];
		$this->IdUsuario				= $arr['IdUsuario'];
		$this->IdTipoApertura			= $arr['IdTipoApertura'];
		$this->IdTurno					= $arr['IdTurno'];
		$this->IdCajaDetalle			= $arr['IdCajaDetalle'];
		$this->TotalRendir				= $arr['TotalRendir'];
		$this->TotalReal				= $arr['TotalReal'];
		$this->Diferencia				= $arr['Diferencia'];
		$this->Fecha					= $arr['Fecha'];	
	}
}

?>