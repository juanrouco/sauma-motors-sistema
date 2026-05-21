<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */

/* declaramos variables necesarias */
$oModelos 			= new Modelos();
$oTiposModelo 		= new TiposModelo();
$oCategoriasModelo 	= new CategoriasModelo();
$oMarcas 			= new Marcas();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de modelos */
$arrModelos = $oModelos->GetAll($filter);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('', 'A4', 0, 'DejaVuSans', 15, 15, 16, 16, 9, 9, 'L');
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
.Estilo4 {font-size: 12px; font-weight: bold;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
-->
</style>
</head>

<body>

<?php if ($arrModelos) { ?>

<table width="950" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE PRECIOS AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="950" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" width="65"><div align="left" class="Estilo1"><div align="left">MARCA</div></div></td>
        <td class="BordeInf BordeDer" width="125"><div align="left" class="Estilo1">
          <div align="left">DENOM. COMERCIAL</div></div></td>
        <td class="BordeInf BordeDer" width="34"><div align="left" class="Estilo1"><div align="left"><?= utf8_encode('A&Ntilde;O') ?></div></div></td>
        <td class="BordeInf BordeDer" width="34"><div align="left" class="Estilo1"><div align="left">CONTADO</div></div></td>
        <td class="BordeInf BordeDer" width="34"><div align="left" class="Estilo1"><div align="left">CREDITO</div></div></td>
    </tr>

	<?php foreach ($arrModelos as $oModelo) { ?>
        <?php $oTipoModelo 		= $oTiposModelo->GetById($oModelo->IdTipoModelo); ?>
        <?php $oCategoriaModelo = $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo); ?>
        <?php $oMarcaMotor 		= $oMarcas->GetById($oModelo->IdMarcaMotor); ?>
        <?php $oMarcaChasis 	= $oMarcas->GetById($oModelo->IdMarcaChasis); ?>
        <?php $oMarcaVehiculo 	= $oMarcas->GetById($oModelo->IdMarcaVehiculo); ?>

    <tr>
        <td width="125" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oModelo->DenominacionComercial)?></div></td>
        <td width="98" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oMarcaVehiculo->Nombre)?></div></td>
        <td width="48" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->Anio?></div></td>
        <td width="80" align="left" valign="top"><div align="left" class="Estilo2">$<?=$oModelo->Precio1?></div></td>
        <td width="74" align="left" valign="top"><div align="left" class="Estilo2">$<?=$oModelo->Precio2?></div></td>
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
$oMpdf->Output('modelos.pdf', 'D'); 

?>