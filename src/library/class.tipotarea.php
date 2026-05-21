<?php
require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoTarea
{
	public $IdTipoTarea;
	public $Nombre;
	
	const Presupuesto = 4;
	
	public function __construct()
	{
		$this->IdTipoTarea 	= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoTarea 	= $arr['IdTipoTarea'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
	}
	

	public function CanDelete()
	{
		return true;
	}
}

?>