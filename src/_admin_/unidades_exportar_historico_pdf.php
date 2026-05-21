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
$oUnidades 				= new Unidades();
$oClientes 				= new Clientes();
$oModelos 				= new Modelos();
$oUbicaciones 			= new Ubicaciones();
$oColores 				= new Colores();
$oEstadosUnidad 		= new EstadosUnidad();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* obtenemos el filtro */
$filter	= array();	
$filter['IdUnidadDesde'] 	= trim($_REQUEST['FilterIdUnidadDesde']);
$filter['IdUnidadHasta'] 	= trim($_REQUEST['FilterIdUnidadHasta']);	

/* obtenemos listado de unidades */
$arrUnidades = $oUnidades->GetAllOrderedById($filter);

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

<?php if ($arrUnidades) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
   
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">PLANILLA DE ADMINISTRACION DE UNIDADES AL <?=date('d-m-Y')?></span></div></td>
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
        <td class="BordeInf BordeDer" width="84" align="left"><div align="left" class="Estilo1">CLIENTE</div></td>
		<td class="BordeInf BordeDer" width="127" align="left"><div align="left" class="Estilo1">FECHA ARRIBO</div></td>		
        <td class="BordeInf BordeDer" width="83" align="center"><div align="center" class="Estilo1">CANC.</div></td>
		<td class="BordeInf BordeDer" width="69" align="center"><div align="center" class="Estilo1">CERT.</div></td>
		<td class="BordeInf BordeDer" width="292">			
        	<table>
            	<tr>
                	<td colspan="3" align="center"><div align="center" class="Estilo1">DATOS DE PROCEDENCIA</div></td>
               	</tr>
            	<tr>
                    <td width="95" align="left"><div align="left" class="Estilo1">NRO. FACT.</div></td>
                    <td width="79" align="left"><div align="left" class="Estilo1">FECHA</div></td>
                    <td width="118" align="center"><div align="center" class="Estilo1">IMPORTE</div></td>
               	</tr>
            </table>					
        </td>
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
				if ($oUnidad->IdEstado == EstadoUnidad::Plan)
					$cliente.= ' - <strong>PA</strong>';
				elseif ($oUnidad->IdEstado == EstadoUnidad::VentasEspeciales)
					$cliente.= ' - <strong>VE</strong>';
			}
	?>

    <tr>
        <td class="BordeInf" width="111" align="center" valign="top"><div align="center" class="Estilo2"><?=$oUnidad->IdUnidad?></div></td>
        <td class="BordeInf" width="116" align="left" valign="top"><div align="left" class="Estilo2"><?=$oModelo->DenominacionComercial?></div></td>
        <td class="BordeInf" width="115" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroChasis?></div></td>
        <td class="BordeInf" width="113" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroMotor?></div></td>
        <td class="BordeInf" width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=$oColor->Nombre?></div></td>
        <td class="BordeInf" width="84" align="left" valign="top"><div align="left" class="Estilo2"><?= utf8_encode($cliente) ?></div></td>
        <td class="BordeInf" width="127" align="left" valign="top">&nbsp;</td>		
        <td class="BordeInf" width="83" align="center" valign="top">&nbsp;</td>		
        <td class="BordeInf" width="69" align="center" valign="top">&nbsp;</td>
        <td class="BordeInf" width="292">
        	<table>
            	<tr>
                    <td width="95" align="left" valign="top"><div align="left" class="Estilo2"><?=$oUnidad->NumeroFacturaCompra?></div></td>
                    <td width="79" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oUnidad->FechaFacturaCompra)?></div></td>					
                    <td width="118" align="center" valign="top"><div align="center" class="Estilo2"><?=number_format($oUnidad->ImporteCompraBruto, 2)?></div></td>
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
$oMpdf->Output('unidades.pdf', 'D'); 

?>