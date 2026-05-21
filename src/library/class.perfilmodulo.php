<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PerfilModulo extends DBAccess 
{
	public $IdPerfil;
	public $IdModulo;
	

	public function __construct()
	{
		$this->IdPerfil	= '';
		$this->IdModulo	= '';
	}


	public function ParseFromArray(array $arr)
	{
		$this->IdPerfil	= $arr['IdPerfil'];
		$this->IdModulo	= $arr['IdModulo'];
	}
}

?>