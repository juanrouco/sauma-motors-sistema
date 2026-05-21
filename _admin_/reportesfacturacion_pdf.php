<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_REPF_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdReporteFacturacion = intval($_REQUEST['IdReporteFacturacion']);

/* declaramos variables necesarias */
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oColores				= new Colores();
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oReportesFacturacion	= new ReportesFacturacion();
$oFacturaUnidades		= new FacturaUnidades();
$oComprobantes 			= new Comprobantes();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe la planilla */
if (!$oReporteFacturacion = $oReportesFacturacion->GetById($IdReporteFacturacion))
{	
	header("Location: reportesfacturacion.php" . $strParams);
	exit();
}

/* obtenemos el filtro */
$filter	= array();
$filter['IdReporteFacturacion']	= $IdReporteFacturacion;

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
.Estilo4 {font-size: 12px; font-weight: bold;}
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
    	<td align="center"><div align="center"><span class="Estilo4">REPORTE DE FACTURACION AL <?=date('d-m-Y')?></span></div></td>
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
        <td class="BordeInf BordeDer" width="292">
        	<table>
            	<tr>
                	<td class="BordeInf" colspan="3" align="center"><div align="center" class="Estilo1">DATOS DE FACTURACION</div></td>
               	</tr>
            	<tr>
                    <td width="95" align="left"><div align="left" class="Estilo1">FECHA</div></td>
                    <td width="79" align="left"><div align="left" class="Estilo1">COMPROBANTE</div></td>
                    <td width="118" align="center"><div align="center" class="Estilo1">IMPORTE</div></td>
               	</tr>
            </table>
        </td>
    </tr>

	<?php foreach ($arrUnidades as $oUnidad) { ?>
		<?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
        <?php $oColor = $oColores->GetById($oUnidad->IdColor); ?>
        <?php $oMinuta = $oMinutas->GetByUnidad($oUnidad); ?>
		<?php $oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion); ?>
        <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
        <?php $oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oMinuta->IdMinuta); ?>
        <?php $oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante); ?>
        <?php $Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oComprobante->Numero; ?>
		<?php $CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : ''; ?>

    <tr>
        <td class="BordeInf BordeDer" width="109" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td class="BordeInf BordeDer" width="184" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oModelo->DenominacionComercial)?></div></td>
        <td class="BordeInf BordeDer" width="175" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oColor->Nombre)?></div></td>
        <td class="BordeInf BordeDer" width="101" align="left" valign="top"><div align="left" class="Estilo2"><?=$CodigoLlaves?></div></td>
        <td class="BordeInf BordeDer" width="79" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroVin?></div></td>
        <td class="BordeInf BordeDer" width="254" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td class="BordeInf" width="292">
        	<table>
            	<tr>
                    <td width="95" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oFacturaUnidad->Fecha)?></div></td>
                    <td width="79" align="left" valign="top"><div align="left" class="Estilo2"><?=$Comprobante?></div></td>
                    <td width="118" align="center" valign="top"><div align="center" class="Estilo2">$ <?=number_format($oFacturaUnidad->Total, 2)?></div></td>
               	</tr>
            </table>
        </td>
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