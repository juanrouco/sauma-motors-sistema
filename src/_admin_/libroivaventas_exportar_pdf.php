<?php 
set_time_limit(10000);
require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();
/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_LIBRO_IVA))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['IdTipoComprobante'] = trim($_REQUEST['FilterIdTipoComprobante']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 		= array();
$oComprobantes 	= new Comprobantes();
$oClientes		= new Clientes();
$oTiposIva		= new TiposIva();
$oProveedores	= new Proveedores();
$oNotasCredito	= new NotasCredito();
$oPage 			= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oComprobantes->GetLibroVentasCountRows($filter), true);
$arrData = $oComprobantes->GetLibroVentas($filter);

$oTotales = $oComprobantes->GetLibroVentasTotales($filter, $oPage);

$arrComprobantesTipos = ComprobanteTipos::GetAllVentas();

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4', 0, 'DejaVuSans', 10, 10, 5, 5, 9, 9, 'L');
$oMpdf->watermarkText = '';


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
.Estilo1 {font-size: 10px;}
.Estilo2 {font-size: 9px}
.Estilo3 {font-size: 9px; color:#FFFFFF;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-top-width: 1px; border-top-style:solid; border-top-color:#000000;}
-->
.Estilo4 {
	font-size: 12px;
}
</style>
</head>
<body>
<?php
$countHoja = 28;
if ($arrData)
{
?>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td align="right"><div align="right"><span class="Estilo4">HOJA: <?= $countHoja ?></span></div></td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">SUBDIARIO DE I.V.A. VENTAS</span></div></td>
    </tr>
    <tr>
    	<td align="left"><div align="left"><span class="Estilo4">ACTION MOTORSPORTS S.R.L.</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">Av Del Liberador 2275</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">Venta de autos, camionetas y utilitarios, nuevos</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">30-71194065-7</span></div></td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">DESDE <?= str_replace('-', '/', $filter['FechaDesde']) ?> HASTA <?= str_replace('-', '/', $filter['FechaHasta']) ?></span></div></td>
    </tr>
</table>
 <table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="BordeInf BordeDer"><div align="left" class="Estilo1">Fecha</strong></div></td>
		<td class="BordeInf BordeDer" width="40"><div align="left" class="Estilo1"><div align="left">T.</div></div></td>
		<td class="BordeInf BordeDer" width="110"><div align="left" class="Estilo1"><div align="left">N&uacute;mero</div></div></td>
		<td class="BordeInf BordeDer" width="250"><div align="left" class="Estilo1"><div align="left">Raz&oacute;n Social</div></div></td>
		<td class="BordeInf BordeDer" width="40"><div align="left" class="Estilo1"><div align="left">Cond.</div></div></td>
		<td class="BordeInf BordeDer" width="110"><div align="left" class="Estilo1"><div align="left">CUIT</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Neto Grav.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">IVA 21%</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">IVA 10,5%.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Ret. Iva</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">No Grav.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Imp. Int.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Total</div></div></td>
	</tr>

<?php 
	$count = 0;
	foreach ($arrData as $oComprobante)
	{	
		$oCliente = $oClientes->GetById($oComprobante->IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
?>

	<tr>
		<td width="70" align="left" valign="top"><div align="left" class="Estilo2"><?=str_replace('-', '/', CambiarFecha($oComprobante->Fecha))?></div></td>
		<td width="40" align="left" valign="top"><div align="left" class="Estilo2"><?= ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) ?>V</div></td>
		<td width="110" align="left" valign="top"><div align="left" class="Estilo2"><?=ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) ?><?= $oComprobante->Prefijo?>-<?= $oComprobante->Numero?></div></td>
<?php
		$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
		if (($oComprobante->FechaAnulada || $oComprobante->IdEstado == ComprobanteEstados::Anulado) && !$oNotaCredito)
		{
?>
		<td width="250" align="left" valign="top"><div align="left" class="Estilo2">ANULADO</div></td>
		<td width="40" align="left" valign="top"><div align="left" class="Estilo2"><?=$oTipoIva->Codigo?></div></td>
		<td width="110" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$0</div></td>
<?php
		}
		else 
		{
			$totalIva10 = $oComprobante->ImporteIva10 * 100 / 10.5;
			$totalIva21 = $oComprobante->ImporteIva21 * 100 / 21;
?>
		<td width="250" align="left" valign="top"><div align="left" class="Estilo2"><?=iconv(mb_detect_encoding($oCliente->RazonSocial),"UTF-8//IGNORE",$oCliente->RazonSocial)?></div></td>
		<td width="40" align="left" valign="top"><div align="left" class="Estilo2"><?=$oTipoIva->Codigo?></div></td>
		<td width="110" align="left" valign="top"><div align="left" class="Estilo2"><?= $oCliente->ClaveFiscalNumero != '' ? substr_replace(substr_replace(str_replace('-', '', $oCliente->ClaveFiscalNumero), '-', 10, 0), '-', 2, 0) : ($oCliente->DocumentoNumero ? $oCliente->DocumentoNumero : '') ?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($totalIva10 + $totalIva21, 2, ',', '.')?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->ImporteIva21, 2, ',', '.')?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->ImporteIva10, 2, ',', '.')?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format(0, 2, ',', '.')?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->PercepcionIIBB, 2, ',', '.')?></div></td>
		<?php
		if (number_format($oComprobante->Importe - $oComprobante->ImpuestoInterno - $totalIva10 - $totalIva21 - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->PercepcionIIBB, 1, ',', '.') == '-0,0')
		{
		?>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?>0,00</div></td>
		<?php
		} else {
			$NoGravado = $oComprobante->Importe - $oComprobante->ImpuestoInterno - $totalIva10 - $totalIva21 - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->PercepcionIIBB;
			$NoGravado = $NoGravado < 0.2 ? 0 : $NoGravado;
		?>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($NoGravado, 1, ',', '.')?>0</div></td>
		<?php
		}
		?>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->ImpuestoInterno, 2, ',', '.')?></div></td>
		<td width="80" align="right" valign="top"><div align="left" class="Estilo2">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->Importe, 2, ',', '.')?></div></td>
