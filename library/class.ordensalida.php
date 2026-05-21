<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrdenSalida
{
	public $IdOrden;
	public $IdUnidad;
	public $IdCliente;
	public $IdTipoDestinatario;
	public $Transporte;
	public $TransporteClaveFiscalTipo;
	public $TransporteClaveFiscalNumero;
	public $AdquirienteRazonSocial;
	public $AdquirienteDocumentoTipo;
	public $AdquirienteDocumentoNumero;
	public $Fecha;
	public $EntregaManuales;
	public $EntregaLlaves;
	public $EntregaTarjetaCode;
	public $EntregaDocumentacion;
	public $IdUbicacion;

	public function __construct()
	{
		$this->IdOrden						= '';
		$this->IdUnidad						= '';
		$this->IdCliente					= '';
		$this->IdTipoDestinatario			= '';
		$this->Transporte					= '';
		$this->TransporteClaveFiscalTipo 	= '';
		$this->TransporteClaveFiscalNumero 	= '';
		$this->AdquirienteRazonSocial		= '';
		$this->AdquirienteDocumentoTipo		= '';
		$this->AdquirienteDocumentoNumero	= '';
		$this->Fecha						= '';
		$this->EntregaManuales				= '';
		$this->EntregaLlaves				= '';
		$this->EntregaTarjetaCode			= '';
		$this->EntregaDocumentacion			= '';
		$this->IdUbicacion					= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrden						= $arr['IdOrden'];
		$this->IdUnidad						= $arr['IdUnidad'];
		$this->IdCliente					= $arr['IdCliente'];
		$this->IdTipoDestinatario			= $arr['IdTipoDestinatario'];
		$this->Transporte					= $arr['Transporte'];
		$this->TransporteClaveFiscalTipo 	= $arr['TransporteClaveFiscalTipo'];
		$this->TransporteClaveFiscalNumero 	= $arr['TransporteClaveFiscalNumero'];
		$this->AdquirienteRazonSocial		= $arr['AdquirienteRazonSocial'];
		$this->AdquirienteDocumentoTipo		= $arr['AdquirienteDocumentoTipo'];
		$this->AdquirienteDocumentoNumero	= $arr['AdquirienteDocumentoNumero'];
		$this->Fecha						= $arr['Fecha'];
		$this->EntregaManuales				= (bool)$arr['EntregaManuales'];
		$this->EntregaLlaves				= (bool)$arr['EntregaLlaves'];
		$this->EntregaTarjetaCode			= (bool)$arr['EntregaTarjetaCode'];
		$this->EntregaDocumentacion			= (bool)$arr['EntregaDocumentacion'];
		$this->IdUbicacion					= $arr['IdUbicacion'];
	}
}

?>