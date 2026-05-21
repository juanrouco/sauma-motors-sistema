<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECEPUS_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUsado				= intval($_REQUEST['IdUsado']);
$IdMarca				= intval($_REQUEST['IdMarca']);
$Marca					= strval($_REQUEST['Marca']);
$IdMarcaMotor			= intval($_REQUEST['IdMarcaMotor']);
$MarcaMotor				= strval($_REQUEST['MarcaMotor']);
$IdMarcaChasis			= intval($_REQUEST['IdMarcaChasis']);
$MarcaChasis			= strval($_REQUEST['MarcaChasis']);
$IdTipoModelo			= intval($_REQUEST['IdTipoModelo']);
$TipoModelo				= strval($_REQUEST['TipoModelo']);
$TipoModeloCodigo		= strval($_REQUEST['TipoModeloCodigo']);
$Modelo					= strval($_REQUEST['Modelo']);
$IdUbicacion			= intval($_REQUEST['IdUbicacion']);
$Ubicacion				= strval($_REQUEST['Ubicacion']);
$UbicacionCodigo		= strval($_REQUEST['UbicacionCodigo']);
$IdColor				= intval($_REQUEST['IdColor']);
$Color					= strval($_REQUEST['Color']);
$ColorCodigo			= strval($_REQUEST['ColorCodigo']);
$NumeroVinPrefijo		= strval($_REQUEST['NumeroVinPrefijo']);
$NumeroVin				= strval($_REQUEST['NumeroVin']);
$NumeroMotor			= strval($_REQUEST['NumeroMotor']);
$NumeroChasisPrefijo	= strval($_REQUEST['NumeroChasisPrefijo']);
$NumeroChasis			= strval($_REQUEST['NumeroChasis']);
$CodigoComercial		= strval($_REQUEST['CodigoComercial']);
$ModeloAnio				= intval($_REQUEST['ModeloAnio']);
$Dominio				= strval($_REQUEST['Dominio']);
$Kilometraje			= intval($_REQUEST['Kilometraje']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Cliente				= strval($_REQUEST['Cliente']);
$Fecha					= strval($_REQUEST['Fecha']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$EntregaTitulo			= intval($_REQUEST['EntregaTitulo']);
$EntregaCedula			= intval($_REQUEST['EntregaCedula']);
$Entrega08				= intval($_REQUEST['Entrega08']);
$EntregaInformeDominio	= intval($_REQUEST['EntregaInformeDominio']);
$Entrega13I				= intval($_REQUEST['Entrega13I']);
$EntregaVerificacionBomberos	= intval($_REQUEST['EntregaVerificacionBomberos']);
$EntregaPatentes		= intval($_REQUEST['EntregaPatentes']);
$EntregaManualLlaves	= intval($_REQUEST['EntregaManualLlaves']);
$EntregaManual	= intval($_REQUEST['EntregaManual']);
$EntregaClaveFiscal	= intval($_REQUEST['EntregaClaveFiscal']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oRecepcionesUsados = new RecepcionesUsados();
$oUsados			= new Usados();
$oClientes			= new Clientes();
$oUbicaciones		= new Ubicaciones();
$oColores			= new Colores();
$oMarcas			= new Marcas();
$oEstadosUnidad		= new EstadosUnidad();
$oTiposModelo		= new TiposModelo();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oUsado = $oUsados->GetById($IdUsado))
{	
	header("Location: recepcionesusados.php" . $strParams);
	exit();
}

if ($Submit)
{
	/* validaciones... */
	if ($Fecha == '')
		$err |= 1;
	if ($Dominio == '')
		$err |= 2;
	/*if ($NumeroChasis == '')
		$err |= 4;*/
	if ($IdUbicacion == '')
		$err |= 16;
	/*if ($NumeroMotor == '')
		$err |= 32;*/
	if ($IdColor == '')
		$err |= 64;
	if ($IdCliente == '')
		$err |= 128;
	

	/* si no hay errores... */
	if ($err == 0)
	{
		$oRecepcionUsado 			= new RecepcionUsado();
		$oRecepcionUsado->Fecha 	= $Fecha;
		$oRecepcionUsado->IdUsado 	= $IdUsado;
		$oRecepcionUsado->IdCliente = $IdCliente;
		$oRecepcionUsado->Observaciones = $Observaciones;
		$oUsado->EntregaTitulo = $EntregaTitulo;
		$oUsado->EntregaCedula = $EntregaCedula;
		$oUsado->Entrega08 = $Entrega08;
		$oUsado->EntregaInformeDominio = $EntregaInformeDominio;
		$oUsado->Entrega13I = $Entrega13I;
		$oUsado->EntregaVerificacionBomberos = $EntregaVerificacionBomberos;
		$oUsado->EntregaPatentes = $EntregaPatentes;
		$oUsado->EntregaManualLlaves = $EntregaManualLlaves;
		$oUsado->EntregaManual = $EntregaManual;
		$oUsado->EntregaClaveFiscal = $EntregaClaveFiscal;
		
		if ($oRecepcionUsado = $oRecepcionesUsados->Create($oRecepcionUsado))
		{
			$oUsado->IdUbicacion		= $IdUbicacion;
			$oUsado->IdColor			= $IdColor;
			$oUsado->NumeroVinPrefijo	= $NumeroVinPrefijo;
			$oUsado->NumeroVin			= $NumeroVin;
			$oUsado->NumeroMotor		= $NumeroMotor;
			$oUsado->NumeroChasis		= $NumeroChasis;
			$oUsado->ModeloAnio			= $ModeloAnio;
			$oUsado->Dominio			= $Dominio;
			$oUsado->Kilometraje		= $Kilometraje;
			$oUsado->IdMarcaMotor		= $IdMarcaMotor;
			$oUsado->IdMarcaChasis		= $IdMarcaChasis;
			$oUsado->IdTipoModelo		= $IdTipoModelo;
			
			$oUsado = $oUsados->Update($oUsado);
		}
		header("Location: recepcionesusados.php" . $strParams);
		exit();
	}
}
else
{
	$oUbicacion 	= $oUbicaciones->GetById(Ubicacion::VelezSarsfield);
	$oColor 		= $oColores->GetById($oUsado->IdColor);
	$oMarca 		= $oMarcas->GetById($oUsado->IdMarca);
	$oMarcaMotor	= $oMarcas->GetById($oUsado->IdMarcaMotor);
	$oMarcaChasis	= $oMarcas->GetById($oUsado->IdMarcaChasis);
	$oTipoModelo 	= $oTiposModelo->GetById($oUsado->IdTipoModelo);
	
	$IdMarca				= $oMarca->IdMarca;
	$IdMarcaMotor			= $oMarcaMotor->IdMarca;
	$IdMarcaChasis			= $oMarcaChasis->IdMarca;
	$Marca					= $oMarca->Nombre;
	$MarcaMotor				= $oMarcaMotor->Nombre;
	$MarcaChasis			= $oMarcaChasis->Nombre;
	$Modelo					= $oUsado->Modelo;
	$Kilometraje			= number_format($oUsado->Kilometraje, 0, ',', '');
	$IdUbicacion			= $oUbicacion->IdUbicacion;
	$Ubicacion				= $oUbicacion->Nombre;
	$UbicacionCodigo		= $oUbicacion->Codigo;
	$IdColor				= $oColor->IdColor;
	$Color					= $oColor->Nombre;
	$ColorCodigo			= $oColor->Codigo;
	$NumeroVinPrefijo		= $oUsado->NumeroVinPrefijo;
	$NumeroVin				= $oUsado->NumeroVin;
	$NumeroMotor			= $oUsado->NumeroMotor;
	$NumeroChasis			= $oUsado->NumeroChasis;
	$ModeloAnio				= $oUsado->ModeloAnio;
	$Dominio				= $oUsado->Dominio;
	$IdEstado				= $oEstadoUnidad->IdEstado;
	$Estado					= $oEstadoUnidad->Nombre;
	$EstadoCodigo			= $oEstadoUnidad->Codigo;
	$Fecha 					= date('d-m-Y');
	$IdTipoModelo			= $oTipoModelo->IdTipoModelo;
	$TipoModeloCodigo		= $oTipoModelo->Codigo;
	$TipoModelo				= $oTipoModelo->Nombre;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

var filterCodigo = new Array();

function FilterMarca(IdMarca, Nombre)
{
	if (IdMarca == '')
	{
		Get('IdMarca').value 	= '';
		Get('Marca').value 		= '';		
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('IdMarca').value 	= oMarca.IdMarca;
	Get('Marca').value 		= oMarca.Nombre;
}

function FilterMarcaMotor(IdMarca, Nombre)
{
	if (IdMarca == '')
	{
		Get('IdMarcaMotor').value 	= '';
		Get('MarcaMotor').value 		= '';		
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('IdMarcaMotor').value 	= oMarca.IdMarca;
	Get('MarcaNotor').value 		= oMarca.Nombre;
}

function FilterMarcaChasis(IdMarca, Nombre)
{
	if (IdMarca == '')
	{
		Get('IdMarcaChasis').value 	= '';
		Get('MarcaChasis').value 		= '';		
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('IdMarcaChasis').value 	= oMarca.IdMarca;
	Get('MarcaChasis').value 		= oMarca.Nombre;
}

function FilterTipoModelo(IdTipoModelo, Nombre)
{
	if ((IdTipoModelo == '') && (Nombre == ''))
	{
		Get('TipoModeloCodigo').value 	= '';
		Get('TipoModelo').value 			= '';
		Get('IdTipoModelo').value 		= '';
	}

	var oTipoModelo = GetTipoModelo(IdTipoModelo);
	if (!(oTipoModelo))
		return;
	
	Get('TipoModeloCodigo').value 	= oTipoModelo.Codigo;
	Get('TipoModelo').value 		= oTipoModelo.Nombre;
	Get('IdTipoModelo').value 		= oTipoModelo.IdTipoModelo;
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
}

function FilterUbicacion(IdUbicacion, Nombre)
{
	if ((IdUbicacion == '') && (Nombre == ''))
	{
		Get('UbicacionCodigo').value 	= '';
		Get('Ubicacion').value 			= '';
		Get('IdUbicacion').value 		= '';
	}

	var oUbicacion = GetUbicacion(IdUbicacion);
	if (!(oUbicacion))
		return;

	Get('UbicacionCodigo').value 	= oUbicacion.Codigo;
	Get('Ubicacion').value 			= oUbicacion.Nombre;
	Get('IdUbicacion').value 		= oUbicacion.IdUbicacion;
}

function FilterColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo').value 	= '';
		Get('Color').value 			= '';
		Get('IdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo').value 	= oColor.Codigo;
	Get('Color').value 			= oColor.Nombre;
	Get('IdColor').value 		= oColor.IdColor;
}

function FilterEstado(IdEstado, Nombre)
{
	if ((IdEstado == '') && (Nombre == ''))
	{
		Get('EstadoCodigo').value 	= '';
		Get('Estado').value 		= '';
		Get('IdEstado').value 		= '';
	}

	var oEstadoUnidad = GetEstadoUnidad(IdEstado);
	if (!(oEstadoUnidad))
		return;

	Get('EstadoCodigo').value 	= oEstadoUnidad.Codigo;
	Get('Estado').value 		= oEstadoUnidad.Nombre;
	Get('IdEstado').value 		= oEstadoUnidad.IdEstado;
}

function SetNumeroChasis(value)
{
	Get('NumeroChasis').value = value;
}

function SetNumeroChasisPrefijo(value)
{
	Get('NumeroChasisPrefijo').value = value;
}


</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Recepci&oacute;n de Usados - Agregar</span></td>
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
                <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                <input type="hidden" name="IdUsado" id="IdUsado" value="<?=$IdUsado?>" />
                <input type="hidden" name="IdColor" id="IdColor" value="<?=$IdColor?>" />
                <input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?=$IdUbicacion?>" />
				<input type="hidden" name="IdMarcaMotor" id="IdMarcaMotor" value="<?=$IdMarcaMotor?>" />
                <input type="hidden" name="IdMarcaChasis" id="IdMarcaChasis" value="<?=$IdMarcaChasis?>" />
                <input type="hidden" name="IdTipoModelo" id="IdTipoModelo" value="<?=$IdTipoModelo?>" />
                

                <table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
                    <tr>
                        <td class="bordeGris">
                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
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
                                                                        <td><div id="margen" align="left">N&uacute;mero Interno:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="IdUsadoAux" id="IdUsadoAux" class="camporFormularioSimpleDisabled" value="<?= $IdUsado ?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" readonly="readonly" />
																				
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
                                                                        <td><div id="margen" align="left">Marca:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Marca" id="Marca" class="camporFormularioSimpleDisabled" value="<?= $Marca ?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" readonly="readonly" />
																				
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
                                                                        <td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Modelo" id="Modelo" class="camporFormularioSimpleDisabled" maxlength="128" value="<?=$Modelo?>" readonly="readonly" />
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
                                                                                <td><div id="margen" align="left">Fecha de Recepci&oacute;n:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input name="Fecha" type="text" class="camporFormularioMediano" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 1) { ?>
                                                                    <li style="color:#FF0000;">Ingrese la fecha de la minuta</li><?php } ?></td>
                                                                </tr>
														<tr>
															<td>
																<table border="0" align="left" cellpadding="0" cellspacing="0">
																	<tr>
																		<td><div id="margen" align="left">Cliente:</div></td>
																		<td><div id="margen" align="left">Id.</div></td>
																		<td>&nbsp;</td>
																		<td>&nbsp;</td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">
																				<input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="Off" />
																				<script language="javascript">
																					SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
																				</script>
																			</div>
																		</td>
																		<td>
																			<div align="left">
																				<input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
																			</div>
																		</td>
																		<td>&nbsp;</td>
																		<td>
																			<input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " />
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
														</tr>
														<tr>
															<td>
																<table border="0" align="left" cellpadding="0" cellspacing="0">
																	<tr>
																		<td><div id="margen" align="left">Tipo:</div></td>
																		<td><div id="margen" align="left">Cod.</div></td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">
																				<input type="text" name="TipoModelo" id="TipoModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$TipoModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
																				<script language="">
																					SUGGESTRequest('TiposModelo', 'GetAll', 'TipoModelo', 'FilterTipoModelo', 'IdTipoModelo', 'Nombre', 'FilterNombre', null);																		
                                                                                    
																				</script>
																			</div>
																		</td>
																		<td>
																			<div align="left">
																				<input type="text" name="TipoModeloCodigo" id="TipoModeloCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$TipoModeloCodigo?>" readonly="readonly" />                                                                                    
																			</div>
																		</td>
																		<td>&nbsp;</td>
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
                                                                            <td><div id="margen" align="left">Chasis Marca:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaChasis" id="MarcaChasis" class="camporFormularioSimple" maxlength="128" value="<?=$MarcaChasis?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'MarcaChasis', 'FilterMarcaChasis', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                    </script>
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
                                                                        <td><div id="margen" align="left">N&uacute;mero de Chasis:</div></td>
                                                                        <td><div id="margen" align="left"></div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroChasis" id="NumeroChasis" class="camporFormularioSimple" maxlength="17" value="<?=$NumeroChasis?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el N&uacute;mero de Chasis</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                                <td>
                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">Motor Marca:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaMotor" id="MarcaMotor" class="camporFormularioSimple" maxlength="128" value="<?=$MarcaMotor?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'MarcaMotor', 'FilterMarcaMotor', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                    </script>
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
                                                                        <td><div id="margen" align="left">N&uacute;mero de Motor:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroMotor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>                                                            
                                                        <tr>
                                                            <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el N&uacute;mero de Motor</li><?php } ?></td>
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
                                                                        <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <select name="ModeloAnio" id="ModeloAnio" class="camporFormularioSimple">
                                                                                    <option value="">[SELECCIONE]</option>
                                                                                    <?php $year = date('Y'); ?>
                                                                                    <?php for ($i=$year-20; $i<=$year+1; $i++) { ?>
                                                                                    <option value="<?=$i?>" <?=($ModeloAnio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                    <?php } ?>
                                                                                </select>                                                                                </div>
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
                                                                        <td><div id="margen" align="left">Dominio:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Dominio" id="Dominio" class="camporFormularioSimple" maxlength="10" value="<?=$Dominio?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>                                                               
                                                        <tr>
                                                            <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el Dominio</li><?php } ?></td>
                                                        </tr>
														<tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Kilometraje" id="Kilometraje" class="camporFormularioSimple" maxlength="10" value="<?=$Kilometraje?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                        <td><div id="margen" align="left">Color:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('Colores', 'GetAll', 'Color', 'FilterColor', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                        <td><input type="button" id="btnAddColor" class="botonBasico"  onClick="javascript:AddColor('Unidad');" value=" + " /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese el color de la unidad</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Ubicaci&oacute;n:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" maxlength="128" value="<?=$Ubicacion?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="UbicacionCodigo" id="UbicacionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UbicacionCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                        <td><input type="button" id="btnAddUbicacion" class="botonBasico"  onClick="javascript:AddUbicacion();" value=" + " /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la ubicaci&oacute;n</li><?php } ?></td>
                                                        </tr>
														<tr>
															<td>
																<table border="0" align="left" cellpadding="0" cellspacing="0">
																	<tr>
																		<td><input type="checkbox" id="EntregaTitulo" name="EntregaTitulo" value="1" <?= $EntregaTitulo ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega T&iacute;tulo</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaCedula" name="EntregaCedula" value="1" <?= $EntregaCedula ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega C&eacute;dula</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="Entrega08" name="Entrega08" value="1" <?= $Entrega08 ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega 08</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaInformeDominio" name="EntregaInformeDominio" value="1" <?= $EntregaInformeDominio ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Informe Dominio</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="Entrega13I" name="Entrega13I" value="1" <?= $Entrega13I ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Informe 13 i</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaVerificacionBomberos" name="EntregaVerificacionBomberos" value="1" <?= $EntregaVerificacionBomberos ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Verificaci&oacute;n Policial</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaPatentes" name="EntregaPatentes" value="1" <?= $EntregaPatentes ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Patentes</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaManual" name="EntregaManual" value="1" <?= $EntregaManual ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Manual</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaManualLlaves" name="EntregaManualLlaves" value="1" <?= $EntregaManualLlaves ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Llaves</td>
																	</tr>
																	<tr>
																		<td><input type="checkbox" id="EntregaClaveFiscal" name="EntregaClaveFiscal" value="1" <?= $EntregaClaveFiscal ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Clave Fiscal</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td>&nbsp;</td>
														</tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Observaciones:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <textarea name="Observaciones" id="Observaciones" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" rows="5" style="height: 75px"><?=$Observaciones?></textarea>
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
                                            </tr>
                                        </table>
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
  				<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td height="30">
							<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'recepcionesusados.php<?=$strParams?>';" value="Cancelar" />
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