<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CPREUS_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */

$arrIdMinuta				= $_REQUEST['IdMinuta'];
$arrNumeroContrato			= $_REQUEST['NumeroContrato'];
$arrMontoSolicitado			= $_REQUEST['MontoSolicitado'];
$arrGastoOtorgamiento		= $_REQUEST['GastoOtorgamiento'];
$arrCostoOtorgamiento		= $_REQUEST['CostoOtorgamiento'];
$arrComision				= $_REQUEST['Comision'];
$arrMontoAcreditado			= $_REQUEST['MontoAcreditado'];
$arrFechaLiquidacion		= $_REQUEST['FechaLiquidacion'];
$arrMontoOtorgado			= $_REQUEST['MontoOtorgado'];
$arrIdAcreedor				= $_REQUEST['IdAcreedor'];
$Submit						= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err						= 0;
$oMinutasUsados 			= new MinutasUsados();
$oContratosPrendasUsados	= new ContratosPrendasUsados();
$oAcreedores				= new Acreedores();
/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

$arrAcreedores = $oAcreedores->GetAll();

if ($Submit)
{
	/* validaciones... */
	
	for ($i = 0; $i < count($arrIdMinuta); $i++)
	{
		$IdMinuta				= intval($arrIdMinuta[$i]);
		$NumeroContrato			= strval($arrNumeroContrato[$i]);
		$FechaLiquidacion		= strval($arrFechaLiquidacion[$i]);
		$MontoSolicitado		= floatval($arrMontoSolicitado[$i]);
		$GastoOtorgamiento		= floatval($arrGastoOtorgamiento[$i]);
		$CostoOtorgamiento		= floatval($arrCostoOtorgamiento[$i]);
		$Comision				= floatval($arrComision[$i]);
		$MontoAcreditado		= floatval($arrMontoAcreditado[$i]);
		$MontoOtorgado			= floatval($arrMontoOtorgado[$i]);
		$IdAcreedor				= intval($arrIdAcreedor[$i]);
		
		
	
		/* si no hay errores... */
		if ($err == 0)
		{
			$MontoSolicitado	= str_replace(",", ".", $MontoSolicitado);
			$GastoOtorgamiento	= str_replace(",", ".", $GastoOtorgamiento);
			$CostoOtorgamiento	= str_replace(",", ".", $CostoOtorgamiento);
			$Comision			= str_replace(",", ".", $Comision);
			$MontoAcreditado	= str_replace(",", ".", $MontoAcreditado);
			$MontoOtorgado		= str_replace(",", ".", $MontoOtorgado);
			
			$Resultado 			= $GastoOtorgamiento - $CostoOtorgamiento + $Comision;
			
			$oMinuta = $oMinutasUsados->GetById($IdMinuta);
			
			$create = false;
			if (!$oContratoPrenda = $oContratosPrendasUsados->GetByIdMinuta($IdMinuta))
			{
				$create = true;
				$oContratoPrenda = new ContratoPrendaUsado();
			}
			
			$oContratoPrenda->IdMinuta			= $oMinuta->IdMinuta;
			$oContratoPrenda->NumeroContrato	= $NumeroContrato;
			$oContratoPrenda->FechaLiquidacion	= $FechaLiquidacion;
			$oContratoPrenda->MontoSolicitado	= $MontoSolicitado;
			$oContratoPrenda->GastoOtorgamiento	= $GastoOtorgamiento;
			$oContratoPrenda->CostoOtorgamiento	= $CostoOtorgamiento;
			$oContratoPrenda->Comision			= $Comision;
			$oContratoPrenda->MontoAcreditado	= $MontoAcreditado;
			$oContratoPrenda->Resultado			= $Resultado;
			$oContratoPrenda->MontoOtorgado		= $MontoOtorgado;
			$oContratoPrenda->IdAcreedor		= $IdAcreedor;
			
			if ($create)
				$oContratosPrendasUsados->Create($oContratoPrenda);
			else
				$oContratosPrendasUsados->Update($oContratoPrenda);
		}
	}
	header('Location: contratosprendasusados.php');
	exit();
}
else
{
	/* determinamos como fecha de compra a la fecha de ayer */
	$FechaLiquidacion = date("Y-m-d");
	$FechaLiquidacion = CambiarFecha($FechaLiquidacion);
	
	$MontoSolicitado	= 0;
	$GastoOtorgamiento	= 0;
	$CostoOtorgamiento	= 0;
	$MontoAcreditado	= 0;
	$Comision			= 0;
	$MontoOtorgado		= 0;
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

function SetMinuta(IdMinuta)
{
	Get('IdMinuta_V').value 		= '';
	var oMinuta = GetMinutaUsado(IdMinuta);
	Get('NumeroCarpeta').innerHTML = '';
	if (!(oMinuta))
	{
		$j('#mensaje-error-minuta').show();
		return;
	}
	else
	{
		$j('#mensaje-error-minuta').hide();
	}
	
	Get('MontoSolicitado_V').value = oMinuta.FinanciacionCapital;
	Get('GastoOtorgamiento_V').value = oMinuta.GastosOtorgamiento;
	
	var oCliente = GetCliente(oMinuta.IdCliente);
	var oUsuario = GetUsuario(oMinuta.IdUsuario);
	
	Get('IdMinuta_V').value 		= oMinuta.IdMinuta;
	Get('NumeroCarpeta').innerHTML = 'Numero de Carpeta: ' + oMinuta.IdMinuta;
	Get('Cliente').innerHTML = 'Cliente: ' + oCliente.RazonSocial + ' - Vendedor: ' + oUsuario.Nombre + ' ' + oUsuario.Apellido;
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
	
	var IdMinuta = $j('#IdMinuta_V').val();
	$j('#IdMinuta_V').val('');
	
	var IdAcreedor = $j('#IdAcreedor_V').val();
	var Acreedor = $j("#IdAcreedor_V option:selected").text();
	$j('#IdAcreedor_V').val('');
	
	var NumeroContrato = $j('#NumeroContrato_V').val();
	$j('#NumeroContrato_V').val('');
	var FechaLiquidacion = $j('#FechaLiquidacion_V').val();
	$j('#FechaLiquidacion_V').val('');
	
	var MontoSolicitado	= parseFloat($j('#MontoSolicitado_V').val());
	$j('#MontoSolicitado_V').val('0');
	var GastoOtorgamiento = parseFloat($j('#GastoOtorgamiento_V').val());
	$j('#GastoOtorgamiento_V').val('0');
	var CostoOtorgamiento 	= parseFloat($j('#CostoOtorgamiento_V').val());
	$j('#CostoOtorgamiento_V').val('0');
	var Comision = parseFloat($j('#Comision_V').val());
	$j('#Comision_V').val('0');
	var MontoAcreditado		= parseFloat($j('#MontoAcreditado_V').val());
	$j('#MontoAcreditado_V').val('0');
	var MontoOtorgado		= parseFloat($j('#MontoOtorgado_V').val());
	$j('#MontoOtorgado_V').val('0');
	
	
	var html = '<tr id="row_' + count + '" onMouseOver="bgColor=\'#f3f3f3\'" onMouseOut="bgColor=\'\'">';
	
	html += '	<td>';
	html += '	<div id="margen" align="center">' + IdMinuta;
	html += '		<input type="hidden" id="IdMinuta[]" name="IdMinuta[]" value="' + IdMinuta + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + NumeroContrato;
	html += '		<input type="hidden" id="NumeroContrato[]" name="NumeroContrato[]" value="' + NumeroContrato + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + Acreedor;
	html += '		<input type="hidden" id="IdAcreedor[]" name="IdAcreedor[]" value="' + IdAcreedor + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + FechaLiquidacion;
	html += '		<input type="hidden" id="FechaLiquidacion[]" name="FechaLiquidacion[]" value="' + FechaLiquidacion + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + MontoSolicitado;
	html += '		<input type="hidden" id="MontoSolicitado[]" name="MontoSolicitado[]" value="' + MontoSolicitado + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">' + GastoOtorgamiento;
	html += '		<input type="hidden" id="GastoOtorgamiento[]" name="GastoOtorgamiento[]" value="' + GastoOtorgamiento + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">';
	html += '		<input type="text" class="camporFormularioChico" id="CostoOtorgamiento[]" name="CostoOtorgamiento[]" value="' + CostoOtorgamiento + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">';
	html += '		<input type="text" class="camporFormularioChico" id="Comision[]" name="Comision[]" value="' + Comision + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">';
	html += '		<input type="text" class="camporFormularioChico" id="MontoOtorgado[]" name="MontoOtorgado[]" value="' + MontoOtorgado + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td>';
	html += '	<div id="margen" align="center">';
	html += '		<input type="text" class="camporFormularioChico" id="MontoAcreditado[]" name="MontoAcreditado[]" value="' + MontoAcreditado + '" />';
	html += '	</div>';
	html += '	</td>';
	html += '	<td><a href="javascript: QuitarFactura(' + count + ')"><img src="images/iconos/del.gif" /></a></td>';
	html += '</tr>';
	html += '<tr><td colspan="9"><div align="center"><table width="100%"  border="0" cellspacing="0" cellpadding="0"><tr><td height="1" background="images/linea_punteada.gif"><div align="center"></div></td></tr></table></div></td></tr>';

	$j('#contratos-prendas').append(html);
}

$j(document).ready(function() {
	$j('#btnAgregar').click(function() {
		if ($j('#IdMinuta_V').val() != '' && $j('#IdMinuta_V').val() != '')
		{
			$j('#mensaje-error-minuta').hide();
			if ($j('#NumeroContrato_V').val() != '')
			{
				$j('#mensaje-error-contrato').hide();
				if ($j('#FechaLiquidacion_V').val() != '')
				{
					$j('#mensaje-error-fecha').hide();
					if ($j('#IdAcreedor_V').val() != '')
					{
						$j('#mensaje-error-acreedor').hide();
						AgregarFactura();
					}
					else
					{
						$j('#mensaje-error-acreedor').show();
					}
				}
				else
				{
					$j('#mensaje-error-fecha').show();
				}
			}
			else
			{
				$j('#mensaje-error-contrato').show();
			}
		}
		else
		{
			$j('#mensaje-error-minuta').show();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Contratos de Prenda de Usados - Agregar Lote de Contratos</span></td>
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
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
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
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
																	<td><div align="right">N&deg; Carpeta:</div></td>
                                                                    <td>
                                                                        <div align="left">
																			<input type="text" name="NumeroCarpeta_V" id="NumeroCarpeta_V" class="camporFormularioChico" maxlength="10" value="<?=$NumeroVin?>" onblur="javascript: SetMinuta(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
																			<input type="hidden" name="IdMinuta_V" id="IdMinuta_V" value="" />
																			
                                                                        </div>
                                                                    </td>
																	
                                                                </tr>
																<tr>
																	<td colspan="2"><label id="Cliente"></label></td>
																</tr>
																<tr>
																	<td colspan="2" height="20"><label id="NumeroCarpeta"></label><li id="mensaje-error-minuta" style="color:#FF0000; display: none">Debe ingresar el n&uacute;mero de Carpeta existente.</li></td>
																</tr>
																<tr>
																	<td><div align="right">Acreedor:</div></td>
                                                                    <td>
                                                                        <div align="left">
																			<select name="IdAcreedor_V" id="IdAcreedor_V" class="camporFormularioSimple">
																				<option value="">Seleccione el Acreedor</option>
																				<?php
																				foreach ($arrAcreedores as $oAcreedor)
																				{
																				?>
																				<option value="<?= $oAcreedor->IdAcreedor ?>"><?= $oAcreedor->RazonSocial ?></option>
																				<?php
																				}
																				?>
																			</select>
																			
                                                                        </div>
                                                                    </td>
																	
                                                                </tr>
																<tr>
																	<td colspan="2" height="20"><li id="mensaje-error-acreedor" style="color:#FF0000; display: none">Debe seleccionar el acreedor.</li></td>
																</tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>
													<tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
                                                                    <td><div align="right">Fecha de Liquidaci&oacute;n:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaLiquidacion_V" type="text" class="camporFormularioMediano" id="FechaLiquidacion_V" value="<?=$FechaLiquidacion_V?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaLiquidacion_V'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Nro. Contrato:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="NumeroContrato_V" id="NumeroContrato_V" class="camporFormularioMediano" maxlength="15" value="<?=$NumeroFacturaCompra?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
																	<td colspan="2" height="20"><li id="mensaje-error-fecha" style="color:#FF0000; display: none">Debe ingresar la fecha de liquidaci&oacute;n.</li></td>
																	<td width="10">&nbsp;</td>
																	<td colspan="2" height="20"><li id="mensaje-error-contrato" style="color:#FF0000; display: none">Debe ingresar un n&uacute;mero de contrato.</li></td>
																</tr>
                                                            </table>
                                                        </td>
                                                    </tr>
													<tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                    	<td>
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
                                                                    <td><div align="right">Monto Solicitado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="MontoSolicitado_V" id="MontoSolicitado_V" class="camporFormularioChico" maxlength="128" value="<?=$MontoSolicitado?>" readonly="true" />
                                                                    </td>
                                                                    <td><div align="right">Gasto Prendario:</div></td>
                                                                    <td>
                                                                        <input type="text" name="GastoOtorgamiento_V" id="GastoOtorgamiento_V" class="camporFormularioChico" maxlength="128" value="<?=$GastoOtorgamiento?>" readonly="true" />
                                                                    </td>
                                                                    <td><div align="right">Costo Subsidio:</div></td>
                                                                    <td>
                                                                        <input type="text" name="CostoOtorgamiento_V" id="CostoOtorgamiento_V" class="camporFormularioChico" maxlength="128" value="<?=$CostoOtorgamiento?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Comisi&oacute;n:</div></td>
                                                                    <td>
                                                                        <input type="text" name="Comision_V" id="Comision_V" class="camporFormularioChico" maxlength="128" value="<?=$Comision?>" />
                                                                    </td>
                                                                    <td><div align="right">Monto Otorgado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="MontoOtorgado_V" id="MontoOtorgado_V" class="camporFormularioChico" maxlength="128" value="<?=$MontoOtorgado?>" />
                                                                    </td>
                                                                    <td><div align="right">Monto Acreditado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="MontoAcreditado_V" id="MontoAcreditado_V" class="camporFormularioChico" maxlength="128" value="<?=$MontoAcreditado?>" />
                                                                    </td>
                                                                </tr>
																<tr>
																	<td colspan="6" height="20" align="right">
																		<input type="button" name="btnAgregar" class="botonBasico" id="btnAgregar" value="Agregar" />
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
									<tr>
										<td>
											<table id="contratos-prendas" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
												<tr class="bordeGrisFondo">
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>N&deg; Carpeta</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>N&deg; Contrato</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Acreedor</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Fecha Liquid.</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Solicitado</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Gasto Prend.</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Costo Subsidio</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Comisi&oacute;n</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Monto Otorg.</strong></div></td>
													<td height="25" class="bordeGrisTitulo"><div id="margin" align="center"><strong>Acreditado</strong></div></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'contratosprendasusados.php<?=$strParams?>';" value="Cancelar" />
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