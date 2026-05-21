<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CUECOR_DETAIL))
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
$oMinutasFinanciacion		= new MinutasFinanciacion();
$oContratosPrendas			= new ContratosPrendas();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: cuentascorriente.php" . $strParams);
	exit();
}

$oPedidoAccesorios = $oPedidosAccesorios->GetByMinuta($oMinuta);
$arrMinutasFinanciacion = $oMinutasFinanciacion->GetByMinuta($oMinuta);

if ($arrMinutasFinanciacion && count($arrMinutasFinanciacion) > 0)
{
		$Financiacion = true;
		$FinanciacionCapital = 0;
		foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
		{
				$FinanciacionCapital+= $oMinutaFinanciacion->Importe;
		}
}

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
//$FinanciacionCapital	= $oMinuta->FinanciacionCapital;
//$Financiacion			= (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) ? '1' : '0';
$Condominio				= $oMinuta->Condominio;
$EntregaUsado			= $oMinuta->EntregaUsado;
$PlazoPrenda			= $oMinuta->PlazoPrenda;
$Chasis					= $oUnidad->NumeroChasis;
$NumeroMotor			= $oUnidad->NumeroMotor;
if ($oMinuta->RentasFinal == 0)
	$Rentas					= $oMinuta->Rentas;
else
	$Rentas					= $oMinuta->RentasFinal;
$Alta					= $oMinuta->Alta;

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
$UsadoValuacion2	= 0;
$UsadoValuacion2	= $oUsado2->Valuacion;
$UsadoDominio		= $oUsado->Dominio;

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

if ($Financiacion)
	$oContratoPrenda = $oContratosPrendas->GetByMinuta($oMinuta);

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente - Detalle</span></td>
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
																			<td><div id="margen" align="left"><strong>Nro. Interno:</strong>&nbsp;<?= $IdMinuta ?></div></td>
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
																			<td><div id="margen" align="left"><strong>Cliente:</strong>&nbsp;<?=$Cliente?></div></td>
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
																			<td><div id="margen" align="left"><strong>Fecha de Minuta:</strong>&nbsp;<?= $FechaMinuta ?></div></td>
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
																			<td><div id="margen" align="left"><strong>Unidad:</strong>&nbsp;<?=$VehiculoModelo?></div></td>
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
																			<td><div id="margen" align="left"><strong>Vendedor:</strong>&nbsp;<?=$Usuario?></div></td>
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
								<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Detalle Cuenta Corriente</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
                                    	<td>
                                        	<div align="center">
												<table width="90%" border="0" cellpadding="0" cellspacing="0" class="bordeGris">
													<tr>
														<td height="20">&nbsp;</td>
													</tr>
													<tr>
														<td>
															<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left"><strong>Detalle</strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center"><strong>Importe</strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Precio de Venta</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($PrecioVenta, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Gastos Otorgamiento</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($GastosOtorgamiento, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<?php
																if ($GastosPatentamiento && $GastosPatentamiento != 0)
																{
																?>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Gastos Gestor&iacute;a</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($GastosPatentamiento, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<?php
																}
																?>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Otros Gastos</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($GastosFlete, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Se&ntilde;a</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">- $<?= number_format($DepositoGarantia, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr  <?=($EntregaUsado) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Usado</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">- $<?= number_format($UsadoValuacion + $UsadoValuacion2, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center"><?= $oUsado->IdUbicacion != Ubicacion::Transito ? '<span style="color: green">Acreditado</span>' : '<span style="color: red">Sin Acreditar</span>' ?></div></td>
																</tr>
																<tr  <?=($EntregaUsado) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Arreglos Usados</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($oUsado->Arreglos + $oUsado2->Arreglos, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr  <?=($Financiacion) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Capital a Financiar</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">- $<?= number_format($FinanciacionCapital, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%">(A acreeditar con pagos)</td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Pedidos Accesorios</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($oMinuta->GetTotalAccesorios(), 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Pagos</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">-$<?= number_format($oMinuta->GetTotalPagos(), 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left"><strong>Saldo a Pagar x Cliente</strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right"><strong>$<?= number_format($oMinuta->GetTotalAAbonar(), 2, ',', '.') ?></strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left"><strong>Saldo Pendiente Acreditaci&oacute;n</strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right"><strong>$<?= number_format($oMinuta->GetTotalPendiente(), 2, ',', '.') ?></strong></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																
															</table>
														</td>
													</tr>                                                               
													<tr>
														<td height="20">&nbsp;</td>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cuentascorriente.php<?=$strParams?>';" value="Volver" />
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