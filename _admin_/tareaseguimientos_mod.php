<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_UPDATE))
	Session::NoPerm();


$CurrentUser = Session::GetCurrentUser();

/* obtiene datos del formulario */
$IdSeguimiento			= $_REQUEST['IdSeguimiento'];
$Submit1				= $_REQUEST['Submitted'];
$FechaSeg				= $_REQUEST['FechaSeg'];
$HoraSeg				= $_REQUEST['HoraSeg'];
$IdUsuarioSeg			= ($_REQUEST['IdUsuarioSeg'] != '') ? $_REQUEST['IdUsuarioSeg'] : $CurrentUser->IdUsuario;
$IdAccionSeg			= $_REQUEST['IdAccionSeg'];
$DetalleSeg				= $_REQUEST['DetalleSeg'];
$NuevaTarea				= $_REQUEST['NuevaTarea'];
$ResultadoSeg			= $_REQUEST['ResultadoSeg'];

/* declaracion de variables */
$oPresupuestos			= new Presupuestos();
$oTareaSeguimientos		= new TareaSeguimientos();
$oTareaSeguimiento		= new TareaSeguimiento();
$oUsuarios				= new Usuarios();
$oClientes				= new Clientes();
$oModelos				= new Modelos();

$oTareaSeguimiento	= $oTareaSeguimientos->GetById($IdSeguimiento);
$oUsuario	= $oUsuarios->GetById($oTareaSeguimiento->IdUsuario);

$arrUsuarios	= $oUsuarios->GetAll();

