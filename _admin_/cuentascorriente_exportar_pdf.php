<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */
$oMinutas 	= new Minutas();
$oUnidades 	= new Unidades();
$oModelos 	= new Modelos();
$oClientes 	= new Clientes();
$oUsuarios 	= new Usuarios();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de minutas */
$arrMinutas = $oMinutas->GetAllConSaldo($filter);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4-L', 0, 'DejaVuSans', 15, 15, 16, 16, 9, 9, 'L');
$oMpdf->watermarkText = '';
$oMpdf->setFooter('{PAGENO}');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
.Estilo1 {font-size: 10px; font-weight: bold;}
.Estilo2 {font-size: 9px}
.Estilo3 {font-size: 9px; color:#FFFFFF;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
-->
</style>
</head>

<body>

<?php if ($arrMinutas) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE MINUTAS CON SALDO AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">NRO INTERNO</div></div></td>
        <td class="BordeInf BordeDer" width="100"><div align="left" class="Estilo1"><div align="left">NRO VIN</div></div></td>
        <td class="BordeInf BordeDer" width="120"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="169"><div align="left" class="Estilo1"><div align="left">CLIENTE</div></div></td>
        <td class="BordeInf BordeDer" width="190"><div align="left" class="Estilo1"><div align="left">VENDEDOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">PRECIO DE VENTA</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">FLETE</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">CIRCULAR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">ACCESORIOS</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="100"><div align="center" class="Estilo1"><div align="center">SALDO</div></div></td>
    </tr>

	<?php
	$SaldoTotal = 0;
	foreach ($arrMinutas as $oMinuta) { ?>
        <?php $oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad); ?>
        <?php $oModelo 	= $oModelos->GetById($oUnidad->IdModelo); ?>
        <?php $oCliente	= $oClientes->GetById($oMinuta->IdCliente); ?>
        <?php $oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario); ?>
        <?php $SaldoTotal += $oMinuta->Saldo; ?>

    <tr>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td width="100" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroVin?></div></td>
        <td width="120" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oModelo->DenominacionComercial)?></div></td>
        <td width="169" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td width="190" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUsuario->Nombre . ', ' . $oUsuario->Apellido)?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $oMinuta->PrecioVenta?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $oMinuta->GastosFlete?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $oMinuta->Circular?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $oMinuta->GetTotalAccesorios()?></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $oMinuta->Saldo?></div></td>
    </tr>

	<?php } ?> 
		<tr>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="100" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="120" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="169" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="190" align="left" valign="top"><div align="left" class="Estilo2"></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"></div></td>
        <td width="100" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . $SaldoTotal?></div></td>
    </tr>

</table>

<?php } ?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('minutas con saldo.pdf', 'D'); 

?>