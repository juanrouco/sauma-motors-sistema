<?php

//require_once('ssi_errores.php'); 
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CMPB_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCliente			= intval($_REQUEST['IdCliente']);
$Cliente			= strval($_REQUEST['Cliente']);
$Fecha				= strval($_REQUEST['Fecha']);
$Detalle			= strval($_REQUEST['Detalle']);
$arrConceptoFactura	= $_REQUEST['ConceptoFactura'];
$arrImporte			= $_REQUEST['Importe'];
$arrIdIva			= $_REQUEST['IdIva'];
$Accion				= strval($_REQUEST['Accion']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$arrErr					= array();
$err					= 0;
$Subtotal				= 0;
$Iva10					= 0;
$Iva21					= 0;
$Total					= 0;
$oNotaCredito 			= new NotaCredito();
$oNotasCredito			= new NotasCredito();
$oComprobantes 			= new Comprobantes();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oConceptosFacturas 	= new ConceptosFacturas();
$oNotaCreditoDetalle	= new NotaCreditoDetalle();
$oNotasCreditoDetalles	= new NotasCreditoDetalles();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdCliente == '')
		$err |= 1;
	else
	{
		$oCliente = $oClientes->GetById($IdCliente);
		if ($oCliente->ObtenerNumeroDocumentoAfip() == '0')
			$err |= 2;
		if ($oCliente->ObtenerTipoDocumentoAfip() == ConstantesFacturaElectronica::DocumentoOtro)
			$err |= 2;
	}
	for ($i = 0; $i < count($arrConceptoFactura); $i++)
	{
		if ($arrConceptoFactura[$i] == '' ||$arrConceptoFactura[$i] == '0')
			$arrErr[$i] = 2;
		if ($arrImporte[$i] == '' || $arrImporte[$i] == '0')
			$arrErr[$i] = 2;
	}
	if ($Fecha == '')
		$err |= 4;
		
	/* si no hay errores... */
	if ($err == 0 && count($arrErr) == 0)
	{
		$oNotaCredito->IdCliente			= $IdCliente;
		$oNotaCredito->Fecha				= $Fecha;
		$oNotaCredito->Comentarios			= $Detalle;
		$oNotaCredito->Importe				= 0;
		
		$oCliente = $oClientes->GetById($IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
		
		$oComprobante	= new Comprobante();
		
		if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA)
			$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
		elseif ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaB)
			$oComprobante->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
		$oComprobante->Prefijo = str_pad(ConfiguracionFactura::PuntoVenta, 4, "0", STR_PAD_LEFT);
		$oComprobante->Numero = '00000000';
		$oComprobante->IdEstado = ComprobanteEstados::Libre;
		
		$oComprobante = $oComprobantes->Create($oComprobante);
		$oNotaCredito->IdComprobante = $oComprobante->IdComprobante;
		
		if ($oNotaCredito = $oNotasCredito->Create($oNotaCredito))
		{
			for ($i = 0; $i < count($arrConceptoFactura); $i++)
			{
				$DetalleItem		= $arrConceptoFactura[$i];
				$Importe			= intval($arrImporte[$i]);
				$IdIva				= intval($arrIdIva[$i]);
				
				$oNotaCreditoDetalle = new NotaCreditoDetalle();
					
				$oNotaCreditoDetalle->IdNotaCredito = $oNotaCredito->IdNotaCredito;
				$oNotaCreditoDetalle->Detalle 		= $DetalleItem;
				$oNotaCreditoDetalle->IdIva		 	= $IdIva;
				$oNotaCreditoDetalle->Importe		= $Importe;
				
				$oNotaCreditoDetalle = $oNotasCreditoDetalles->Create($oNotaCreditoDetalle);
				
				if ($oNotaCreditoDetalle->IdIva)
				{
					if ($oNotaCreditoDetalle->IdIva == Iva::Iva21)
					{
						$Importe = ($Importe / 1.21);
						$Iva21 += $oNotaCreditoDetalle->Importe - $Importe;
					}
					if ($oNotaCreditoDetalle->IdIva == Iva::Iva10)
					{
						$Importe = ($Importe / 1.105);
						$Iva10 += $oNotaCreditoDetalle->Importe - $Importe;
					}
				}
					
				$Subtotal += $Importe;
				$Total += $oNotaCreditoDetalle->Importe;
			}
			
			/* actualizamos detalle e importes de la factura */
			$oNotaCredito->Comentarios 	= $Detalle;
			$oNotaCredito->Subtotal 	= $Subtotal;
			$oNotaCredito->Iva10 		= $Iva10;
			$oNotaCredito->Iva21 		= $Iva21;
			$oNotaCredito->Importe 		= $Total;
			
			$oNotasCredito->Update($oNotaCredito);
		
			/* actualizamos el estado del comprobante */
			if ($oComprobante = $oComprobantes->GetById($oComprobante->IdComprobante))
			{
				$oComprobante->IdEstado		= ComprobanteEstados::Utilizado;
				$oComprobante->Fecha 		= $oNotaCredito->Fecha;
				$oComprobante->IdCliente	= $oNotaCredito->IdCliente;
				$oComprobante->Importe		= $Total;
				$oComprobante->ImporteIva10	= $Iva10;
				$oComprobante->ImporteIva21	= $Iva21;
				
				$oComprobantes->Update($oComprobante);
			}
		}
		
		if ($Accion == 'Guardar')
			header("Location: notascredito.php" . $strParams);
		elseif ($Accion == 'Enviar')
			header("Location: notascredito_afip.php?IdNotaCredito=" . $oNotaCredito->IdNotaCredito);
		exit();
	}
}
else
{
	/* determinamos como fecha a la fecha de ayer */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
}

