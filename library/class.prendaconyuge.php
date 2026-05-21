<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PrendaConyuge
{
	public $IdConyuge;
	public $IdPrenda;
	public $IdTipoConyuge;
	public $RazonSocial;
	public $DomicilioCalle;
	public $DomicilioNumero;
	public $DomicilioPiso;
	public $DomicilioDpto;
	public $DomicilioIdLocalidad;
	public $DocumentoTipo;
	public $DocumentoNumero;
	public $FechaNacimiento;
	public $IdProfesion;
	public $IdNacionalidad;
	public $IdEstadoCivil;
	public $ConsentimientoConyugal;


	public function __construct()
	{
		$this->IdConyuge 				= '';
		$this->IdPrenda 				= '';
		$this->IdTipoConyuge 			= '';
		$this->RazonSocial 				= '';
		$this->DomicilioCalle 			= '';
		$this->DomicilioNumero 			= '';
		$this->DomicilioPiso 			= '';
		$this->DomicilioDpto 			= '';
		$this->DomicilioIdLocalidad 	= '';
		$this->DocumentoTipo 			= '';
		$this->DocumentoNumero 			= '';
		$this->FechaNacimiento 			= '';
		$this->IdProfesion 				= '';
		$this->IdNacionalidad 			= '';
		$this->IdEstadoCivil 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdConyuge 				= $arr['IdConyuge'];
		$this->IdPrenda 				= $arr['IdPrenda'];
		$this->IdTipoConyuge 			= $arr['IdTipoConyuge'];
		$this->RazonSocial 				= $arr['RazonSocial'];
		$this->DomicilioCalle 			= $arr['DomicilioCalle'];
		$this->DomicilioNumero 			= $arr['DomicilioNumero'];
		$this->DomicilioPiso 			= $arr['DomicilioPiso'];
		$this->DomicilioDpto 			= $arr['DomicilioDpto'];
		$this->DomicilioIdLocalidad 	= $arr['DomicilioIdLocalidad'];
		$this->DocumentoTipo 			= $arr['DocumentoTipo'];
		$this->DocumentoNumero 			= $arr['DocumentoNumero'];
		$this->FechaNacimiento 			= $arr['FechaNacimiento'];
		$this->IdProfesion 				= $arr['IdProfesion'];
		$this->IdNacionalidad 			= $arr['IdNacionalidad'];
		$this->IdEstadoCivil 			= $arr['IdEstadoCivil'];
	}
	
	
	public function GetDomicilio()
	{
		$Domicilio = '';

		$Domicilio.= $this->DomicilioCalle . ' ' . $this->DomicilioNumero;
		
		if (!empty($this->DomicilioPiso))
			$Domicilio.= ' Piso: ' . $this->DomicilioPiso;
		if (!empty($this->DomicilioDpto))
			$Domicilio.= ' Dpto: ' . $this->DomicilioDpto;
			
		return $Domicilio;
	}
}

?>