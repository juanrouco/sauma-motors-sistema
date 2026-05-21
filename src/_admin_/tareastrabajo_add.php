<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TARE_CREATE))
	Session::NoPerm();

$IdModeloPV				= intval($_REQUEST['IdModeloPV']);
$NumeroLista			= strval($_REQUEST['NumeroLista']);
$AnioDesde				= intval($_REQUEST['AnioDesde']);
$AnioHasta				= intval($_REQUEST['AnioHasta']);
$Importe				= floatval($_REQUEST['Importe']);
$ImporteCalculado		= floatval($_REQUEST['ImporteCalculado']);
$Titulo					= strval($_REQUEST['Titulo']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$HorasEstimadas			= floatval($_REQUEST['HorasEstimadas']);
$IdTipoCosto			= intval($_REQUEST['IdTipoCosto']);
$IdCodigoTrabajo		= intval($_REQUEST['IdCodigoTrabajo']);
$CodigoTrabajo			= strval($_REQUEST['CodigoTrabajo']);
$Submit					= (isset($_REQUEST['Submitted']));

$err				= 0;
$oTareaTrabajo		= new TareaTrabajo();
$oTareasTrabajo		= new TareasTrabajo();
$oModelos			= new Modelos();
$oTiposCosto		= new TiposCosto();
$oCostosManoObra	= new CostosManoObra();
$oCodigosTrabajo	= new CodigosTrabajo();
$oModelosPV			= new ModelosPV();

$strParams = '?' . $_SERVER['QUERY_STRING'];
$arrTiposCosto = $oTiposCosto->GetAll();

if ($Submit)
{
	if ($IdModeloPV == '')
		$err |= 1;
	//if ($AnioDesde == '' || $AnioHasta == '')
		//$err |= 2;
	if ($IdTipoCosto == TipoCosto::CostoFijo && $Importe == '')
		$err |= 4;
	if ($Titulo == '')
		$err |= 8;
	if ($HorasEstimadas == '')
		$err |= 16;
	if ($IdTipoCosto == '')
		$err |= 32;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$Importe	= str_replace(",", ".", $Importe);
		
		$oTareaTrabajo->IdModeloPV		= $IdModeloPV;
		$oTareaTrabajo->AnioDesde		= $AnioDesde;
		$oTareaTrabajo->AnioHasta		= $AnioHasta;
		$oTareaTrabajo->Titulo			= $Titulo;
		$oTareaTrabajo->Descripcion		= $Descripcion;
		$oTareaTrabajo->HorasEstimadas	= $HorasEstimadas;
		$oTareaTrabajo->Importe			= $Importe;
		$oTareaTrabajo->IdTipoCosto		= $IdTipoCosto;
		$oTareaTrabajo->IdCodigoTrabajo	= $IdCodigoTrabajo;
		
		$oTareaTrabajo = $oTareasTrabajo->Create($oTareaTrabajo);

		header("Location: tareastrabajo.php" . $strParams);
		exit();
	}
}

$filter = array();
$filter['Disponible'] = '1';

$arrModelos = $oModelosPV->GetAll($filter);

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">
function ValidarVacio()
{
	if ($j('#Modelo').val() == '')
		FilterDenominacionComercial('', '');
}

function FilterCodigoTrabajo(IdCodigoTrabajo, CodigoTrabajo) {
	$j('#IdCodigoTrabajo').val(IdCodigoTrabajo);
	$j('#CodigoTrabajo').val(CodigoTrabajo);
	
	$j('#modal-popup').dialog('close');
}

