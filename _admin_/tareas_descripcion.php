<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_UPDATE))
	Session::NoPerm();


$CurrentUser = Session::GetCurrentUser();

/* obtiene datos del formulario */
$IdPresupuesto			= $_REQUEST['IdPresupuesto'];
$Submit1				= $_REQUEST['Submitted1'];
$FechaSeg				= $_REQUEST['FechaSeg'];
$HoraSeg				= $_REQUEST['HoraSeg'];
$IdUsuarioSeg			= ($_REQUEST['IdUsuarioSeg'] != '') ? $_REQUEST['IdUsuarioSeg'] : $CurrentUser->IdUsuario;
$IdAccionSeg			= $_REQUEST['IdAccionSeg'];
$DetalleSeg				= $_REQUEST['DetalleSeg'];

/* declaracion de variables */
$oPresupuestos			= new Presupuestos();
$oTareaSeguimientos		= new TareaSeguimientos();
$oTareaSeguimiento		= new TareaSeguimiento();
$oUsuarios				= new Usuarios();
$oClientes				= new Clientes();
$oModelos				= new Modelos();

$filtro 				= array();
$filtro['IdTarea']		= $IdPresupuesto; 
$arrSeguimientos		= $oTareaSeguimientos->GetAll($filtro);


/* verifica si existe el registro */
if (!$oPresupuesto = $oPresupuestos->GetById($IdPresupuesto))
	exit;

$oCliente = $oClientes->GetById($oPresupuesto->IdCliente);
$oModelo = $oModelos->GetById($oPresupuesto->IdModelo);

$arrUsuarios	= $oUsuarios->GetAll();

