<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */
$oUsados 				= new Usados();
$oMarcas 				= new Marcas();
$oUbicaciones 			= new Ubicaciones();
$oColores 				= new Colores();
$oEstadosUnidad 		= new EstadosUnidad();

/* obtenemos el filtro */
$filter	= array();	
$filter['IdUnidadDesde'] 	= trim($_REQUEST['FilterIdUnidadDesde']);
$filter['IdUnidadHasta'] 	= trim($_REQUEST['FilterIdUnidadHasta']);	

/* obtenemos listado de unidades */
$arrUsados = $oUsados->GetAll($filter);

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
.Estilo4 {font-size: 14px; font-weight: bold;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
-->
</style>
</head>

<body>

<?php if ($arrUsados) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
   
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">PLANILLA DE ADMINISTRACION DE USADOS AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">NRO. INTERNO</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">MODELO</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">MARCA</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">NRO. CHASIS</div></td>
        <td class="BordeInf BordeDer" width="113" align="left"><div align="left" class="Estilo1">NRO. MOTOR</div></td>
        <td class="BordeInf BordeDer" width="64" align="left"><div align="left" class="Estilo1">COLOR</div></td>		
        <td class="BordeInf BordeDer" width="69" align="left"><div align="left" class="Estilo1">DOMNIO</div></td>
		<td class="BordeInf BordeDer" width="80" align="left"><div align="left" class="Estilo1">KILOMETRAJE</div></td>		
		<td class="BordeInf BordeDer" width="80" align="left"><div align="left" class="Estilo1">VALUACION</div></td>
    </tr>

	<?php 
	foreach ($arrUsados as $oUsado) 
	{
		$oMarca 				= $oMarcas->GetById($oUsado->IdMarca);
			$oColor 				= $oColores->GetById($oUsado->IdColor);
			$oUbicacion 			= $oUbicaciones->GetById($oUsado->IdUbicacion);
			$oEstadoUnidad 		= $oEstadosUnidad->GetById($oUsado->IdEstado);
			
	?>

    <tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUsado->IdUsado?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->Modelo?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMarca->Nombre?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->NumeroChasis?></div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->NumeroMotor?></div></td>
        <td class="BordeInf" width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
        <td class="BordeInf" width="69" align="left" valign="top"><div align="left" class="Estilo2"><?= $oUsado->Dominio ?></div></td>
        <td class="BordeInf" width="80" align="left" valign="top"><div align="left" class="Estilo2"><?= number_format($oUsado->Kilometraje, 0, ',', '.') ?>Km</div></td>
        <td class="BordeInf" width="80" align="left" valign="top"><div align="left" class="Estilo2">$<?= number_format($oUsado->Valuacion, 1, ',', '.') ?></div></td>
        
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
$oMpdf->Output('usados.pdf', 'D'); 

?>