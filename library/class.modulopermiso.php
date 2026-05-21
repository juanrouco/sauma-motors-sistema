<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ModuloPermiso extends DBAccess 
{
	public $IdModulo;
	public $IdPermiso;
	

	public function __construc()
	{
		$this->IdModulo		= '';
		$this->IdPermiso 	= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdModulo		= $arr['IdModulo'];
		$this->IdPermiso 	= $arr['IdPermiso'];
	}
}

?>
