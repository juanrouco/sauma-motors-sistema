<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Gestoria
{
	public $IdGestoria;
	public $IdMinuta;
	public $IdTipoUso;
	public $IdClienteCondominio;
	public $CondominioConyuge;
	public $PorcentajeTitularidad;
	public $NumeroCertificado;
	public $DomicilioFiscalCalle;
	public $DomicilioFiscalNumero;
	public $DomicilioFiscalPiso;
	public $DomicilioFiscalDpto;
	public $DomicilioFiscalIdLocalidad;
	public $FechaGestion;
	public $Formularios;
	public $SociedadHecho;

	public function __construct()
	{
		$this->IdGestoria 					= '';
		$this->IdMinuta 					= '';
		$this->IdTipoUso 					= '';
		$this->IdClienteCondominio 			= '';
		$this->CondominioConyuge 			= '';
		$this->PorcentajeTitularidad		= '';
		$this->NumeroCertificado 			= '';
		$this->DomicilioFiscalCalle 		= '';
		$this->DomicilioFiscalNumero 		= '';
		$this->DomicilioFiscalPiso 			= '';
		$this->DomicilioFiscalDpto 			= '';
		$this->DomicilioFiscalIdLocalidad 	= '';
		$this->FechaGestion 				= '';
		$this->SociedadHecho				= '';

		$this->Formularios = array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdGestoria 					= $arr['IdGestoria'];
		$this->IdMinuta 					= $arr['IdMinuta'];
		$this->IdTipoUso 					= $arr['IdTipoUso'];
		$this->IdClienteCondominio 			= $arr['IdClienteCondominio'];
		$this->CondominioConyuge 			= (($arr['CondominioConyuge']) == 1);
		$this->PorcentajeTitularidad		= $arr['PorcentajeTitularidad'];
		$this->NumeroCertificado 			= $arr['NumeroCertificado'];
		$this->DomicilioFiscalCalle 		= $arr['DomicilioFiscalCalle'];
		$this->DomicilioFiscalNumero 		= $arr['DomicilioFiscalNumero'];
		$this->DomicilioFiscalPiso 			= $arr['DomicilioFiscalPiso'];
		$this->DomicilioFiscalDpto 			= $arr['DomicilioFiscalDpto'];
		$this->DomicilioFiscalIdLocalidad 	= $arr['DomicilioFiscalIdLocalidad'];
		$this->FechaGestion 				= $arr['FechaGestion'];
		$this->SociedadHecho				= $arr['SociedadHecho'];
	}
	
	
	public function GetAllFormularios()
	{
		$Formularios = new Formularios();
		
		return $Formularios->GetAllByGestoria($this);
	}


	public function GetAllCedulas()
	{
		$GestoriaCedulas = new GestoriaCedulas();
		
		return $GestoriaCedulas->GetAllByGestoria($this);
	}
	
	public function GetAllSocios()
	{
		$GestoriaSocios = new GestoriaSocios();
		
		return $GestoriaSocios->GetAllByGestoria($this);
	}
}

?>