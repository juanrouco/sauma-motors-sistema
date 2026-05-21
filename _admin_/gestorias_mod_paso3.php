<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_UPDATE))
	Session::NoPerm();

/* solo podemos acceder si tenemos el id de fatura */
if (GestoriaCreate::$IdMinuta == '')
	exit();

/* obtiene datos enviados */
$Action	= strval($_REQUEST['MainAction']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err 				= 0;
$errFiador 			= 0;
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposDocumento 	= new TiposDocumento();
$oProfesiones 		= new Profesiones();
$oLocalidades 		= new Localidades();
$oEstadosCiviles 	= new EstadosCiviles();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oPaises 			= new Paises();

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
	header("Location: gestorias_mod_paso5.php" . $strParams);
	exit;
}

/* si el formulario fue enviado */
if ($Submit)
{
	GestoriaCreate::$Acreedor 						= $_REQUEST['Acreedor'];
	GestoriaCreate::$IdAcreedor 					= $_REQUEST['IdAcreedor'];
	GestoriaCreate::$FinanciacionCapital 			= $_REQUEST['FinanciacionCapital'];
	GestoriaCreate::$CantidadCuotas 				= $_REQUEST['CantidadCuotas'];
	GestoriaCreate::$ImporteCuota 					= $_REQUEST['ImporteCuota'];
	GestoriaCreate::$FechaVencimientoPrimerCuota 	= $_REQUEST['FechaVencimientoPrimerCuota'];
	GestoriaCreate::$TasaNominal 					= $_REQUEST['TasaNominal'];
	GestoriaCreate::$TasaEfectiva 					= $_REQUEST['TasaEfectiva'];
	GestoriaCreate::$CostoFinancieroTotal 			= $_REQUEST['CostoFinancieroTotal'];
	GestoriaCreate::$Observaciones 					= $_REQUEST['Observaciones'];

	/* procesamos la acción principal */
	switch ($Action)
	{
		case 'AddFiador':
		
			/* obtenemos los datos para la cedula */
			$Code					= strval($_REQUEST['Code']);
			$Descripcion 			= strval($_REQUEST['Descripcion']);
			$Posicion 				= intval($_REQUEST['Posicion']);
			$RazonSocial 			= strval($_REQUEST['RazonSocial']);
			$DocumentoTipo 			= intval($_REQUEST['DocumentoTipo']);
			$DocumentoTipoNombre 	= strval($_REQUEST['DocumentoTipoNombre']);
			$DocumentoTipoCodigo 	= strval($_REQUEST['DocumentoTipoCodigo']);
			$DocumentoNumero 		= strval($_REQUEST['DocumentoNumero']);
			$FechaNacimiento 		= strval($_REQUEST['FechaNacimiento']);
			$IdProfesion 			= intval($_REQUEST['IdProfesion']);
			$IdEstadoCivil 			= intval($_REQUEST['IdEstadoCivil']);
			$IdNacionalidad 		= intval($_REQUEST['IdNacionalidad']);
			$DomicilioCalle			= strval($_REQUEST['DomicilioCalle']);
			$DomicilioNumero		= strval($_REQUEST['DomicilioNumero']);
			$DomicilioPiso			= strval($_REQUEST['DomicilioPiso']);
			$DomicilioDpto			= strval($_REQUEST['DomicilioDpto']);
			$DomicilioIdLocalidad	= intval($_REQUEST['DomicilioIdLocalidad']);
			$DomicilioCodigoPostal	= strval($_REQUEST['DomicilioCodigoPostal']);
		
			/* verificamos los campos obligatorios */
			if ($Posicion == '')
				$errFiador |= 1;
			if ($RazonSocial == '')
				$errFiador |= 2;
			if ($DocumentoTipo == '')
				$errFiador |= 4;
			if ($DocumentoNumero == '')
				$errFiador |= 8;
			if ($FechaNacimiento == '')
				$errFiador |= 16;
			if ($IdEstadoCivil == '')
				$errFiador |= 32;
	
			/* creamos el objeto de Fiador */
			$oFiador = new Fiador($Code);
			$oFiador->RazonSocial 			= $RazonSocial;
			$oFiador->DocumentoTipo 		= $DocumentoTipo;
			$oFiador->DocumentoNumero 		= $DocumentoNumero;
			$oFiador->FechaNacimiento 		= $FechaNacimiento;
			$oFiador->IdProfesion 			= $IdProfesion;
			$oFiador->IdEstadoCivil 		= $IdEstadoCivil;
			$oFiador->IdNacionalidad 		= $IdNacionalidad;
			$oFiador->DomicilioCalle		= $DomicilioCalle;
			$oFiador->DomicilioNumero		= $DomicilioNumero;
			$oFiador->DomicilioPiso			= $DomicilioPiso;
			$oFiador->DomicilioDpto			= $DomicilioDpto;
			$oFiador->DomicilioIdLocalidad	= $DomicilioIdLocalidad;
			$oFiador->Descripcion 			= $Descripcion;
			$oFiador->Posicion 				= $Posicion;
	
			/* si no ha habido ningun error durante las verificaciones, */
			/* lo guardamos y reseteamos los campos para que siga cargando */
			if ($errFiador == 0)
			{
				$oFiador->Id			= GestoriaCreate::AddFiador($oFiador);
				$Descripcion 			= "";
				$RazonSocial 			= "";
				$DocumentoTipo 			= "";
				$DocumentoTipoNombre 	= "";
				$DocumentoTipoCodigo 	= "";
				$DocumentoNumero 		= "";
				$FechaNacimiento 		= "";
				$IdProfesion 			= "";
				$IdEstadoCivil 			= "";
				$IdNacionalidad 		= "";
				$DomicilioCalle			= "";
				$DomicilioNumero		= "";
				$DomicilioPiso			= "";
				$DomicilioDpto			= "";
				$DomicilioIdLocalidad	= "";
				$DomicilioCodigoPostal	= "";
			}	
		
			break;

		case 'RemoveFiador':
			GestoriaCreate::RemoveFiador($_REQUEST['Id']);
			break;
		
		case 'Next':
		
			/* validaciones... */
			if (GestoriaCreate::$IdAcreedor == '')
				$err |= 1;
			if (GestoriaCreate::$FinanciacionCapital == '')
				$err |= 2;
			if (GestoriaCreate::$CantidadCuotas == '')
				$err |= 4;
			if (GestoriaCreate::$ImporteCuota == '')
				$err |= 8;
			if (GestoriaCreate::$FechaVencimientoPrimerCuota == '')
				$err |= 16;
			if (GestoriaCreate::$TasaNominal == '')
				$err |= 32;
			if (GestoriaCreate::$TasaEfectiva == '')
				$err |= 64;

			/* si no hay erroes... */
			if ($err == 0)
			{
				if (($oCliente->IdEstadoCivil != EstadoCivil::Casado) && (!GestoriaCreate::$CondominioConyuge) && 
                	(!$oClienteCondominio || $oClienteCondominio->IdEstadoCivil != EstadoCivil::Casado)) 
				{
					header("Location: gestorias_mod_paso5.php" . $strParams);
					exit;
				}
				else
				{
					header("Location: gestorias_mod_paso4.php" . $strParams);
					exit;
				}
			}

			break;
			
		case 'Back':
		
			header("Location: gestorias_mod_paso2.php");
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
	if ((GestoriaCreate::$FinanciacionCapital == '') || (GestoriaCreate::$FinanciacionCapital == '0'))
		GestoriaCreate::$FinanciacionCapital = $oMinuta->FinanciacionCapital;
}

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* informacion del fiador */
$oDocumentoTipo 		= $oTiposDocumento->GetById($DocumentoTipo);
$oEstadoCivil 			= $oEstadosCiviles->GetById($IdEstadoCivil);
$oProfesion 			= $oProfesiones->GetById($IdProfesion);
$oDomicilioLocalidad 	= $oLocalidades->GetById($DomicilioIdLocalidad);

$DocumentoTipoNombre 	= $oDocumentoTipo->Nombre;
$DocumentoTipoCodigo 	= $oDocumentoTipo->Codigo;
$EstadoCivil			= $oEstadoCivil->Nombre;
$EstadoCivilCodigo		= $oEstadoCivil->Codigo;
$Profesion				= $oProfesion->Nombre;
$ProfesionCodigo		= $oProfesion->Codigo;
$DomicilioLocalidad	 	= $oDomicilioLocalidad->Nombre;
$DomicilioCodigoPostal 	= $oDomicilioLocalidad->CodigoPostal;

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

function FilterAcreedor(IdAcreedor, RazonSocial)
{
	if ((IdAcreedor == '') && (RazonSocial == ''))
	{
		Get('IdAcreedor').value = '';
		Get('Acreedor').value 	= '';
	}

	var oAcreedor = GetAcreedor(IdAcreedor);
	if (!(oAcreedor))
		return;

	Get('IdAcreedor').value = oAcreedor.IdAcreedor;
	Get('Acreedor').value 	= oAcreedor.RazonSocial;
}

function VerificarAcreedor()
{
	var IdAcreedor = Get('IdAcreedor').value;

	HideSection('trModificarAcreedor');
	
	if (IdAcreedor != '')
	{
		ShowSection('trAcreedor');
	}
}

function ModAcreedor()
{
	var IdAcreedor = Get('IdAcreedor').value;

	if (IdAcreedor == '')
		return;
	
	var Url = 'acreedores_mod_popup.php?IdAcreedor=' + IdAcreedor;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function AddFiador()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'AddFiador';
	frmData.action = 'gestorias_mod_paso3.php#Fiadores';
	frmData.submit();
	return true;
}

function RemoveFiador(Id)
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
	
	if (frmData == undefined)
		return false;
	
	MainAction.value = 'RemoveFiador';
	IdField.value = Id;
	frmData.submit();
	return true;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Modificar - Paso 3</span></td>
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
				    <input type="hidden" name="Id" id="Id" value="" />
                    <input type="hidden" name="DocumentoTipo" id="DocumentoTipo" value="<?=$DocumentoTipo?>" />
                    <input type="hidden" name="IdEstadoCivil" id="IdEstadoCivil" value="<?=$IdEstadoCivil?>" />
                    <input type="hidden" name="IdProfesion" id="IdProfesion" value="<?=$IdProfesion?>" />
                    <input type="hidden" name="DomicilioIdLocalidad" id="DomicilioIdLocalidad" value="<?=$DomicilioIdLocalidad?>" />
					<input type="hidden" name="Code" id="Code" value="<?php echo md5(rand(1, 1000)); ?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                    	<td>
                                        	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">Datos de la Prenda</span></td>
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
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Acreedor Prendario:</div></td>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Acreedor" id="Acreedor" class="camporFormularioSuggest" maxlength="128" value="<?=GestoriaCreate::$Acreedor?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarAcreedor();" />
                                                                                        <script language="javascript">
                                                                                        SUGGESTRequest('Acreedores', 'GetAll', 'Acreedor', 'FilterAcreedor', 'IdAcreedor', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdAcreedor" id="IdAcreedor" class="camporFormularioChicoSuggest" maxlength="5" value="<?=GestoriaCreate::$IdAcreedor?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddAcreedor" class="botonBasico"  onClick="javascript:AddAcreedor();" value=" + " /></td>
                                                                            </tr>
                                                                            <tr id="trModificarAcreedor" style="display:none;">
                                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModAcreedor('IdAcreedor');">Modificar datos del Acreedor</a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el acreedor prendario</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Monto de la Prenda:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioChico" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$FinanciacionCapital?>" />
                                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el monto de la prenda</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Cantidad Cuotas:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="CantidadCuotas" id="CantidadCuotas" class="camporFormularioChico" maxlength="5" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$CantidadCuotas?>" />
                                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la cantidad de cuotas</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Importe Cuota:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="ImporteCuota" id="ImporteCuota" class="camporFormularioChico" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$ImporteCuota?>" />
                                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese importe de la cuota</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Vencimiento Primer Cuota:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaVencimientoPrimerCuota" type="text" class="camporFormularioChico" id="FechaVencimientoPrimerCuota" value="<?=GestoriaCreate::$FechaVencimientoPrimerCuota?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaVencimientoPrimerCuota'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td colspan="2"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese el vencimiento de la primer cuota</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Tasa Nominal:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="TasaNominal" id="TasaNominal" class="camporFormularioChico" maxlength="5" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$TasaNominal?>" />
                                                                            &nbsp;%
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td colspan="2"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el valor de la tasa nominal</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Tasa Efectiva:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="TasaEfectiva" id="TasaEfectiva" class="camporFormularioChico" maxlength="5" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$TasaEfectiva?>" />
                                                                            &nbsp;%
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td colspan="2"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese el valor de la tasa efectiva</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Costo Financiero Total:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="CostoFinancieroTotal" id="CostoFinancieroTotal" class="camporFormularioChico" maxlength="5" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$CostoFinancieroTotal?>" />
                                                                            &nbsp;%
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="right">Observaciones:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <textarea name="Observaciones" id="Observaciones" class="camporFormularioMultilineGrande" onkeyup="javascript: StrToUpper(this.id);"><?=GestoriaCreate::$Observaciones?></textarea>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                           	</table>
                                                       	</div>
                                                  	</td>
                                              	</tr>
                                          	</table>
                                     	</td>
                                  	</tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                    	<td>
                                        	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">Fiadores</span></td>
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
                                                                    
                                                                    <?php if (GestoriaCreate::GetFiadoresCount() < 2) { ?>
                                                                    
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
                                                                                                                    <td><div id="margen" align="left">Descripci&oacute;n:</div></td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                            <input type="text" name="Descripcion" id="Descripcion" class="camporFormularioSimple" maxlength="128" value="<?=$Descripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                                        <td>
                                                                                                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                                <tr>
                                                                                                                    <td><div id="margen" align="left">Posici&oacute;n:</div></td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                        	<select name="Posicion" id="Posicion" class="camporFormularioSimple">
                                                                                                                            	<option value="0" <?=($Posicion == '0') ? 'selected="selected"' : ''?> >[SELECCIONE]</option>
                                                                                                                                <option value="1" <?=($Posicion == '1') ? 'selected="selected"' : ''?> >PRIMERO</option>
                                                                                                                                <option value="2" <?=($Posicion == '2') ? 'selected="selected"' : ''?> >SEGUNDO</option>
                                                                                                                            </select>
                                                                                                                            
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td height="20"><?php if ($errFiador & 1) { ?>
                                                                                                        <li style="color:#FF0000;">seleccione la posici&oacute;n de impresi&oacute;n</li><?php } ?></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                                <tr>
                                                                                                                    <td><div id="margen" align="left">Apellido y Nombre:</div></td>
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
                                                                                                        <td height="20"><?php if ($errFiador & 2) { ?>
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
                                                                                                                    <td><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('Documento');" value=" + " /></td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td height="20"><?php if ($errFiador & 4) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
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
                                                                                                                            <input type="text" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoNumero?>" />
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td height="20"><?php if ($errFiador & 8) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
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
                                                                                                    <tr>
                                                                                                        <td height="20"><?php if ($errFiador & 16) { ?>
                                                                                                        <li style="color:#FF0000;">Ingrese la fecha de nacimiento</li>
                                                                                                        <?php } ?></td>
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
                                                                                                                    <td><div id="margen" align="left">Profesi&oacute;n:</div></td>
                                                                                                                    <td><div id="margen" align="left">Cod.</div></td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                            <input type="text" name="Profesion" id="Profesion" class="camporFormularioSuggest" maxlength="128" value="<?=$Profesion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                            <script language="javascript">
                                                                                                                            SUGGESTRequest('Profesiones', 'GetAll', 'Profesion', 'FilterProfesion', 'IdProfesion', 'Nombre', 'FilterNombre', null);
                                                                                                                            </script>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                            <input type="text" name="ProfesionCodigo" id="ProfesionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ProfesionCodigo?>" readonly="readonly" />
                                                                                                                            
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>&nbsp;</td>
                                                                                                                    <td><input type="button" id="btnAddProfesion" class="botonBasico"  onClick="javascript:AddProfesion();" value=" + " /></td>
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
                                                                                                                    <td><div id="margen" align="left">Estado Civil:</div></td>
                                                                                                                    <td><div id="margen" align="left">Cod.</div></td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                            <input type="text" name="EstadoCivil" id="EstadoCivil" class="camporFormularioSuggest" maxlength="128" value="<?=$EstadoCivil?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarEstadoCivil(this.value);" />
                                                                                                                            <script language="javascript">
                                                                                                                            SUGGESTRequest('EstadosCiviles', 'GetAll', 'EstadoCivil', 'FilterEstadoCivil', 'IdEstadoCivil', 'Nombre', 'FilterNombre', null);
                                                                                                                            </script>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <div align="left">
                                                                                                                            <input type="text" name="EstadoCivilCodigo" id="EstadoCivilCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$EstadoCivilCodigo?>" readonly="readonly" />
                                                                                                                            
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>&nbsp;</td>
                                                                                                                    <td><input type="button" id="btnAddEstadoCivil" class="botonBasico"  onClick="javascript:AddEstadoCivil();" value=" + " /></td>
                                                                                                                </tr>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td height="20"><?php if ($errFiador & 32) { ?><li style="color:#FF0000;">Ingrese el estado civil</li><?php } ?></td>
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
                                                                                                                            <select name="IdNacionalidad" id="IdNacionalidad" class="camporFormularioSimple">
                                                                                                                                <option value="">[SELECCIONE]</option>
                                                                                                                                <?php foreach ($arrPaises as $oPais) { ?>
                                                                                                                                <option value="<?=$oPais->IdPais?>" <?=($IdNacionalidad == $oPais->IdPais) ? 'selected="selected"' : ''?> ><?=$oPais->Nombre?></option>
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
                                                                                                                    <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('Domicilio');" value=" + " /></td>
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
                                                                                                    <tr>
                                                                                                    	<td height="20">&nbsp;</td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                        	<div align="right">
                                                                                                            	<input type="button" name="btnAddFiador" class="botonBasico" value="Agregar Fiador" onclick="javascript: AddFiador();" />
                                                                                                         	</div>
                                                                                                        </td>
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
                                                                        
                                                                  	<?php } ?>
                                                                    <?php if (GestoriaCreate::GetFiadoresCount() > 0) { ?>
                                            
                                                                        <table width="100%">
                                                                            <tr>
															                    <a name="Fiadores" id="Fiadores"></a>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                        </table>

                                                                        <table width="100%" align="left" class="bordeGris">
                                                                            <tr class="bordeGrisFondo">
                                                                                <td width="20%" height="20" class="bordeGrisTitulo"><div id="margen">Apellido y Nombre</div></td>
                                                                                <td width="22%" height="20" class="bordeGrisTitulo"><div id="margen">Documento</div></td>
                                                                                <td width="14%" class="bordeGrisTitulo">&nbsp;</td>
                                                                            </tr>
                                                                                                        
                                                                        <?php foreach (GestoriaCreate::GetAllFiadores() as $oFiador) { ?>
                                                                            <?php $oTipoDocumento = $oTiposDocumento->GetById($oFiador->DocumentoTipo); ?>
                                            
                                                                            <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                                                <td height="20"><div id="margen"><?=$oFiador->RazonSocial?></div></td>
                                                                                <td height="20"><div id="margen"><?=$oTipoDocumento->Codigo . ' - ' . $oFiador->DocumentoNumero?></div></td>
                                                                                <td height="20">
                                                                                    <div align="center">
                                                                                        <a href="#bottom" onclick="javascript: RemoveFiador('<?=$oFiador->Id?>');"><strong>Quitar</strong>&nbsp;<img border="0" align="absbottom" src="images/iconos/delete-icon.gif" /></a>                                      	
                                                                                    </div>                                                
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="5"><div align="center">
                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                                        <tr>
                                                                                            <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div></td>
                                                                            </tr>
                                                                                
                                                                            <?php } ?>
                                            
                                                                        </table>
                                                                        <table width="100%">
                                                                            <tr>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                            
                                                                        <?php } ?>
                                            
                                                                    </td>
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
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
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

<script language="javascript">
VerificarAcreedor();
</script>

</body>
</html>