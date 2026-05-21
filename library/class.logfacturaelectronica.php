<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class LogFacturaElectronica
{	
	public $IdLogFacturaElectronica;
	public $Fecha;
	public $IdUsuario;
	public $IdComprobante;
	public $IdComprobanteAfip;
	public $Comentarios;
	public $XmlRequest;
	public $XmlResponse;
	
	public function __construct()
	{
		$this->IdLogFacturaElectronica 	= '';
		$this->Fecha 					= '';
		$this->IdUsuario 				= '';
		$this->IdComprobante 			= '';
		$this->IdComprobanteAfip 		= '';
		$this->Comentarios 				= '';
		$this->XmlRequest 				= '';
		$this->XmlResponse 				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdLogFacturaElectronica 	= $arr['IdLogFacturaElectronica'];
		$this->Fecha			 		= $arr['Fecha'];
		$this->IdUsuario			 	= $arr['IdUsuario'];
		$this->IdComprobante			= $arr['IdComprobante'];
		$this->IdComprobanteAfip 		= $arr['IdComprobanteAfip'];
		$this->Comentarios			 	= stripslashes($arr['Comentarios']);
		$this->XmlRequest			 	= stripslashes($arr['XmlRequest']);
		$this->XmlResponse			 	= stripslashes($arr['XmlResponse']);
	}
}

?>