<?php
		}
?>

	</tr>

<?php 
		$count++;
		
		if ($count % 99 == 0)
		{
			$countHoja++;
?>
</table>
<pagebreak>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td align="right"><div align="right"><span class="Estilo4">HOJA: <?= $countHoja ?></span></div></td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">SUBDIARIO DE I.V.A. VENTAS</span></div></td>
    </tr>
    <tr>
    	<td align="left"><div align="left"><span class="Estilo4">ACTION MOTORSPORTS S.R.L.</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">Av Del Liberador 2275</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">Venta de autos, camionetas y utilitarios, nuevos</span></div></td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">30-71194065-7</span></div></td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">DESDE <?= str_replace('-', '/', $filter['FechaDesde']) ?> HASTA <?= str_replace('-', '/', $filter['FechaHasta']) ?></span></div></td>
    </tr>
</table>

 <table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="BordeInf BordeDer"><div align="left" class="Estilo1">Fecha</strong></div></td>
		<td class="BordeInf BordeDer" width="40"><div align="left" class="Estilo1"><div align="left">T.</div></div></td>
		<td class="BordeInf BordeDer" width="110"><div align="left" class="Estilo1"><div align="left">N&uacute;mero</div></div></td>
		<td class="BordeInf BordeDer" width="250"><div align="left" class="Estilo1"><div align="left">Raz&oacute;n Social</div></div></td>
		<td class="BordeInf BordeDer" width="40"><div align="left" class="Estilo1"><div align="left">Cond.</div></div></td>
		<td class="BordeInf BordeDer" width="110"><div align="left" class="Estilo1"><div align="left">CUIT</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Neto Grav.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">IVA 21%</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">IVA 10,5%.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Ret. Iva</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">No Grav.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Imp. Int.</div></div></td>
		<td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">Total</div></div></td>
	</tr><?php
		}
	} ?>      

</table>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="BordeDer" width="100" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
		<td class="BordeDer" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Neto Gravado</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->NetoGravado, 2, '.', '') ?></div></td>
	</tr>
	<?php /*<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Neto Gravado 10.50</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->Iva10 * 100 / 10.5, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Neto Gravado 21.00 </div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->Iva21 * 100 / 21, 2, '.', '') ?></div></td>
	</tr> */ ?>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">IVA 21.00</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->Iva21, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">IVA 10.50</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->Iva10, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Retenci&oacute;n IVA</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format(0, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Percepcion IIBB</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->PercepcionIIBB, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">No Gravado</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->NoGra, 2, '.', '') ?></div></td>
	</tr>
	<tr>
		<td width="100" align="left" valign="top"><div align="left" class="Estilo2">Impuesto Interno</div></td>
		<td align="left" valign="top"><div align="left" class="Estilo2">:&nbsp;<?= number_format($oTotales->ImpuestoInterno, 2, '.', '') ?></div></td>
	</tr>
</table>
<?php
}
?>
</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('libro iva ventas.pdf', 'D'); 

?>