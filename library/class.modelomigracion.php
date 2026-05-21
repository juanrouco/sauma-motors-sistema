<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ModeloMigracion
{
	public $IdModeloMigracion;
	public $Codigo;
	public $Denominacion;
	public $IdMarca;
	
	public function __construct()
	{
		$this->IdModeloMigracion 	= '';
		$this->Codigo	= '';
		$this->Denominacion 	= '';
		$this->IdMarca 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdModeloMigracion 	= $arr['IdModeloMigracion'];
		$this->Codigo	= $arr['Codigo'];
		$this->Denominacion 	= stripslashes($arr['Denominacion']);
		$this->IdMarca 	= $arr['IdMarca'];
	}	
}

?>