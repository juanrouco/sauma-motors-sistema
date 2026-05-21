<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta	= intval($_REQUEST['IdMinuta']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oMinutas 			= new Minutas();
$oGestorias 		= new Gestorias();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oTiposIva 			= new TiposIva();
$oTiposDocumento 	= new TiposDocumento();
$oProfesiones 		= new Profesiones();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oUsados 			= new Usados();
$oEstadosCiviles	= new EstadosCiviles();
$oPedidosAccesorios	= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oAcreedores 		= new Acreedores();
$oProveedores 		= new Proveedores();

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: minutas.php" . $strParams);
	exit();
}

$oPedidoAccesorios = $oPedidosAccesorios->GetByMinuta($oMinuta);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
	exit();
	
if ($oUnidad->IdProveedor == 11)
{
	header('Location: minutas_pedidofactura_espasa_pdf.php?IdMinuta=' . $IdMinuta);
	exit;
}
	
if (!$oProveedor = $oProveedores->GetById($oUnidad->IdProveedor))
	exit();
	
/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
	exit();

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();

/* obtenemos los datos de la marca del motor */
if (!$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor))
	exit();

/* obtenemos los datos de la marca del chasis */
if (!$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis))
	exit();

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
	exit();

/* obtenemos los datos del vendedor */
if (!$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

$oAcreedor = $oAcreedores->GetById($oMinuta->IdAcreedor);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos los datos de la gestoria en caso de que existiera */
$oGestoria = $oGestorias->GetByMinuta($oMinuta);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);

$oClienteReventa = $oClientes->GetById($oMinuta->IdClienteReventa);

/* obtenemos informacion del usado en caso de que existiera */
$arrUsados = $oUsados->GetAllByIdMinuta($oMinuta->IdMinuta);
if ($arrUsados)
{
	$oUsado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$Cliente = $oCliente->RazonSocial;

if ($oMinuta->Condominio)
{
	/* obtenemos los datos del cliente */
	$Cliente.= ' / ' . $oClienteCondominio->RazonSocial;
}

$CostoUsados = 0;
if ($oUsado)
{
	$oMarcaUsado = $oMarcas->GetById($oUsado->IdMarca);
	$oColorUsado = $oColores->GetById($oUsado->IdColor);
	$Arreglos = $oUsado->Arreglos;
	$Observaciones = $oUsado->Observaciones;
	$CostoUsados+= $oUsado->Valuacion;
}

if ($oUsado2)
{
	$oMarcaUsado2 = $oMarcas->GetById($oUsado2->IdMarca);
	$oColorUsado2 = $oColores->GetById($oUsado2->IdColor);
	$Arreglos2 = $oUsado2->Arreglos;
	$Observaciones2 = $oUsado2->Observaciones;
	$CostoUsados+= $oUsado2->Valuacion;
}

/* obtenemos el listado de tipos de documentos */
$arrTiposDocumento = $oTiposDocumento->GetAll();

$oEstadoCivil = $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);

$TotalGastos = 0;

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
//$oMpdf->watermarkText = '';

