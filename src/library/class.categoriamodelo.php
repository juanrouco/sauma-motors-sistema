<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CategoriaModelo
{
	public $IdCategoriaModelo;
	public $Codigo;
	public $Nombre;
	
	
	public function __construct()
	{
		$this->IdCategoriaModelo 	= '';
		$this->Codigo				= '';
		$this->Nombre 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCategoriaModelo 	= $arr['IdCategoriaModelo'];
		$this->Codigo				= $arr['Codigo'];
		$this->Nombre 				= stripslashes($arr['Nombre']);
	}
	
	public function CanDelete() 
	{
		return true;
	}
}

?>