if ($Submit1)
{			
	
	if ($DetalleSeg == '')
		$err |= 8;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oTareaSeguimiento	 				= new TareaSeguimiento();
		
		$oTareaSeguimiento->IdTarea					= $IdPresupuesto;
		$oTareaSeguimiento->IdUsuario				= $IdUsuarioSeg;
		$oTareaSeguimiento->IdAccion	 			= $IdAccionSeg;
		$oTareaSeguimiento->Fecha	 				= $FechaSeg . ' ' . $HoraSeg;
		$oTareaSeguimiento->Detalle		 			= $DetalleSeg;
		$oTareaSeguimiento->SeguimientoRealizado	= 0;
			
		
		/* crea el cliente */
		$oTareaSeguimiento = $oTareaSeguimientos->Create($oTareaSeguimiento);
		
		if ($IdAccionSeg == SeguimientoEstados::Perdido) {
			$oPresupuesto->IdEstado = PresupuestoEstados::Perdido;
			$oPresupuestos->Update($oPresupuesto);
		}
		
		header("Location: tareas_descripcion.php?IdPresupuesto=" . $IdPresupuesto);
		exit();
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

<script language="javascript">
function Actualizar() {
	window.opener.location.reload(false);
	window.location.reload(false);
}

function RealizarSeguimiento(id) {
	window.open('tareaseguimientos_mod.php?IdSeguimiento=' + id, this.target, 'width=1000,height=700');
}

function frmDataSegSubmit()
{
	var frmDataSeg = Get('frmDataSeg');
	
	if (frmDataSeg == undefined)
		return false;
	
	if (Get('DetalleSeg').value == '') {
		alert('Debe agregar un comentario');
		return false;
	}
	
	frmDataSeg.submit();
	return true;
}
</script>

<?php include('include/head.inc.php'); ?>
</head>
<body>

<table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Datos Presupuesto: <?=$oCliente->RazonSocial?> - <?= $oModelo->DenominacionComercial ?></span></td>
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
				<table width="98%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
								<tr>
									<td colspan="3">
									<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
										<tr>
											<td width="20" height="40" class="TituloRubro">&nbsp;</td>
											<td height="40"><span class="tituloPagina">Datos Cliente</span></td>
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
									<td height="25"><div align="right"><strong>Cliente:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oCliente->RazonSocial?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Telefono:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oCliente->Telefono?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Email:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oCliente->Email?></div>
									</td>
								</tr>
								<tr>
									<td width="48%">&nbsp;</td>
									<td width="4%">&nbsp;</td>
									<td width="48%">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3">
									<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
										<tr>
											<td width="20" height="40" class="TituloRubro">&nbsp;</td>
											<td height="40"><span class="tituloPagina">Datos Presupuesto</span></td>
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
									<td height="25"><div align="right"><strong>Modelo:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?= $oModelo->DenominacionComercial ?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Estado:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=PresupuestoEstados::GetColorById($oPresupuesto->IdEstado)?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Fecha Presupuesto:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?= CambiarFecha($oPresupuesto->Fecha)?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Fecha Vencimiento:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?= CambiarFecha($oPresupuesto->FechaVencimiento)?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Precio:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left">$<?= number_format($oPresupuesto->Precio, 2, ',', '.')?></div>
									</td>
								</tr>
								<?php
								if ($oPresupuesto->EntregaUsado)
								{
								?>
								<tr>
									<td height="25"><div align="right"><strong>Usado:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?= $oPresupuesto->UsadoModelo ?> - $<?= number_format($oPresupuesto->Precio, 2, ',', '.')?></div>
									</td>
								</tr>
								<?php
								}
								?>
								<?php
								if ($oPresupuesto->Financia)
								{
								?>
								<tr>
									<td height="25"><div align="right"><strong>Financiaci&oacute;n: <?= $oPresupuesto->FinanciacionAcreedor ?></strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left">$<?= number_format($oPresupuesto->FinanciacionCapital, 2, ',', '.')?> en <?= $oPresupuesto->FinanciacionCuotas ?> cuotas de $<?= number_format($oPresupuesto->FinanciacionValorCuota, 2, ',', '.')?></div>
									</td>
								</tr>
								<?php
								}
								?>
								<tr>
									<td height="25" valign="top"><div align="right"><strong>Observaciones:</strong></div></td>
									<td>&nbsp;</td>
									<td><div align="left"><?=$oPresupuesto->Observaciones?></div></td>
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
                 <form name="frmDataSeg" id="frmDataSeg" method="post" action="tareas_descripcion.php<?=$strParams?>">
                                <input type="hidden" name="Submitted1" id="Submitted1" value="1">	
                                <input type="hidden" name="IdPresupuesto" id="IdPresupuesto" value="<?=$IdPresupuesto?>">
                					<table width="98%" cellpadding="0" cellspacing="0">
                	        			<tr>
                                        	<td colspan="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="10">
                                                <table width="98%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td><strong style="font-size:16px"><b>&nbsp;<img src="images/iconos/clock.png"  />&nbsp;&nbsp;Tareas a realizar:</b></strong></td>
                                                    </tr>    
                                                </table>
                                            </td>        
                                        </tr>
                                        <tr>
                                        	<td colspan="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                        	<td><div align="left">
                                            	<input name="FechaSeg" type="text" class="camporFormularioChico" id="FechaSeg" value="<?=$FechaSeg?>" size="12" maxlength="12" onKeyDown="javascript: return false;"/> 
												<script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmDataSeg',
                                                        'controlname': 'FechaSeg'
                                                    });
                                                </script></div></td>
                                        	<td><div align="left">
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
												</select></div></td>
                                            <td>
                                                <select name="IdUsuarioSeg" id="IdUsuarioSeg" class="camporFormularioSimple" style="width: 200px">													<?php foreach ($arrUsuarios as $oUsuarioAux) { ?>
                                                          <option value="<?=$oUsuarioAux->IdUsuario?>" <?php echo ($oUsuarioAux->IdUsuario == $IdUsuarioSeg) ? "selected='selected'" : "" ?> >
                                                            <?=$oUsuarioAux->Apellido;?>
                                                            ,
                                                            <?=$oUsuarioAux->Nombre;?>
                                                          </option>
                                                          <?php } ?>
                                                        </select></td>
                                                        <td><select name="IdAccionSeg" id="IdAccionSeg" class="camporFormularioSimple" style="width: 200px">
                                                          <?php foreach (SeguimientoEstados::GetAll() as $oEstado) { ?>
                                                          <option value="<?=$oEstado['IdAccion']?>" <?php echo ($oEstado['IdAccion'] == $IdAccion) ? "selected='selected'" : "" ?> >
                                                            <?=$oEstado['Descripcion']?>
                                                          </option>
                                                          <?php } ?>
                                                        </select></td>
                                                        <td valign="middle"><input name="DetalleSeg" onkeyup="javascript: this.value = this.value.toUpperCase();;" id="DetalleSeg" class="camporFormularioSimple" value="<?=$DetalleSeg?>" /> <a onclick="javascript: frmDataSegSubmit()" href="<?=$_SERVER['REQUEST_URI']?>#m"><img src="images/iconos/add.gif" alt="Agregar Seguimiento" title="Agregar Seguimiento" border="0" /></a> </td>
                                      	</tr> 
                					</table>
                </form>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                   <?php if ($arrSeguimientos != NULL) { ?>
								<tr>
                                   		<td>
                                            <table width="98%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                <tr class="bordeGrisFondo">
                                                              <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                                                              <td width="129" height="25" class="bordeGrisTitulo"><strong>Vendedor</strong></td>
                                                              <td width="119" height="25" class="bordeGrisTitulo"><strong>Acci&oacute;n</strong></td>
                                                              <td width="180" height="25" class="bordeGrisTitulo"><strong>Comentario</strong></td>
                                                              <td width="180" height="25" class="bordeGrisTitulo"><strong>Resultado</strong></td>
                                                              <td width="10" height="25" class="bordeGrisTitulo" align="center"><strong>Acciones</strong></td>
                                                </tr>

                                                        <?php foreach ($arrSeguimientos as $oTareaSeguimiento) { 
                                                        
                                                            $oUsuarioAux = $oUsuarios->GetById($oTareaSeguimiento->IdUsuario);
                                                            
                                                        ?>
                                                      
                                                <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                    <td height="25"><div id="margen"><?=CambiarFechaHora($oTareaSeguimiento->Fecha)?></div></td>
                                                    <td height="25"><?=$oUsuarioAux->Apellido?>, <?=$oUsuarioAux->Nombre?></td>
                                                    <td height="25"><?=SeguimientoEstados::GetById($oTareaSeguimiento->IdAccion)?></td>
                                                    <td height="25"><?=$oTareaSeguimiento->Detalle?></td>         
                                                    <td height="25"><?=$oTareaSeguimiento->Resultado?></td>         
                                                    <td height="25" align="center">
														<?php
														if (!$oTareaSeguimiento->SeguimientoRealizado)
														{
														?>
														<a href="javascript: RealizarSeguimiento('<?= $oTareaSeguimiento->IdSeguimiento ?>');" title="Completar tarea"><img src="images/iconos/check.gif" /></a>
														<?php
														}
														?>
													</td>         
                                                 </tr>
                                                <tr>
                                                    <td colspan="8"><div align="center">
                                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                            </tr>
                                                        </table></div>
                                                    </td>
                                                </tr>
                                                
                                                             <?php } ?>      
                                            </table>
                                   		</td>
                                </tr>
                                <?php } else { ?>  
                                
                                    <tr>
                                        <td>
                                            <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="center"><strong>No hay seguimientos para esta tarea.</strong></div></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                      
                                <?php } ?>
                </table>
		  	</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>
