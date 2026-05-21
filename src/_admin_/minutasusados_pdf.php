<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENTUS_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta	= intval($_REQUEST['IdMinuta']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oMinutasUsados			= new MinutasUsados();
$oClientes 				= new Clientes();
$oUsuarios 				= new Usuarios();
$oTiposIva 				= new TiposIva();
$oTiposDocumento 		= new TiposDocumento();
$oProfesiones 			= new Profesiones();
$oUsados 				= new Usados();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oColores 				= new Colores();
$oMarcas 				= new Marcas();
$oEstadosCiviles		= new EstadosCiviles();
$oMinutasFinanciacion	= new MinutasUsadosFinanciacion();
$oAcreedores 			= new Acreedores();
$oAcreedores 			= new Acreedores();
$oPedidosAccesorios			= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oPagos					= new Pagos();

$TotalGastos = 0;
$Total = 0;
$TotalAPagar = 0;

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutasUsados->GetById($IdMinuta))
{	
	header("Location: minutasusados.php" . $strParams);
	exit();
}


$oPedidoAccesorios = $oPedidosAccesorios->GetByMinutaUsado($oMinuta);
$arrMinutasFinanciacion = $oMinutasFinanciacion->GetByMinuta($oMinuta);
/* verifica si existe el registro a modificar */
if (!$oUsado = $oUsados->GetById($oMinuta->IdUsado))
{	
	header("Location: minutasusados.php" . $strParams);
	exit();
}



$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinuta->IdMinuta);
if ($arrUsados)
{
	$oUsadoTomado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$CostoUsados = 0;
if ($oUsadoTomado)
{
	$oMarcaUsadoTomado = $oMarcas->GetById($oUsadoTomado->IdMarca);
	$oColorUsadoTomado = $oColores->GetById($oUsadoTomado->IdColor);
	$CostoUsados+= $oUsadoTomado->Valuacion;
}

if ($oUsado2)
{
	$oMarcaUsado2 = $oMarcas->GetById($oUsado2->IdMarca);
	$oColorUsado2 = $oColores->GetById($oUsado2->IdColor);
	$CostoUsados+= $oUsado2->Valuacion;
}

/* obtenemos los datos del color */
$oColor = $oColores->GetById($oUsado->IdColor);
	
/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oUsado->IdMarca))
	exit();
	
/* obtenemos los datos del vendedor */
if (!$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario))
	exit();
	
/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();
	
	
$arrPagos = $oPagos->GetByIdMinutaUsado($oMinuta->IdMinuta);

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);

$oAcreedor = $oAcreedores->GetById($oMinuta->IdAcreedor);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);

