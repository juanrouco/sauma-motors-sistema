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
$NumeroComprobante		= strval($_REQUEST['NumeroComprobante']);
$IdFormaPago			= FormaPago::Efectivo;
$IdPlanCuota			= 1;
$Descuento				= floatval($_REQUEST['Descuento']);
$Comentarios			= strval($_REQUEST['Comentarios']);
$arrIdTipoPago			= $_REQUEST['IdTipoPago'];
$arrImporte				= $_REQUEST['Importe'];
$Submit					= (isset($_REQUEST['Submitted']));

$err						= 0;
$oOrdenTrabajoFactura 		= new OrdenTrabajoFactura();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades			= new TallerUnidades();
$oClientes					= new Clientes();
$oComprobantes				= new Comprobantes();
$oFormasPago				= new FormasPago();
$oPlanesCuotas				= new PlanesCuotas();
$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
$oFacturasPostVentas		= new FacturasPostVentas();
$oTiposIva					= new TiposIva();
$oPagos						= new Pagos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$arrFranquicias = $oOrdenesTrabajoFranquicias->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

$TotalFranquicia = 0;
if ($arrFranquicias)
{
	foreach ($arrFranquicias as $oFranquicia)
	{
		$oComprobanteF = $oComprobantes->GetById($oFranquicia->IdComprobante);
		$oFacturaPostVentaF = $oFacturasPostVentas->GetByIdComprobante($oFranquicia->IdComprobante);
		$arrItems = $oFacturaPostVentaF->GetAllItems();
		$Interes = 0;
		foreach ($arrItems as $oItem)
		{
			if ($oItem->Interes)
				$Interes+= $oItem->ImporteBruto;
		}
		$TotalFranquicia+= $oFranquicia->Importe - $oComprobanteF->PercepcionIIBB - $Interes;
	}
}

$arrFormasPago = $oFormasPago->GetAll();

