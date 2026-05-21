<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.clientecontactos.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.tiposiva.php');
require_once('class.tiposdocumento.php');
require_once('class.clavefiscaltipos.php');
require_once('class.tallerunidades.php');

class Cliente
{
	const IdClienteInterno = 257;
	const IdConsumidorFinal = 153078;

	public $IdCliente;
	public $IdTipoPersona;
	public $RazonSocial;
	public $DomicilioCalle;
	public $DomicilioNumero;
	public $DomicilioPiso;
	public $DomicilioDpto;
	public $DomicilioIdLocalidad;
	public $DomicilioCodigoPostal;
	public $DomicilioCallePostal;
	public $DomicilioNumeroPostal;
	public $DomicilioPisoPostal;
	public $DomicilioDptoPostal;
	public $DomicilioIdLocalidadPostal;
	public $DomicilioCodigoPostalPostal;
	public $NacimientoIdLocalidad;
	public $NacimientoCodigoPostal;
	public $TelefonoCodigoArea;
	public $Telefono;
	public $FaxCodigoArea;
	public $Fax;
	public $DocumentoTipo;
	public $DocumentoNumero;
	public $DocumentoExpedido;
	public $FechaNacimiento;
	public $Empresa;
	public $ClaveFiscalTipo;
	public $ClaveFiscalNumero;
	public $Email;
	public $IdVendedor;
	public $IdTipoIva;
	public $IdProfesion;
	public $IdNacionalidad;
	public $IdEstadoCivil;
	public $Nupcia;
	public $ConyugeNombre;
	public $ConyugeApellido;
	public $ConyugeDocumentoTipo;
	public $ConyugeDocumentoNumero;
	public $RepresentanteRazonSocial;
	public $RepresentanteDocumentoTipo;
	public $RepresentanteDocumentoNumero;
	public $EnteJuridicoOtorgacion;
	public $EnteJuridicoDatosInscripcion;
	public $EnteJuridicoFechaInscripcion;
	public $IdAntiguo;
	public $Contactos;
	public $PercepcionIIBB;
	public $FechaHastaPercepcion;

