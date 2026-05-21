<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Comision extends DBAccess 
{
	public $IdComision;
	public $IdMinuta;
	public $IndiceComision;
	
	public function __construct()
	{
		$this->IdComision			= '';
		$this->IdMinuta 			= '';
		$this->IndiceComision		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdComision			= $arr['IdComision'];
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->IndiceComision		= $arr['IndiceComision'];
	}
}
?>
