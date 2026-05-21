<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class PrendaFiador
{
	public $IdFiador;
	public $IdPrenda;
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
	public $Descripcion;
	public $Posicion;


	public function __construct()
	{
		$this->IdFiador 			= '';
		$this->IdPrenda 			= '';
		$this->RazonSocial 			= '';
		$this->DomicilioCalle 		= '';
		$this->DomicilioNumero 		= '';
		$this->DomicilioPiso 		= '';
		$this->DomicilioDpto 		= '';
		$this->DomicilioIdLocalidad = '';
		$this->DocumentoTipo 		= '';
		$this->DocumentoNumero 		= '';
		$this->FechaNacimiento 		= '';
		$this->IdProfesion 			= '';
		$this->IdNacionalidad 		= '';
		$this->IdEstadoCivil 		= '';
		$this->Descripcion 			= '';
		$this->Posicion 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFiador 			= $arr['IdFiador'];
		$this->IdPrenda 			= $arr['IdPrenda'];
		$this->RazonSocial 			= $arr['RazonSocial'];
		$this->DomicilioCalle 		= $arr['DomicilioCalle'];
		$this->DomicilioNumero 		= $arr['DomicilioNumero'];
		$this->DomicilioPiso 		= $arr['DomicilioPiso'];
		$this->DomicilioDpto 		= $arr['DomicilioDpto'];
		$this->DomicilioIdLocalidad = $arr['DomicilioIdLocalidad'];
		$this->DocumentoTipo 		= $arr['DocumentoTipo'];
		$this->DocumentoNumero 		= $arr['DocumentoNumero'];
		$this->FechaNacimiento 		= $arr['FechaNacimiento'];
		$this->IdProfesion 			= $arr['IdProfesion'];
		$this->IdNacionalidad 		= $arr['IdNacionalidad'];
		$this->IdEstadoCivil 		= $arr['IdEstadoCivil'];
		$this->Descripcion 			= $arr['Descripcion'];
		$this->Posicion 			= $arr['Posicion'];
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