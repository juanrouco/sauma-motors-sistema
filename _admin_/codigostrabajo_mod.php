<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_CODTRA_UPDATE))
	Session::NoPerm();

$IdCodigoTrabajo		= intval($_REQUEST['IdCodigoTrabajo']);
$IdModeloPV				= intval($_REQUEST['IdModeloPV']);
$CodigoHistorico		= strval($_REQUEST['CodigoHistorico']);
$Codigo					= strval($_REQUEST['Codigo']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$Tiempo					= floatval($_REQUEST['Tiempo']);
$Articulo				= strval($_REQUEST['Articulo']);
$IdArticulo				= intval($_REQUEST['IdArticulo']);
$Submit					= (isset($_REQUEST['Submitted']));

$err				= 0;
$oCodigosTrabajo	= new CodigosTrabajo();
$oModelosPV			= new ModelosPV();
$oArticulos			= new Articulos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oCodigoTrabajo = $oCodigosTrabajo->GetById($IdCodigoTrabajo))
{
	header('Location: codigostrabajo.php' . $strParams);
	exit;
}

$arrModelosPV = $oModelosPV->GetAll();

if ($Submit)
{
	if ($IdModeloPV == '')
		$err |= 1;
	if ($CodigoHistorico == '')
		$err |= 2;
	if ($Codigo == '')
		$err |= 4;
	if ($Descripcion == '')
		$err |= 8;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oCodigoTrabajo->IdModeloPV			= $IdModeloPV;
		$oCodigoTrabajo->CodigoHistorico	= $CodigoHistorico;
		$oCodigoTrabajo->Codigo				= $Codigo;
		$oCodigoTrabajo->Descripcion		= $Descripcion;
		$oCodigoTrabajo->Tiempo				= $Tiempo;
		$oCodigoTrabajo->IdArticulo			= $IdArticulo;
		
		$oCodigoTrabajo = $oCodigosTrabajo->Update($oCodigoTrabajo);

		header("Location: codigostrabajo.php" . $strParams);
		exit();
	}
}
else
{
	$IdModeloPV			= $oCodigoTrabajo->IdModeloPV;
	$CodigoHistorico	= $oCodigoTrabajo->CodigoHistorico;
	$Codigo				= $oCodigoTrabajo->Codigo;
	$Descripcion		= $oCodigoTrabajo->Descripcion;
	$Tiempo				= $oCodigoTrabajo->Tiempo;
	$IdArticulo			= $oCodigoTrabajo->IdArticulo;
	if ($oCodigoTrabajo->IdArticulo)
	{
		$oArticulo = $oArticulos->GetById($oCodigoTrabajo->IdArticulo);
		$Articulo			= $oArticulo->Codigo;
	}
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">


function FilterArticulo(IdArticulo, Codigo)
{
	if ((IdArticulo == '') && (Codigo == ''))
	{		
		$j('#Articulo').val('');
		$j('#IdArticulo').val('');
	}

	$j('#Articulo').val(Codigo);
	$j('#IdArticulo').val(IdArticulo);
	
	$j('#modal-popup').dialog('close');
}

$j(document).ready(function() {
	$j('#buscar-articulos').click(function(e) {
		e.preventDefault();
		
		RealizarBusquedaPopup('articulos_buscar_popup2.php', {}, 'Repuestos');
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de C&oacute;digos de Trabajo - Modificar</span></td>
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
					<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?= $IdArticulo ?>" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                 	
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
																			foreach ($arrModelosPV as $oModeloPV)
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
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione el Modelo</li><?php } ?></td>
                                                            </tr>
															 <tr>
																<td><div align="right">C&oacute;digo Hist&oacute;rico:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="CodigoHistorico" name="CodigoHistorico" value="<?= $CodigoHistorico ?>" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>                                                            
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese C&oacute;digo Hist&oacute;rico.</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div align="right">C&oacute;digo:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="Codigo" name="Codigo" value="<?= $Codigo ?>" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>                                                            
                                                            <tr>
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese C&oacute;digo.</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
																<td><div align="right">Descripci&oacute;n:</div></td>
																<td>
																	<div align="left">
																		<textarea name="Descripcion" id="Descripcion" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" style="height: 75px"><?=$Descripcion?></textarea>
																	</div>
																</td>                                                                            
															</tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese la descripci&oacute;n.</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
																<td><div align="right">Tiempo:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Tiempo" id="Tiempo" class="camporFormularioSimple" value="<?=$Tiempo?>" />
																	</div>
																</td>                                                                            
															</tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															</tr>
															<tr>
																<td><div align="right">Repuesto:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Articulo" id="Articulo" class="camporFormularioSimpleDisabled" value="<?=$Articulo?>" readonly="readonly" />
																		<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?=$IdArticulo?>" />
																		<a id="buscar-articulos" href="#"><img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" /></a>
																							
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'codigosdescripcion.php<?=$strParams?>';" value="Cancelar" />
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