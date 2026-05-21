<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_CREATE))
	Session::NoPerm();

/* solo podemos acceder si tenemos el id de fatura */
if (GestoriaCreate::$IdMinuta == '')
	exit();

/* obtiene datos enviados */
$Action	= strval($_REQUEST['MainAction']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$errConyugeTitular 		= 0;
$errConyugeCondominio 	= 0;
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposDocumento 		= new TiposDocumento();
$oProfesiones 			= new Profesiones();
$oLocalidades 			= new Localidades();
$oEstadosCiviles 		= new EstadosCiviles();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oPaises 				= new Paises();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById(GestoriaCreate::$IdMinuta))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del condominio en caso de que existiera */
$oClienteCondominio = $oClientes->GetById(GestoriaCreate::$IdClienteCondominio);

/* si la venta no es con prenda, ssalteamos este paso */
if (($oMinuta->FinanciacionCapital == '') || ($oMinuta->FinanciacionCapital == 0))
{
	header("Location: gestorias_add_paso5.php" . $strParams);
	exit;
}

/* si no se requieren los datos del conyuge, salteamos este paso */
if (($oCliente->IdEstadoCivil != EstadoCivil::Casado) && (!GestoriaCreate::$CondominioConyuge) && ($oClienteCondominio->IdEstadoCivil != EstadoCivil::Casado)) 
{
	header("Location: gestorias_add_paso5.php" . $strParams);
	exit;
}

/* obtenemos los conyuges en casi de que se hayan cargado */
$oConyugeTitular 	= GestoriaCreate::GetConyuge(GestoriaCreate::ConyugeTitular);
$oConyugeCondominio = GestoriaCreate::GetConyuge(GestoriaCreate::ConyugeCondominio);

