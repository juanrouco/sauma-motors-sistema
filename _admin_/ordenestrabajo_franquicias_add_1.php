<?php


require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$Importe				= floatval($_REQUEST['Importe']);
$Cliente				= strval($_REQUEST['Cliente']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$IdComprobante			= intval($_REQUEST['IdComprobante']);
$Comprobante			= strval($_REQUEST['Comprobante']);
$IdFormaPago			= intval($_REQUEST['IdFormaPago']);
$IdPlanCuota			= intval($_REQUEST['IdPlanCuota']);
$Descuento				= floatval($_REQUEST['Descuento']);
$Submit					= (isset($_REQUEST['Submitted']));

$err						= 0;
$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades			= new TallerUnidades();
$oClientes					= new Clientes();
$oComprobantes				= new Comprobantes();
$oOrdenTrabajoFactura 		= new OrdenTrabajoFactura();
$oFormasPago				= new FormasPago();
$oPlanesCuotas				= new PlanesCuotas();
$oFacturasPostVentas		= new FacturasPostVentas();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);

$arrFormasPago = $oFormasPago->GetAll();

if ($Submit)
{
	if ($Importe == '')
		$err |= 1;
	if ($Descripcion == '')
		$err |= 2;
	if ($IdCliente == '' || $IdCliente == 0)
		$err |= 4;
	if ($IdComprobante == '' || $IdComprobante == 0)
		$err |= 8;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$Importe	= str_replace(",", ".", $Importe);
		
		$oCliente = $oClientes->GetById($IdCliente);
		
		$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
		$oOrdenTrabajoFranquicia->IdCliente			= $IdCliente;
		$oOrdenTrabajoFranquicia->Importe			= $Importe;
		$oOrdenTrabajoFranquicia->IdOrdenTrabajo	= $IdOrdenTrabajo;
		$oOrdenTrabajoFranquicia->Descripcion		= $Descripcion;
		
		$oOrdenTrabajoFranquicia = $oOrdenesTrabajoFranquicias->Create($oOrdenTrabajoFranquicia);
		
		$oComprobante = $oComprobantes->GetById($IdComprobante);
		$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
		$oComprobante->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
		$oComprobante->Fecha = date('d-m-Y');
		$oComprobante->IdCliente = $oCliente->IdCliente;
		
		$oFacturaPostVenta = $oOrdenTrabajoFactura->GenerarFacturaFromFranquicia($oOrdenTrabajoFranquicia, $oCliente, $oComprobante, $IdPlanCuota, $Descuento);
		
		if ($IdFormaPago != FormaPago::CtaCte)
		{
			$oFacturaPostVenta->TotalPago = $oFacturaPostVenta->ImporteBruto;
			$oFacturaPostVenta->FechaPago = date('d-m-Y');
			$oFacturasPostVentas->Update($oFacturaPostVenta);
		}
		
		$oComprobante->Importe = $oFacturaPostVenta->ImporteBruto;
		$oComprobante->PercepcionIIBB = $oFacturaPostVenta->PercepcionIIBB;
		$oComprobante->ImporteIva21 = $oFacturaPostVenta->Iva21;
		$oComprobante->ImporteIva10 = 0;
		
		$oComprobantes->Update($oComprobante);
		
		$oOrdenTrabajoFranquicia->IdComprobante = $oComprobante->IdComprobante;
		$oOrdenTrabajoFranquicia->Importe = $oFacturaPostVenta->ImporteBruto;
		$oOrdenesTrabajoFranquicias->Update($oOrdenTrabajoFranquicia);

		header('Location: ordenestrabajo_facturacion.php' . $strParams);
		exit();
	}
}
else
{
	$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
	$IdCliente = $oCliente->IdCliente;
	$Cliente = $oCliente->RazonSocial;
	$IdFormaPago = FormaPago::Efectivo;
	$IdPlanCuota = 1;
	$Descuento = 0;
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

var IdTipoComprobante = '';
var arrParams = new Array();
arrParams['Prefijo'] = '0002';
var TotalOT = 0;
var IvaOT = 0;
var PercIIBBOT = 0;
var NetoOT = 0;

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	Get('cuit-cliente').innerHTML 	= 'CUIT/CUIL: ' + oCliente.ClaveFiscalNumero;
	
	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
		return;
		
	if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaA?>')
	{		
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaA?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
		
		/* obtenemos la proxima factura */
		oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaA?>', '0002');
			
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;
			
		$j('#lblTipoFactura').html('FACTURA A');
	}
	else if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

		/* obtenemos la proxima factura */
		oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaB?>', '0002');
			
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;
		$j('#lblTipoFactura').html('FACTURA B');
	}
	
	ActualizarFacturacion();
}

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	Get('IdComprobante').value 		= IdComprobante;
	Get('NumeroComprobante').value 	= NumeroComprobante;
}

