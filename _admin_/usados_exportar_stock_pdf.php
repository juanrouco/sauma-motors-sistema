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
$oUsados 				= new Usados();
$oMarcas 				= new Marcas();
$oUbicaciones 			= new Ubicaciones();
$oColores 				= new Colores();
$oEstadosUnidad 		= new EstadosUnidad();
$oMinutas				= new Minutas();
$oMinutasUsados			= new MinutasUsados();
$oUsuarios				= new Usuarios();
$oRecepcionesUsados 	= new RecepcionesUsados();

/* obtenemos el filtro */
$filter	= array();
$filter['IdEstado'] = array();
$filter['IdEstado'] = EstadoUnidad::Stock;

/* obtenemos listado de unidades */
$arrUsados = $oUsados->GetAllOrdered($filter);

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
.BordeInf {border-bottom-width: 1px; border-bottom-style:solid; border-bottom-color:#FFFFFF;}
.BordeDer {border-right-width: 1px; border-right-style:solid; border-right-color:#FFFFFF;}

-->
</style>
</head>

<body>

<?php if ($arrUsados) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE USADOS POR ESTADO DE SITUACION AL <?=date('d-m-Y')?></span></div></td>
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
        <td class="BordeInf BordeDer" align="left"><div align="left" class="Estilo1">Marca</div></td>
		<td class="BordeInf BordeDer" align="left"><div align="left" class="Estilo1">Descripci&oacute;n del Usado</div></td>
		<td class="BordeInf BordeDer" align="left"><div align="left" class="Estilo1">A&ntilde;o</div></td>
        <td class="BordeInf BordeDer" width="60" align="left"><div align="center" class="Estilo1">Interno</div></td>
        <td class="BordeInf BordeDer" width="60" align="left"><div align="left" class="Estilo1">Dominio</div></td>  
        <td class="BordeInf BordeDer" width="60" align="left"><div align="left" class="Estilo1">Color</div></td>		
        <td class="BordeInf BordeDer" width="25" align="left"><div align="left" class="Estilo1">Fecha Recepci&oacute;n</div></td>
        <td class="BordeInf BordeDer" width="25" align="left"><div align="left" class="Estilo1">Ubicaci&oacute;n</div></td>
        <td class="BordeInf BordeDer" width="25" align="left"><div align="center" class="Estilo1">Precio</div></td>
        <td class="BordeInf BordeDer" width="150" align="left"><div align="left" class="Estilo1">Observaciones</div></td>
		
    </tr>

	<?php 
	foreach ($arrUsados as $oUsado) 
	{
		$oMarca 				= $oMarcas->GetById($oUsado->IdMarca);
        $oColor 				= $oColores->GetById($oUsado->IdColor);
        $oUbicacion 			= $oUbicaciones->GetById($oUsado->IdUbicacion);
        $oEstadoUnidad 			= $oEstadosUnidad->GetById($oUsado->IdEstado);
		$oRecepcionUsado = $oRecepcionesUsados->GetByIdUsado($oUsado->IdUsado);
        $CarpetaOrigen = '';
		$oMinuta = $oMinutas->GetByIdUsado($oUsado->IdUsado);
		if ($oMinuta)
			$CarpetaOrigen = $oMinuta->IdMinuta;
		else
		{
			$oMinutaUsado = $oMinutasUsados->GetByIdUsadoTomado($oUsado->IdUsado);
			$CarpetaOrigen = 'U-' . $oMinutaUsado->IdUsado;
		}
		$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario);
	?>

    <tr <?= $oUsado->Pisado ? 'bgColor="#ADECDF"' : '' ?>>
		<td width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMarca->Nombre?></div></td>
		<td width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->Modelo?></div></td>
		<td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->ModeloAnio?></div></td>
        <td width="60" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUsado->IdUsado?></div></td>
        <td width="60" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUsado->Dominio?></div></td>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
		<td width="25" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oRecepcionUsado->Fecha)?></div></td>
		<td width="25" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUbicacion->Nombre?></div></td>
		<td width="60" align="center" valign="top"><div align="center" class="Estilo2">$<?=number_format($oUsado->PrecioVenta, 0, ',', '.')?></div></td>
		<td width="150" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUsado->Pisado ? iconv(mb_detect_encoding($oUsado->Comentarios),"UTF-8//IGNORE",$oUsado->Comentarios) : ''?></div></td>
			
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
$oMpdf->Output('usados.pdf', 'D');

?>