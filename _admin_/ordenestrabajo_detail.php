<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$errFinalizar			= intval($_REQUEST['errFinalizar']);
$errMONegativa			= intval($_REQUEST['errMONegativa']);


$oOrdenTrabajo		= new OrdenTrabajo();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oEstadosOrden		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oUsuarios			= new Usuarios();
$oClientes			= new Clientes();
$oMarcas			= new Marcas();
$oComprobantes		= new Comprobantes();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oOrdenTrabajoHitos 			= new OrdenTrabajoHitos();
$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();
$oCompras						= new Compras();
$oArticulos						= new Articulos();
$oCuponesDescuento				= new CuponesDescuento();
$oNotasCredito					= new NotasCredito();
$oColores						= new Colores();

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
$arrCompras		= $oCompras->GetByOrdenTrabajo($oOrdenTrabajo);
$oColor			= $oColores->GetById($oTallerUnidad->IdColor);

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
	$FechaInicio	= CambiarFechaHora($oOrdenTrabajo->FechaInicio);
	$FechaFin		= CambiarFechaHora($oOrdenTrabajo->FechaFin);
	$IdUsuario		= $oOrdenTrabajo->IdUsuarioAsignado;
	$Kilometros		= $oOrdenTrabajo->Kilometros;
}

$strParams = '?' . str_replace('errFinalizar=1', 'errFinalizar=0', $_SERVER['QUERY_STRING']);
$strParams = '?' . str_replace('errMONegativa=1', 'errMONegativa=0', $_SERVER['QUERY_STRING']);

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
<?php
if ($errFinalizar == 1)
{
?>
alert('La Orden de Trabajo no puede ser finalizada hasta que se completen las tareas realizadas en cada trabajo');
<?php
}
if ($errMONegativa == 1)
{
?>
alert('La Orden de Trabajo no puede ser finalizada ya que hay tareas con mano de obra negativa.');
<?php
}
?>
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de ordenes de trabajo - Detalle</span></td>
					<td width="20" height="40" class="TituloGrupo"><a href="ordenestrabajo_pdf.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" target="_blank"><img src="images/iconos/imprimir.png" alt="Imprimir" title="Imprimir" /></a></td>
					<td width="20" height="40" class="TituloGrupo"><a href="ordenestrabajo_pdf.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" target="_blank" class="linkMenu">Imprimir</a></td>
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
																			<td height="40" align="left"><span class="tituloPagina">ORDEN DE TRABAJO N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?></span></td>
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
																			<?= $oOrdenTrabajo->FechaInicio ? CambiarFechaHora($oOrdenTrabajo->FechaInicio) : 'N/C' ?>
																		</td>
																	</tr>
																	<tr>
																		<td width="25%" height="25" align="center"  class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Costo Estimado:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			$<?= $oOrdenTrabajo->ImporteEstimado() ?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Costo Final:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-top-gris">
																			$<?= $oOrdenTrabajo->ImporteTotal() ?>
																		</td>
																	</tr>
																	<?php
																	if ($oEstadoOrden->IdEstado == EstadoOrden::Finalizado && $oOrdenTrabajo->IdComprobante)
																	{
																		$oComprobante = $oComprobantes->GetById($oOrdenTrabajo->IdComprobante);
																	?>
																	<tr>
																		<td width="25%" height="25" align="center"  class="border-derecho-gris border-top-gris">
																			<div id="margen"><strong>Factura:</strong>&nbsp;</div>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			<?=ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)?> <?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?>
																		</td>
																		<td width="25%"  align="center" class="border-derecho-gris border-top-gris">
																			&nbsp;
																		</td>
																		<td width="25%"  align="center" class="border-top-gris">
																			&nbsp;
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
																			<?= $oOrdenTrabajo->Kilometros ?>
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
																						<td width="20"><a href="ordenestrabajotareas.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="linkMenu"><img src="images/iconos/adm_general.png" title="Administrar" /></a></td>
																						<td width="20"><a href="ordenestrabajotareas.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="linkMenu">Administrar Tareas</a></td>
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
																if ($arrOrdenesTrabajoTareas != NULL)
																{ 
																?>
																<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
																	<tr class="bordeGrisFondo">
																		<td width="40%" height="25"><div id="margen"><strong>Tarea</strong></div></td>
																		<td width="12%" height="25"><div id="margen" align="center"><strong>Cargo</strong></div></td>
																		<td width="12%" height="25"><div id="margen" align="center"><strong>Mano Obra</strong></div></td>
																		<td width="12%" height="25"><div id="margen" align="center"><strong>Cantidad</strong></div></td>
																		<td width="12%" height="25"><div id="margen" align="center"><strong>Repuestos</strong></div></td>																			
																		<td width="12%" height="25"><div id="margen" align="center"><strong>&nbsp;</strong></div></td>
																	</tr>
																<?php 
																	foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
																	{ 
																		$oTipoCargo = TipoVenta::GetByIdOrdenTrabajo($oRelacion->IdTipoVenta);
																		$arrCompras = $oCompras->GetByOrdenTrabajoTarea($oRelacion);
																		
																		$arrOrdenesTrabajoHitos = $oOrdenTrabajoHitos->GetLastByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo, $oRelacion->IdOrdenTrabajoTarea);
																		
																		$finalizado = false;
																		$empezado = false;
																		
																		if ($arrOrdenesTrabajoHitos)
																		{
																			$empezado = true;
																			foreach ($arrOrdenesTrabajoHitos as $oOrdenTrabajoHito)
																			{
																				if ($oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Finalizar)
																				{
																					$finalizado = true;
																				}
																			}
																		}
																?>
																	<tr bgColor="#ADECDF">
																		<td  height="25">
																			<div id="margen">
																<?php
																		if (!$empezado)
																		{
																		}
																		else
																		{
																			if (!$finalizado)
																			{
																?>
																				<img src="images/iconos/clock.png" alt="En Proceso" title="En Proceso" />
																<?php
																			} 
																			else 
																			{
																?>
																				<img src="images/iconos/check.gif" alt="Finalizado" title="Finalizado" />
																<?php
																			}
																		}
																?>
																				<strong><?= $oRelacion->Titulo ?>&nbsp;&nbsp;</strong>
																			</div>
																		</td>
																		<td  height="25"><div id="margen" align="center"><?=$oTipoCargo['Nombre']?></div></td>
																		<td  height="25"><div id="margen" align="center">&nbsp;</div></td>
																		<td  height="25"><div id="margen" align="center">&nbsp;</div></td>
																		<td  height="25"><div id="margen" align="center">&nbsp;</div></td>
																		<td  height="25"><div id="margen" align="center">&nbsp;</div></td>
																	</tr>
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
																	<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
																		<td colspan="2" height="25"><div id="margen" style="margin-left: 35px">Mano de Obra</div></td>
																		<td height="25"><div id="margen" align="center">$<?= number_format($oRelacion->ImporteSinRepuestos(), 2) ?></div></td>
																		<td colspan="3">&nbsp;</td>
																	</tr>
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
																		<td colspan="2" height="25">&nbsp;</td>
																		<td height="25"><div id="margen" align="center"><?= $oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion ? '-' : '' ?><?= $oCompraDetalle->Cantidad ?></div></td>
																		<td height="25"><div id="margen" align="center"><?= $oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion ? '-' : '' ?>$<?= number_format($CostoItem, 2) ?></div></td>
																		<td>&nbsp;</td>
																	</tr>
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
																<?php 
																				}
																			}
																		}
																?>
																	<tr class="bordeGrisFondo">
																		<td height="25"><div id="margen"><strong>Total Tarea</strong></div></td>																				
																		<td height="25"><div id="margen" align="center"><?=$oTipoCargo['Nombre']?></div></td>
																		<td height="25"><div id="margen" align="center">$<?= number_format($oRelacion->ImporteSinRepuestos(), 2) ?></div></td>
																		<td height="25"><div id="margen" align="center">&nbsp;</div></td>
																		<td height="25"><div id="margen" align="center">$<?= number_format($oRelacion->ImporteRepuestosReal(), 2) ?></div></td>
																		<td height="25"><div id="margen" align="center"><strong>$<?= number_format($oRelacion->ImporteTotalReal(), 2) ?></strong></div></td>
																	</tr>
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
																<?php
																	}
																?>
																<?php
																}
																		
																if ($oCliente->PercepcionIIBB && $oCliente->PercepcionIIBB > 0)
																{
																?>
																<tr>
																	<td colspan="5" height="25"><div id="margen" align="right"><strong>Perc. IIBB:</strong></div></td>																				
																	<td height="25"><div id="margen" align="center"><strong>$<?= number_format($oOrdenTrabajo->ImportePercepcionIIBB(), 2) ?> (<?= $oCliente->PercepcionIIBB ?>%)</strong></div></td>
																</tr>
																<?php
																}
																?>
																<tr>
																	<td colspan="5" height="25"><div id="margen" align="right"><strong>Total Cliente:</strong></div></td>																				
																	<td height="25"><div id="margen" align="center"><strong>$<?= $oOrdenTrabajo->ImporteTotal() ?></strong></div></td>
																</tr>
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
														<tr>
															<td align="right" height="35">
																<?php
																if ($oOrdenTrabajo->IdEstadoOrden != EstadoOrden::Finalizado)
																{
																?>
																<input type="button" name="btnEditar" class="botonBasico" id="btnEditar" onclick="javascript: window.location.href = 'ordenestrabajotareas_abrir.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Reabrir Tareas" />
																<?php
																}
																?>
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
																						<td width="10"><a href="ordenestrabajo_comentarios_add.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" class="linkMenu"><img src="images/iconos/add.gif" title="Agregar" /></a></td>
																						<td width="20"><a href="ordenestrabajo_comentarios_add.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" class="linkMenu">Agregar</a></td>
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
																		<td width="75%"  align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Comentario</strong>&nbsp;</div>
																		</td>
																		<td width="10%"  align="center" class="border-derecho-gris">
																			<div id="margen"><strong>Eliminar</strong>&nbsp;</div>
																		</td>
																	</tr>
																	<?php
																	if ($oOrdenTrabajo->Comentarios != '')
																	{
																	?>	
																	<tr>
																		<td height="25" width="15%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen">Al Ingreso</div>
																		</td>
																		<td width="75%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oOrdenTrabajo->Comentarios ?></div>
																		</td>
																		<td width="10%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><a href="ordenestrabajo_comentarios_del.php?IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>"><img src="images/iconos/del.gif" /></a></div>
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
																	<tr>
																		<td height="25" width="15%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></div>
																		</td>
																		<td height="25" width="75%" align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><?= $oOrdenTrabajoComentario->Comentarios ?></div>
																		</td>
																		<td width="10%"  align="center" class="border-derecho-gris border-top-gris">
																			<div id="margen"><a href="ordenestrabajo_comentarios_del.php?IdOrdenTrabajoComentario=<?= $oOrdenTrabajoComentario->IdOrdenTrabajoComentario ?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>"><img src="images/iconos/del.gif" /></a></div>
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
								
								/*if ($oEstadoOrden->IdEstado == EstadoOrden::Finalizado && $oOrdenTrabajo->IdComprobante)
								{
									$oNotaCredito = $oNotasCredito->GetByIdFactura($oOrdenTrabajo->IdComprobante);
									if (!$oNotaCredito)
									{
								?>
								<input type="button" name="btnAnular" class="botonBasico" id="btnAnular" onclick="javascript: window.open('ordenestrabajo_notacredito.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>');" value="Anular Factura" />
								<?php
									}
								}*/
								?>
								<?php
								if (Session::CheckPerm(PERM_FACTPV_LIST) && $oEstadoOrden->IdEstado == EstadoOrden::Finalizado)
								{
									/*$oNotaCredito = $oNotasCredito->GetByIdFactura($oOrdenTrabajo->IdComprobante);
									if (!$oOrdenTrabajo->IdComprobante || $oNotaCredito)
									{
								?>
								<input type="button" name="btnFacturar" class="botonBasico" id="btnFacturar" onclick="javascript: window.open('ordenestrabajo_facturar.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>');" value="Facturar" />
								<?php
									}*/
								?>
								<input type="button" name="btnFacturar" class="botonBasico" id="btnFacturar" onclick="javascript: window.location.href = 'ordenestrabajo_facturacion.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Facturaci&oacute;n" />
								<?php
								}
								else
								{
								?>
									<input type="button" name="btnModificar" class="botonBasico" id="btnModificar" onclick="javascript: window.location.href = 'ordenestrabajo_mod.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Modificar" />
								
								<?php
								}
								/*<input type="button" name="btnAceptar" class="botonBasico" id="btnAceptar" onclick="javascript: window.location.href = 'ordenestrabajo_pdf.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Imprimir" />
								*/
								if ($oOrdenTrabajo->IdEstadoOrden == EstadoOrden::Presupuesto)
								{
								?>

								<input type="button" name="btnIngresar" class="botonBasico" id="btnIngresar" onclick="javascript: window.location.href = 'ordenestrabajo_ingresar.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Ingresar Unidad" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_cancelar.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Rechazar OT" />
								<?php
								}
								if ($oOrdenTrabajo->IdEstadoOrden == EstadoOrden::Auditoria)
								{
								?>
								<input type="button" name="btnFinalizar" class="botonBasico" id="btnFinalizar" onclick="javascript: window.location.href = 'ordenestrabajo_finalizar.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Finalizar" />
								<?php
								}
								
								/*
								<input type="button" name="btnAsignar" class="botonBasico" id="btnAsignar" onclick="javascript: window.location.href = 'ordenestrabajotareas.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Editar Tareas" />
								*/ ?>
								<input type="button" name="btnHitos" class="botonBasico" id="btnHitos" onclick="javascript: window.location.href = 'ordenestrabajo_hitos.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>';" value="Resumen Horas" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo.php<?=$strParams?>';" value="Volver a ordenes de trabajo" />
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