<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrdenTrabajoComentario
{
	public $IdOrdenTrabajoComentario;
	public $IdOrdenTrabajo;
	public $IdUsuario;
	public $Comentarios;
	public $IdTipoRechazo;
	
	public function __construct()
	{
		$this->IdOrdenTrabajoComentario 	= '';
		$this->IdOrdenTrabajo			 	= '';
		$this->IdUsuario				 	= '';
		$this->Comentarios 					= '';
		$this->IdTipoRechazo				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajoComentario 	= $arr['IdOrdenTrabajoComentario'];
		$this->IdOrdenTrabajo			 	= $arr['IdOrdenTrabajo'];
		$this->IdUsuario				 	= $arr['IdUsuario'];
		$this->Comentarios 					= stripslashes($arr['Comentarios']);
		$this->IdTipoRechazo				= $arr['IdTipoRechazo'];
	}	
}

?>