	public function __construct()
	{
		$this->IdCliente 					= '';
		$this->IdTipoPersona 				= '';
		$this->RazonSocial 					= '';
		$this->DomicilioCalle 				= '';
		$this->DomicilioNumero 				= '';
		$this->DomicilioPiso 				= '';
		$this->DomicilioDpto 				= '';
		$this->DomicilioIdLocalidad 		= '';
		$this->DomicilioCodigoPostal 		= '';
		$this->DomicilioCallePostal			= '';
		$this->DomicilioNumeroPostal		= '';
		$this->DomicilioPisoPostal			= '';
		$this->DomicilioDptoPostal			= '';
		$this->DomicilioIdLocalidadPostal	= '';
		$this->DomicilioCodigoPostalPostal	= '';
		$this->NacimientoIdLocalidad 		= '';
		$this->NacimientoCodigoPostal 		= '';
		$this->TelefonoCodigoArea 			= '';
		$this->Telefono 					= '';
		$this->FaxCodigoArea 				= '';
		$this->Fax 							= '';
		$this->DocumentoTipo 				= '';
		$this->DocumentoNumero 				= '';
		$this->DocumentoExpedido 			= '';
		$this->FechaNacimiento 				= '';
		$this->Empresa 						= '';
		$this->ClaveFiscalTipo 				= '';
		$this->ClaveFiscalNumero 			= '';
		$this->Email 						= '';
		$this->IdVendedor 					= '';
		$this->IdTipoIva 					= '';
		$this->IdProfesion 					= '';
		$this->IdNacionalidad 				= '';
		$this->IdEstadoCivil 				= '';
		$this->Nupcia 						= '';
		$this->ConyugeNombre 				= '';
		$this->ConyugeApellido 				= '';
		$this->ConyugeDocumentoTipo 		= '';
		$this->ConyugeDocumentoNumero 		= '';
		$this->RepresentanteRazonSocial 	= '';
		$this->RepresentanteDocumentoTipo 	= '';
		$this->RepresentanteDocumentoNumero = '';
		$this->EnteJuridicoOtorgacion 		= '';
		$this->EnteJuridicoDatosInscripcion = '';
		$this->EnteJuridicoFechaInscripcion = '';
		$this->IdAntiguo					= '';
		$this->PercepcionIIBB				= '';
		$this->FechaHastaPercepcion			= '';

		$this->Contactos = array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCliente 					= $arr['IdCliente'];
		$this->IdTipoPersona 				= $arr['IdTipoPersona'];
		$this->RazonSocial 					= $arr['RazonSocial'];
		$this->DomicilioCalle 				= $arr['DomicilioCalle'];
		$this->DomicilioNumero 				= $arr['DomicilioNumero'];
		$this->DomicilioPiso 				= $arr['DomicilioPiso'];
		$this->DomicilioDpto 				= $arr['DomicilioDpto'];
		$this->DomicilioIdLocalidad			= $arr['DomicilioIdLocalidad'];
		$this->DomicilioCodigoPostal		= $arr['DomicilioCodigoPostal'];
		$this->DomicilioCallePostal			= $arr['DomicilioCallePostal'];
		$this->DomicilioNumeroPostal		= $arr['DomicilioNumeroPostal'];
		$this->DomicilioPisoPostal			= $arr['DomicilioPisoPostal'];
		$this->DomicilioDptoPostal			= $arr['DomicilioDptoPostal'];
		$this->DomicilioIdLocalidadPostal	= $arr['DomicilioIdLocalidadPostal'];
		$this->DomicilioCodigoPostalPostal	= $arr['DomicilioCodigoPostalPostal'];
		$this->NacimientoIdLocalidad 		= $arr['NacimientoIdLocalidad'];
		$this->NacimientoCodigoPostal 		= $arr['NacimientoCodigoPostal'];
		$this->TelefonoCodigoArea 			= $arr['TelefonoCodigoArea'];
		$this->Telefono 					= $arr['Telefono'];
		$this->FaxCodigoArea 				= $arr['FaxCodigoArea'];
		$this->Fax 							= $arr['Fax'];
		$this->DocumentoTipo 				= $arr['DocumentoTipo'];
		$this->DocumentoNumero 				= $arr['DocumentoNumero'];
		$this->DocumentoExpedido 			= $arr['DocumentoExpedido'];
		$this->FechaNacimiento 				= $arr['FechaNacimiento'];
		$this->Empresa 						= $arr['Empresa'];
		$this->ClaveFiscalTipo 				= $arr['ClaveFiscalTipo'];
		$this->ClaveFiscalNumero 			= $arr['ClaveFiscalNumero'];
		$this->Email 						= $arr['Email'];
		$this->IdVendedor 					= $arr['IdVendedor'];
		$this->IdTipoIva 					= $arr['IdTipoIva'];
		$this->IdProfesion 					= $arr['IdProfesion'];
		$this->IdNacionalidad 				= $arr['IdNacionalidad'];
		$this->IdEstadoCivil 				= $arr['IdEstadoCivil'];
		$this->Nupcia 						= $arr['Nupcia'];
		$this->ConyugeNombre 				= $arr['ConyugeNombre'];
		$this->ConyugeApellido 				= $arr['ConyugeApellido'];
		$this->ConyugeDocumentoTipo 		= $arr['ConyugeDocumentoTipo'];
		$this->ConyugeDocumentoNumero 		= $arr['ConyugeDocumentoNumero'];
		$this->RepresentanteRazonSocial 	= $arr['RepresentanteRazonSocial'];
		$this->RepresentanteDocumentoTipo 	= $arr['RepresentanteDocumentoTipo'];
		$this->RepresentanteDocumentoNumero = $arr['RepresentanteDocumentoNumero'];
		$this->EnteJuridicoOtorgacion 		= $arr['EnteJuridicoOtorgacion'];
		$this->EnteJuridicoDatosInscripcion = $arr['EnteJuridicoDatosInscripcion'];
		$this->EnteJuridicoFechaInscripcion = $arr['EnteJuridicoFechaInscripcion'];
		$this->IdAntiguo					= $arr['IdAntiguo'];
		$this->PercepcionIIBB				= $arr['PercepcionIIBB'];
		$this->FechaHastaPercepcion			= $arr['FechaHastaPercepcion'];
	}
	
