<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TareaTrabajoCodigoTrabajo extends DBAccess 
{
	public $IdTareaTrabajo;
	public $IdCodigoTrabajo;

	public function __construct()
	{
		$this->IdTareaTrabajo	= '';
		$this->IdCodigoTrabajo	= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdTareaTrabajo	= $arr['IdTareaTrabajo'];
		$this->IdCodigoTrabajo	= $arr['IdCodigoTrabajo'];
	}
}
?>