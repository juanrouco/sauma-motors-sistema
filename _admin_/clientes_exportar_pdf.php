<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CLIE_LIST))
	Session::NoPerm();

/* declaramos variables necesarias */
$oClientes 			= new Clientes();
$oTiposDocumento 	= new TiposDocumento();
$oTiposIva 			= new TiposIva();
$oProfesiones 		= new Profesiones();
$oEstadosCiviles 	= new EstadosCiviles();
$oUsuarios 			= new Usuarios();

/* obtenemos el filtro */
$filter	= ReceiveArray($_REQUEST['filter']);

/* obtenemos listado de clientes */
$arrClientes = $oClientes->GetAll($filter);

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

<?php if ($arrClientes) { ?>

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td align="center"><div align="center"><span class="Estilo4">LISTADO DE CLIENTES AL <?=date('d-m-Y')?></span></div></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<table width="1200" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <td class="BordeInf BordeDer"><div align="left" class="Estilo1">TIPO DE CLIENTE</div></td>
        <td class="BordeInf BordeDer" width="77"><div align="left" class="Estilo1"><div align="left">RAZON SOCIAL</div></div></td>
        <td class="BordeInf BordeDer" width="61"><div align="left" class="Estilo1"><div align="left">COD. AREA</div></div></td>
        <td class="BordeInf BordeDer" width="66"><div align="left" class="Estilo1"><div align="left">TELEFONO</div></div></td>
        <td class="BordeInf BordeDer" width="101"><div align="left" class="Estilo1"><div align="left">TIPO DOCUMENTO</div></div></td>
        <td class="BordeInf BordeDer" width="127"><div align="left" class="Estilo1"><div align="left">NUMERO DOCUMENTO</div></div></td>
        <td class="BordeInf BordeDer" width="111"><div align="left" class="Estilo1"><div align="left">FECHA NACIMIENTO</div></div></td>
        <td class="BordeInf BordeDer" width="64"><div align="left" class="Estilo1"><div align="left">EMPRESA</div></div></td>
        <td class="BordeInf BordeDer" width="58"><div align="left" class="Estilo1"><div align="left">CUIT/CUIL</div></div></td>
        <td class="BordeInf BordeDer" width="75"><div align="left" class="Estilo1"><div align="left">EMAIL</div></div></td>
        <td class="BordeInf BordeDer" width="74"><div align="left" class="Estilo1"><div align="left">VENDEDOR</div></div></td>
        <td class="BordeInf BordeDer" width="85"><div align="left" class="Estilo1"><div align="left">CONDICION IVA</div></div></td>
        <td class="BordeInf BordeDer" width="84"><div align="left" class="Estilo1"><div align="left">PROFESION</div></div></td>
        <td class="BordeInf BordeDer" width="87"><div align="left" class="Estilo1"><div align="left">ESTADO CIVIL</div></div></td>
    </tr>

	<?php foreach ($arrClientes as $oCliente) { ?>
        <?php $oTipoDocumento 	= $oTiposDocumento->GetById($oCliente->DocumentoTipo); ?>
        <?php $oTipoIva 		= $oTiposIva->GetById($oCliente->IdTipoIva); ?>
        <?php $oProfesion 		= $oProfesiones->GetById($oCliente->IdProfesion); ?>
        <?php $oEstadoCivil 	= $oEstadosCiviles->GetById($oCliente->IdEstadoCivil); ?>
        <?php $oUsuario 		= $oUsuarios->GetById($oCliente->IdVendedor); ?>

    <tr>
        <td width="100" align="left" valign="top"><div align="left" class="Estilo2"><?=PersonaTipos::GetById($oCliente->IdTipoPersona)?></div></td>
        <td width="77" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
        <td width="61" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->TelefonoCodigoArea)?></div></td>
        <td width="66" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->Telefono)?></div></td>
        <td width="101" align="left" valign="top"><div align="left" class="Estilo2"><?=$oTipoDocumento->Nombre?></div></td>
        <td width="127" align="left" valign="top"><div align="left" class="Estilo2"><?=$oCliente->DocumentoNumero?></div></td>
        <td width="111" align="left" valign="top"><div align="left" class="Estilo2"><?=CambiarFecha($oCliente->FechaNacimiento)?></div></td>
        <td width="64" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->Empresa)?></div></td>
        <td width="58" align="left" valign="top"><div align="left" class="Estilo2"><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></div></td>
        <td width="75" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oCliente->Email)?></div></td>
        <td width="74" align="left" valign="top"><div align="left" class="Estilo2"><?=utf8_encode($oUsuario->Nombre . ' ' . $oUsuario->Apellido)?></div></td>
        <td width="85" align="left" valign="top"><div align="left" class="Estilo2"><?=$oTipoIva->Nombre?></div></td>
        <td width="84" align="left" valign="top"><div align="left" class="Estilo2"><?=$oProfesion->Nombre?></div></td>
        <td width="87" align="left" valign="top"><div align="left" class="Estilo2"><?=$oEstadoCivil->Nombre?></div></td>
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
$oMpdf->Output('clientes.pdf', 'D'); 

?>