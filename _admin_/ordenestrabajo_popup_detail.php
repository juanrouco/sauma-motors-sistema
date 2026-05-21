<?php

require_once('../inc_library.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);

$oOrdenTrabajo		= new OrdenTrabajo();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oEstadosOrden		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oUsuarios			= new Usuarios();
$oClientes			= new Clientes();
$oMarcas			= new Marcas();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header("Location: ordenestrabajo.php" . $strParams);
	exit();
}

$oEstadoOrden 	= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$oUsuario		= $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
$oMarca			= $oMarcas->GetById($oTallerUnidad->IdMarca);
$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);

if ($Submit)
{
	if ($IdEstadoOrden == '')
		$err |= 1;
	if ($IdTallerUnidad == '')
		$err |= 2;
		
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oOrdenTrabajo->FechaInicio			= $FechaInicio;
		$oOrdenTrabajo->FechaFin			= $FechaFin;
		$oOrdenTrabajo->IdUsuarioAsignado	= $IdUsuario;
		$oOrdenTrabajo->Kilometros			= $Kilometros;
		
		$oOrdenTrabajo = $oOrdenesTrabajo->Update($oOrdenTrabajo);

		header("Location: ordenestrabajo.php" . $strParams);
		exit();
	}
}
else
{
	$IdEstadoOrden 	= $oOrdenTrabajo->IdEstadoOrden;
	$IdTallerUnidad	= $oOrdenTrabajo->IdTallerUnidad;
	$FechaInicio	= CambiarFecha($oOrdenTrabajo->FechaInicio);
	$FechaFin		= CambiarFecha($oOrdenTrabajo->FechaFin);
	$IdUsuario		= $oOrdenTrabajo->IdUsuarioAsignado;
	$Kilometros		= $oOrdenTrabajo->Kilometros;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">	
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
												<td valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">                                                           
														<tr>		
															<td width="50%">
																<div align="left"><strong>Detalles Unidad: &nbsp;</strong></div>
															</td>
															<td>&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Dominio: &nbsp;</div>
															</td>
															<td>
																<?= $oTallerUnidad->Dominio ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Marca/Modelo: &nbsp;</div>
															</td>
															<td>
																<?= $oMarca->Nombre ?> / <?= $oTallerUnidad->Modelo ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Vin: &nbsp;</div>
															</td>
															<td>
																<?= $oTallerUnidad->PrefijoVin ?> <?= $oTallerUnidad->NumeroVin ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">N&uacute;mero de Motor: &nbsp;</div>
															</td>
															<td>
																<?= $oTallerUnidad->NumeroMotor ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Fecha de Incio de Garant&iacute;a: &nbsp;</div>
															</td>
															<td>
																<?= CambiarFecha($oTallerUnidad->FechaInicioGarantia) ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Kil&oacute;metros: &nbsp;</div>
															</td>
															<td>
																<?= $oOrdenTrabajo->Kilometros ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
																 <div align="center">
																	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>		
															<td>
																<div align="left"><strong>Datos Cliente: &nbsp;</strong></div>
															</td>
															<td>&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Raz&oacute;n Social / Nombre y Apellido: &nbsp;</div>
															</td>
															<td>
																<?= $oCliente->RazonSocial ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Tel&eacute;fono: &nbsp;</div>
															</td>
															<td>
																(<?= $oCliente->TelefonoCodigoArea ?>) <?= $oCliente->Telefono ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
																 <div align="center">
																	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>		
															<td>
																<div align="left"><strong>Orden de Trabajo: &nbsp;</strong></div>
															</td>
															<td>&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Fecha: &nbsp;</div>
															</td>
															<td>
																<?= CambiarFecha($oOrdenTrabajo->Fecha) ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Estado: &nbsp;</div>
															</td>
															<td>
																<?= $oEstadoOrden->Nombre ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Fecha de Ingreso: &nbsp;</div>
															</td>
															<td>
																<?= $oOrdenTrabajo->FechaIngreso ? CambiarFecha($oOrdenTrabajo->FechaIngreso) : 'N/C' ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td>
																<div align="right">Fecha de Salida: &nbsp;</div>
															</td>
															<td>
																<?= $oOrdenTrabajo->FechaFin ? CambiarFecha($oOrdenTrabajo->FechaFin) : 'N/C' ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td><div align="right">Usuario Asignado:</div></td>
															<td>
																<?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td><div align="right">Costo Total:</div></td>
															<td>
																$<?= $oOrdenTrabajo->ImporteTotal() ?>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
																 <div align="center">
																	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>		
															<td>
																<div align="left"><strong>Tareas a Desarrollar: &nbsp;</strong></div>
															</td>
															<td>&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<?php 
														if ($arrOrdenesTrabajoTareas != NULL)
														{ 
														?>
															<tr>
																<td colspan="2">
																	<table width="75%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																		<tr class="bordeGrisFondo">							
																			<td width="80%" height="25"><div id="margen"><strong>Nombre</strong></div></td>
																			<td width="20%" height="25"><div id="margen"><strong>Total</strong></div></td>
																		</tr>
																		<?php 
																		foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
																		{ 
																		?>
																			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																				<td  height="25"><div id="margen"><?=$oRelacion->Titulo?></div></td>
																				<td  height="25"><div id="margen">$<?=$oRelacion->Importe ?></div></td>
																			</tr>
																			<tr>
																				<td colspan="2">
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
																		?> 
																	</table>
																</td>
															</tr>
														<?php 
														}
														?>	
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
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>


</body>
</html>