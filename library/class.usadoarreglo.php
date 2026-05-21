<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class UsadoArreglo extends DBAccess 
{
	public $IdUsadoArreglo;
	public $IdUsado;
	public $Detalle;
	public $Importe;
	

	public function __construc()
	{
		$this->IdUsadoArreglo	= '';
		$this->IdUsado 			= '';
		$this->Detalle 			= '';
		$this->Importe 			= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdUsadoArreglo	= $arr['IdUsadoArreglo'];
		$this->IdUsado	 		= $arr['IdUsado'];
		$this->Detalle 			= stripslashes($arr['Detalle']);
		$this->Importe 			= $arr['Importe'];
	}
}

?>
