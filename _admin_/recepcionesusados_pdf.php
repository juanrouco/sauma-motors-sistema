<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECEPUS_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdRecepcionUsado	= intval($_REQUEST['IdRecepcionUsado']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oRecepcionesUsados		= new RecepcionesUsados();
$oClientes 			= new Clientes();
$oTiposDocumento 	= new TiposDocumento();
$oUsados 			= new Usados();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oEstadosCiviles	= new EstadosCiviles();
$oNumber			= new Number();

/* verifica si existe el registro a modificar */
if (!$oRecepcionUsado = $oRecepcionesUsados->GetById($IdRecepcionUsado))
{	
	header("Location: recepcionesusados.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUsado = $oUsados->GetById($oRecepcionUsado->IdUsado))
	exit();

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUsado->IdColor))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarca = $oMarcas->GetById($oUsado->IdMarca))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oRecepcionUsado->IdCliente))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos el listado de tipos de documentos */
$arrTiposDocumento = $oTiposDocumento->GetAll();

$oEstadoCivil = $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);


/* determinamo el tipo de documento del cliente */
$TipoDocumentoCliente = '';
$TipoDocumentoCliente.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	$TipoDocumentoCliente.= ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '<strike>' : '';
	$TipoDocumentoCliente.= $oTipoDocumento->Codigo;
	$TipoDocumentoCliente.= ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '</strike>' : '';
	$TipoDocumentoCliente.= ' - ';
}
$TipoDocumentoCliente.= '</span>';

$TipoDocumentoClienteConyugue = '';
$TipoDocumentoClienteConyugue.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	$TipoDocumentoClienteConyugue.= ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '<strike>' : '';
	$TipoDocumentoClienteConyugue.= $oTipoDocumento->Codigo;
	$TipoDocumentoClienteConyugue.= ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '</strike>' : '';
	$TipoDocumentoClienteConyugue.= ' - ';
}
$TipoDocumentoClienteConyugue.= '</span>';

