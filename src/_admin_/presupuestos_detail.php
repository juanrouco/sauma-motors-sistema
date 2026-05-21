<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PRESUP_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPresupuesto			= intval($_REQUEST['IdPresupuesto']);
$IdModelo				= intval($_REQUEST['IdModelo']);
$VehiculoModelo			= strval($_REQUEST['VehiculoModelo']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Cliente				= strval($_REQUEST['Cliente']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);
$Usuario				= strval($_REQUEST['Usuario']);
$Color					= strval($_REQUEST['Color']);
$ColorCodigo			= strval($_REQUEST['ColorCodigo']);
$IdColor				= intval($_REQUEST['IdColor']);
$Fecha					= strval($_REQUEST['Fecha']);
$Precio					= floatval($_REQUEST['Precio']);
$Financia				= intval($_REQUEST['Financia']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);
$FinanciacionCuotas		= intval($_REQUEST['FinanciacionCuotas']);
$FinanciacionAcreedor	= strval($_REQUEST['FinanciacionAcreedor']);
$FinanciacionValorCuota	= floatval($_REQUEST['FinanciacionValorCuota']);
$EntregaUsado			= intval($_REQUEST['EntregaUsado']);
$UsadoMarca				= strval($_REQUEST['UsadoMarca']);
$UsadoMarcaCodigo		= strval($_REQUEST['UsadoMarcaCodigo']);
$UsadoIdMarca			= intval($_REQUEST['UsadoIdMarca']);
$UsadoModelo			= strval($_REQUEST['UsadoModelo']);
$UsadoModelo			= strval($_REQUEST['UsadoModelo']);
$UsadoAnio				= intval($_REQUEST['UsadoAnio']);
$UsadoKm				= strval($_REQUEST['UsadoKm']);
$UsadoPrecioTomado		= floatval($_REQUEST['UsadoPrecioTomado']);
$FechaVencimiento		= strval($_REQUEST['FechaVencimiento']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oPresupuestos	= new Presupuestos();
$oModelos	= new Modelos();
$oColores	= new Colores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oPresupuesto = $oPresupuestos->GetById($IdPresupuesto))
{
	header('Location: presupuestos.php');
	exit;
}

/* si el formulario fue enviado */

	$Financia				= $oPresupuesto->Financia;
	$FinanciacionCapital	= $oPresupuesto->FinanciacionCapital;
	$FinanciacionCuotas		= $oPresupuesto->FinanciacionCuotas;
	$FinanciacionAcreedor	= $oPresupuesto->FinanciacionAcreedor;
	$FinanciacionValorCuota	= $oPresupuesto->FinanciacionValorCuota;
	$EntregaUsado			= $oPresupuesto->EntregaUsado;
	$UsadoIdMarca			= $oPresupuesto->UsadoIdMarca;
	$UsadoModelo			= $oPresupuesto->UsadoModelo;
	$UsadoAnio				= $oPresupuesto->UsadoAnio;
	$UsadoKm				= $oPresupuesto->UsadoKm;
	$UsadoPrecioTomado		= $oPresupuesto->UsadoPrecioTomado;
	$IdModelo				= $oPresupuesto->IdModelo;
	$IdUsuario				= $oPresupuesto->IdUsuario;
	$IdCliente				= $oPresupuesto->IdCliente;
	$Fecha					= CambiarFecha($oPresupuesto->Fecha);
	$Precio					= $oPresupuesto->Precio;
	$IdColor				= $oPresupuesto->IdColor;
	$FechaVencimiento		= CambiarFecha($oPresupuesto->FechaVencimiento);
	$Observaciones			= $oPresupuesto->Observaciones;


/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}
}

function FilterUsadoMarca(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('UsadoMarcaCodigo').value 	= '';
		Get('UsadoMarca').value 		= '';
		Get('UsadoIdMarca').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('UsadoMarcaCodigo').value 	= oMarca.Codigo;
	Get('UsadoMarca').value 		= oMarca.Nombre;
	Get('UsadoIdMarca').value 		= oMarca.IdMarca;
}

function VerificarEntregaUsado(value)
{
	HideSection('trDatosUsadoTitulo');
	HideSection('trDatosUsado');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trDatosUsadoTitulo');
		ShowSection('trDatosUsado');
	}
}