	public function ObtenerTipoDocumentoAfip()
	{
		if ($this->ClaveFiscalNumero && $this->ClaveFiscalNumero != '')
		{
			if ($this->ClaveFiscalTipo == ClaveFiscalTipos::Cuit)
				return ConstantesFacturaElectronica::Cuit . '';
			else
				return ConstantesFacturaElectronica::Cuil . '';
		}
		else
		{
			if ($this->DocumentoTipo == DocumentoTipos::DNI)
				return ConstantesFacturaElectronica::DNI . '';
			if ($this->DocumentoTipo == DocumentoTipos::Pasaporte)
				return ConstantesFacturaElectronica::Pasaporte . '';
			if ($this->DocumentoTipo == DocumentoTipos::Cedula)
				return ConstantesFacturaElectronica::Cedula . '';
			if ($this->DocumentoTipo == DocumentoTipos::LibretaEnrolamiento)
				return ConstantesFacturaElectronica::LE . '';
			if ($this->DocumentoTipo == DocumentoTipos::LibretaCivica)
				return ConstantesFacturaElectronica::LC . '';
			
			return ConstantesFacturaElectronica::DocumentoOtro . '';
		}
	}
	
	public function ObtenerNumeroDocumentoAfip()
	{
		if ($this->ClaveFiscalNumero && $this->ClaveFiscalNumero != '')
		{
			return str_replace('-', '', $this->ClaveFiscalNumero);
		}
		else
		{
			if ($this->DocumentoNumero && $this->DocumentoNumero != '')
				return str_replace('.', '', $this->DocumentoNumero);
			
			return '0'; // Solo para consumidor final
		}
	}
	
	
	public function GetUsuario()
	{
		if (!empty($this->Apellido) && !empty($this->Nombre))
			return $this->Apellido . ', ' . $this->Nombre;
		elseif (!empty($this->Apellido))
			return $this->Apellido;
		elseif (!empty($this->Nombre))
			return $this->Nombre;
		return $this->RazonSocial;
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


	public function GetTelefono()
	{
		$Telefono = '';

		if ((!empty($this->TelefonoCodigoArea)) && (!empty($this->Telefono)))
		{
			$Telefono.= '(' . $this->TelefonoCodigoArea . ') ';
			$Telefono.= $this->Telefono;
		}
			
		return $Telefono;
	}


	public function GetLocalidad()
	{
		$oLocalidades = new Localidades();
		
		$oLocalidad = $oLocalidades->GetById($this->DomicilioIdLocalidad);
		return $oLocalidad->Nombre;
	}

	public function GetPartido()
	{
		$oLocalidades = new Localidades();
		$oPartidos = new Partidos();
		
		$oLocalidad = $oLocalidades->GetById($this->DomicilioIdLocalidad);
		$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);
		return $oPartido->Nombre;
	}


	public function GetProvincia()
	{
		$oLocalidades = new Localidades();
		$oProvincias = new Provincias();
		
		$oLocalidad = $oLocalidades->GetById($this->DomicilioIdLocalidad);
		$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);
		return $oProvincia->Nombre;
	}


	public function GetIva()
	{
		$oTiposIva = new TiposIva();
		
		$oTipoIva = $oTiposIva->GetById($this->IdTipoIva);
		return $oTipoIva->Nombre;
	}


	public function GetDocumentoAfip()
	{	
		$oTiposDocumento = new TiposDocumento();
		if ($this->ClaveFiscalNumero)
			return ClaveFiscalTipos::GetById($this->ClaveFiscalTipo) . ': ' . $this->ClaveFiscalNumero;
		else
		{
			$oTipoDocumento = $oTiposDocumento->GetById($this->DocumentoTipo);
			return $oTipoDocumento->Codigo . ': ' . $this->DocumentoNumero;
		}
	}

	
	public function CanDelete()
	{
		if ($this->GetAllMinutas())
			return false;
		if ($this->GetAllTallerUnidades())
			return false;
		
		return true;
	}
	
	
	public function GetAllMinutas()
	{
		$Minutas = new Minutas();
		
		return $Minutas->GetAllByCliente($this);
	}
	
	
	public function GetAllTallerUnidades()
	{
		$TallerUnidades = new TallerUnidades();
		
		return $TallerUnidades->GetAllByCliente($this);
	}
	
	
	public function GetAllContactos()
	{
		$ClienteContactos = new ClienteContactos();
		
		return $ClienteContactos->GetAllByCliente($this);
	}
}

?>