<?php

set_time_limit(200);
require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();
/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RETEN_REPORT))
	Session::NoPerm();

/* obtiene datos enviados */
$FechaDesde				= strval($_REQUEST['FilterFechaDesde']);
$FechaHasta				= strval($_REQUEST['FilterFechaHasta']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oMinutasPago			= new MinutasPago();
$oMinutasPagoItems		= new MinutasPagoItems();
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oNumber				= new Number(); 
$oProveedores			= new Proveedores();
$oProvincias			= new Provincias();
$oFacturasCompras		= new FacturasCompras();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

$filter = array();
$filter['FechaDesde'] = $FechaDesde;
$filter['FechaHasta'] = $FechaHasta;
$filter['Saldo'] = '0';

/* obtenemos todos las unidades del recepcion */
$arrData = $oMinutasPagoItems->GetAllOrdered($filter);
$oMpdf = new mPDF();
$oMpdf->watermarkText = '';
$mpdf->ignore_table_widths = false;
$mpdf->ignore_table_percents = false;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
body {
	background-color: #FFFFFF;
}
td {
	font-size: 12px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
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
.bordeGris {
	border: 0.5px solid #000000;
}
.tituloPagina {
	font-weight: bold;
	text-align: center;
}
.bordeGrisFondo {
	background: #F3F3F3;
	width: 100%;
}
</style>
</head>
<body>
<?php
foreach ($arrData as $oMinutaPagoItem)
{
	$oUnidad = $oUnidades->GetById($oMinutaPagoItem->IdUnidad);
	$oMinutaPago = $oMinutasPago->GetById($oMinutaPagoItem->IdMinutaPago);
	if (!$oMinutaPagoItem->NumeroRetencion)
	{
		$oMinutaPagoItem->NumeroRetencion = $oMinutasPagoItems->GetProximoNumeroRetencion();
		$oMinutasPagoItems->Update($oMinutaPagoItem);
	}
	$oProveedor = $oProveedores->GetById($oMinutaPagoItem->IdProveedor);
	$oProvincia = $oProvincias->GetById($oProveedor->IdProvincia);
	$oFacturaCompra = $oFacturasCompras->GetById($oMinutaPagoItem->IdFacturaCompra);
?>
    <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr class="bordeGrisFondo">
            <td width="100%">
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td height="25" align="center"><span class="tituloPagina">CERTIFICADO DE RETENCION DE INGRESOS BRUTOS PROVINCIA DE BUENOS AIRES</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="30%">
						<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td height="10" width="20"></td>
								<td height="10"></td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td>Agente de Retenci&oacute;n:</td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td><span class="tituloPagina">ACTION MOTORSPORTS S.R.L.</span></td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td>Av. Del Liberador 2275</td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td>1636 Olivos (B)</td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td>CUIT:30-71194065-7</td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td>Ing. Brutos; 30-71194065-7</td>
							</tr>
							<tr>
								<td height="10" width="20">&nbsp;</td>
								<td height="10">&nbsp;</td>
							</tr>
						</table>
					</td>
					<td width="40%">
					</td>
					<td width="30%">
						<table width="100%" align="right" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td height="10" width="20"></td>
								<td height="20"></td>
							</tr>
							<tr>
								<td align="right">Hoja 1/1&nbsp;&nbsp;</td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td width="20">&nbsp;</td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td align="right"><span class="tituloPagina">NUMERO: 0000-<?= str_pad($oMinutaPagoItem->NumeroRetencion, 8, "0", STR_PAD_LEFT) ?></span></td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td align="center"><span class="tituloPagina">ORIGINAL</span></td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">Fecha de Emisi&oacute;n: <?= CambiarFecha($oMinutaPagoItem->Fecha) ?></td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">Retenci&oacute;n de Ingresos Brutos - D.G.R.</td>
								<td width="20">&nbsp;</td>
							</tr>
							<tr>
								<td height="10" width="20">&nbsp;</td>
								<td height="10">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td width="20">&nbsp;</td>
								<td class="bordeGris">
									<div align="center">
									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="border: 0;">
										<tr>
											<td colspan="3" align="center">Contibuyente sujeto a retenci&oacute;n</td>
										</tr>
										<tr>
											<td width="40%">
												<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" >
													<tr>
														<td height="10" width="20"></td>
														<td height="10"></td>
													</tr>
													<tr>
														<td width="20">&nbsp;</td>
														<td><span class="tituloPagina"><?= $oProveedor->Empresa ?></span></td>
													</tr>
													<tr>
														<td width="20">&nbsp;</td>
														<td><?= $oProveedor->DomicilioCalle ?> <?= $oProveedor->DomicilioNumero ?></td>
													</tr>
													<tr>
														<td width="20">&nbsp;</td>
														<td><?= $oProveedor->CodigoPostal ?> <?= $oProvincia->Nombre ?></td>
													</tr>
													<tr>
														<td height="10" width="20">&nbsp;</td>
														<td height="10">&nbsp;</td>
													</tr>
												</table>
											</td>
											<td width="30%"></td>
											<td width="30%">
												<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0" >
													<tr>
														<td height="10" width="20"></td>
													</tr>
													<tr>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td>C.U.I.T.: <?= $oProveedor->Cuit ?></td>
													</tr>
													<tr>
														<td>Ing. Brutos: <?= $oProveedor->Cuit ?></td>
													</tr>
													<tr>
														<td height="10">&nbsp;</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</div>
								</td>
								<td width="20">&nbsp;</td>
							</tr>
						</table>		
					</td>
				</tr>
				<tr>
					<td colspan="3" height="20">&nbsp;</td>
				</tr>
			</table></td>
        </tr>
        
        <tr>
            <td>
				<div align="center" width="100%">
				<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="3" height="20">&nbsp;</td>
					</tr>
					<tr>
						<td width="20">&nbsp;</td>
						<td>
							<table width="850" align="left" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="30%" align="center">Fecha Factura</td>
									<td width="40%" align="left">Comprobante</td>
									<td width="30%" align="center">Imp. Comprobante</td>
								</tr>
								<tr>
									<td width="30%" height="10" align="center"></td>
									<td width="40%" align="left"></td>
									<td width="30%" align="center"></td>
								</tr>
								<tr>
									<td width="30%" align="center"><?= CambiarFecha($oFacturaCompra->Fecha) ?></td>
									<td width="40%" align="left">FA<?= $oFacturaCompra->Numero ?></td>
									<td width="30%" align="center"><?= number_format($oFacturaCompra->Total, 2) ?></td>
								</tr>
							</table>
							</div>
						</td>
						<td width="20">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" height="100">&nbsp;</td>
					</tr>
					<tr>
						<td width="20">&nbsp;</td>
						<td>
							<table width="850" align="left" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="20%" align="right">Base de calculo:</td>
									<td width="14%" align="center"><?= number_format($oFacturaCompra->ImporteNeto, 2) ?></td>
									<td width="14%" align="center">Alicuota:</td>
									<td width="14%" align="center"><?= number_format($oMinutaPagoItem->Retencion * 100 / $oFacturaCompra->ImporteNeto, 2) ?></td>
									<td width="24%" align="center">Total Retenido:</td>
									<td width="14%" align="center"><?= number_format($oMinutaPagoItem->Retencion, 2) ?></td>
								</tr>
							</table>
							</div>
						</td>
						<td width="20">&nbsp;</td>
					</tr>
				</table>
			</td>
        </tr>
    
    
    </table>
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2">SON: <?=$oNumber->ValorEnLetras($oMinutaPagoItem->Retencion, "pesos") ?></td>
		</tr>
		<tr>
			<td colspan="2" height="70">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td width="200" align="center"><img src="images/firma_aldana.jpg" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td width="200" style="border-top: 1px solid #000000" align="center">Ref. Emisor del pago</td>
		</tr>
		
		<tr>
			<td colspan="2" height="20">&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Aclaraci&oacute;n:</td>
			<td width="200" style="border-bottom: 1px solid #000000" align="center">ALDANA BARRICHI</td>
		</tr>
		<tr>
			<td colspan="2" height="20">&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Cargo:</td>
			<td width="200" style="border-bottom: 1px solid #000000" align="center">APODERADA</td>
		</tr>
	</table>
<pagebreak>
<?php
}
?>
</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('certificado retencion.pdf', 'D'); 

?>