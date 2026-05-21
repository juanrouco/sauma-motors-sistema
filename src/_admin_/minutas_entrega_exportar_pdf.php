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
$oUbicaciones = new Ubicaciones();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de minutas */
$arrMinutas = $oMinutas->GetAll($filter);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4-L', 0, 'DejaVuSans', 15, 15, 16, 16, 9, 9, 'L');
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

<?php if ($arrMinutas) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE MINUTAS AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">CARPETA</div></td>
        <td class="BordeInf BordeDer" width="65"><div align="left" class="Estilo1"><div align="left">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="60"><div align="left" class="Estilo1"><div align="left">INT</div></div></td>
        <td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">NRO VIN</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">CLIENTE</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">VENDEDOR</div></div></td>
        <td class="BordeInf BordeDer" width="100"><div align="left" class="Estilo1"><div align="left">UBICACION</div></div></td>
    </tr>

	<?php foreach ($arrMinutas as $oMinuta) { ?>
        <?php $oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad); ?>
        <?php $oModelo 	= $oModelos->GetById($oUnidad->IdModelo); ?>
        <?php $oCliente	= $oClientes->GetById($oMinuta->IdCliente); ?>
        <?php $oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario); ?>
        <?php $oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion); ?>
        <?php $Rentabilidad = $oMinuta->PrecioVenta + $oMinuta->GastosFlete + $oMinuta->Circular - $oUnidad->ImporteCompraBruto; ?>

    <tr>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMinuta->IdMinuta?></div></td>
        <td width="65" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroVin?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oModelo->DenominacionComercial)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUsuario->Nombre . ', ' . $oUsuario->Apellido)?></div></td>
        <td width="100" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUbicacion->Nombre)?></div></td>
    </tr>

	<?php } ?>     

</table>

<?php } ?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('minutas.pdf', 'D'); 

?>