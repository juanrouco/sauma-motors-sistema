<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class RecepcionUsado
{
	public $IdRecepcionUsado;
	public $IdUsado;
	public $IdCliente;
	public $Fecha;
	public $Observaciones;
	public $EntregaTitulo;
	public $EntregaCedula;
	public $Entrega08;
	public $EntregaInformeDominio;
	public $Entrega13I;
	public $EntregaVerificacionBomberos;
	public $EntregaPatentes;
	public $EntregaManualLlaves;
	public $EntregaManual;
	public $EntregaClaveFiscal;
	
	public function __construct()
	{
		$this->IdRecepcionUsado	= '';
		$this->IdUsado 			= '';
		$this->IdCliente 		= '';
		$this->Fecha			= '';
		$this->Observaciones	= '';
		$this->EntregaTitulo	= '';
		$this->EntregaCedula	= '';
		$this->Entrega08	= '';
		$this->EntregaInformeDominio	= '';
		$this->Entrega13I	= '';
		$this->EntregaVerificacionBomberos	= '';
		$this->EntregaPatentes	= '';
		$this->EntregaManualLlaves	= '';
		$this->EntregaManual		= '';
		$this->EntregaClaveFiscal	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdRecepcionUsado	= $arr['IdRecepcionUsado'];
		$this->IdUsado 			= $arr['IdUsado'];
		$this->IdCliente 		= $arr['IdCliente'];
		$this->Fecha			= $arr['Fecha'];
		$this->Observaciones	= $arr['Observaciones'];
		$this->EntregaTitulo	= $arr['EntregaTitulo'];
		$this->EntregaCedula	= $arr['EntregaCedula'];
		$this->Entrega08		= $arr['Entrega08'];
		$this->EntregaInformeDominio	= $arr['EntregaInformeDominio'];
		$this->Entrega13I	= $arr['Entrega13I'];
		$this->EntregaVerificacionBomberos	= $arr['EntregaVerificacionBomberos'];
		$this->EntregaPatentes	= $arr['EntregaPatentes'];
		$this->EntregaManualLlaves	= $arr['EntregaManualLlaves'];
		$this->EntregaManual		= $arr['EntregaManual'];
		$this->EntregaClaveFiscal	= $arr['EntregaClaveFiscal'];
	}
	
	public function CanDelete()
	{
		return true;
	}
}

?>