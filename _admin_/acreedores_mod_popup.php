<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para acreedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ACRE_UPDATE))
	Session::NoPerm();

/* obtiene datos del formulario */
$IdAcreedor						= intval($_REQUEST['IdAcreedor']);
$IdTipoPersona					= intval($_REQUEST['IdTipoPersona']);
$NumeroInscripcion				= strval($_REQUEST['NumeroInscripcion']);
$RazonSocial					= strval($_REQUEST['RazonSocial']);
$DomicilioCalle					= strval($_REQUEST['DomicilioCalle']);
$DomicilioNumero				= strval($_REQUEST['DomicilioNumero']);
$DomicilioPiso					= strval($_REQUEST['DomicilioPiso']);
$DomicilioDpto					= strval($_REQUEST['DomicilioDpto']);
$DomicilioIdLocalidad			= intval($_REQUEST['DomicilioIdLocalidad']);
$DomicilioCodigoPostal			= strval($_REQUEST['DomicilioCodigoPostal']);
$TelefonoCodigoArea				= strval($_REQUEST['TelefonoCodigoArea']);
$Telefono						= strval($_REQUEST['Telefono']);
$DocumentoTipo					= intval($_REQUEST['DocumentoTipo']);
$DocumentoTipoNombre			= strval($_REQUEST['DocumentoTipoNombre']);
$DocumentoTipoCodigo			= strval($_REQUEST['DocumentoTipoCodigo']);
$DocumentoNumero				= strval($_REQUEST['DocumentoNumero']);
$DocumentoExpedido				= strval($_REQUEST['DocumentoExpedido']);
$FechaNacimiento				= strval($_REQUEST['FechaNacimiento']);
$ClaveFiscalTipo				= intval($_REQUEST['ClaveFiscalTipo']);
$ClaveFiscalNumero				= strval($_REQUEST['ClaveFiscalNumero']);
$Email							= strval($_REQUEST['Email']);
$IdNacionalidad					= intval($_REQUEST['IdNacionalidad']);
$EnteJuridicoOtorgacion			= strval($_REQUEST['EnteJuridicoOtorgacion']);
$EnteJuridicoDatosInscripcion	= strval($_REQUEST['EnteJuridicoDatosInscripcion']);
$EnteJuridicoFechaInscripcion	= strval($_REQUEST['EnteJuridicoFechaInscripcion']);
$Submit							= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oAcreedor 			= new Acreedor();
$oAcreedores		= new Acreedores();
$oTiposDocumento	= new TiposDocumento();
$oLocalidades		= new Localidades();
$oPaises			= new Paises();

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oAcreedor = $oAcreedores->GetById($IdAcreedor))
{
	header('Location: acreedores.php' . $strParams);
	exit;
}

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($NumeroInscripcion == '')
		$err |= 1;
	if ($IdTipoPersona == '')
		$err |= 2;
	if ($RazonSocial == '')
		$err |= 4;
	if ($ClaveFiscalNumero == '')
		$err |= 8;

	/* validaciones segun el tipo de persona que cargamos */
	if ($IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		if ($DocumentoTipo == '')
			$err |= 16;
		if ($DocumentoNumero == '')
			$err |= 32;
	}
	elseif ($IdTipoPersona == PersonaTipos::PersonaJuridica)
	{
		if ($EnteJuridicoOtorgacion == '')
			$err |= 64;
		if ($EnteJuridicoDatosInscripcion == '')
			$err |= 128;
		if ($EnteJuridicoFechaInscripcion == '')
			$err |= 256;
	}

	/* si no hay errores... */
	if ($err == 0)
	{
		/* anulamos los campos no utilizados segun el tipo de persona */
		if ($IdTipoPersona == PersonaTipos::PersonaFisica)
		{
			$EnteJuridicoOtorgacion 		= '';
			$EnteJuridicoDatosInscripcion 	= '';
			$EnteJuridicoFechaInscripcion 	= '';
		}
		elseif ($IdTipoPersona == PersonaTipos::PersonaJuridica)
		{
			$DocumentoTipo 		= '';
			$DocumentoNumero 	= '';
			$DocumentoExpedido 	= '';
			$Nacionalidad 		= '';
		}
		
		$oAcreedor->IdTipoPersona 					= $IdTipoPersona;
		$oAcreedor->IdNacionalidad 					= $IdNacionalidad;
		$oAcreedor->NumeroInscripcion 				= $NumeroInscripcion;
		$oAcreedor->RazonSocial 					= $RazonSocial;
		$oAcreedor->DomicilioCalle 					= $DomicilioCalle;
		$oAcreedor->DomicilioNumero 				= $DomicilioNumero;
		$oAcreedor->DomicilioPiso 					= $DomicilioPiso;
		$oAcreedor->DomicilioDpto 					= $DomicilioDpto;
		$oAcreedor->DomicilioIdLocalidad	 		= $DomicilioIdLocalidad;
		$oAcreedor->TelefonoCodigoArea 				= $TelefonoCodigoArea;
		$oAcreedor->Telefono 						= $Telefono;
		$oAcreedor->DocumentoTipo 					= $DocumentoTipo;
		$oAcreedor->DocumentoNumero 				= $DocumentoNumero;
		$oAcreedor->DocumentoExpedido 				= $DocumentoExpedido;
		$oAcreedor->FechaNacimiento 				= $FechaNacimiento;
		$oAcreedor->ClaveFiscalTipo 				= $ClaveFiscalTipo;
		$oAcreedor->ClaveFiscalNumero 				= $ClaveFiscalNumero;
		$oAcreedor->Email 							= strtolower($Email);
		$oAcreedor->EnteJuridicoOtorgacion 			= $EnteJuridicoOtorgacion;
		$oAcreedor->EnteJuridicoDatosInscripcion 	= $EnteJuridicoDatosInscripcion;
		$oAcreedor->EnteJuridicoFechaInscripcion 	= $EnteJuridicoFechaInscripcion;

		/* crea el usuario */
		$oAcreedor = $oAcreedores->Update($oAcreedor);
		
		$Update = true;
	}
}
else
{
	$oDocumentoTipo 		= $oTiposDocumento->GetById($oAcreedor->DocumentoTipo);
	$oDomicilioLocalidad 	= $oLocalidades->GetById($oAcreedor->DomicilioIdLocalidad);
	
	$DocumentoTipo			= $oDocumentoTipo->IdTipoDocumento;
	$DocumentoTipoNombre	= $oDocumentoTipo->Nombre;
	$DocumentoTipoCodigo	= $oDocumentoTipo->Codigo;

	$IdTipoPersona 					= $oAcreedor->IdTipoPersona;
	$NumeroInscripcion 				= $oAcreedor->NumeroInscripcion;
	$RazonSocial 					= $oAcreedor->RazonSocial;
	$DomicilioCalle 				= $oAcreedor->DomicilioCalle;
	$DomicilioNumero 				= $oAcreedor->DomicilioNumero;
	$DomicilioPiso 					= $oAcreedor->DomicilioPiso;
	$DomicilioDpto 					= $oAcreedor->DomicilioDpto;
	$DomicilioIdLocalidad	 		= $oAcreedor->DomicilioIdLocalidad;
	$DomicilioLocalidad	 			= $oDomicilioLocalidad->Nombre;
	$DomicilioCodigoPostal 			= $oDomicilioLocalidad->CodigoPostal;
	$TelefonoCodigoArea 			= $oAcreedor->TelefonoCodigoArea;
	$Telefono 						= $oAcreedor->Telefono;
	$DocumentoNumero 				= $oAcreedor->DocumentoNumero;
	$DocumentoExpedido 				= $oAcreedor->DocumentoExpedido;
	$FechaNacimiento 				= CambiarFecha($oAcreedor->FechaNacimiento);
	$ClaveFiscalTipo 				= $oAcreedor->ClaveFiscalTipo;
	$ClaveFiscalNumero 				= $oAcreedor->ClaveFiscalNumero;
	$Email 							= $oAcreedor->Email;
	$IdNacionalidad 				= $oAcreedor->IdNacionalidad;
	$EnteJuridicoOtorgacion 		= $oAcreedor->EnteJuridicoOtorgacion;
	$EnteJuridicoDatosInscripcion 	= $oAcreedor->EnteJuridicoDatosInscripcion;
	$EnteJuridicoFechaInscripcion 	= CambiarFecha($oAcreedor->EnteJuridicoFechaInscripcion);
}

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
	HideSection('trNacionalidad');
	HideSection('trNacionalidadError');
	
	if (IdTipoPersona == '<?=PersonaTipos::PersonaJuridica?>')
	{
		ShowSection('trEnteJuridicoCabezal');
		ShowSection('trEnteJuridicoDatos');
	}
	else if (IdTipoPersona == '<?=PersonaTipos::PersonaFisica?>')
	{
		ShowSection('trDocumentoTipo');
		ShowSection('trDocumentoTipoError');
		ShowSection('trDocumentoNumero');
		ShowSection('trDocumentoNumeroError');
		ShowSection('trDocumentoExpedido');
		ShowSection('trDocumentoExpedidoError');
		ShowSection('trNacionalidad');
		ShowSection('trNacionalidadError');
	}
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="DocumentoTipo" id="DocumentoTipo" value="<?=$DocumentoTipo?>" />
    <input type="hidden" name="DomicilioIdLocalidad" id="DomicilioIdLocalidad" value="<?=$DomicilioIdLocalidad?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Acreedores - Agregar</span></td>
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
                                                                                            <td><div id="margen" align="left">N&uacute;mero Inscripci&oacute;n:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="NumeroInscripcion" id="NumeroInscripcion" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroInscripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nro. de inscripci&oacute;n</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Tipo de Acreedor:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <select name="IdTipoPersona" id="IdTipoPersona" class="camporFormularioSimple" onchange="javascript: VerificarTipoPersona(this.value);">
                                                                                                        <option value="">[SELECCIONE]</option>
                                                                                                        <?php foreach (PersonaTipos::GetAll() as $oPersonaTipo) { ?>
                                                                                                        <option value="<?=$oPersonaTipo['IdTipo']?>" <?=($IdTipoPersona == $oPersonaTipo['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oPersonaTipo['Descripcion']?></option>
                                                                                                        <?php } ?>
                                                                                                    </select>                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione el tipo de cliente</li><?php } ?></td>
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
                                                                                                    <input type="text" name="RazonSocial" id="RazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$RazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la raz&oacute;n social</li><?php } ?></td>
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
                                                                                                  <input type="text" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="128" value="<?=$TelefonoCodigoArea?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                  <input type="text" name="Telefono" id="Telefono" class="camporFormularioMedianoI" maxlength="128" value="<?=$Telefono?>" />
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
                                                                                                    <input type="text" name="DocumentoTipoNombre" id="DocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$DocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('TiposDocumento', 'GetAll', 'DocumentoTipoNombre', 'FilterTipoDocumento', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DocumentoTipoCodigo" id="DocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$DocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td style="display:none;"><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('Documento');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trDocumentoTipoError" style="display:none;">
                                                                                <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
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
                                                                                                    <input type="text" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoNumero?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr id="trDocumentoNumeroError" style="display:none;">
                                                                                <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
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
                                                                                                    <input type="text" name="DocumentoExpedido" id="DocumentoExpedido" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoExpedido?>" onkeyup="javascript: StrToUpper(this.id);" />
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
																									<input name="FechaNacimiento" type="text" class="camporFormularioMediano" id="FechaNacimiento" value="<?=$FechaNacimiento?>" size="12" maxlength="12" />
																									<script language="javascript">
                                                                                                    new tcal({'formname': 'frmData', 'controlname': 'FechaNacimiento'});
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
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
                                                                                            <td><div id="margen" align="left">CUIT / CUIL:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <select name="ClaveFiscalTipo" id="ClaveFiscalTipo" class="camporFormularioChico">
                                                                                                                    <?php foreach (ClaveFiscalTipos::GetAll() as $oClaveFiscal) { ?>
                                                                                                                    <option value="<?=$oClaveFiscal['IdTipo']?>" <?=($ClaveFiscalTipo == $oClaveFiscal['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oClaveFiscal['Descripcion']?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="text" name="ClaveFiscalNumero" id="ClaveFiscalNumero" class="camporFormularioMedianoI" maxlength="16" value="<?=$ClaveFiscalNumero?>" />
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el nro. de cuit/cuil</li><?php } ?></td>
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
                                                                                                    <input type="text" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>                                                                            <tr>
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
                                                                                                    <select name="IdNacionalidad" id="IdNacionalidad" class="camporFormularioSimple">
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
                                                                            <tr id="trNacionalidadError" style="display:none;">
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
                                                                                                    <input type="text" name="DomicilioLocalidad" id="DomicilioLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$DomicilioLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Localidades', 'GetAllSuggest', 'DomicilioLocalidad', 'FilterDomicilioLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DomicilioCodigoPostal" id="DomicilioCodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=$DomicilioCodigoPostal?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td style="display:none;"><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('Domicilio');" value=" + " /></td>
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
                                                                                        <input type="text" name="DomicilioCalle" id="DomicilioCalle" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                                    <input type="text" name="DomicilioNumero" id="DomicilioNumero" class="camporFormularioChico" maxlength="12" value="<?=$DomicilioNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DomicilioPiso" id="DomicilioPiso" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioPiso?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DomicilioDpto" id="DomicilioDpto" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioDpto?>" />
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
                                                                                        <input type="text" name="EnteJuridicoOtorgacion" id="EnteJuridicoOtorgacion" class="camporFormularioSimple" maxlength="128" value="<?=$EnteJuridicoOtorgacion?>" onkeyup="javascript: StrToUpper(this.id);" />
                            														</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 64) { ?>
                                                                                <li style="color:#FF0000;">Ingrese otorgaci&oacute;n de personer&iacute;a</li>
                                                                                <?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">N&uacute;mero o datos de inscripci&oacute;n o creaci&oacute;n:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="EnteJuridicoDatosInscripcion" id="EnteJuridicoDatosInscripcion" class="camporFormularioSimple" maxlength="128" value="<?=$EnteJuridicoDatosInscripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 128) { ?>
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
                                                                                        <input name="EnteJuridicoFechaInscripcion" type="text" class="camporFormularioMediano" id="EnteJuridicoFechaInscripcion" value="<?=$EnteJuridicoFechaInscripcion?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'EnteJuridicoFechaInscripcion'});
                                                                                        </script>
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 256) { ?>
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
                                    <input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.close();" value="Cancelar" />
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
VerificarTipoPersona('<?=$IdTipoPersona?>');
</script>

<?php if ($Update) { ?>
<script language="javascript">
window.close();
</script>
<?php } ?>

</body>
</html>