$Resultado = false;
if ($Submit1)
{			
	
	if ($ResultadoSeg == '')
		$err |= 4;
	
	if ($NuevaTarea == '1' && $DetalleSeg == '')
		$err |= 8;
	
	/* si no hay errores... */
	if ($err == 0)
	{	
		$oTareaSeguimiento->FechaAccion				= date('d-m-Y H:i:s');
		$oTareaSeguimiento->Resultado		 		= $ResultadoSeg;
		$oTareaSeguimiento->SeguimientoRealizado	= 1;
			
		$oTareaSeguimientos->Update($oTareaSeguimiento);
		
		if ($NuevaTarea)
		{
			$oTareaSeguimientoN	= new TareaSeguimiento();
			
			$oTareaSeguimientoN->IdTarea				= $oTareaSeguimiento->IdTarea;
			$oTareaSeguimientoN->IdUsuario				= $IdUsuarioSeg;
			$oTareaSeguimientoN->IdAccion	 			= $IdAccionSeg;
			$oTareaSeguimientoN->Fecha	 				= $FechaSeg . ' ' . $HoraSeg;
			$oTareaSeguimientoN->Detalle		 		= $DetalleSeg;
			$oTareaSeguimientoN->SeguimientoRealizado	= 0;
		
			/* crea el cliente */
			$oTareaSeguimiento = $oTareaSeguimientos->Create($oTareaSeguimientoN);
		}
			
		
		$Resultado = true;
	}
}
else
{
	$FechaSeg = date('d-m-Y');
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script language="javascript">
<?php
if ($Resultado)
{
?>
window.opener.Actualizar();
window.close();
<?php
}
?>
$j(document).ready(function() {
	CheckNuevaTarea();
});

function CheckNuevaTarea() {
	var nt = Get('NuevaTarea').value;
	
	if (nt == '1') {
		$j('.nuevaTarea').each(function() {
			$j(this).show();
		});
	} else {
		$j('.nuevaTarea').each(function() {
			$j(this).hide();
		});
	}
}

function frmDataSegSubmit()
{
	var frmDataSeg = Get('frmDataSeg');
	
	if (frmDataSeg == undefined)
		return false;
	
	frmDataSeg.submit();
	return true;
}
</script>

</head>
<body>

<table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Modificar tarea</span></td>
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
                 <form name="frmDataSeg" id="frmDataSeg" method="post" action="tareaseguimientos_mod.php<?=$strParams?>">
                                <input type="hidden" name="Submitted" id="Submitted" value="1">	
                                <input type="hidden" name="IdSeguimiento" id="IdSeguimiento" value="<?=$IdSeguimiento?>">
                				<table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	
								<tr>
									<td>
										<div align="center">
											<table width="90%"  border="0" align="center" cellpadding="4" cellspacing="0">
												<tr>
													<td class="bordeGris">
														<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
															
															<tr>
																<td colspan="3">
																<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																	<tr>
																		<td width="20" height="40" class="TituloRubro">&nbsp;</td>
																		<td height="40"><span class="tituloPagina">Datos de la tarea</span></td>
																  </tr>
																</table>
																</td>
															</tr>
															<tr>
																<td width="48%">&nbsp;</td>
																<td width="4%">&nbsp;</td>
																<td width="48%">&nbsp;</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Fecha:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><?= CambiarFechaHora($oTareaSeguimiento->Fecha) ?></div>
																</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Usuario:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?></div>
																</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Acci&oacute;n:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><?= SeguimientoEstados::GetById($oTareaSeguimiento->IdAccion)?></div>
																</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Comentarios:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><?= $oTareaSeguimiento->Detalle?></div>
																</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Acciones realizadas:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><textarea class="camporFormularioMultiline" id="ResultadoSeg" name="ResultadoSeg"><?= $ResultadoSeg?></textarea></div>
																</td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
																<td><?php if ($err & 4) { ?><li style="color:#FF0000;">Debe ingresar un resultado</li><?php } ?></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td colspan="3">
																<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																	<tr>
																		<td width="20" height="40" class="TituloRubro">&nbsp;</td>
																		<td height="40"><span class="tituloPagina"><img src="images/iconos/clock.png"  />&nbsp;&nbsp;&iquest;Desea agendar nueva tarea?</span></td>
																  </tr>
																</table>
																</td>
															</tr>
															<tr>
																<td width="48%">&nbsp;</td>
																<td width="4%">&nbsp;</td>
																<td width="48%">&nbsp;</td>
															</tr>
															<tr>
																<td height="25"><div align="right"><strong>Nueva tarea:</strong></div></td>
																<td width="4%">&nbsp;</td>
																<td width="48%">
																	<select id="NuevaTarea" name="NuevaTarea" class="camporFormularioSimple" style="width: 200px" onchange="javascript:CheckNuevaTarea();">
																		<option value="1" <?= $NuevaTarea == '1' ? 'selected="selected"': '' ?>>Si</option>
																		<option value="0" <?= $NuevaTarea == '0' ? 'selected="selected"': '' ?>>No</option>
																	</select>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td height="25"><div align="right"><strong>Fecha:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left">
																		<input name="FechaSeg" type="text" class="camporFormularioChico" id="FechaSeg" value="<?=$FechaSeg?>" size="12" maxlength="12" onKeyDown="javascript: return false;"/> 
																		<script language="JavaScript" type="text/javascript">
																			new tcal
																			({
																				'formname': 'frmDataSeg',
																				'controlname': 'FechaSeg'
																			});
																		</script>
																	</div>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td height="25"><div align="right"><strong>Hora:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left">
																		<select name="HoraSeg" type="text" class="camporFormularioChico" id="HoraSeg"> 
																		<?php
																		for ($j = 7; $j <= 21; $j++)
																		{
																		?>
																		<option value="<?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:00" <?= $HoraSeg == str_pad($j, 2, '0', STR_PAD_LEFT) . ':00' ? 'selected="selected"' : '' ?>><?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:00</option>
																		<option value="<?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:15" <?= $HoraSeg == str_pad($j, 2, '0', STR_PAD_LEFT) . ':15' ? 'selected="selected"' : '' ?>><?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:15</option>
																		<option value="<?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:30" <?= $HoraSeg == str_pad($j, 2, '0', STR_PAD_LEFT) . ':30' ? 'selected="selected"' : '' ?>><?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:30</option>
																		<option value="<?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:45" <?= $HoraSeg == str_pad($j, 2, '0', STR_PAD_LEFT) . ':45' ? 'selected="selected"' : '' ?>><?= str_pad($j, 2, '0', STR_PAD_LEFT) ?>:45</option>
																		<?php
																		}
																		?>
																	</select>
																	</div>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td height="25"><div align="right"><strong>Usuario:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left">
																		<select name="IdUsuarioSeg" id="IdUsuarioSeg" class="camporFormularioSimple" style="width: 200px">													<?php foreach ($arrUsuarios as $oUsuarioAux) { ?>
																		  <option value="<?=$oUsuarioAux->IdUsuario?>" <?php echo ($oUsuarioAux->IdUsuario == $IdUsuarioSeg) ? "selected='selected'" : "" ?> >
																			<?=$oUsuarioAux->Apellido;?>
																			,
																			<?=$oUsuarioAux->Nombre;?>
																		  </option>
																		  <?php } ?>
																		</select>
																	</div>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td height="25"><div align="right"><strong>Acci&oacute;n:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left">
																		<select name="IdAccionSeg" id="IdAccionSeg" class="camporFormularioSimple" style="width: 200px">
																		  <?php foreach (SeguimientoEstados::GetAll() as $oEstado) { ?>
																		  <option value="<?=$oEstado['IdAccion']?>" <?php echo ($oEstado['IdAccion'] == $IdAccion) ? "selected='selected'" : "" ?> >
																			<?=$oEstado['Descripcion']?>
																		  </option>
																		  <?php } ?>
																		</select>
																	</div>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td height="25"><div align="right"><strong>Comentarios:</strong></div></td>
																<td>&nbsp;</td>
																<td>
																	<div align="left"><textarea class="camporFormularioMultiline" id="DetalleSeg" name="DetalleSeg"><?= $DetalleSeg?></textarea></div>
																</td>
															</tr>
															<tr class="nuevaTarea">
																<td>&nbsp;</td>
																<td>&nbsp;</td>
																<td><?php if ($err & 8) { ?><li style="color:#FF0000;">Debe ingresar un comentario</li><?php } ?></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
														</table>						
													</td>
												</tr>
											</table>
											<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
												<tr>
													<td height="30">
														<div align="center">
															<input type="button" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" onclick="javascript: frmDataSegSubmit()" />
															<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.close();" value="Cancelar" />
														</div>
													</td>
												</tr>

											</table>
                </form>
		  	</div>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>

</body>
</html>
