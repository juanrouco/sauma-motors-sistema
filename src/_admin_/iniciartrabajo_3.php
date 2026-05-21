<?php
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 


$IdOrdenTrabajo			= intval($_REQUEST['NumeroOrdenTrabajo']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);

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
	header("Location: iniciartrabajo.php?Mensaje=1");
	exit();
}

if ($oOrdenTrabajo->IdEstadoOrden != EstadoOrden::Aceptada)
{
	header("Location: iniciartrabajo.php?Mensaje=2");
	exit();
}

$oEstadoOrden 	= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$oUsuario		= $oUsuarios->GetById($IdUsuario);
$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
$oMarca			= $oMarcas->GetById($oTallerUnidad->IdMarca);

$arrAbiertas = $oOrdenTrabajoHitos->GetByIdUsuarioAndTipoHitoAndNotIdOrdenTrabajo($oUsuario->IdUsuario, OrdenTrabajoHito::Iniciar, $oOrdenTrabajo->IdOrdenTrabajo);
if ($arrAbiertas && count($arrAbiertas) > 0)
{
	header("Location: iniciartrabajo.php?Mensaje=7");
	exit();
}

$arrAbiertasEnOrden = $oOrdenTrabajoHitos->GetByIdUsuarioAndTipoHitoAndIdOrdenTrabajo($oUsuario->IdUsuario, OrdenTrabajoHito::Iniciar, $oOrdenTrabajo->IdOrdenTrabajo);

$filter = array();
$filter['IdOrdenTrabajo'] = $oOrdenTrabajo->IdOrdenTrabajo;
$filter['NotIdEstado'] = OrdenTrabajoTarea::IdEstadoFinalizado;
$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAll($filter);

if (!$arrOrdenesTrabajoTareas ||count($arrOrdenesTrabajoTareas) == 0)
{
	header("Location: iniciartrabajo.php?Mensaje=2");
	exit();
}

$arrOrdenTrabajoHitos = $oOrdenTrabajoHitos->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
$arrOrdenTrabajoComentarios = $oOrdenTrabajoComentarios->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

$IdEstadoOrden 	= $oOrdenTrabajo->IdEstadoOrden;
$IdTallerUnidad	= $oOrdenTrabajo->IdTallerUnidad;
$FechaInicio	= CambiarFecha($oOrdenTrabajo->FechaInicio);
$FechaFin		= CambiarFecha($oOrdenTrabajo->FechaFin);
$IdUsuario		= $IdUsuario;
$Kilometros		= $oOrdenTrabajo->Kilometros;

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
function IniciarTarea(IdOrdenTrabajoTarea, titulo, control) {
	$j('<div></div>').appendTo('body').html('<div><h6>&iquest;Usted ha seleccionado para iniciar la tarea ' + titulo + '?</h6></div>').dialog({
		modal: true, title: 'Confirmaci&oacute;n', zIndex: 10000, autoOpen: true,
		width: 'auto', resizable: false,
		buttons: {
			Si: function () {
				$j('#IdOrdenTrabajoTarea').val(IdOrdenTrabajoTarea);
				$j('#frmData').submit();		
				$j(this).dialog("close");
			},
			No: function () {
				$j(this).dialog("close");
			}
		},
		close: function (event, ui) {
			$j(this).remove();
			control.disabled = false;
		}
	});
}

