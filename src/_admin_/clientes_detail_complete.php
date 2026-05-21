<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CLIE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCliente						= intval($_REQUEST['IdCliente']);
$IdTipoPersona					= intval($_REQUEST['IdTipoPersona']);
$RazonSocial					= strval($_REQUEST['RazonSocial']);
$DomicilioCalle					= strval($_REQUEST['DomicilioCalle']);
$DomicilioNumero				= strval($_REQUEST['DomicilioNumero']);
$DomicilioPiso					= strval($_REQUEST['DomicilioPiso']);
$DomicilioDpto					= strval($_REQUEST['DomicilioDpto']);
$DomicilioIdLocalidad			= intval($_REQUEST['DomicilioIdLocalidad']);
$DomicilioCodigoPostal			= intval($_REQUEST['DomicilioCodigoPostal']);
$DomicilioCallePostal			= strval($_REQUEST['DomicilioCallePostal']);
$DomicilioNumeroPostal			= strval($_REQUEST['DomicilioNumeroPostal']);
$DomicilioPisoPostal			= strval($_REQUEST['DomicilioPisoPostal']);
$DomicilioDptoPostal			= strval($_REQUEST['DomicilioDptoPostal']);
$DomicilioIdLocalidadPostal		= intval($_REQUEST['DomicilioIdLocalidadPostal']);
$DomicilioCodigoPostalPostal	= intval($_REQUEST['DomicilioCodigoPostalPostal']);
$NacimientoIdLocalidad			= intval($_REQUEST['NacimientoIdLocalidad']);
$NacimientoCodigoPostal			= intval($_REQUEST['NacimientoCodigoPostal']);
$TelefonoCodigoArea				= strval($_REQUEST['TelefonoCodigoArea']);
$Telefono						= strval($_REQUEST['Telefono']);
$FaxCodigoArea					= strval($_REQUEST['FaxCodigoArea']);
$Fax							= strval($_REQUEST['Fax']);
$DocumentoTipo					= intval($_REQUEST['DocumentoTipo']);
$DocumentoTipoNombre			= strval($_REQUEST['DocumentoTipoNombre']);
$DocumentoTipoCodigo			= strval($_REQUEST['DocumentoTipoCodigo']);
$DocumentoNumero				= strval($_REQUEST['DocumentoNumero']);
$DocumentoExpedido				= strval($_REQUEST['DocumentoExpedido']);
$FechaNacimiento				= strval($_REQUEST['FechaNacimiento']);
$Empresa						= strval($_REQUEST['Empresa']);
$ClaveFiscalTipo				= intval($_REQUEST['ClaveFiscalTipo']);
$ClaveFiscalNumero				= strval($_REQUEST['ClaveFiscalNumero']);
$Email							= strval($_REQUEST['Email']);
$IdUsuario						= intval($_REQUEST['IdUsuario']);
$Usuario						= strval($_REQUEST['Usuario']);
$IdTipoIva						= intval($_REQUEST['IdTipoIva']);
$TipoIva						= strval($_REQUEST['TipoIva']);
$TipoIvaCodigo					= strval($_REQUEST['TipoIvaCodigo']);
$IdProfesion					= intval($_REQUEST['IdProfesion']);
$Profesion						= strval($_REQUEST['Profesion']);
$ProfesionCodigo				= strval($_REQUEST['ProfesionCodigo']);
$IdNacionalidad					= intval($_REQUEST['IdNacionalidad']);
$IdEstadoCivil					= intval($_REQUEST['IdEstadoCivil']);
$EstadoCivil					= strval($_REQUEST['EstadoCivil']);
$EstadoCivilCodigo				= strval($_REQUEST['EstadoCivilCodigo']);
$Nupcia							= intval($_REQUEST['Nupcia']);
$ConyugeNombre					= strval($_REQUEST['ConyugeNombre']);
$ConyugeApellido				= strval($_REQUEST['ConyugeApellido']);
$ConyugeDocumentoTipo			= intval($_REQUEST['ConyugeDocumentoTipo']);
$ConyugeDocumentoTipoNombre		= strval($_REQUEST['ConyugeDocumentoTipoNombre']);
$ConyugeDocumentoTipoCodigo		= strval($_REQUEST['ConyugeDocumentoTipoCodigo']);
$ConyugeDocumentoNumero			= strval($_REQUEST['ConyugeDocumentoNumero']);
$RepresentanteRazonSocial		= strval($_REQUEST['RepresentanteRazonSocial']);
$RepresentanteDocumentoTipo		= intval($_REQUEST['RepresentanteDocumentoTipo']);
$RepresentanteDocumentoTipoNombre	= strval($_REQUEST['RepresentanteDocumentoTipoNombre']);
$RepresentanteDocumentoTipoCodigo	= strval($_REQUEST['RepresentanteDocumentoTipoCodigo']);
$RepresentanteDocumentoNumero	= strval($_REQUEST['RepresentanteDocumentoNumero']);
$EnteJuridicoOtorgacion			= strval($_REQUEST['EnteJuridicoOtorgacion']);
$EnteJuridicoDatosInscripcion	= strval($_REQUEST['EnteJuridicoDatosInscripcion']);
$EnteJuridicoFechaInscripcion	= strval($_REQUEST['EnteJuridicoFechaInscripcion']);
$Submit							= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oClientes			= new Clientes();
$oUsuarios			= new Usuarios();
$oTiposIva 			= new TiposIva();
$oProfesiones 		= new Profesiones();
$oLocalidades 		= new Localidades();
$oTiposDocumento 	= new TiposDocumento();
$oEstadosCiviles 	= new EstadosCiviles();
$oPaises			= new Paises();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oCliente = $oClientes->GetById($IdCliente))
{
	header('Location: clientes.php' . $strParams);
	exit;
}

