<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class MinutaUsadoFinanciacion
{
	public $IdMinutaFinanciacion;
	public $IdMinuta;
	public $IdAcreedor;
	public $Importe;


	public function __construct()
	{
		$this->IdMinutaFinanciacion		= '';
		$this->IdMinuta					= '';
		$this->IdAcreedor				= '';
		$this->Importe					= '';
		$this->Cuotas					= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinutaFinanciacion	= $arr['IdMinutaFinanciacion'];
		$this->IdMinuta				= $arr['IdMinuta'];
		$this->IdAcreedor			= $arr['IdAcreedor'];
		$this->Importe				= $arr['Importe'];
		$this->Cuotas				= $arr['Cuotas'];
	}
}

?>