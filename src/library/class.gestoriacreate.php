<?php

require_once('class.acreedores.php');
require_once('class.gestorias.php');
require_once('class.clientes.php');
require_once('class.formularios.php');
require_once('class.gestorias.php');
require_once('class.gestoriacedulas.php');
require_once('class.prendas.php');
require_once('class.prendafiadores.php');
require_once('class.prendaconyuges.php');

abstract class GestoriaCreate
{
	const ConyugeTitular	= 1;
	const ConyugeCondominio	= 2;
	
	static public $UniqueId;
	static public $IdMinuta;
	static public $IdTipoUso;
	static public $IdClienteCondominio;
	static public $ClienteCondominio;
	static public $CondominioConyuge;
	static public $PorcentajeTitularidad;
	static public $NumeroCertificado;
	static public $DomicilioFiscalCalle;
	static public $DomicilioFiscalNumero;
	static public $DomicilioFiscalPiso;
	static public $DomicilioFiscalDpto;
	static public $DomicilioFiscalIdLocalidad;
	static public $DomicilioFiscalLocalidad;
	static public $DomicilioFiscalCodigoPostal;
	static public $FechaGestion;
	static public $Acreedor;
	static public $IdAcreedor;
	static public $FinanciacionCapital;
	static public $CantidadCuotas;
	static public $ImporteCuota;
	static public $FechaVencimientoPrimerCuota;
	static public $TasaNominal;
	static public $TasaEfectiva;
	static public $CostoFinancieroTotal;
	static public $Conyuge;
	static public $Observaciones;
	static public $SociedadHecho;
	static private $Formularios;
	static private $Cedulas;
	static private $Socios;
	static private $Fiadores;
	static private $Conyuges;
	static private $Saved;
	static private $IdGestoria;
	static private $IdPrenda;
	static private $Estado;

		
	static function Initialize()
	{
		GestoriaCreate::$UniqueId						= md5(rand(0, 65535));
		GestoriaCreate::$IdMinuta						= '';
		GestoriaCreate::$IdTipoUso						= '';
		GestoriaCreate::$IdClienteCondominio			= '';
		GestoriaCreate::$ClienteCondominio				= '';
		GestoriaCreate::$CondominioConyuge				= false;
		GestoriaCreate::$PorcentajeTitularidad			= 100;
		GestoriaCreate::$NumeroCertificado				= '';
		GestoriaCreate::$DomicilioFiscalCalle			= '';
		GestoriaCreate::$DomicilioFiscalNumero			= '';
		GestoriaCreate::$DomicilioFiscalPiso			= '';
		GestoriaCreate::$DomicilioFiscalDpto			= '';
		GestoriaCreate::$DomicilioFiscalIdLocalidad		= '';
		GestoriaCreate::$DomicilioFiscalLocalidad		= '';
		GestoriaCreate::$DomicilioFiscalCodigoPostal	= '';
		GestoriaCreate::$FechaGestion					= date("d-m-Y");
		GestoriaCreate::$Acreedor						= '';
		GestoriaCreate::$IdAcreedor						= '';
		GestoriaCreate::$FinanciacionCapital			= '';
		GestoriaCreate::$CantidadCuotas					= '';
		GestoriaCreate::$ImporteCuota					= '';
		GestoriaCreate::$FechaVencimientoPrimerCuota	= date("d-m-Y");
		GestoriaCreate::$TasaNominal					= '';
		GestoriaCreate::$TasaEfectiva					= '';
		GestoriaCreate::$CostoFinancieroTotal			= '';
		GestoriaCreate::$Conyuge						= false;
		GestoriaCreate::$Observaciones					= '';
		GestoriaCreate::$Formularios					= array();
		GestoriaCreate::$Cedulas						= array();
		GestoriaCreate::$Socios							= array();
		GestoriaCreate::$Fiadores						= array();
		GestoriaCreate::$Conyuges						= array();
		GestoriaCreate::$Estado							= '';
		GestoriaCreate::$Saved							= false;
		GestoriaCreate::$IdGestoria						= '';
		GestoriaCreate::$SociedadHecho					= false;	
		
		if (!isset($_SESSION['Formularios']))
		{
			$_SESSION['UniqueId']						= &GestoriaCreate::$UniqueId;
			$_SESSION['IdMinuta']						= &GestoriaCreate::$IdMinuta;
			$_SESSION['IdTipoUso']						= &GestoriaCreate::$IdTipoUso;
			$_SESSION['IdClienteCondominio']			= &GestoriaCreate::$IdClienteCondominio;
			$_SESSION['ClienteCondominio']				= &GestoriaCreate::$ClienteCondominio;
			$_SESSION['CondominioConyuge']				= &GestoriaCreate::$CondominioConyuge;
			$_SESSION['PorcentajeTitularidad']			= &GestoriaCreate::$PorcentajeTitularidad;
			$_SESSION['NumeroCertificado']				= &GestoriaCreate::$NumeroCertificado;
			$_SESSION['DomicilioFiscalCalle']			= &GestoriaCreate::$DomicilioFiscalCalle;
			$_SESSION['DomicilioFiscalNumero']			= &GestoriaCreate::$DomicilioFiscalNumero;
			$_SESSION['DomicilioFiscalPiso']			= &GestoriaCreate::$DomicilioFiscalPiso;
			$_SESSION['DomicilioFiscalDpto']			= &GestoriaCreate::$DomicilioFiscalDpto;
			$_SESSION['DomicilioFiscalIdLocalidad']		= &GestoriaCreate::$DomicilioFiscalIdLocalidad;
			$_SESSION['DomicilioFiscalLocalidad']		= &GestoriaCreate::$DomicilioFiscalLocalidad;
			$_SESSION['DomicilioFiscalCodigoPostal']	= &GestoriaCreate::$DomicilioFiscalCodigoPostal;
			$_SESSION['FechaGestion']					= &GestoriaCreate::$FechaGestion;
			$_SESSION['Acreedor']						= &GestoriaCreate::$Acreedor;
			$_SESSION['IdAcreedor']						= &GestoriaCreate::$IdAcreedor;
			$_SESSION['FinanciacionCapital']			= &GestoriaCreate::$FinanciacionCapital;
			$_SESSION['CantidadCuotas']					= &GestoriaCreate::$CantidadCuotas;
			$_SESSION['ImporteCuota']					= &GestoriaCreate::$ImporteCuota;
			$_SESSION['FechaVencimientoPrimerCuota']	= &GestoriaCreate::$FechaVencimientoPrimerCuota;
			$_SESSION['TasaNominal']					= &GestoriaCreate::$TasaNominal;
			$_SESSION['TasaEfectiva']					= &GestoriaCreate::$TasaEfectiva;
			$_SESSION['CostoFinancieroTotal']			= &GestoriaCreate::$CostoFinancieroTotal;
			$_SESSION['Conyuge']						= &GestoriaCreate::$Conyuge;
			$_SESSION['Observaciones']					= &GestoriaCreate::$Observaciones;
			$_SESSION['SociedadHecho']					= &GestoriaCreate::$SociedadHecho;
			$_SESSION['Formularios']					= &GestoriaCreate::$Formularios;
			$_SESSION['Cedulas']						= &GestoriaCreate::$Cedulas;
			$_SESSION['Socios']							= &GestoriaCreate::$Socios;
			$_SESSION['Fiadores']						= &GestoriaCreate::$Fiadores;
			$_SESSION['Conyuges']						= &GestoriaCreate::$Conyuges;
			$_SESSION['Estado']							= &GestoriaCreate::$Estado;
			$_SESSION['Saved']							= &GestoriaCreate::$Saved;
			$_SESSION['IdGestoria']						= &GestoriaCreate::$IdGestoria;
			$_SESSION['IdPrenda']						= &GestoriaCreate::$IdPrenda;
			
			return;
		}
		
		GestoriaCreate::$UniqueId						= &$_SESSION['UniqueId'];
		GestoriaCreate::$IdMinuta						= &$_SESSION['IdMinuta'];
		GestoriaCreate::$IdTipoUso						= &$_SESSION['IdTipoUso'];
		GestoriaCreate::$IdClienteCondominio			= &$_SESSION['IdClienteCondominio'];
		GestoriaCreate::$ClienteCondominio				= &$_SESSION['ClienteCondominio'];
		GestoriaCreate::$CondominioConyuge				= &$_SESSION['CondominioConyuge'];
		GestoriaCreate::$PorcentajeTitularidad			= &$_SESSION['PorcentajeTitularidad'];
		GestoriaCreate::$NumeroCertificado				= &$_SESSION['NumeroCertificado'];
		GestoriaCreate::$DomicilioFiscalNumero			= &$_SESSION['DomicilioFiscalNumero'];
		GestoriaCreate::$DomicilioFiscalCalle			= &$_SESSION['DomicilioFiscalCalle'];
		GestoriaCreate::$DomicilioFiscalPiso			= &$_SESSION['DomicilioFiscalPiso'];
		GestoriaCreate::$DomicilioFiscalDpto			= &$_SESSION['DomicilioFiscalDpto'];
		GestoriaCreate::$DomicilioFiscalIdLocalidad		= &$_SESSION['DomicilioFiscalIdLocalidad'];
		GestoriaCreate::$DomicilioFiscalLocalidad		= &$_SESSION['DomicilioFiscalLocalidad'];
		GestoriaCreate::$DomicilioFiscalCodigoPostal	= &$_SESSION['DomicilioFiscalCodigoPostal'];
		GestoriaCreate::$FechaGestion					= &$_SESSION['FechaGestion'];
		GestoriaCreate::$Acreedor						= &$_SESSION['Acreedor'];
		GestoriaCreate::$IdAcreedor						= &$_SESSION['IdAcreedor'];
		GestoriaCreate::$FinanciacionCapital			= &$_SESSION['FinanciacionCapital'];
		GestoriaCreate::$CantidadCuotas					= &$_SESSION['CantidadCuotas'];
		GestoriaCreate::$ImporteCuota					= &$_SESSION['ImporteCuota'];
		GestoriaCreate::$FechaVencimientoPrimerCuota	= &$_SESSION['FechaVencimientoPrimerCuota'];
		GestoriaCreate::$TasaNominal					= &$_SESSION['TasaNominal'];
		GestoriaCreate::$TasaEfectiva					= &$_SESSION['TasaEfectiva'];
		GestoriaCreate::$CostoFinancieroTotal			= &$_SESSION['CostoFinancieroTotal'];
		GestoriaCreate::$Conyuge						= &$_SESSION['Conyuge'];
		GestoriaCreate::$Observaciones					= &$_SESSION['Observaciones'];
		GestoriaCreate::$SociedadHecho					= &$_SESSION['SociedadHecho'];
		GestoriaCreate::$Formularios					= &$_SESSION['Formularios'];
		GestoriaCreate::$Cedulas						= &$_SESSION['Cedulas'];
		GestoriaCreate::$Socios							= &$_SESSION['Socios'];
		GestoriaCreate::$Fiadores						= &$_SESSION['Fiadores'];
		GestoriaCreate::$Conyuges						= &$_SESSION['Conyuges'];
		GestoriaCreate::$Estado							= &$_SESSION['Estado'];
		GestoriaCreate::$Saved							= &$_SESSION['Saved'];
		GestoriaCreate::$IdGestoria						= &$_SESSION['IdGestoria'];
		GestoriaCreate::$IdPrenda						= &$_SESSION['IdPrenda'];
	}


