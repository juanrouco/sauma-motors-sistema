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
$IdMinutaEspera	= intval($_REQUEST['IdMinutaEspera']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oMinutasEspera		= new MinutasEspera();
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

/* verifica si existe el registro a modificar */
if (!$oMinutaEspera = $oMinutasEspera->GetById($IdMinutaEspera))
{	
	header("Location: minutasespera.php" . $strParams);
	exit();
}

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oMinutaEspera->IdColor))
	exit();
$oColor2 = $oColores->GetById($oMinutaEspera->IdColor2);
$oColor3 = $oColores->GetById($oMinutaEspera->IdColor3);

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oMinutaEspera->IdModelo))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
	exit();

/* obtenemos los datos del vendedor */
if (!$oUsuario = $oUsuarios->GetById($oMinutaEspera->IdUsuario))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinutaEspera->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);

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

<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td width="31%"><div align="left">MINUTA N&deg;: <?=$oMinutaEspera->IdMinutaEspera?></div></td>
                                    <td width="45%">&nbsp;</td>
	                                <td width="24%" align="right"><div align="right">FECHA: <?=CambiarFecha($oMinutaEspera->FechaMinuta)?></div></td>
                                </tr>
                                <tr>
	                                <td><div align="left">VIN N&deg;: <?=$oMinutaEspera->NumeroVin?></div></td>
                                    <td>&nbsp;</td>
	                                <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                	<td align="center"><div align="center"><img src="images/logo_tolosa.jpg" width="250" height="50" /></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="50" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">MINUTA DE VENTA</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="30">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="70%" height="25"><strong>MODELO: </strong><?=utf8_encode($oModelo->DenominacionModelo)?></td>
                                    <td width="30%"><strong>COLOR 1: </strong><?=utf8_encode($oColor->Nombre)?></td>
                                </tr>
								<tr>
                                	<td width="70%" height="25">&nbsp;</td>
                                    <td width="30%"><strong>COLOR 2: </strong><?=utf8_encode($oColor2->Nombre)?></td>
                                </tr>
								<tr>
                                	<td width="70%" height="25">&nbsp;</td>
                                    <td width="30%"><strong>COLOR 3: </strong><?=utf8_encode($oColor3->Nombre)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                
                                <tr>
                                	<td height="25">SALDO ANTICIPO</td>
                                    <td>&nbsp;</td>
                                	<td height="25">$ <?=number_format($oMinutaEspera->Anticipo)?></td>
                                </tr>                                
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="center"><div align="center"><strong>DATOS DEL SOLICITANTE</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="25"><strong>APELLIDO Y NOMBRE: </strong><?=utf8_encode($oCliente->RazonSocial)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>DOMICILIO: </strong><?=utf8_encode($oCliente->GetDomicilio())?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>CIUDAD: </strong><?=utf8_encode($oLocalidad->Nombre)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong><?=$TipoDocumentoCliente?>: </strong><?=$oCliente->DocumentoNumero?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>F. NAC.: </strong><?=CambiarFecha($oCliente->FechaNacimiento)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>TEL. PARTICULAR: </strong><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>CUIT/CUIL: </strong><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>EMAIL: </strong><?= $oCliente->Email ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%" height="25">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<tr>
                                    <td height="30">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>ESTADO CIVIL: </strong><?=$oEstadoCivil->Nombre?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<?php
								if ($oEstadoCivil->IdEstadoCivil == EstadoCivil::Casado)
								{
								?>
									<tr>
										<td height="30">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="52%" height="25"><strong>APELLIDO Y NOMBRE: </strong><?=$oCliente->ConyugeNombre . ' ' . $oCliente->ConyugeApellido?></td>
													<td width="8%">&nbsp;</td>
													<td width="40%">&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>	
									 <tr>
										<td height="30">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="52%" height="25"><strong><?=$TipoDocumentoClienteConyugue?>: </strong><?=$oCliente->ConyugeDocumentoNumero?></td>
													<td width="8%">&nbsp;</td>
													<td width="40%"><strong>F. NAC.: </strong><?=CambiarFecha($oCliente->FechaNacimiento)?></td>
												</tr>
											</table>
										</td>
									</tr>
								<?php
								}
								?>
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
                                  <td align="left"><div align="left"><strong>NOMBRE DEL VENDEDOR: </strong><?=$oUsuario->Nombre . ' ' . $oUsuario->Apellido?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td height="60">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center"><div align="center">_____________________________________</div></td>
									<td width="25">&nbsp;</td>
									<td align="center"><div align="center">_____________________________________</div></td>
                                </tr>
                                <tr>
                                    <td align="center"><div align="center">FIRMA DEL COMPRADOR</div></td>
									<td width="25">&nbsp;</td>
									<td align="center"><div align="center">FIRMA DEL VENDEDOR</div></td>
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
$oMpdf->Output('minuta.pdf', 'D'); 

?>