/* si el formulario fue enviado */
if ($Submit)
{
	/* obtenemos los datos del conyuge del titular */
	$ConyugeTitularRazonSocial 				= $_REQUEST['ConyugeTitularRazonSocial'];
	$ConyugeTitularDomicilioCalle 			= $_REQUEST['ConyugeTitularDomicilioCalle'];
	$ConyugeTitularDomicilioNumero 			= $_REQUEST['ConyugeTitularDomicilioNumero'];
	$ConyugeTitularDomicilioPiso 			= $_REQUEST['ConyugeTitularDomicilioPiso'];
	$ConyugeTitularDomicilioDpto 			= $_REQUEST['ConyugeTitularDomicilioDpto'];
	$ConyugeTitularDomicilioIdLocalidad 	= $_REQUEST['ConyugeTitularDomicilioIdLocalidad'];
	$ConyugeTitularDomicilioCodigoPostal 	= $_REQUEST['ConyugeTitularDomicilioCodigoPostal'];
	$ConyugeTitularDocumentoTipo 			= $_REQUEST['ConyugeTitularDocumentoTipo'];
	$ConyugeTitularDocumentoNumero 			= $_REQUEST['ConyugeTitularDocumentoNumero'];
	$ConyugeTitularFechaNacimiento 			= $_REQUEST['ConyugeTitularFechaNacimiento'];
	$ConyugeTitularIdProfesion 				= $_REQUEST['ConyugeTitularIdProfesion'];
	$ConyugeTitularIdNacionalidad 			= $_REQUEST['ConyugeTitularIdNacionalidad'];
	$ConyugeTitularIdEstadoCivil 			= $_REQUEST['ConyugeTitularIdEstadoCivil'];

	/* obtenemos los datos del conyuge del condominio */
	$ConyugeCondominioRazonSocial 			= $_REQUEST['ConyugeCondominioRazonSocial'];
	$ConyugeCondominioDomicilioCalle 		= $_REQUEST['ConyugeCondominioDomicilioCalle'];
	$ConyugeCondominioDomicilioNumero 		= $_REQUEST['ConyugeCondominioDomicilioNumero'];
	$ConyugeCondominioDomicilioPiso 		= $_REQUEST['ConyugeCondominioDomicilioPiso'];
	$ConyugeCondominioDomicilioDpto 		= $_REQUEST['ConyugeCondominioDomicilioDpto'];
	$ConyugeCondominioDomicilioIdLocalidad 	= $_REQUEST['ConyugeCondominioDomicilioIdLocalidad'];
	$ConyugeCondominioDomicilioCodigoPostal = $_REQUEST['ConyugeCondominioDomicilioCodigoPostal'];
	$ConyugeCondominioDocumentoTipo 		= $_REQUEST['ConyugeCondominioDocumentoTipo'];
	$ConyugeCondominioDocumentoNumero 		= $_REQUEST['ConyugeCondominioDocumentoNumero'];
	$ConyugeCondominioFechaNacimiento 		= $_REQUEST['ConyugeCondominioFechaNacimiento'];
	$ConyugeCondominioIdProfesion 			= $_REQUEST['ConyugeCondominioIdProfesion'];
	$ConyugeCondominioIdNacionalidad 		= $_REQUEST['ConyugeCondominioIdNacionalidad'];
	$ConyugeCondominioIdEstadoCivil 		= $_REQUEST['ConyugeCondominioIdEstadoCivil'];

	/* procesamos la acción principal */
	switch ($Action)
	{
		case 'Next':

			/* si el titular esta casado... */
			if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) || (GestoriaCreate::$CondominioConyuge)) 
			{
				if ($ConyugeTitularRazonSocial == '')
					$errConyugeTitular |= 1;
				if ($ConyugeTitularDocumentoTipo == '')
					$errConyugeTitular |= 2;
				if ($ConyugeTitularDocumentoNumero == '')
					$errConyugeTitular |= 4;
				if ($ConyugeTitularFechaNacimiento == '')
					$errConyugeTitular |= 8;
				if ($ConyugeTitularIdEstadoCivil == '')
					$errConyugeTitular |= 16;
			}

			/* si el condominio esta casado y no es conyuge del titular, */
			/* entonces verificamos los datos del conyuge del condominio */
			if ((!GestoriaCreate::$CondominioConyuge) && ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)) 
			{
				if ($ConyugeCondominioRazonSocial == '')
					$errConyugeCondominio |= 1;
				if ($ConyugeCondominioDocumentoTipo == '')
					$errConyugeCondominio |= 2;
				if ($ConyugeCondominioDocumentoNumero == '')
					$errConyugeCondominio |= 4;
				if ($ConyugeCondominioFechaNacimiento == '')
					$errConyugeCondominio |= 8;
				if ($ConyugeCondominioIdEstadoCivil == '')
					$errConyugeCondominio |= 16;
			}

			/* si no hay erroes... */
			if (($errConyugeTitular == 0) && ($errConyugeCondominio == 0))
			{				
				/* si el titular esta casado... */
				if ($oCliente->IdEstadoCivil == EstadoCivil::Casado) 
				{
					/* creamos el objeto de Conyuge */		
					$oConyuge = new Conyuge();
					$oConyuge->IdTipoConyuge 		= GestoriaCreate::ConyugeTitular;
					$oConyuge->RazonSocial 			= $ConyugeTitularRazonSocial;
					$oConyuge->DomicilioCalle 		= $ConyugeTitularDomicilioCalle;
					$oConyuge->DomicilioNumero 		= $ConyugeTitularDomicilioNumero;
					$oConyuge->DomicilioPiso 		= $ConyugeTitularDomicilioPiso;
					$oConyuge->DomicilioDpto 		= $ConyugeTitularDomicilioDpto;
					$oConyuge->DomicilioIdLocalidad = $ConyugeTitularDomicilioIdLocalidad;
					$oConyuge->DocumentoTipo 		= $ConyugeTitularDocumentoTipo;
					$oConyuge->DocumentoNumero 		= $ConyugeTitularDocumentoNumero;
					$oConyuge->FechaNacimiento 		= $ConyugeTitularFechaNacimiento;
					$oConyuge->IdProfesion 			= $ConyugeTitularIdProfesion;
					$oConyuge->IdNacionalidad 		= $ConyugeTitularIdNacionalidad;
					$oConyuge->IdEstadoCivil 		= $ConyugeTitularIdEstadoCivil;
					
					GestoriaCreate::AddConyuge($oConyuge);
				}
	
				/* si el condominio esta casado y no es conyuge del titular, */
				/* entonces guardamos los datos del conyuge del condominio */
				if ((!GestoriaCreate::$CondominioConyuge) && ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)) 
				{
					/* creamos el objeto de Conyuge */		
					$oConyuge = new Conyuge();
					$oConyuge->IdTipoConyuge 		= GestoriaCreate::ConyugeCondominio;
					$oConyuge->RazonSocial 			= $ConyugeCondominioRazonSocial;
					$oConyuge->DomicilioCalle 		= $ConyugeCondominioDomicilioCalle;
					$oConyuge->DomicilioNumero 		= $ConyugeCondominioDomicilioNumero;
					$oConyuge->DomicilioPiso 		= $ConyugeCondominioDomicilioPiso;
					$oConyuge->DomicilioDpto 		= $ConyugeCondominioDomicilioDpto;
					$oConyuge->DomicilioIdLocalidad = $ConyugeCondominioDomicilioIdLocalidad;
					$oConyuge->DocumentoTipo 		= $ConyugeCondominioDocumentoTipo;
					$oConyuge->DocumentoNumero 		= $ConyugeCondominioDocumentoNumero;
					$oConyuge->FechaNacimiento 		= $ConyugeCondominioFechaNacimiento;
					$oConyuge->IdProfesion 			= $ConyugeCondominioIdProfesion;
					$oConyuge->IdNacionalidad 		= $ConyugeCondominioIdNacionalidad;
					$oConyuge->IdEstadoCivil 		= $ConyugeCondominioIdEstadoCivil;
					
					GestoriaCreate::AddConyuge($oConyuge);
				}
			
				header("Location: gestorias_add_paso5.php" . $strParams);
				exit;
			}

			break;
			
		case 'Back':
		
			header("Location: gestorias_add_paso3.php");
			exit;

			break;				
			
		case 'Cancel':
		
			/* cancelamos la transaccion */
			GestoriaCreate::Cancel();		
			GestoriaCreate::ClearAll();		

			header("Location: gestorias.php" . $strParams);
			exit;

			break;
	}
}
else
{
	if ((GestoriaCreate::$CondominioConyuge) && (!$oConyugeTitular))
	{
		/* creamos el objeto de Conyuge */		
		$oConyuge = new Conyuge();
		$oConyuge->IdTipoConyuge 		= GestoriaCreate::ConyugeTitular;
		$oConyuge->RazonSocial 			= $oClienteCondominio->RazonSocial;
		$oConyuge->DomicilioCalle 		= $oClienteCondominio->DomicilioCalle;
		$oConyuge->DomicilioNumero 		= $oClienteCondominio->DomicilioNumero;
		$oConyuge->DomicilioPiso 		= $oClienteCondominio->DomicilioPiso;
		$oConyuge->DomicilioDpto 		= $oClienteCondominio->DomicilioDpto;
		$oConyuge->DomicilioIdLocalidad = $oClienteCondominio->DomicilioIdLocalidad;
		$oConyuge->DocumentoTipo 		= $oClienteCondominio->DocumentoTipo;
		$oConyuge->DocumentoNumero 		= $oClienteCondominio->DocumentoNumero;
		$oConyuge->FechaNacimiento 		= CambiarFecha($oClienteCondominio->FechaNacimiento);
		$oConyuge->IdProfesion 			= $oClienteCondominio->IdProfesion;
		$oConyuge->IdNacionalidad 		= $oClienteCondominio->IdNacionalidad;
		$oConyuge->IdEstadoCivil 		= $oClienteCondominio->IdEstadoCivil;
		
		GestoriaCreate::AddConyuge($oConyuge);

		$oConyugeTitular = GestoriaCreate::GetConyuge(GestoriaCreate::ConyugeTitular);
		header('Location: gestorias_add_paso5.php');
		exit;
	}

	/* datos del conyuge del titular */
	$ConyugeTitularRazonSocial 				= $oConyugeTitular->RazonSocial;
	$ConyugeTitularDomicilioCalle 			= $oConyugeTitular->DomicilioCalle;
	$ConyugeTitularDomicilioNumero 			= $oConyugeTitular->DomicilioNumero;
	$ConyugeTitularDomicilioPiso 			= $oConyugeTitular->DomicilioPiso;
	$ConyugeTitularDomicilioDpto 			= $oConyugeTitular->DomicilioDpto;
	$ConyugeTitularDomicilioIdLocalidad 	= $oConyugeTitular->DomicilioIdLocalidad;
	$ConyugeTitularDocumentoTipo 			= $oConyugeTitular->DocumentoTipo;
	$ConyugeTitularDocumentoNumero 			= $oConyugeTitular->DocumentoNumero;
	$ConyugeTitularFechaNacimiento 			= CambiarFecha($oConyugeTitular->FechaNacimiento);
	$ConyugeTitularIdProfesion 				= $oConyugeTitular->IdProfesion;
	$ConyugeTitularIdNacionalidad 			= $oConyugeTitular->IdNacionalidad;
	$ConyugeTitularIdEstadoCivil 			= $oConyugeTitular->IdEstadoCivil;

	/* datos del conyuge del condominio */
	$ConyugeCondominioRazonSocial 			= $oConyugeCondominio->RazonSocial;
	$ConyugeCondominioDomicilioCalle 		= $oConyugeCondominio->DomicilioCalle;
	$ConyugeCondominioDomicilioNumero 		= $oConyugeCondominio->DomicilioNumero;
	$ConyugeCondominioDomicilioPiso 		= $oConyugeCondominio->DomicilioPiso;
	$ConyugeCondominioDomicilioDpto 		= $oConyugeCondominio->DomicilioDpto;
	$ConyugeCondominioDomicilioIdLocalidad 	= $oConyugeCondominio->DomicilioIdLocalidad;
	$ConyugeCondominioDocumentoTipo 		= $oConyugeCondominio->DocumentoTipo;
	$ConyugeCondominioDocumentoNumero 		= $oConyugeCondominio->DocumentoNumero;
	$ConyugeCondominioFechaNacimiento 		= CambiarFecha($oConyugeCondominio->FechaNacimiento);
	$ConyugeCondominioIdProfesion 			= $oConyugeCondominio->IdProfesion;
	$ConyugeCondominioIdNacionalidad 		= $oConyugeCondominio->IdNacionalidad;
	$ConyugeCondominioIdEstadoCivil 		= $oConyugeCondominio->IdEstadoCivil;
	
	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && (!$oConyugeTitular))
	{
		$ConyugeTitularRazonSocial 		= $oCliente->ConyugeApellido . ' ' . $oCliente->ConyugeNombre;
		$ConyugeTitularDocumentoTipo 	= $oCliente->ConyugeDocumentoTipo;
		$ConyugeTitularDocumentoNumero 	= $oCliente->ConyugeDocumentoNumero;
	}
}

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* informacion del conyuge titular */
$oDocumentoTipoConyugeTitular 		= $oTiposDocumento->GetById($ConyugeTitularDocumentoTipo);
$oEstadoCivilConyugeTitular 		= $oEstadosCiviles->GetById($ConyugeTitularIdEstadoCivil);
$oProfesionConyugeTitular 			= $oProfesiones->GetById($ConyugeTitularIdProfesion);
$oDomicilioLocalidadConyugeTitular 	= $oLocalidades->GetById($ConyugeTitularDomicilioIdLocalidad);

