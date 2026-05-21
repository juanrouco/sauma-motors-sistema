<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta				= intval($_REQUEST['IdMinuta']);

/* declaracion de variables */
$oMinutas					= new Minutas();
$oUnidades					= new Unidades();
$oModelos					= new Modelos();
$oClientes					= new Clientes();
$oUsuarios					= new Usuarios();
$oUsados					= new Usados();
$oMarcas					= new Marcas();
$oColores					= new Colores();
$oMinutasFinanciacion 		= new MinutasFinanciacion();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oAcreedores				= new Acreedores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: minutas.php" . $strParams);
	exit();
}

$oPedidoAccesorios = $oPedidosAccesorios->GetByMinuta($oMinuta);
$arrMinutasFinanciacion = $oMinutasFinanciacion->GetByMinuta($oMinuta);

/* obtenemos los datos del usado entregado en caso de que exista */
if ($oMinuta->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinuta($oMinuta->IdMinuta);
	$oUsado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$oUnidad 		= $oUnidades->GetById($oMinuta->IdUnidad);
$oModelo 		= $oModelos->GetById($oUnidad->IdModelo);
$oCliente 		= $oClientes->GetById($oMinuta->IdCliente);
$oUsuario 		= $oUsuarios->GetById($oMinuta->IdUsuario);
$oUsadoMarca 	= $oMarcas->GetById($oUsado->IdMarca);
$oUsadoColor 	= $oColores->GetById($oUsado->IdColor);
$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);

if ($oMinuta->Condominio)
{
	$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
}

$VehiculoModelo = $oModelo->DenominacionModelo;

$IdUnidad				= $oMinuta->IdUnidad;
$IdUsuario				= $oUsuario->IdUsuario;
$Usuario				= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);
$IdCliente				= $oCliente->IdCliente;
$IdClienteCondominio	= $oMinuta->IdClienteCondominio;
$Cliente				= $oCliente->RazonSocial;
$SeguroCompania			= $oMinuta->SeguroCompania;
$SeguroCobertura		= $oMinuta->SeguroCobertura;
$SeguroValor			= $oMinuta->SeguroValor;
$SeguroIdTipoPago		= $oMinuta->SeguroIdTipoPago;
$ClienteCondominio		= '';
if ($oMinuta->Condominio)
{
	$ClienteCondominio = $oClienteCondominio->RazonSocial;
}
$FechaMinuta			= CambiarFecha($oMinuta->FechaMinuta);
$PrecioVenta			= $oMinuta->PrecioVenta;
$GastosFlete			= $oMinuta->GastosFlete;
$GastosPatentamiento	= $oMinuta->GastosPatentamiento;
$GastosOtorgamiento		= $oMinuta->GastosOtorgamiento;
$DepositoGarantia		= $oMinuta->DepositoGarantia;
$GastosPrenda			= $oMinuta->GastosPrenda;
$Circular				= $oMinuta->Circular;
$Anticipo				= $oMinuta->Anticipo;
$FinanciacionCapital	= $oMinuta->FinanciacionCapital;
//$Financiacion			= (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) ? '1' : '0';
$Condominio				= $oMinuta->Condominio;
$EntregaUsado			= $oMinuta->EntregaUsado;
$PlazoPrenda			= $oMinuta->PlazoPrenda;
$Chasis					= $oUnidad->NumeroChasis;
$NumeroMotor			= $oUnidad->NumeroMotor;
$oAcreedor 				= $oAcreedores->GetById($oMinuta->IdAcreedor);
$Observaciones			= $oMinuta->Observaciones;
$CedulaAzul				= $oMinuta->CedulaAzul;

if ($arrMinutasFinanciacion)
{
	$Financiacion = true;
	$arrIdAcreedor = array();
	$arrFinanciacionImportes = array();
	$arrFinanciacionCuotas = array();
	
	foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
	{
		$arrIdAcreedor[] = $oMinutaFinanciacion->IdAcreedor;
		$arrFinanciacionImportes[] = number_format($oMinutaFinanciacion->Importe, 2);
		$arrFinanciacionCuotas[] = $oMinutaFinanciacion->Cuotas;
	}
}

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