function FinalizarTarea(IdOrdenTrabajoTarea, titulo) {
	$j('<div></div>').appendTo('body').html('<div><h6>&iquest;Usted ha seleccionado para finalizar la tarea ' + titulo + '?</h6></div>').dialog({
		modal: true, title: 'Confirmaci&oacute;n', zIndex: 10000, autoOpen: true,
		width: 'auto', resizable: false,
		buttons: {
			Si: function () {
				$j('#IdOrdenTrabajoTarea').val(IdOrdenTrabajoTarea);
				$j('#MainAction').val('finalizar');
				$j('#frmData').submit();		
				$j(this).dialog("close");
			},
			No: function () {
				$j(this).dialog("close");
			}
		},
		close: function (event, ui) {
			$j(this).remove();
			control.disabled = false;
		}
	});
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
        			<td height="40"><span class="tituloPagina">Fichado de Tareas</span></td>
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
				<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">					
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
												<td><span class="tituloPagina">MEC&Aacute;NICOSELECCIONADO: <?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?></span></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td><span class="tituloPagina">ORDEN DE TRABAJO N&deg;: <?= $oOrdenTrabajo->IdOrdenTrabajo ?></span></td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td valign="top">
													<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">                                                           
														
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
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
																<table border="0" class="bordeGris" width="100%" cellpadding="0" cellspacing="0">
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
																		<td  align="center" class="border-derecho-gris border-top-gris">&nbsp;</td>
																		<td  align="center" class="border-top-gris">&nbsp;</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td colspan="2">&nbsp;</td>
														</tr>
														<tr>
															<td colspan="2">
																<div align="center">
																	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
																		<tr>
																			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																			<td height="40" align="left"><span class="tituloPagina">TAREAS A DESARROLLAR</span></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
														<?php 
														if ($arrOrdenesTrabajoTareas != NULL)
														{ 
														?>
															<tr>
																<td colspan="2">
																	<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																		<?php 
																		foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
																		{ 
																			$oTipoCargo = TipoVenta::GetByIdOrdenTrabajo($oRelacion->IdTipoVenta);
																			$arrCompras = $oCompras->GetByOrdenTrabajoTarea($oRelacion);
																			$oOrdenTrabajoHito = $oOrdenTrabajoHitos->GetLastByIdOrdenTrabajoAndIdUsuario($oOrdenTrabajo->IdOrdenTrabajo, $oRelacion->IdOrdenTrabajoTarea, $oUsuario->IdUsuario);
																			if (!$arrAbiertasEnOrden || count($arrAbiertasEnOrden) == 0 || ($oOrdenTrabajoHito && $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Iniciar))
																			{
																		?>
																			<tr onMouseOver="bgColor='#FFF'" onMouseOut="bgColor=''">
																				<td height="25" width="75%"><div id="margen"><?= $oRelacion->Titulo ?></div></td>
																				<td height="25" width="25%">
																					<div id="margen" align="right" style="width: 250px">
																						<?php
																						if (!$oOrdenTrabajoHito || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Detener || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Finalizar)
																						{
																						?>
																						<input type="button" class="botonBasico" style="margin-right: 25px; background-color: green; width: 100px" onclick="this.disabled = true;IniciarTarea(<?= $oRelacion->IdOrdenTrabajoTarea ?>, '<?= $oRelacion->Titulo ?>', this)" value="Iniciar" />
																						<?php
																						}
																						else
																						{
																						?>
																						
																						<input type="button" class="botonBasico" style="float: right;margin-right: 25px; background-color: blue; width: 100px" onclick="this.disabled = true;FinalizarTarea(<?= $oRelacion->IdOrdenTrabajoTarea ?>, '<?= $oRelacion->Titulo ?>')" value="Finalizar" />
																						<input type="button" class="botonBasico" style="float: right;margin-right: 25px; background-color: red; width: 100px" onclick="this.disabled = true;IniciarTarea(<?= $oRelacion->IdOrdenTrabajoTarea ?>, '<?= $oRelacion->Titulo ?>')" value="Detener" />
																						<?php
																						}
																						?>
																					</div>
																				</td>
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
																			/*if ($arrCompras)
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
																			<tr onMouseOver="bgColor='#FFF'" onMouseOut="bgColor=''">
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
																			}*/
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
<form id="frmData" name="frmData" action="iniciartrabajo_4.php" method="post">
	<input type="hidden" id="IdOrdenTrabajo" name="IdOrdenTrabajo" value="<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" />
	<input type="hidden" id="IdOrdenTrabajoTarea" name="IdOrdenTrabajoTarea" value="" />
	<input type="hidden" id="IdUsuario" name="IdUsuario" value="<?= $oUsuario->IdUsuario ?>" />
	<input type="hidden" id="MainAction" name="MainAction" value="" />
</form>
</body>
</html>