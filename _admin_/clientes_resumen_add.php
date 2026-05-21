<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CLIE_CREATE))
	Session::NoPerm();

/* obtiene datos del formulario */
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
$NacimientoIdLocalidad			= isset($_REQUEST['NacimientoIdLocalidad']) ? intval($_REQUEST['NacimientoIdLocalidad']) : '';
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
$IdUsuario						= Session::GetCurrentUser()->IdUsuario;
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
$Conyuge						= intval($_REQUEST['Conyuge']);
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
$Condominio						= intval($_REQUEST['Condominio']);
$Reventa						= intval($_REQUEST['Reventa']);
$Submit							= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oCliente 	= new Cliente();
$oClientes	= new Clientes();
$oUsuarios	= new Usuarios();
$oPaises	= new Paises();
$oTiposDocumento = new TiposDocumento();

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdTipoPersona == '')
		$err |= 1;
	if ($RazonSocial == '')
		$err |= 2;
	/*if ($IdUsuario == '')
		$err |= 16;
	if ($IdTipoIva == '')
		$err |= 32;	
		*/
	if ($ClaveFiscalNumero != '' && !CPcuitValido($ClaveFiscalNumero))
		$err |= 8192;

	/* validaciones segun el tipo de persona que cargamos */
	/*if ($IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		if ($DocumentoTipo == '')
			$err |= 128;
		if ($DocumentoNumero == '')
			$err |= 256;	
	}
	elseif ($IdTipoPersona == PersonaTipos::PersonaJuridica)
	{
		/*if ($EnteJuridicoOtorgacion == '')
			$err |= 1024;
		if ($EnteJuridicoDatosInscripcion == '')
			$err |= 2048;
		if ($EnteJuridicoFechaInscripcion == '')
			$err |= 4096;*/
		/*if ($ClaveFiscalNumero == '')
			$err |= 8;
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
			
			$RepresentanteRazonSocial		= '';
			$RepresentanteDocumentoTipo		= '';
			$RepresentanteDocumentoTipoNombre	= '';
			$RepresentanteDocumentoTipoCodigo	= '';
			$RepresentanteDocumentoNumero	= '';
		}
		elseif ($IdTipoPersona == PersonaTipos::PersonaJuridica)
		{
			$DocumentoTipo 				= '';
			$DocumentoNumero 			= '';
			$DocumentoExpedido 			= '';
			$IdEstadoCivil 				= '';
			$Nupcia 					= '';
			$Nacionalidad 				= '';
			$ConyugeNombre				= '';
			$ConyugeApellido			= '';
			$ConyugeDocumentoTipo		= '';
			$ConyugeDocumentoTipoNombre	= '';
			$ConyugeDocumentoTipoCodigo	= '';
			$ConyugeDocumentoNumero		= '';
		}
		
		/* si el estado civil no es casado, anulamos los campos de conyuge */
		if ($IdEstadoCivil != EstadoCivil::Casado)
		{
			$Nupcia 					= '';
			$ConyugeNombre				= '';
			$ConyugeApellido			= '';
			$ConyugeDocumentoTipo		= '';
			$ConyugeDocumentoTipoNombre	= '';
			$ConyugeDocumentoTipoCodigo	= '';
			$ConyugeDocumentoNumero		= '';
		}
		
		$oCliente->IdTipoPersona 				= $IdTipoPersona;
		$oCliente->RazonSocial 					= $RazonSocial;
		$oCliente->DomicilioCalle 				= $DomicilioCalle;
		$oCliente->DomicilioNumero 				= $DomicilioNumero;
		$oCliente->DomicilioPiso 				= $DomicilioPiso;
		$oCliente->DomicilioDpto 				= $DomicilioDpto;
		$oCliente->DomicilioIdLocalidad	 		= $DomicilioIdLocalidad;
		$oCliente->DomicilioCodigoPostal 		= $DomicilioCodigoPostal;
		$oCliente->DomicilioCallePostal			= $DomicilioCallePostal;
		$oCliente->DomicilioNumeroPostal		= $DomicilioNumeroPostal;
		$oCliente->DomicilioPisoPostal			= $DomicilioPisoPostal;
		$oCliente->DomicilioDptoPostal			= $DomicilioDptoPostal;
		$oCliente->DomicilioIdLocalidadPostal	= $DomicilioIdLocalidadPostal;
		$oCliente->DomicilioCodigoPostalPostal	= $DomicilioCodigoPostalPostal;
		$oCliente->NacimientoIdLocalidad 		= $NacimientoIdLocalidad;
		$oCliente->NacimientoCodigoPostal 		= $NacimientoCodigoPostal;
		$oCliente->TelefonoCodigoArea 			= $TelefonoCodigoArea;
		$oCliente->Telefono 					= $Telefono;
		$oCliente->FaxCodigoArea 				= $FaxCodigoArea;
		$oCliente->Fax 							= $Fax;
		$oCliente->DocumentoTipo 				= $DocumentoTipo;
		$oCliente->DocumentoNumero 				= $DocumentoNumero;
		$oCliente->DocumentoExpedido 			= $DocumentoExpedido;
		$oCliente->FechaNacimiento 				= $FechaNacimiento;
		$oCliente->Empresa 						= $Empresa;
		$oCliente->ClaveFiscalTipo 				= $ClaveFiscalTipo;
		$oCliente->ClaveFiscalNumero 			= $ClaveFiscalNumero;
		$oCliente->Email 						= strtolower($Email);
		$oCliente->IdVendedor 					= $IdUsuario;
		$oCliente->IdTipoIva 					= $IdTipoIva;
		$oCliente->IdProfesion 					= $IdProfesion;
		$oCliente->IdNacionalidad 				= $IdNacionalidad;
		$oCliente->IdEstadoCivil 				= $IdEstadoCivil;
		$oCliente->Nupcia 						= $Nupcia;
		$oCliente->ConyugeNombre 				= $ConyugeNombre;
		$oCliente->ConyugeApellido 				= $ConyugeApellido;
		$oCliente->ConyugeDocumentoTipo 		= $ConyugeDocumentoTipo;
		$oCliente->ConyugeDocumentoNumero 		= $ConyugeDocumentoNumero;
		$oCliente->RepresentanteRazonSocial		= $RepresentanteRazonSocial;
		$oCliente->RepresentanteDocumentoTipo 	= $RepresentanteDocumentoTipo;
		$oCliente->RepresentanteDocumentoNumero = $RepresentanteDocumentoNumero;
		$oCliente->EnteJuridicoOtorgacion 		= $EnteJuridicoOtorgacion;
		$oCliente->EnteJuridicoDatosInscripcion = $EnteJuridicoDatosInscripcion;
		$oCliente->EnteJuridicoFechaInscripcion = $EnteJuridicoFechaInscripcion;

		/* crea el usuario */
		$oCliente = $oClientes->Create($oCliente);
		$oClientes->ActualizarPercepciones($oCliente);

		if (!$popup)
		{
			header("Location: clientes.php" . $strParams);
			exit();
		}
		else
		{
			$Create = true;
		}
	}
}
else
{
	$FechaNacimiento 				= '';
	$EnteJuridicoFechaInscripcion 	= '';
	$IdUsuario						= Session::GetCurrentUser()->IdUsuario;
	$IdTipoIva						= TipoIva::CF;
	$IdNacionalidad					= 13;
	
	if ($Conyuge)
	{
		$oClienteConyuge = $oClientes->GetById($Conyuge);
		
		$IdTipoPersona = PersonaTipos::PersonaFisica;
		$DocumentoTipo = $oClienteConyuge->ConyugeDocumentoTipo;
		$DocumentoNumero = $oClienteConyuge->ConyugeDocumentoNumero;
		$RazonSocial = $oClienteConyuge->ConyugeNombre . ' ' . $oClienteConyuge->ConyugeApellido;
		if ($oTipoDocumentoConyuge = $oTiposDocumento->GetById($DocumentoTipo))
		{
			$DocumentoExpedido = $oTipoDocumentoConyuge->Expedido;
		}
		
		$IdEstadoCivil = EstadoCivil::Casado;
		
		$ConyugeNombre 				= $oClienteConyuge->RazonSocial;
		$ConyugeDocumentoTipo 		= $oClienteConyuge->DocumentoTipo;
		$ConyugeDocumentoNumero 	= $oClienteConyuge->DocumentoNumero;
	}
	
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php 
	include('include/head.inc.php'); 
	require_once('../library/suggest/include.php'); 
	/* incluimkos funcion para armar suggest */
	IncludeSUGGEST();
?>

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
}

