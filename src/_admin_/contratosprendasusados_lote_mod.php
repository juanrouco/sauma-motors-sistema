<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CPREUS_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */

$IdContratoPrenda			= $_REQUEST['IdContratoPrenda'];
$IdMinuta					= intval($_REQUEST['IdMinuta']);
$NumeroContrato				= strval($_REQUEST['NumeroContrato']);
$MontoSolicitado			= floatval($_REQUEST['MontoSolicitado']);
$GastoOtorgamiento			= floatval($_REQUEST['GastoOtorgamiento']);
$CostoOtorgamiento			= floatval($_REQUEST['CostoOtorgamiento']);
$Comision					= floatval($_REQUEST['Comision']);
$MontoAcreditado			= floatval($_REQUEST['MontoAcreditado']);
$MontoOtorgado				= floatval($_REQUEST['MontoOtorgado']);
$FechaLiquidacion			= strval($_REQUEST['FechaLiquidacion']);
$IdAcreedor					= intval($_REQUEST['IdAcreedor']);
$Submit						= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err						= 0;
$oMinutasUsados 			= new MinutasUsados();
$oContratosPrendasUsados	= new ContratosPrendasUsados();
$oAcreedores		= new Acreedores();
/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];


if (!$oContratoPrenda = $oContratosPrendasUsados->GetById($IdContratoPrenda))
{
	header('Location: contratosprendasusados.php');
	exit;
}

if ($Submit)
{
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
			
		$oContratosPrendasUsados->Update($oContratoPrenda);
	}
	
	header('Location: contratosprendasusados.php');
	exit();
}
else
{
	/* determinamos como fecha de compra a la fecha de ayer */
	$IdMinuta 			= $oContratoPrenda->IdMinuta;
	$NumeroContrato		= $oContratoPrenda->NumeroContrato;
	$FechaLiquidacion	= CambiarFecha($oContratoPrenda->FechaLiquidacion);
	$MontoSolicitado	= $oContratoPrenda->MontoSolicitado;
	$GastoOtorgamiento	= $oContratoPrenda->GastoOtorgamiento;
	$CostoOtorgamiento	= $oContratoPrenda->CostoOtorgamiento;
	$Comision			= $oContratoPrenda->Comision;
	$MontoAcreditado	= $oContratoPrenda->MontoAcreditado;
	$Resultado			= $oContratoPrenda->Resultado;
	$MontoOtorgado		= $oContratoPrenda->MontoOtorgado;
	$IdAcreedor			= $oContratoPrenda->IdAcreedor;
}

$arrAcreedores = $oAcreedores->GetAll();
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
	Get('IdMinuta').value 		= '';
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
	
	Get('MontoSolicitado').value = oMinuta.FinanciacionCapital;
	Get('GastoOtorgamiento').value = oMinuta.GastosOtorgamiento;
	
	var oCliente = GetCliente(oMinuta.IdCliente);
	var oUsuario = GetUsuario(oMinuta.IdUsuario);
	
	Get('IdMinuta').value 		= oMinuta.IdMinuta;
	Get('NumeroCarpeta').innerHTML = 'Numero de Carpeta: ' + oMinuta.IdMinuta;
	Get('Cliente').innerHTML = 'Cliente: ' + oCliente.RazonSocial + ' - Vendedor: ' + oUsuario.Nombre + ' ' + oUsuario.Apellido;
}

function ValidarCuentas()
{	
	var brutoCalculado = CalcularBruto();
	
	var ImporteCompraBruto	= parseFloat($j('#ImporteCompraBruto').val());
	if (!ImporteCompraBruto)
		ImporteCompraBruto = 0;
	
	return (Math.abs(brutoCalculado - ImporteCompraBruto) < 0.0000001);
}

