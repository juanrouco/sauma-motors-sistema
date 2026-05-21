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
$IdNota	= intval($_REQUEST['IdNota']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oNotasNoRodamiento	= new NotasNoRodamiento();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oMarcas 			= new Marcas();
$oLocalidades 		= new Localidades();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oDatosEmpresa	= new DatosEmpresa();

$oDatoEmpresa = $oDatosEmpresa->GetAll();

/* obtenemos los datos de la orden */
if (!$oNotaNoRodamiento = $oNotasNoRodamiento->GetById($IdNota))
	exit();

/* obtenemos los datos de la venta */
$oMinuta = $oMinutas->GetById($oNotaNoRodamiento->IdUnidad);

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oNotaNoRodamiento->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oNotaNoRodamiento->IdUnidad))
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
                                	<td align="left"><img src="images/logo_tolosa.jpg" width="251" height="48" /></td>
                                </tr>
                                <tr>
	                                <td class="bordeBottom" width="700"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="72%">&nbsp;</td>
                                    <td width="28%" align="right"><div align="right">Olivos, <?=date('d', strtotime($oNotaNoRodamiento->Fecha))?> de <?=Meses::GetById(date('m', strtotime($oNotaNoRodamiento->Fecha)))?> de <?=date('Y', strtotime($oNotaNoRodamiento->Fecha))?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center">NOTA  NO  RODAMIENTO</div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>A quien corresponda:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="80%">&nbsp;</td>
                                    <td width="20%"><div align="right">Cliente: <?=utf8_encode($oCliente->RazonSocial)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>De nuestra mayor consideraci&oacute;n:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="237">&nbsp;</td>
                                    <td width="403">Nos dirigimos a Uds. a los efectos de certificar que el veh&iacute;culo</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>OKM/<?=$oModelo->Anio?> Marca: <strong><?=$oMarcaVehiculo->Nombre?></strong> Modelo: <strong><?=$oModelo->DenominacionComercial?></strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>Motor Marca: <strong><?=$oMarcaMotor->Nombre?></strong> Motor N&deg;: <strong><?=$oUnidad->NumeroMotor?></strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>Chasis Marca: <strong><?=$oMarcaChasis->Nombre?></strong> Chasis N&deg;: <strong><?=$oModelo->NumeroVinPrefijo . $oUnidad->NumeroVin?></strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>que ha adquirido en &eacute;sta concesionaria, <?=utf8_encode($oCliente->RazonSocial)?>, se encuestra sin rodar en </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="25">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>perfecto estado en nuestro poder para poder entregarlo al comprador en el d&iacute;a de la fecha.</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="44%">&nbsp;</td>
                                    <td width="56%"><div align="left">Sin otro particular, saludamos a Uds. muy atentamente.-</div></td>
                                </tr>
								<tr>
									<td width="44%">&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<img src="images/firma.jpg" width="250" height="250" />										
									</td>									
								</tr>
                            </table>
                        </td>
                    </tr>                   
					<tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="100" valign="bottom">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td class="bordeBottom" width="700"></td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td align="center"><div align="center"><span class="textoPie"><?= $oDatoEmpresa->DomicilioCalle?> <?= $oDatoEmpresa->DomicilioNumero ?> - OLIVOS - C.P.: 1636 Tel: <?= $oDatoEmpresa->TelefonoCodigoArea ?>-<?= $oDatoEmpresa->Telefono ?> - Email: <?= $oDatoEmpresa->Email ?></span></div></td>
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
$oMpdf->Output('nota_no_rodamiento.pdf', 'D'); 

?>