$oMpdf->SetImportUse();

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
	font-size: 20px; 
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
                        <td>
                            <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td><div align="left"><strong>HARASIC VERA SH</strong></div></td>
								</tr>
                                <tr>
	                                <td><div align="left"><strong><?= utf8_encode('AV. PTE. NESTOR C. KIRCHNER N&deg; 371'); ?></strong></div></td>
								</tr>
                                <tr>
	                                <td><div align="left"><strong>9400 RIO GALLEGOS</strong></div></td>
								</tr>
                                <tr>
	                                <td><div align="left"><strong>PCIA SANTA CRUZ</strong></div></td>
								</tr>
                            </table>
                        </td>
                    </tr>
					<tr>
						<td width="100%" style="width:100%; border-bottom: 2px solid #000000">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="100%" ></td>
								</tr>
							</table>
						</td>
					</tr>
                    <tr>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="right">
                                <tr>
                                	<td align="right"><div align="right">RIO GALLEGOS, <?= date('d') ?> DE <?= ObtenerMes(date('m')) ?> DE <?= date('Y') ?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">A <?= utf8_encode($oProveedor->Empresa) ?>:</div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left"><?= utf8_encode('FACTURACI&Oacute;N') ?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left"><?= utf8_encode('ME DIRIJO A UDS. A FIN DE SOLICITAR LA CORRESPONDIENTE FACTURACI&Oacute;N DE LA UNIDAD QUE SEGUIDAMENTE DETALLO') ?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">FACTURA <strong>"<?= $oCliente->IdTipoIva == TipoIva::RI ? 'A' : 'B' ?>"</strong> A NOMBRE DE: <strong><?= utf8_encode($Cliente) ?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">FORMULARIO 01 A NOMBRE DE: <strong><?= utf8_encode($Cliente) ?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td width="50%" align="left"><div align="left">DNI <?= $oClienteCondominio ? ' ' . utf8_encode($oCliente->RazonSocial) : '' ?>: <?= $oCliente->DocumentoNumero ?></div></td>
                                    <td width="50%" align="right"><div align="right"><?= ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) ?>: <?= $oCliente->ClaveFiscalNumero ?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
					<?php
					if ($oClienteCondominio)
					{
					?>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td width="50%" align="left"><div align="left">DNI <?= utf8_encode($oClienteCondominio->RazonSocial) ?>: <?= $oClienteCondominio->DocumentoNumero ?></div></td>
                                    <td width="50%" align="right"><div align="right"><?= ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) ?>: <?= $oCliente->ClaveFiscalNumero ?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
					<?php
					}
					?>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">DOMICILIO: <strong><?=utf8_encode($oCliente->GetDomicilio())?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">LOCALIDAD: <strong><?=utf8_encode($oLocalidad->Nombre)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">PROVINCIA: <strong><?=utf8_encode($oProvincia->Nombre)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">MODELO: <strong><?=utf8_encode($oModelo->DenominacionModelo)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">VIN: <strong><?=utf8_encode($oUnidad->NumeroVin)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">TELEFONO: <strong><?=utf8_encode('(' . $oCliente->TelefonoCodigoArea . ')' . $oCliente->Telefono)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="left">
                                    <td align="left"><div align="left">MAIL: <strong><?=utf8_encode($oCliente->Email)?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border: 1px solid #000000;" width="100%" border="0" cellpadding="10" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong><?= utf8_encode('SOLICITAMOS POR LA PRESENTE QUE NOS ENVIEN URGENTEMENTE LA DOCUMENTACI&Oacute;N POR CORREO OCA 24 A VELEZ SARFIELD 168 (RIO GALLEGOS) CP: 9400')?></strong></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td height="60">&nbsp;</td>
                    </tr>
                </table>
          	</div>
       	</td>
   	</tr>
</table>
<?php
if ($oPedidoAccesorios)
{
	
$arrPedidosAccesoriosItems = $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($oPedidoAccesorios);
?>
<pagebreak>
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
                                    <td width="31%" colspan="2" align="right"><div align="right">Pilar, <?=date('d', strtotime($oPedidoAccesorios->Fecha))?> de <?=Meses::GetById(date('m', strtotime($oPedidoAccesorios->Fecha)))?> de <?=date('Y', strtotime($oPedidoAccesorios->Fecha))?></div></td>
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
                                	<td align="center"><div align="center"><span class="textoPie">ACCESO NORTE KM 53 - PILAR - C.P.: 1629 Tel/Fax: 02322-432230/432401 - Email: vtolosa@speedy.com.ar</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
          	</div>
       	</td>
   	</tr>
</table>

<?php
}

?>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();


$CurrentUser = Session::GetCurrentUser();

$oMpdf->WriteHTML($Contenido);


$oMpdf->Output('minuta.pdf', 'D'); 

?>