/* si los datos fueron enviados... */

	$oDocumentoTipo 		= $oTiposDocumento->GetById($oCliente->DocumentoTipo);
	$oEstadoCivil 			= $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);
	$oProfesion 			= $oProfesiones->GetById($oCliente->IdProfesion);
	$oTipoIva 				= $oTiposIva->GetById($oCliente->IdTipoIva);
	$oDocumentoTipoConyuge 	= $oTiposDocumento->GetById($oCliente->ConyugeDocumentoTipo);
	$oDocumentoTipoRepresentante 	= $oTiposDocumento->GetById($oCliente->RepresentanteDocumentoTipo);
	$oUsuario 				= $oUsuarios->GetById($oCliente->IdVendedor);
	$oDomicilioLocalidad 	= $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
	$oDomicilioLocalidadPostal 	= $oLocalidades->GetById($oCliente->DomicilioIdLocalidadPostal);
	$oNacimientoLocalidad 	= $oLocalidades->GetById($oCliente->NacimientoIdLocalidad);
	
	$DocumentoTipo					= $oDocumentoTipo->IdTipoDocumento;
	$DocumentoTipoNombre			= $oDocumentoTipo->Nombre;
	$DocumentoTipoCodigo			= $oDocumentoTipo->Codigo;
	$IdEstadoCivil					= $oEstadoCivil->IdEstadoCivil;
	$EstadoCivil					= $oEstadoCivil->Nombre;
	$EstadoCivilCodigo				= $oEstadoCivil->Codigo;
	$IdProfesion					= $oProfesion->IdProfesion;
	$Profesion						= $oProfesion->Nombre;
	$ProfesionCodigo				= $oProfesion->Codigo;
	$IdTipoIva						= $oTipoIva->IdTipoIva;
	$TipoIva						= $oTipoIva->Nombre;
	$TipoIvaCodigo					= $oTipoIva->Codigo;
	$IdEstadoCivil					= $oEstadoCivil->IdEstadoCivil;
	$EstadoCivil					= $oEstadoCivil->Nombre;
	$EstadoCivilCodigo				= $oEstadoCivil->Codigo;
	$ConyugeDocumentoTipo 			= $oDocumentoTipoConyuge->IdTipoDocumento;
	$ConyugeDocumentoTipoNombre 	= $oDocumentoTipoConyuge->Nombre;
	$ConyugeDocumentoTipoCodigo 	= $oDocumentoTipoConyuge->Codigo;
	$RepresentanteRazonSocial		= $oCliente->RepresentanteRazonSocial;
	$RepresentanteDocumentoTipo 		= $oDocumentoTipoRepresentante->IdTipoDocumento;
	$RepresentanteDocumentoTipoNombre 	= $oDocumentoTipoRepresentante->Nombre;
	$RepresentanteDocumentoTipoCodigo 	= $oDocumentoTipoRepresentante->Codigo;
	$RepresentanteDocumentoNumero 	= $oCliente->RepresentanteDocumentoNumero;
	

	$IdUsuario	= $oUsuario->IdUsuario;
	$Usuario	= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);
	
	$IdTipoPersona 					= $oCliente->IdTipoPersona;
	$RazonSocial 					= $oCliente->RazonSocial;
	$DomicilioCalle 				= $oCliente->DomicilioCalle;
	$DomicilioNumero 				= $oCliente->DomicilioNumero;
	$DomicilioPiso 					= $oCliente->DomicilioPiso;
	$DomicilioDpto 					= $oCliente->DomicilioDpto;
	$DomicilioIdLocalidad	 		= $oCliente->DomicilioIdLocalidad;
	$DomicilioLocalidad	 			= $oDomicilioLocalidad->Nombre;
	$DomicilioCodigoPostal 			= $oCliente->DomicilioCodigoPostal;
	$DomicilioCallePostal			= $oCliente->DomicilioCallePostal;
	$DomicilioNumeroPostal			= $oCliente->DomicilioNumeroPostal;
	$DomicilioPisoPostal			= $oCliente->DomicilioPisoPostal;
	$DomicilioDptoPostal			= $oCliente->DomicilioDptoPostal;
	$DomicilioIdLocalidadPostal		= $oCliente->DomicilioIdLocalidadPostal;
	$DomicilioLocalidadPostal		= $oDomicilioLocalidadPostal->Nombre;
	$DomicilioCodigoPostalPostal	= $oCliente->DomicilioCodigoPostalPostal;
	$NacimientoIdLocalidad 			= $oCliente->NacimientoIdLocalidad;
	$NacimientoLocalidad 			= $oNacimientoLocalidad->Nombre;
	$NacimientoCodigoPostal 		= $oCliente->NacimientoCodigoPostal;
	$TelefonoCodigoArea 			= $oCliente->TelefonoCodigoArea;
	$Telefono 						= $oCliente->Telefono;
	$FaxCodigoArea 					= $oCliente->FaxCodigoArea;
	$Fax 							= $oCliente->Fax;
	$DocumentoNumero 				= $oCliente->DocumentoNumero;
	$DocumentoExpedido 				= $oCliente->DocumentoExpedido;
	$FechaNacimiento 				= CambiarFecha($oCliente->FechaNacimiento);
	$Empresa 						= $oCliente->Empresa;
	$ClaveFiscalTipo 				= $oCliente->ClaveFiscalTipo;
	$ClaveFiscalNumero 				= $oCliente->ClaveFiscalNumero;
	$Email 							= $oCliente->Email;
	$IdNacionalidad 				= $oCliente->IdNacionalidad;
	$Nupcia 						= $oCliente->Nupcia;
	$ConyugeNombre 					= $oCliente->ConyugeNombre;
	$ConyugeApellido 				= $oCliente->ConyugeApellido;
	$ConyugeDocumentoNumero 		= $oCliente->ConyugeDocumentoNumero;
	$EnteJuridicoOtorgacion 		= $oCliente->EnteJuridicoOtorgacion;
	$EnteJuridicoDatosInscripcion 	= $oCliente->EnteJuridicoDatosInscripcion;
	$EnteJuridicoFechaInscripcion 	= CambiarFecha($oCliente->EnteJuridicoFechaInscripcion);


