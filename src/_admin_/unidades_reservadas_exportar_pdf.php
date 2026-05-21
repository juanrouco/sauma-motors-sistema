<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_REPORT))
	Session::NoPerm();

/* declaramos variables necesarias */
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oMinutas 				= new Minutas();
$oColores 				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oUsuarios 				= new Usuarios();
$oClientes				= new Clientes();
$oEstadosUnidad 		= new EstadosUnidad();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oMinutasEspera			= new MinutasEspera();

$TotalReservado = 0;
$TotalEspera = 0;

/* obtenemos el filtro */
$filter	= array();	
$filter['FechaMinutaDesde'] 	= trim($_REQUEST['FilterFechaDesde']);
$filter['FechaMinutaHasta'] 	= trim($_REQUEST['FilterFechaHasta']);	
$filter['IdEstado'] 			= EstadoUnidad::Reservado;

/* obtenemos listado de unidades */
$arrUnidades = $oUnidades->GetAllOrdered($filter);

$filterEspera	= array();	
$filterEspera['FechaMinutaDesde'] 	= trim($_REQUEST['FilterFechaDesde']);
$filterEspera['FechaMinutaHasta'] 	= trim($_REQUEST['FilterFechaHasta']);

/* obtenemos listado de minutas en espera */
$arrMinutasEspera = $oMinutasEspera->GetAll($filterEspera);

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
    	<td align="center"><div align="center"><span class="Estilo4">PLANILLA DE UNIDADES RESERVADAS AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">FECHA MINUTA</div></td>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">NRO. INTERNO</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">MODELO</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">Cliente</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">VENDEDOR</div></td>
        <td class="BordeInf BordeDer" width="113" align="left"><div align="left" class="Estilo1">PAGOS</div></td>
    </tr>

	<?php 
	foreach ($arrUnidades as $oUnidad) 
	{
		if ($oMinuta 				= $oMinutas->GetById($oUnidad->IdUnidad))
		{
			$oModelo 				= $oModelos->GetById($oUnidad->IdModelo);
			$oUsuario				= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oCliente				= $oClientes->GetById($oMinuta->IdCliente);
			$TotalReservado += $oMinuta->GetTotalAcreditado();
	?>

    <tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
        <td class="BordeInf" width="80" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->DenominacionComercial?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsuario->Nombre . ' ' . $oUsuario->Apellido?></div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2">$<?=number_format($oMinuta->GetTotalAcreditado(), 2, ',', '.')?></div></td>
    </tr>

	<?php 
		}
	} 
	?>     
	<tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="80" align="center" valign="top"><div align="center" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2">$<?=number_format($TotalReservado, 2, ',', '.')?></div></td>
    </tr>
</table>

<?php } ?>

<?php if ($arrMinutasEspera) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">PLANILLA DE MINUTAS EN ESPERA AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">FECHA MINUTA</div></td>
        <td class="BordeInf BordeDer" align="center"><div align="center" class="Estilo1">NRO. MINUTA</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">MODELO</div></td>
        <td class="BordeInf BordeDer" width="116" align="left"><div align="left" class="Estilo1">COLOR</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">CLIENTE</div></td>
        <td class="BordeInf BordeDer" width="115" align="left"><div align="left" class="Estilo1">VENDEDOR</div></td>
        <td class="BordeInf BordeDer" width="113" align="left"><div align="left" class="Estilo1">PAGOS</div></td>
    </tr>

	<?php 
	foreach ($arrMinutasEspera as $oMinuta) 
	{
		$oModelo 				= $oModelos->GetById($oMinuta->IdModelo);
		$oColor 				= $oColores->GetById($oMinuta->IdColor);
		$oUsuario				= $oUsuarios->GetById($oMinuta->IdUsuario);
		$oCliente				= $oClientes->GetById($oMinuta->IdCliente);
		$TotalEspera += $oMinuta->Anticipo;
	?>

    <tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
        <td class="BordeInf" width="80" align="center" valign="top"><div align="center" class="Estilo2"><?=$oMinuta->IdMinutaEspera?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->DenominacionComercial?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsuario->Nombre . ' ' . $oUsuario->Apellido?></div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2">$<?=number_format($oMinuta->Anticipo, 2, ',', '.')?></div></td>
    </tr>

	<?php } ?>     
	<tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="80" align="center" valign="top"><div align="center" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2">&nbsp;</div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2">$<?=number_format($TotalEspera, 2, ',', '.')?></div></td>
    </tr>
</table>

<?php } ?>
<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div><span class="Estilo4">TOTAL PAGOS $<?=number_format($TotalEspera + $TotalReservado, 2, ',', '.')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('unidades reservadas.pdf', 'D'); 

?>