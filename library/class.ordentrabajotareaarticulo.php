<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrdenTrabajoTareaArticulo extends DBAccess 
{
	public $IdOrdenTrabajoTarea;
	public $IdArticulo;
	public $Cantidad;
	public $PrecioTotal;

	public function __construct()
	{
		$this->IdOrdenTrabajoTarea	= '';
		$this->IdArticulo			= '';
		$this->Cantidad				= '';
		$this->PrecioTotal			= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajoTarea	= $arr['IdOrdenTrabajoTarea'];
		$this->IdArticulo		= $arr['IdArticulo'];
		$this->Cantidad		= $arr['Cantidad'];
		$this->PrecioTotal	= $arr['PrecioTotal'];
	}
}
?>