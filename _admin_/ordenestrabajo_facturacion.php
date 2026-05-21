<?php
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACTPV_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$Action 				= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oComprobantes					= new Comprobantes();
$oOrdenesTrabajo		 		= new OrdenesTrabajo();
$oOrdenesTrabajoFranquicias		= new OrdenesTrabajoFranquicias();
$oClientes	 					= new Clientes();
$oNotasCredito					= new NotasCredito();
$oTiposCosto					= new TiposCosto();
$oModelos						= new Modelos();
$oTallerUnidades				= new TallerUnidades();
$oFacturasPostVentas			= new FacturasPostVentas();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header('Location: ordenestrabajo.php' . $strParams);
	exit;
}

$arrComprobantes = $oComprobantes->GetAllByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

$arrFacturasPostVentas = $oFacturasPostVentas->GetByOrdenTrabajo($oOrdenTrabajo);

$arrOrdenesTrabajoFranquicias = $oOrdenesTrabajoFranquicias->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);

$MostrarAgregar = true;
if ($arrFacturasPostVentas)
{
	foreach ($arrFacturasPostVentas as $oFacturaPostVenta)
	{
		$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
		$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
		if (!$oNotaCredito && false)
			$MostrarAgregar = false;
	}
}


IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
</form>
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Orden de Trabajo N&deg; <?= $IdOrdenTrabajo ?> - Detalle Facturaci&oacute;n</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><table border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>
								<?php
									if ($MostrarAgregar)
									{
										if (Session::CheckPerm(PERM_FACTPV_CREATE))
										{
									?>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="ordenestrabajo_franquicias_add.php<?=$strParams?>">Agregar Franquicia</a></td>
									 <td width="30"><div align="center">&nbsp;</div></td>
									
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="ordenestrabajo_factura_add.php<?=$strParams?>">Agregar Factura</a></td>
									<td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="ordenestrabajo_factura_add_3.php<?=$strParams?>">Agregar Recibo</a></td>
									<?php
										}
									}
									?>
									<td width="30"><div align="center">&nbsp;</div></td>
                                </tr>
                            </table></td>
			</tr>
