<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CPRE_REPORT))
	Session::NoPerm();

/* declaramos variables necesarias */
$arrData 			= array();
$oContratosPrendas 	= new ContratosPrendas();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oAcreedores		= new Acreedores();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de minutas */
$arrData 	= $oContratosPrendas->GetAll($filter);

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
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE CONTRATOS DE PRENDAS AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">CARPETA</div></td>
        <td class="BordeInf BordeDer" width="80"><div align="left" class="Estilo1"><div align="left">NRO CONTRATO</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">CLIENTE</div></div></td>
        <td class="BordeInf BordeDer" width="140"><div align="left" class="Estilo1"><div align="left">ACREEDOR</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">MONTO SOLICITADO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">GASTO PRENDARIO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="90"><div align="center" class="Estilo1"><div align="center">COSTO SUBSIDIO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="90"><div align="center" class="Estilo1"><div align="center">COMISION</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">MONTO OTORGADO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">MONTO ACREDITADO</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">FECHA LIQUID.</div></div></td>
        <td class="BordeInf BordeDer" align="center" width="120"><div align="center" class="Estilo1"><div align="center">UTILIDAD</div></div></td>
    </tr>

	<?php 
		foreach ($arrData as $oContratoPrenda) 
		{ 
			$oMinuta = $oMinutas->GetById($oContratoPrenda->IdMinuta);
			$oCliente = $oClientes->GetById($oMinuta->IdCliente);
			$cliente = $oCliente->RazonSocial;
			$oAcreedor = $oAcreedores->GetById($oContratoPrenda->IdAcreedor);
			if ($oMinuta->Condominio)
			{
				$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
				$cliente.= " / " . $oClienteCondominio->RazonSocial;
			}
		?>
    <tr>
        <td width="60" align="left" valign="top"><div align="left" class="Estilo2"><?=$oMinuta->IdMinuta?></div></td>
        <td width="65" align="left" valign="top"><div align="left" class="Estilo2"><?=$oContratoPrenda->NumeroContrato?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($cliente)?></div></td>
        <td width="140" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oAcreedor->RazonSocial)?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->MontoSolicitado, 2, ',', '')?></div></td>
        <td width="90" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->GastoOtorgamiento, 2, ',', '')?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->CostoOtorgamiento, 2, ',', '')?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->Comision, 2, ',', '')?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->MontoOtorgado, 2, ',', '')?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->MontoAcreditado, 2, ',', '')?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?=CambiarFecha($oContratoPrenda->FechaLiquidacion)?></div></td>
        <td width="120" align="center" valign="top"><div align="center" class="Estilo2"><?='$ ' . number_format($oContratoPrenda->Resultado, 2, ',', '')?></div></td>
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
$oMpdf->Output('contratos prenda.pdf', 'D'); 

?>