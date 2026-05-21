<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class UsuarioJornada
{
	public $IdUsuarioJornada;
	public $IdUsuario;
	public $DiaSemana;
	public $HoraInicio;
	public $HoraFin;
	public $HoraAlmuerzoInicio;
	public $HoraAlmuerzoFin;
	
	public function __construct()
	{
		$this->IdUsuarioJornada 	= '';
		$this->IdUsuario 			= '';
		$this->DiaSemana			= '';
		$this->HoraInicio			= '';
		$this->HoraFin				= '';
		$this->HoraAlmuerzoInicio	= '';
		$this->HoraAlmuerzoFin		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUsuarioJornada 	= $arr['IdUsuarioJornada'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->DiaSemana 			= $arr['DiaSemana'];
		$this->HoraInicio 			= $arr['HoraInicio'];
		$this->HoraFin	 			= $arr['HoraFin'];
		$this->HoraAlmuerzoInicio	= $arr['HoraAlmuerzoInicio'];
		$this->HoraAlmuerzoFin		= $arr['HoraAlmuerzoFin'];
	}
}

?>