function GetNextFactura(IdTipoComprobante, prefijo)
{
	var arr = new Array();
	var obj;
	var oComprobante;

	if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
		return;
					
	arr['IdTipoComprobante'] = IdTipoComprobante;
	arr['Prefijo'] = prefijo;
	obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
		
	oComprobante = obj.Response;

	return oComprobante;	
}

function ActualizarFacturacion()
{
	var IdCliente = $j('#IdCliente').val();
	if (IdCliente == '' || IdCliente == '0')
		return false;
		
	var oCliente = GetCliente(IdCliente);
	if (!oCliente)
		return false;
	
	var IdPlanCuota = $j('#IdPlanCuota').val();
	if (IdPlanCuota == null || IdPlanCuota == '' || IdPlanCuota == '0')
		IdPlanCuota = '<?= $IdPlanCuota ?>';
		
	var oPlanCuota = GetPlanCuota(IdPlanCuota);
	if (!oPlanCuota)
		return false;
		
	var Descuento = parseFloat($j('#Descuento').val());
	if (!Descuento)
		Descuento = 0;
		
	var IndiceDescuento = (100 - Descuento) / 100;
	
	
	var multiplicadorInteres = oPlanCuota.Interes * oPlanCuota.Coeficiente / 100;
	
	var itemBruto = parseFloat($j('#Importe').val()) * IndiceDescuento;
	var NetoOT2 = itemBruto / 1.21;
	var IvaOT2 = NetoOT2 * 0.21;
	var PercIIBBOT2 = NetoOT2 * ((oCliente.PercepcionIIBB / 100));
	var TotalOT2 = NetoOT2 + IvaOT2 + PercIIBBOT2;
	
	var TotalInteres = TotalOT2 * (1 + oPlanCuota.Interes / 100) * oPlanCuota.Coeficiente;
	var InteresOT = (TotalInteres - TotalOT2);
	var InteresOTNeto = InteresOT / 1.21;
	
	NetoOT2 += InteresOTNeto;
	
	IvaOT2 = NetoOT2 * 0.21;
	PercIIBBOT2 = NetoOT2 * ((oCliente.PercepcionIIBB / 100));
	TotalOT2 = NetoOT2 + IvaOT2 + PercIIBBOT2;
	
	$j('#lblNeto').html('$' + NetoOT2.toFixed(2));
	$j('#lblIva').html('$' + IvaOT2.toFixed(2));
	$j('#lblPercIIBB').html('$' + PercIIBBOT2.toFixed(2));
	$j('#lblTotal').html('$' + TotalOT2.toFixed(2));
	$j('#lblInteres').html('$' + InteresOT.toFixed(2));
	
}

