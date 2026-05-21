<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */

$arrIdUnidad				= $_REQUEST['IdUnidad'];
$arrNumeroVin				= $_REQUEST['NumeroVin'];
$FechaFacturaCompra			= strval($_REQUEST['FechaFacturaCompra']);
$NumeroFacturaCompra		= strval($_REQUEST['NumeroFacturaCompra']);
$ImporteCompraNeto			= $_REQUEST['ImporteCompraNeto_V'];
$Iva10						= $_REQUEST['Iva10_V'];
$Iva21						= $_REQUEST['Iva21_V'];
$PercepcionIVA				= $_REQUEST['PercepcionIVA_V'];
$PercepcionIB				= $_REQUEST['PercepcionIB_V'];
$PercepcionGanancias		= $_REQUEST['PercepcionGanancias_V'];
$NoGrabado					= $_REQUEST['NoGrabado_V'];
$ImpuestoInterno			= $_REQUEST['ImpuestoInterno_V'];
$ImpuestoInternoD			= $_REQUEST['ImpuestoInternoD_V'];
$ImporteCompraBruto			= $_REQUEST['ImporteCompraBruto_V'];
$ImporteNotaCredito			= $_REQUEST['ImporteNotaCredito_V'];
$Submit						= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err				= 0;
$oUnidad 					= new Unidad();
$oUnidades					= new Unidades();
$oEstadosUnidad				= new EstadosUnidad();
$oModelos					= new Modelos();
$oUbicaciones				= new Ubicaciones();
$oMinutasEspera				= new MinutasEspera();
$oFacturasCompras 			= new FacturasCompras();
$oFacturasComprasUnidades 	= new FacturasComprasUnidades();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($FechaFacturaCompra == '')
		$err |= 1;
	
	$brutoCalculado = $ImporteCompraNeto + $Iva10 + $Iva21 + $PercepcionIVA + $PercepcionIB + $PercepcionGanancias + $NoGrabado + $ImpuestoInterno + $ImpuestoInternoD - $ImporteNotaCredito;
	
	if (abs($ImporteCompraBruto - $brutoCalculado) > 0.001)
		$err |= 4096;
	
	if ($err == 0)
	{
			$ImporteCompraNeto	= str_replace(",", ".", $ImporteCompraNeto);
			$Iva10				= str_replace(",", ".", $Iva10);
			$Iva21				= str_replace(",", ".", $Iva21);
			$PercepcionIVA		= str_replace(",", ".", $PercepcionIVA);
			$PercepcionIB		= str_replace(",", ".", $PercepcionIB);
			$PercepcionGanancias= str_replace(",", ".", $PercepcionGanancias);
			$NoGrabado			= str_replace(",", ".", $NoGrabado);
			$ImpuestoInterno	= str_replace(",", ".", $ImpuestoInterno);
			$ImpuestoInternoD	= str_replace(",", ".", $ImpuestoInternoD);
			$ImporteCompraBruto	= str_replace(",", ".", $ImporteCompraBruto);
			$ImporteNotaCredito	= str_replace(",", ".", $ImporteNotaCredito);
			
			$create = false;
			if (!$oFacturaCompra = $oFacturasCompras->GetByIdUnidad($IdUnidad))
			{
				$create = true;
				$oFacturaCompra = new FacturaCompra();
			}
			
			$oFacturaCompra->IdComprobanteTipo		= ComprobanteTipos::FacturaA;
			$oFacturaCompra->Numero					= $NumeroFacturaCompra;
			$oFacturaCompra->Fecha					= $FechaFacturaCompra;
			$oFacturaCompra->IdProveedor			= 108;
			$oFacturaCompra->Cuit					= '30-63916908-8';
			$oFacturaCompra->ImporteNeto			= $ImporteCompraNeto;
			$oFacturaCompra->Iva10					= $Iva10;
			$oFacturaCompra->Iva21					= $Iva21;
			$oFacturaCompra->Iva27					= 0;
			$oFacturaCompra->PercepcionIva			= $PercepcionIVA;
			$oFacturaCompra->PercepcionIB			= $PercepcionIB;
			$oFacturaCompra->PercepcionGanancias	= $PercepcionGanancias;
			$oFacturaCompra->NoGrabados				= $NoGrabado;
			$oFacturaCompra->ImpuestoInterno		= $ImpuestoInterno;
			$oFacturaCompra->ImpuestoInternoD		= $ImpuestoInternoD;
			$oFacturaCompra->IdConcepto				= 18;
			$oFacturaCompra->Total					= $ImporteCompraBruto;
			
			if ($create)
				$oFacturasCompras->Create($oFacturaCompra);
			else
			{
				$oFacturasCompras->Update($oFacturaCompra);
				$oFacturasComprasUnidades->GetIdFacturaCompra($oFacturaCompra->IdFacturaCompra);
			}
		
	
			for ($i = 0; $i < count($arrIdUnidad); $i++)
			{
				$IdUnidad				= intval($arrIdUnidad[$i]);
				$NumeroVin				= strval($arrNumeroVin[$i]);
					
				/* si no hay errores... */
				if ($err == 0)
				{	
					$oUnidad = $oUnidades->GetById($IdUnidad);

					$oUnidad->FechaFacturaCompra	= $FechaFacturaCompra;
					$oUnidad->NumeroFacturaCompra	= $NumeroFacturaCompra;
					/*$oUnidad->ImporteCompraNeto		= $ImporteCompraNeto;
					$oUnidad->Iva10					= $Iva10;
					$oUnidad->Iva21					= $Iva21;
					$oUnidad->PercepcionIVA			= $PercepcionIVA;
					$oUnidad->PercepcionIB			= $PercepcionIB;
					$oUnidad->PercepcionGanancias	= $PercepcionGanancias;
					$oUnidad->NoGrabado				= $NoGrabado;
					$oUnidad->ImpuestoInterno		= $ImpuestoInterno;
					$oUnidad->ImpuestoInternoD		= $ImpuestoInternoD;
					$oUnidad->ImporteCompraBruto	= $ImporteCompraBruto;
					$oUnidad->ImporteNotaCredito	= $ImporteNotaCredito;*/
					
					$oUnidad = $oUnidades->Update($oUnidad);
					
					$oFacturaCompraUnidad = new FacturaCompraUnidad();
					$oFacturaCompraUnidad->IdUnidad = $oUnidad->IdUnidad;
					$oFacturaCompraUnidad->IdFacturaCompra = $oFacturaCompra->IdFacturaCompra;
					
					$oFacturasComprasUnidades->Create($oFacturaCompraUnidad);
				}
			}
	header('Location: facturascompras.php');
	exit();
	}
}
else
{
	/* determinamos como fecha de compra a la fecha de ayer */
	$FechaFacturaCompra = date("Y-m-d");
	$FechaFacturaCompra = CambiarFecha($FechaFacturaCompra);
	
	$ImporteCompraNeto	= 0;
	$Iva10				= 0;
	$Iva21				= 0;
	$PercepcionIVA		= 0;
	$PercepcionIB		= 0;
	$PercepcionGanancias= 0;
	$NoGrabado			= 0;
	$ImpuestoInterno 	= 0;
	$ImpuestoInternoD	= 0;
	$ImporteCompraBruto	= 0;
	$ImporteNotaCredito	= 0;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

var count = 0;

function FilterNumeroChasis(IdUnidad, NumeroChasis)
{
	if (NumeroChasis == '')
	{
		Get('IdUnidad_V').value 		= '';
	}
	
	var oUnidad = GetUnidadByNumeroChasis(NumeroChasis);
	Get('NumeroInterno').innerHTML = '';
	if (!(oUnidad))
		return;
	var oModelo = GetModelo(oUnidad.IdModelo);
	if (!(oModelo))
		return;
	
	if (oUnidad.NumeroFacturaCompra != '')
	{
		Get('NumeroVin_V').value 		= oUnidad.NumeroChasis;
		Get('IdUnidad_V').value 		= oUnidad.IdUnidad;
		Get('NumeroInterno').innerHTML = 'Numero de Interno: ' + oUnidad.IdUnidad + ' - Modelo: ' + oModelo.DenominacionComercial;
		Get('NumeroFacturaCompra_V').value = oUnidad.NumeroFacturaCompra;
		Get('ImporteCompraBruto_V').value = oUnidad.ImporteCompraBruto;
		Get('ImporteCompraNeto_V').value = oUnidad.ImporteCompraNeto;
		Get('Iva10_V').value = oUnidad.Iva10;
		Get('Iva21_V').value = oUnidad.Iva21;
		Get('PercepcionIVA_V').value = oUnidad.PercepcionIVA;
		Get('PercepcionIB_V').value = oUnidad.PercepcionIB;
		Get('PercepcionGanancias_V').value = oUnidad.PercepcionGanancias;
		Get('ImpuestoInterno_V').value = oUnidad.ImpuestoInterno;
		Get('ImpuestoInternoD_V').value = oUnidad.ImpuestoInternoD;
	}
	else
	{
		Get('NumeroVin_V').value 		= oUnidad.NumeroVin;
		Get('IdUnidad_V').value 		= oUnidad.IdUnidad;
		Get('NumeroInterno').innerHTML = 'Numero de Interno: ' + oUnidad.IdUnidad + ' - Modelo: ' + oModelo.DenominacionComercial;
	}
}

function CalcularBruto()
{
	var ImporteCompraNeto	= parseFloat($j('#ImporteCompraNeto_V').val());
	if (!ImporteCompraNeto)
		ImporteCompraNeto = 0;
	var Iva10 				= parseFloat($j('#Iva10_V').val());
	if (!Iva10)
		Iva10 = 0;
	var Iva21 				= parseFloat($j('#Iva21_V').val());
	if (!Iva21)
		Iva21 = 0;
	var PercepcionIVA 		= parseFloat($j('#PercepcionIVA_V').val());
	if (!PercepcionIVA)
		PercepcionIVA = 0;
	var PercepcionIB		= parseFloat($j('#PercepcionIB_V').val());
	if (!PercepcionIB)
		PercepcionIB = 0;
	var PercepcionGanancias	= parseFloat($j('#PercepcionGanancias_V').val());
	if (!PercepcionGanancias)
		PercepcionGanancias = 0;
	var NoGrabado			= parseFloat($j('#NoGrabado_V').val());
	if (!NoGrabado)
		NoGrabado = 0;
	var ImpuestoInterno		= parseFloat($j('#ImpuestoInterno_V').val());
	if (!ImpuestoInterno)
		ImpuestoInterno = 0;
	var ImpuestoInternoD	= parseFloat($j('#ImpuestoInternoD_V').val());
	if (!ImpuestoInternoD)
		ImpuestoInternoD = 0;
	var ImporteCompraBruto	= parseFloat($j('#ImporteCompraBruto_V').val());
	if (!ImporteCompraBruto)
		ImporteCompraBruto = 0;
		
	var ImporteNotaCredito	= parseFloat($j('#ImporteNotaCredito_V').val());
	if (!ImporteNotaCredito)
		ImporteNotaCredito = 0;
	
	var brutoCalculado = ImporteCompraNeto + Iva10 + Iva21 + PercepcionIVA + PercepcionIB + PercepcionGanancias + NoGrabado + ImpuestoInterno + ImpuestoInternoD - ImporteNotaCredito;
	return brutoCalculado;
}

function ValidarCuentas()
{	
	var brutoCalculado = CalcularBruto();
	
	var ImporteCompraBruto	= parseFloat($j('#ImporteCompraBruto_V').val());
	if (!ImporteCompraBruto)
		ImporteCompraBruto = 0;
	
	return (Math.abs(brutoCalculado - ImporteCompraBruto) < 0.0000001);
}

function QuitarFactura(id) {
	$j('#row_' + id).remove();
}

function AgregarFactura() {
	count++;
	
	var IdUnidad = $j('#IdUnidad_V').val();
	$j('#IdUnidad_V').val('');
	var NumeroVin = $j('#NumeroVin_V').val();
	$j('#NumeroVin_V').val('');
	var oUnidad = GetUnidad(IdUnidad);
	if (!(oUnidad))
		return;
	var oModelo = GetModelo(oUnidad.IdModelo);
	if (!(oModelo))
		return;
	
	
	var html = '<tr id="row_' + count + '" onMouseOver="bgColor=\'#f3f3f3\'" onMouseOut="bgColor=\'\'">';
	
	html += '	<td height="25">';
	html += '	<div id="margen" align="center">' + NumeroVin;
	html += '		<input type="hidden" id="NumeroVin[]" name="NumeroVin[]" value="' + NumeroVin + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + IdUnidad;
	html += '		<input type="hidden" id="IdUnidad[]" name="IdUnidad[]" value="' + IdUnidad + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + oModelo.DenominacionComercial;
	html += '	</div>';
	html += '	</td>';
	html += '	<td><a href="javascript: QuitarFactura(' + count + ')"><img src="images/iconos/del.gif" /></a></td>';
	html += '<tr><td colspan="10"><div align="center"><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td height="1" background="images/linea_punteada.gif"><div align="center"></div></td></tr></table></div></td></tr>';

	$j('#facturas-unidades').append(html);
}

$j(document).ready(function() {
	$j('#btnAgregar').click(function() {
		if ($j('#IdUnidad_V').val() != '' && $j('#NumeroVin_V').val() != '')
		{
			$j('#mensaje-error-unidad').hide();
			if ($j('#NumeroFacturaCompra_V').val() != '')
			{
				$j('#mensaje-error-factura').hide();
				if (ValidarCuentas())
				{
					$j('#mensaje-error-calculo').hide();
					AgregarFactura();
				}
				else
				{
					$j('#mensaje-error-calculo').show();
				}
			}
			else
			{
				$j('#mensaje-error-factura').show();
			}
		}
		else
		{
			$j('#mensaje-error-unidad').show();
		}
	});
	
	$j('.calcular').on('input', function() {
		var brutoCalculado = CalcularBruto();
		$j('#ImporteCompraBruto_V').val(brutoCalculado.toFixed(2));
	});
	
	$j(".calcular").keydown(function(e) {
        var key = e.which;

		// backspace, tab, left arrow, up arrow, right arrow, down arrow, delete, numpad decimal pt, period, enter
		if (key != 8 && key != 9 && key != 37 && key != 38 && key != 39 && key != 40 && key != 46 && key != 110 && key != 190 && key != 13){
			if (key < 48){
				e.preventDefault();
			}
			else if (key > 57 && key < 96){
				e.preventDefault();
			}
			else if (key > 105) {
				e.preventDefault();
			}
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de de unidades - Agregar Lote de Facturas</span></td>
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
					<input type="hidden" name="IdUnidad_V" id="IdUnidad_V" value="<?=$IdUnidad?>" />
                    
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
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
																	<td><div align="right">Fecha de Factura:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaFacturaCompra" type="text" class="camporFormularioMediano" id="FechaFacturaCompra" value="<?=$FechaFacturaCompra?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaFacturaCompra'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
																	<td>&nbsp;</td>
																</tr>
																<tr>
																	<td><div align="right">Nro. Factura:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="NumeroFacturaCompra" type="text" class="camporFormularioMediano" id="NumeroFacturaCompra" value="<?=$NumeroFacturaCompra?>" size="13" maxlength="13" />
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
																	<td>&nbsp;</td>
																	<td  height="20"><li id="mensaje-error-factura" style="color:#FF0000; display: none">Debe ingresar un n&uacute;mero factura.</li></td>
																</tr>
																<tr>
																	<td><div align="right">N&deg; Vin:</div></td>
                                                                    <td>
                                                                        <div align="left">
																			<input type="text" name="NumeroVin_V" id="NumeroVin_V" class="camporFormularioMediano" maxlength="10" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" />
																			<input type="button" name="btnAgregar" class="botonBasico" id="btnAgregar" value="Agregar" />
																	
																			<script language="">
																				SUGGESTRequest('Unidades', 'GetAll', 'NumeroVin_V', 'FilterNumeroChasis', 'IdUnidad', 'NumeroChasis', 'FilterNumeroChasis', null);
																			</script>
																		</div>
                                                                    </td>
                                                                </tr>
																<tr>
																	<td>&nbsp;</td>
																	<td  height="20"><label id="NumeroInterno"></label><li id="mensaje-error-unidad" style="color:#FF0000; display: none">Debe ingresar el n&uacute;mero de Vin.</li></td>
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
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
                                                                    <td><div align="right">Importe Neto:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImporteCompraNeto_V" id="ImporteCompraNeto_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$ImporteCompraNeto?>" />
                                                                    </td>
                                                                    <td><div align="right">IVA 10,5%:</div></td>
                                                                    <td>
                                                                        <input type="text" name="Iva10_V" id="Iva10_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$Iva10?>" />
                                                                    </td>
                                                                    <td><div align="right">IVA 21%:</div></td>
                                                                    <td>
                                                                        <input type="text" name="Iva21_V" id="Iva21_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$Iva21?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Percepci&oacute;n IVA:</div></td>
                                                                    <td>
                                                                        <input type="text" name="PercepcionIVA_V" id="PercepcionIVA_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$PercepcionIVA?>" />
                                                                    </td>
                                                                    <td><div align="right">Percepci&oacute;n IIBB:</div></td>
                                                                    <td>
                                                                        <input type="text" name="PercepcionIB_V" id="PercepcionIB_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$PercepcionIB?>" />
                                                                    </td>
                                                                    <td><div align="right">Percepci&oacute;n Ganancias:</div></td>
                                                                    <td>
                                                                        <input type="text" name="PercepcionGanancias_V" id="PercepcionGanancias_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$PercepcionGanancias?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Imp. Interno:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImpuestoInterno_V" id="ImpuestoInterno_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$ImpuestoInterno?>" />
                                                                    </td>
                                                                    <td><div align="right">Imp. Interno D.:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImpuestoInternoD_V" id="ImpuestoInternoD_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$ImpuestoInternoD?>" />
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">No Grabado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="NoGrabado_V" id="NoGrabado_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$NoGrabado?>" />
                                                                    </td>
                                                                    <td><div align="right">Importe Bruto:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImporteCompraBruto_V" id="ImporteCompraBruto_V" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraBruto?>" />
                                                                    </td>
                                                                    <td><div align="right">Importe Nota Cto.:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImporteNotaCredito_V" id="ImporteNotaCredito_V" class="camporFormularioChico calcular" maxlength="128" value="<?=$ImporteNotaCredito?>" />
                                                                    </td>
                                                                </tr>
																<tr>
																	<td colspan="6" height="20"><li id="mensaje-error-calculo" style="color:#FF0000; display: none">Los importes ingresados son inconsistentes.</li></td>
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
									<tr>
										<td>
											<table id="facturas-unidades" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
												<tr class="bordeGrisFondo">
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>N&deg; Vin</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>N&deg; Int.</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Modelo</strong></div></td>
													<td>&nbsp;</td>
												</tr>
											</table>
										</td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturascompras.php<?=$strParams?>';" value="Cancelar" />
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