/* determinamo el tipo de documento del condominio */
$TipoDocumentoCondominio = '';
$TipoDocumentoCondominio.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	$TipoDocumentoCondominio.= ($oClienteCondominio->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '<strike>' : '';
	$TipoDocumentoCondominio.= $oTipoDocumento->Codigo;
	$TipoDocumentoCondominio.= ($oClienteCondominio->DocumentoTipo == $oTipoDocumento->IdTipoDocumento) ? '</strike>' : '';
	$TipoDocumentoCondominio.= ' - ';
}
$TipoDocumentoCondominio.= '</span>';

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
.texto20 {
	font-size: 16px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
	font-weight:bold;
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

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                    
                    <tr>
                        <td height="50" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">BOLETO DE COMPRA - VENTA</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%" height="25"><?= utf8_encode('Conste por la presente que el señor') ?> <?=utf8_encode($oCliente->RazonSocial)?> <?= utf8_encode('domiciliado en') ?> <?=utf8_encode($oCliente->GetDomicilio())?> DNI <?=$oCliente->DocumentoNumero?> <?= utf8_encode('vende y transfiere al señor') ?> VICTOR H. TOLOSA S.A. <?= utf8_encode('domiciliado en') ?> ACCESO NORTE KM 53,000 CUIT 30-67925285-9 <?= utf8_encode('lo siguiente UN VEHICULO USADO') ?> Marca <?= utf8_encode($oMarca->Nombre) ?> Modelo <?= utf8_encode($oUsado->Modelo) ?> <?= utf8_encode('Año') ?> <?= $oUsado->ModeloAnio ?> Dominio <?= utf8_encode($oUsado->Dominio) ?> <?= utf8_encode('Motor Nº') ?> <?= utf8_encode($oUsado->NumeroMotor) ?> <?= utf8_encode('Chasis Nº') ?> <?= utf8_encode($oUsado->NumeroChasis) ?> <?= utf8_encode('en el estado en el que se encuentra tomando en la fecha el comprador posesión del mismo de conformidad.') ?></td>
                                </tr>
								<tr>
									<td width="100%" height="25"><?= utf8_encode('El precio de venta se establece en $') ?> <?= number_format($oUsado->Valuacion, 2, ',', '') ?> (<?= $oNumber->ValorEnLetras($oUsado->Valuacion, "pesos") ?>)</td>
								</tr>
								<tr>
									<td width="100%" height="25"><?= utf8_encode('Pagaderos en la siguiente forma: ')?> $<?= number_format($oUsado->Valuacion, 2, ',', '') ?> (<?= $oNumber->ValorEnLetras($oUsado->Valuacion, "pesos") ?>) <?= utf8_encode('que el comprador abona en el acto sirviendo el presente de suficiente recibo.') ?></td>
								</tr>
								<tr>
									<td width="100%" height="25"><?= utf8_encode('El vendedor declara expresamente que el automotor motivo del presente no reconoce gravámenes de ninguna naturaleza por prenda, embargo, depósito o préstamo, responsabilizándose por cualquier inconveniente que impidiera disponer libremente del mismo. El comprador recibe de conformidad el automóvil en el estado en el que se encuentra y a su entera conformidad, haciéndose responsable civil y criminalmente a partir de la fecha y hora mas abajo indicadas, por todo accidente, daño o perjuicio que el mismo pudiera ocasionar, así como de las infracciones de cualquier índole que pudiera cometer, comprometiéndose a efectuar la inscripción traslativa del dominio dejando indemne al vendedor de cualquier inconveniente que pudiera surgir.') ?></td>
								</tr>
								<tr>
									<td width="100%" height="25"><?= utf8_encode('Declara también haber recibido la siguiente documentación del referido automotor: ') ?></td>
								</tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600" height="25">&nbsp;</td>
                                    <td width="75" align="center"><strong>SI</strong></td>
                                    <td width="75" align="center"><strong>NO</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">CEDULA DEL AUTOMOTOR</td>
                                    <td width="75" style="border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">TITULO DEL AUTOMOTOR</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">VERIFICACION POLICIAL DE LA UNIDAD (F. 12)</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">FORMULARIO 08 DE LA TRANSFERENCIA</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">INFORME DOMINIO (F. 02) CON F. 13i</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">LIBRE DEUDA DEL TRIBUNAL DE FALTAS DE CAPITAL FEDERAL</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">LIBRE DEUDAS DE RENTAS</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">RECIBOS DE PATENTES</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">FORM. CETA (AFIP)</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">MANUALES Y CODIGO DE RADIO</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600">DUPLICADO DE LLAVE Y CODIGO</td>
                                    <td width="75" style="border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                    <td width="75" style="border-right: 1px solid #000000;border-bottom: 1px solid #000000;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%">EN CASO DE POSEER DEBERA SER RETIRADO EL RASTREADOR SATELITAL</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%">Observaciones: <?= utf8_encode($oRecepcionUsado->Observaciones) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%"><?= utf8_encode('En prueba de conformidad se firman dos ejemplares del mismo tenor y a un solo efecto.') ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="100%">En <?= utf8_encode('OLIVOS') ?>, a los <?= date("d", strtotime($oRecepcionUsado->Fecha)) ?> <?= utf8_encode('días del mes de') ?> <?= ObtenerMes(date("m", strtotime($oRecepcionUsado->Fecha))) ?> de <?= date("Y", strtotime($oRecepcionUsado->Fecha)) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td>&nbsp;</td>
                                    <td align="center"><div align="center">_____________________________________</div></td>
									<td width="25">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="center"><div align="center">_____________________________________</div></td>
                                </tr>
                                <tr>
									<td>&nbsp;</td>
                                    <td align="center"><div align="center">FIRMA DEL COMPRADOR</div></td>
									<td width="25">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="center"><div align="center">FIRMA DEL VENDEDOR</div></td>
                                </tr>
								<tr>
									<td align="right">Tel.:&nbsp;</td>
                                    <td align="center"><div align="center">0230 - 4432230</div></td>
									<td width="25">&nbsp;</td>
									<td align="right">Tel.:&nbsp;</td>
									<td align="center"><div align="center"><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
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
$oMpdf->Output('boleto.pdf', 'D'); 

?>