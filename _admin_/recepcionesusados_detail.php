<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECEPUS_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdRecepcionUsado				= intval($_REQUEST['IdRecepcionUsado']);
$IdUsado				= intval($_REQUEST['IdUsado']);
$IdMarca				= intval($_REQUEST['IdMarca']);
$Marca					= strval($_REQUEST['Marca']);
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
if (!$oRecepcionUsado = $oRecepcionesUsados->GetById($IdRecepcionUsado))
{	
	header("Location: recepcionesusados.php" . $strParams);
	exit();
}

/* verifica si existe el registro */
if (!$oUsado = $oUsados->GetById($oRecepcionUsado->IdUsado))
{	
	header("Location: recepcionesusados.php" . $strParams);
	exit();
}

	$oUbicacion 	= $oUbicaciones->GetById(Ubicacion::VelezSarsfield);
	$oColor 		= $oColores->GetById($oUsado->IdColor);
	$oMarca 		= $oMarcas->GetById($oUsado->IdMarca);
	$oCliente		= $oClientes->GetById($oRecepcionUsado->IdCliente);
	$oMarcaMotor	= $oMarcas->GetById($oUsado->IdMarcaMotor);
	$oMarcaChasis	= $oMarcas->GetById($oUsado->IdMarcaChasis);
	$oTipoModelo 	= $oTiposModelo->GetById($oUsado->IdTipoModelo);
	
	$IdUsado				= $oUsado->IdUsado;
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
	$NumeroChasisPrefijo	= $oUsado->NumeroVinPrefijo;
	$NumeroChasis			= $oUsado->NumeroChasis;
	$ModeloAnio				= $oUsado->ModeloAnio;
	$Dominio				= $oUsado->Dominio;
	$IdEstado				= $oEstadoUnidad->IdEstado;
	$Estado					= $oEstadoUnidad->Nombre;
	$EstadoCodigo			= $oEstadoUnidad->Codigo;
	$Fecha 					= CambiarFecha($oRecepcionUsado->Fecha);
	$IdCliente				= $oCliente->IdCliente;
	$Cliente				= $oCliente->RazonSocial;
	$Observaciones			= $oRecepcionUsado->Observaciones;
	
	$IdTipoModelo			= $oTipoModelo->IdTipoModelo;
	$TipoModeloCodigo		= $oTipoModelo->Codigo;
	$TipoModelo				= $oTipoModelo->Nombre;
	
	
	$EntregaTitulo = $oRecepcionUsado->EntregaTitulo;
	$EntregaCedula = $oRecepcionUsado->EntregaCedula;
	$Entrega08 = $oRecepcionUsado->Entrega08;
	$EntregaInformeDominio = $oRecepcionUsado->EntregaInformeDominio;
	$Entrega13I = $oRecepcionUsado->Entrega13I;
	$EntregaVerificacionBomberos = $oRecepcionUsado->EntregaVerificacionBomberos;
	$EntregaPatentes = $oRecepcionUsado->EntregaPatentes;
	$EntregaManualLlaves = $oRecepcionUsado->EntregaManualLlaves;
	$EntregaClaveFiscal = $oRecepcionUsado->EntregaClaveFiscal;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Recepci&oacute;n de Usados - Detalles</span></td>
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
                                                                                        <input name="Fecha" type="text" class="camporFormularioMediano" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12"  readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
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
																				<input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);"  readonly="readonly"  autocomplete="Off" />
																				
																			</div>
																		</td>
																		<td>
																			<div align="left">
																				<input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
																			</div>
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
																				<input type="text" readonly="readonly" name="TipoModelo" id="TipoModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$TipoModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                    <input readonly="readonly" type="text" name="MarcaChasis" id="MarcaChasis" class="camporFormularioSimple" maxlength="128" value="<?=$MarcaChasis?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
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
                                                                                <input type="text" readonly="readonly" name="NumeroChasis" id="NumeroChasis" class="camporFormularioSimple" maxlength="17" value="<?=$NumeroChasis?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                    <input type="text" readonly="readonly" name="MarcaMotor" id="MarcaMotor" class="camporFormularioSimple" maxlength="128" value="<?=$MarcaMotor?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
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
                                                                                <input type="text" readonly="readonly" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroMotor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                <input type="text" name="ModeloAnio" id="ModeloAnio" class="camporFormularioSimple" value="<?=$ModeloAnio?>"  readonly="readonly" /></div>
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
                                                                                <input type="text" name="Dominio" id="Dominio" class="camporFormularioSimple" maxlength="10" value="<?=$Dominio?>" onkeyup="javascript: StrToUpper(this.id);"  readonly="readonly" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                <input type="text" name="Kilometraje" id="Kilometraje" class="camporFormularioSimple" maxlength="10" value="<?=$Kilometraje?>"  readonly="readonly" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                <input type="text" name="Color"  readonly="readonly" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                <input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" maxlength="128" value="<?=$Ubicacion?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off"  readonly="readonly" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="UbicacionCodigo" id="UbicacionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UbicacionCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
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
																		<td><input type="checkbox" id="EntregaManualLlaves" name="EntregaManualLlaves" value="1" <?= $EntregaManualLlaves ? 'checked="checked"' : '' ?> /></td>
																		<td>Entrega Manual y Llaves</td>
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
                                                                                <textarea name="Observaciones" id="Observaciones" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);"  readonly="readonly" rows="5" style="height: 75px"><?=$Observaciones?></textarea>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'recepcionesusados.php<?=$strParams?>';" value="Volver" />
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