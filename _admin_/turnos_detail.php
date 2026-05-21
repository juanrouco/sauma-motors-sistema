<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TURNO_LIST))
	Session::NoPerm();

$IdTurno					= intval($_REQUEST['IdTurno']);

$oTurno						= new Turno();
$oTurnos					= new Turnos();
$oEstadosOrden				= new EstadosOrden();
$oTallerUnidades			= new TallerUnidades();
$oUsuarios					= new Usuarios();
$oClientes					= new Clientes();
$oMarcas					= new Marcas();
$oTurnosTareas				= new TurnosTareas();
$oTurnosTareasArticulos		= new TurnosTareasArticulos();
$oTurnosComentarios			= new TurnosComentarios();
$oArticulos					= new Articulos();
$oColores					= new Colores();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oTurno = $oTurnos->GetById($IdTurno))
{
	header("Location: turnos.php" . $strParams);
	exit();
}

$oEstadoOrden 		= $oEstadosOrden->GetById($oTurno->IdEstadoOrden);
$oTallerUnidad 		= $oTallerUnidades->GetById($oTurno->IdTallerUnidad);
$oUsuario			= $oUsuarios->GetById($oTurno->IdUsuarioAsignado);
$oCliente			= $oClientes->GetById($oTallerUnidad->IdCliente);
$oMarca				= $oMarcas->GetById($oTallerUnidad->IdMarca);
$arrTurnosTareas 	= $oTurnosTareas->GetAllByTurno($oTurno);
$oColor				= $oColores->GetById($oTallerUnidad->IdColor);

$arrTurnosComentarios = $oTurnosComentarios->GetByIdTurno($oTurno->IdTurno);

