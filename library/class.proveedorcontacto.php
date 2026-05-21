<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ProveedorContacto
{
	public $IdContacto;
	public $IdProveedor;
	public $Nombre;
	public $Apellido;
	public $IdDepartamento;
	public $IdCargo;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $TelefonoCodigoArea2;
	public $Telefono2;
	public $PINBlackberry;
	public $Email;
	public $Email2;
	public $IdSkype;
	public $Msn;
	public $FechaNacimiento;
	public $IdEstadoCivil;
	public $Observaciones;
	
	
	public function __construct()
	{
		$this->Grupos = array();
	}
	
	public function ParseFromArray(array $arr)
	{
		$this->IdContacto 			= $arr['IdContacto'];
		$this->IdProveedor 			= $arr['IdProveedor'];
		$this->Nombre 				= stripslashes($arr['Nombre']);
		$this->Apellido				= stripslashes($arr['Apellido']);		
		$this->IdDepartamento		= $arr['IdDepartamento'];
		$this->IdCargo				= $arr['IdCargo'];
		$this->TelefonoCodigoArea 	= stripslashes($arr['TelefonoCodigoArea']);
		$this->Telefono 			= stripslashes($arr['Telefono']);
		$this->TelefonoCodigoArea2 	= stripslashes($arr['TelefonoCodigoArea2']);
		$this->Telefono2 			= stripslashes($arr['Telefono2']);
		$this->PINBlackberry		= stripslashes($arr['PINBlackberry']);
		$this->Email 				= stripslashes($arr['Email']);
		$this->Email2 				= stripslashes($arr['Email2']);
		$this->IdSkype 				= stripslashes($arr['IdSkype']);		
		$this->Msn			 		= stripslashes($arr['Msn']);
		$this->FechaNacimiento		= stripslashes($arr['FechaNacimiento']);
		$this->IdEstadoCivil 		= $arr['IdEstadoCivil'];
		$this->Observaciones 		= stripslashes($arr['Observaciones']);
	}

	
	public function GetCountAccesos(array $filter = NULL)
	{
		$LogAccesos = new LogAccesos();
		
		return $LogAccesos->GetCountByProveedor($this, $filter);
	}					


	public function GetAllAccesos(array $filter = NULL, Page $oPage = NULL)
	{
		$LogAccesos = new LogAccesos();
		
		return $LogAccesos->GetAllByProveedor($this, $filter, $oPage);
	}					


	public function GetCountVisitas(array $filter = NULL)
	{
		$LogVisitas = new LogVisitas();
		
		return $LogVisitas->GetCountByProveedor($this, $filter);
	}					


	public function GetAllVisitas(array $filter = NULL, Page $oPage = NULL)
	{
		$LogVisitas = new LogVisitas();
		
		return $LogVisitas->GetAllByProveedor($this, $filter, $oPage);
	}					


	public function GetCountBusquedas(array $filter = NULL)
	{
		$LogBusquedas = new LogBusquedas();
		
		return $LogBusquedas->GetCountByProveedor($this, $filter);
	}					


	public function GetAllBusquedas(array $filter = NULL, Page $oPage = NULL)
	{
		$LogBusquedas = new LogBusquedas();
		
		return $LogBusquedas->GetAllByProveedor($this, $filter, $oPage);
	}	
}

?>