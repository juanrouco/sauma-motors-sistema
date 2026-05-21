<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TareaTrabajoArticulo extends DBAccess 
{
	public $IdTareaTrabajo;
	public $IdArticulo;
	public $Cantidad;

	public function __construct()
	{
		$this->IdTareaTrabajo	= '';
		$this->IdArticulo		= '';
		$this->Cantidad			= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdTareaTrabajo	= $arr['IdTareaTrabajo'];
		$this->IdArticulo		= $arr['IdArticulo'];
		$this->Cantidad		= $arr['Cantidad'];
	}
}
?>