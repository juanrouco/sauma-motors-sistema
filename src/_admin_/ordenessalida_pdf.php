<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ORDS_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdOrden	= intval($_REQUEST['IdOrden']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oOrdenesSalida			= new OrdenesSalida();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposDocumento 		= new TiposDocumento();
$oTiposIva 				= new TiposIva();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oMarcas 				= new Marcas();
$oLocalidades 			= new Localidades();
$oColores 				= new Colores();
$oMarcas 				= new Marcas();
$oTiposModelo 			= new TiposModelo();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oUbicaciones			= new Ubicaciones();
$oDatosEmpresa	= new DatosEmpresa();

$oDatoEmpresa = $oDatosEmpresa->GetAll();


/* obtenemos los datos de la orden */
if (!$oOrdenSalida = $oOrdenesSalida->GetById($IdOrden))
	exit();

/* obtenemos los datos de la venta */
$oMinuta = $oMinutas->GetById($oOrdenSalida->IdUnidad);

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oOrdenSalida->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos del tipo de documento */
$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oOrdenSalida->IdUnidad))
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
if ($oPlanillaRecepcion)
$CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : '';

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
	                                <td class="bordeBottom" width="700">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="60" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center">ORDEN  DE  SALIDA</div></td>
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
                                    <td width="31%" colspan="2" align="right"><div align="right">Rio Gallegos, <?=date('d', strtotime($oOrdenSalida->Fecha))?> de <?=Meses::GetById(date('m', strtotime($oOrdenSalida->Fecha)))?> de <?=date('Y', strtotime($oOrdenSalida->Fecha))?></div></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Codigo de Llaves:</td>
                                    <td><?=$CodigoLlaves?></td>
                                    <td>Nro. Stock:</td>
                                    <td width="31%"><?=$oUnidad->IdUnidad?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Marca:</td>
                                    <td><?=$oMarcaVehiculo->Nombre?></td>
                                    <td>Tipo:</td>
                                    <td width="31%"><?=$oTipoModelo->Nombre?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Modelo:</td>
                                    <td><?=$oModelo->DenominacionModelo?></td>
                                    <td>Color:</td>
                                    <td width="31%"><?=$oColor->Nombre?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Dominio N&deg;:</td>
                                    <td><?=$oUnidad->Patente?></td>
                                    <td>Motor N&deg;:</td>
                                    <td width="31%"><?=$oUnidad->NumeroMotor?></td>
                                </tr>
                                <tr>
                                	<td width="19%" height="30">Chasis N&deg;:</td>
                                    <td><?=$oUnidad->NumeroVinPrefijo . $oUnidad->NumeroVin?></td>
                                    <td>A&ntilde;o:</td>
                                    <td width="31%"><?=$oModelo->Anio?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    
              	<?php 
					if (($oOrdenSalida->IdTipoDestinatario == OrdenSalidaDestinatarios::Cliente) || 
							($oOrdenSalida->IdTipoDestinatario == OrdenSalidaDestinatarios::Tercero)) 
					{ 
				?>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="36%">Apellido y Nombre del Adquiriente:</td>
                                    <td width="64%"><?=utf8_encode($oCliente->RazonSocial)?></td>
                                </tr>
				<?php 
					if (($oOrdenSalida->IdTipoDestinatario == OrdenSalidaDestinatarios::Cliente)) 
					{ 
				?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=utf8_encode($oCliente->DomicilioCalle) . ' ' . utf8_encode($oCliente->DomicilioNumero)?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=utf8_encode($oLocalidad->Nombre)?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></td>
                                </tr>
                                
                   	<?php } ?>
                                
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="23%">Tipo de Documento:</td>
                                    <td width="34%"><?=$oTipoDocumento->Nombre?></td>
                                    <td width="5%">Nro.:</td>
                                    <td width="38%"><?=$oCliente->DocumentoNumero?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
              	<?php } elseif (($oOrdenSalida->IdTipoDestinatario == OrdenSalidaDestinatarios::Transporte)) 
				{ 
					$oUbicacion = $oUbicaciones->GetById($oOrdenSalida->IdUbicacion);
					?>

                    <tr>
                        <td height="70">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="36%">Empresa de Transporte:</td>
                                    <td width="64%"><?=utf8_encode($oOrdenSalida->Transporte)?></td>
                                <tr>
                                    <td>Nro. CUIT/CUIL:</td>
                                    <td><?=ClaveFiscalTipos::GetById($oOrdenSalida->TransporteClaveFiscalTipo) . ': ' . $oOrdenSalida->TransporteClaveFiscalNumero?></td>
                                </tr>
								<tr>
                                    <td>Destino:</td>
                                    <td><?= $oUbicacion->Nombre ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                
                <?php } ?>                     
					<tr>
						<td>
					<?php 
					$str = '';
					if ($oOrdenSalida->EntregaManuales)
					{
						$str.= 'Manuales entregados<br />';					
					}
					if ($oOrdenSalida->EntregaLlaves)
					{
						$str.= 'Llaves entregadas<br />';
					}										
					if ($oOrdenSalida->EntregaTarjetaCode)
					{
						$str.= 'Tarjeta Code entregada<br />';
					}
					echo $str;
					?>
						</td>
					</tr>
					
                    <tr>
                        <td height="150">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="9%">&nbsp;</td>
                                    <td width="91%">
                                    	<p>
                                        Recibo de conformidad el veh&iacute;culo en perfecto estado haci&eacute;ndome responsable civil
                                        <br />
                                        y criminalmente, a partir de la fecha y hora m&aacute;s abajo indicadas, por cualquier
                                        <br />
                                        accidente, da&ntilde;o o perjuicio que pudiera ocasionar el automotor referido.
                                        <br />
                                        Dejando constancia que la Unidad no presenta detalles de chapa y / o pintura.
                                        <br />
                                        Asimismo, declaro conocer y aceptar los t&eacute;rminos de garant&iacute;a que me fueron
                                        <br />
                                        informados.
                                        </p>
                                    </td>
                                </tr>
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
                                    <td width="42%" align="center"><div align="center">Por la Concesionaria</div></td>
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
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr>
                                	<td align="center"><div align="center"><span class="textoPie">VELEZ SARFIELD 168 - RIO GALLEGOS - C.P.: 9400 Tel/Fax: (02966) 430368</span></div></td>
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
$oMpdf->Output('orden_salida.pdf', 'D'); 

?>