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
$oMinutas					= new MinutasUsados();
$oUsados					= new Usados();
$oClientes					= new Clientes();
$oUsuarios					= new Usuarios();
$oMarcas					= new Marcas();
$oColores					= new Colores();
$oMinutasFinanciacion		= new MinutasUsadosFinanciacion();
$oContratosPrendas			= new ContratosPrendasUsados();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: cuentascorrienteusados.php" . $strParams);
	exit();
}
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
	$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinuta->IdMinuta);
	
	$oUsadoTomado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$oUsado 		= $oUsados->GetById($oMinuta->IdUsado);
$oCliente 		= $oClientes->GetById($oMinuta->IdCliente);
$oUsuario 		= $oUsuarios->GetById($oMinuta->IdUsuario);
$oUsadoMarca 	= $oMarcas->GetById($oUsadoTomado->IdMarca);
$oUsadoColor 	= $oColores->GetById($oUsadoTomado->IdColor);
$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);

if ($oMinuta->Condominio)
{
	$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
}

$VehiculoModelo = $oUsado->Modelo;

$IdUsado				= $oMinuta->IdUsado;
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
$Gastos					= $oMinuta->Gastos;
$GastosPatentamiento	= $oMinuta->GastosPatentamiento;
$GastosOtorgamiento		= $oMinuta->GastosOtorgamiento;
$DepositoGarantia		= $oMinuta->DepositoGarantia;
$GastosPrenda			= $oMinuta->GastosPrenda;
$Circular				= $oMinuta->Circular;
$Anticipo				= $oMinuta->Anticipo;
//$FinanciacionCapital	= $oMinuta->FinanciacionCapital;
///$Financiacion			= (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) ? '1' : '0';
$Condominio				= $oMinuta->Condominio;
$EntregaUsado			= $oMinuta->EntregaUsado;
$PlazoPrenda			= $oMinuta->PlazoPrenda;
$Chasis					= $oUnidad->NumeroChasis;
$NumeroMotor			= $oUnidad->NumeroMotor;
$Alta					= $oMinuta->Alta;

/* datos del usado */
$UsadoIdMarca		= $oUsadoMarca->IdMarca;
$UsadoMarca			= $oUsadoMarca->Nombre;
$UsadoMarcaCodigo	= $oUsadoMarca->Codigo;
$UsadoIdMarca2		= $oUsadoMarca2->IdMarca;
$UsadoMarca2		= $oUsadoMarca2->Nombre;
$UsadoMarcaCodigo2	= $oUsadoMarca2->Codigo;
$UsadoIdColor		= $oUsadoColor->IdColor;
$UsadoColor			= $oUsadoColor->Nombre;
$UsadoColorCodigo	= $oUsadoColor->Codigo;
$UsadoIdColor2		= $oUsadoColor2->IdColor;
$UsadoColor2		= $oUsadoColor2->Nombre;
$UsadoColorCodigo2	= $oUsadoColor2->Codigo;
$UsadoModelo		= $oUsadoTomado->Modelo;
$UsadoModeloAnio	= $oUsadoTomado->ModeloAnio;
$UsadoKilometraje	= $oUsadoTomado->Kilometraje;
$UsadoValuacion		= $oUsadoTomado->Valuacion;
$UsadoValuacion2	= 0;
$UsadoValuacion2	= $oUsado2->Valuacion;
$UsadoDominio		= $oUsado->Dominio;

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Usados - Detalle</span></td>
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
																<tr  <?=($EntregaUsado) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Usado</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">- $<?= number_format($UsadoValuacion + $UsadoValuacion2, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center"><?= $oUsadoTomado->IdUbicacion != Ubicacion::Transito ? '<span style="color: green">Acreditado</span>' : '<span style="color: red">Sin Acreditar</span>' ?></div></td>
																</tr>
																<tr  <?=($EntregaUsado) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Arreglos Usados</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($oUsadoTomado->Arreglos + $oUsado2->Arreglos, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr  <?=($Financiacion) ? '' : 'style="display:none;"'?>>
																	<td width="40%" height="25"><div id="margen" align="left">Capital a Financiar</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">- $<?= number_format($FinanciacionCapital, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%">&nbsp;</td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Gastos Otorgamiento</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($GastosOtorgamiento, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Gastos Gestor&iacute;a</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($Anticipo, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Otros Gastos</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($Gastos, 2, ',', '.') ?></div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="center">&nbsp;</div></td>
																</tr>
																<tr>
																	<td width="40%" height="25"><div id="margen" align="left">Quebranto</div></td>
																	<td width="5%">&nbsp;</td>
																	<td width="25%"><div id="margen" align="right">$<?= number_format($GastosPrenda, 2, ',', '.') ?></div></td>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cuentascorrienteusados.php<?=$strParams?>';" value="Volver" />
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