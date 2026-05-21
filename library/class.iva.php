<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Iva
{
	public $IdIva;
	public $Nombre;
	public $Alicuota;
	
	const Iva21 = 1;
	const Iva10 = 2;
	
	public function __construct()
	{
		$this->IdIva 	= '';
		$this->Nombre 	= '';
		$this->Alicuota	= 0;
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdIva 	= $arr['IdIva'];
		$this->Nombre 	= stripslashes($arr['Nombre']);
		$this->Alicuota	= $arr['Alicuota'];
	}	
}

?>