$j(document).ready(function() {
	<?php
		if ($IdCliente) {
		?>
			FilterCliente(<?= $IdCliente ?>, '');
		<?php
		}
		if ($IdComprobante) {
		?>	
			SetNumeroComprobante(<?= $IdComprobante ?>, <?= $NumeroComprobante ?>);
		<?php
		}
		
		if ($IdFormaPago)
		{
		?>
			LoadPlanesCuotas('IdPlanCuota', '<?= $IdFormaPago ?>', '<?= $IdPlanCuota ?>');
		<?php
		}
		?>
	
	$j('#IdFormaPago').change(function() {
		var value = $j(this).val();
		LoadPlanesCuotas('IdPlanCuota', value, '');
		ActualizarFacturacion();
	});
	
	$j('#IdPlanCuota').change(function() {
		ActualizarFacturacion();
	});
	
	$j('#Descuento').bind('input', function() {
		ActualizarFacturacion();
	});
	
	$j('#Importe').bind('input', function() {
		if ($j(this).val() != '')
			ActualizarFacturacion();
	});
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de la orden de trabajo N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?> - Agregar Franquicia</span></td>
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
                    <input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
					<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?= $IdComprobante ?>" />
                 	
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
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Cliente:</div></td>
																			<td><div id="margen" align="left">Id.</div></td>
																		</tr>
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
																			<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																		</tr>
																	</table><br><label id="cuit-cliente"></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un cliente</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
																<td colspan="2"><div id="margen" align="left">Descripci&oacute;n:</div></td>
															</tr>
															<tr>
																<td colspan="2">
																	<div align="left">
																		<input type="text" name="Descripcion" id="TitDescripcionulo" class="camporFormularioSimple"  value="<?=$Descripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese una descripci&oacute;n</li><?php } ?></td>
                                                            </tr>
															 
															<tr>
																<td colspan="2"><div id="margen" align="left">Importe:</div></td>
															</tr>
															<tr>
																<td colspan="2">
																	<div align="left">
																		<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?=$Importe?>" autocomplete="off" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
                                                            <tr class="tr_costoFijo">
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese un importe</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left"><label id="lblTipoFactura"></label>:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioMediano" maxlength="8" value="<?=$oComprobante->Numero?>" autocomplete="off" />
																					<script language="javascript">
																						arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
																						SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
																					</script>
																				</div>
																			</td>
																			<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																		</tr>
																	</table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese la factura.</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Forma de Pago:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<select name="IdFormaPago" id="IdFormaPago" class="camporFormularioMediano">
																					<?php
																					foreach ($arrFormasPago as $oFormaPago)
																					{
																						$selected = '';
																						if ($oFormaPago->IdFormaPago == $IdFormaPago)
																							$selected = 'selected="selected"';
																					?>
																						<option value="<?= $oFormaPago->IdFormaPago ?>" <?= $selected ?>><?= $oFormaPago->Nombre ?></option>
																					<?php
																					}
																					?>
																					</select>
																				</div>
																			</td>
																			<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																		</tr>
																	</table>
                                                                </td>
                                                            </tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Cuotas:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<select name="IdPlanCuota" id="IdPlanCuota" class="camporFormularioMediano">
																					</select>
																				</div>
																			</td>
																			<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																		</tr>
																	</table>
                                                                </td>
                                                            </tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															<tr>
																<td colspan="2">
																	<table width="100%" align="center" border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td width="50%" align="right"><div id="margen"><strong>Neto:&nbsp;</strong></td>
																			<td width="50%"><label id="lblNeto"></label></div></td>
																		</tr>
																		<tr>
																			<td colspan="2" height="5">&nbsp;</td>
																		</tr>
																		<tr>
																			<td align="right"><div id="margen"><strong>IVA:&nbsp;</strong></td>
																			<td><label id="lblIva"></label></div></td>
																		</tr>
																		<tr>
																			<td colspan="2" height="5">&nbsp;</td>
																		</tr>
																		<tr>
																			<td align="right"><div id="margen"><strong>Perc. IIBB:&nbsp;</strong></td>
																			<td><label id="lblPercIIBB"></label></div></td>
																		</tr>
																		<tr>
																			<td colspan="2" height="5">&nbsp;</td>
																		</tr>
																		<tr>
																			<td align="right"><div id="margen"><strong>Total:&nbsp;</strong></td>
																			<td><label id="lblTotal"></label></div></td>
																		</tr>
																		<tr>
																			<td colspan="2" height="5">&nbsp;</td>
																		</tr>
																		<tr>
																			<td align="right"><div id="margen"><strong>Inter&eacute;s:&nbsp;</strong></td>
																			<td><label id="lblInteres"></label></div></td>
																		</tr>
																	</table>
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
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_facturacion.php<?=$strParams?>';" value="Cancelar" />
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

<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>

</body>
</html>