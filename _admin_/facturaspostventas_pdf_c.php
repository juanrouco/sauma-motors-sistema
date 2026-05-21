<?php

require_once('../inc_library.php');
require_once('../library/mpdf60/mpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFacturaPostVenta = intval($_REQUEST['IdFacturaPostVenta']);

$oOrdenesTrabajo 		= new OrdenesTrabajo();
$oComprobantes 			= new Comprobantes();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oColores 				= new Colores();
$oMarcas 				= new Marcas();
$oTiposModelo 			= new TiposModelo();
$oNumber				= new Number(); 
$oTiposDocumento 		= new TiposDocumento();
$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
$oCompras 				= new Compras();
$oArticulos				= new Articulos();
$oFacturasPostVentas	= new FacturasPostVentas();	
$oNotasCredito			= new NotasCredito();

/* obtenemos los datos del comprobante de pago */
if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta))
	exit();
	
/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante))
	exit();


$oItems = $oFacturaPostVenta->GetAllItems();
	
/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oComprobante->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);

/* comenzamos la creacion del archivo pdf */
$oMpdf = new mPDF('utf-8', 'A4');
$oMpdf->watermarkText = '';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
<!--
td { font-size: 12px }
.style11 {font-weight: bold}
.style12 {font-weight: bold; font-size: 12px;}
.style1 {font-weight: bold; font-size: 14px;}
.style1-normal{font-weight: normal; font-size: 14px;}
.style0 {font-weight: bold; font-size: 18px;}
.border-none {border: 0}
.border-top {border-top: 1px solid #000000;}
.border-bottom {border-bottom: 1px solid #000000;}
.border-left {border-left: 1px solid #000000;}
.border-right {border-right: 1px solid #000000;}
.margin-left {margin-left: 10px }
-->
</style>
</head>
<body>
<?php
for ($k = 1; $k < 3; $k++)
{
?>
	<table width="100%" border="1" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="45%" class="border-none">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td align="center"><img src="../facturaelectronica/plantillas/logo.png" width="180" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><div align="left" class="style11"><?= ConfiguracionFactura::RazonSocial ?></div></td>
					</tr>
					<tr>
						<td><div align="left"><?= ConfiguracionFactura::Direccion ?></div></td>
					</tr>
					<tr>
						<td><div align="left"><?= ConfiguracionFactura::Direccion2 ?></div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td width="45%"><?= ConfiguracionFactura::Fax ?></td>
									<td align="right" width="55%">IVA Responsable Inscripto</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td width="10%" class="border-none">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td align="center" height="50" colspan="2" class="border-bottom border-left border-right">
							<div class="style1" align="center" style="font-size: 25px">X</div>
							<div class="style12" align="center"></div>
						</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td width="45%" class="border-none">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td height="30" align="center"><div class="style0">Recibo</div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td width="50%" align="center"><?= $k == 1 ? 'Original' : 'Duplicado' ?></td>
									<td align="center" width="50%">P&aacute;gina 1 de 1</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="25" align="center"><div class="style0">N&deg;: <?= $oComprobante->Prefijo ?>-<?= $oComprobante->Numero ?></div></td>
					</tr>
					<tr>
						<td align="center"><div class="style1-normal">Fecha: <?= str_replace('-', '/', CambiarFecha($oComprobante->Fecha)) ?></div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td height="25" width="50%">CUIT: <?= ConfiguracionFactura::CuitLetras ?></td>
									<td align="right" width="50%">I. Brutos: <?= ConfiguracionFactura::IIBB ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center"><div>Inicio de Actividades: <?= ConfiguracionFactura::FechaInicioActividad ?></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="border-none border-top">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="left">Sr.(s):   <?= $oCliente->RazonSocial ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="left">Direcci&oacute;n:   <?= $oCliente->GetDomicilio() ?></div>
						</td>
					</tr>
					<tr>
						<td width="33%">
							<div align="left">Localidad:   <?= $oCliente->GetLocalidad() ?></div>
						</td>
						<td colspan="2">
							<div align="left">Provincia:   <?= $oCliente->GetProvincia() ?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div align="left">IVA:   <?= $oCliente->GetIva() ?></div>
						</td>
						<td width="33%">
							<div align="left"><?= $oCliente->GetDocumentoAfip() ?></div>
						</td>
						<td width="34%">
							<div align="left">Tel&eacute;fono:  <?= $oCliente->GetTelefono() ?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div align="left">Condiciones de Venta:   </div>
						</td>
						<td colspan="2">
							<div align="left">Remito N&deg;:   </div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="border-none border-top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td height="25" align="center" class="border-right border-bottom" width="10%"><div class="style11">Cantidad</div></td>
						<td class="border-right border-bottom" width="60%"><div align="left" class="style11 margin-left">&nbsp;Descripci&oacute;n</div></td>
						<td class="border-right border-bottom" width="15%" align="center"><div class="style11">Precio Unitario</div></td>
						<td class="border-bottom" width="15%" align="center"><div class="style11">Precio</div></td>
					</tr>
					<?php
					$TotalNeto = 0;
					$TotalIva = 0;
					$TotalBruto = 0;
					$Cantidad = 0;
					foreach ($oItems as $oItem)
					{
						$Cantidad++;
					?>
					<tr>
						<td height="20" class="border-right" align="center"><div><?= $oItem->Cantidad ?></div></td>
						<td class="border-right"><div class="margin-left" align="left">&nbsp;<?= $oItem->Descripcion ?></div></td>
						<td class="border-right" align="center"><div>$ <?= number_format($oItem->ImporteBruto / $oItem->Cantidad, 2, ',', '.') ?></div></td>
						<td align="center"><div>$ <?= number_format($oItem->ImporteBruto, 2, ',', '.') ?></div></td>
					</tr>
					<?php

						if ($Cantidad % 35 == 0) {
					?>
				</table>
			</td>
		</tr>
	</table>
					<pagebreak />
	<table width="100%" border="1" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="45%" class="border-none">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td align="center"><img src="../facturaelectronica/plantillas/logo.png" width="180" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><div align="left" class="style11"><?= ConfiguracionFactura::RazonSocial ?></div></td>
					</tr>
					<tr>
						<td><div align="left"><?= ConfiguracionFactura::Direccion ?></div></td>
					</tr>
					<tr>
						<td><div align="left"><?= ConfiguracionFactura::Direccion2 ?></div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td width="45%"><?= ConfiguracionFactura::Fax ?></td>
									<td align="right" width="55%">IVA Responsable Inscripto</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td width="10%" class="border-none">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td align="center" height="50" colspan="2" class="border-bottom border-left border-right">
							<div class="style1" align="center" style="font-size: 25px">X</div>
							<div class="style12" align="center"></div>
						</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%" class="border-right">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td width="45%" class="border-none">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td height="30" align="center"><div class="style0">Recibo</div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td width="50%" align="center"><?= $k == 1 ? 'Original' : 'Duplicado' ?></td>
									<td align="center" width="50%">P&aacute;gina 1 de 1</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="25" align="center"><div class="style0">N&deg;: <?= $oComprobante->Prefijo ?>-<?= $oComprobante->Numero ?></div></td>
					</tr>
					<tr>
						<td align="center"><div class="style1-normal">Fecha: <?= str_replace('-', '/', CambiarFecha($oComprobante->Fecha)) ?></div></td>
					</tr>
					<tr>
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td height="25" width="50%">CUIT: <?= ConfiguracionFactura::CuitLetras ?></td>
									<td align="right" width="50%">I. Brutos: <?= ConfiguracionFactura::IIBB ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center"><div>Inicio de Actividades: <?= ConfiguracionFactura::FechaInicioActividad ?></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="border-none border-top">
				<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="left">Sr.(s):   <?= $oCliente->RazonSocial ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="left">Direcci&oacute;n:   <?= $oCliente->GetDomicilio() ?></div>
						</td>
					</tr>
					<tr>
						<td width="33%">
							<div align="left">Localidad:   <?= $oCliente->GetLocalidad() ?></div>
						</td>
						<td colspan="2">
							<div align="left">Provincia:   <?= $oCliente->GetProvincia() ?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div align="left">IVA:   <?= $oCliente->GetIva() ?></div>
						</td>
						<td width="33%">
							<div align="left"><?= $oCliente->GetDocumentoAfip() ?></div>
						</td>
						<td width="34%">
							<div align="left">Tel&eacute;fono:  <?= $oCliente->GetTelefono() ?></div>
						</td>
					</tr>
					<tr>
						<td>
							<div align="left">Condiciones de Venta:   </div>
						</td>
						<td colspan="2">
							<div align="left">Remito N&deg;:   </div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="border-none border-top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td height="25" align="center" class="border-right border-bottom" width="10%"><div class="style11">Cantidad</div></td>
						<td class="border-right border-bottom" width="60%"><div align="left" class="style11 margin-left">&nbsp;Descripci&oacute;n</div></td>
						<td class="border-right border-bottom" width="15%" align="center"><div class="style11">Precio Unitario</div></td>
						<td class="border-bottom" width="15%" align="center"><div class="style11">Precio</div></td>
					</tr>
					<?php	
						}
						
					}

					
					for ($items = $Cantidad; $items < 32; $items++)
					{
					?>
					<tr>
						<td height="20" class="border-right" align="center"><div></div></td>
						<td class="border-right"><div class="margin-left" align="left">&nbsp;</div></td>
						<td class="border-right" align="center"><div></div></td>
						<td align="center"><div></div></td>
					</tr>
					<?php
					}
					
					?>
					<tr>
						<td height="22" colspan="2" class="border-right border-top"><div>&nbsp;</div></td>
						<td class="border-right border-top" align="center"><div class="style11">Total</div></td>
						<td class="border-top" align="center"><div class="style11">$ <?= number_format($oComprobante->Importe, 2, ',', '.') ?></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		$PesosLetra = $oNumber->ValorEnLetras($oComprobante->Importe, "pesos");
		?>
		<tr>
			<td colspan="3" class="border-none border-top">
				<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td height="25">
							<div align="left">Son pesos: <?= $PesosLetra ?></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?= $k == 1 ? '<pagebreak />' : '' ?>
<?php
}
?>
</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

//$oMpdf->SetJS('this.print();');
$oMpdf->WriteHTML(utf8_encode($Contenido));
$oMpdf->Output('factura.pdf', 'I'); 

?>