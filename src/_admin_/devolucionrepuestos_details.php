<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCompra	= intval($_REQUEST['IdCompra']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oCompras				= new Compras();
$oComprobantes 			= new Comprobantes();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oLocalidades 			= new Localidades();
$oArticulos				= new Articulos();
$oTallerUnidades		= new TallerUnidades();
$oCuponesDescuento		= new CuponesDescuento();
$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
$oNotasCredito 			= new NotasCredito();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la factura */
if (!$oCompra = $oCompras->GetById($IdCompra))
{	
	header("Location: devolucionrepuestos.php" . $strParams);
	exit();
}
$oCompra->LoadAllDetalles();

$oTipoVenta = TipoVenta::GetById($oCompra->TipoOperacion);

/*if ($oCompra->IdFactura)
{
	if (!$oFactura = $oComprobantes->GetById($oCompra->IdFactura))
	{	
		header("Location: ventarepuestos.php" . $strParams);
		exit();
	}
}*/

if ($oCompra->IdNotaCredito)
{
	$oNotaCredito = $oNotasCredito->GetById($oCompra->IdNotaCredito);
	$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante);
	$nc = $oComprobante->Prefijo . '-' . $oComprobante->Numero;
}

if ($oCompra->IdCliente)
{
	if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
	{	
		header("Location: devolucionrepuestos.php" . $strParams);
		exit();
	}
}
else
{
	$oTallerUnidad = $oTallerUnidades->GetById($oCompra->IdTallerUnidad);
	$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
	if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
	{	
		header("Location: devolucionrepuestos.php" . $strParams);
		exit();
	}
}

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: devolucionrepuestos.php" . $strParams);
	exit();
}

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Devoluciones de Repuestos</span></td>
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
					<input type="hidden" name="IdCompra" id="IdCompra" value="<?=$IdCompra?>" />					
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                <tr>
                                                    <td width="20%"><div align="left">Tipo Devoluci&oacute;n:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$oTipoVenta['Nombre']?> <?= $oCompra->IdOrdenTrabajo ? ' - OT N&deg; ' . $oCompra->IdOrdenTrabajo : '' ?></span>
                                                        </div>
                                                    </td>
                                                </tr>
												<?php
												if ($oOrdenTrabajoTarea)
												{
													$oTipoCargo = TipoVenta::GetByIdOrdenTrabajo($oOrdenTrabajoTarea->IdTipoVenta);
												?>
												<tr>
                                                    <td width="20%"><div align="left">Tipo Cargo:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$oTipoCargo['Nombre']?></span>
                                                        </div>
                                                    </td>
                                                </tr>
												
												<?php
												}
												?>
												<tr>
                                                    <td width="20%"><div align="left">Nota de Cr&eacute;dito:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$nc?></span>
                                                        </div>
                                                    </td>
                                                </tr>
												<?php
												/*
												<tr>
                                                    <td><div align="right">Factura Tipo:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=ComprobanteTipos::GetDescripcionById($oTipoIva->FacturaTipo)?></span>
                                                        </div>
                                                    </td>
                                                </tr>												
                                                <tr>
                                                    <td><div align="right">Nro. Factura:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$oFactura->Prefijo?> - <?=$oFactura->Numero?></span>
                                                        </div>
                                                    </td>
                                                </tr>*/ ?>
                                                <tr>
                                                    <td><div align="left">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=CambiarFecha($oCompra->FechaCarga)?></span>
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
                                        	<div align="center">
                                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
																<tr>
                                                                    <td><div id="margen" align="left">Orden de Trabajo N&deg;:</div></td>
																	 <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="43%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilio"><?=$oCompra->IdOrdenTrabajo?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">Tarea:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?= $oOrdenTrabajoTarea ? $oOrdenTrabajoTarea->Titulo : '' ?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oCliente->RazonSocial?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Domicilio:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="36%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilio"><?=$oCliente->GetDomicilio()?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?= $oLocalidad ? $oLocalidad->Nombre : '' ?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?= $oLocalidad ? $oLocalidad->CodigoPostal : '' ?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tel&eacute;fono:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="ClienteTelefono"><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Condici&oacute;n IVA:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="43%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCondicionIva"><?=$oTipoIva->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT/CUIL:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></label>
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
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
													<tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table id="articulos" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0">
																<tr class="bordeGrisFondo">
																	<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
																	<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
																	<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio<br />(s/IVA)</strong></div></td>
																	<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 21%</strong></div></td>
																	<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 10,5%</strong></div></td>
																	<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cantidad</strong></div></td>
																	<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Subtotal</strong></div></td>
																</tr>
																<?php
																foreach ($oCompra->CompraDetalles as $oCompraDetalle)
																{
																	$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
																?>
																<tr height="20">
																	<td>
																		<div id="margen"><?= $oArticulo->Codigo ?></div>
																	</td>
																	<td>
																		<div id="margen"><?= $oArticulo->Descripcion ?></div>
																	</td>
																	<td>
																		<div id="margen">$<?= $oCompraDetalle->GetSubtotalSinIva() ?></div>
																	</td>
																	<td>
																		<div id="margen">$<?= $oCompraDetalle->GetUnitarioIva(1) ?></div>
																	</td>
																	<td>
																		<div id="margen">$<?= $oCompraDetalle->GetUnitarioIva(2) ?></div>
																	</td>
																	<td>
																		<div id="margen"><?= $oCompraDetalle->Cantidad ?></div>
																	</td>
																	<td>
																		<div id="margen">$<?= $oCompraDetalle->GetSubtotal() ?></div>
																	</td>
																</tr>
																<?php
																}
																?>
															</table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>
													<tr>
														<td>
															<table border="0" align="right" cellpadding="0" cellspacing="0">
															<?php 
															if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA || true) 
															{ 
															?>
																<tr>
																	<td><div align="left">Subtotal:</div></td>
																	<td width="10">&nbsp;</td>
																	<td>
																		<div align="left">
																			<span>$ <?=$oCompra->GetSubtotal()?></span>
																		</div>
																	</td>
																	<td>&nbsp;</td>
																</tr>
																<tr>
																	<td><div align="left">IVA 21%:</div></td>
																	<td width="10">&nbsp;</td>
																	<td>
																		<div align="left">
																			<span>$ <?=$oCompra->GetSubtotalIva(1)?></span>
																		</div>
																	</td>
																	<td>&nbsp;</td>
																</tr>		
																<tr>
																	<td><div align="left">IVA 10,5%:</div></td>
																	<td width="10">&nbsp;</td>
																	<td>
																		<div align="left">
																			<span>$ <?=$oCompra->GetSubtotalIva(2)?></span>
																		</div>
																	</td>
																	<td>&nbsp;</td>
																</tr>																	
															<?php 
															} 
															if ($oCompra->IdCuponDescuento)
															{
																if ($oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento))
																{
															?>
																<tr>
																	<td><div align="left">Descuento:</div></td>
																	<td width="10">&nbsp;</td>
																	<td>
																		<div align="left">
																			<span><?=$oCuponDescuento->Descuento?>%</span>
																		</div>
																	</td>
																	<td>&nbsp;</td>
																</tr>
															<?php
																}
															}
															?>
																<tr>
																	<td><div align="left">Total:</div></td>
																	<td width="10">&nbsp;</td>
																	<td>
																		<div align="left">
																			<span>$ <?=number_format($oCompra->Total(), 2)?></span>
																		</div>
																	</td>
																	<td width="20">&nbsp;</td>
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
									<?php
									if (!$oCompra->IdNotaCredito && $oCompra->TipoOperacion == TipoVenta::Mostrador)
									{
									?>
									<input type="button" name="btnImprimirFactura" class="botonBasico" id="btnImprimirFactura" onclick="javascript: window.open('devolucionrepuestos_notacredito.php?IdCompra=<?= $oCompra->IdCompra ?>')" value="Imprimir Nota de Cr&eacute;dito" />
									<?php
									}
									?>
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'devolucionrepuestos.php<?=$strParams?>';" value="Volver" />
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