$j(document).ready(function() {
	VerificarTipoPersona('<?=$IdTipoPersona?>');
	<?php
	if ($IdTipoIva)
	{
	?>
		FilterTipoIva('<?= $IdTipoIva ?>', '');
	<?php
	}
	if ($Create) 
	{ 
		
	?>
		window.opener.FilterCliente('<?=$oCliente->IdCliente?>', '');
		window.close();
	<?php
	}
	?>
});

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdTipoIva" id="IdTipoIva" value="<?=$IdTipoIva?>" />
   
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Clientes - Agregar</span></td>
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
                                                                                                    <input type="text" name="RazonSocial" id="RazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$RazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
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
                                                                                <td height="20">
																					<?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el nro. de cuit/cuil</li><?php } ?>
																					<?php if ($err & 8192) { ?><li style="color:#FF0000;">El nro. de cuit/cuil ingresado es incorrecto</li><?php } ?>
																				</td>
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
                                                                                                    <input type="text" name="TipoIva" id="TipoIva" class="camporFormularioSuggest" maxlength="128" value="<?=$TipoIva?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('TiposIva', 'GetAll', 'TipoIva', 'FilterTipoIva', 'IdTipoIva', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="TipoIvaCodigo" id="TipoIvaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$TipoIvaCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddTipoIva" class="botonBasico"  onClick="javascript:AddTipoIva();" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la condici&oacute;n de iva</li><?php } ?></td>
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
                                                                                                    <input type="text" name="FaxCodigoArea" id="FaxCodigoArea" class="camporFormularioChico" maxlength="128" value="<?=$FaxCodigoArea?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="Fax" id="Fax" class="camporFormularioMedianoI" maxlength="128" value="<?=$Fax?>" />
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
																		</table>
																	</td>
																	<td width="10">&nbsp;</td>
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
                                                                                                    <input type="text" name="DomicilioLocalidad" id="DomicilioLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$DomicilioLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Localidades', 'GetAllSuggest', 'DomicilioLocalidad', 'FilterDomicilioLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DomicilioCodigoPostal" id="DomicilioCodigoPostal" class="camporFormularioChico" maxlength="10" value="<?=$DomicilioCodigoPostal?>" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('Domicilio');" value=" + " /></td>
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
									<?php
									if  (!$popup)
									{
									?>
                                    <input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'clientes.php<?=$strParams?>';" value="Cancelar" />
									<?php
									}
									else
									{
									?>
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.close();" value="Cancelar" />
									<?php
									}
									?>
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

</body>
</html>