$arrConceptosFacturas = $oConceptosFacturas->GetAll();
/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente;
	if (!(oCliente = GetCliente(IdCliente)))
		return;

	var oTipoiva;
	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	Get('lblTipoFactura').innerHTML = oTipoIva.FacturaTipo == '<?= ComprobanteTipos::FacturaA ?>' ? 'Nota de cr&eacute;dito A' : 'Nota de cr&eacute;dito B';
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
	
function QuitarItem(id) 
{
	$j('#row_' + id).remove();
	$j('#row_err_' + id).remove();
	ActualizarPrecios();
}

function AgregarItem() {
	var html = '<tr class="bordeGris">';	
		html+= '	<td height="30">&nbsp;</td>';
		html+= '	<td>';
		html+= '		<div id="margen" align="left">';
		html+= '			<input type="text" name="ConceptoFactura[]" id="ConceptoFactura[]" class="camporFormularioSimple" />';
		html+= '		</div>';
		html+= '	</td>';
		html+= '	<td>';
		html+= '		<div id="margen" align="left">';
		html+= '			<select id="IdIva[]" name="IdIva[]" class="camporFormularioChico actualizar-iva">';
		html+= '				<option value="<?= Iva::Iva21 ?>">21%</option>';
		html+= '				<option value="<?= Iva::Iva10 ?>">10,5%</option>';
		html+= '			</select>';
		html+= '		</div>';
		html+= '	</td>';
		html+= '	<td><div id="margen" align="left"><input type="text"  id="Importe[]" name="Importe[]" class="camporFormularioMediano actualizar-precio" /></div></td>';
		html+= '	<td><div id="margen" align="center"><a href="#" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>';
		html+= '	<td height="30">&nbsp;</td>';
		html+= '</tr>';
	
		var element = $j(html);
		element.find('#quitar-item').click(function(e) {
			e.preventDefault();
			element.remove();
		});
		element.find('.actualizar-precio').on('input',function(e){
			ActualizarPrecios();
		});
		element.find('.concepto-iva').change(function(e){
			ActualizarPrecios();
		});
		$j('#contenedor-items').append(element);
}

function ActualizarPrecios() {
	var total = 0;
	var iva10 = 0;
	var iva21 = 0;
	var count = 0;
	$j('.actualizar-precio').each(function() {
		var parcial = parseFloat($j(this).val());
		var IdIva = $j($j('.actualizar-iva')[count]).val();
		if (parcial && IdIva)
		{
			total+= parcial;
			if (IdIva == <?= Iva::Iva21 ?>)
				iva21+= parcial * 0.21 / 1.21;
			else
				if (IdIva == <?= Iva::Iva10 ?>)
					iva21+= parcial * 0.105 / 1.105;
		}
		count++;
	});
	$j('#lblIva10').html('$' + iva10.toFixed(2));
	$j('#lblIva21').html('$' + iva21.toFixed(2));
	$j('#lblTotal').html('$' + total.toFixed(2));
}

function EnviarAfip()
{
	$j('#Accion').val('Enviar');
	$j('#frmData').submit();
}

