<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GESCUE_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */

$oCuentasGestoria 	= new CuentasGestoriaUsados();
$oMinutas 			= new MinutasUsados();
$oUsados 			= new Usados();
$oGestores			= new Gestores();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);
$Fecha = strval($_REQUEST['Fecha']);

/* obtenemos listado de minutas */
$arrData = $oCuentasGestoria->GetAllByFechaOrdered($Fecha);

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

<?php if ($arrData) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE GESTORIAS DEL <?=$Fecha ?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">NRO. CARPETA</div></td>
        <td class="BordeInf BordeDer" width="65"><div align="left" class="Estilo1"><div align="left">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">GESTOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">TRANSFERENCIA</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">GASTO GESTOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="90"><div align="center" class="Estilo1"><div align="center">TOTAL</div></div></td>
    </tr>

	<?php 
		$Total = 0;
		foreach ($arrData as $oCuentaGestoria) 
		{
			$oMinuta 	= $oMinutas->GetById($oCuentaGestoria->IdMinuta);
			$oUsado 	= $oUsados->GetById($oMinuta->IdUsado);
			$oGestor	= $oGestores->GetById($oCuentaGestoria->IdGestor);
			$Total += $oCuentaGestoria->TotalCalculado;
	?>

    <tr>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMinuta->IdMinuta?></div></td>
        <td width="65" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oCuentaGestoria->Fecha)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUsado->Modelo)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oGestor->RazonSocial)?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->PatentamientoCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->PrendaCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->TotalCalculado, 2, ',', '')?></div></td>
    </tr>

	<?php } ?>     
	<tr>
        <td colspan="5">&nbsp;</td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><strong>TOTAL</strong></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($Total, 2, ',', '')?></div></td>
    </tr>
</table>

<?php } ?>
<pagebreak>

<?php if ($arrData) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE GESTORIAS DEL <?= $Fecha ?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">NRO. CARPETA</div></td>
        <td class="BordeInf BordeDer" width="65"><div align="left" class="Estilo1"><div align="left">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">GESTOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">TRANSFERENCIA</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="90"><div align="center" class="Estilo1"><div align="center">TOTAL</div></div></td>
    </tr>

	<?php 
		$Total = 0;
		$IdGestor = 0;
		foreach ($arrData as $oCuentaGestoria) 
		{
			if ($IdGestor != $oCuentaGestoria->IdGestor)
			{
				
				if ($IdGestor != 0)
				{
	?>
	<tr>
        <td colspan="4">&nbsp;</td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><strong>TOTAL</strong></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($Total, 2, ',', '')?></div></td>
    </tr>
</table>
<pagebreak>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
    
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE GESTORIAS DEL <?= $Fecha ?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">NRO. CARPETA</div></td>
        <td class="BordeInf BordeDer" width="65"><div align="left" class="Estilo1"><div align="left">FECHA</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">GESTOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">TRANSFERENCIA</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="90"><div align="center" class="Estilo1"><div align="center">TOTAL</div></div></td>
    </tr>
	<?php
			}
				$IdGestor = $oCuentaGestoria->IdGestor;
				$Total = 0;
			}
			$oMinuta 	= $oMinutas->GetById($oCuentaGestoria->IdMinuta);
			$oUsado 	= $oUsados->GetById($oMinuta->IdUsado);
			$oGestor	= $oGestores->GetById($oCuentaGestoria->IdGestor);
			$Total += $oCuentaGestoria->TotalCalculado;
	?>

    <tr>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMinuta->IdMinuta?></div></td>
        <td width="65" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oCuentaGestoria->Fecha)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUsado->Modelo)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oGestor->RazonSocial)?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->PatentamientoCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->PrendaCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->AltaCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->SelladoCalculado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oCuentaGestoria->TotalCalculado, 2, ',', '')?></div></td>
    </tr>

	<?php } ?>     
	<tr>
        <td colspan="7">&nbsp;</td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><strong>TOTAL</strong></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($Total, 2, ',', '')?></div></td>
    </tr>
</table>

<?php } ?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('gestorias.pdf', 'D'); 

?>