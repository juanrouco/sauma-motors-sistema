<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Recepcion
{
	public $IdRecepcion;
	public $NumeroCartaPorte;
	public $FechaRecepcion;
	public $Observaciones;
	public $IdEstado;
	
	
	public function __construct()
	{
		$this->IdRecepcion 		= '';
		$this->NumeroCartaPorte	= '';
		$this->FechaRecepcion 	= '';
		$this->Observaciones 	= '';
		$this->IdEstado 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdRecepcion 		= $arr['IdRecepcion'];
		$this->NumeroCartaPorte	= $arr['NumeroCartaPorte'];
		$this->FechaRecepcion 	= $arr['FechaRecepcion'];
		$this->Observaciones 	= $arr['Observaciones'];
		$this->IdEstado 		= $arr['IdEstado'];
	}
	
	
	public function GetAllDetalles()
	{
		$RecepcionDetalles = new RecepcionDetalles();
		
		return $RecepcionDetalles->GetAllByRecepcion($this);
	}
}

?>