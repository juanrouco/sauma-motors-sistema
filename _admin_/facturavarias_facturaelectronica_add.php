<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACV_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCliente			= intval($_REQUEST['IdCliente']);
$Cliente			= strval($_REQUEST['Cliente']);
$Fecha				= strval($_REQUEST['Fecha']);
$Detalle			= strval($_REQUEST['Detalle']);
$arrConceptoFactura	= $_REQUEST['IdConceptoFactura'];
$arrImporte			= $_REQUEST['Importe'];
$Accion				= strval($_REQUEST['Accion']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$arrErr					= array();
$err					= 0;
$Subtotal				= 0;
$Iva10					= 0;
$Iva21					= 0;
$Total					= 0;
$oFacturaVaria 			= new FacturaVaria();
$oFacturaVarias			= new FacturaVarias();
$oComprobantes 			= new Comprobantes();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oConceptosFacturas 	= new ConceptosFacturas();
$oFacturaVariaDetalle	= new FacturaVariaDetalle();
$oFacturaVariaDetalles	= new FacturaVariaDetalles();

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
		$oFacturaVaria->IdCliente			= $IdCliente;
		$oFacturaVaria->Fecha				= $Fecha;
		$oFacturaVaria->Detalle				= $Detalle;
		
		$oCliente = $oClientes->GetById($IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
		
		$oComprobante	= new Comprobante();
		$oComprobante->IdTipoComprobante = $oTipoIva->FacturaTipo;
		$oComprobante->Prefijo = str_pad(ConfiguracionFactura::PuntoVenta, 4, "0", STR_PAD_LEFT);
		$oComprobante->Numero = '00000000';
		$oComprobante->IdEstado = ComprobanteEstados::Libre;
		
		$oComprobante = $oComprobantes->Create($oComprobante);
		$oFacturaVaria->IdComprobante = $oComprobante->IdComprobante;
		
		if ($oFacturaVaria = $oFacturaVarias->Create($oFacturaVaria))
		{
			for ($i = 0; $i < count($arrConceptoFactura); $i++)
			{
				$IdConceptoFactura	= $arrConceptoFactura[$i];
				$Importe			= floatval($arrImporte[$i]);
				
				$oFacturaVariaDetalle = new FacturaVariaDetalle();
				$oConceptoFactura = $oConceptosFacturas->GetById($IdConceptoFactura);
					
				$oFacturaVariaDetalle->IdFactura 	= $oFacturaVaria->IdFactura;
				$oFacturaVariaDetalle->Detalle 		= $oConceptoFactura->Nombre;
				$oFacturaVariaDetalle->IvaGravado 	= $oConceptoFactura->IvaGravado;
				$oFacturaVariaDetalle->Importe		= $Importe;
				
				$oFacturaVariaDetalle = $oFacturaVariaDetalles->Create($oFacturaVariaDetalle);
				
				if ($oFacturaVariaDetalle->IvaGravado)
				{
					if ($oFacturaVariaDetalle->IvaGravado == 1)
					{
						$Importe = ($Importe / 1.21);
						$Iva21 += $oFacturaVariaDetalle->Importe - $Importe;
					}
					elseif ($oFacturaVariaDetalle->IvaGravado == 2)
					{
						$Importe = ($Importe / 1.105);
						$Iva10 += $oFacturaVariaDetalle->Importe - $Importe;
					}
				}
					
				$Subtotal += $Importe;
				$Total += $oFacturaVariaDetalle->Importe;
			}
			
			$oFacturaVaria->Detalle 	= $Detalle;
			$oFacturaVaria->Subtotal 	= $Subtotal;
			$oFacturaVaria->Iva10 		= $Iva10;
			$oFacturaVaria->Iva21 		= $Iva21;
			$oFacturaVaria->Total 		= $Total;
			
			$oFacturaVarias->Update($oFacturaVaria);
		
			/* actualizamos el estado del comprobante */
			if ($oComprobante = $oComprobantes->GetById($oComprobante->IdComprobante))
			{
				$oComprobante->IdEstado		= ComprobanteEstados::Utilizado;
				$oComprobante->Fecha 		= $oFacturaVaria->Fecha;
				$oComprobante->IdCliente	= $oFacturaVaria->IdCliente;
				$oComprobante->Importe		= $Total;
				$oComprobante->ImporteIva10	= $Iva10;
				$oComprobante->ImporteIva21	= $Iva21;
				
				$oComprobantes->Update($oComprobante);
			}
		}
		
		if ($Accion == 'Guardar')
			header("Location: facturavarias.php" . $strParams);
		elseif ($Accion == 'Enviar')
			header("Location: facturavarias_afip.php?IdFactura=" . $oFacturaVaria->IdFactura);
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

var arrIvas = {
<?php
foreach ($arrConceptosFacturas as $oConceptoFactura)
{
	$Iva = 0;
	if ($oConceptoFactura->IvaGravado)
	{
		if ($oConceptoFactura->IvaGravado == 1)
		{
			$Iva = 0.21;
		}
		elseif ($oFacturaVariaDetalle->IvaGravado == 2)
		{
			$Iva = 0.105;
		}
	}
?>
	<?= $oConceptoFactura->IdConceptoFactura ?>: <?= $Iva ?>,
<?php
}
?>
	3333: 0
};

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
	Get('lblTipoFactura').innerHTML = oTipoIva.FacturaTipo == '<?= ComprobanteTipos::FacturaA ?>' ? 'FACTURA A' : 'FACTURA B';
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
		html+= '			<select name="IdConceptoFactura[]" id="IdConceptoFactura[]" class="camporFormularioSimple concepto-factura">';
		html+= '				<option value="">Seleccione el item</option>';
		<?php
		foreach ($arrConceptosFacturas as $oConceptoFactura)
		{
		?>
		html+= '				<option value="<?= $oConceptoFactura->IdConceptoFactura ?>" <?= $seledted ?>><?=$oConceptoFactura->Nombre?></option>';
		<?php
		}
		?>
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
		element.find('.concepto-factura').change(function(e){
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
		var IdConcepto = $j($j('.concepto-factura')[count]).val();
		if (parcial && IdConcepto)
		{
			total+= parcial;
			if (arrIvas[IdConcepto] == 0.21)
				iva21+= parcial * 0.21 / 1.21;
			else
				if (arrIvas[IdConcepto] == 0.105)
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
		$j('.concepto-factura').change(function(e){
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Agregar</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>"  onsubmit="btnEnviar.disabled = true; return true;">
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
                                                	<td>Tipo de Factura:</td>
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
													<td width="50%"><div id="margen" align="left"><strong>Detalle</strong></div></td>
													<td width="40%"><div id="margen" align="left"><strong>Importe C/IVA</strong></div></td>									
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
															<select name="IdConceptoFactura[]" id="IdConceptoFactura[]" class="camporFormularioSimple concepto-factura">
																<option value="">Seleccione el item</option>
																<?php
																foreach ($arrConceptosFacturas as $oConceptoFactura)
																{
																	$selected = '';
																	if ($oConceptoFactura->IdConceptoFactura == $arrConceptoFactura[$i])
																		$selected = "selected='selected'";
																?>
																<option value="<?= $oConceptoFactura->IdConceptoFactura ?>" <?= $selected ?>><?=$oConceptoFactura->Nombre?></option>
																<?php
																}
																?>
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
													<td colspan="3">
														<div id="margen" align="left">
															<li style="color: red">Debe seleccionar el item, e ingresar un importe mayor a cero.</li>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturavarias.php<?=$strParams?>';" value="Cancelar" />
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