$ConyugeTitularDocumentoTipoNombre 		= $oDocumentoTipoConyugeTitular->Nombre;
$ConyugeTitularDocumentoTipoCodigo 		= $oDocumentoTipoConyugeTitular->Codigo;
$ConyugeTitularEstadoCivil				= $oEstadoCivilConyugeTitular->Nombre;
$ConyugeTitularEstadoCivilCodigo		= $oEstadoCivilConyugeTitular->Codigo;
$ConyugeTitularProfesion				= $oProfesionConyugeTitular->Nombre;
$ConyugeTitularProfesionCodigo			= $oProfesionConyugeTitular->Codigo;
$ConyugeTitularDomicilioLocalidad	 	= $oDomicilioLocalidadConyugeTitular->Nombre;
$ConyugeTitularDomicilioCodigoPostal 	= $oDomicilioLocalidadConyugeTitular->CodigoPostal;

/* informacion del conyuge condominio */
$oDocumentoTipoConyugeCondominio 		= $oTiposDocumento->GetById($ConyugeCondominioDocumentoTipo);
$oEstadoCivilConyugeCondominio 			= $oEstadosCiviles->GetById($ConyugeCondominioIdEstadoCivil);
$oProfesionConyugeCondominio 			= $oProfesiones->GetById($ConyugeCondominioIdProfesion);
$oDomicilioLocalidadConyugeCondominio 	= $oLocalidades->GetById($ConyugeCondominioDomicilioIdLocalidad);

