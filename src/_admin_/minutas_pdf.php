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
$oMinutas 					= new Minutas();
$oGestorias 				= new Gestorias();
$oClientes 					= new Clientes();
$oUsuarios 					= new Usuarios();
$oTiposIva 					= new TiposIva();
$oTiposDocumento 			= new TiposDocumento();
$oProfesiones 				= new Profesiones();
$oUnidades 					= new Unidades();
$oModelos 					= new Modelos();
$oLocalidades 				= new Localidades();
$oPartidos 					= new Partidos();
$oProvincias 				= new Provincias();
$oPaises 					= new Paises();
$oColores 					= new Colores();
$oMarcas 					= new Marcas();
$oTiposModelo 				= new TiposModelo();
$oUsados 					= new Usados();
$oEstadosCiviles			= new EstadosCiviles();
$oMinutasFinanciacion		= new MinutasFinanciacion();
$oPedidosAccesorios			= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oAcreedores 				= new Acreedores();
$oOrigenesCliente 			= new OrigenesCliente();
$oPagos 					= new Pagos();

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: minutas.php" . $strParams);
	exit();
}

$oPedidoAccesorios = $oPedidosAccesorios->GetByMinuta($oMinuta);
$arrMinutasFinanciacion = $oMinutasFinanciacion->GetByMinuta($oMinuta);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
	exit();

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
	exit();

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();print_r(1);

/* obtenemos los datos de la marca del motor */
if (!$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor))
	exit();

/* obtenemos los datos de la marca del chasis */
if (!$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis))
	exit();
/* obtenemos los datos del tipo de modelo */
$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);

/* obtenemos los datos del vendedor */
if (!$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();
	
$arrPagos = $oPagos->GetByMinuta($oMinuta);

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
$oOrigenCliente = OrigenesCliente::GetById($oMinuta->IdOrigenCliente);

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
$oProfesion = $oProfesiones->GetById($oCliente->IdProfesion);

$TotalGastos = 0;

/* determinamo el tipo de documento del cliente */
$TipoDocumentoCliente = '';
$TipoDocumentoCliente.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	if ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento)
		$TipoDocumentoCliente.= $oTipoDocumento->Codigo;
}
$TipoDocumentoCliente.= '</span>';

$TipoDocumentoClienteConyugue = '';
$TipoDocumentoClienteConyugue.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	if ($oCliente->DocumentoTipo == $oTipoDocumento->IdTipoDocumento)
		$TipoDocumentoClienteConyugue.= $oTipoDocumento->Codigo;
}
$TipoDocumentoClienteConyugue.= '</span>';