$j(document).ready(function() {
	$j('#buscar-codigos').click(function(e) {
		e.preventDefault();
		
		RealizarBusquedaPopup('codigostrabajo_buscar_popup.php', {}, 'C&oacute;digos de Trabajo');
	});

	$j('.tr_CostoFijo').each(function() {
		$j(this).hide();
	});
	$j('.tr_costoCalculado').each(function() {
		$j(this).hide();
	});
	
	$j('#HorasEstimadas').change(function() {
		if ($j('#IdTipoCosto').val() == <?= TipoCosto::CostoCalculado ?>)
		{
			$j('#ImporteCalculado').val(<?= $oCostosManoObra->GetLast() ?> * $j('#HorasEstimadas').val());
		}
	});
	
	$j('#IdTipoCosto').change(function() {
		if ($j('#IdTipoCosto').val() == <?= TipoCosto::CostoFijo ?>)
		{
			$j('.tr_CostoFijo').each(function() {
				$j(this).show();
			});
			$j('.tr_costoCalculado').each(function() {
				$j(this).hide();
			});
		}
		else if ($j('#IdTipoCosto').val() == <?= TipoCosto::CostoCalculado ?>)
		{
			$j('.tr_CostoFijo').each(function() {
				$j(this).hide();
			});
			$j('.tr_costoCalculado').each(function() {
				$j(this).show();
			});
		}
		else
		{
			$j('.tr_CostoFijo').each(function() {
				$j(this).hide();
			});
			$j('.tr_costoCalculado').each(function() {
				$j(this).hide();
			});
		}
	});
	<?php
	if ($IdTipoCosto)
	{
	?>
	$j('#IdTipoCosto').val(<?= $IdTipoCosto ?>)
	<?php
	}
	?>
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de taller - Agregar</span></td>
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
                    <input type="hidden" name="IdModelo" id="IdModelo" value="<?=$IdModelo?>" />
                 	
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
																<td><div align="right">Modelo:</div></td>
																<td>
																	<div align="left">
																		<select name="IdModeloPV" id="IdModeloPV" class="camporFormularioSimple">
																			<option value="">Seleccione el modelo</option>
																			<?php
																			foreach ($arrModelos as $oModeloPV)
																			{
																				$selected = '';
																				if ($oModeloPV->IdModeloPV == $IdModeloPV)
																					$selected = 'selected="selected"';
																			?>
																			<option value="<?= $oModeloPV->IdModeloPV ?>" <?= $selected ?>><?= $oModeloPV->Modelo ?></option>
																			<?php
																			}
																			?>
																		</select>
																		<span style="color:#FF0000;">&nbsp;(*)</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
															
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione el modelo</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
																<td><div align="right">Nombre Tarea:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="Titulo" name="Titulo" value="<?= $Titulo ?>" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>                                                            
                                                            <tr>
                                                                <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese un nombre para la tarea.</li><?php } ?></td>
                                                            </tr>
															<?php /*<tr>
																<td><div align="right">Codigo de Trabajo:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="CodigoTrabajo" name="CodigoTrabajo" value="<?= $CodigoTrabajo ?>" class="camporFormularioSimpleDisabled" style="width: 225px" readonly="readonly" />
																		<a id="buscar-codigos" href="#"><img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" /></a>
																		<input type="hidden" id="IdCodigoTrabajo" name="IdCodigoTrabajo" value="<?= $IdCodigoTrabajo ?>" />
																		<span style="color:#FF0000;">&nbsp;</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>   
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>	*/ ?>														
                                                            <tr>
																<td><div align="right">Descripci&oacute;n:</div></td>
																<td>
																	<div align="left">
																		<textarea name="Descripcion" id="Descripcion" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" style="height: 75px"><?=$Descripcion?></textarea>
																	</div>
																</td>                                                                            
															</tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
                                                                <td><div align="right">Costo:</div></td>
																<td>
																	<div align="left">
																		<select id="IdTipoCosto" name="IdTipoCosto" class="camporFormularioSimple">
																			<option value="">Seleccione Costo</option>
																			<?php
																			foreach ($arrTiposCosto as $oTipoCosto)
																			{
																				$selected = $oTipoCosto->IdTipoCosto == $IdTipoCosto ? 'selected="selected"' : '';
																			?>
																			<option value="<?= $oTipoCosto->IdTipoCosto ?>" <?= $selected ?>><?= $oTipoCosto->Nombre ?></option>
																			<?php
																			}
																			?>
																		</select>
																	</div>
																</td>   
                                                            </tr>
															<tr>
                                                                <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Seleccione el tipo de costo</li><?php } else {?> &nbsp; <?php } ?></td>
                                                            </tr>
															<tr class="tr_costoFijo">
																<td><div align="right">Importe:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?=$Importe?>" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
                                                            <tr class="tr_costoFijo">
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un importe</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div align="right">Tiempo Estimado (Hs.):</div></td>                                                                        
                                                                <td>
																	<div align="left">
																		<input type="text" name="HorasEstimadas" id="HorasEstimadas" class="camporFormularioChico" value="<?= $HorasEstimadas ?>" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>																				
																</td>
															</tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Seleccione el tiempo estimado</li><?php } ?><?php if ($err & 64) { ?><li style="color:#FF0000;">ya existe registrado el n&uacute;mero de motor</li><?php } ?></td>
                                                            </tr>
															<tr class="tr_costoCalculado">
																<td><div align="right">Importe Calculado:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="ImporteCalculado" id="ImporteCalculado" class="camporFormularioSimple" value="<?=$ImporteCalculado?>" readonly="yes" />
																	</div>
																</td>
															</tr>
                                                            <tr class="tr_costoCalculado">
                                                                <td height="20">&nbsp;</td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'tareastrabajo.php<?=$strParams?>';" value="Cancelar" />
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