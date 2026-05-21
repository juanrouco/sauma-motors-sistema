<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PRESUP_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPresupuesto	= intval($_REQUEST['IdPresupuesto']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oPresupuestos		= new Presupuestos();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oModelos 			= new Modelos();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oUsados 			= new Usados();
$oEstadosCiviles	= new EstadosCiviles();

/* verifica si existe el registro a modificar */
if (!$oPresupuesto = $oPresupuestos->GetById($IdPresupuesto))
{	
	header("Location: presupuectos.php" . $strParams);
	exit();
}


/* obtenemos los datos del color */
/*if (!$oColor = $oColores->GetById($oPresupuesto->IdColor))
	exit();*(

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oPresupuesto->IdModelo))
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
if (!$oUsuario = $oUsuarios->GetById($oPresupuesto->IdUsuario))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oPresupuesto->IdCliente))
	exit();

if ($oPresupuesto->EntregaUsado)
{
	$oMarcaUsado = $oMarcas->GetById($oPresupuesto->UsadoIdMarca);
}

$TotalGastos = 0;

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
//$oMpdf->watermarkText = '';

$oMpdf->SetImportUse();

?>

<style>
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

<table width="850" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="850" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td>
                            <table width="850" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td width="425" align="left"><img src="images/logo_tolosa.jpg" width="250" height="50" /></td>
	                                <td width="425" align="right"><div align="right">FECHA: <?=CambiarFecha($oPresupuesto->Fecha)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="30" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">FACTURA PROFORMA</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" style="border: 1px solid #E8E8E8; background: #f3f3f3;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="20">&nbsp;</td>
                                    <td align="left"><div align="left"><span class="texto20">DATOS DEL CLIENTE</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
					<tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td height="20">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="25" colspan="2"><strong>APELLIDO Y NOMBRE: </strong><?=utf8_encode($oCliente->RazonSocial)?><?= $oClienteReventa ? '  (' . $oClienteReventa->RazonSocial . ')' : '' ?></td>
                                               </tr>
											   <tr>
												<td height="25" width="50%"><strong>TEL.: </strong><?=utf8_encode($oCliente->TelefonoCodigoArea)?> - <?= $oCliente->Telefono ?></td>
                                                <td height="25" width="50%"><strong>EMAIL: </strong><?=utf8_encode($oCliente->Email)?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" style="border: 1px solid #E8E8E8; background: #f3f3f3;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="20">&nbsp;</td>
                                    <td align="left"><div align="left"><span class="texto20">UNIDAD PRESUPUESTADA</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="20">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%" height="25"><strong>MODELO: </strong><?= utf8_encode($oMarcaVehiculo->Nombre) ?> <?=utf8_encode($oModelo->DenominacionModelo)?></td>
                                    <td width="41%"><?php /*<strong>COLOR: </strong><?=utf8_encode($oColor->Nombre)?>*/ ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="30" style="border: 1px solid #E8E8E8; background: #f3f3f3;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="20">&nbsp;</td>
                                    <td align="left"><div align="left"><span class="texto20">FORMA DE PAGO</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
					<tr>
						<td>
							<table width="850" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #E8E8E8;">
								<tr>
									<td height="25" width="60%">
										<strong>&nbsp;PRECIO UNIDAD:</strong>
									</td>
									<td width="40%">
										&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?= number_format($oPresupuesto->Precio, 2, ',', '.') ?>
									</td>
								</tr>
								<tr>
									<td height="25">
										<strong>&nbsp;PATENTAMIENTO:</strong>
									</td>
									<td>
										&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?=number_format($oPresupuesto->GastosPatentamiento, 2, ',', '.')?>
									</td>
								</tr>
								<?php
								$TotalGastos += $oPresupuesto->GastosPatentamiento;
								?>
								<tr>
									<td height="25">
										<strong>&nbsp;PRENDA:</strong>
									</td>
									<td>
										&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?=number_format($oPresupuesto->GastosPrenda, 2, ',', '.')?>
									</td>
								</tr>
								<?php
								$TotalGastos += $oPresupuesto->GastosPrenda;
								?>
								<tr>
									<td height="25">
										<strong>&nbsp;G. PRENDARIOS:</strong>
									</td>
									<td>
										&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?=number_format($oPresupuesto->GastosOtorgamiento, 2, ',', '.')?>
									</td>
								</tr>
								<?php
								$TotalGastos += $oPresupuesto->GastosOtorgamiento;
								?>
								<tr>
									<td height="25">
										<strong>&nbsp;FLETE:</strong>
									</td>
									<td>
										&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?=number_format($oPresupuesto->GastosFlete, 2, ',', '.')?>
									</td>
								</tr>
								<?php
								$TotalGastos += $oPresupuesto->GastosFlete;
								?>
								<tr>
									<td align="right" height="25">
										<strong>TOTAL:&nbsp;</strong>
									</td>
									<td>
										<strong>&nbsp;<?= $oPresupuesto->Dolares ? 'U$S' : '$' ?><?=number_format($oPresupuesto->Precio + $TotalGastos, 2, ',', '.')?></strong>
									</td>
								</tr>
							</table>
						</td>
					</tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td><strong>EL PRESENTE PRESUESTO NO INCLUYE ALTA DE PATENTE NI SELLADO 0KM. 6% RESIDENTES EN PCIA. BS. AS. / 7% RESIDENTES C.A.B.A.</strong></td>
                    </tr>
                    <tr>
                        <td height="20" align="center">&nbsp;</td>
                    </tr>
					<?php
					if ($oPresupuesto->Financia)
					{
					?>
                    <tr>
                        <td height="30" style="border: 1px solid #E8E8E8; background: #f3f3f3;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="20">&nbsp;</td>
                                    <td align="left"><div align="left"><span class="texto20">FINANCIACION</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="850" border="0" cellpadding="0" cellspacing="0">
                               <tr>
                                    <td height="20">
                                        <table width="850" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>ACREEDOR: </strong><?= $oPresupuesto->FinanciacionAcreedor ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MONTO A FINANCIAR: </strong>$<?= number_format($oPresupuesto->FinanciacionCapital, 2, ',', '.') ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="850" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>PLAZO: </strong><?=$oPresupuesto->FinanciacionCuotas ?> CUOTAS</td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>VALOR CUOTA: </strong>$<?= number_format($oPresupuesto->FinanciacionValorCuota, 2, ',', '.') ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								
                            </table>
                        </td>
                    </tr>
					<?php
					}
					if ($oPresupuesto->EntregaUsado)
					{
					?>
                    <tr>
                        <td height="30" style="border: 1px solid #E8E8E8; background: #f3f3f3;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="20">&nbsp;</td>
                                    <td align="left"><div align="left"><span class="texto20">DATOS DEL USADO</span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="850" border="0" cellpadding="0" cellspacing="0">
                               <tr>
                                    <td height="20">
                                        <table width="850" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>MARCA: </strong><?=$oMarcaUsado->Nombre?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%"><strong>MODELO: </strong><?=$oPresupuesto->UsadoModelo?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="850" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>A&Ntilde;O: </strong><?=$oPresupuesto->UsadoAnio ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20">
                                        <table width="850" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="52%" height="25"><strong>KILOMETROS: </strong><?=$oPresupuesto->UsadoKm ?></td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="40%" height="25"><strong>IMPORTE: $</strong><?= number_format($oPresupuesto->UsadoPrecioTomado, 2, ',', '.') ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
								
                            </table>
                        </td>
                    </tr>
					<?php
					}
					
					if ($oPresupuesto->Observaciones)
					{
					?>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
					
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong>OBSERVACIONES: </strong><?= utf8_encode($oPresupuesto->Observaciones)?></div></td>
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
                        <td><?= utf8_encode('NOTA: Los valores expresados serán actualizados con la lista de precios vigente al momento de la efectivizar la operación.') ?></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
					<?php 
					if ($oPresupuesto->Dolares)
					{
					?>
                    <tr>
                        <td><?= utf8_encode('Cotizaci&oacute;n billete vendedor d&oacute;lar estadounidense.'); ?></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
					<?php
					}
					?>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td align="left"><div align="left"><strong><?=$oUsuario->Nombre . ' ' . $oUsuario->Apellido?></strong></div></td>
                                </tr>
                                <tr>
                                  <td align="left"><div align="left">Depto. Ventas</div></td>
                                </tr>
                                <tr>
                                  <td align="left"><div align="left">Email: <?= $oUsuario->Email ?></div></td>
                                </tr>
                                <tr>
                                  <td align="left"><div align="left">Tel.: (011) 4794-6833</div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
					<?php 
					if ($oUsuario->IdUbicacion == 3 || true) {
					?>
                    <tr>
                        <td><strong>Datos Bancarios</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Banco: </strong>Banco Macro</td>
                    </tr>
                    <tr>
                        <td><?= utf8_encode('<strong>Razón Social:</strong> Action Motorsports SRL') ?> | <?= utf8_encode('<strong>CUIT:</strong> 30-71194065-7') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= utf8_encode('Cuenta Pesos:') ?></strong> <?= utf8_encode('CBU: 2850521330094198582351') ?> | <?= utf8_encode('Cuenta: CC $ 352109419858235') ?> | <?= utf8_encode('Alias: CFMOTOMARTINEZ.SAUMA') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= utf8_encode('Sucursal Libertador') ?></strong></td>
                    </tr>
					<?php
					} else {
					?>
                    <tr>
                        <td><strong>Datos Bancarios</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Banco: </strong>Banco Santander R&iacute;o</td>
                    </tr>
                    <tr>
                        <td><?= utf8_encode('<strong>Razón Social:</strong> Action Motorsports SRL') ?> | <?= utf8_encode('<strong>CUIT:</strong> 30-71194065-7') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= utf8_encode('Cuenta Pesos:') ?></strong> <?= utf8_encode('CBU: 0720207220000000990372') ?> | <?= utf8_encode('Cuenta: CC $ 207-9903/7') ?></td>
                    </tr>
                    <tr>
                        <td><strong><?= utf8_encode('Sucursal Libertador') ?></strong></td>
                    </tr>
					<?php
					}
					?>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #E8E8E8">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center"><strong>EL PRESUPUESTO TIENE FECHA DE VENCIMIENTO EL DIA <?= CambiarFecha($oPresupuesto->FechaVencimiento) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #E8E8E8">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center"><?= utf8_encode('Av. Del Libertador 14099 - 1636 Martinez<br />Tel.: (011) 3986-3576 - www.saumamotos.com.ar') ?></td>
                    </tr>
                </table>
          	</div>
       	</td>
   	</tr>
</table>

<?php

$Contenido = ob_get_contents();
ob_end_clean();
	
$oMpdf->AddPage('P');
$oMpdf->WriteHTML($Contenido);

if ($oModelo->Catalogo && file_exists(Modelo::PathArchivo . $oModelo->Catalogo))
{	
	$oMpdf->AddPage('P');
	$pagecount = $oMpdf->SetSourceFile(Modelo::PathArchivo . $oModelo->Catalogo);
	
    for ($i=1; $i<=$pagecount; $i++) {
        $import_page = $oMpdf->ImportPage($i);
        $oMpdf->UseTemplate($import_page);

        if ($i < $pagecount)
            $oMpdf->AddPage('P');
    }
}
$oMpdf->Output('presupuesto.pdf', 'D'); 

?>