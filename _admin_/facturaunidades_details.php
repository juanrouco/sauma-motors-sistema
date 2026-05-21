<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura	= intval($_REQUEST['IdFactura']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oFacturaUnidades	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la factura */
if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oFacturaUnidad->IdMinuta))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos del comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}


/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oComprobante->IdCliente))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

if ($oMinuta->Condominio)
{
	/* obtenemos los datos del cliente */
	if (!$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio))
	{	
		header("Location: facturaunidades.php" . $strParams);
		exit();
	}
	
	/* obtenemos los datos de condicion de iva del cliente */
	if (!$oTipoIvaCondominio = $oTiposIva->GetById($oClienteCondominio->IdTipoIva))
	{	
		header("Location: facturaunidades.php" . $strParams);
		exit();
	}

	/* obtenemos los datos de la localidad */
	if (!$oLocalidadCondominio = $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad))
	{	
		header("Location: facturaunidades.php" . $strParams);
		exit();
	}
}
/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos de la localidad */
if (!$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos de la marca */
if (!$oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
{	
	header("Location: facturaunidades.php" . $strParams);
	exit();
}

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
{	
	header("Location: facturaunidades.php" . $strParams);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Ventas de Unidades - Agregar</span></td>
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
					<input type="hidden" name="IdFactura" id="IdFactura" value="<?=$IdFactura?>" />
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
                                            <table width="100%" border="0" align="center" cellpadding="3" cellspacing="3">
                                                <tr>
                                                    <td width="17%"><div align="right">Nro. Carpeta:</div></td>
                                                    <td width="83%">
                                                        <div align="left">
                                                        	<span><?=$oFacturaUnidad->IdMinuta?></span>
                                                        </div>
                                                    </td>
                                                </tr>
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
                                                        	<span><?=$oFacturaUnidad->NumeroComprobante?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=CambiarFecha($oFacturaUnidad->Fecha)?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                           	<?php if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA) { ?>
                                                <tr>
                                                    <td><div align="right">Subtotal:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span>$ <?=number_format($oFacturaUnidad->Subtotal, 2)?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">IVA 10.5%:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span>$ <?=number_format($oFacturaUnidad->Iva10, 2)?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">IVA 21%:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span>$ <?=number_format($oFacturaUnidad->Iva21, 2)?></span>
                                                        </div>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td><div align="right">Impuesto Interno:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span>$ <?=number_format($oFacturaUnidad->ImpuestoInterno, 2)?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                           	<?php } ?>
                                                
                                                <tr>
                                                    <td><div align="right">Total:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span>$ <?=number_format($oFacturaUnidad->Total, 2)?></span>
                                                        </div>
                                                    </td>
                                                </tr>

											<?php if ($oFacturaUnidad->OtrosTitulares != '') { ?>
                                                <tr>
                                                    <td><div align="right">Otros Titulares:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$oFacturaUnidad->OtrosTitulares?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                         	<?php } ?>
											<?php if ($oFacturaUnidad->Observaciones != '') { ?>
                                                <tr>
                                                    <td><div align="right">Observaciones:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<span><?=$oFacturaUnidad->Observaciones?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                         	<?php } ?>

                                            </table>
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
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Minuta</span></td>
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
													<?php
														if ($oMinuta->Condominio)
														{
													?>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente Condominio:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oClienteCondominio->RazonSocial?></label>
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
                                                                                        <label id="ClienteDomicilio"><?=$oClienteCondominio->GetDomicilio()?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidadCondominio->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidadCondominio->CodigoPostal?></label>
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
                                                                        	<label id="ClienteTelefono"><?=$oClienteCondominio->TelefonoCodigoArea . ' - ' . $oClienteCondominio->Telefono?></label>
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
                                                                                        <label id="ClienteCondicionIva"><?=$oTipoIvaCondominio->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT/CUIL:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero?></label>
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
													<?php
														}
													?>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturaunidades.php<?=$strParams?>';" value="Volver" />
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