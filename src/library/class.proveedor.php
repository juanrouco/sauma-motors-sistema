<?php

require_once('class.db.php');
require_once('class.dbaccess.php');


class Proveedor
{
	const Ford = 1;

	public $IdProveedor;
	public $Empresa;
	public $IdRubro;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $TelefonoCodigoArea2;
	public $Telefono2;
	public $FaxCodigoArea;
	public $Fax;
	public $Email;
	public $Web;
	public $DomicilioCalle;
	public $DomicilioNumero;
	public $DomicilioPiso;
	public $DomicilioDpto;
	public $IdPais;
	public $IdProvincia;	
	public $IdPartido;
	public $IdLocalidad;
	public $CodigoPostal;
	public $Observaciones;
	public $Cuit;

	public function __construct()
	{
		$this->Grupos = array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdProveedor 			= $arr['IdProveedor'];
		$this->Empresa 				= stripslashes($arr['Empresa']);
		$this->IdRubro				= $arr['IdRubro'];
		$this->TelefonoCodigoArea 	= stripslashes($arr['TelefonoCodigoArea']);
		$this->Telefono 			= stripslashes($arr['Telefono']);
		$this->TelefonoCodigoArea2 	= stripslashes($arr['TelefonoCodigoArea2']);
		$this->Telefono2 			= stripslashes($arr['Telefono2']);
		$this->FaxCodigoArea		= stripslashes($arr['FaxCodigoArea']);
		$this->Fax 					= stripslashes($arr['Fax']);
		$this->Email 				= stripslashes($arr['Email']);
		$this->Web 					= stripslashes($arr['Web']);		
		$this->DomicilioCalle 		= stripslashes($arr['DomicilioCalle']);
		$this->DomicilioNumero 		= stripslashes($arr['DomicilioNumero']);
		$this->DomicilioPiso 		= stripslashes($arr['DomicilioPiso']);
		$this->DomicilioDpto 		= stripslashes($arr['DomicilioDpto']);
		$this->IdPais 				= $arr['IdPais'];
		$this->IdProvincia 			= $arr['IdProvincia'];
		$this->IdPartido			= $arr['IdPartido'];
		$this->IdLocalidad 			= $arr['IdLocalidad'];
		$this->CodigoPostal 		= stripslashes($arr['CodigoPostal']);
		$this->Observaciones 		= stripslashes($arr['Observaciones']);
		$this->Cuit			 		= stripslashes($arr['Cuit']);
	}
	
	public function GetDomicilio()
	{
		$Domicilio = '';

		$Domicilio.= $this->DomicilioCalle . ' ' . $this->DomicilioNumero;
		
		if ($this->DomicilioPiso != '' || $this->DomicilioDpto != '')
			$Domicilio.= ' || ';
		if ($this->DomicilioPiso != '')
			$Domicilio.= ' Piso: ' . $this->DomicilioPiso;
		if ($this->DomicilioDpto != '')
			$Domicilio.= ' Dpto: ' . $this->DomicilioDpto;
			
		return $Domicilio;
	}
	
	public function GetAllArticulos()
	{
		$Articulos = new Articulos();
		return $Articulos->GetAllByProveedor($this);
	}
	
	public function CanDelete()
	{
		if ($this->GetAllArticulos())
			return false;
		
		return true;
	}
}

?>