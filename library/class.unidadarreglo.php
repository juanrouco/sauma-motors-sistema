<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class UnidadArreglo extends DBAccess 
{
	public $IdUnidadArreglo;
	public $IdUnidad;
	public $Detalle;
	public $Importe;
	

	public function __construc()
	{
		$this->IdUnidadArreglo	= '';
		$this->IdUnidad 			= '';
		$this->Detalle 			= '';
		$this->Importe 			= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdUnidadArreglo	= $arr['IdUnidadArreglo'];
		$this->IdUnidad	 		= $arr['IdUnidad'];
		$this->Detalle 			= stripslashes($arr['Detalle']);
		$this->Importe 			= $arr['Importe'];
	}
}

?>
