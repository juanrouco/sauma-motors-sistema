<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TurnoComentario
{
	public $IdTurnoComentario;
	public $IdTurno;
	public $IdUsuario;
	public $Comentarios;
	public $IdTipoRechazo;
	
	public function __construct()
	{
		$this->IdTurnoComentario 			= '';
		$this->IdTurno			 			= '';
		$this->IdUsuario				 	= '';
		$this->Comentarios 					= '';
		$this->IdTipoRechazo				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTurnoComentario 			= $arr['IdTurnoComentario'];
		$this->IdTurno			 			= $arr['IdTurno'];
		$this->IdUsuario				 	= $arr['IdUsuario'];
		$this->Comentarios 					= stripslashes($arr['Comentarios']);
		$this->IdTipoRechazo				= $arr['IdTipoRechazo'];
	}	
}

?>