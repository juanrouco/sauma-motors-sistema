<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CPREUS_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */

$IdMinuta					= intval($_REQUEST['IdMinuta']);
$NumeroContrato				= strval($_REQUEST['NumeroContrato']);
$MontoSolicitado			= floatval($_REQUEST['MontoSolicitado']);
$GastoOtorgamiento			= floatval($_REQUEST['GastoOtorgamiento']);
$CostoOtorgamiento			= floatval($_REQUEST['CostoOtorgamiento']);
$Comision					= floatval($_REQUEST['Comision']);
$MontoAcreditado			= floatval($_REQUEST['MontoAcreditado']);
$FechaEnvioCarpeta			= strval($_REQUEST['FechaEnvioCarpeta']);
$FechaAprobado				= strval($_REQUEST['FechaAprobado']);
$FechaRechazado				= strval($_REQUEST['FechaRechazado']);
$FechaLiquidacion			= strval($_REQUEST['FechaLiquidacion']);
$FechaObservacion			= strval($_REQUEST['FechaObservacion']);
$Observacion				= strval($_REQUEST['Observacion']);
$MontoOtorgado				= floatval($_REQUEST['MontoOtorgado']);
$IdAcreedor					= intval($_REQUEST['IdAcreedor']);
$CarpetaCompleta			= intval($_REQUEST['CarpetaCompleta']);
$PrePrenda					= intval($_REQUEST['PrePrenda']);
$PrendaInscripta			= intval($_REQUEST['PrendaInscripta']);
$IdEstado					= intval($_REQUEST['IdEstado']);
$FechaGestoria				= strval($_REQUEST['FechaGestoria']);
$FechaEnvioPrenda			= strval($_REQUEST['FechaEnvioPrenda']);
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
		$oContratoPrenda->CarpetaCompleta	= $CarpetaCompleta;
		$oContratoPrenda->PrePrenda			= $PrePrenda;
		$oContratoPrenda->PrendaInscripta	= $PrendaInscripta;
		$oContratoPrenda->FechaEnvioCarpeta	= $FechaEnvioCarpeta;
		$oContratoPrenda->FechaAprobado		= $FechaAprobado;
		$oContratoPrenda->FechaRechazado	= $FechaRechazado;
		$oContratoPrenda->FechaObservacion	= $FechaObservacion;
		$oContratoPrenda->Observacion		= $Observacion;
		$oContratoPrenda->IdEstado			= $IdEstado;
		$oContratoPrenda->FechaGestoria		= $FechaGestoria;
		$oContratoPrenda->FechaEnvioPrenda	= $FechaEnvioPrenda;
			
		if ($create)
			$oContratosPrendasUsados->Create($oContratoPrenda);
		else
			$oContratosPrendasUsados->Update($oContratoPrenda);
			
		header('Location: contratosprendasusados.php');
		exit();
	}

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
	$IdEstado 			= EstadosPrendas::Preaprobado;
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
	Get('Cliente').innerHTML = '<br /><br /><strong>Cliente:</strong> ' + oCliente.RazonSocial + ' - <strong>Vendedor:</strong> ' + oUsuario.Nombre + ' ' + oUsuario.Apellido;
}

function ValidarCuentas()
{	
	var brutoCalculado = CalcularBruto();
	
	var ImporteCompraBruto	= parseFloat($j('#ImporteCompraBruto_V').val());
	if (!ImporteCompraBruto)
		ImporteCompraBruto = 0;
	
	return (Math.abs(brutoCalculado - ImporteCompraBruto) < 0.0000001);
}

$j(document).ready(function() {
	$j('#btnAceptar').click(function() {
		if ($j('#IdMinuta').val() != '' && $j('#IdMinuta').val() != '0' && $j('#IdMinuta').val() != '')
		{
			$j('#mensaje-error-minuta').hide();
			$j('#mensaje-error-fecha').hide();
			if ($j('#IdAcreedor').val() != '')
			{
				$j('#mensaje-error-acreedor').hide();
				return true;
			}
			else
			{
				$j('#mensaje-error-acreedor').show();
			}
		}
		else
		{
			$j('#mensaje-error-minuta').show();
		}
		return false;
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
																			<input type="text" name="NumeroCarpeta" id="NumeroCarpeta" class="camporFormularioChico" maxlength="10" value="<?=$NumeroCarpeta?>" onblur="javascript: SetMinuta(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
																			<input type="hidden" name="IdMinuta" id="IdMinuta" value="<?= $IdMinuta ?>" />
																			
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
																<tr>
																	<td><div align="right">Estado:</div></td>
                                                                    <td>
                                                                        <div align="left">
																			<select name="IdEstado" id="IdEstado" class="camporFormularioSimple">
																				<option value="">Seleccione el estado</option>
																				<?php
																				foreach (EstadosPrendas::GetAll() as $oEstado)
																				{
																					$selected = '';
																					if ($oEstado['IdEstado'] == $IdEstado)
																						$selected = 'selected="selected"';
																				?>
																				<option value="<?= $oEstado['IdEstado'] ?>" <?= $selected ?>><?= $oEstado['Descripcion'] ?></option>
																				<?php
																				}
																				?>
																			</select>
																			
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
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            	<tr>
                                                                    <td><div align="right">Fecha de Env&iacute;o Carpeta:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaEnvioCarpeta" type="text" class="camporFormularioMediano" id="FechaEnvioCarpeta" value="<?=$FechaEnvioCarpeta?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaEnvioCarpeta'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Carpeta Completa:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="checkbox" name="CarpetaCompleta" id="CarpetaCompleta" value="1" <?= $CarpetaCompleta ? 'checked="checked"' : '' ?> />
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Fecha de Aprobados:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaAprobado" type="text" class="camporFormularioMediano" id="FechaAprobado" value="<?=$FechaAprobado?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaAprobado'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Fecha de Rechazo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaRechazado" type="text" class="camporFormularioMediano" id="FechaRechazado" value="<?=$FechaRechazado?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaRechazado'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Fecha de Gestor&iacute;a:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaGestoria" type="text" class="camporFormularioMediano" id="FechaGestoria" value="<?=$FechaGestoria?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaGestoria'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Fecha de Envio de Prenda:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaEnvioPrenda" type="text" class="camporFormularioMediano" id="FechaEnvioPrenda" value="<?=$FechaEnvioPrenda?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaEnvioPrenda'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Fecha de Observaci&oacute;n:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaObservacion" type="text" class="camporFormularioMediano" id="FechaObservacion" value="<?=$FechaObservacion?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaObservacion'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Observacion:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <textarea name="Observacion" id="Observacion" class="camporFormularioMediano" value="<?=$Observacion?>" onkeyup="javascript: StrToUpper(this.id);" style="height: 75px"><?= $Observacion ?></textarea>
                                                                        </div>
                                                                    </td>
                                                                </tr>
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
																<tr>
                                                                    <td><div align="right">Pre Prenda:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="PrePrenda" type="checkbox" id="PrePrenda" value="1" <?= $PrePrenda ? 'checked="checked"' : '' ?> />
                                                                        </div>
                                                                    </td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Prenda Inscripta:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="PrendaInscripta" type="checkbox" id="PrendaInscripta" value="1" <?= $PrendaInscripta ? 'checked="checked"' : '' ?> />
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