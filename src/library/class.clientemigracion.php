<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ClienteMigracion extends DBAccess 
{
	public $IdClienteMigracion;
	public $IdCliente;
	public $IdAntiguo;
	

	public function __construc()
	{
		$this->IdClienteMigracion	= '';
		$this->IdCliente		 	= '';
		$this->IdAntiguo		 	= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdClienteMigracion	= $arr['IdClienteMigracion'];
		$this->IdCliente		 	= $arr['IdCliente'];
		$this->IdAntiguo		 	= $arr['IdAntiguo'];
	}
}

?>