if ($Submit)
{
	if ($IdEstadoOrden == '')
		$err |= 1;
	if ($IdTallerUnidad == '')
		$err |= 2;
		
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oTurno->FechaInicio		= $FechaInicio;
		$oTurno->FechaFin			= $FechaFin;
		$oTurno->IdUsuarioAsignado	= $IdUsuario;
		$oTurno->Kilometros			= $Kilometros;
		
		$oTurno = $oTurnos->Update($oTurno);

		header("Location: ordenestrabajo.php" . $strParams);
		exit();
	}
}
else
{
	$IdEstadoOrden 	= $oTurno->IdEstadoOrden;
	$IdTallerUnidad	= $oTurno->IdTallerUnidad;
	$FechaInicio	= CambiarFechaHora($oTurno->FechaInicio);
	$FechaFin		= CambiarFechaHora($oTurno->FechaFin);
	$IdUsuario		= $oTurno->IdUsuarioAsignado;
	$Kilometros		= $oTurno->Kilometros;
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<style>
.border-derecho-gris {
	border-right: 1px solid #E8E8E8;
}

.border-top-gris {
	border-top: 1px solid #E8E8E8;
}
</style>
<script type="text/javascript">
function ShowRepuestos() {
	HideSection('ShowRepuestos');
	ShowSection('tr_Repuestos');
	ShowSection('tr_Repuestos_pre');
	ShowSection('HideRepuestos');
}
function HideRepuestos() {
	ShowSection('ShowRepuestos');
	HideSection('tr_Repuestos');
	HideSection('tr_Repuestos_pre');
	HideSection('HideRepuestos');
}

function ShowComentarios() {
	HideSection('ShowComentarios');
	ShowSection('tb_Comentarios');
	ShowSection('HideComentarios');
}
function HideComentarios() {
	ShowSection('ShowComentarios');
	HideSection('tb_Comentarios');
	HideSection('HideComentarios');
}
</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Turnos - Detalle</span></td>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
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
				<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>		
												<td colspan="2">
													<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGris">
														<tr>
															<td>
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td height="40" align="left"><span class="tituloPagina">DATOS TURNO</span></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td align="center">
																<table border="0" width="100%" cellpadding="0" cellspacing="0">
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Estado</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris">
																			<span style="color: <?= $oEstadoOrden->Color ?>">
																			<?= $oEstadoOrden->Nombre ?>
																			</span>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Fecha de Ingreso</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center">
																			<?= $oTurno->FechaInicio ? CambiarFechaHora($oTurno->FechaInicio) : 'N/C' ?>
																		</td>
																	</tr>
																	<tr>
																		<td width="25%" height="25" align="center"  class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Costo Estimado:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			$<?= $oTurno->ImporteEstimado() ?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			&nbsp;
																		</td>
																		<td width="25%"  align="center" class="border-top-gris">
																			&nbsp;
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" width="100%" valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
														<tr>
															<td>
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td height="40" align="left"><span class="tituloPagina">DATOS UNIDAD</span></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td align="center">
																<table border="0" width="100%" cellpadding="0" cellspacing="0">
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Marca/Modelo</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris">
																			<?= $oMarca->Nombre ?> / <?= $oTallerUnidad->Modelo ?>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>A&ntilde;o</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center">
																			<?= $oTallerUnidad->ModeloAnio ?>
																		</td>
																	</tr>
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Color</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<?= $oColor->Nombre ?>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>N&deg; Chasis</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-top-gris">
																			<?= $oTallerUnidad->NumeroVin ?>
																		</td>
																	</tr>
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Kilometraje</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<?= $oTurno->Kilometros ?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>N&deg; Motor</strong>&nbsp;</div>
																		</td>
																		<td width="25%" align="center" class="border-top-gris">
																			<?= $oTallerUnidad->NumeroMotor ?>
																		</td>
																	</tr>
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Patente</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oTallerUnidad->Dominio ?>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Inicio Gtia.</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-top-gris">
																			<?= CambiarFecha($oTallerUnidad->FechaInicioGarantia) ?>
																		</td>
																	</tr>
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Cons. Vend.</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oTallerUnidad->Concesionario ?>
																		</td>
																		<td  align="center" class="border-derecho-gris border-top-gris">&nbsp;</td>
																		<td  align="center" class="border-top-gris">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" height="100%" valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
														<tr>
															<td>
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td height="40" align="left"><span class="tituloPagina">DATOS CLIENTE</span></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td align="center">
																<table border="0" width="100%" cellpadding="0" cellspacing="0">
																	<tr>
																		<td height="25" width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Cliente:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris">
																			<?= $oCliente->RazonSocial ?>
																		</td>
																		<td width="25%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Tel&eacute;fono:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris">
																			<?= $oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . ' - ' : '' ?> <?= $oCliente->Telefono ?>
																		</td>
																	</tr>
																	<tr>
																		
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Email:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oCliente->Email ?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Tel&eacute;fono 2:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oCliente->FaxCodigoArea ? $oCliente->FaxCodigoArea . ' - ' : '' ?> <?= $oCliente->Fax ?>
																		</td>
																	</tr>
																	<tr>
																		
																		<td height="25" width="25%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Necesita Remis:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oTurno->Remis ? 'SI' : 'NO' ?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Reconfirmado:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?= $oTurno->Reconfirmado ? 'SI' : 'NO' ?>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" height="100%" valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
														<tr>
															<td>
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td height="40" align="left"><span class="tituloPagina">TAREAS A DESARROLLAR</span></td>
																			<td align="right">
																				<table width="200" border="0" cellspacing="0" cellpadding="0">
																					<tr>
																						<td height="35" width="20">&nbsp;</td>
																						<td width="20"><a href="turnostareas.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>" class="linkMenu"><img src="images/iconos/adm_general.png" title="Administrar" /></a></td>
																						<td width="20"><a href="turnostareas.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>" class="linkMenu">Administrar Tareas</a></td>
																						<td width="20">&nbsp;</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td align="center">
																<?php 
																if ($arrTurnosTareas != NULL)
																{ 
																?>
																<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																	<tr class="bordeGrisFondo">
																		<td width="70%" height="25"><div id="margen"><strong>Tarea</strong></div></td>
																		<td width="10%" height="25"><div id="margen" align="left"><strong>Cargo</strong></div></td>
																		<td width="10%" height="25"><div id="margen" align="left"><strong>Importe</strong></div></td>
																	</tr>
																<?php 
																	foreach ($arrTurnosTareas as $oRelacion) 
																	{ 
																		$oTipoCargo = TipoVenta::GetByIdOrdenTrabajo($oRelacion->IdTipoVenta);
																?>
																	<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																		<td  height="25">
																			<div id="margen">
																				<strong><?= $oRelacion->Titulo ?>&nbsp;&nbsp;</strong>
																			</div>
																		</td>
																		<td  height="25"><div id="margen" align="left"><?=$oTipoCargo['Nombre']?></div></td>
																		<td  height="25"><div id="margen" align="left">$<?= $oRelacion->Importe ?></div></td>
																	</tr>
																	<tr>
																		<td colspan="3">
																			<div align="center">
																				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																					<tr>
																						<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																					</tr>
																				</table>
																			</div>
																		</td>
																	</tr>
																	
																<?php
																	}
																
																/*if ($oCliente->PercepcionIIBB && $oCliente->PercepcionIIBB > 0)
																{
																?>
																<tr>
																	<td colspan="5" height="25"><div id="margen"><strong>Perc. IIBB</strong></div></td>																				
																	<td height="25"><div id="margen" align="center"><strong>$<?= number_format($oTurno->ImportePercepcionIIBB(), 2) ?> (<?= $oCliente->PercepcionIIBB ?>%)</strong></div></td>
																</tr>
																<?php
																}*/
																?>
																<tr>
																	<td colspan="2" height="25"><div id="margen"><strong>Total Cliente</strong></div></td>																				
																	<td height="25"><div id="margen" align="left"><strong>$<?= $oTurno->ImporteEstimado() ?></strong></div></td>
																	
																</tr>
																<?php
																}
																		
																?>
																<tr>
																	<td colspan="6">
																		<div align="center">
																			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																				</tr>
																			</table>
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
												<td colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" height="100%" valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
														<tr>
															<td>
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td width="125" height="40" align="left">
																				<span class="tituloPagina">COMENTARIOS</span>
																			</td>
																			<?php /*<td>
																				<div id="ShowComentarios" style="display: none;">
																					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																						<tr>
																							<td><font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowComentarios();"><b> Mostrar</b></a></font></td>
																							<td width="20">&nbsp;</td>
																						</tr>
																					</table>
																				</div>
																				<div id="HideComentarios" style=" width: 100%;">
																					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																						<tr>
																							<td><font>[-]<a href="#bottom" class = "linkMenu" onclick="javascript: HideComentarios();"><b> Ocultar</b></a></font></td>
																							<td width="20">&nbsp;</td>
																						</tr>
																					</table>
																				</div>
																			</td> */ ?>
																			<td align="right">
																				<table width="80"  border="0" cellspacing="0" cellpadding="0">
																					<tr>
																						<td width="10"><a href="turnos_comentarios_add.php<?=$strParams?>&IdTurno=<?=$oTurno->IdTurno?>" class="linkMenu"><img src="images/iconos/add.gif" title="Agregar" /></a></td>
																						<td width="20"><a href="turnos_comentarios_add.php<?=$strParams?>&IdTurno=<?=$oTurno->IdTurno?>" class="linkMenu">Agregar</a></td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr id="tb_Comentarios">
															<td align="center">
																<table border="0" width="100%" cellpadding="0" cellspacing="0">
																	<tr>
																		<td height="25" width="15%" align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Usuario</strong>&nbsp;</div>
																		</td>
																		<td width="85%"  align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Comentario</strong>&nbsp;</div>
																		</td>
																	</tr>
																	<?php
																	if ($oTurno->Comentarios != '')
																	{
																	?>	
																	<tr>
																		<td height="25" width="15%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen">Al Ingreso</div>
																		</td>
																		<td width="85%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oTurno->Comentarios ?></div>
																		</td>
																	</tr>
																	<?php
																	}
																	if ($arrTurnosComentarios != NULL)
																	{ 
																		foreach ($arrTurnosComentarios as $oTurnoComentario) 
																		{ 
																			$oUsuario = $oUsuarios->GetById($oTurnoComentario->IdUsuario);
																	?>
																	<tr>
																		<td height="25" width="15%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></div>
																		</td>
																		<td height="25" width="85%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oTurnoComentario->Comentarios ?></div>
																		</td>
																	</tr>
																	<?php
																		}
																	}
																	?>
																	
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
								<input type="button" name="btnModificar" class="botonBasico" id="btnModificar" onclick="javascript: window.location.href = 'turnos_mod.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>';" value="Modificar" />
								<input type="button" name="btnIngresar" class="botonBasico" id="btnIngresar" onclick="javascript: window.location.href = 'turnos_ingresar.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>';" value="Ingresar Unidad" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'turnos_cancelar.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>';" value="Rechazar Turno" />
								<?php
								if ($oTurno->IdEstadoOrden == EstadoOrden::Rechazado)
								{
								?>
								<input type="button" name="btnReagendar" class="botonBasico" id="btnReagendar" onclick="javascript: window.location.href = 'turnos_add.php<?=$strParams?>&IdTurno=<?= $oTurno->IdTurno ?>';" value="Reagendar Turno" />
								<?php
								}
								?>
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_turnos.php<?=$strParams?>';" value="Volver a Turnos" />
							</div>
						</td>
					</tr>
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