/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterTipoDocumento(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('DocumentoTipoCodigo').value 	= '';
		Get('DocumentoTipoNombre').value 	= '';
		Get('DocumentoTipo').value 			= '';
		Get('DocumentoExpedido').value 		= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('DocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('DocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('DocumentoTipo').value 			= oTipoDocumento.IdTipoDocumento;
	Get('DocumentoExpedido').value 		= oTipoDocumento.Expedido;
}

function FilterTipoDocumentoConyuge(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('ConyugeDocumentoTipoCodigo').value 	= '';
		Get('ConyugeDocumentoTipoNombre').value 	= '';
		Get('ConyugeDocumentoTipo').value 			= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('ConyugeDocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('ConyugeDocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('ConyugeDocumentoTipo').value 			= oTipoDocumento.IdTipoDocumento;
}

function FilterTipoDocumentoRepresentante(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('RepresentanteDocumentoTipoCodigo').value 	= '';
		Get('RepresentanteDocumentoTipoNombre').value 	= '';
		Get('RepresentanteDocumentoTipo').value 			= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('RepresentanteDocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('RepresentanteDocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('RepresentanteDocumentoTipo').value 			= oTipoDocumento.IdTipoDocumento;
}

function FilterTipoIva(IdTipoIva, Nombre)
{
	if ((IdTipoIva == '') && (NumeroNombreVinPrefijo == ''))
	{
		Get('TipoIvaCodigo').value 	= '';
		Get('TipoIva').value 		= '';
		Get('IdTipoIva').value 		= '';
	}

	var oTipoIva = GetTipoIva(IdTipoIva);
	if (!(oTipoIva))
		return;

	Get('TipoIvaCodigo').value 	= oTipoIva.Codigo;
	Get('TipoIva').value 		= oTipoIva.Nombre;
	Get('IdTipoIva').value 		= oTipoIva.IdTipoIva;
}

function FilterProfesion(IdProfesion, Nombre)
{
	if ((IdProfesion == '') && (Nombre == ''))
	{
		Get('ProfesionCodigo').value 	= '';
		Get('Profesion').value 			= '';
		Get('IdProfesion').value 		= '';
	}

	var oProfesion = GetProfesion(IdProfesion);
	if (!(oProfesion))
		return;

	Get('ProfesionCodigo').value 	= oProfesion.Codigo;
	Get('Profesion').value 			= oProfesion.Nombre;
	Get('IdProfesion').value 		= oProfesion.IdProfesion;
}

function FilterEstadoCivil(IdEstadoCivil, Nombre)
{
	if ((IdEstadoCivil == '') && (Nombre == ''))
	{
		Get('EstadoCivilCodigo').value 	= '';
		Get('EstadoCivil').value 		= '';
		Get('IdEstadoCivil').value 		= '';
	}

	var oEstadoCivil = GetEstadoCivil(IdEstadoCivil);
	if (!(oEstadoCivil))
		return;

	Get('EstadoCivilCodigo').value 	= oEstadoCivil.Codigo;
	Get('EstadoCivil').value 		= oEstadoCivil.Nombre;
	Get('IdEstadoCivil').value 		= oEstadoCivil.IdEstadoCivil;
}

function FilterDomicilioLocalidad(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('DomicilioIdLocalidad').value 	= '';
		Get('DomicilioCodigoPostal').value 	= '';
		Get('DomicilioLocalidad').value 	= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('DomicilioIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('DomicilioCodigoPostal').value 	= oLocalidad.CodigoPostal;
	Get('DomicilioLocalidad').value 	= oLocalidad.Nombre;
}

function FilterDomicilioLocalidadPostal(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('DomicilioIdLocalidadPostal').value 	= '';
		Get('DomicilioCodigoPostalPostal').value 	= '';
		Get('DomicilioLocalidadPostal').value 	= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('DomicilioIdLocalidadPostal').value 	= oLocalidad.IdLocalidad;
	Get('DomicilioCodigoPostalPostal').value 	= oLocalidad.CodigoPostal;
	Get('DomicilioLocalidadPostal').value 	= oLocalidad.Nombre;
}

function FilterNacimientoLocalidad(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('NacimientoIdLocalidad').value 	= '';
		Get('NacimientoCodigoPostal').value = '';
		Get('NacimientoLocalidad').value 	= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('NacimientoIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('NacimientoCodigoPostal').value = oLocalidad.CodigoPostal;
	Get('NacimientoLocalidad').value 	= oLocalidad.Nombre;
}

function FilterUsuario(IdUsuario, Nombre)
{
	if ((IdUsuario == '') && (Nombre == ''))
	{
		Get('IdUsuario').value 	= '';
		Get('Usuario').value 	= '';
	}

	var oUsuario = GetUsuario(IdUsuario);
	if (!(oUsuario))
		return;

	Get('IdUsuario').value 	= oUsuario.IdUsuario;
	Get('Usuario').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function VerificarEstadoCivil(IdEstadoCivil)
{
	HideSection('trNupcia');
	HideSection('trEstadoCivilCabezal');
	HideSection('trEstadoCivilDatos');
	
	if ((IdEstadoCivil == 'CASADO') && (IdEstadoCivil != '') && (IdEstadoCivil != '0'))
	{
		ShowSection('trNupcia');
		ShowSection('trEstadoCivilCabezal');
		ShowSection('trEstadoCivilDatos');
	}
}

function VerificarTipoPersona(IdTipoPersona)
{
	HideSection('trEnteJuridicoCabezal');
	HideSection('trEnteJuridicoDatos');	
	HideSection('trDocumentoTipo');
	HideSection('trDocumentoTipoError');
	HideSection('trDocumentoNumero');
	HideSection('trDocumentoNumeroError');
	HideSection('trDocumentoExpedido');
	HideSection('trDocumentoExpedidoError');
	HideSection('trEstadoCivil');
	HideSection('trEstadoCivilError');
	HideSection('trNupcia');
	HideSection('trNacionalidad');
	HideSection('trLugarNacimiento');
	HideSection('trEstadoCivilCabezal');
	HideSection('trEstadoCivilDatos');
	HideSection('trRepresentante');
	HideSection('trRepresentanteCabezal');
	
	if (IdTipoPersona == '<?=PersonaTipos::PersonaJuridica?>')
	{
		ShowSection('trEnteJuridicoCabezal');
		ShowSection('trEnteJuridicoDatos');
		ShowSection('trRepresentante');
		ShowSection('trRepresentanteCabezal');
	}
	else if (IdTipoPersona == '<?=PersonaTipos::PersonaFisica?>')
	{
		var IdEstadoCivil = Get('IdEstadoCivil').value;
		
		ShowSection('trDocumentoTipo');
		ShowSection('trDocumentoTipoError');
		ShowSection('trDocumentoNumero');
		ShowSection('trDocumentoNumeroError');
		ShowSection('trDocumentoExpedido');
		ShowSection('trDocumentoExpedidoError');
		ShowSection('trEstadoCivil');
		ShowSection('trEstadoCivilError');
		ShowSection('trNacionalidad');
		ShowSection('trLugarNacimiento');
	
		if ((IdEstadoCivil != '<?=EstadoCivil::Soltero?>') && (IdEstadoCivil != '') && (IdEstadoCivil != '0'))
		{
			ShowSection('trNupcia');
			ShowSection('trEstadoCivilCabezal');
			ShowSection('trEstadoCivilDatos');
		}
	}
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdCliente" id="IdCliente" value="<?=$IdCliente?>" />
    <input type="hidden" name="ConyugeDocumentoTipo" id="ConyugeDocumentoTipo" value="<?=$ConyugeDocumentoTipo?>" />
    <input type="hidden" name="RepresentanteDocumentoTipo" id="RepresentanteDocumentoTipo" value="<?=$RepresentanteDocumentoTipo?>" />
    <input type="hidden" name="DocumentoTipo" id="DocumentoTipo" value="<?=$DocumentoTipo?>" />
    <input type="hidden" name="IdTipoIva" id="IdTipoIva" value="<?=$IdTipoIva?>" />
    <input type="hidden" name="IdEstadoCivil" id="IdEstadoCivil" value="<?=$IdEstadoCivil?>" />
    <input type="hidden" name="IdProfesion" id="IdProfesion" value="<?=$IdProfesion?>" />
    <input type="hidden" name="DomicilioIdLocalidad" id="DomicilioIdLocalidad" value="<?=$DomicilioIdLocalidad?>" />
	<input type="hidden" name="DomicilioIdLocalidadPostal" id="DomicilioIdLocalidadPostal" value="<?=$DomicilioIdLocalidadPostal?>" />
    <input type="hidden" name="NacimientoIdLocalidad" id="NacimientoIdLocalidad" value="<?=$NacimientoIdLocalidad?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Clientes - Detalles</span></td>
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
                    <table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos Personales</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
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
                                                                                            <td><div id="margen" align="left">Tipo de Persona:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <select disabled="disabled" name="IdTipoPersona" id="IdTipoPersona" class="camporFormularioSimple" onchange="javascript: VerificarTipoPersona(this.value);">
                                                                                                        <option value="">[SELECCIONE]</option>
                                                                                                        <?php foreach (PersonaTipos::GetAll() as $oPersonaTipo) { ?>
                                                                                                        <option value="<?=$oPersonaTipo['IdTipo']?>" <?=($IdTipoPersona == $oPersonaTipo['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oPersonaTipo['Descripcion']?></option>
                                                                                                        <?php } ?>
                                                                                                    </select>                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 1) { ?>
                                                                                <li style="color:#FF0000;">Seleccione el tipo de persona</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Razon Social / Apellido y Nombres:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="RazonSocial" id="RazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$RazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 2) { ?>
                                                                                <li style="color:#FF0000;">Ingrese nombre o raz&oacute;n social</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Cod. Area:</div></td>
                                                                                            <td><div id="margen" align="left">Tel&eacute;fono:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                  <input type="text" readonly="readonly" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="128" value="<?=$TelefonoCodigoArea?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                  <input type="text" readonly="readonly" name="Telefono" id="Telefono" class="camporFormularioMedianoI" maxlength="128" value="<?=$Telefono?>" />
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
                                                                                            <td><div id="margen" align="left">Cod. Area:</div></td>
                                                                                            <td><div id="margen" align="left">Fax:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="FaxCodigoArea" id="FaxCodigoArea" class="camporFormularioChico" maxlength="128" value="<?=$FaxCodigoArea?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="Fax" id="Fax" class="camporFormularioMedianoI" maxlength="128" value="<?=$Fax?>" />
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
                                                                            <tr id="trDocumentoTipo" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Tipo Documento:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="DocumentoTipoNombre" id="DocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$DocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DocumentoTipoCodigo" id="DocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$DocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trDocumentoTipoError" style="display:none;">
                                                                                <td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr id="trDocumentoNumero" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoNumero?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trDocumentoNumeroError" style="display:none;">
                                                                                <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr id="trDocumentoExpedido" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Documento Expedido por:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="DocumentoExpedido" id="DocumentoExpedido" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoExpedido?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trDocumentoExpedidoError" style="display:none;">
                                                                                <td height="20">&nbsp;</td>
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
																									<input  readonly="readonly" name="FechaNacimiento" type="text" class="camporFormularioMediano" id="FechaNacimiento" value="<?=$FechaNacimiento?>" size="12" maxlength="12" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr id="trNacionalidad" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Nacionalidad:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <select disabled="disabled" name="IdNacionalidad" id="IdNacionalidad" class="camporFormularioSimple">
                                                                                                        <option value="">[SELECCIONE]</option>
                                                                                                        <?php foreach ($arrPaises as $oPais) { ?>
                                                                                                        <option value="<?=$oPais->IdPais?>" <?=($IdNacionalidad == $oPais->IdPais) ? 'selected="selected"' : ''?> ><?=$oPais->Nombre?></option>
                                                                                                        <?php } ?>
                                                                                                    </select>                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
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
                                                                                            <td><div id="margen" align="left">Empresa:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input  readonly="readonly" type="text" name="Empresa" id="Empresa" class="camporFormularioSimple" maxlength="128" value="<?=$Empresa?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la emmpresa</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">CUIT / CUIL:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <select disabled="disabled" name="ClaveFiscalTipo" id="ClaveFiscalTipo" class="camporFormularioChico">
                                                                                                                    <?php foreach (ClaveFiscalTipos::GetAll() as $oClaveFiscal) { ?>
                                                                                                                    <option value="<?=$oClaveFiscal['IdTipo']?>" <?=($ClaveFiscalTipo == $oClaveFiscal['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oClaveFiscal['Descripcion']?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="text" readonly="readonly" name="ClaveFiscalNumero" id="ClaveFiscalNumero" class="camporFormularioMedianoI" maxlength="16" value="<?=$ClaveFiscalNumero?>" />
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>                                                                            <tr>
                                                                                <td height="20">
																					<?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el nro. de cuit/cuil</li><?php } ?>
																					<?php if ($err & 8192) { ?><li style="color:#FF0000;">El nro. de cuit/cuil ingresado es incorrecto</li><?php } ?>
																				</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Email:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Vendedor:</div></td>
                                                                                            <td><div id="margen" align="left">Id.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="IdUsuario" id="IdUsuario" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUsuario?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 16) { ?>
                                                                                <li style="color:#FF0000;">Ingrese el vendedor asignado</li>
                                                                                <?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Condici&oacute;n Iva:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input readonly="readonly" type="text" name="TipoIva" id="TipoIva" class="camporFormularioSuggest" maxlength="128" value="<?=$TipoIva?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="TipoIvaCodigo" id="TipoIvaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$TipoIvaCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la condici&oacute;n de iva</li><?php } ?></td>
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
                                                                                                    <input  readonly="readonly" type="text" name="Profesion" id="Profesion" class="camporFormularioSuggest" maxlength="128" value="<?=$Profesion?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="ProfesionCodigo" id="ProfesionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ProfesionCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese la profesi&oacute;n</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr id="trEstadoCivil" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Estado Civil:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input readonly="readonly" type="text" name="EstadoCivil" id="EstadoCivil" class="camporFormularioSuggest" maxlength="128" value="<?=$EstadoCivil?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarEstadoCivil(this.value);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="EstadoCivilCodigo" id="EstadoCivilCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$EstadoCivilCodigo?>" readonly="readonly" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trEstadoCivilError" style="display:none;">
                                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el estado civil</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr id="trNupcia" style="display:none;">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Nupcia:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="Nupcia" id="Nupcia" class="camporFormularioChico" maxlength="3" value="<?=$Nupcia?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trEstadoCivilCabezal" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos del Conyuge</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trEstadoCivilDatos" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Nombre:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="ConyugeNombre" id="ConyugeNombre" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                            														</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Apellido:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="ConyugeApellido" id="ConyugeApellido" class="camporFormularioSimple" maxlength="255" value="<?=$ConyugeApellido?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input readonly="readonly" type="text" name="ConyugeDocumentoTipoNombre" id="ConyugeDocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$ConyugeDocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="ConyugeDocumentoTipoCodigo" id="ConyugeDocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ConyugeDocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
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
                                                                                            <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="ConyugeDocumentoNumero" id="ConyugeDocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$ConyugeDocumentoNumero?>" />
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
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>&nbsp;</td>
                                    </tr>
									<tr id="trRepresentanteCabezal" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos del Representante</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trRepresentante" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0" valign="top">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Nombre:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="RepresentanteRazonSocial" id="RepresentanteRazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$RepresentanteRazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                            														</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input readonly="readonly" type="text" name="RepresentanteDocumentoTipoNombre" id="RepresentanteDocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$RepresentanteDocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="RepresentanteDocumentoTipoCodigo" id="RepresentanteDocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$RepresentanteDocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
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
                                                                                            <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="RepresentanteDocumentoNumero" id="RepresentanteDocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$RepresentanteDocumentoNumero?>" />
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
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>&nbsp;</td>
                                    </tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Domicilio Fiscal</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input readonly="readonly" type="text" name="DomicilioLocalidad" id="DomicilioLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$DomicilioLocalidad?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="DomicilioCodigoPostal" id="DomicilioCodigoPostal" class="camporFormularioChico" maxlength="10" value="<?=$DomicilioCodigoPostal?>" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
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
                                                                                        <input type="text" name="DomicilioCalle"  readonly="readonly" id="DomicilioCalle" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td valign="top">&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input type="text" readonly="readonly" name="DomicilioNumero" id="DomicilioNumero" class="camporFormularioChico" maxlength="12" value="<?=$DomicilioNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="DomicilioPiso" id="DomicilioPiso" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioPiso?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="DomicilioDpto" id="DomicilioDpto" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioDpto?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr id="trLugarNacimiento">
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Localidad de Nacimiento:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" readonly="readonly" name="NacimientoLocalidad" id="NacimientoLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$NacimientoLocalidad?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text"  readonly="readonly" name="NacimientoCodigoPostal" id="NacimientoCodigoPostal" class="camporFormularioChico" maxlength="10" value="<?=$NacimientoCodigoPostal?>" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Domicilio Postal</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input  readonly="readonly" type="text" name="DomicilioLocalidadPostal" id="DomicilioLocalidadPostal" class="camporFormularioSuggest" maxlength="128" value="<?=$DomicilioLocalidadPostal?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input  readonly="readonly" type="text" name="DomicilioCodigoPostalPostal" id="DomicilioCodigoPostalPostal" class="camporFormularioChico" maxlength="10" value="<?=$DomicilioCodigoPostalPostal?>" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
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
                                                                                        <input readonly="readonly" type="text" name="DomicilioCallePostal" id="DomicilioCallePostal" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioCallePostal?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td valign="top">&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
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
                                                                                                    <input readonly="readonly" type="text" name="DomicilioNumeroPostal" id="DomicilioNumeroPostal" class="camporFormularioChico" maxlength="12" value="<?=$DomicilioNumeroPostal?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input readonly="readonly" type="text" name="DomicilioPisoPostal" id="DomicilioPisoPostal" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioPisoPostal?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input readonly="readonly" type="text" name="DomicilioDptoPostal" id="DomicilioDptoPostal" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioDptoPostal?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>                                                                            
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									
									<tr id="trEnteJuridicoCabezal" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Ente Jur&iacute;dico</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trEnteJuridicoDatos" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Personer&iacute;a otorgada por:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="EnteJuridicoOtorgacion" id="EnteJuridicoOtorgacion" class="camporFormularioSimple" maxlength="128" value="<?=$EnteJuridicoOtorgacion?>" onkeyup="javascript: StrToUpper(this.id);" />
                            														</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 1024) { ?>
                                                                                <li style="color:#FF0000;">Ingrese otorgaci&oacute;n de personer&iacute;a</li>
                                                                                <?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">N&uacute;mero o datos de inscripci&oacute;n o creaci&oacute;n:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="EnteJuridicoDatosInscripcion" id="EnteJuridicoDatosInscripcion" class="camporFormularioSimple" maxlength="128" value="<?=$EnteJuridicoDatosInscripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 2048) { ?>
                                                                                <li style="color:#FF0000;">Ingrese nro. de inscripci&oacute;n</li>
                                                                                <?php } ?></td>
                                                                            </tr>
                                                                       	</table>
                                                                  	</td>
                                                                    <td>&nbsp;</td>
                                                                    <td valign="top">
                                                                    	<table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de inscripci&oacute;n o creaci&oacute;n:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" name="EnteJuridicoFechaInscripcion" type="text" class="camporFormularioMediano" id="EnteJuridicoFechaInscripcion" value="<?=$EnteJuridicoFechaInscripcion?>" size="12" maxlength="12" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4096) { ?>
                                                                                <li style="color:#FF0000;">Ingrese la fecha de creaci&oacute;n</li>
                                                                                <?php } ?></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
                                </table>						
                            </td>
                        </tr>
                    </table>
                    <table width="75%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td height="1"><div align="center"></div></td>
                        </tr>
                    </table>
                    <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                        <tr>
                            <td height="30">
                                <div align="center">
                                    <input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'clientes.php<?=$strParams?>';" value="Volver" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>

<script language="javascript">

VerificarEstadoCivil('<?=$EstadoCivil?>');
VerificarTipoPersona('<?=$IdTipoPersona?>');

</script>

</body>
</html>