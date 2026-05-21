<?php
ini_set('memory_limit', '20000M');

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */
$oUnidades 				= new Unidades();
$oClientes 				= new Clientes();
$oModelos 				= new Modelos();
$oUbicaciones 			= new Ubicaciones();
$oColores 				= new Colores();
$oEstadosUnidad 		= new EstadosUnidad();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);
$filter['FechaFacturaDesde']	= $_REQUEST['FechaFacturaDesde'];

/* obtenemos listado de unidades */
$arrUnidades = $oUnidades->GetAllOrdered($filter);

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
.Estilo4 {font-size: 12px; font-weight: bold;}
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
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
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE UNIDADES AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">NRO. INTERNO</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">MODELO</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">NRO. CHASIS</div></td>
        <td class="BordeInf BordeDer" width="113" align="left"><div align="left" class="Estilo1">NRO. MOTOR</div></td>
        <td class="BordeInf BordeDer" width="64" align="left"><div align="left" class="Estilo1">COLOR</div></td>
        <td class="BordeInf BordeDer" width="64" align="left"><div align="left" class="Estilo1">ESTADO</div></td>
        <td class="BordeInf BordeDer" width="64" align="left"><div align="left" class="Estilo1">UBICACION</div></td>
		
    </tr>

	<?php 
		foreach ($arrUnidades as $oUnidad) 
		{
			$oModelo 				= $oModelos->GetById($oUnidad->IdModelo);
			$oColor 				= $oColores->GetById($oUnidad->IdColor);
			$oUbicacion 			= $oUbicaciones->GetById($oUnidad->IdUbicacion);
			$oEstadoUnidad 		= $oEstadosUnidad->GetById($oUnidad->IdEstado);
			$oPlanillaRecepcion 	= $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
			$cliente = '';
			if ($oUnidad->IdClientePlan)
			{
				$oCliente = $oClientes->GetById($oUnidad->IdClientePlan);
				$cliente = $oCliente->RazonSocial;
			}
	?>

    <tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->DenominacionModelo?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroChasis?></div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroMotor?></div></td>
        <td class="BordeInf" width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
        <td class="BordeInf" width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=$oEstadoUnidad->Nombre?></div></td>
        <td class="BordeInf" width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUbicacion->Nombre?></div></td>
		   
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
$oMpdf->Output('unidades.pdf', 'D'); 

?>