/* determinamo el tipo de documento del condominio */
$TipoDocumentoCondominio = '';
$TipoDocumentoCondominio.= '<span>';
foreach ($arrTiposDocumento as $oTipoDocumento)
{
	if ($oClienteCondominio->DocumentoTipo == $oTipoDocumento->IdTipoDocumento)
		$TipoDocumentoCondominio.= $oTipoDocumento->Codigo;
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
                        <td height="30" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">MINUTA DE VENTA</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr><tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%"><strong>MODELO: </strong><?=utf8_encode($oModelo->DenominacionModelo)?></td>
                                    <td width="41%"><strong>COLOR: </strong><?=utf8_encode($oColor->Nombre)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%"><strong>CHASIS N&deg;:</strong> <?=$oUnidad->NumeroChasis?></td>
                                    <td width="41%"><strong>MOTOR N&deg;: </strong><?=$oUnidad->NumeroMotor?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="70%"><strong>DNRPA N&deg;:</strong> <?=$oUnidad->DNRPA?></td>
                                    <td width="30%"><strong>CILINDRADA: </strong><?=$oModelo->Cilindrada?>cc</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="70%"><strong>N&deg; GARANTIA:</strong> <?=$oMinuta->NumeroGarantia?></td>
                                    <td width="30%"></td>
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
								$TotalGastos += $oMinuta->GastosPatentamiento;
								$TotalGastos += $oMinuta->Interes;
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
					<?php
					$oUsuarioM = Session::GetCurrentUser();
					
					?>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>PRECIO DE VENTA: </strong>$ <?=number_format($oMinuta->PrecioVenta)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>GASTOS GESTORIA: </strong>$ <?=number_format($oMinuta->GastosPatentamiento)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>GASTOS DE OTORGAMIENTO: </strong>$ <?=number_format($oMinuta->GastosOtorgamiento)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>OTROS GASTOS: </strong>$ <?=number_format($oMinuta->GastosFlete)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>INTERES: </strong>$ <?=number_format($oMinuta->Interes)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td><strong>PEDIDOS ACCESORIOS: </strong>$ <?=number_format($oMinuta->GetTotalAccesorios())?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<?php
					?>
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
                    	<td align="left"><div align="left"><strong>ORIGEN CLIENTE:</strong> <?= $oOrigenCliente ?></div></td>
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
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td><strong>APELLIDO Y NOMBRE: </strong><?=utf8_encode($oCliente->RazonSocial)?><?= $oClienteReventa ? '  (' . utf8_encode($oClienteReventa->RazonSocial) . ')' : '' ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>DOMICILIO: </strong><?=utf8_encode($oCliente->GetDomicilio())?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>CIUDAD: </strong><?=utf8_encode($oLocalidad->Nombre)?> (CP: <?= $oCliente->DomicilioCodigoPostal ?>)</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong><?=$TipoDocumentoCliente?>: </strong><?=$oCliente->DocumentoNumero?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>F. NAC.: </strong><?=CambiarFecha($oCliente->FechaNacimiento)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo)?>: </strong><?=$oCliente->ClaveFiscalNumero?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>ESTADO CIVIL: </strong><?=utf8_encode($oEstadoCivil->Nombre)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>PROFESION.: </strong><?=utf8_encode($oProfesion->Nombre)?></td>
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
                                                <td width="52%"><strong>TELEFONO: </strong><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>EMAIL: </strong><?= utf8_encode($oCliente->Email) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<?php
								if ($oEstadoCivil->IdEstadoCivil == EstadoCivil::Casado)
								{
								?>
									<tr>
										<td height="20">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="52%"><strong>APELLIDO Y NOMBRE: </strong><?=utf8_encode($oCliente->ConyugeNombre . ' ' . $oCliente->ConyugeApellido)?></td>
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
													<td width="52%"><strong><?=$TipoDocumentoClienteConyugue?>: </strong><?=$oCliente->ConyugeDocumentoNumero?></td>
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
					<?php
					if ($oUsado)
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
                                                <td width="52%"><strong>MARCA: </strong><?=utf8_encode($oMarcaUsado->Nombre)?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MODELO: </strong><?=utf8_encode($oUsado->Modelo)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>DOMINIO: </strong><?=utf8_encode($oUsado->Dominio)?></td>
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
                                                <td width="52%"><strong>COLOR: </strong><?=utf8_encode($oColorUsado->Nombre) ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>A&Ntilde;O: </strong><?=$oUsado->ModeloAnio ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>KILOMETROS: </strong><?=$oUsado->Kilometraje ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>IMPORTE: $</strong><?= number_format($oUsado->Valuacion, 2) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>PRECIO INFO AUTO: </strong><?= number_format($oUsado->Info, 2) ?></td>
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
                                                <td width="52%"><strong>ARREGLOS: $</strong><?=number_format($oUsado->Arreglos, 2) ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>OBSERVACIONES: </strong><?= utf8_encode($oUsado->Observaciones) ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								
                            </table>
                        </td>
                    </tr>
								
                            </table>
                        </td>
                    </tr>
					<?php
					}
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
                                                <td width="52%"><strong>MARCA: </strong><?=utf8_encode($oMarcaUsado22->Nombre)?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MODELO: </strong><?=utf8_encode($oUsado->Modelo)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								<tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%"><strong>DOMINIO: </strong><?=utf8_encode($oUsado2->Dominio)?></td>
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
                                                <td width="52%"><strong>COLOR: </strong><?=utf8_encode($oColorUsado2->Nombre) ?></td>
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
                                                <td width="52%"><strong>PRECIO INFO AUTO: </strong><?= number_format($oUsado2->Info, 2) ?></td>
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
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong>NOMBRE DEL VENDEDOR: </strong><?=utf8_encode($oUsuario->Nombre . ' ' . $oUsuario->Apellido)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong>OBSERVACIONES: </strong><?=utf8_encode($oMinuta->Observaciones)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong>FECHA RETIRO: </strong><?=CambiarFecha($oMinuta->FechaRetiro)?></div></td>
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


$CurrentUser = Session::GetCurrentUser();

$oMpdf->WriteHTML(utf8_encode($Contenido));


$oMpdf->Output('minuta.pdf', 'I'); 

?>