<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST) && !Session::CheckPerm(PERM_UNID_STOCK))
	Session::NoPerm();

/* declaramos variables necesarias */
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oUbicaciones 			= new Ubicaciones();
$oColores 				= new Colores();
$oEstadosUnidad 		= new EstadosUnidad();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* obtenemos el filtro */
$filter	= array();
$filter['IdEstado'] = array();
$filter['IdEstado'][0] = EstadoUnidad::Stock;
$filter['IdEstado'][1] = EstadoUnidad::PreVenta;

/* obtenemos listado de unidades */
$arrUnidades = $oUnidades->GetAllOrdered($filter);

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
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#FFFFFF;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#FFFFFF;}

-->
</style>
</head>

<body>

<?php if ($arrUnidades) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO POR ESTADO DE SITUACION AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="left"><div align="left"><span class="Estilo4">ESTADO STOCK</span></div></td>
    </tr>
	<tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="left"><div align="left" class="Estilo1">Descripci&oacute;n de la Unidad</div></td>
        <td class="BordeInf BordeDer" width="20" align="left"><div align="center" class="Estilo1">Int</div></td>
        <td class="BordeInf BordeDer" width="40" align="left"><div align="left" class="Estilo1">Vin</div></td>
        <td class="BordeInf BordeDer" width="15" align="left"><div align="left" class="Estilo1">&nbsp;</div></td>        
        <td class="BordeInf BordeDer" width="100" align="left"><div align="left" class="Estilo1">Color</div></td>		
        <td class="BordeInf BordeDer" width="25" align="left"><div align="left" class="Estilo1">Ubicaci&oacute;n</div></td>
        <td class="BordeInf BordeDer" width="70" align="left"><div align="left" class="Estilo1">Contado</div></td>
        <td class="BordeInf BordeDer" width="70" align="left"><div align="left" class="Estilo1">Credito</div></td>
        <td class="BordeInf BordeDer" width="80" align="left"><div align="left" class="Estilo1">Observaciones</div></td>
		
    </tr>

	<?php 
	foreach ($arrUnidades as $oUnidad) 
	{
		$oModelo 				= $oModelos->GetById($oUnidad->IdModelo);
        $oColor 				= $oColores->GetById($oUnidad->IdColor);
        $oUbicacion 			= $oUbicaciones->GetById($oUnidad->IdUbicacion);
        $oEstadoUnidad 		= $oEstadosUnidad->GetById($oUnidad->IdEstado);
		$oPlanillaRecepcion 	= $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
		$CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : ''; 
	?>

    <tr <?= $oUnidad->Pisado ? 'bgColor="#ADECDF"' : '' ?>>
		<td width="120" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->DenominacionComercial?></div></td>
        <td width="20" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td width="40" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->NumeroVin?></div></td>
        <td width="15" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->Cancelada == '1' ? 'P' : ''?></div></td>
        <td width="100" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
		<td width="25" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUbicacion->Nombre?></div></td>
		<td width="70" align="left" valign="top"><div align="left" class="Estilo2">$<?= number_format($oModelo->Precio1, 0)?></div></td>
		<td width="70" align="left" valign="top"><div align="left" class="Estilo2">$<?= number_format($oModelo->Precio2, 0)?></div></td>
		<td width="80" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->Pisado || $oUnidad->Reparacion ? iconv(mb_detect_encoding($oUnidad->Comentarios),"UTF-8//IGNORE",$oUnidad->Comentarios) : ''?></div></td>
			
    </tr>
	<?php 
	} 
	?>
</table>

<?php } ?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('unidades.pdf', 'D');

?>