if ($Submit)
{	
	/*if ($IdComprobante == '' || $IdComprobante == 0)
		$err |= 1;
		
	$oComprobante = $oComprobantes->GetById($IdComprobante);
	
	if ($oComprobante->Numero != $NumeroComprobante)
		$err |= 2;*/
	
	$oFacturaPostVenta;
		
	if ($IdCliente == '' || $IdCliente == 0)
		$err |= 4;
	else
	{	
		$oCliente = $oClientes->GetById($IdCliente);
		
		//if($oCliente)
			//$oClientes->ActualizarPercepciones($oCliente);
		$oFacturaPostVenta = $oOrdenTrabajoFactura->GenerarFacturaSinGuardar($oOrdenTrabajo, $oCliente, $oComprobante, $IdPlanCuota, $Descuento, $Comentarios);
		/*if ($oFacturaPostVenta->ImporteBruto > 999.99)
		{
			if ($oCliente->ObtenerNumeroDocumentoAfip() == '0' || $oCliente->ObtenerNumeroDocumentoAfip() == '')
				$err |= 32;
			if ($IdCliente == Cliente::IdConsumidorFinal)
				$err |= 64;
		}
		elseif ($IdCliente != Cliente::IdConsumidorFinal && ($oCliente->ObtenerNumeroDocumentoAfip() == '0' || $oCliente->ObtenerNumeroDocumentoAfip() == ''))
				$err |= 32;*/
			
	}
		
	if (!$oOrdenTrabajo->GetListoFinalizar())
		$err |= 8;
	
	$TotalIndicado = 0;
	$index = 0;
	foreach ($arrIdTipoPago as $IdTipoPago)
	{
		$ImportePago = floatval(str_replace(',', '.', $arrImporte[TipoPago::GetIndexPV($IdTipoPago)]));
		$TotalIndicado+= $ImportePago;
		$index++;
	}
	
	$totaldesc = $oFacturaPostVenta->ImporteBruto;//;
	//print_r($oFacturaPostVenta->ImporteBruto);
	if (abs($totaldesc - $TotalIndicado) > 0.1)
	{
		$err |= 1;
	}
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oCliente = $oClientes->GetById($IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
		
		$oComprobante	= $oComprobantes->GetNextPrefijo(ComprobanteTipos::FacturaC, str_pad(ConfiguracionFactura::PuntoVentaPV, 4, "0", STR_PAD_LEFT));
		
		$oOrdenTrabajo->IdComprobante = $oComprobante->IdComprobante;
		$oOrdenesTrabajo->Update($oOrdenTrabajo);
		
		$oFacturaPostVenta = $oOrdenTrabajoFactura->GenerarFactura($oOrdenTrabajo, $oCliente, $oComprobante, $IdPlanCuota, $Descuento, $Comentarios);
		
		if (true)
		{
			$oFacturaPostVenta->TotalPago = $oFacturaPostVenta->ImporteBruto;
			$oFacturaPostVenta->FechaPago = date('d-m-Y');
			$oFacturasPostVentas->Update($oFacturaPostVenta);
			
			$index = 0;
			foreach ($arrIdTipoPago as $IdTipoPago)
			{
				if ($IdTipoPago != 99)
				{
					$ImportePago = floatval(str_replace(',', '.', $arrImporte[TipoPago::GetIndexPV($IdTipoPago)]));
					$oPago = new Pago();
					$oPago->Fecha 		= date('d-m-Y');
					$oPago->IdTipoPago	= $IdTipoPago;
					$oPago->Importe		= $ImportePago;
					$oPago->Pago		= 1;
					$oPago->IdFacturaPostVenta	= $oFacturaPostVenta->IdFacturaPostVenta;
					$oPagos->Create($oPago);
				}
				$index++;
			}
		}
		
		$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
		$oComprobante->Fecha = $oFacturaPostVenta->Fecha;
		$oComprobante->IdCliente = $oCliente->IdCliente;
		$oComprobante->Importe = $oFacturaPostVenta->ImporteBruto;
		$oComprobante->ImporteIva21 = $oFacturaPostVenta->Iva21;
		$oComprobante->ImporteIva10 = 0;
	
		$oComprobantes->Update($oComprobante);
		
		header("Location: ordenestrabajo_facturacion.php?IdOrdenTrabajo=" . $IdOrdenTrabajo);
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
	$arrIdTipoPago = array(TipoPago::Efectivo);
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
var TotalOT = <?= $oOrdenTrabajo->ImporteTotalCalculado() ?>;
var IvaOT = <?= $oOrdenTrabajo->ImporteIva() ?>;
var PercIIBBOT = <?= $oOrdenTrabajo->ImportePercepcionIIBB() ?>;
var NetoOT = TotalOT - IvaOT - PercIIBBOT - <?= $TotalFranquicia / 1.21 ?>;

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
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaA?>', '0002');
			
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/
			
		$j('#lblTipoFactura').html('FACTURA A');
	}
	else if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

		/* obtenemos la proxima factura */
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaB?>', '0002');
			
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/
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
	
	
	var multiplicadorInteres = 1;//oPlanCuota.Interes * oPlanCuota.Coeficiente; // / 100;
	var NetoOT2 = NetoOT * IndiceDescuento;
	var IvaOT2 = NetoOT2 * 0.21;
	var PercIIBBOT2 = 0;//NetoOT2 * ((oCliente.PercepcionIIBB / 100));
	var TotalOT2 = NetoOT2 + IvaOT2 + PercIIBBOT2;
	
	var TotalInteres = TotalOT2 * (1 + oPlanCuota.Interes / 100) * oPlanCuota.Coeficiente;
	var InteresOT = (TotalInteres - TotalOT2);
	var InteresOTNeto = InteresOT / 1.21;
	
	//var TotalInteres = TotalOT2 * multiplicadorInteres; //(1 + oPlanCuota.Interes / 100) * oPlanCuota.Coeficiente;
	//var InteresOT = (TotalInteres - TotalOT2);
	//var InteresOTNeto = InteresOT / 1.21;
	
	var InteresOT = TotalOT2 * (multiplicadorInteres - 1);
	NetoOT2 *= multiplicadorInteres;
	IvaOT2 *= multiplicadorInteres;
	PercIIBBOT2 = 0;//*= multiplicadorInteres;
	TotalOT2 *= multiplicadorInteres;
	
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
	
	$j('#btnAceptar').click(function() {
		$j('#frmData').attr('action', '<?= $strParams ?>');
		$j('#frmData').attr('target', '');
	});
	
	$j('#btnPreview').click(function() {
		$j('#frmData').attr('action', 'ordenestrabajo_factura_preview.php');
		$j('#frmData').attr('target', '_blank');
		$j('#frmData').submit();
		
		setTimeout(function(){ $j('#btnAceptar').removeAttr('disabled');; }, 1000);
	});
		
	$j('.cbTipoPago').change(function() {
		var elemId = $j(this).attr('id');
			elemId = elemId.split('_');
		if (this.checked) {
			$j('#Importe_' + elemId[1]).show();
		}
		else {
			$j('#Importe_' + elemId[1]).hide();
		}
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de la orden de trabajo N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?> - Agregar Recibo</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" onsubmit="btnAceptar.disabled = true; return true;">
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
                                                                <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Para facturar debe cargar la tarea realizada en todos los trabajos</li><?php } ?></td>
                                                            </tr>														
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
																					<input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" />
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
																			<td><input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddClienteResumen();" value=" + " /></td>
																			<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																		</tr>
																	</table><br><label id="cuit-cliente"></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                
                                                                <td height="20">
																	<?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un cliente</li><?php } ?>
																	<?php if ($err & 32) { ?><li style="color:#FF0000;">No puede realizar una factura mayor o igual a $1000<br />sin ingresar el CUIL, o CUIT del cliente.</li><?php } ?>
																	<?php if ($err & 64) { ?><li style="color:#FF0000;">No puede realizar una factura mayor o igual a $1000<br />a nombre de consumidor final.</li><?php } ?>
																</td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Descuento:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<input type="text" name="Descuento" id="Descuento" class="camporFormularioChico" value="<?= $Descuento ?>" />%
																				</div>
																			</td>
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
																			<td><div id="margen" align="left">Forma de Pago:<span style="color:#FF0000;">&nbsp;(*)</span></div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																					<?php
																					foreach (TipoPago::GetAllPV() as $oTipoPago)
																					{
																						if ($oTipoPago['IdTipoPago'] == 14) continue;
																					?>
																						<tr>
																							<td height="22">
																								<div id="margen" align="left"><input type="checkbox" class="cbTipoPago" name="IdTipoPago[]" id="IdTipoPago_<?= $oTipoPago['IdTipoPago'] ?>" value="<?= $oTipoPago['IdTipoPago'] ?>" <?= in_array($oTipoPago['IdTipoPago'], $arrIdTipoPago) ? 'checked="checked"' : '' ?> /></div>
																							</td>
																							<td><label for="IdTipoPago_<?= $oTipoPago['IdTipoPago'] ?>"><?= $oTipoPago['Descripcion'] ?></label></td>
																							<td>
																								<div id="margen">
																									<input type="text" class="camporFormularioChico" <?= in_array($oTipoPago['IdTipoPago'], $arrIdTipoPago) ? '' : 'style="display: none"' ?> name="Importe[]" id="Importe_<?= $oTipoPago['IdTipoPago'] ?>" value="<?= $oTipoPago['IdTipoPago'] == 1 ?  $oOrdenTrabajo->ImporteTotalCalculado() - $oOrdenTrabajo->ImportePercepcionIIBB() : 0 ?>" />
																								</div>
																							</td>
																						</tr>
																					<?php
																					}
																					?>
																						<tr>
																							<td height="22">
																								<div id="margen" align="left"><input type="checkbox" class="cbTipoPago" name="IdTipoPago[]" id="IdTipoPago_99" value="99" <?= in_array(99, $arrIdTipoPago) ? 'checked="checked"' : '' ?> /></div>
																							</td>
																							<td><label for="IdTipoPago_99">Cuenta Corriente</label></td>
																							<td>
																								<div id="margen">
																									<input type="text" class="camporFormularioChico" <?= in_array(99, $arrIdTipoPago) ? '' : 'style="display: none"' ?> name="Importe[]" id="Importe_99" value="<?= $oOrdenTrabajo->ImporteTotalCalculado() ?>" />
																								</div>
																							</td>
																						</tr>
																					</table>
																				</div>
																			</td>
																		</tr>
																	</table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">
																	<?php if ($err & 1) { ?><li style="color:#FF0000;">El importe a pagar ingresado debe ser igual al total de la factura</li><?php } ?>
																</td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
																		<tr>
																			<td><div id="margen" align="left">Comentarios:</div></td>
																		</tr>
																		<tr>
																			<td>
																				<div align="left">
																					<textarea name="Comentarios" id="Comentarios" style="height: 75px" class="camporFormularioSimple"><?= $Comentarios ?></textarea>
																				</div>
																			</td>
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
									<input type="button" name="btnPreview" class="botonBasico" id="btnPreview" value="Vista Previa" />
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