<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class RecepcionDetalle extends DBAccess 
{
	public $IdRecepcion;
	public $IdUnidad;	
	public $CodigoLlaves;
	

	public function __construc()
	{
		$this->IdRecepcion	= '';
		$this->IdUnidad 	= '';
		$this->CodigoLlaves = '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdRecepcion	= $arr['IdRecepcion'];
		$this->IdUnidad 	= $arr['IdUnidad'];
		$this->CodigoLlaves = stripslashes($arr['CodigoLlaves']);
	}
}

?>
