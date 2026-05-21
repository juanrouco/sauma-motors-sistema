<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinutaEspera		= intval($_REQUEST['IdMinutaEspera']);

/* declaracion de variables */
$oMinutasEspera	= new MinutasEspera();
$oModelos		= new Modelos();
$oClientes		= new Clientes();
$oUsuarios		= new Usuarios();
$oColores		= new Colores();
$oMarcas		= new Marcas();
$oUsados		= new Usados();
$oAcreedores	= new Acreedores();


/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinutaEspera = $oMinutasEspera->GetById($IdMinutaEspera))
{	
	header("Location: minutasespera.php" . $strParams);
	exit();
}

$oModelo 		= $oModelos->GetById($oMinutaEspera->IdModelo);
$oCliente 		= $oClientes->GetById($oMinutaEspera->IdCliente);
$oUsuario 		= $oUsuarios->GetById($oMinutaEspera->IdUsuario);
$oColor 		= $oColores->GetById($oMinutaEspera->IdColor);
$oColor2 		= $oColores->GetById($oMinutaEspera->IdColor2);
$oColor3 		= $oColores->GetById($oMinutaEspera->IdColor3);

$VehiculoModelo = $oModelo->DenominacionModelo;

$IdUnidad				= $oMinutaEspera->IdUnidad;
$IdUsuario				= $oUsuario->IdUsuario;
$Usuario				= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);
$IdCliente				= $oCliente->IdCliente;
$Cliente				= $oCliente->RazonSocial;

$FechaMinuta			= CambiarFecha($oMinutaEspera->FechaMinuta);
$NumeroPedido			= $oMinutaEspera->NumeroPedido;
$NumeroVin				= $oMinutaEspera->NumeroVin;
$Anticipo				= $oMinutaEspera->Anticipo;

$Financiacion				= $oMinutaEspera->Financia;
	$FinanciacionCapital	= $oMinutaEspera->FinanciacionCapital;
	$FinanciacionCuotas		= $oMinutaEspera->FinanciacionCuotas;
	$FinanciacionAcreedor	= $oMinutaEspera->FinanciacionAcreedor;
	$FinanciacionValorCuota	= $oMinutaEspera->FinanciacionValorCuota;
	$EntregaUsado			= $oMinutaEspera->EntregaUsado;


/* obtenemos los datos del usado entregado en caso de que exista */
if ($oMinutaEspera->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinutaEspera($oMinutaEspera->IdMinutaEspera);
	$oUsado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}


$oUsadoMarca 	= $oMarcas->GetById($oUsado->IdMarca);
$oUsadoColor 	= $oColores->GetById($oUsado->IdColor);
$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);
	
/* datos del usado */
$UsadoIdMarca		= $oUsadoMarca->IdMarca;
$UsadoMarca			= $oUsadoMarca->Nombre;
$UsadoMarcaCodigo	= $oUsadoMarca->Codigo;
$UsadoIdColor		= $oUsadoColor->IdColor;
$UsadoColor			= $oUsadoColor->Nombre;
$UsadoColorCodigo	= $oUsadoColor->Codigo;
$UsadoModelo		= $oUsado->Modelo;
$UsadoModeloAnio	= $oUsado->ModeloAnio;
$UsadoKilometraje	= $oUsado->Kilometraje;
$UsadoValuacion		= $oUsado->Valuacion;
$UsadoDominio		= $oUsado->Dominio;
$UsadoArreglos		= $oUsado->Arreglos;
$UsadoObservaciones	= $oUsado->Observaciones;

