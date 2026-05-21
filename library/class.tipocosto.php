<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoCosto
{
	const CostoFijo = 1;
	const CostoCalculado = 2;
	
	public $IdTipoCosto;
	public $Nombre;
	
	public function __construct()
	{
		$this->IdTipoCosto 	= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoCosto 	= $arr['IdTipoCosto'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
	}
}

?>