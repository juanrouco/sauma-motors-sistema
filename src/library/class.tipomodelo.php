<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoModelo
{
	public $IdTipoModelo;
	public $Codigo;
	public $Nombre;
	
	
	public function __construct()
	{
		$this->IdTipoModelo = '';
		$this->Codigo		= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoModelo = $arr['IdTipoModelo'];
		$this->Codigo		= $arr['Codigo'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
	}
}

?>