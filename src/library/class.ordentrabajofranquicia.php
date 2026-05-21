<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrdenTrabajoFranquicia
{
	public $IdOrdenTrabajoFranquicia;
	public $IdOrdenTrabajo;
	public $IdCliente;
	public $Descripcion;
	public $Importe;
	public $IdComprobante;
	public $Anulado;
	public $IdFactura;
	
	public function __construct()
	{
		$this->IdOrdenTrabajoFranquicia 	= '';
		$this->IdOrdenTrabajo			 	= '';
		$this->IdCliente				 	= '';
		$this->Descripcion 					= '';
		$this->Importe						= '';
		$this->IdComprobante				= '';
		$this->Anulado						= '';
		$this->IdFactura					= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajoFranquicia 	= $arr['IdOrdenTrabajoFranquicia'];
		$this->IdOrdenTrabajo			 	= $arr['IdOrdenTrabajo'];
		$this->IdCliente				 	= $arr['IdCliente'];
		$this->Descripcion 					= stripslashes($arr['Descripcion']);
		$this->Importe						= $arr['Importe'];
		$this->IdComprobante				= $arr['IdComprobante'];
		$this->Anulado						= $arr['Anulado'];
		$this->IdFactura					= $arr['IdFactura'];
	}	
}

?>