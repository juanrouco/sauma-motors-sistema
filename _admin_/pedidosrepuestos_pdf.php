<?php 

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdPedidoRepuesto = intval($_REQUEST['IdPedidoRepuesto']);

$oPedidosRepuestos			= new PedidosRepuestos();
$oPedidosRepuestosDetalles	= new PedidosRepuestosDetalles();
$oUsuarios	 				= new Usuarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades 			= new TallerUnidades();
$oArticulos 				= new Articulos();
$oArticuloStocks			= new ArticuloStocks();
$oClientes					= new Clientes();	

if (!$oPedidoRepuesto = $oPedidosRepuestos->GetById($IdPedidoRepuesto))
	exit();

$arrDetalles = $oPedidosRepuestosDetalles->GetAllByPedidoRepuesto($oPedidoRepuesto);

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);

$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);

$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
$oUsuarioGenerador = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioGenerador);
$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
$oUsuarioPedido = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioPedido);

$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4-L', 0, 'DejaVuSans', 15, 15, 16, 16, 9, 9, 'L');
$oMpdf->watermarkText = '';
$oMpdf->showWatermarkText = false;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
body {
	background-color: #FFFFFF;
}
table {
	border-collapse: collapse;
	width: 100%;
}
td {
	font-size: 14px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
td.bordeNegro {
	border-bottom: 1px solid #E8E8E8;
	border-left: 1px solid #E8E8E8;
}
td.bordeGris {
	border-top: 1px solid #E8E8E8;
	border-bottom: 1px solid #E8E8E8;
	border-left: 1px solid #E8E8E8;
	border-right: 1px solid #E8E8E8;
	
}
td.bordeGrisFondo {
	border-top: 1px solid #E8E8E8;
	border-bottom: 1px solid #E8E8E8;
	border-left: 1px solid #E8E8E8;
	border-right: 1px solid #E8E8E8;
	background: #F3F3F3;
	
}
td.bordeNegroBottom {
	border-bottom: 1px solid #E8E8E8;
}
td.bordeNegroTop {
	border-top: 1px solid #E8E8E8;
}
td.bordeNegroLeft {
	border-left: 1px solid #E8E8E8;
}
td.bordeNegroRight {
	border-right: 1px solid #E8E8E8;
}
td.Item {	
	font-size: 12px; 
}
.texto20 {
	font-size: 20px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.bordeBottom {
	border-bottom: 2px solid #000000;
}
.textoPie {
	font-size: 11px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td width="100%">
			<div style="width:100%" align="center">				
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td width="100%">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td width="100%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="33%">
													<img src="images/logo_tolosa.jpg" width="200" />
												</td>
												<td width="34%" align="center">
													&nbsp;
												</td>
												<td width="33%">
												</td>
											</tr>
											<tr>
												<td colspan="3" align="center">
													<div align="center"><span style="width: 100%; align:center" class="texto20">SOLICITUD DE REPUESTOS / PRESUPUESTO</span></div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
                    	<td align="right">&nbsp;</td>
                    </tr>
					<tr>
                    	<td width="1000" align="center">
							<table width="1000" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="30%" valign="top">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td align="left" colspan="2">
													<strong>Datos del Solicitante</strong>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" align="center" class="bordeGrisFondo">
													<strong><?= utf8_encode('N&deg; PEDIDO') ?></strong>
												</td>
												<td width="50%">&nbsp;
													<?= $oPedidoRepuesto->IdPedidoRepuesto ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" align="center" class="bordeGrisFondo">
													<strong>FECHA</strong>
												</td>
												<td width="50%">&nbsp;
													<?= CambiarFecha($oPedidoRepuesto->Fecha) ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>SOLICITANTE</strong>
												</td>
												<td>&nbsp;
													<?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>AREA</strong>
												</td>
												<td>&nbsp;
													<?= $oSector['Nombre'] ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
										</table>
									</td>
									<td width="3%">&nbsp;</td>
									<td width="30%" valign="top">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td colspan="2">
													<strong>Datos de la Unidad</strong>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" align="center" class="bordeGrisFondo">
													<strong><?= utf8_encode('N&deg; OT') ?></strong>
												</td>
												<td width="50%">&nbsp;
													<?= $oOrdenTrabajo->IdOrdenTrabajo ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>MODELO</strong>
												</td>
												<td align="center">&nbsp;
													<?= $oTallerUnidad->Modelo ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>DOMINIO</strong>
												</td>
												<td>&nbsp;
													<?= $oPedidoRepuesto->Dominio ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>CHASIS</strong>
												</td>
												<td>&nbsp;
													<?= $oTallerUnidad->NumeroVin ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
										</table>
									</td>
									<td width="4%">&nbsp;</td>
									<td width="30%" valign="top">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td colspan="2">
													<strong>Modalidad</strong>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td width="50%" align="center" class="bordeGrisFondo">
													<strong>&nbsp;&nbsp;&nbsp;MODALIDAD&nbsp;&nbsp;&nbsp;</strong>
												</td>
												<td width="50%">&nbsp;
													<?= $oModalidad['Nombre'] ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>KILOMETROS</strong>
												</td>
												<td>&nbsp;
													<?= number_format($oOrdenTrabajo->Kilometros, 0, ',', '.') ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>INICIO GARANTIA</strong>
												</td>
												<td>&nbsp;
													<?= CambiarFecha($oTallerUnidad->FechaInicioGarantia) ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td align="center" class="bordeGrisFondo">
													<strong>CLIENTE</strong>
												</td>
												<td>&nbsp;
													<?= $oCliente->RazonSocial ?>
												</td>
											</tr>
											<tr>
												<td align="left" colspan="2">&nbsp;</td>
											</tr>
										</table>
									</td>
									<td width="3%">&nbsp;</td>
								</tr>
							</table>
						</td>
                    </tr>				
					<tr>
						<td width="100%" align="center">
								<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="5" align="center">&nbsp;</td>									
										<td colspan="3" width="12%" class="bordeGrisFondo" align="center"><strong>Cargo</strong></td>									
										<td width="10%" align="center">&nbsp;</td>									
									</tr>
									<tr>
										<td width="20%" class="bordeGrisFondo" align="center"><strong><?= utf8_encode('Descripci&oacute;n') ?></strong></td>
										<td width="6%" class="bordeGrisFondo" align="center"><strong>Cantidad</strong></td>
										<td width="6%" class="bordeGrisFondo" align="center"><strong><?= utf8_encode('C&oacute;digo') ?></strong></td>									
										<td width="8%" class="bordeGrisFondo" align="center"><strong>Precio</strong></td>									
										<td width="4%" class="bordeGrisFondo" align="center"><strong>Disp</strong></td>									
										<td width="4%" class="bordeGrisFondo" align="center"><strong>CL</strong></td>									
										<td width="4%" class="bordeGrisFondo" align="center"><strong>GT</strong></td>									
										<td width="4%" class="bordeGrisFondo" align="center"><strong>IN</strong></td>									
										<td width="25%" class="bordeGrisFondo" align="center"><strong>Fecha Pedido</strong></td>									
										<td width="28%" class="bordeGrisFondo" align="center"><strong>Nro. Pedido SAP</strong></td>									
									</tr>
									<?php 
									$count = 0;
									$UltimaFecha = false;
									if ($arrDetalles)
									{
										foreach ($arrDetalles as $oPedidoRepuestoDetalle) 
										{
											$oArticulo = $oArticulos->GetById($oPedidoRepuestoDetalle->IdArticulo);
												$oArticuloStock = $oArticuloStocks->GetByArticuloAndUbicacion($oArticulo->IdArticulo, Ubicacion::Libertador);
											if (!$UltimaFecha || $oPedidoRepuestoDetalle->FechaPedido > $UltimaFecha)
												$UltimaFecha = $oPedidoRepuestoDetalle->FechaPedido;
									?>
									<tr>
										<td width="20%" class="bordeNegro Item" align="center"><?=$oArticulo->Descripcion?></td>
										<td width="6%" class="bordeNegro Item" align="center"><?=$oPedidoRepuestoDetalle->Cantidad?></td>
										<td width="6%" class="bordeNegro Item" align="center"><?=$oArticulo->Codigo?></td>
										<td width="8%" class="bordeNegro Item" align="center">$<?=$oPedidoRepuestoDetalle->Precio?></td>
										<td width="4%" class="bordeNegro Item" align="center"><?= $oArticuloStock->StockActual > 0 ? 'SI' : 'NO' ?></td>
										<td width="4%" class="bordeNegro Item" align="center"><?= $oPedidoRepuestoDetalle->IdCargo == TipoVenta::OrdenReparacion ? 'X' : '' ?></td>
										<td width="4%" class="bordeNegro Item" align="center"><?= $oPedidoRepuestoDetalle->IdCargo == TipoVenta::Garantia ? 'X' : '' ?></td>
										<td width="4%" class="bordeNegro Item" align="center"><?= $oPedidoRepuestoDetalle->IdCargo == TipoVenta::VentaInterna ? 'X' : '' ?></td>
										<td width="28%" class="bordeNegro Item bordeNegroRight" align="center"><?= $oPedidoRepuestoDetalle->FechaPedido ? CambiarFechaHora($oPedidoRepuestoDetalle->FechaPedido) : '' ?></td>
										<td width="28%" class="bordeNegro Item bordeNegroRight" align="center"><?= $oPedidoRepuestoDetalle->NumeroSap ?></td>
									</tr>
									<?php
										}
									}
									?>
								</table>
						</td>
					</tr>					
					
					<tr>
						<td height="70"></td>
					</tr>
					<tr>
						<td>
							<table width="1000" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td width="33%" align="center"><strong>RESPONSABLE REPUESTOS</strong></td>
									<td width="34%" align="center"><strong>APROBO JEFE DE SECTOR</strong></td>
									<td width="33%" align="center"><strong>RESPONSABLE COMPRAS</strong></td>
								</tr>
								<tr>
									<td width="33%" height="40" align="center">&nbsp;</td>
									<td width="34%" align="center">&nbsp;</td>
									<td width="33%" align="center">&nbsp;</td>
								</tr>
								<tr>
									<td width="33%" align="center"><?= $oUsuarioGenerador->Nombre . ' ' . $oUsuarioGenerador->Apellido ?> / <?= CambiarFecha($oPedidoRepuesto->Fecha) ?></td>
									<td width="34%" align="center"><?= $oUsuarioAprobado->Nombre . ' ' . $oUsuarioAprobado->Apellido ?> / <?= CambiarFecha($oPedidoRepuesto->FechaAprobado) ?></td>
									<td width="33%" align="center"><?= $oUsuarioPedido->Nombre . ' ' . $oUsuarioPedido->Apellido ?> / <?= CambiarFecha($UltimaFecha) ?></td>
								</tr>
								<tr>
									<td width="33%" align="center">FIRMA / FECHA</td>
									<td width="34%" align="center">FIRMA / FECHA</td>
									<td width="33%" align="center">FIRMA / FECHA</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
    		</div>
		</td>
  	</tr>
</table>
</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

//$oMpdf->SetJS('this.print();');
$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('pedidosrepuestos.pdf', 'I'); 

?>