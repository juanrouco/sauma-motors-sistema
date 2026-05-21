<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$Taller 				= false;

$oOrdenTrabajo					= new OrdenTrabajo();
$oOrdenesTrabajo				= new OrdenesTrabajo();
$oEstadosOrden					= new EstadosOrden();
$oTallerUnidades				= new TallerUnidades();
$oUsuarios						= new Usuarios();
$oClientes						= new Clientes();
$oMarcas						= new Marcas();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oArticulos						= new Articulos();
$oTiposDocumento 				= new TiposDocumento();
$oLocalidades					= new Localidades();
$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();
$oUsuarios						= new Usuarios();
$oTurnos						= new Turnos();
$oCodigosTrabajo				= new CodigosTrabajo();
$oProvincias					= new Provincias();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header("Location: ordenestrabajo.php" . $strParams);
	exit();
}

$oEstadoOrden 	= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$oUsuario		= $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
$oLocalidad		= $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
$oMarca			= $oMarcas->GetById($oTallerUnidad->IdMarca);
$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);
$arrTiposDocumento = $oTiposDocumento->GetAll();
$arrOrdenTrabajoComentarios = $oOrdenTrabajoComentarios->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
$oUsuario = $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
$oProvincia		= $oProvincias->GetById($oLocalidad->IdProvincia);

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

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF('utf-8', 'A4');
$oMpdf->watermarkText = '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
body {
	background: url("images/honda_2.jpg") 0 0 no-repeat;
	background-image-resize: 3;
}
table {
	border-collapse: collapse;
	width: 100%;
}
td {
	font-size: 12px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
td.bordeNegro {
	border-bottom: 1px solid #000000;
	border-left: 1px solid #000000;
}
td.bordeNegroBottom {
	border-bottom: 1px solid #000000;
}
td.bordeNegroTop {
	border-top: 1px solid #000000;
}
td.bordeNegroLeft {
	border-left: 1px solid #000000;
}
td.bordeNegroRight {
	border-right: 1px solid #000000;
}
td.Item {	
	font-size: 12px; 
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
<body style="background: url('images/honda_2.jpg') 0 0 no-repeat;background-image-resize: 3;">
<div style="height: 2px"></div>
<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td id="fondo">
			<div align="center">				
				<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="170">&nbsp;</td>
									<td><div align="left"><strong>SAUMA MOTOS</strong></div></td>
									<td width="100"></td>
								</tr>
							</table>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="50">&nbsp;</td>
									<td><div align="left">Av. Del Liberador 2275</div></td>
									<td width="75"></td>
									<td valign="middle"><div align="left"></div></td>
									<td width="65"></td>
									<td valign="middle"><div align="left"></div></td>
									<td width="200"></td>
									<td valign="top"><div align="left"><?= CambiarFecha($oOrdenTrabajo->FechaInicio) ?></div></td>
								</tr>
							</table>
							
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="52">&nbsp;</td>
									<td height="30" valign="middle"><div align="left">Bs. As.</div></td>
									<td width="75"></td>
									<td height="30" valign="middle"><div align="left">Olivos</div></td>
									<td width="65"></td>
									<td height="30" valign="middle"><div align="left">1636</div></td>
									<td width="160"></td>
									<td height="30" valign="top"><div align="left"><?= $IdOrdenTrabajo ?></div></td>
								</tr>
							</table>
							
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td></td>
									<td height="5"><div height="12"></div></td>
									<td></td>
								</tr>
								<tr>
									<td width="35">&nbsp;</td>
									<td><div align="left">011 4794-6833</div></td>
									<td width="55"></td>
									<td><div align="left">servicios@saumamotos.com.ar</div></td>
									<td width="155"></td>
									<td><div align="left"><?=utf8_encode($oTallerUnidad->Dominio)?></div></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr height="13">
                    	<td height="13"></td>
                    </tr>	
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td valign="top" width="500">
										<table width="500" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="90">&nbsp;</td>
												<td width="400"><?=utf8_encode($oCliente->RazonSocial)?></td>
											</tr>
										</table>
										<table width="500" border="0" cellpadding="0" cellspacing="0">
											<tr height="7">
												<td height="7"></td>
												<td></td>
											</tr>
											<tr>
												<td width="55">&nbsp;</td>
												<td width="200"><?=utf8_encode($oCliente->GetDomicilio())?></td>
												<td width="15">&nbsp;</td>
												<td><?= $oLocalidad ? utf8_encode($oLocalidad->Nombre) : '' ?></td>
											</tr>
										</table>
										<table width="500" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="30">&nbsp;</td>
												<td width="120"><?= $oLocalidad ? utf8_encode($oLocalidad->CodigoPostal) : '' ?></td>
												<td width="25">&nbsp;</td>
												<td><?= utf8_encode($oCliente->Email) ?></td>
											</tr>
										</table>
										<table width="500" border="0" cellpadding="0" cellspacing="0">
											<tr height="8">
												<td height="8"></td>
												<td></td>
											</tr>
											<tr>
												<td width="90">&nbsp;</td>
												<td><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?> / <?=$oCliente->FaxCodigoArea . ' - ' . $oCliente->Fax?></td>
											</tr>
										</table>
									</td>
                                    <td valign="top" width="222">
										<table width="222" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td><?= str_replace('-', '/', CambiarFecha($oTallerUnidad->FechaInicioGarantia)) ?></td>
											</tr>
											<tr height="8">
												<td height="8"></td>
												<td></td>
											</tr>
											<tr>
												<td><?=utf8_encode($oTallerUnidad->Modelo)?></td>
											</tr>
											<tr height="9">
												<td height="9"></td>
												<td></td>
											</tr>
											<tr>
												<td><?=utf8_encode($oTallerUnidad->NumeroVin)?></td>
											</tr>
											<tr height="9">
												<td height="9"></td>
												<td></td>
											</tr>
											<tr>
												<td><?=utf8_encode($oTallerUnidad->NumeroMotor)?></td>
											</tr>
										</table>
									</td>
                                </tr>
							</table>
						</td>
					</tr>					
					 <tr>
                    	<td height="390">&nbsp;</td>
                    </tr>				
					<tr>
						<td align="left">
							<div align="left">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<table width="611" border="0" cellpadding="0" cellspacing="0">
												<?php 
												if ($arrOrdenesTrabajoTareas != NULL)
												{
													$count = 1;
													foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
													{
														$oCodigoTrabajo = $oCodigosTrabajo->GetById($oRelacion->IdCodigoTrabajo);
												?>
												<tr>
													<td width="41" height="20" align="center"><em><?= $count ?></em></td>
													<td width="450" align="left">
														<table width="600" border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td><em><?=utf8_encode($oRelacion->Titulo)?></em></td>
															</tr>
														</table>
													</td>
												</tr>
												<?php
													$count++;
													}
												}
												?>
											</table>
										</td>
										<td width="150">&nbsp;</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>	
					
					<?php
					if (!$Taller && false)
					{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>PRESUPUESTO ESTIMADO</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="40%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<?php /*<tr>
												<td width="30%" height="25"><strong>Mano de Obra: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteManoObra() ?></em></td>
											</tr>
											<tr>
												<td width="30%" height="25"><strong>Repuestos: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteRepuestos() ?></em></td>
											</tr>*/ ?>
											<tr>
												<td width="30%" height="25"><strong>Costo Estimado: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteEstimado() ?></em></td>
											</tr>
										</table>
									</td>
									<td width="10%"></td>
									<td width="50%">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					}
					elseif (false)
					{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>LISTA DE REPUESTOS</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30" align="center">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="75" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>C&oacute;digo</strong></td>
									<td width="500" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>Descripci&oacute;n</strong></td>
									<td width="75" height="25" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>Cantidad</strong></td>
								</tr>
								<?php 
								if ($arrOrdenesTrabajoTareas != NULL)
								{
									foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
									{
										$arrArticulosRelacionados = $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($oRelacion);
										if ($arrArticulosRelacionados)
										{
											foreach ($arrArticulosRelacionados as $oArticuloRelacionado) 
											{
												$oArticulo = $oArticulos->GetById($oArticuloRelacionado->IdArticulo);
								?>
								<tr>
									<td width="200" class="bordeNegro Item" height="25" align="center"><em><?= $oArticulo->Codigo ?></em></td>
									<td width="550" class="bordeNegro Item" height="25"><em><?= $oArticulo->Descripcion ?></em></td>
									<td width="75" class="bordeNegro Item bordeNegroRight" height="25"  align="center"><em><?= $oArticuloRelacionado->Cantidad ?></em></td>
								</tr>
								<?php
											}
										}
									}
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td height="70"></td>
					</tr>
					<tr>
						<td align="right"><strong>FIRMA MECANICO</strong></td>
					</tr>
					<?php
					}
					?>
				</table>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>
<?php /*
<pagebreak />
<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td>
			<div align="center">				
				<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td width="10%">&nbsp;</td>
                                    <td width="30%">&nbsp;</td>
	                                <td width="60%" align="left">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td><div align="right"><img src="../library/barcodegen/test_1D.php?text=<?=str_pad($oOrdenTrabajo->IdOrdenTrabajo, 9, "0", STR_PAD_LEFT)?>" /></div></td>
											</tr>
											<tr>
												<td><div align="right"><strong>Orden de Trabajo N&deg;: </strong><em><?=$oOrdenTrabajo->IdOrdenTrabajo?></em></div></td>
											</tr>
											<tr>
												<td><div align="right"><strong>Fecha: </strong><em><?=CambiarFecha($oOrdenTrabajo->Fecha)?></em></div></td>
											</tr>
										</table>
									</td>
                                </tr>
							</table>
						</td>
					</tr>					
					 <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="left"  style="border-bottom: 1px solid #000"><div align="left"><strong>DATOS DEL CLIENTE</strong></div></td>
                    </tr>
                    <tr height="10">
                    	<td height="10"></td>
                    </tr>
					<tr>
						<td height="30">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="52%" height="25"><strong><?= $oCliente->IdTipoPersona == PersonaTipos::PersonaFisica ? 'APELLIDO Y NOMBRE' : 'RAZ&Oacute;N SOCIAL' ?>: </strong><em><?=utf8_encode($oCliente->RazonSocial)?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%" height="25"><strong><?=$TipoDocumentoCliente?>: </strong><em><?=$oCliente->DocumentoNumero?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>DOMICILIO: </strong><em><?=utf8_encode($oCliente->GetDomicilio())?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>CIUDAD: </strong><em><?= $oLocalidad ? utf8_encode($oLocalidad->Nombre) : '' ?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>CUIT/CUIL: </strong><em><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>TEL. PARTICULAR: </strong><em><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>EMAIL: </strong><em><?= $oCliente->Email ?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
                    	<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>DETALLES UNIDAD</strong></div></td>
                    </tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>
					<tr>
                        <td height="30">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="52%" height="25"><strong>FECHA INGRESO: </strong><em><?= $oOrdenTrabajo->FechaInicio ? CambiarFechaHora($oOrdenTrabajo->FechaInicio) : 'N/C' ?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>FECHA SALIDA: </strong><em><?= $oOrdenTrabajo->FechaFin ? CambiarFechaHora($oOrdenTrabajo->FechaFin) : 'N/C' ?></em></td>
								</tr>
								<tr>
                                	<td width="52%" height="25"><strong>N&deg; Chasis: </strong><em><?=utf8_encode($oTallerUnidad->PrefijoVin . $oTallerUnidad->NumeroVin)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%"><strong>NUMERO MOTOR: </strong><em><?=utf8_encode($oTallerUnidad->NumeroMotor)?></em></td>
                                </tr>
                                <tr>
									<td width="52%"><strong>MARCA/MODELO: </strong><em><?=utf8_encode($oMarca->Nombre . ' / ' . $oTallerUnidad->Modelo)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%" height="25"><strong>DOMINIO: </strong><em><?=utf8_encode($oTallerUnidad->Dominio)?></em></td>
                                </tr>                                
                                <tr>
                                	<td width="52%" height="25"><strong>INICIO GARANTIA: </strong><em><?=utf8_encode(CambiarFecha($oTallerUnidad->FechaInicioGarantia))?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%"><strong>CONCESIONARIA COMPRA: </strong><em><?=utf8_encode($oTallerUnidad->Concesionario)?></em></td>
                                </tr>
								<tr>
                                	<td width="52%" height="25"><strong>KMS: </strong><em><?=utf8_encode($oOrdenTrabajo->Kilometros)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>                    
                    <tr>
                    	<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>SERVICIOS SOLICITADOS</strong></div></td>
                    </tr>
                    <tr height="10">
                    	<td height="10"></td>
                    </tr>					
					<tr>
						<td align="center">
							<div align="center">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="4" align="center"></td>
										<td colspan="3" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>Cargos</strong></td>
									</tr>
									<tr>
										<td width="41" class="bordeNegro bordeNegroTop" align="center"><strong>N&deg;</strong></td>
										<td width="300" class="bordeNegro bordeNegroTop" align="center"><strong>Servicios Solicitados</strong></td>
										<td width="300" class="bordeNegro bordeNegroTop" align="center"><strong>Intervenciones Realizadas</strong></td>
										<td width="65" class="bordeNegro bordeNegroTop" align="center"><strong>Mec</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop" align="center"><strong>CL</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop" align="center"><strong>INT</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>GTIA</strong></td>									
									</tr>
									<?php 
									if ($arrOrdenesTrabajoTareas != NULL)
									{
										$count = 1;
										foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
										{
									?>
									<tr>
										<td width="41" height="50" class="bordeNegro Item" align="center"><em><?= $count ?></em></td>
										<td width="300" class="bordeNegro Item" align="center"><em><?=$oRelacion->Titulo?></em></td>
										<td width="300" class="bordeNegro Item" align="center"></td>
										<td width="65" class="bordeNegro Item" align="center"></td>
										<td width="40" height="50" class="bordeNegro Item" align="center"><?= $oRelacion->IdTipoVenta != TipoVenta::Garantia && $oRelacion->IdTipoVenta != TipoVenta::VentaInterna ? 'X': '' ?></td>
										<td width="40" height="50" class="bordeNegro Item" align="center"><?= $oRelacion->IdTipoVenta == TipoVenta::VentaInterna ? 'X': '' ?></td>


										<td width="40" height="50" class="bordeNegro Item bordeNegroRight" align="center"><?= $oRelacion->IdTipoVenta == TipoVenta::Garantia ? 'X': '' ?></td>																	
									</tr>
									<?php
										$count++;
										}
									}
									?>
								</table>
							</div>
						</td>
					</tr>					
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>COMENTARIOS</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td>
							<div align="left">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">

								<?php
								if ($oOrdenTrabajo->Comentarios != '')
								{
								?>
									<tr  width="100%">
										<td width="100%" align="left"><div id="margen"><em>* <?= iconv(mb_detect_encoding($oOrdenTrabajo->Comentarios),"UTF-8//IGNORE",$oOrdenTrabajo->Comentarios) ?></em></div></td>
									</tr>
								<?php 
								}
					
								if ($arrOrdenTrabajoComentarios != NULL)
								{
									foreach ($arrOrdenTrabajoComentarios as $oOrdenTrabajoComentario)
									{
								?>
								<tr  width="100%">
									<td width="100%" align="left"><div id="margen"><em>* <?= iconv(mb_detect_encoding($oOrdenTrabajoComentario->Comentarios),"UTF-8//IGNORE",$oOrdenTrabajoComentario->Comentarios) ?></em></div></td>
								</tr>
								<?php
									}
								}
								?>
							</table>
							</div>
						</td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>			
					<?php
					if (!$Taller)
					{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>PRESUPUESTO ESTIMADO</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="40%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<?php /*<tr>
												<td width="30%" height="25"><strong>Mano de Obra: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteManoObra() ?></em></td>
											</tr>
											<tr>
												<td width="30%" height="25"><strong>Repuestos: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteRepuestos() ?></em></td>
											</tr>*/ ?>
									<?php /*		<tr>
												<td width="30%" height="25"><strong>Costo Estimado: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteEstimado() ?></em></td>
											</tr>
										</table>
									</td>
									<td width="10%"></td>
									<td width="50%">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					}
					else
					{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>LISTA DE REPUESTOS</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30" align="center">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="75" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>C&oacute;digo</strong></td>
									<td width="500" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>Descripci&oacute;n</strong></td>
									<td width="75" height="25" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>Cantidad</strong></td>
								</tr>
								<?php 
								if ($arrOrdenesTrabajoTareas != NULL)
								{
									foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
									{
										$arrArticulosRelacionados = $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($oRelacion);
										if ($arrArticulosRelacionados)
										{
											foreach ($arrArticulosRelacionados as $oArticuloRelacionado) 
											{
												$oArticulo = $oArticulos->GetById($oArticuloRelacionado->IdArticulo);
								?>
								<tr>
									<td width="200" class="bordeNegro Item" height="25" align="center"><em><?= $oArticulo->Codigo ?></em></td>
									<td width="550" class="bordeNegro Item" height="25"><em><?= $oArticulo->Descripcion ?></em></td>
									<td width="75" class="bordeNegro Item bordeNegroRight" height="25"  align="center"><em><?= $oArticuloRelacionado->Cantidad ?></em></td>
								</tr>
								<?php
											}
										}
									}
								}
								?>
							</table>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

<pagebreak />
<?php
	$Taller 				= true;
?>

<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td>
			<div align="center">				
				<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td width="10%">&nbsp;</td>
                                    <td width="30%">&nbsp;</td>
	                                <td width="60%" align="left">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td><div align="right"><img src="../library/barcodegen/test_1D.php?text=<?=str_pad($oOrdenTrabajo->IdOrdenTrabajo, 9, "0", STR_PAD_LEFT)?>" /></div></td>
											</tr>
											<tr>
												<td><div align="right"><strong>Orden de Trabajo N&deg;: </strong><em><?=$oOrdenTrabajo->IdOrdenTrabajo?></em></div></td>
											</tr>
											<tr>
												<td><div align="right"><strong>Fecha: </strong><em><?=CambiarFecha($oOrdenTrabajo->Fecha)?></em></div></td>
											</tr>
										</table>
									</td>
                                </tr>
							</table>
						</td>
					</tr>					
					 <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="left"  style="border-bottom: 1px solid #000"><div align="left"><strong>DATOS DEL CLIENTE</strong></div></td>
                    </tr>
                    <tr height="10">
                    	<td height="10"></td>
                    </tr>
					<tr>
						<td height="30">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="52%" height="25"><strong><?= $oCliente->IdTipoPersona == PersonaTipos::PersonaFisica ? 'APELLIDO Y NOMBRE' : 'RAZ&Oacute;N SOCIAL' ?>: </strong><em><?=utf8_encode($oCliente->RazonSocial)?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%" height="25"><strong><?=$TipoDocumentoCliente?>: </strong><em><?=$oCliente->DocumentoNumero?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>DOMICILIO: </strong><em><?=utf8_encode($oCliente->GetDomicilio())?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>CIUDAD: </strong><em><?= $oLocalidad ? utf8_encode($oLocalidad->Nombre) : '' ?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>CUIT/CUIL: </strong><em><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>TEL. PARTICULAR: </strong><em><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></em></td>
								</tr>
								<tr>
									<td width="52%" height="25"><strong>EMAIL: </strong><em><?= $oCliente->Email ?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
                    	<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>DETALLES UNIDAD</strong></div></td>
                    </tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>
					<tr>
                        <td height="30">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="52%" height="25"><strong>FECHA INGRESO: </strong><em><?= $oOrdenTrabajo->FechaInicio ? CambiarFechaHora($oOrdenTrabajo->FechaInicio) : 'N/C' ?></em></td>
									<td width="8%">&nbsp;</td>
									<td width="40%"><strong>FECHA SALIDA: </strong><em><?= $oOrdenTrabajo->FechaFin ? CambiarFechaHora($oOrdenTrabajo->FechaFin) : 'N/C' ?></em></td>
								</tr>
								<tr>
                                	<td width="52%" height="25"><strong>N&deg; Chasis: </strong><em><?=utf8_encode($oTallerUnidad->PrefijoVin . $oTallerUnidad->NumeroVin)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%"><strong>NUMERO MOTOR: </strong><em><?=utf8_encode($oTallerUnidad->NumeroMotor)?></em></td>
                                </tr>
                                <tr>
									<td width="52%"><strong>MARCA/MODELO: </strong><em><?=utf8_encode($oMarca->Nombre . ' / ' . $oTallerUnidad->Modelo)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%" height="25"><strong>DOMINIO: </strong><em><?=utf8_encode($oTallerUnidad->Dominio)?></em></td>
                                </tr>                                
                                <tr>
                                	<td width="52%" height="25"><strong>INICIO GARANTIA: </strong><em><?=utf8_encode(CambiarFecha($oTallerUnidad->FechaInicioGarantia))?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%"><strong>CONCESIONARIA COMPRA: </strong><em><?=utf8_encode($oTallerUnidad->Concesionario)?></em></td>
                                </tr>
								<tr>
                                	<td width="52%" height="25"><strong>KMS: </strong><em><?=utf8_encode($oOrdenTrabajo->Kilometros)?></em></td>
									<td width="8%">&nbsp;</td>
                                    <td width="40%">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>                    
                    <tr>
                    	<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>SERVICIOS SOLICITADOS</strong></div></td>
                    </tr>
                    <tr height="10">
                    	<td height="10"></td>
                    </tr>					
					<tr>
						<td align="center">
							<div align="center">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="4" align="center"></td>
										<td colspan="3" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>Cargos</strong></td>
									</tr>
									<tr>
										<td width="41" class="bordeNegro bordeNegroTop" align="center"><strong>N&deg;</strong></td>
										<td width="300" class="bordeNegro bordeNegroTop" align="center"><strong>Servicios Solicitados</strong></td>
										<td width="300" class="bordeNegro bordeNegroTop" align="center"><strong>Intervenciones Realizadas</strong></td>
										<td width="65" class="bordeNegro bordeNegroTop" align="center"><strong>Mec</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop" align="center"><strong>CL</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop" align="center"><strong>INT</strong></td>
										<td width="40" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>GTIA</strong></td>									
									</tr>
									<?php 
									if ($arrOrdenesTrabajoTareas != NULL)
									{
										$count = 1;
										foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
										{
									?>
									<tr>
										<td width="41" height="50" class="bordeNegro Item" align="center"><em><?= $count ?></em></td>
										<td width="300" class="bordeNegro Item" align="center"><em><?=$oRelacion->Titulo?></em></td>
										<td width="300" class="bordeNegro Item" align="center"></td>
										<td width="65" class="bordeNegro Item" align="center"></td>
										<td width="40" height="50" class="bordeNegro Item" align="center"><?= $oRelacion->IdTipoVenta != TipoVenta::Garantia && $oRelacion->IdTipoVenta != TipoVenta::VentaInterna ? 'X': '' ?></td>
										<td width="40" height="50" class="bordeNegro Item" align="center"><?= $oRelacion->IdTipoVenta == TipoVenta::VentaInterna ? 'X': '' ?></td>
										<td width="40" height="50" class="bordeNegro Item bordeNegroRight" align="center"><?= $oRelacion->IdTipoVenta == TipoVenta::Garantia ? 'X': '' ?></td>									
									</tr>
									<?php
										$count++;
										}
									}
									?>
								</table>
							</div>
						</td>
					</tr>					
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>COMENTARIOS</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td>
							<div align="left">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">

								<?php
								if ($oOrdenTrabajo->Comentarios != '')
								{
								?>
									<tr  width="100%">
										<td width="100%" align="left"><div id="margen"><em>* <?= iconv(mb_detect_encoding($oOrdenTrabajo->Comentarios),"UTF-8//IGNORE",$oOrdenTrabajo->Comentarios) ?></em></div></td>
									</tr>
								<?php 
								}
					
								if ($arrOrdenTrabajoComentarios != NULL)
								{
									foreach ($arrOrdenTrabajoComentarios as $oOrdenTrabajoComentario)
									{
								?>
								<tr  width="100%">
									<td width="100%" align="left"><div id="margen"><em>* <?= iconv(mb_detect_encoding($oOrdenTrabajoComentario->Comentarios),"UTF-8//IGNORE",$oOrdenTrabajoComentario->Comentarios) ?></em></div></td>
								</tr>
								<?php
									}
								}
								?>
							</table>
							</div>
						</td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>
				</table>
			</div>
		</td>
	</tr>
  	<tr>
    	<td>
			<div align="center">				
				<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
					<?php
					if (!$Taller)
					{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>PRESUPUESTO ESTIMADO</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="40%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<?php /*<tr>
												<td width="30%" height="25"><strong>Mano de Obra: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteManoObra() ?></em></td>
											</tr>
											<tr>
												<td width="30%" height="25"><strong>Repuestos: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteRepuestos() ?></em></td>
											</tr>*/ ?>
											<?php /*<tr>
												<td width="30%" height="25"><strong>Costo Estimado: </strong></td>
												<td width="8%">&nbsp;</td>
												<td width="62%"><em>$<?= $oOrdenTrabajo->ImporteEstimado() ?></em></td>
											</tr>
										</table>
									</td>
									<td width="10%"></td>
									<td width="50%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="250" class="bordeNegroRight" height="30"></td>
												<td width="250" class="bordeNegroRight" height="30"></td>
												<td width="250" height="30"></td>
											</tr>
											<tr>
												<td class="bordeNegroRight" align="center"><strong>ACUERDO CLIENTE</strong></td>
												<td class="bordeNegroRight" align="center"><strong>RETIRO CONFORME</strong></td>
												<td align="center"><strong>MECANICO</strong></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					}
					else
					{
						if ($arrOrdenesTrabajoTareas != NULL && false)
						{
					?>
					<tr>
						<td align="left" style="border-bottom: 1px solid #000"><div align="left"><strong>LISTA DE REPUESTOS</strong></div></td>
					</tr>
					<tr height="10">
                    	<td height="10"></td>
                    </tr>	
					<tr>
						<td height="30" align="center">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="75" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>C&oacute;digo</strong></td>
									<td width="500" height="25" class="bordeNegro bordeNegroTop" align="center"><strong>Descripci&oacute;n</strong></td>
									<td width="75" height="25" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>Cantidad</strong></td>
								</tr>
								<?php 
								
									foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
									{
										$arrArticulosRelacionados = $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($oRelacion);
										if ($arrArticulosRelacionados)
										{
											foreach ($arrArticulosRelacionados as $oArticuloRelacionado) 
											{
												$oArticulo = $oArticulos->GetById($oArticuloRelacionado->IdArticulo);
								?>
								<tr>
									<td width="200" class="bordeNegro Item" height="25" align="center"><em><?= $oArticulo->Codigo ?></em></td>
									<td width="550" class="bordeNegro Item" height="25"><em><?= $oArticulo->Descripcion ?></em></td>
									<td width="75" class="bordeNegro Item bordeNegroRight" height="25"  align="center"><em><?= $oArticuloRelacionado->Cantidad ?></em></td>
								</tr>
								<?php
											}
										}
									}
								
								?>
							</table>
						</td>
					</tr>
					<?php
						}
					}
					?>
				</table>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>
*/ ?>
</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

//$oMpdf->SetJS('this.print();');
$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('ordentrabajo.pdf', 'I'); 

?>