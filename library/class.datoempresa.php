<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class DatoEmpresa
{
	public $IdDatoEmpresa;
	public $RazonSocial;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $TelefonoCodigoArea2;
	public $Telefono2;
	public $CodigoAreaFax;
	public $Fax;
	public $Email;
	public $IdPais;
	public $IdProvincia;
	public $Localidad;
	public $CodigoPostal;
	public $DomicilioCalle;
	public $DomicilioNumero;
	public $DomicilioPiso;
	public $DomicilioDpto;
	public $PaginaWeb;
	public $ComercianteHabitualista;
	public $CantidadMinimaComprobantes;
	public $CantidadMinimaFormularios;

	public function ParseFromArray(array $arr)
	{
		$this->IdDatoEmpresa				= $arr['IdDatoEmpresa'];
		$this->RazonSocial					= $arr['RazonSocial'];
		$this->TelefonoCodigoArea			= $arr['TelefonoCodigoArea'];
		$this->Telefono						= $arr['Telefono'];
		$this->TelefonoCodigoArea2			= $arr['TelefonoCodigoArea2'];
		$this->Telefono2					= $arr['Telefono2'];
		$this->CodigoAreaFax				= $arr['CodigoAreaFax'];
		$this->Fax							= $arr['Fax'];
		$this->Email						= $arr['Email'];
		$this->IdPais						= $arr['IdPais'];
		$this->IdProvincia					= $arr['IdProvincia'];
		$this->IdPartido					= $arr['IdPartido'];
		$this->IdLocalidad					= $arr['IdLocalidad'];
		$this->CodigoPostal					= $arr['CodigoPostal'];
		$this->DomicilioCalle				= $arr['DomicilioCalle'];
		$this->DomicilioNumero				= $arr['DomicilioNumero'];
		$this->ComercianteHabitualista		= $arr['ComercianteHabitualista'];
		$this->DomicilioPiso				= $arr['DomicilioPiso'];
		$this->DomicilioDpto				= $arr['DomicilioDpto'];
		$this->PaginaWeb					= $arr['PaginaWeb'];
		$this->CantidadMinimaComprobantes	= $arr['CantidadMinimaComprobantes'];
		$this->CantidadMinimaFormularios	= $arr['CantidadMinimaFormularios'];
	}
}

?>