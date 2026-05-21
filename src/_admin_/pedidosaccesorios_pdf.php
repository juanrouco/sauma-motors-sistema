<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_NONR_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPedido	= intval($_REQUEST['IdPedido']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= 0;
$oPedidosAccesorios			= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oMinutas 					= new Minutas();
$oClientes 					= new Clientes();
$oUsuarios 					= new Usuarios();
$oTiposDocumento 			= new TiposDocumento();
$oTiposIva 					= new TiposIva();
$oUnidades 					= new Unidades();
$oModelos 					= new Modelos();
$oMarcas 					= new Marcas();
$oLocalidades 				= new Localidades();
$oColores 					= new Colores();
$oMarcas 					= new Marcas();
$oTiposModelo 				= new TiposModelo();
$oPlanillasRecepcion 		= new PlanillasRecepcion();

/* obtenemos los datos de la orden */
if (!$oPedidoAccesorios = $oPedidosAccesorios->GetById($IdPedido))
	exit();

$arrPedidosAccesoriosItems = $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($oPedidoAccesorios);

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oPedidoAccesorios->IdMinuta))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos del tipo de documento */
$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
	exit();

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
	exit();

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
	exit();

/* obtenemos la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();

/* obtenemos la marca del motor */
if (!$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor))
	exit();

/* obtenemos la marca del chasis */
if (!$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis))
	exit();

/* obtenemos los datos de la marca */
if (!$oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
	exit();

/* obtenemos los datos de la planilla de recepcion */
$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);

/* determinamos el codigo de llaves si la planilla de recepcion se encuentra aprobada */
$CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : '';

/* obtenemos los datos del vendedor */
$oUsuario = $oUsuarios->GetById($oCliente->IdVendedor);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
$oMpdf->watermarkText = '';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<style>
body {
	background-color: #FFFFFF;
}
td {
	font-size: 14px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
.bordeBottom {
	border-bottom: 2px solid #000000;
}
.textoPie {
	font-size: 11px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
</style>

</head>
<body>

<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td height="100" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td align="center"><img src="images/logo_tolosa.jpg" width="350" height="82" /></td>
                                </tr>
                                <tr>
	                                <td class="bordeBottom" width="700"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="60" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center">PEDIDO  DE  ACCESORIOS</div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="19%" height="30">Orden N&deg;:</td>
                                    <td width="38%"><?=$oUnidad->IdUnidad?></td>
                                    <td width="31%" colspan="2" align="right"><div align="right">Olivos, <?=date('d', strtotime($oPedidoAccesorios->Fecha))?> de <?=Meses::GetById(date('m', strtotime($oPedidoAccesorios->Fecha)))?> de <?=date('Y', strtotime($oPedidoAccesorios->Fecha))?></div></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Codigo de Llaves:</td>
                                    <td><?=utf8_decode($CodigoLlaves)?></td>
                                    <td>Nro. Stock:</td>
                                    <td width="31%"><?=$oUnidad->IdUnidad?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Marca:</td>
                                    <td><?=utf8_decode($oMarcaVehiculo->Nombre)?></td>
                                    <td>Tipo:</td>
                                    <td width="31%"><?=utf8_decode($oTipoModelo->Nombre)?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Modelo:</td>
                                    <td><?=utf8_decode($oModelo->DenominacionModelo)?></td>
                                    <td>Color:</td>
                                    <td width="31%"><?=utf8_decode($oColor->Nombre)?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Dominio N&deg;:</td>
                                    <td><?=utf8_decode($oUnidad->Patente)?></td>
                                    <td>Motor N&deg;:</td>
                                    <td width="31%"><?=utf8_decode($oUnidad->NumeroMotor)?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Chasis N&deg;:</td>
                                    <td><?=utf8_decode($oUnidad->NumeroVinPrefijo . $oUnidad->NumeroVin)?></td>
                                    <td>A&ntilde;o:</td>
                                    <td width="31%"><?=utf8_decode($oModelo->Anio)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="36%">Apellido y Nombre del Adquiriente:</td>
                                    <td width="64%"><?=utf8_decode($oCliente->RazonSocial)?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=utf8_decode($oCliente->DomicilioCalle) . ' ' . utf8_decode($oCliente->DomicilioNumero)?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=utf8_decode($oLocalidad->Nombre)?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="24%">Tipo de Documento:</td>
                                    <td width="48%"><?=$oTipoDocumento->Nombre?></td>
                                    <td width="6%">Nro.:</td>
                                    <td width="22%"><?=$oCliente->DocumentoNumero?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="40">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="12%">Vendedor:</td>
                                    <td width="88%"><?=utf8_decode($oUsuario->Nombre . ' ' . $oUsuario->Apellido)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                    	<p>
                                        Comentarios:
                                        <br /><br />
                                        <?=utf8_decode(strip_tags($oPedidoAccesorios->Accesorios))?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                    	<p>
                                        Accesorios:</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" width="450"><strong>Item</strong></td>
									<td width="10">&nbsp;</td>
                                    <td width="200" align="center"><strong>Importe</strong></td>
                                </tr>
								<?php
									if ($arrPedidosAccesoriosItems)
									{
										foreach ($arrPedidosAccesoriosItems as $oPedidoAccesorioItem)
										{
								?>
								<tr class="bordeGris">
									<td><div id="margen"><?= utf8_decode(strip_tags($oPedidoAccesorioItem->Detalle)) ?></div></td>
									<td width="10">&nbsp;</td>
									<td width="200" align="center">$<?=number_format($oPedidoAccesorioItem->Importe, 2)?></td>
								</tr>
								<?php
										}
									}
								?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td height="50">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="90" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                	<td width="42%">__________________________________</td>
                                    <td width="15%">&nbsp;</td>
                                	<td width="43%">__________________________________</td>
                                </tr>
                                <tr>
                                    <td width="42%" align="center"><div align="center">Por la Consecionaria</div></td>
                                    <td>&nbsp;</td>
                                    <td width="43%" align="center"><div align="center"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="100">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td class="bordeBottom" width="700"></td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td align="center"><div align="center"><span class="textoPie">AV. DEL LIBERTADOR 2275 - OLIVOS - C.P.: 1636 Tel: 011-4794-6833 - Email: ventas@saumamotors.com.ar</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
          	</div>
       	</td>
   	</tr>
</table>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('pedido_accesorios.pdf', 'D'); 

?>