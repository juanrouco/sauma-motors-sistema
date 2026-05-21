<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class NotaNoRodamiento
{
	public $IdNota;
	public $IdUnidad;
	public $IdCliente;
	public $Fecha;


	public function __construct()
	{
		$this->IdNota		= '';
		$this->IdUnidad		= '';
		$this->IdCliente	= '';
		$this->Fecha		= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdNota		= $arr['IdNota'];
		$this->IdUnidad		= $arr['IdUnidad'];
		$this->IdCliente	= $arr['IdCliente'];
		$this->Fecha		= $arr['Fecha'];
	}
}

?>