if($oUsado2)
{
	$UsadoIdMarca2		= $oUsadoMarca2->IdMarca;
	$UsadoMarca2		= $oUsadoMarca2->Nombre;
	$UsadoMarcaCodigo2	= $oUsadoMarca2->Codigo;
	$UsadoIdColor2		= $oUsadoColor2->IdColor;
	$UsadoColor2		= $oUsadoColor2->Nombre;
	$UsadoColorCodigo2	= $oUsadoColor2->Codigo;
	$UsadoModelo2		= $oUsado2->Modelo;
	$UsadoModeloAnio2	= $oUsado2->ModeloAnio;
	$UsadoKilometraje2	= $oUsado2->Kilometraje;
	$UsadoValuacion2	= $oUsado2->Valuacion;
	$UsadoDominio2		= $oUsado2->Dominio;
	$UsadoArreglos2		= $oUsado2->Arreglos;
	$UsadoObservaciones2= $oUsado2->Observaciones;
}
	
	
	$GastosFlete			= $oMinutaEspera->GastosFlete;
	$GastosPatentamiento	= $oMinutaEspera->GastosPatentamiento;
	$GastosOtorgamiento		= $oMinutaEspera->GastosOtorgamiento;
	$GastosPrenda			= $oMinutaEspera->GastosPrenda;
	$Circular				= $oMinutaEspera->Circular;
	$Precio					= $oMinutaEspera->Precio;
	$DepositoGarantia		= $oMinutaEspera->DepositoGarantia;
	$Rentas					= $oMinutaEspera->Rentas;
	$Observaciones			= $oMinutaEspera->Observaciones;
	$IdAcreedor				= $oMinutaEspera->IdAcreedor;
	$oAcreedor				 = $oAcreedores->GetById($IdAcreedor);



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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Preventa - Detalle</span></td>
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
													<td height="40" align="center"><span class="tituloPagina">Datos de la Venta</span></td>
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
																			<td><div id="margen" align="left">Nro. Minuta:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" class="camporFormularioMediano" maxlength="10" readonly="readonly" value="<?=$IdMinutaEspera?>" />
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
																			<td><div id="margen" align="left">Modelo:</div></td>
																			
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$VehiculoModelo?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Color 1:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSuggest" maxlength="128" value="<?=$oColor->Nombre?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Color 2:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSuggest" maxlength="128" value="<?=$oColor2->Nombre?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Color 3:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSuggest" maxlength="128" value="<?=$oColor3->Nombre?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Cliente:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
																			<td><div id="margen" align="left">Vendedor:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
																			<td><div id="margen" align="left">Fecha de Minuta:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input name="FechaMinuta" type="text" class="camporFormularioMediano" id="FechaMinuta" value="<?=$FechaMinuta?>" size="12" maxlength="12" readonly="readonly" />
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
                                                                                            <input type="checkbox" name="EntregaUsado" id="EntregaUsado" value="1" onchange="javascript: VerificarEntregaUsado(this.checked);" <?=($EntregaUsado) ? 'checked="checked"' : ''?> readonly="readonly" />&nbsp;Entrega Usado
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
                                                                                            <input type="checkbox" name="Financiacion" id="Financiacion" value="1" onchange="javascript: VerificarFinanciacion(this.checked);" <?=($Financiacion) ? 'checked="checked"' : ''?> readonly="readonly" />&nbsp;Requiere Financiaci&oacute;n
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
														</table>
													</td>
													<td>&nbsp;</td>
													<td valign="top">
														<table border="0" align="center" cellpadding="0" cellspacing="0">
															<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Precio de Venta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="Precio" id="Precio" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Precio?>" />
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
                                                                                <td><div id="margen" align="left">Gastos Otorgamiento:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosOtorgamiento" id="GastosOtorgamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosOtorgamiento?>" readonly="readonly" />
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
                                                                                <td><div id="margen" align="left">Gastos Gestor&iacute;a:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosPatentamiento" id="GastosPatentamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPatentamiento?>" readonly="readonly" />
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
                                                                                <td><div id="margen" align="left">Otros Gastos:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="GastosFlete" id="GastosFlete" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosFlete?>" />
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
																<tr id="trAcreedor" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Acreedor Prendario:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdAcreedor" readonly="readonly" id="IdAcreedor" class="camporFormularioSimple" value="<?= $oAcreedor->RazonSocial ?>">
																							
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trAcreedorError" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<tr id="trFinanciacionCapital" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Capital a Financiar:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCapital?>" />
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionCapitalError" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>															
																<tr id="trPlazoPrenda" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Plazo Prenda:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="FinanciacionCuotas" id="FinanciacionCuotas" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCuotas?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr> 					
																<tr id="trPlazoPrendaError" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr id="trQuebranto" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Quebranto:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" readonly="readonly" name="GastosPrenda" id="GastosPrenda" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPrenda?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr id="trQuebrantoError" <?= $Financiacion ? '' : 'style="display: none"' ?>>
                                                                    <td height="20">&nbsp;</td>
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
                                                                                        <textarea readonly="readonly" name="Observaciones" id="Observaciones" class="camporFormularioSimple" style="height: 75px"><?=$Observaciones?></textarea>
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
										</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr id="trDatosUsadoTitulo" <?= $EntregaUsado ? '' : 'style="display: none"' ?>>
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
                                    <tr <?=($EntregaUsado) ? '' : 'style="display: none;"'?>>
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
																					</tr>
																					<tr>
																						<td>
																							<div align="left">
																								<input type="text" name="UsadoMarca" id="UsadoMarca" class="camporFormularioSimple" maxlength="128" value="<?=$UsadoMarca?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
																			<td><div id="margen" align="left">Modelo:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="UsadoModelo" id="UsadoModelo" class="camporFormularioSimple" maxlength="12" value="<?=$UsadoModelo?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
																				</div>
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
																					</tr>
																					<tr>
																						<td>
																							<div align="left">
																								<input type="text" name="UsadoColor" id="UsadoColor" class="camporFormularioSimple" maxlength="128" value="<?=$UsadoColor?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
                                                                                            <td><div id="margen" align="left">Dominio:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoDominio" id="UsadoDominio" class="camporFormularioSimple" maxlength="128" value="<?=$UsadoDominio?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																	</table>
																</td>
																<td>&nbsp;</td>
																<td valign="top">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">A&ntilde;o:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<select name="UsadoModeloAnio" id="UsadoModeloAnio" class="camporFormularioSimple" readonly="readonly">
																						<option value="">[SELECCIONE]</option>
																						<?php $year = date('Y'); ?>
																						<?php for ($i=$year-15; $i<=$year; $i++) { ?>
																						<option value="<?=$i?>" <?=($UsadoModeloAnio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
																						<?php } ?>
																					</select>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td height="20">&nbsp;</td>
																		</tr>
																		<tr>
																			<td><div id="margen" align="left">Kilometraje:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="UsadoKilometraje" id="UsadoKilometraje" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoKilometraje?>" readonly="readonly" />
																				</div>
																			</td>
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
																					<input type="text" name="UsadoValuacion" id="UsadoValuacion" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoValuacion?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td height="20">&nbsp;</td>
																		</tr>
																		
																		<tr>
																			<td><div id="margen" align="left">Arreglos:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="UsadoArreglos" id="UsadoArreglos" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoArreglos?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td height="20">&nbsp;</td>
																		</tr>
																		<tr>
																			<td><div id="margen" align="left">Observaciones:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<textarea name="UsadoObservaciones" id="UsadoObservaciones" class="camporFormularioSimple" style="height: 75px"><?=$UsadoObservaciones?></textarea>
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
														<td>&nbsp;</td>
													</tr>
													<tr class="bordeGrisFondo">
														<td height="40" align="center"><span class="tituloPagina">Segundo Usado</span></td>
													</tr>
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
                                                                                            <td><div id="margen" align="left">Marca:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarca2" id="UsadoMarca2" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca2?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarcaCodigo2" id="UsadoMarcaCodigo2" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoMarcaCodigo2?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoModelo2" id="UsadoModelo2" class="camporFormularioSimple" maxlength="255" value="<?=$UsadoModelo2?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 2048) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
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
                                                                                                    <input type="text" name="UsadoColor2" id="UsadoColor2" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoColor2?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoColorCodigo2" id="UsadoColorCodigo2" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoColorCodigo2?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
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
                                                                                            <td><div id="margen" align="left">Dominio:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoDominio2" id="UsadoDominio2" class="camporFormularioSimple" maxlength="128" value="<?=$UsadoDominio2?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="UsadoModeloAnio2" id="UsadoModeloAnio2" class="camporFormularioSimple">
                                                                                            <option value="">[SELECCIONE]</option>
                                                                                            <?php $year = date('Y'); ?>
                                                                                            <?php for ($i=$year-15; $i<=$year; $i++) { ?>
                                                                                            <option value="<?=$i?>" <?=($UsadoModeloAnio2 == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                 	</div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4096) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoKilometraje2" id="UsadoKilometraje2" class="camporFormularioSimple" maxlength="12" value="<?=$UsadoKilometraje2?>" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
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
                                                                                        <input type="text" name="UsadoValuacion2" id="UsadoValuacion2" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoValuacion2?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 8192) { ?><li style="color:#FF0000;">Ingrese el importe del usado</li><?php } ?></td>
                                                                            </tr>
																			
																		<tr>
																			<td><div id="margen" align="left">Arreglos:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="UsadoArreglos2" id="UsadoArreglos2" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoArreglos2?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td height="20">&nbsp;</td>
																		</tr>
																		<tr>
																			<td><div id="margen" align="left">Observaciones:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<textarea name="UsadoObservaciones2" id="UsadoObservaciones2" class="camporFormularioSimple" style="height: 75px"><?=$UsadoObservaciones2?></textarea>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'minutasespera.php<?=$strParams?>';" value="Volver" />
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


</body>
</html>