<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PerfilPermiso extends DBAccess 
{
	public $IdPerfil;
	public $IdPermiso;
	

	public function __construct()
	{
		$this->IdPerfil		= '';
		$this->IdPermiso	= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdPerfil		= $arr['IdPerfil'];
		$this->IdPermiso	= $arr['IdPermiso'];
	}
}

?>