<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TurnoTareaArticulo extends DBAccess 
{
	public $IdTurnoTarea;
	public $IdArticulo;
	public $Cantidad;
	public $PrecioTotal;

	public function __construct()
	{
		$this->IdTurnoTarea	= '';
		$this->IdArticulo	= '';
		$this->Cantidad		= '';
		$this->PrecioTotal	= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdTurnoTarea	= $arr['IdTurnoTarea'];
		$this->IdArticulo	= $arr['IdArticulo'];
		$this->Cantidad		= $arr['Cantidad'];
		$this->PrecioTotal	= $arr['PrecioTotal'];
	}
}
?>