$j(document).ready(function() {
		<?php
		if (!$arrConceptoFactura || count($arrConceptoFactura) == 0)
		{
		?>
		AgregarItem();
		<?php
		}
		?>
		$j('#agregar-item').click(function(e) {
			e.preventDefault();
			
			AgregarItem();
		});
		$j('.actualizar-precio').on('input',function(e){
			ActualizarPrecios();
		});
		$j('.actualizar-iva').change(function(e){
			ActualizarPrecios();
		});
		ActualizarPrecios();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Notas de Cr&eacute;dito - Agregar</span></td>
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
					<input type="hidden" name="Accion" id="Accion" value="Guardar" />
                    
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
                                                    <td><div align="right">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                            <script language="javascript">
                                                            new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                            </script>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese fecha</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Cliente:</div></td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarCliente();" autocomplete="Off" />
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
                                                                <td><input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr id="trModificarCliente" style="display:none;">
                                                	<td>&nbsp;</td>
                                                    <td height="20" colspan="2"><a href="#" class="linkMenu" onclick="javascript:ModCliente();">Modificar datos del Cliente</a></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?><?php if ($err & 2) { ?><li style="color:#FF0000;">Por favor revise el tipo y n&uacute;mero de documento del cliente.</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                	<td>Tipo de NC:</td>
                                                	<td colspan="2"><label id="lblTipoFactura"></label></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td align="center">
											<table id="contenedor-items" width="75%" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr class="bordeGrisFondo">
													<td height="30" width="2%">&nbsp;</td>
													<td width="40%"><div id="margen" align="left"><strong>Detalle</strong></div></td>
													<td width="20%"><div id="margen" align="left"><strong>IVA</strong></div></td>									
													<td width="30%"><div id="margen" align="left"><strong>Importe C/IVA</strong></div></td>									
													<td width="6%"><div id="margen" align="left">&nbsp;</div></td>
													<td height="30" width="2%">&nbsp;</td>
												</tr>
												<?php
												if ($arrConceptoFactura && count($arrConceptoFactura) > 0)
												{
													for ($i = 0; $i < count($arrConceptoFactura); $i++)
													{
												?>
												<tr id="row_<?= $i ?>" class="bordeGris">
													<td>&nbsp;</td>
													<td>
														<div id="margen" align="left">
															<input type="text" name="ConceptoFactura[]" id="ConceptoFactura[]" class="camporFormularioSimple" value="<?= $arrConceptoFactura[$i] ?>" />
														</div>
													</td>
													<td>
														<div id="margen" align="left">
															<select name="IdIva[]" id="IdIva[]" class="camporFormularioChico actualizar-iva">
																<option value="<?= Iva::Iva21 ?>" <?= Iva::Iva21 == $arrIdIva[$i] ? 'selected="selected"' : '' ?>>21%</option>
																<option value="<?= Iva::Iva10 ?>" <?= Iva::Iva10 == $arrIdIva[$i] ? 'selected="selected"' : '' ?>>10,5%</option>
															</select>
														</div>
													</td>
													<td>
														<div id="margen" align="left">
															<input type="text" name="Importe[]" id="Importe[]" class="camporFormularioMediano actualizar-precio" maxlength="128" value="<?=$arrImporte[$i]?>" />
														</div>
													</td>
													<td align="center"><a href="#" onclick="QuitarItem('<?= $i ?>'); return false;"><img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" /></a></td>
													<td height="30" width="2%">&nbsp;</td>
												</tr>
												<?php
														if (count($arrErr) >0 && $arrErr[$i] == 2)
														{
												?>
												<tr id="row_err_<?= $i ?>" class="bordeGris">
													<td>&nbsp;</td>
													<td colspan="4">
														<div id="margen" align="left">
															<li style="color: red">Debe ingresar el item, e ingresar un importe mayor a cero.</li>
														</div>
													</td>
													<td height="30" width="2%">&nbsp;</td>
												</tr>
												<?php
														}
													}
												}
												?>
												
											</table>
										</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td align="right"><a href="#" id="agregar-item" style="margin-right: 35px">Agregar Item</a></td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td>
											<table border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td width="10">&nbsp;</td>
													<td height="25" valign="top"><strong style="margin-top: 3px">Detalle: </strong></td>
													<td height="25" valign="top">
														<textarea name="Detalle" id="Detalle" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$Detalle?></textarea>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td align="center">
											<table width="75%" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td align="right">
														<table border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td align="right">
																	<strong>Iva 10,5%:</strong>
																</td>
																<td width="2">&nbsp;</td>
																<td><label id="lblIva10">0.00</label></td>
															</tr>
															<tr>
																<td colspan="3">&nbsp;</td>
															</tr>
															<tr>
																<td align="right">
																	<strong>Iva 21%:</strong>
																</td>
																<td width="2">&nbsp;</td>
																<td><label id="lblIva21">0.00</label></td>
															</tr>
															<tr>
																<td colspan="3">&nbsp;</td>
															</tr>
															<tr>
																<td align="right">
																	<strong>Total:</strong>
																</td>
																<td width="2">&nbsp;</td>
																<td><label id="lblTotal">0.00</label></td>
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
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Guardar" />
									<input type="button" name="btnEnviar" class="botonBasico" id="btnEnviar" value="Guardar y Enviar" onclick="EnviarAfip();" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'notascredito.php<?=$strParams?>';" value="Cancelar" />
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
FilterCliente('<?=$IdCliente?>', '');
</script>

</body>
</html>