<?php 
			if ($arrFacturasPostVentas != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Detalle de facturas a imprimir:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="40%" height="25"><div id="margen"><strong>Cliente</strong></div></td>
							<td width="12%" height="25"><div id="margen"><strong>Tipo Factura</strong></div></td>
							<td width="12%" height="25"><div id="margen"><strong>Numero Factura</strong></div></td>
							<td width="12%" height="25"><div id="margen"><strong>Estado</strong></div></td>
							<td width="12%" height="25"><div id="margen"><strong>Total</strong></div></td>
							<td width="12%"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrFacturasPostVentas as $oFacturaPostVenta) 
						{ 
							$oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente);
							$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
							$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="40%"><div id="margen"><?= $oCliente->RazonSocial ?></div></td>
							<td width="12%"><div id="margen"><?= ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) ?></div></td>
							<td width="12%"><div id="margen"><?= $oFacturaPostVenta->NumeroFactura ?></div></td>
							<td width="12%"><div id="margen"><?= $oNotaCredito ? 'Anulada' : 'Facturada' ?></div></td>
							<td width="12%" height="25"><div id="margen">$<?=$oFacturaPostVenta->ImporteBruto?></div></td>	
							<td>
								<div align="center">
									<a href="ordenestrabajo_factura_details.php<?=$strParams?>&IdFacturaPostVenta=<?=$oFacturaPostVenta->IdFacturaPostVenta?>">
										<img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a> - 
										<?php
										if (!$oNotaCredito)
										{
											if (!$oComprobante)
											{
										?>
										<a target="_blank" href="facturaspostventas_imprimir.php<?=$strParams?>&IdFacturaPostVenta=<?= $oFacturaPostVenta->IdFacturaPostVenta ?>">
										<img src="images/iconos/imprimir.png" alt="Imprimir" border="0" /></a>
										<?php
											}
											elseif (!$oComprobante->Numero || $oComprobante->Numero == '00000000'){
										?>
											<form action="ordenestrabajo_factura_afip.php" style="display: inline">
												<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaPostVenta->IdFacturaPostVenta ?>" />
												<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
											</form> - 
										<?php
											}
											else
											{
										?>
											<a href="facturaspostventas_pdf.php<?=$strParams?>&IdFacturaPostVenta=<?=$oFacturaPostVenta->IdFacturaPostVenta?>">
												<img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> - 
										<?php
											}
										}
										else
										{
											$oComprobanteNC = $oComprobantes->GetById($oNotaCredito->IdComprobante);
											if (!$oComprobanteNC->Numero || $oComprobanteNC->Numero == '00000000'){
										?>
											<form action="ordenestrabajo_factura_notascredito_afip.php" style="display: inline">
												<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaPostVenta->IdFacturaPostVenta ?>" />
												<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
											</form> - 
										<?php
											}
											else
											{
										?>
										<a target="_blank" href="facturaspostventas_notacredito_imprimir.php<?=$strParams?>&IdFacturaPostVenta=<?= $oFacturaPostVenta->IdFacturaPostVenta ?>">
										<img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> - 
										<?php
											}
										}
										?><?php if (!$oNotaCredito) { ?>
										<a href="ordenestrabajo_facturas_anular.php<?=$strParams?>&IdFacturaPostVenta=<?= $oFacturaPostVenta->IdFacturaPostVenta ?>">
										<img src="images/iconos/permisos.gif" alt="Anular" border="0" /></a><?php } ?> - 
									<a href="facturaspostventas_pagos.php<?=$strParams?>&IdFacturaPostVenta=<?=$oFacturaPostVenta->IdFacturaPostVenta?>">
										<img src="images/iconos/facturacion.png" alt="Ver Pagos" border="0" /></a>
								</div>
							</td>
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
						?> 
					</table>
				</td>
			</tr>
			<?php 
			}
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<?php 
			/*if ($arrComprobantes != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Detalle de facturaci&oacute;n de la OT N&deg; <?= $IdOrdenTrabajo ?>:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="100" height="25"><div id="margen"><strong>Fecha</strong></div></td>
							<td width="100" height="25"><div id="margen"><strong>Tipo</strong></div></td>
							<td width="100" height="25"><div id="margen"><strong>N&uacute;mero</strong></div></td>
							<td width="350" height="25"><div id="margen"><strong>Cliente</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Total</strong></div></td>
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrComprobantes as $oComprobante) 
						{ 
							$oCliente = $oClientes->GetById($oComprobante->IdCliente);
							$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="100"><div id="margen"><?=CambiarFecha($oComprobante->Fecha)?></div></td>
							<td width="100"><div id="margen"><?= ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) ?></div></td>
							<td width="100"><div id="margen"><?= $oComprobante->Prefijo ?> - <?= $oComprobante->Numero ?></div></td>
							<td width="350"><div id="margen"><?= $oCliente->RazonSocial ?></div></td>
							<td width="100" height="25"><div id="margen">$<?=$oComprobante->Importe?></div></td>	
							<td>
								<div align="center">
								<?php
									if (!$oNotaCredito)
									{
								?>
									<a target="_blank" href="ordenestrabajo_facturas_pdf.php<?=$strParams?>&IdComprobante=<?= $oComprobante->IdComprobante ?>">
										<img src="images/iconos/pdf.png" alt="Imprimir" border="0" />
									</a> - <a target="_blank" href="ordenestrabajo_notacredito.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>">
										<img src="images/iconos/permisos.gif" alt="Anular" border="0" />
									</a>
								<?php
									}
								?>
								</div>
							</td>
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
					</table>
				</td>
			</tr>
			<?php 
			} 
			else 
			{ 
			?>
			<tr height="20">
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
						</tr>
						<tr>
							<td><div align="center"><strong>No hay ninguna factura emitida.</strong></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>		  
			<?php
			}*/
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<?php 
			if ($arrOrdenesTrabajoFranquicias != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Detalle de franquicias de la OT N&deg; <?= $IdOrdenTrabajo ?>:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="350" height="25"><div id="margen"><strong>Cliente</strong></div></td>
							<td width="350" height="25"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
							<td width="100" height="25"><div id="margen"><strong>Estado</strong></div></td>
							<td width="100" height="25"><div id="margen"><strong>Importe</strong></div></td>
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrOrdenesTrabajoFranquicias as $oOrdenTrabajoFranquicia) 
						{ 
							$oCliente = $oClientes->GetById($oOrdenTrabajoFranquicia->IdCliente);
							$oComprobante = $oComprobantes->GetById($oOrdenTrabajoFranquicia->IdComprobante);
							$oNotaCredito = $oNotasCredito->GetByIdFactura($oOrdenTrabajoFranquicia->IdComprobante);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="350"><div id="margen"><?= $oCliente->RazonSocial ?></div></td>
							<td width="350"><div id="margen"><?= $oOrdenTrabajoFranquicia->Descripcion ?></div></td>
							<td width="100"><div id="margen"><?= !$oOrdenTrabajoFranquicia->IdComprobante ? 'Sin Facturar' : ($oOrdenTrabajoFranquicia->Anulado ? 'Anulado' : 'Facturado') ?></div></td>
							<td width="100" height="25"><div id="margen">$<?=$oOrdenTrabajoFranquicia->Importe?></div></td>	
							<td>
								<div align="center">
								<?php
									if (!$oOrdenTrabajoFranquicia->IdComprobante)
									{
								?>
									<a href="ordenestrabajo_franquicias_mod.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>&IdOrdenTrabajoFranquicia=<?= $oOrdenTrabajoFranquicia->IdOrdenTrabajoFranquicia ?>">
										<img src="images/iconos/mod.gif" alt="Anular" border="0" /></a>
									
								<?php
									}
								?>
								</div>
							</td>
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
					</table>
				</td>
			</tr>
			<?php 
			} 
			else 
			{ 
			?>
			<tr height="20">
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
						</tr>
						<tr>
							<td><div align="center"><strong>No hay ninguna franquicia.</strong></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>		  
			<?php
			}
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			
			
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="right">
									<label>
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'ordenestrabajo_detail.php<?=$strParams?>';" value="Volver a ordenes de trabajo" />
									</label>
								</div>
							</td>
							<td width="10" height="30">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<div id="modal-popup" style="display:none">
	</div>
</body>
</html>