	static function GetIdGestoria()
	{
		return GestoriaCreate::$IdGestoria;
	}


	static function GetIdPrenda()
	{
		return GestoriaCreate::$IdPrenda;
	}


	static function GetAllFormularios()
	{
		return GestoriaCreate::$Formularios;
	}


	static function GetAllCedulas()
	{
		return GestoriaCreate::$Cedulas;
	}
	
	static function GetAllSocios()
	{
		return GestoriaCreate::$Socios;
	}

	static function GetAllFiadores()
	{
		return GestoriaCreate::$Fiadores;
	}


	static function GetAllConyuges()
	{
		return GestoriaCreate::$Conyuges;
	}


	static function ClearAll()
	{
		GestoriaCreate::$UniqueId						= md5(rand(0, 65535));
		GestoriaCreate::$IdMinuta						= '';
		GestoriaCreate::$IdTipoUso						= '';
		GestoriaCreate::$IdClienteCondominio			= '';
		GestoriaCreate::$ClienteCondominio				= '';
		GestoriaCreate::$CondominioConyuge				= false;
		GestoriaCreate::$PorcentajeTitularidad			= 100;
		GestoriaCreate::$NumeroCertificado				= '';
		GestoriaCreate::$DomicilioFiscalCalle			= '';
		GestoriaCreate::$DomicilioFiscalNumero			= '';
		GestoriaCreate::$DomicilioFiscalPiso			= '';
		GestoriaCreate::$DomicilioFiscalDpto			= '';
		GestoriaCreate::$DomicilioFiscalIdLocalidad		= '';
		GestoriaCreate::$DomicilioFiscalLocalidad		= '';
		GestoriaCreate::$DomicilioFiscalCodigoPostal	= '';
		GestoriaCreate::$FechaGestion					= date("d-m-Y");
		GestoriaCreate::$Acreedor						= '';
		GestoriaCreate::$IdAcreedor						= '';
		GestoriaCreate::$FinanciacionCapital			= '';
		GestoriaCreate::$CantidadCuotas					= '';
		GestoriaCreate::$ImporteCuota					= '';
		GestoriaCreate::$FechaVencimientoPrimerCuota	= date("d-m-Y");
		GestoriaCreate::$TasaNominal					= '';
		GestoriaCreate::$TasaEfectiva					= '';
		GestoriaCreate::$CostoFinancieroTotal			= '';
		GestoriaCreate::$Conyuge						= false;
		GestoriaCreate::$Observaciones					= '';
		GestoriaCreate::$SociedadHecho					= false;
		GestoriaCreate::$Formularios					= array();
		GestoriaCreate::$Cedulas						= array();
		GestoriaCreate::$Socios							= array();
		GestoriaCreate::$Fiadores						= array();
		GestoriaCreate::$Conyuges						= array();
		GestoriaCreate::$Estado							= '';
		GestoriaCreate::$Saved							= false;
		
		return true;
	}

	
	static function GetFormulariosCount()
	{
		return count(GestoriaCreate::$Formularios);
	}
	

