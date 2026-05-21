<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
	Session::NoPerm();
	
$IdMinutaPago = intval($_REQUEST['IdMinutaPago']);

/* declaramos variables necesarias */
$oMinutasPago 	= new MinutasPago();
$oMinutasPagoItems 	= new MinutasPagoItems();
$oUnidades 		= new Unidades();
$oModelos 	= new Modelos();
$oClientes 	= new Clientes();
$oUsuarios 	= new Usuarios();

$oMinutaPago = $oMinutasPago->GetById($IdMinutaPago);

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de minutas */

$arrData = $oMinutasPagoItems->GetAllByIdMinutaPagoOrdered($IdMinutaPago);

$TotalAPagar = 0;
foreach ($arrData as $oMinutaPagoItem)
{
	$TotalAPagar += $oMinutaPagoItem->Importe;
}

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4', 0, 'DejaVuSans', 15, 15, 16, 16, 9, 9, 'L');
$oMpdf->watermarkText = '';
$oMpdf->setFooter('{PAGENO}');
$oMpdf->SetHTMLFooter('<table align="right"><tr><td>{PAGENO}</td></tr></table>');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
.Estilo1 {font-size: 12px; font-weight: bold;}
.Estilo2 {font-size: 11px}
.Estilo3 {font-size: 11px; color:#FFFFFF;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
-->
</style>
</head>

<body>

<?php if ($arrData) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4"><strong>MINUTA DE PAGO</strong></span></div></td>
    </tr>
    <tr>
    	<td align="right"><?= CambiarFecha($oMinutaPago->Fecha) ?></td>
    </tr>
	<tr>
    	<td>SALDO DISPONIBLE $<?= number_format($oMinutaPago->MontoDisponible, 2, '.', ',') ?></td>
    </tr>
	<tr>
    	<td>A PAGAR $<?= number_format($TotalAPagar, 2, '.', ',') ?></td>
    </tr>
	<tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" width="60"><div align="center" class="Estilo1"><div align="center">INTERNO</div></div></td>
        <td class="BordeInf BordeDer" width="100"><div align="center" class="Estilo1"><div align="center">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="100"><div align="center" class="Estilo1"><div align="center">IMPORTE</div></div></td>
        <td class="BordeInf BordeDer" width="100"><div align="center" class="Estilo1"><div align="center">PAGO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">SALDO</div></div></td>
    </tr>

	<?php $saldo = 0;
	foreach ($arrData as $oMinutaPagoItem) { ?>
        <?php $oUnidad 	= $oUnidades->GetById($oMinutaPagoItem->IdUnidad); ?>
        <?php $saldo 	+= $oMinutaPagoItem->Saldo; ?>

    <tr>
        <td width="60" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?=CambiarFecha($oUnidad->FechaFacturaCompra)?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2">$<?=number_format($oUnidad->ImporteNotaCredito, 2)?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2">$<?=number_format($oMinutaPagoItem->Importe, 2)?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2">&nbsp;</div></td>
    </tr>

	<?php } ?>  
		

</table>
Saldo: $ <?= number_format($saldo, 2) ?>
<?php } ?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('minutas.pdf', 'D'); 

?>