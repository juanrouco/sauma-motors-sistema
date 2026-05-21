<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Acreedor
{
	const Tarshop = 11;
	const Credilogros = 12;
	const Confina = 13;
	const AM = 10;
	const Visa = 17;
	const MC = 18;
	const Credicuotas = 20;
	
	public $IdAcreedor;
	public $IdTipoPersona;
	public $IdNacionalidad;
	public $NumeroInscripcion;
	public $RazonSocial;
	public $DomicilioCalle;
	public $DomicilioNumero;
	public $DomicilioPiso;
	public $DomicilioDpto;
	public $DomicilioIdLocalidad;
	public $DomicilioCodigoPostal;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $DocumentoTipo;
	public $DocumentoNumero;
	public $DocumentoExpedido;
	public $FechaNacimiento;
	public $ClaveFiscalTipo;
	public $ClaveFiscalNumero;
	public $Email;
	public $EnteJuridicoOtorgacion;
	public $EnteJuridicoDatosInscripcion;
	public $EnteJuridicoFechaInscripcion;
	

	public function __construct()
	{
		$this->IdAcreedor 					= '';
		$this->IdTipoPersona 				= '';
		$this->IdNacionalidad 				= '';
		$this->NumeroInscripcion 			= '';
		$this->RazonSocial 					= '';
		$this->DomicilioCalle 				= '';
		$this->DomicilioNumero 				= '';
		$this->DomicilioPiso 				= '';
		$this->DomicilioDpto 				= '';
		$this->DomicilioIdLocalidad 		= '';
		$this->DomicilioCodigoPostal 		= '';
		$this->TelefonoCodigoArea 			= '';
		$this->Telefono 					= '';
		$this->DocumentoTipo 				= '';
		$this->DocumentoNumero 				= '';
		$this->DocumentoExpedido 			= '';
		$this->FechaNacimiento 				= '';
		$this->ClaveFiscalTipo 				= '';
		$this->ClaveFiscalNumero 			= '';
		$this->Email 						= '';
		$this->EnteJuridicoOtorgacion 		= '';
		$this->EnteJuridicoDatosInscripcion = '';
		$this->EnteJuridicoFechaInscripcion = '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdAcreedor 					= $arr['IdAcreedor'];
		$this->IdTipoPersona 				= $arr['IdTipoPersona'];
		$this->IdNacionalidad 				= $arr['IdNacionalidad'];
		$this->NumeroInscripcion 			= $arr['NumeroInscripcion'];
		$this->RazonSocial 					= $arr['RazonSocial'];
		$this->DomicilioCalle 				= $arr['DomicilioCalle'];
		$this->DomicilioNumero 				= $arr['DomicilioNumero'];
		$this->DomicilioPiso 				= $arr['DomicilioPiso'];
		$this->DomicilioDpto 				= $arr['DomicilioDpto'];
		$this->DomicilioIdLocalidad 		= $arr['DomicilioIdLocalidad'];
		$this->DomicilioCodigoPostal 		= $arr['DomicilioCodigoPostal'];
		$this->TelefonoCodigoArea 			= $arr['TelefonoCodigoArea'];
		$this->Telefono 					= $arr['Telefono'];
		$this->DocumentoTipo 				= $arr['DocumentoTipo'];
		$this->DocumentoNumero 				= $arr['DocumentoNumero'];
		$this->DocumentoExpedido 			= $arr['DocumentoExpedido'];
		$this->FechaNacimiento 				= $arr['FechaNacimiento'];
		$this->ClaveFiscalTipo 				= $arr['ClaveFiscalTipo'];
		$this->ClaveFiscalNumero 			= $arr['ClaveFiscalNumero'];
		$this->Email 						= $arr['Email'];
		$this->EnteJuridicoOtorgacion 		= $arr['EnteJuridicoOtorgacion'];
		$this->EnteJuridicoDatosInscripcion = $arr['EnteJuridicoDatosInscripcion'];
		$this->EnteJuridicoFechaInscripcion = $arr['EnteJuridicoFechaInscripcion'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllPrendas())
			return false;
		
		return true;
	}
	
	
	public function GetAllPrendas()
	{
		$Prendas = new Prendas();
		
		return $Prendas->GetAllByAcreedor($this);
	}
}

?>