	static function GetFormulario($IdTipoFormulario)
	{
		foreach (GestoriaCreate::$Formularios as $oFormulario)
			if ($oFormulario->IdTipoFormulario == $IdTipoFormulario)
				return $oFormulario;
				
		return false;
	}


	static function AddFormulario(Formulario $oFormulario)
	{
		for ($i=0; $i<count(GestoriaCreate::$Formularios); $i++)
			if (GestoriaCreate::$Formularios[$i]->IdTipoFormulario == $oFormulario->IdTipoFormulario)
				break;

		/* si no lo encuentra lo agregamos */
		if ($i >= count(GestoriaCreate::$Formularios))
			GestoriaCreate::$Formularios[] = $oFormulario;
		else
			GestoriaCreate::$Formularios[$i] = $oFormulario;
		
		return GestoriaCreate::GetFormulariosCount();
	}


	static function RemoveFormulario($IdTipoFormulario)
	{
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Formularios); $i++)
			if (GestoriaCreate::$Formularios[$i]->IdTipoFormulario == $IdTipoFormulario)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Formularios))
			return false;
		
		array_splice(GestoriaCreate::$Formularios, $i, 1);
		
		for ($i=0; $i<count(GestoriaCreate::$Formularios); $i++)
			GestoriaCreate::$Formularios[$i]->Id = $i+1;

		return true;		
	}


	static function RemoveAllFormularios()
	{
		GestoriaCreate::$Formularios = array();		
		
		return true;		
	}


	static function GetCedulasCount()
	{
		return count(GestoriaCreate::$Cedulas);
	}
	

	static function GetCedula($Id)
	{		
		foreach (GestoriaCreate::$Cedulas as $oCedula)
			if ($oCedula->Id == $Id)
				return $oCedula;

		return false;
	}
	

	static function AddCedula(Cedula $oCedula)
	{		
		/* eliminamos los ecos producidos al realizar un Refresh de la página y */
		/* reenviar el mismo juego de datos */
		foreach (GestoriaCreate::$Cedulas as $obj)
			if ($oCedula->Code == $obj->Code)
				return $obj->Id;

		GestoriaCreate::$Cedulas[] = $oCedula;

		return GestoriaCreate::GetCedulasCount();
	}


	static function EditCedula(Cedula $oCedula)
	{		
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Cedulas); $i++)
			if (GestoriaCreate::$Cedulas[$i]->Id == $oCedula->Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Cedulas))
			return false;
		
		/* reemplazamos el objeto */
		GestoriaCreate::$Cedulas[$i] = $oCedula;
		
		return true;		
	}


	static function RemoveCedula($Id)
	{
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Cedulas); $i++)
			if (GestoriaCreate::$Cedulas[$i]->Id == $Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Cedulas))
			return false;
		
		array_splice(GestoriaCreate::$Cedulas, $i, 1);
		
		for ($i=0; $i<count(GestoriaCreate::$Cedulas); $i++)
			GestoriaCreate::$Cedulas[$i]->Id = $i+1;

		return true;		
	}


	static function RemoveAllCedulas()
	{
		GestoriaCreate::$Cedulas = array();		
		
		return true;		
	}
	
	static function GetSociosCount()
	{
		return count(GestoriaCreate::$Socios);
	}
	

	static function GetSocio($Id)
	{		
		foreach (GestoriaCreate::$Socios as $oSocio)
			if ($oSocio->Id == $Id)
				return $oSocio;

		return false;
	}
	

	static function AddSocio(Socio $oSocio)
	{		
		/* eliminamos los ecos producidos al realizar un Refresh de la página y */
		/* reenviar el mismo juego de datos */
		foreach (GestoriaCreate::$Socios as $obj)
			if ($oSocio->Code == $obj->Code)
				return $obj->Id;

		GestoriaCreate::$Socios[] = $oSocio;

		return GestoriaCreate::GetSociosCount();
	}


	static function EditSocio(Socio $oSocio)
	{		
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Socios); $i++)
			if (GestoriaCreate::$Socios[$i]->Id == $oSocio->Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Socios))
			return false;
		
		/* reemplazamos el objeto */
		GestoriaCreate::$Socios[$i] = $oSocio;
		
		return true;		
	}


	static function RemoveSocio($Id)
	{
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Socios); $i++)
			if (GestoriaCreate::$Socios[$i]->Id == $Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Socios))
			return false;
		
		array_splice(GestoriaCreate::$Socios, $i, 1);
		
		for ($i=0; $i<count(GestoriaCreate::$Socios); $i++)
			GestoriaCreate::$Socios[$i]->Id = $i+1;

		return true;		
	}


	static function RemoveAllSocios()
	{
		GestoriaCreate::$Socios = array();		
		
		return true;		
	}


	static function GetFiadoresCount()
	{
		return count(GestoriaCreate::$Fiadores);
	}
	

	static function GetFiador($Id)
	{		
		foreach (GestoriaCreate::$Fiadores as $oFiador)
			if ($oFiador->Id == $Id)
				return $oFiador;

		return false;
	}
	

	static function AddFiador(Fiador $oFiador)
	{		
		/* eliminamos los ecos producidos al realizar un Refresh de la página y */
		/* reenviar el mismo juego de datos */
		foreach (GestoriaCreate::$Fiadores as $obj)
			if ($oFiador->Code == $obj->Code)
				return $obj->Id;

		GestoriaCreate::$Fiadores[] = $oFiador;

		return GestoriaCreate::GetFiadoresCount();
	}


	static function EditFiador(Fiador $oFiador)
	{		
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Fiadores); $i++)
			if (GestoriaCreate::$Fiadores[$i]->Id == $oFiador->Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Fiadores))
			return false;
		
		/* reemplazamos el objeto */
		GestoriaCreate::$Fiadores[$i] = $oFiador;
		
		return true;		
	}


	static function RemoveFiador($Id)
	{
		/* buscamos el id correspondiente */
		for ($i=0; $i<count(GestoriaCreate::$Fiadores); $i++)
			if (GestoriaCreate::$Fiadores[$i]->Id == $Id)
				break;

		/* si no lo encuentra */
		if ($i >= count(GestoriaCreate::$Fiadores))
			return false;
		
		array_splice(GestoriaCreate::$Fiadores, $i, 1);
		
		for ($i=0; $i<count(GestoriaCreate::$Fiadores); $i++)
			GestoriaCreate::$Fiadores[$i]->Id = $i+1;

		return true;		
	}


	static function RemoveAllFiadores()
	{
		GestoriaCreate::$Fiadores = array();		
		
		return true;		
	}


	static function GetConyugesCount()
	{
		return count(GestoriaCreate::$Conyuges);
	}
	

	static function GetConyuge($IdTipoConyuge)
	{
		foreach (GestoriaCreate::$Conyuges as $oConyuge)
			if ($oConyuge->IdTipoConyuge == $IdTipoConyuge)
				return $oConyuge;
				
		return false;
	}


	static function AddConyuge(Conyuge $oConyuge)
	{
		for ($i=0; $i<count(GestoriaCreate::$Conyuges); $i++)
			if (GestoriaCreate::$Conyuges[$i]->IdTipoConyuge == $oConyuge->IdTipoConyuge)
				break;

		/* si no lo encuentra lo agregamos */
		if ($i >= count(GestoriaCreate::$Conyuges))
			GestoriaCreate::$Conyuges[] = $oConyuge;
		else
			GestoriaCreate::$Conyuges[$i] = $oConyuge;
		
		return GestoriaCreate::GetConyugesCount();
	}


	static function Save()
	{
		$oGestorias 		= new Gestorias();
		$oFormularios		= new Formularios();
		$oGestoriaCedulas	= new GestoriaCedulas();
		$oGestoriaSocios	= new GestoriaSocios();
		$oPrendas			= new Prendas();
		$oPrendaFiadores	= new PrendaFiadores();
		$oPrendaConyuges	= new PrendaConyuges();
				
		/* verifica si fue guardado anteriormente evitando de esta forma que los Refresh dupliquen los datos */
		if (GestoriaCreate::$Saved)
			return true;

		/* comenzamos una transaccion */
		if (!DBAccess::Begin())
			return false;

		$oGestoria = new Gestoria();
		$oGestoria->IdMinuta					= GestoriaCreate::$IdMinuta;
		$oGestoria->IdTipoUso					= GestoriaCreate::$IdTipoUso;
		$oGestoria->IdClienteCondominio			= GestoriaCreate::$IdClienteCondominio;
		$oGestoria->CondominioConyuge			= GestoriaCreate::$CondominioConyuge;
		$oGestoria->PorcentajeTitularidad		= GestoriaCreate::$PorcentajeTitularidad;
		$oGestoria->NumeroCertificado			= GestoriaCreate::$NumeroCertificado;
		$oGestoria->DomicilioFiscalCalle		= GestoriaCreate::$DomicilioFiscalCalle;
		$oGestoria->DomicilioFiscalNumero		= GestoriaCreate::$DomicilioFiscalNumero;
		$oGestoria->DomicilioFiscalPiso			= GestoriaCreate::$DomicilioFiscalPiso;
		$oGestoria->DomicilioFiscalDpto			= GestoriaCreate::$DomicilioFiscalDpto;
		$oGestoria->DomicilioFiscalIdLocalidad	= GestoriaCreate::$DomicilioFiscalIdLocalidad;
		$oGestoria->FechaGestion				= GestoriaCreate::$FechaGestion;
		$oGestoria->SociedadHecho				= GestoriaCreate::$SociedadHecho;

		if (!$oGestoria = $oGestorias->Create($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* almacenamos el Id de gestoria generado por si se requiere cancelar la gestion */
		GestoriaCreate::$IdGestoria = $oGestoria->IdGestoria;

		/* guardamos id de la gestoria en los formularios utilizados */	
		foreach (GestoriaCreate::$Formularios as $oFormulario)
		{
			if (is_object($oFormulario))
			{
				$oFormulario->IdEstado 		= FormularioEstados::Utilizado;
				$oFormulario->IdGestoria 	= $oGestoria->IdGestoria;
				$oFormulario->IdDeclaracion = '';
					
				if (!$oFormularios->Update($oFormulario))
				{
					DBAccess::Rollback();
					return false;
				}
			}
		}

		/* guardamos cada cedula solicitada */	
		foreach (GestoriaCreate::$Cedulas as $oCedula)
		{
			$oGestoriaCedula = new GestoriaCedula();
			$oGestoriaCedula->IdGestoria		= $oGestoria->IdGestoria;
			$oGestoriaCedula->Nombre 			= $oCedula->Nombre;
			$oGestoriaCedula->Apellido 			= $oCedula->Apellido;
			$oGestoriaCedula->DocumentoTipo 	= $oCedula->DocumentoTipo;
			$oGestoriaCedula->DocumentoNumero 	= $oCedula->DocumentoNumero;
				
			if (!$oGestoriaCedulas->Create($oGestoriaCedula))
			{
				DBAccess::Rollback();
				return false;
			}
		}
		
		/* guardamos cada socio */	
		foreach (GestoriaCreate::$Socios as $oSocio)
		{
			$oGestoriaSocio = new GestoriaSocio();
			$oGestoriaSocio->IdGestoria		= $oGestoria->IdGestoria;
			$oGestoriaSocio->IdCliente		= $oSocio->IdCliente;
			$oGestoriaSocio->Porcentaje		= $oSocio->Porcentaje;
				
			if (!$oGestoriaSocios->Create($oGestoriaSocio))
			{
				DBAccess::Rollback();
				return false;
			}
		}
		
		/* guaardamos los datos de la prenda en caso de que sea necesario */
		if (GestoriaCreate::$FinanciacionCapital != '')
		{
			$oPrenda = new Prenda();
			$oPrenda->IdGestoria					= $oGestoria->IdGestoria;
			$oPrenda->IdAcreedor					= GestoriaCreate::$IdAcreedor;
			$oPrenda->FinanciacionCapital			= GestoriaCreate::$FinanciacionCapital;
			$oPrenda->CantidadCuotas				= GestoriaCreate::$CantidadCuotas;
			$oPrenda->ImporteCuota					= GestoriaCreate::$ImporteCuota;
			$oPrenda->FechaVencimientoPrimerCuota	= GestoriaCreate::$FechaVencimientoPrimerCuota;
			$oPrenda->TasaNominal					= GestoriaCreate::$TasaNominal;
			$oPrenda->TasaEfectiva					= GestoriaCreate::$TasaEfectiva;
			$oPrenda->CostoFinancieroTotal			= GestoriaCreate::$CostoFinancieroTotal;
			$oPrenda->Observaciones					= GestoriaCreate::$Observaciones;
	
			if (!$oPrenda = $oPrendas->Create($oPrenda))
			{
				DBAccess::Rollback();
				return false;
			}

			/* almacenamos el Id de prenda generado por si se requiere cancelar la gestion */
			GestoriaCreate::$IdPrenda = $oPrenda->IdPrenda;
	
			/* guardamos cada fiador de la prenda */	
			foreach (GestoriaCreate::$Fiadores as $oFiador)
			{
				$oPrendaFiador = new PrendaFiador();
				$oPrendaFiador->IdPrenda 				= $oPrenda->IdPrenda;
				$oPrendaFiador->RazonSocial 			= $oFiador->RazonSocial;
				$oPrendaFiador->DomicilioCalle 			= $oFiador->DomicilioCalle;
				$oPrendaFiador->DomicilioNumero 		= $oFiador->DomicilioNumero;
				$oPrendaFiador->DomicilioPiso 			= $oFiador->DomicilioPiso;
				$oPrendaFiador->DomicilioDpto 			= $oFiador->DomicilioDpto;
				$oPrendaFiador->DomicilioIdLocalidad 	= $oFiador->DomicilioIdLocalidad;
				$oPrendaFiador->DocumentoTipo 			= $oFiador->DocumentoTipo;
				$oPrendaFiador->DocumentoNumero 		= $oFiador->DocumentoNumero;
				$oPrendaFiador->FechaNacimiento 		= $oFiador->FechaNacimiento;
				$oPrendaFiador->IdProfesion 			= $oFiador->IdProfesion;
				$oPrendaFiador->IdNacionalidad 			= $oFiador->IdNacionalidad;
				$oPrendaFiador->IdEstadoCivil 			= $oFiador->IdEstadoCivil;
				$oPrendaFiador->Descripcion 			= $oFiador->Descripcion;
				$oPrendaFiador->Posicion 				= $oFiador->Posicion;
					
				if (!$oPrendaFiadores->Create($oPrendaFiador))
				{
					DBAccess::Rollback();
					return false;
				}
			}

			/* guardamos el conyuge del titular y del condominio en caso de que existieran */	
			foreach (GestoriaCreate::$Conyuges as $oConyuge)
			{
				$oPrendaConyuge = new PrendaConyuge();
				$oPrendaConyuge->IdPrenda 				= $oPrenda->IdPrenda;
				$oPrendaConyuge->IdTipoConyuge 			= $oConyuge->IdTipoConyuge;
				$oPrendaConyuge->RazonSocial 			= $oConyuge->RazonSocial;
				$oPrendaConyuge->DomicilioCalle 		= $oConyuge->DomicilioCalle;
				$oPrendaConyuge->DomicilioNumero 		= $oConyuge->DomicilioNumero;
				$oPrendaConyuge->DomicilioPiso 			= $oConyuge->DomicilioPiso;
				$oPrendaConyuge->DomicilioDpto 			= $oConyuge->DomicilioDpto;
				$oPrendaConyuge->DomicilioIdLocalidad 	= $oConyuge->DomicilioIdLocalidad;
				$oPrendaConyuge->DocumentoTipo 			= $oConyuge->DocumentoTipo;
				$oPrendaConyuge->DocumentoNumero 		= $oConyuge->DocumentoNumero;
				$oPrendaConyuge->FechaNacimiento 		= $oConyuge->FechaNacimiento;
				$oPrendaConyuge->IdProfesion 			= $oConyuge->IdProfesion;
				$oPrendaConyuge->IdNacionalidad 		= $oConyuge->IdNacionalidad;
				$oPrendaConyuge->IdEstadoCivil 			= $oConyuge->IdEstadoCivil;
					
				if (!$oPrendaConyuges->Create($oPrendaConyuge))
				{
					DBAccess::Rollback();
					return false;
				}
			}
		}
		
		/* concluimos la transacción */		
		if (!DBAccess::Commit())
			return false;
			
		GestoriaCreate::$Saved = true;
		
		return true;
	}


	static function Update()
	{
		$oGestorias 		= new Gestorias();
		$oFormularios		= new Formularios();
		$oGestoriaCedulas	= new GestoriaCedulas();
		$oGestoriaSocios	= new GestoriaSocios();
		$oPrendas			= new Prendas();
		$oPrendaFiadores	= new PrendaFiadores();
		$oPrendaConyuges	= new PrendaConyuges();
				
		/* verifica si fue guardado anteriormente evitando de esta forma que los Refresh dupliquen los datos */
		if (GestoriaCreate::$Saved)
			return true;

		/* comenzamos una transaccion */
		if (!DBAccess::Begin())
			return false;

		/* obtenemos los datos de la gestoria */
		if (!$oGestoria = $oGestorias->GetById(GestoriaCreate::$IdGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* cancelamos la gestoria en los formularios */
		if (!$oFormularios->LiberarByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* eliminamos las cedulas solicitadas en caso de que existieran */
		if (!$oGestoriaCedulas->DeleteByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}
		
		/* eliminamos los socios en caso de que existieran */
		if (!$oGestoriaSocios->DeleteByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}
		
		/* eliminamos la prenda en caso de que existiera */
		if ($oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
		{
			if (!$oPrendas->Delete($oPrenda->IdPrenda))
			{
				DBAccess::Rollback();
				return false;
			}
		}

		$oGestoria->IdMinuta					= GestoriaCreate::$IdMinuta;
		$oGestoria->IdTipoUso					= GestoriaCreate::$IdTipoUso;
		$oGestoria->IdClienteCondominio			= GestoriaCreate::$IdClienteCondominio;
		$oGestoria->CondominioConyuge			= GestoriaCreate::$CondominioConyuge;
		$oGestoria->PorcentajeTitularidad		= GestoriaCreate::$PorcentajeTitularidad;
		$oGestoria->NumeroCertificado			= GestoriaCreate::$NumeroCertificado;
		$oGestoria->DomicilioFiscalCalle		= GestoriaCreate::$DomicilioFiscalCalle;
		$oGestoria->DomicilioFiscalNumero		= GestoriaCreate::$DomicilioFiscalNumero;
		$oGestoria->DomicilioFiscalPiso			= GestoriaCreate::$DomicilioFiscalPiso;
		$oGestoria->DomicilioFiscalDpto			= GestoriaCreate::$DomicilioFiscalDpto;
		$oGestoria->DomicilioFiscalIdLocalidad	= GestoriaCreate::$DomicilioFiscalIdLocalidad;
		$oGestoria->FechaGestion				= GestoriaCreate::$FechaGestion;
		$oGestoria->SociedadHecho				= GestoriaCreate::$SociedadHecho;

		/* actualizamos el registro */
		if (!$oGestoria = $oGestorias->Update($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* guardamos id de la gestoria en los formularios utilizados */	
		foreach (GestoriaCreate::$Formularios as $oFormulario)
		{
			if (is_object($oFormulario))
			{
				$oFormulario->IdEstado 		= FormularioEstados::Utilizado;
				$oFormulario->IdGestoria 	= $oGestoria->IdGestoria;
					
				if (!$oFormularios->Update($oFormulario))
				{
					DBAccess::Rollback();
					return false;
				}
			}
		}

		/* guardamos cada cedula solicitada */	
		foreach (GestoriaCreate::$Cedulas as $oCedula)
		{
			$oGestoriaCedula = new GestoriaCedula();
			$oGestoriaCedula->IdGestoria		= $oGestoria->IdGestoria;
			$oGestoriaCedula->Nombre 			= $oCedula->Nombre;
			$oGestoriaCedula->Apellido 			= $oCedula->Apellido;
			$oGestoriaCedula->DocumentoTipo 	= $oCedula->DocumentoTipo;
			$oGestoriaCedula->DocumentoNumero 	= $oCedula->DocumentoNumero;
				
			if (!$oGestoriaCedulas->Create($oGestoriaCedula))
			{
				DBAccess::Rollback();
				return false;
			}
		}
		
		/* guardamos cada socio */	
		foreach (GestoriaCreate::$Socios as $oSocio)
		{
			$oGestoriaSocio = new GestoriaSocio();
			$oGestoriaSocio->IdGestoria		= $oGestoria->IdGestoria;
			$oGestoriaSocio->IdCliente		= $oSocio->IdCliente;
			$oGestoriaSocio->Porcentaje		= $oSocio->Porcentaje;
				
			if (!$oGestoriaSocios->Create($oGestoriaSocio))
			{
				DBAccess::Rollback();
				return false;
			}
		}
		
		/* guaardamos los datos de la prenda en caso de que sea necesario */
		if (GestoriaCreate::$FinanciacionCapital != '')
		{
			$oPrenda = new Prenda();
			$oPrenda->IdGestoria					= $oGestoria->IdGestoria;
			$oPrenda->IdAcreedor					= GestoriaCreate::$IdAcreedor;
			$oPrenda->FinanciacionCapital			= GestoriaCreate::$FinanciacionCapital;
			$oPrenda->CantidadCuotas				= GestoriaCreate::$CantidadCuotas;
			$oPrenda->ImporteCuota					= GestoriaCreate::$ImporteCuota;
			$oPrenda->FechaVencimientoPrimerCuota	= GestoriaCreate::$FechaVencimientoPrimerCuota;
			$oPrenda->TasaNominal					= GestoriaCreate::$TasaNominal;
			$oPrenda->TasaEfectiva					= GestoriaCreate::$TasaEfectiva;
			$oPrenda->CostoFinancieroTotal			= GestoriaCreate::$CostoFinancieroTotal;
			$oPrenda->Observaciones					= GestoriaCreate::$Observaciones;
	
			if (!$oPrenda = $oPrendas->Create($oPrenda))
			{
				DBAccess::Rollback();
				return false;
			}

			/* almacenamos el Id de prenda generado por si se requiere cancelar la gestion */
			GestoriaCreate::$IdPrenda = $oPrenda->IdPrenda;
	
			/* guardamos cada fiador de la prenda */	
			foreach (GestoriaCreate::$Fiadores as $oFiador)
			{
				$oPrendaFiador = new PrendaFiador();
				$oPrendaFiador->IdPrenda 				= $oPrenda->IdPrenda;
				$oPrendaFiador->RazonSocial 			= $oFiador->RazonSocial;
				$oPrendaFiador->DomicilioCalle 			= $oFiador->DomicilioCalle;
				$oPrendaFiador->DomicilioNumero 		= $oFiador->DomicilioNumero;
				$oPrendaFiador->DomicilioPiso 			= $oFiador->DomicilioPiso;
				$oPrendaFiador->DomicilioDpto 			= $oFiador->DomicilioDpto;
				$oPrendaFiador->DomicilioIdLocalidad 	= $oFiador->DomicilioIdLocalidad;
				$oPrendaFiador->DocumentoTipo 			= $oFiador->DocumentoTipo;
				$oPrendaFiador->DocumentoNumero 		= $oFiador->DocumentoNumero;
				$oPrendaFiador->FechaNacimiento 		= $oFiador->FechaNacimiento;
				$oPrendaFiador->IdProfesion 			= $oFiador->IdProfesion;
				$oPrendaFiador->IdNacionalidad 			= $oFiador->IdNacionalidad;
				$oPrendaFiador->IdEstadoCivil 			= $oFiador->IdEstadoCivil;
				$oPrendaFiador->Descripcion 			= $oFiador->Descripcion;
				$oPrendaFiador->Posicion 				= $oFiador->Posicion;
					
				if (!$oPrendaFiadores->Create($oPrendaFiador))
				{
					DBAccess::Rollback();
					return false;
				}
			}

			/* guardamos el conyuge del titular y del condominio en caso de que existieran */	
			foreach (GestoriaCreate::$Conyuges as $oConyuge)
			{
				$oPrendaConyuge = new PrendaConyuge();
				$oPrendaConyuge->IdPrenda 				= $oPrenda->IdPrenda;
				$oPrendaConyuge->IdTipoConyuge 			= $oConyuge->IdTipoConyuge;
				$oPrendaConyuge->RazonSocial 			= $oConyuge->RazonSocial;
				$oPrendaConyuge->DomicilioCalle 		= $oConyuge->DomicilioCalle;
				$oPrendaConyuge->DomicilioNumero 		= $oConyuge->DomicilioNumero;
				$oPrendaConyuge->DomicilioPiso 			= $oConyuge->DomicilioPiso;
				$oPrendaConyuge->DomicilioDpto 			= $oConyuge->DomicilioDpto;
				$oPrendaConyuge->DomicilioIdLocalidad 	= $oConyuge->DomicilioIdLocalidad;
				$oPrendaConyuge->DocumentoTipo 			= $oConyuge->DocumentoTipo;
				$oPrendaConyuge->DocumentoNumero 		= $oConyuge->DocumentoNumero;
				$oPrendaConyuge->FechaNacimiento 		= $oConyuge->FechaNacimiento;
				$oPrendaConyuge->IdProfesion 			= $oConyuge->IdProfesion;
				$oPrendaConyuge->IdNacionalidad 		= $oConyuge->IdNacionalidad;
				$oPrendaConyuge->IdEstadoCivil 			= $oConyuge->IdEstadoCivil;
					
				if (!$oPrendaConyuges->Create($oPrendaConyuge))
				{
					DBAccess::Rollback();
					return false;
				}
			}
		}
		
		/* concluimos la transacción */		
		if (!DBAccess::Commit())
			return false;
			
		GestoriaCreate::$Saved = true;
		
		return true;
	}


	static function Cancel()
	{
		/* comenzamos una transacción */
		if (!DBAccess::Begin())
			return false;
			
		/* si no había sido grabada ... */
		if (!GestoriaCreate::$Saved)
			return true;

		$oGestorias 		= new Gestorias();
		$oFormularios		= new Formularios();
		$oGestoriaCedulas	= new GestoriaCedulas();
		$oGestoriaSocios	= new GestoriaSocios();
		$oPrendas			= new Prendas();
		$oPrendaFiadores	= new PrendaFiadores();
		$oPrendaConyuges	= new PrendaConyuges();
		
		/* obtenemos la gestoria relacionada */
		if (!$oGestoria = $oGestorias->GetById(GestoriaCreate::$IdGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* cancelamos la gestoria en los formularios */
		if (!$oFormularios->LiberarByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}

		/* eliminamos las cedulas solicitadas en caso de que existieran */
		if (!$oGestoriaCedulas->DeleteByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}
		
		/* eliminamos los socios en caso de que existieran */
		if (!$oGestoriaSocios->DeleteByGestoria($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}
		
		/* eliminamos la prenda en caso de que existiera */
		if ($oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
		{
			if (!$oPrendas->Delete($oPrenda->IdPrenda))
			{
				DBAccess::Rollback();
				return false;
			}
		}
		
		/* eliminamos la gestoria */
		if (!$oGestorias->Delete($oGestoria))
		{
			DBAccess::Rollback();
			return false;
		}
		
		/* reestablecemos algunos valores */
		GestoriaCreate::$Saved 		= false;
		GestoriaCreate::$IdGestoria = '';
		GestoriaCreate::$IdPrenda 	= '';
		
		/* concluimos la transacción */
		if (!DBAccess::Commit())
			return false;
			
		GestoriaCreate::$Saved = false;
		
		return true;
	}


	static function Load($IdGestoria)
	{
		$oGestorias 	= new Gestorias();
		$oPrendas 		= new Prendas();
		$oFormularios 	= new Formularios();
		$oClientes		= new Clientes();
		
		GestoriaCreate::ClearAll();

		/* obtenemos la gestoria solicitada */
		if (!$oGestoria = $oGestorias->GetById($IdGestoria))
			return false;

		/* cargamos los datos */
		GestoriaCreate::$IdGestoria 					= $oGestoria->IdGestoria;
		GestoriaCreate::$IdMinuta 						= $oGestoria->IdMinuta;
		GestoriaCreate::$IdTipoUso 						= $oGestoria->IdTipoUso;
		GestoriaCreate::$IdClienteCondominio 			= $oGestoria->IdClienteCondominio;
		if (GestoriaCreate::$IdClienteCondominio)
		{
			$oCliente = $oClientes->GetById(GestoriaCreate::$IdClienteCondominio);			
			GestoriaCreate::$ClienteCondominio = $oCliente->GetUsuario();
		}
		GestoriaCreate::$CondominioConyuge 				= $oGestoria->CondominioConyuge;
		GestoriaCreate::$PorcentajeTitularidad 			= $oGestoria->PorcentajeTitularidad;
		GestoriaCreate::$NumeroCertificado 				= $oGestoria->NumeroCertificado;
		GestoriaCreate::$DomicilioFiscalCalle 			= $oGestoria->DomicilioFiscalCalle;
		GestoriaCreate::$DomicilioFiscalNumero 			= $oGestoria->DomicilioFiscalNumero;
		GestoriaCreate::$DomicilioFiscalPiso 			= $oGestoria->DomicilioFiscalPiso;
		GestoriaCreate::$DomicilioFiscalDpto 			= $oGestoria->DomicilioFiscalDpto;
		GestoriaCreate::$DomicilioFiscalIdLocalidad 	= $oGestoria->DomicilioFiscalIdLocalidad;
		GestoriaCreate::$FechaGestion 					= $oGestoria->FechaGestion;
		GestoriaCreate::$SociedadHecho 					= $oGestoria->SociedadHecho;

		/* obtenemos los formularios cargados */
		$arrFormularios = $oGestoria->GetAllFormularios();

		/* asignamos cada formulario */
		foreach ($arrFormularios as $oFormulario)
			GestoriaCreate::AddFormulario($oFormulario);

		/* obtenemos las cedulas cargadas */
		$arrGestoriaCedulas = $oGestoria->GetAllCedulas();

		/* para cada cedula debemos generar un objeto Cedula */
		foreach ($arrGestoriaCedulas as $oGestoriaCedula)
		{
			$oCedula = new Cedula($oGestoriaCedula->IdCedula);
			$oCedula->ParseFromGestoriaCedula($oGestoriaCedula);
						
			GestoriaCreate::AddCedula($oCedula);
		}
		
		/* obtenemos las cedulas cargadas */
		$arrGestoriaSocios = $oGestoria->GetAllSocios();
		
		/* para cada socio debemos generar un objeto Socio */
		foreach ($arrGestoriaSocios as $oGestoriaSocio)
		{
			$oSocio = new Socio($oGestoriaSocio->IdGestoriaSocio);
			$oSocio->ParseFromGestoriaSocio($oGestoriaSocio);
						
			GestoriaCreate::AddSocio($oSocio);
		}

		/* obtenemos la prenda en caso de que existiera */
		if ($oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
		{
			GestoriaCreate::$IdPrenda 						= $oPrenda->IdPrenda;
			GestoriaCreate::$IdAcreedor 					= $oPrenda->IdAcreedor;
			
			$oAcreedores = new Acreedores();
			$oAcreedor = $oAcreedores->GetById($oPrenda->IdAcreedor);
			
			GestoriaCreate::$Acreedor 						= $oAcreedor->RazonSocial;
			
			GestoriaCreate::$FinanciacionCapital 			= $oPrenda->FinanciacionCapital;
			GestoriaCreate::$CantidadCuotas 				= $oPrenda->CantidadCuotas;
			GestoriaCreate::$ImporteCuota 					= $oPrenda->ImporteCuota;
			GestoriaCreate::$FechaVencimientoPrimerCuota 	= $oPrenda->FechaVencimientoPrimerCuota;
			GestoriaCreate::$TasaNominal 					= $oPrenda->TasaNominal;
			GestoriaCreate::$TasaEfectiva 					= $oPrenda->TasaEfectiva;
			GestoriaCreate::$CostoFinancieroTotal			= $oPrenda->CostoFinancieroTotal;
			GestoriaCreate::$Observaciones 					= $oPrenda->Observaciones;			

			/* obtenemos los fiadores cargados */
			$arrPrendaFiadores = $oPrenda->GetAllFiadores();

			/* para cada fiador debemos generar un objeto Fiador */
			foreach ($arrPrendaFiadores as $oPrendaFiador)
			{
				$oFiador = new Fiador($oPrendaFiador->IdFiador);
				$oFiador->ParseFromPrendaFiador($oPrendaFiador);
							
				GestoriaCreate::AddFiador($oFiador);
			}

			/* obtenemos los conyuges cargados */
			$arrPrendaConyuges = $oPrenda->GetAllConyuges();

			/* para cada conyuge debemos generar un objeto Conyuge */
			foreach ($arrPrendaConyuges as $oPrendaConyuge)
			{
				$oConyuge = new Conyuge($oPrendaConyuge->IdConyuge);
				$oConyuge->ParseFromPrendaConyuge($oPrendaConyuge);
							
				GestoriaCreate::AddConyuge($oConyuge);
			}
		}

		return true;
	}
}


class Cedula extends GestoriaCedula
{
	public $Id;
	public $Code;
	
	
	public function __construct($Code)
	{
		$this->Code = $Code;
	}
	

	public function ParseFromGestoriaCedula(GestoriaCedula $oGestoriaCedula)
	{
		$this->IdCedula 		= $oGestoriaCedula->IdCedula;
		$this->IdGestoria 		= $oGestoriaCedula->IdGestoria;
		$this->nombre			= $oGestoriaCedula->Nombre;
		$this->Apellido 		= $oGestoriaCedula->Apellido;
		$this->DocumentoTipo 	= $oGestoriaCedula->DocumentoTipo;
		$this->DocumentoNumero 	= $oGestoriaCedula->DocumentoNumero;
	}
}

class Socio extends GestoriaSocio
{
	public $Id;
	public $Code;
	
	
	public function __construct($Code)
	{
		$this->Code = $Code;
	}
	

	public function ParseFromGestoriaSocio(GestoriaSocio $oGestoriaSocio)
	{
		$this->IdGestoriaSocio	= $oGestoriaSocio->IdGestoriaSocio;
		$this->IdGestoria 		= $oGestoriaSocio->IdGestoria;
		$this->IdCliente		= $oGestoriaSocio->IdCliente;
		$this->Porcentaje 		= $oGestoriaSocio->Porcentaje;
	}
}


class Fiador extends PrendaFiador
{
	public $Id;
	public $Code;
	
	
	public function __construct($Code)
	{
		$this->Code = $Code;
	}
	

	public function ParseFromPrendaFiador(PrendaFiador $oPrendaFiador)
	{
		$this->IdFiador 			= $oPrendaFiador->IdFiador;
		$this->IdPrenda 			= $oPrendaFiador->IdPrenda;
		$this->RazonSocial 			= $oPrendaFiador->RazonSocial;
		$this->DomicilioCalle 		= $oPrendaFiador->DomicilioCalle;
		$this->DomicilioNumero 		= $oPrendaFiador->DomicilioNumero;
		$this->DomicilioPiso 		= $oPrendaFiador->DomicilioPiso;
		$this->DomicilioDpto 		= $oPrendaFiador->DomicilioDpto;
		$this->DomicilioIdLocalidad = $oPrendaFiador->DomicilioIdLocalidad;
		$this->DocumentoTipo 		= $oPrendaFiador->DocumentoTipo;
		$this->DocumentoNumero 		= $oPrendaFiador->DocumentoNumero;
		$this->FechaNacimiento 		= $oPrendaFiador->FechaNacimiento;
		$this->IdProfesion 			= $oPrendaFiador->IdProfesion;
		$this->IdNacionalidad 		= $oPrendaFiador->IdNacionalidad;
		$this->IdEstadoCivil 		= $oPrendaFiador->IdEstadoCivil;
		$this->Descripcion 			= $oPrendaFiador->Descripcion;
		$this->Posicion 			= $oPrendaFiador->Posicion;
	}
}


class Conyuge extends PrendaConyuge
{
	public $Id;
	
	
	public function ParseFromPrendaConyuge(PrendaConyuge $oPrendaConyuge)
	{
		$this->IdConyuge 				= $oPrendaConyuge->IdConyuge;
		$this->IdPrenda 				= $oPrendaConyuge->IdPrenda;
		$this->IdTipoConyuge 			= $oPrendaConyuge->IdTipoConyuge;
		$this->RazonSocial 				= $oPrendaConyuge->RazonSocial;
		$this->DomicilioCalle 			= $oPrendaConyuge->DomicilioCalle;
		$this->DomicilioNumero 			= $oPrendaConyuge->DomicilioNumero;
		$this->DomicilioPiso 			= $oPrendaConyuge->DomicilioPiso;
		$this->DomicilioDpto 			= $oPrendaConyuge->DomicilioDpto;
		$this->DomicilioIdLocalidad 	= $oPrendaConyuge->DomicilioIdLocalidad;
		$this->DocumentoTipo 			= $oPrendaConyuge->DocumentoTipo;
		$this->DocumentoNumero 			= $oPrendaConyuge->DocumentoNumero;
		$this->FechaNacimiento 			= $oPrendaConyuge->FechaNacimiento;
		$this->IdProfesion 				= $oPrendaConyuge->IdProfesion;
		$this->IdNacionalidad 			= $oPrendaConyuge->IdNacionalidad;
		$this->IdEstadoCivil 			= $oPrendaConyuge->IdEstadoCivil;
	}
}

?>