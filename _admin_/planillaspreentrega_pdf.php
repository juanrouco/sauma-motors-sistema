<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_IPRE_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oColores				= new Colores();
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oOrdenesSalida 		= new OrdenesSalida();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de undiades */
$arrUnidades = $oUnidades->GetAll($filter);

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
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#000000;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#000000;}
-->
</style>
</head>

<body>

<?php if ($arrUnidades) { ?>

<table width="794" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">INFORME DE PREENTREGA AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="794" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">NRO. INTERNO</div></td>
        <td class="BordeInf BordeDer" width="184"><div align="left" class="Estilo1"><div align="left">MODELO</div></div></td>
        <td class="BordeInf BordeDer" width="175"><div align="left" class="Estilo1"><div align="left">COLOR</div></div></td>
        <td class="BordeInf BordeDer" width="101"><div align="left" class="Estilo1"><div align="left">LLAVE</div></div></td>
        <td class="BordeInf BordeDer" width="79"><div align="left" class="Estilo1"><div align="left">NRO. VIN</div></div></td>
        <td class="BordeInf BordeDer" width="254"><div align="left" class="Estilo1"><div align="left">CLIENTE</div></div></td>
        <td class="BordeInf BordeDer" width="85" align="center"><div align="center" class="Estilo1"><div align="center">FECHA RETIRO</div></div></td>
    </tr>

	<?php foreach ($arrUnidades as $oUnidad) { ?>
        <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
        <?php $oColor = $oColores->GetById($oUnidad->IdColor); ?>
        <?php $oMinuta = $oMinutas->GetByUnidad($oUnidad); ?>
        <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
		<?php $oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion); ?>
		<?php $CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : ''; ?>
        <?php
        /* si la unidad posee una orden de salida asociada, entonces la salteamos */
        if ($oOrdenesSalida->GetByIdMinuta($oMinuta->IdMinuta))
            continue;
		?>

    <tr>
        <td width="109" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td width="184" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oModelo->DenominacionComercial)?></div></td>
        <td width="175" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oColor->Nombre)?></div></td>
        <td width="101" align="left" valign="top"><div align="left" class="Estilo2"><?=$CodigoLlaves?></div></td>
        <td width="79" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroVin?></div></td>
        <td width="254" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td width="85" align="center" valign="top"><div align="center" class="Estilo2"><?=CambiarFecha($oUnidad->FechaRetiro)?></div></td>
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
$oMpdf->Output('planilla_lavado.pdf', 'D'); 

?>