$j(document).ready(function() {
	SetMinuta('<?= $IdMinuta ?>');
	$j('#btnAceptar').click(function() {
		if ($j('#IdMinuta').val() != '' && $j('#IdMinuta').val() != '')
		{
			$j('#mensaje-error-minuta').hide();
			if ($j('#NumeroContrato').val() != '')
			{
				$j('#mensaje-error-contrato').hide();
				if ($j('#FechaLiquidacion').val() != '')
				{
					$j('#mensaje-error-fecha').hide();
					if ($j('#IdAcreedor').val() != '')
					{
						$j('#mensaje-error-acreedor').hide();
						$j('#frmData').submit();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Contratos de Prenda de Usados - Modificar Contrato</span></td>
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
					<input type="hidden" name="IdContratoPrenda" id="IdContratoPrenda" value="<?=$IdContratoPrenda?>" />
                    
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
																			<input type="text" name="NumeroCarpeta" id="NumeroCarpeta" class="camporFormularioChicoDisabled" maxlength="10" readonly="readonly" value="<?=$IdMinuta?>" onblur="javascript: SetMinuta(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
																			<input type="hidden" name="IdMinuta" id="IdMinuta" value="" />
																			
                                                                        </div>
                                                                    </td>
																	
                                                                </tr>
																<tr>
																	<td colspan="2"><label id="Cliente"></label></td>
																</tr>
																<tr>
																	<td colspan="2" height="20"><label id="NumeroCarpeta"></label><li id="mensaje-error-minuta" style="color:#FF0000; display: none">Debe ingresar el n&uacute;mero de Carpeta existente.</li></td>
																</tr><tr>
																	<td><div align="right">Acreedor:</div></td>
                                                                    <td>
                                                                        <div align="left">
																			<select name="IdAcreedor" id="IdAcreedor" class="camporFormularioSimple">
																				<option value="">Seleccione el Acreedor</option>
																				<?php
																				foreach ($arrAcreedores as $oAcreedor)
																				{
																					$selected = '';
																					if ($oAcreedor->IdAcreedor == $IdAcreedor)
																						$selected = 'selected="selected"';
																				?>
																				<option value="<?= $oAcreedor->IdAcreedor ?>" <?= $selected ?>><?= $oAcreedor->RazonSocial ?></option>
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
                                                                            <input name="FechaLiquidacion" type="text" class="camporFormularioMediano" id="FechaLiquidacion" value="<?=$FechaLiquidacion?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaLiquidacion'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Nro. Contrato:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="NumeroContrato" id="NumeroContrato" class="camporFormularioMediano" maxlength="15" value="<?=$NumeroContrato?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                        <input type="text" name="MontoSolicitado" id="MontoSolicitado" class="camporFormularioChico" maxlength="128" value="<?=$MontoSolicitado?>" readonly="true" />
                                                                    </td>
                                                                    <td><div align="right">Gasto Prendario:</div></td>
                                                                    <td>
                                                                        <input type="text" name="GastoOtorgamiento" id="GastoOtorgamiento" class="camporFormularioChico" maxlength="128" value="<?=$GastoOtorgamiento?>" readonly="true" />
                                                                    </td>
                                                                    <td><div align="right">Costo Subsidio:</div></td>
                                                                    <td>
                                                                        <input type="text" name="CostoOtorgamiento" id="CostoOtorgamiento" class="camporFormularioChico" maxlength="128" value="<?=$CostoOtorgamiento?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Comisi&oacute;n:</div></td>
                                                                    <td>
                                                                        <input type="text" name="Comision" id="Comision" class="camporFormularioChico" maxlength="128" value="<?=$Comision?>" />
                                                                    </td>
                                                                    <td><div align="right">Monto Otorgado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="MontoOtorgado" id="MontoOtorgado" class="camporFormularioChico" maxlength="128" value="<?=$MontoOtorgado?>" />
                                                                    </td>
                                                                    <td><div align="right">Monto Acreditado:</div></td>
                                                                    <td>
                                                                        <input type="text" name="MontoAcreditado" id="MontoAcreditado" class="camporFormularioChico" maxlength="128" value="<?=$MontoAcreditado?>" />
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
									<input type="button" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
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