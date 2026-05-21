<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

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
$oOrdenTrabajoHitos 			= new OrdenTrabajoHitos();
$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();
$oCompras						= new Compras();
$oArticulos						= new Articulos();	
$oCuponesDescuento				= new CuponesDescuento();

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

$arrOrdenTrabajoHitos = $oOrdenTrabajoHitos->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
$arrOrdenTrabajoComentarios = $oOrdenTrabajoComentarios->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

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

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de ordenes de trabajo - Detalle - OT N&deg; <?= $IdOrdenTrabajo ?></span></td>
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
												<td valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">                                                           
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
																<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																	<tr>
																		<td width="200">
																			<div align="left">OT N&deg;: &nbsp;</div>
																		</td>
																		<td>
																			<?= $oOrdenTrabajo->IdOrdenTrabajo ?>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">Fecha: &nbsp;</div>
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
																			<div align="left">Estado: &nbsp;</div>
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
																			<div align="left">Fecha de Ingreso: &nbsp;</div>
																		</td>
																		<td>
																			<?= $oOrdenTrabajo->FechaInicio ? CambiarFechaHora($oOrdenTrabajo->FechaInicio) : 'N/C' ?>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">Fecha de Salida: &nbsp;</div>
																		</td>
																		<td>
																			<?= $oOrdenTrabajo->FechaFin ? CambiarFechaHora($oOrdenTrabajo->FechaFin) : 'N/C' ?>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td><div align="left">Usuario Asignado: &nbsp;</div></td>
																		<td>
																			<?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?>
																		</td>
																	</tr><?php /*
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td><div align="left">Costo: &nbsp;</div></td>
																		<td>
																			$<?= $oOrdenTrabajo->ImporteTotal() ?>
																		</td>
																	</tr>*/ ?>
																</table>
															</td>
															<td>&nbsp;</td>
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
															<td width="50%" valign="top">
																<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																	<tr>		
																		<td width="200">
																			<div align="left"><strong>Detalles Unidad: &nbsp;</strong></div>
																		</td>
																		<td>&nbsp;</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">Dominio: &nbsp;</div>
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
																			<div align="left">Marca/Modelo: &nbsp;</div>
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
																			<div align="left">N&deg; Chasis: &nbsp;</div>
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
																			<div align="left">N&uacute;mero de Motor: &nbsp;</div>
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
																			<div align="left">Fecha de Incio de Garant&iacute;a: &nbsp;</div>
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
																			<div align="left">Concesionario: &nbsp;</div>
																		</td>
																		<td>
																			<?= $oTallerUnidad->Concesionario ?>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																	<tr>
																		<td>
																			<div align="left">Kil&oacute;metros: &nbsp;</div>
																		</td>
																		<td>
																			<?= $oOrdenTrabajo->Kilometros ?>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">&nbsp;</td>
																	</tr>
																</table>
															</td>
															<td width="50%" valign="top">&nbsp;</td>
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
															<td colspan="2">
																<div style="width: 100%; height: 15px; border: none; float: left;padding: 5px 0 ; " class="bordeGrisFondo">&nbsp;&nbsp;<strong>Detalles de Tareas a Desarrollar:</strong></div>
															</td>
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
																	<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																		<tr class="bordeGrisFondo">
																			<td width="100%" height="25"><div id="margen"><strong>Tarea</strong></div></td>																			
																		</tr>
																		<?php 
																		foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
																		{ 
																			$oTipoCargo = TipoVenta::GetByIdOrdenTrabajo($oRelacion->IdTipoVenta);
																			$arrCompras = $oCompras->GetByOrdenTrabajoTarea($oRelacion);
																		?>
																			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																				<td  height="25"><div id="margen"><strong><?= $oRelacion->Titulo ?></strong></div></td>																				
																			</tr>
																			<tr>
																				<td colspan="5">
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
																			if ($arrCompras)
																			{
																				foreach ($arrCompras as $oCompra)
																				{
																					$oCompra->LoadAllDetalles();
																					foreach ($oCompra->CompraDetalles as $oCompraDetalle)
																					{
																						$CostoItem = $oCompraDetalle->ImporteCompraNeto;
																						if ($oCompra->IdCuponDescuento)
																						{
																							$oCuponDescuento = $oCuponesDescuento->GetById($oCompra->IdCuponDescuento);
																							
																							$CostoItem = $oCompraDetalle->GetSubtotal() * (1 - ($oCuponDescuento->Descuento / 100));
																							$CostoItem += $oCompraDetalle->GetTotalIva();
																						}
																						$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
																						if ($oCompra->IdOrdenTrabajoTarea)
																						{
																							$oOrdenTrabajoTareaCompra = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
																							$oTipoVenta	= TipoVenta::GetByIdOrdenTrabajo($oOrdenTrabajoTareaCompra->IdTipoVenta);
																						}
																						else
																							$oTipoVenta	= TipoVenta::GetByIdOrdenTrabajo($oCompra->TipoOperacion);
																		?>
																			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																				<td height="25"><div id="margen" style="margin-left: 35px"><?=$oArticulo->Codigo?> - <?=$oArticulo->Descripcion?></div></td>																				
																			</tr>
																			<tr>
																				<td colspan="5">
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
																				}
																			}
																		}
																		?> 																		
																	</table>
																</td>
															</tr>
														<?php 
														}
														?>	
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
																<div id="ShowComentarios" style="width: 100%; height: 15px; border: none; float: left;padding: 5px 0; " class="bordeGrisFondo">&nbsp;&nbsp;<font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowComentarios();"><b> Detalles de Comentarios</b></a></font></div>
																<div id="HideComentarios" style="display: none; width: 100%; height: 15px; border: none; float: left;padding: 5px 0; " class="bordeGrisFondo">&nbsp;&nbsp;<font>[-]<a href="#bottom" class = "linkMenu" onclick="javascript: HideComentarios();"><b> Detalles de Comentarios</b></a></font></div>																
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>														
														<tr id="tb_Comentarios" style="display: none">
															<td colspan="2">
																<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																	<tr class="bordeGrisFondo">							
																		<td width="70%" height="25"><div id="margen"><strong>Comentario</strong></div></td>
																		<td width="30%" height="25"><div id="margen"><strong>Usuario</strong></div></td>
																	</tr>
																	<?php
																	if ($oOrdenTrabajo->Comentarios != '')
																	{
																	?>
																		<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																			<td height="25"><div id="margen"><?= $oOrdenTrabajo->Comentarios ?></div></td>
																			<td height="25"><div id="margen">Al ingreso</div></td>
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
														
																	if ($arrOrdenTrabajoComentarios != NULL)
																	{ 
																		foreach ($arrOrdenTrabajoComentarios as $oOrdenTrabajoComentario) 
																		{ 
																			$oUsuario = $oUsuarios->GetById($oOrdenTrabajoComentario->IdUsuario);
																	?>
																		<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																			<td height="25"><div id="margen"><?= $oOrdenTrabajoComentario->Comentarios ?></div></td>
																			<td height="25"><div id="margen"><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></div></td>
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
								<?php
								if ($oEstadoOrden->IdEstado == EstadoOrden::Finalizado)
								{
								?>
								<input type="button" name="btnFacturar" class="botonBasico" id="btnFacturar" onclick="javascript: window.open('ordenestrabajo_facturar.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>');" value="Facturar" />
								<?php
								}
								?>
								<input type="button" name="btnImprimir" class="botonBasico" id="btnImprimir" onclick="javascript: window.location.href = 'ordenestrabajo_pdf.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>&Taller=1';" value="Imprimir" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_taller.php<?=$strParams?>';" value="Volver a ordenes de trabajo" />
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

<script language="javascript">
LoadListas('CodigoComercial', '<?=$NumeroVinPrefijo?>', '<?=$CodigoComercial?>');
</script>

</body>
</html>