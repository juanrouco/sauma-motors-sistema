<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PACC_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPedido	= intval($_REQUEST['IdPedido']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= 0;
$oPedidosAccesorios			= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oMinutas 					= new Minutas();
$oClientes 					= new Clientes();
$oTiposIva 					= new TiposIva();
$oUnidades 					= new Unidades();
$oModelos 					= new Modelos();
$oLocalidades 				= new Localidades();
$oColores 					= new Colores();
$oMarcas 					= new Marcas();
$oTiposModelo 				= new TiposModelo();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la orden */
if (!$oPedidoAccesorios = $oPedidosAccesorios->GetById($IdPedido))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

$arrPedidosAccesoriosItems = $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($oPedidoAccesorios);

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oPedidoAccesorios->IdMinuta))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos de la localidad */
if (!$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos de la marca */
if (!$oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
}

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
{	
	header("Location: pedidosaccesorios.php" . $strParams);
	exit();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Pedidos de Accesorios - Detalle</span></td>
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
					<input type="hidden" name="IdPedido" id="IdPedido" value="<?=$IdPedido?>" />
					<input type="hidden" name="IdMinuta" id="IdMinuta" value="" />
                    
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
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Operaci&oacute;n</span></td>
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
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Nro. Operaci&oacute;n:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="NumeroOperacion"><?=$oUnidad->IdUnidad?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Fecha Venta:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="FacturaFecha"><?=CambiarFecha($oMinuta->FechaMinuta)?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Comentarios:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="FacturaFecha"><?=$oPedidoAccesorios->Accesorios?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td width="20%"><div id="margen" align="left">Accesorios:</div></td>
                                                                    <td width="80%">&nbsp;</td>
                                                                </tr>
																<tr>
																	<td colspan="2">
																		<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																			<tr class="bordeGrisFondo">
																				<td height="30"><div id="margen"><strong>Item</strong></div></td>
																				<td width="10">&nbsp;</td>
																				<td width="200"><div id="margen" align="center"><strong>Importe</strong></div></td>
																			</tr>
																<?php
																if ($arrPedidosAccesoriosItems)
																{
																	foreach ($arrPedidosAccesoriosItems as $oPedidoAccesorioItem)
																	{
																?>
																			<tr class="bordeGris">
																				<td height="30"><div id="margen"><?= $oPedidoAccesorioItem->Detalle ?></div></td>
																				<td width="10">&nbsp;</td>
																				<td width="200"><div id="margen" align="center">$<?=number_format($oPedidoAccesorioItem->Importe, 2)?></div></td>
																			</tr>
																<?php
																	}
																}
																?>
																		</table>
																	</td>
																</tr>
																<tr>
                                                                    <td colspan="2">&nbsp;</td>
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
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
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
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidad->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidad->CodigoPostal?></label>
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
                                                                                <td width="8%"><div align="left">CUIT:</div></td>
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
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Marca:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="VehiculoMarca"><?=$oMarca->Nombre?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tipo:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="53%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoTipo"><?=$oTipoModelo->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="7%"><div align="left">A&ntilde;o:</div></td>
                                                                            	<td width="40%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoAnio"><?=$oModelo->Anio?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Modelo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoModelo"><?=$oModelo->DenominacionModelo?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Motor:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroMotor"><?=$oUnidad->NumeroMotor?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Chasis:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroChasis"><?=$oUnidad->NumeroChasis?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Color:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoColor"><?=$oColor->Nombre?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Equipo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoDenominacionComercial"><?=$oModelo->DenominacionComercial?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Iva:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoIva"><?=$oModelo->Iva?> %</label>
                                                                        </div>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pedidosaccesorios.php<?=$strParams?>';" value="Volver" />
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