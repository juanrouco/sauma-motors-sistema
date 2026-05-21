<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TallerUnidad
{
	public $IdTallerUnidad;
	public $IdMarca;
	public $IdColor;
	public $Modelo;
	public $ModeloAnio;
	public $IdCliente;
	public $Dominio;
	public $PrefijoVin;
	public $NumeroVin;
	public $NumeroMotor;
	public $FechaInicioGarantia;
	public $Concesionario;
	public $IdUnidad;
	
	public function __construct()
	{
		$this->IdTallerUnidad 		= '';
		$this->IdMarca 				= '';
		$this->IdColor 				= '';
		$this->Modelo				= '';
		$this->ModeloAnio 			= '';
		$this->IdCliente 			= '';
		$this->Dominio 				= '';
		$this->PrefijoVin			= '';
		$this->NumeroVin			= '';
		$this->NumeroMotor			= '';
		$this->FechaInicioGarantia	= '';
		$this->Concesionario		= '';
		$this->IdUnidad				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTallerUnidad		= $arr['IdTallerUnidad'];
		$this->IdMarca 				= $arr['IdMarca'];
		$this->IdColor 				= $arr['IdColor'];
		$this->Modelo				= $arr['Modelo'];
		$this->ModeloAnio 			= $arr['ModeloAnio'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->Dominio 				= $arr['Dominio'];
		$this->PrefijoVin			= $arr['PrefijoVin'];
		$this->NumeroVin 			= $arr['NumeroVin'];
		$this->NumeroMotor 			= $arr['NumeroMotor'];
		$this->FechaInicioGarantia 	= $arr['FechaInicioGarantia'];
		$this->Concesionario		= $arr['Concesionario'];
		$this->IdUnidad				= $arr['IdUnidad'];
	}
	
	public function CanDelete()
	{
		return true;
	}
}

?>