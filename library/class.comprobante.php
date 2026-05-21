<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Comprobante
{
	const PathFile			= '../_recursos/comprobantes/';
	const PathFileFisico	= '\..\_recursos\comprobantes';
	
	public $IdComprobante;
	public $IdTipoComprobante;
	public $Prefijo;
	public $Numero;
	public $IdEstado;
	public $FechaAnulada;
	public $IdCliente;
	public $Importe;
	public $Fecha;
	public $IdOrdenTrabajo;
	public $ImporteIva21;
	public $ImporteIva10;
	public $ImpuestoInterno;
	public $PercepcionIIBB;
	public $Cae;
	public $Archivo;
	
	public function __construct()
	{
		$this->IdComprobante 		= '';
		$this->IdTipoComprobante	= '';
		$this->Prefijo 				= '';
		$this->Numero 				= '';
		$this->IdEstado 			= '';
		$this->FechaAnulada 		= '';
		$this->IdCliente	 		= '';
		$this->Importe		 		= '';
		$this->Fecha		 		= '';
		$this->IdOrdenTrabajo 		= '';
		$this->ImporteIva21			= '';
		$this->ImporteIva10			= '';
		$this->ImpuestoInterno		= '';
		$this->PercepcionIIBB		= '';
		$this->Cae					= '';
		$this->Archivo				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdComprobante 		= $arr['IdComprobante'];
		$this->IdTipoComprobante	= $arr['IdTipoComprobante'];
		$this->Prefijo 				= stripslashes($arr['Prefijo']);
		$this->Numero 				= stripslashes($arr['Numero']);
		$this->IdEstado 			= $arr['IdEstado'];
		$this->FechaAnulada 		= $arr['FechaAnulada'];
		$this->IdCliente	 		= $arr['IdCliente'];
		$this->Importe		 		= $arr['Importe'];
		$this->Fecha		 		= $arr['Fecha'];
		$this->IdOrdenTrabajo		= $arr['IdOrdenTrabajo'];
		$this->ImporteIva21			= $arr['ImporteIva21'];
		$this->ImporteIva10			= $arr['ImporteIva10'];
		$this->ImpuestoInterno		= $arr['ImpuestoInterno'];
		$this->PercepcionIIBB		= $arr['PercepcionIIBB'];
		$this->Cae					= $arr['Cae'];
		$this->Archivo				= $arr['Archivo'];
	}
}

?>