function VerificarFinanciacion(value)
{
	HideSection('trFinanciacionCapital');
	HideSection('trFinanciacionCapitalError');
	HideSection('trFinanciacionAcreedor');
	HideSection('trFinanciacionAcreedorError');
	HideSection('trFinanciacionCuotas');
	HideSection('trFinanciacionCuotasError');
	HideSection('trFinanciacionValorCuota');
	HideSection('trFinanciacionValorCuotaError');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trFinanciacionCapital');
		ShowSection('trFinanciacionCapitalError');
		ShowSection('trFinanciacionAcreedor');
		ShowSection('trFinanciacionAcreedorError');
		ShowSection('trFinanciacionCuotas');
		ShowSection('trFinanciacionCuotasError');
		ShowSection('trFinanciacionValorCuota');
		ShowSection('trFinanciacionValorCuotaError');
	}
}

function VerificarCliente()
{
	var IdCliente = Get('IdCliente').value;

	HideSection('trModificarCliente');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarCliente');
	}
}

function ModCliente()
{
	var IdCliente = Get('IdCliente').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function FilterCodigoComercial(IdModelo, NumeroVinPrefijo)
{
	if (IdModelo == '')
	{
		Get('VehiculoModelo').value 		= '';		
		Get('CodigoComercial').value 		= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;
	Get('IdModelo').value 				= oModelo.IdModelo;
	Get('CodigoComercial').value 		= oModelo.CodigoComercial;	
	Get('VehiculoModelo').value 		= oModelo.DenominacionModelo;
}

function FilterColor(IdColor, Nombre)
{
	if ((IdModelo == ''))
	{
		Get('IdColor').value 	= '';
		Get('Color').value 	= '';
		Get('ColorCodigo').value 	= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;

	Get('IdColor').value 	= oColor.IdColor;
	Get('Color').value 	= oColor.Nombre;	
	Get('ColorCodigo').value 	= oColor.Codigo;	
}

function FilterDenominacionComercial(IdModelo, Nombre)
{
	if ((IdModelo == ''))
	{
		Get('IdModelo').value 	= '';
		Get('VehiculoModelo').value 	= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;

	Get('IdModelo').value 	= oModelo.IdModelo;
	Get('VehiculoModelo').value 	= oModelo.DenominacionModelo;	
}

$j(document).ready(function() {
<?php
if ($IdModelo && !$Submit)
{
?>
	FilterDenominacionComercial('<?= $IdModelo ?>', '');
<?php
}
if ($IdCliente && !$Submit)
{
?>
	FilterCliente('<?= $IdCliente ?>', '');
<?php
}
if ($IdUsuario && !$Submit)
{
?>
	FilterUsuario('<?= $IdUsuario ?>', '');
<?php
}
if ($IdColor && !$Submit)
{
?>
	FilterColor('<?= $IdColor ?>', '');
<?php
}
if ($UsadoIdMarca && !$Submit)
{
?>
	FilterUsadoMarca('<?= $UsadoIdMarca ?>', '');
<?php
}
?>

});
</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas Proforma - Detalle</span></td>
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
					<input type="hidden" name="IdModelo" id="IdModelo" value="<?=$IdModelo?>" />
					<input type="hidden" name="UsadoIdMarca" id="UsadoIdMarca" value="<?=$UsadoIdMarca?>" />
					<input type="hidden" name="IdColor" id="IdColor" value="<?=$IdColor?>" />
					<input type="hidden" name="IdPresupuesto" id="IdPresupuesto" value="<?=$IdPresupuesto?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Factura Proforma</span></td>
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
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input readonly="readonly" type="checkbox" name="EntregaUsado" id="EntregaUsado" value="1" onchange="javascript: VerificarEntregaUsado(this.checked);" <?=($EntregaUsado) ? 'checked="checked"' : ''?> />&nbsp;Entrega Usado
                                                                                        </label>
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
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input readonly="readonly" type="checkbox" name="Financia" id="Financia" value="1" onchange="javascript: VerificarFinanciacion(this.checked);" <?=($Financia) ? 'checked="checked"' : ''?> />&nbsp;Requiere Financiaci&oacute;n
                                                                                        </label>
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
                                                                                <td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSimple" maxlength="128" value="<?=$VehiculoModelo?>" autocomplete="off" />
																						
																					</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
                                                                </tr>
                                                                <?php /*<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Color:</div></td>
																				<td><div id="margen" align="left">Cod.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
																					<div align="left">
																						<input readonly="readonly" type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
																					</div>
																				</td>
																				<td>
																					<div align="left">
																						<input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
																						
																					</div>
																				</td>
																				<td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
																			
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 8192) { ?>
                                                                    <li style="color:#FF0000;">seleccione el color</li>
                                                                    <?php } ?></td>
                                                                </tr>*/ ?>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Cliente:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarCliente();" autocomplete="Off" />
                                                                                    </div>
                                                                                </td>
                                                                                <td> 
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
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
                                                                                        <input readonly="readonly" type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="Off" />
                                                                                       
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
                                                                    <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el vendedor</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de Presupuesto:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" name="Fecha" type="text" class="camporFormularioMediano" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 8) { ?>
                                                                    <li style="color:#FF0000;">Ingrese la fecha de la minuta</li><?php } ?></td>
                                                                </tr>
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha Vencimiento de Presupuesto:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" name="FechaVencimiento" type="text" class="camporFormularioMediano" id="FechaVencimiento" value="<?=$FechaVencimiento?>" size="12" maxlength="12" />
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
                                                                
                                                            </table>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
																 <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Precio de Venta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="Precio" id="Precio" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Precio?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese precio de venta</li><?php } ?></td>
                                                                </tr>
                                                                <tr id="trFinanciacionCapital" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Capital a Financiar:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCapital?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionCapitalError">
                                                                    <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el capital a financiar</li><?php } ?></td>
                                                                </tr>
																<tr id="trFinanciacionCuotas" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Cantidad de Cuotas:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="FinanciacionCuotas" id="FinanciacionCuotas" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCuotas?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionCuotasError">
                                                                    <td height="20"><?php if ($err & 2048) { ?><li style="color:#FF0000;">Ingrese la cantidad de cuotas</li><?php } ?></td>
                                                                </tr>
																<tr id="trFinanciacionAcreedor" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Acreedor Prendario:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="FinanciacionAcreedor" id="FinanciacionAcreedor" class="camporFormularioSimple" maxlength="255" onkeyup="javascript: StrToUpper(this.id);" value="<?=$FinanciacionAcreedor?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionAcreedorError">
                                                                    <td height="20"><?php if ($err & 1024) { ?><li style="color:#FF0000;">Ingrese el acredor prendario</li><?php } ?></td>
                                                                </tr>
																<tr id="trFinanciacionValorCuota" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Valor Cuota:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input readonly="readonly" type="text" name="FinanciacionValorCuota" id="FinanciacionValorCuota" class="camporFormularioSimple" maxlength="255" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionValorCuota?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionValorCuotaError">
                                                                    <td height="20"><?php if ($err & 4096) { ?><li style="color:#FF0000;">Ingrese el valor de la cuota</li><?php } ?></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
													<tr>
														<td><div id="margen" align="left">Observaciones:</div></td>
													</tr>
													<tr>
														<td colspan="3">
															 <div align="left" style="margin: 5px 25px 10px 10px">
																<textarea id="Observaciones" name="Observaciones" readonly="readonly" class="camporFormularioMultilineGrande" style="width: 100%;height: 75px;"><?= $Observaciones ?></textarea>
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
									<tr id="trDatosUsadoTitulo">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos del Usado</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosUsado">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Marca:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input readonly="readonly" type="text" name="UsadoMarca" id="UsadoMarca" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadocolorCodigo" id="UsadoMarcaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoMarcaCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese la marca</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text readonly="readonly"" name="UsadoModelo" id="UsadoModelo" class="camporFormularioSimple" maxlength="255" value="<?=$UsadoModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
                                                                            </tr>
                                                                            
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoAnio" readonly="readonly" id="UsadoAnio" class="camporFormularioSimple"value="<?=$UsadoAnio?>" />
                                                                                 	</div>
                                                                                </td>
																				<td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoKm" readonly="readonly" id="UsadoKm" class="camporFormularioSimple" maxlength="12" value="<?=$UsadoKm?>" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Importe:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="UsadoPrecioTomado" id="UsadoPrecioTomado" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoPrecioTomado?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el importe del usado</li><?php } ?></td>
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
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'presupuestos.php<?=$strParams?>';" value="Cancelar" />
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
VerificarEntregaUsado('<?=$EntregaUsado?>');
VerificarFinanciacion('<?=$Financia?>');

</script>

</body>
</html>