$oClienteReventa = $oClientes->GetById($oMinuta->IdClienteReventa);


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

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td>
                            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td width="31%"><div align="left">CARPETA N&deg;: <?=$oMinuta->IdMinuta?></div></td>
                                    <td width="45%">&nbsp;</td>
	                                <td width="24%" align="right"><div align="right">FECHA: <?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                	<td align="center"><div align="center"><img src="images/logo_compania.jpg" width="125" height="80" /></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="50" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">MINUTA DE VENTA USADOS</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%">
                            <table width="800" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="600" height="25"><strong>MODELO: </strong><?=utf8_encode($oUsado->Modelo)?></td>
                                
                                	<td width="200" height="25"><strong><?= utf8_encode('A&Ntilde;O') ?>: </strong><?=utf8_encode($oUsado->ModeloAnio)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%" height="25"><strong>CHASIS: </strong><?=utf8_encode($oUsado->NumeroChasis)?></td>
                                    <td width="41%"><strong>MOTOR: </strong><?=utf8_encode($oUsado->NumeroMotor)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="37%"><strong>COLOR: </strong><?=utf8_encode($oColor->Nombre)?></td>
                                	<td width="30%"><strong>KM: </strong><?=number_format($oUsado->Kilometraje, 0, '.', ',')?></td>
                                	<td width="33%"><strong>PATENTE: </strong><?=utf8_encode($oUsado->Dominio)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
								
								<?php
								$TotalGastos += $oMinuta->GastosFlete;
								$TotalGastos += $oUsado->Arreglos;
								$TotalGastos += $oUsado2->Arreglos;
								//$TotalGastos += $oMinuta->GetTotalAccesorios();
								$TotalGastos += $oMinuta->GastosOtorgamiento;
								?>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>MONTO TOTAL DE VENTA: </strong>$ <?=number_format($TotalGastos + $oMinuta->PrecioVenta)?></td>
                                </tr>
                            </table>
                        </td>
					</tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>GASTOS GESTORIA: </strong>$ <?=number_format($oMinuta->Anticipo)?></td>
                                </tr>
                            </table>
                        </td>
					</tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
					<tr>
                    	<td align="center"><div align="center"><strong>FORMA DE PAGO</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
					<?php
					$Efectivo = $TotalGastos + $oMinuta->PrecioVenta;
					if ($arrPagos)
					{
						foreach ($arrPagos as $oPago)
						{
							if ($oPago->IdTipoPago != TipoPago::Efectivo && $oPago->IdTipoPago != TipoPago::DepositoCheque && $oPago->IdTipoPago != TipoPago::Cheque && $oPago->IdTipoPago != TipoPago::Transferencia && $oPago->IdTipoPago != TipoPago::DepositoEfectivo && $oPago->IdTipoPago != TipoPago::CreditoPersonal)
							{
								$oAcreedorPago = $oAcreedores->GetById($oPago->IdAcreedor);
								$Efectivo -= $oPago->Importe;
					?>
								<tr>
                                	<td><strong><?= strtoupper(TipoPago::GetById($oPago->IdTipoPago)) ?><?= $oAcreedorPago ? ' - ' . $oAcreedorPago->RazonSocial : '' ?></strong></td>
                                    <td>&nbsp;</td>
                                	<td>$ <?=number_format($oPago->Importe)?></td>
                                </tr>
					<?php
							}
						}
					}
					
					if ($CostoUsados) {
							
								$Efectivo -= $CostoUsados;
					?>
                    
                                <tr>
                                	<td><strong>USADO</Strong></td>
                                    <td>&nbsp;</td>
                                	<td>$ <?=number_format($CostoUsados)?></td>
                                </tr>
					<?php
					}
					if ($arrMinutasFinanciacion && count ($arrMinutasFinanciacion) > 0) 
					{
						foreach($arrMinutasFinanciacion as $oMinutaFinanciacion)
						{
								$Efectivo -= $oMinutaFinanciacion->Importe;
								$oAcreedor = $oAcreedores->GetById($oMinutaFinanciacion->IdAcreedor);
					?>
                    
                                <tr>
                                	<td><strong><?= $oAcreedor->RazonSocial ?></Strong></td>
                                    <td>&nbsp;</td>
                                	<td>$ <?=number_format($oMinutaFinanciacion->Importe)?> (<?= $oMinutaFinanciacion->Cuotas ?> Cuotas)</td>
                                </tr>
					<?php
						}
					}
					?>
								<tr>
                                	<td><strong>EFECTIVO</strong></td>
                                    <td>&nbsp;</td>
                                	<td>$ <?=number_format($Efectivo)?></td>
                                </tr>
							</table>
						</td>
					</tr>
						
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>	
					<?php
					$Total = 0;
					
					if ($oPedidoAccesorios)
					{
						$arrItems = $oPedidoAccesorios->GetAllItems();
					?>
								
					<tr>
                    	<td align="center"><div align="center"><strong>ACCESORIOS PEDIDOS</strong></div></td>
                    </tr>
					
					<?php
						if ($arrItems)
						{
					?>
					<tr>
                    	<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<?php
							foreach ($arrItems as $oItem)
							{
					?>
								<tr>
                                	<td  width="450" ><?= utf8_encode($oItem->Detalle) ?></td>
                                    <td  width="10" >&nbsp;</td>
                                	<td  width="200"  align="center">$<?= number_format($oItem->Importe * 1.275, 2) ?></td>
                                </tr>
					<?php
							}
					?>
							</table>
						</td>
                    </tr>
					<?php
						}
						if ($oPedidoAccesorios->Accesorios)
						{
					?>
					<tr>
                    	<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
                                	<td width="37%" height="20"><strong>COMENTARIOS:</strong></td>
                                    <td width="14%">&nbsp;</td>
                                	<td width="49%" height="20"><?=utf8_encode($oPedidoAccesorios->Accesorios)?></td>
                                </tr>
							</table>
						</td>
                    </tr>
					<?php
						}
					}
					?>
						
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>	
					<tr>
                    	<td align="left"><div align="left"><strong>REQUIERE CEDULA AZUL:</strong> <?= $oMinuta->CedulaAzul ? 'SI': 'NO' ?></div></td>
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
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="25"><strong>APELLIDO Y NOMBRE: </strong><?=utf8_encode($oCliente->RazonSocial)?><?= $oClienteReventa ? '  (' . $oClienteReventa->RazonSocial . ')' : '' ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
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
                                    <td>
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
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>TEL. PARTICULAR: </strong><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>C.P.: </strong><?= $oCliente->DomicilioCodigoPostal ? $oCliente->DomicilioCodigoPostal : $oLocalidad->CodigoPostal ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>COND. I.V.A.: </strong><?=$oTipoIva->Nombre?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%" height="25"><strong>EMAIL: </strong><?= utf8_encode($oCliente->Email) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<?php
					if ($oUsadoTomado)
					{
					?>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="center"><div align="center"><strong>DATOS DEL USADO</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                               <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>MARCA: </strong><?=$oMarcaUsadoTomado->Nombre?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MODELO: </strong><?=$oUsadoTomado->Modelo?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>DOMINIO: </strong><?=$oUsadoTomado->Dominio?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>COLOR: </strong><?=$oColorUsadoTomado->Nombre ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>A&Ntilde;O: </strong><?=$oUsadoTomado->ModeloAnio ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>PRECIO INFO AUTO: </strong>$<?=$oUsadoTomado->Info ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>KILOMETROS: </strong><?=$oUsadoTomado->Kilometraje ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>IMPORTE: $</strong><?= number_format($oUsadoTomado->Valuacion, 2) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>ARREGLOS: $</strong><?=number_format($oUsadoTomado->Arreglos, 2) ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>OBSERVACIONES: </strong><?= utf8_encode($oUsadoTomado->Observaciones) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								
                            </table>
                        </td>
                    </tr>
					<?php
					}
					?>
					<?php
					if ($oUsado2)
					{
					?>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="center"><div align="center"><strong>DATOS DEL SEGUNDO USADO</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                               <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>MARCA: </strong><?=$oMarcaUsado2->Nombre?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MODELO: </strong><?=$oUsado2->Modelo?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>DOMINIO: </strong><?=$oUsado2->Dominio?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>COLOR: </strong><?=$oColorUsado2->Nombre ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>A&Ntilde;O: </strong><?=$oUsado2->ModeloAnio ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>PRECIO INFO AUTO: </strong>$<?=$oUsado2->Info ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>KILOMETROS: </strong><?=$oUsado2->Kilometraje ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>IMPORTE: $</strong><?= number_format($oUsado2->Valuacion, 2) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>ARREGLOS: $</strong><?=number_format($oUsado2->Arreglos, 2) ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>OBSERVACIONES: </strong><?= utf8_encode($oUsado2->Observaciones) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								
                            </table>
                        </td>
                    </tr>
					<?php
					}
					?>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
					<tr>
                    	<td width="300"><p style="font-size:12px; width: 300px;"><?= utf8_encode('Sumas que el comprador abona en el acto sirviendo el presente de suficiente recibo.')?></p>
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
                    	<td height="20">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong>OBSERVACIONES: </strong><?= utf8_encode($oMinuta->Observaciones) ?></div></td>
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
$oMpdf->Output('minuta usado.pdf', 'I'); 

?>