if ($oPedidoAccesorios)
{
	$PedidoAccesorios = true;
	$Accesorios = $oPedidoAccesorios->Accesorios;
	$arrPedidosAccesoriosItems = $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($oPedidoAccesorios);	
	$arrDetalles = array();
	$arrImportes = array();
		
	foreach ($arrPedidosAccesoriosItems as $oPedidoAccesorioItem)
	{
	$arrDetalles[] = $oPedidoAccesorioItem->Detalle;
		$arrImportes[] = number_format($oPedidoAccesorioItem->Importe, 2);
	}
}

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas - Detalle</span></td>
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
																			<td><div id="margen" align="left">Nro. Carpeta:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" class="camporFormularioMediano" maxlength="10" readonly="readonly" value="<?=$IdMinuta?>" />
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
																			<td><div id="margen" align="left">Unidad:</div></td>
																			<td><div id="margen" align="left">Interno:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$VehiculoModelo?>" readonly="readonly" />
																				</div>
																			</td>
																			<td>
																				<div align="left">
																					<input type="text" name="IdUnidad" id="NumeroVin" class="camporFormularioChico" maxlength="5" value="<?=$IdUnidad?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
																			<td><div id="margen" align="left">Chasis:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="Chasis" id="Chasis" class="camporFormularioSuggest" maxlength="128" value="<?=$Chasis?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">N&uacute;mero Motor:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSuggest" maxlength="128" value="<?=$NumeroMotor?>" readonly="readonly" />
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
																						<input type="checkbox" name="Condominio" id="Condominio" value="1" <?=($Condominio) ? 'checked="checked"' : ''?> onclick="javascript: VerificarCondominio(this.checked);"  readonly="readonly" />&nbsp;En Condominio
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
															<tr id="trClienteCondominio" <?=($Condominio) ? '' : 'style="display:none;"'?>>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Cliente Condominio:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="ClienteCondominio" id="ClienteCondominio" class="camporFormularioSuggest" maxlength="128" value="<?=$ClienteCondominio?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarClienteCondominio();" autocomplete="Off" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															 <tr <?=($Condominio) ? '' : 'style="display:none;"'?>>
																<td height="20">&nbsp;</td>
															</tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input type="checkbox" name="CedulaAzul" id="CedulaAzul" value="1" <?=($CedulaAzul) ? 'checked="checked"' : ''?> />&nbsp;Requiere Cedula Azul
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
																						<input type="checkbox" name="Financiacion" id="Financiacion" value="1"  <?=($Financiacion) ? 'checked="checked"' : ''?> readonly="readonly" />&nbsp;Requiere Financiaci&oacute;n
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
                                                                                            <input type="checkbox" readonly="readonly" name="PedidoAccesorios" id="PedidoAccesorios" value="1"  <?=($PedidoAccesorios) ? 'checked="checked"' : ''?> />&nbsp;Pedido de Accesorios
                                                                                        </label>
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
																			<td><div id="margen" align="left">Precio de Venta:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="PrecioVenta" id="PrecioVenta" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento + $oMinuta->Interes?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>                                                               
															<tr>
																<td height="20">&nbsp;</td>
															</tr><tr>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Precio de Facturaci&oacute;n:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="PrecioVenta" id="PrecioVenta" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$PrecioVenta?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Gastos Otorgamiento:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="GastosOtorgamiento" id="GastosOtorgamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosOtorgamiento?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Gastos Gestor&iacute;a:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="GastosPatentamiento" id="GastosPatentamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPatentamiento?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Otros Gastos:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="GastosFlete" id="GastosFlete" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosFlete?>" readonly="readonly" />
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
																			<td><div id="margen" align="left">Inter&eacute;:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="GastosFlete" id="GastosFlete" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$oMinuta->Interes?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>                                                               
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Acreedor Prendario:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="Acreedor" id="Acreedor" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$oAcreedor->RazonSocial?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>                                                               
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td height="20">&nbsp;</td>
															</tr>
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Capital a Financiar:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCapital?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>                                                               
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td height="20">&nbsp;</td>
															</tr>
															
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Plazo Prenda:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="PlazoPrenda" id="PlazoPrenda" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$PlazoPrenda?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>    																																
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td height="20">&nbsp;</td>
															</tr>
															
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Quebranto:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="GastosPrenda" id="GastosPrenda" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPrenda?>" readonly="readonly" />
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>  
															<tr <?=($Financiacion && false) ? '' : 'style="display:none;"'?>>
																<td height="20">&nbsp;</td>
															</tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Compa&ntilde;ia de Seguros:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="SeguroCompania" id="SeguroCompania" class="camporFormularioSimple" maxlength="10" onkeyup="javascript: StrToUpper(this.id);" value="<?=$SeguroCompania?>" />
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
                                                                                <td><div id="margen" align="left">Cobertura de Seguros:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="SeguroCobertura" id="SeguroCobertura" class="camporFormularioSimple" maxlength="10" onkeyup="javascript: StrToUpper(this.id);" value="<?=$SeguroCobertura?>" />
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
                                                                                <td><div id="margen" align="left">Valor del Seguro:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="SeguroValor" id="SeguroValor" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$SeguroValor?>" />
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
                                                                                <td><div id="margen" align="left">Forma de Pago del Seguro:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="SeguroIdTipoPago" id="SeguroIdTipoPago" class="camporFormularioSimple">
																							<option value="1" <?= $SeguroIdTipoPago == 1 ? 'selected="selected"' : '' ?>>Efectivo</option>
																							<option value="2" <?= $SeguroIdTipoPago == 2 ? 'selected="selected"' : '' ?>>Tarjeta</option>
																							<option value="3" <?= $SeguroIdTipoPago == 3 ? 'selected="selected"' : '' ?>>Debito</option>
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
                                                                                <td><div id="margen" align="left">Origen de Cliente:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="SeguroValor" id="SeguroValor" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=OrigenesCliente::GetById($oMinuta->IdOrigenCliente)?>" />
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
                                                                                <td><div id="margen" align="left">Observaciones:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="margen"  align="left">
                                                                                        <?=$Observaciones?>
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
									<tr <?=($Financiacion) ? '' : 'style="display: none;"'?>>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Financiaci&oacute;n</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr <?=($Financiacion) ? '' : 'style="display: none;"'?>>
                                    	<td>
                                        	<div align="center">
												<table id="financiacion-items" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
													<tr class="bordeGrisFondo">
														<td height="30"><div id="margen"><strong>Acreedor</strong></div></td>
														<td width="10">&nbsp;</td>
														<td width="200"><div id="margen" align="center"><strong>Cuotas</strong></div></td>
														<td width="200"><div id="margen" align="center"><strong>Importe</strong></div></td>
													</tr>
													<?php
													if ($arrIdAcreedor && count($arrIdAcreedor) > 0)
													{
														for ($i = 0; $i < count($arrIdAcreedor); $i++)
														{
																$oAcreedor = $oAcreedores->GetById($arrIdAcreedor[$i]);
													?>
													<tr id="rowfinanciacion_<?= $i ?>" class="bordeGris">
														<td height="30">
															<div id="margen">
																<?= $oAcreedor->RazonSocial ?>
																</select>
															</div>
														</td>
														<td width="10">&nbsp;</td>
														<td width="200"><div id="margen" align="center"><?= $arrFinanciacionCuotas[$i] ?></div></td>
														<td width="200"><div id="margen" align="center">$<?= $arrFinanciacionImportes[$i] ?></div></td>
													</tr>
													<?php
														}
													}
													
													?>
												</table>
											</div>
										</td>
									</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr <?=($PedidoAccesorios) ? '' : 'style="display: none;"'?>>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Pedido de Accesorios</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr <?=($PedidoAccesorios) ? '' : 'style="display: none;"'?>>
										<td>
											<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0" class="bordeGris">
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
                                                    <td width="40%"><div align="right">Comentarios:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<textarea name="Accesorios" id="Accesorios" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$Accesorios?></textarea>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr <?=($PedidoAccesorios) ? '' : 'style="display: none;"'?>>
                                    	<td>
                                        	<div align="center">
												<table id="contenedor-items" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
													<tr class="bordeGrisFondo">
														<td height="30"><div id="margen"><strong>Item</strong></div></td>
														<td width="10">&nbsp;</td>
														<td width="200">&nbsp;</td>
													</tr>
													<?php
													if ($arrDetalles && count($arrDetalles) > 0)
													{
														for ($i = 0; $i < count($arrDetalles); $i++)
														{
													?>
													<tr id="row_<?= $i ?>" class="bordeGris">
														<td height="30"><div id="margen"><input type="text"  readonly="readonly" id="Detalle[]" name="Detalle[]" class="camporFormularioSimple" value="<?= $arrDetalles[$i] ?>" /></div></td>
														<td width="10">&nbsp;</td>
														<td width="200">&nbsp;</td>
													</tr>
													<?php
														}
													}
													
													?>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								<tr <?=($EntregaUsado) ? '' : 'style="display: none;"'?>>
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
																								<input type="text" name="UsadoMarca" id="UsadoMarca" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
																								<input type="text" name="UsadoColor" id="UsadoColor" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoColor?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
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
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'UsadoMarca2', 'FilterUsadoMarca2', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
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
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Colores', 'GetAll', 'UsadoColor2', 'FilterUsadoColor2', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'minutas.php<?=$strParams?>';" value="Volver" />
								<input type="button" name="btnImprimir" class="botonBasico" id="btnImprimir" onclick="javascript: window.location.href = 'minutas_pdf.php?IdMinuta=<?=$IdMinuta?>';" value="Imprimir" />
								<input type="button" name="btnPagos" class="botonBasico" id="btnPagos" onclick="javascript: window.location.href = 'pagos.php?IdMinuta=<?=$IdMinuta?>';" value="Pagos" />
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