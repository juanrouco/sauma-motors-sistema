<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class SerieMigracion
{
	public $IdSerieMigracion;
	public $Codigo;
	public $Descripcion;
	public $Descripcion2;
	public $IdMarca;
	public $IdModeloMigracion;
	public $Iva;
	
	public function __construct()
	{
		$this->IdSerieMigracion 	= '';
		$this->Codigo	= '';
		$this->Descripcion 	= '';
		$this->Descripcion2 	= '';
		$this->IdMarca 	= '';
		$this->IdModeloMigracion 	= '';
		$this->Iva 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdSerieMigracion 	= $arr['IdSerieMigracion'];
		$this->Codigo	= $arr['Codigo'];
		$this->Descripcion 	= stripslashes($arr['Descripcion']);
		$this->Descripcion2 	= stripslashes($arr['Descripcion2']);
		$this->IdMarca 	= $arr['IdMarca'];
		$this->IdModeloMigracion 	= $arr['IdModeloMigracion'];
		$this->Iva 	= $arr['Iva'];
	}	
}

?>