$ConyugeCondominioDocumentoTipoNombre 	= $oDocumentoTipoConyugeCondominio->Nombre;
$ConyugeCondominioDocumentoTipoCodigo 	= $oDocumentoTipoConyugeCondominio->Codigo;
$ConyugeCondominioEstadoCivil			= $oEstadoCivilConyugeCondominio->Nombre;
$ConyugeCondominioEstadoCivilCodigo		= $oEstadoCivilConyugeCondominio->Codigo;
$ConyugeCondominioProfesion				= $oProfesionConyugeCondominio->Nombre;
$ConyugeCondominioProfesionCodigo		= $oProfesionConyugeCondominio->Codigo;
$ConyugeCondominioDomicilioLocalidad	= $oDomicilioLocalidadConyugeCondominio->Nombre;
$ConyugeCondominioDomicilioCodigoPostal = $oDomicilioLocalidadConyugeCondominio->CodigoPostal;

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterTipoDocumentoConyugeTitular(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('ConyugeTitularDocumentoTipoCodigo').value 	= '';
		Get('ConyugeTitularDocumentoTipoNombre').value 	= '';
		Get('ConyugeTitularDocumentoTipo').value 		= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('ConyugeTitularDocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('ConyugeTitularDocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('ConyugeTitularDocumentoTipo').value 		= oTipoDocumento.IdTipoDocumento;
}

function FilterProfesionConyugeTitular(IdProfesion, Nombre)
{
	if ((IdProfesion == '') && (Nombre == ''))
	{
		Get('ConyugeTitularProfesionCodigo').value 	= '';
		Get('ConyugeTitularProfesion').value 		= '';
		Get('ConyugeTitularIdProfesion').value 		= '';
	}

	var oProfesion = GetProfesion(IdProfesion);
	if (!(oProfesion))
		return;

	Get('ConyugeTitularProfesionCodigo').value 	= oProfesion.Codigo;
	Get('ConyugeTitularProfesion').value 		= oProfesion.Nombre;
	Get('ConyugeTitularIdProfesion').value 		= oProfesion.IdProfesion;
}

function FilterEstadoCivilConyugeTitular(IdEstadoCivil, Nombre)
{
	if ((IdEstadoCivil == '') && (Nombre == ''))
	{
		Get('ConyugeTitularEstadoCivilCodigo').value 	= '';
		Get('ConyugeTitularEstadoCivil').value 		= '';
		Get('ConyugeTitularIdEstadoCivil').value 		= '';
	}

	var oEstadoCivil = GetEstadoCivil(IdEstadoCivil);
	if (!(oEstadoCivil))
		return;

	Get('ConyugeTitularEstadoCivilCodigo').value 	= oEstadoCivil.Codigo;
	Get('ConyugeTitularEstadoCivil').value 			= oEstadoCivil.Nombre;
	Get('ConyugeTitularIdEstadoCivil').value 		= oEstadoCivil.IdEstadoCivil;
}

function FilterDomicilioLocalidadConyugeTitular(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('ConyugeTitularDomicilioIdLocalidad').value 	= '';
		Get('ConyugeTitularDomicilioCodigoPostal').value 	= '';
		Get('ConyugeTitularDomicilioLocalidad').value 		= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('ConyugeTitularDomicilioIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('ConyugeTitularDomicilioCodigoPostal').value 	= oLocalidad.CodigoPostal;
	Get('ConyugeTitularDomicilioLocalidad').value 		= oLocalidad.Nombre;
}

function FilterTipoDocumentoConyugeCondominio(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('ConyugeCondominioDocumentoTipoCodigo').value 	= '';
		Get('ConyugeCondominioDocumentoTipoNombre').value 	= '';
		Get('ConyugeCondominioDocumentoTipo').value 		= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('ConyugeCondominioDocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('ConyugeCondominioDocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('ConyugeCondominioDocumentoTipo').value 		= oTipoDocumento.IdTipoDocumento;
}

function FilterProfesionConyugeCondominio(IdProfesion, Nombre)
{
	if ((IdProfesion == '') && (Nombre == ''))
	{
		Get('ConyugeCondominioProfesionCodigo').value 	= '';
		Get('ConyugeCondominioProfesion').value 		= '';
		Get('ConyugeCondominioIdProfesion').value 		= '';
	}

	var oProfesion = GetProfesion(IdProfesion);
	if (!(oProfesion))
		return;

	Get('ConyugeCondominioProfesionCodigo').value 	= oProfesion.Codigo;
	Get('ConyugeCondominioProfesion').value 		= oProfesion.Nombre;
	Get('ConyugeCondominioIdProfesion').value 		= oProfesion.IdProfesion;
}

function FilterEstadoCivilConyugeCondominio(IdEstadoCivil, Nombre)
{
	if ((IdEstadoCivil == '') && (Nombre == ''))
	{
		Get('ConyugeCondominioEstadoCivilCodigo').value = '';
		Get('ConyugeCondominioEstadoCivil').value 		= '';
		Get('ConyugeCondominioIdEstadoCivil').value 	= '';
	}

	var oEstadoCivil = GetEstadoCivil(IdEstadoCivil);
	if (!(oEstadoCivil))
		return;

	Get('ConyugeCondominioEstadoCivilCodigo').value = oEstadoCivil.Codigo;
	Get('ConyugeCondominioEstadoCivil').value 		= oEstadoCivil.Nombre;
	Get('ConyugeCondominioIdEstadoCivil').value 	= oEstadoCivil.IdEstadoCivil;
}

function FilterDomicilioLocalidadConyugeCondominio(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('ConyugeCondominioDomicilioIdLocalidad').value 		= '';
		Get('ConyugeCondominioDomicilioCodigoPostal').value 	= '';
		Get('ConyugeCondominioDomicilioLocalidad').value 		= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('ConyugeCondominioDomicilioIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('ConyugeCondominioDomicilioCodigoPostal').value = oLocalidad.CodigoPostal;
	Get('ConyugeCondominioDomicilioLocalidad').value 	= oLocalidad.Nombre;
}

function Next()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Next';
	frmData.submit();
	return true;
}

function Back()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Back';
	frmData.submit();
	return true;
}

function Cancel()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Cancel';
	frmData.submit();
	return true;
}

</script>

</head>
<body>

<blockquote>&nbsp;</blockquote>
<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Agregar - Paso 4</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
				    <input type="hidden" name="MainAction" id="MainAction" value="" />
                    <input type="hidden" name="ConyugeTitularDocumentoTipo" id="ConyugeTitularDocumentoTipo" value="<?=$ConyugeTitularDocumentoTipo?>" />
                    <input type="hidden" name="ConyugeTitularIdEstadoCivil" id="ConyugeTitularIdEstadoCivil" value="<?=$ConyugeTitularIdEstadoCivil?>" />
                    <input type="hidden" name="ConyugeTitularIdProfesion" id="ConyugeTitularIdProfesion" value="<?=$ConyugeTitularIdProfesion?>" />
                    <input type="hidden" name="ConyugeTitularDomicilioIdLocalidad" id="ConyugeTitularDomicilioIdLocalidad" value="<?=$ConyugeTitularDomicilioIdLocalidad?>" />
                    <input type="hidden" name="ConyugeCondominioDocumentoTipo" id="ConyugeCondominioDocumentoTipo" value="<?=$ConyugeCondominioDocumentoTipo?>" />
                    <input type="hidden" name="ConyugeCondominioIdEstadoCivil" id="ConyugeCondominioIdEstadoCivil" value="<?=$ConyugeCondominioIdEstadoCivil?>" />
                    <input type="hidden" name="ConyugeCondominioIdProfesion" id="ConyugeCondominioIdProfesion" value="<?=$ConyugeCondominioIdProfesion?>" />
                    <input type="hidden" name="ConyugeCondominioDomicilioIdLocalidad" id="ConyugeCondominioDomicilioIdLocalidad" value="<?=$ConyugeCondominioDomicilioIdLocalidad?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
                            <td>
                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

                                <?php if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) || (GestoriaCreate::$CondominioConyuge)) { ?>

                                    <tr>
                                        <td>
                                            <div align="center">
                                                <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Consentimiento Conyugal Titular</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>
                                            <div align="center">
                                                <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td valign="top">
                                                                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Apellido y Nombre:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularRazonSocial" id="ConyugeTitularRazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeTitularRazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeTitular & 1) { ?>
                                                                                            <li style="color:#FF0000;">Ingrese apellido y nombre</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Tipo Documento:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDocumentoTipoNombre" id="ConyugeTitularDocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeTitularDocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('TiposDocumento', 'GetAll', 'ConyugeTitularDocumentoTipoNombre', 'FilterTipoDocumentoConyugeTitular', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDocumentoTipoCodigo" id="ConyugeTitularDocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeTitularDocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('DocumentoConyugeTitular');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeTitular & 2) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDocumentoNumero" id="ConyugeTitularDocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeTitularDocumentoNumero?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeTitular & 4) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Fecha de Nacimiento:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input name="ConyugeTitularFechaNacimiento" type="text" class="camporFormularioMediano" id="ConyugeTitularFechaNacimiento" value="<?=$ConyugeTitularFechaNacimiento?>" size="12" maxlength="12" />
                                                                                                                <script language="javascript">
                                                                                                                new tcal({'formname': 'frmData', 'controlname': 'ConyugeTitularFechaNacimiento'});
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeTitular & 8) { ?>
                                                                                            <li style="color:#FF0000;">Ingrese la fecha de nacimiento</li>
                                                                                            <?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Profesi&oacute;n:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularProfesion" id="ConyugeTitularProfesion" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeTitularProfesion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('Profesiones', 'GetAll', 'ConyugeTitularProfesion', 'FilterProfesionConyugeTitular', 'IdProfesion', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularProfesionCodigo" id="ConyugeTitularProfesionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeTitularProfesionCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddProfesion" class="botonBasico"  onClick="javascript:AddProfesion('ProfesionConyugeTitular');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td valign="top">
                                                                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Estado Civil:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularEstadoCivil" id="ConyugeTitularEstadoCivil" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeTitularEstadoCivil?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarEstadoCivil(this.value);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('EstadosCiviles', 'GetAll', 'ConyugeTitularEstadoCivil', 'FilterEstadoCivilConyugeTitular', 'IdEstadoCivil', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularEstadoCivilCodigo" id="ConyugeTitularEstadoCivilCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeTitularEstadoCivilCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddEstadoCivil" class="botonBasico"  onClick="javascript:AddEstadoCivil('EstadoCivilConyugeTitular');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeTitular & 16) { ?><li style="color:#FF0000;">Ingrese el estado civil</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Nacionalidad:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <select name="ConyugeTitularIdNacionalidad" id="ConyugeTitularIdNacionalidad" class="camporFormularioSimple">
                                                                                                                    <option value="">[SELECCIONE]</option>
                                                                                                                    <?php foreach ($arrPaises as $oPais) { ?>
                                                                                                                    <option value="<?=$oPais->IdPais?>" <?=($ConyugeTitularIdNacionalidad == $oPais->IdPais) ? 'selected="selected"' : ''?> ><?=$oPais->Nombre?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Localidad:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDomicilioLocalidad" id="ConyugeTitularDomicilioLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeTitularDomicilioLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('Localidades', 'GetAllSuggest', 'ConyugeTitularDomicilioLocalidad', 'FilterDomicilioLocalidadConyugeTitular', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDomicilioCodigoPostal" id="ConyugeTitularDomicilioCodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=$ConyugeTitularDomicilioCodigoPostal?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('DomicilioConyugeTitular');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Calle:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="ConyugeTitularDomicilioCalle" id="ConyugeTitularDomicilioCalle" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeTitularDomicilioCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">N&uacute;mero:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Piso:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Dpto.:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDomicilioNumero" id="ConyugeTitularDomicilioNumero" class="camporFormularioChico" maxlength="12" value="<?=$ConyugeTitularDomicilioNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDomicilioPiso" id="ConyugeTitularDomicilioPiso" class="camporFormularioChico" maxlength="4" value="<?=$ConyugeTitularDomicilioPiso?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeTitularDomicilioDpto" id="ConyugeTitularDomicilioDpto" class="camporFormularioChico" maxlength="4" value="<?=$ConyugeTitularDomicilioDpto?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr> 
                                                            </table>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                <?php } ?>                                            
                                <?php if ((!GestoriaCreate::$CondominioConyuge) && 
                                            ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)) { ?>

                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div align="center">
                                                <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Consentimiento Conyugal Condominio</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>
                                            <div align="center">
                                                <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td valign="top">
                                                                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Apellido y Nombre:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioRazonSocial" id="ConyugeCondominioRazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeCondominioRazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeCondominio & 1) { ?>
                                                                                            <li style="color:#FF0000;">Ingrese apellido y nombre</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Tipo Documento:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDocumentoTipoNombre" id="ConyugeCondominioDocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeCondominioDocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('TiposDocumento', 'GetAll', 'ConyugeCondominioDocumentoTipoNombre', 'FilterTipoDocumentoConyugeCondominio', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDocumentoTipoCodigo" id="ConyugeCondominioDocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeCondominioDocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('DocumentoConyugeCondominio');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeCondominio & 2) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDocumentoNumero" id="ConyugeCondominioDocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeCondominioDocumentoNumero?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeCondominio & 4) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Fecha de Nacimiento:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input name="ConyugeCondominioFechaNacimiento" type="text" class="camporFormularioMediano" id="ConyugeCondominioFechaNacimiento" value="<?=$ConyugeCondominioFechaNacimiento?>" size="12" maxlength="12" />
                                                                                                                <script language="javascript">
                                                                                                                new tcal({'formname': 'frmData', 'controlname': 'ConyugeCondominioFechaNacimiento'});
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeCondominio & 8) { ?>
                                                                                            <li style="color:#FF0000;">Ingrese la fecha de nacimiento</li>
                                                                                            <?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Profesi&oacute;n:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioProfesion" id="ConyugeCondominioProfesion" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeCondominioProfesion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('Profesiones', 'GetAll', 'ConyugeCondominioProfesion', 'FilterProfesionConyugeCondominio', 'IdProfesion', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioProfesionCodigo" id="ConyugeCondominioProfesionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeCondominioProfesionCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddProfesion" class="botonBasico"  onClick="javascript:AddProfesion('ProfesionConyugeCondominio');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td valign="top">
                                                                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Estado Civil:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioEstadoCivil" id="ConyugeCondominioEstadoCivil" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeCondominioEstadoCivil?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarEstadoCivil(this.value);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('EstadosCiviles', 'GetAll', 'ConyugeCondominioEstadoCivil', 'FilterEstadoCivilConyugeCondominio', 'IdEstadoCivil', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioEstadoCivilCodigo" id="ConyugeCondominioEstadoCivilCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeCondominioEstadoCivilCodigo?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddEstadoCivil" class="botonBasico"  onClick="javascript:AddEstadoCivil('EstadoCivilConyugeCondominio');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20"><?php if ($errConyugeCondominio & 16) { ?><li style="color:#FF0000;">Ingrese el estado civil</li><?php } ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Nacionalidad:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <select name="ConyugeCondominioIdNacionalidad" id="ConyugeCondominioIdNacionalidad" class="camporFormularioSimple">
                                                                                                                    <option value="">[SELECCIONE]</option>
                                                                                                                    <?php foreach ($arrPaises as $oPais) { ?>
                                                                                                                    <option value="<?=$oPais->IdPais?>" <?=($ConyugeCondominioIdNacionalidad == $oPais->IdPais) ? 'selected="selected"' : ''?> ><?=$oPais->Nombre?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Localidad:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDomicilioLocalidad" id="ConyugeCondominioDomicilioLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeCondominioDomicilioLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('Localidades', 'GetAllSuggest', 'ConyugeCondominioDomicilioLocalidad', 'FilterDomicilioLocalidadConyugeCondominio', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDomicilioCodigoPostal" id="ConyugeCondominioDomicilioCodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=$ConyugeCondominioDomicilioCodigoPostal?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('DomicilioConyugeCondominio');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Calle:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="ConyugeCondominioDomicilioCalle" id="ConyugeCondominioDomicilioCalle" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeCondominioDomicilioCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">N&uacute;mero:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Piso:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Dpto.:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDomicilioNumero" id="ConyugeCondominioDomicilioNumero" class="camporFormularioChico" maxlength="12" value="<?=$ConyugeCondominioDomicilioNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDomicilioPiso" id="ConyugeCondominioDomicilioPiso" class="camporFormularioChico" maxlength="4" value="<?=$ConyugeCondominioDomicilioPiso?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="ConyugeCondominioDomicilioDpto" id="ConyugeCondominioDomicilioDpto" class="camporFormularioChico" maxlength="4" value="<?=$ConyugeCondominioDomicilioDpto?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr> 
                                                            </table>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                <?php } ?>

                                </table>
                            </td>
						</tr>
					</table>
					<table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr height="30">
                        	<td>
                            	<div align="center">
                                	<input type="button" value="Anterior" class="botonBasico" onclick="javascript: Back();" />
                                    &nbsp;
                        			<input type="button" value="Cancelar" class="botonBasico" onclick="javascript: Cancel();" />
                                    &nbsp;
                        			<input type="button" value="Siguiente" class="botonBasico" onclick="javascript: Next();" />
                            	</div>
                           	</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

</body>
</html>