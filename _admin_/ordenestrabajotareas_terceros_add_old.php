<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$IdTipoVenta			= intval($_REQUEST['IdTipoVenta']);
$IdCategoria			= intval($_REQUEST['IdCategoria']);
$IdComprobanteTipo		= intval($_REQUEST['IdComprobanteTipo']);
$Numero					= strval($_REQUEST['Numero']);
$Fecha					= strval($_REQUEST['Fecha']);
$IdProveedor			= strval($_REQUEST['IdProveedor']);
$Cuit					= strval($_REQUEST['Cuit']);
$ImporteNeto			= floatval(str_replace(',', '.', $_REQUEST['ImporteNeto']));
$Iva10					= floatval(str_replace(',', '.', $_REQUEST['Iva10']));
$Iva21					= floatval(str_replace(',', '.', $_REQUEST['Iva21']));
$Iva27					= floatval(str_replace(',', '.', $_REQUEST['Iva27']));
$PercepcionIva			= floatval(str_replace(',', '.', $_REQUEST['PercepcionIva']));
$PercepcionIB			= floatval(str_replace(',', '.', $_REQUEST['PercepcionIB']));
$PercepcionGanancias	= floatval(str_replace(',', '.', $_REQUEST['PercepcionGanancias']));
$NoGrabados				= floatval(str_replace(',', '.', $_REQUEST['NoGrabados']));
$Total					= floatval(str_replace(',', '.', $_REQUEST['Total']));
$IdConcepto				= Concepto::TallerTerceros;
$Titulo					= strval($_REQUEST['Titulo']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$Importe				= floatval($_REQUEST['Importe']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
$oFacturaCompra			= new FacturaCompra();
$oFacturasCompras		= new FacturasCompras();
$oPeriodos				= new Periodos();

$arrComprobantesTipos = ComprobanteTipos::GetAllCompras();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];
if ($Submit)
{
	/* validaciones... */
	if (!$IdComprobanteTipo)
		$err |= 1;
	if ($Numero == '')
		$err |= 2;
	if ($Fecha == '')
		$err |= 4;
	if ($IdProveedor == '')
		$err |= 8;		
	if ($Cuit == '' || !CPcuitValido($Cuit))
		$err |= 16;
	if (!$Total)
		$err |= 32;
	if ($oFacturaCompraRepetida = $oFacturasCompras->GetByNumeroAndProveedorAndTipo($Numero, $IdProveedor, $IdComprobanteTipo))
		$err |= 64;
	if ((strlen($Numero) != 13 || strlen(str_replace('-', '', $Numero)) != 12))
		$err |= 128;
		
	$BrutoCalculado = $ImporteNeto + $Iva10 + $Iva21 + $Iva27 + $PercepcionIva + $PercepcionIB + $PercepcionGanancias + $NoGrabados;
	if (abs($Total - $BrutoCalculado) > 0.001)
		$err |= 256;
		
	if (!$Importe)
		$err |= 512;
	
	if ($Titulo == '')
		$err |= 1024;
	
	if ($err == 0)
	{
		
		$oOrdenesTrabajoTareas->Begin();
		try
		{
			$oFacturaCompra->IdComprobanteTipo		= $IdComprobanteTipo;
			$oFacturaCompra->Numero					= $Numero;
			$oFacturaCompra->Fecha					= $Fecha;
			$oFacturaCompra->IdProveedor			= $IdProveedor;
			$oFacturaCompra->Cuit					= $Cuit;
			$oFacturaCompra->ImporteNeto			= $ImporteNeto;
			$oFacturaCompra->Iva10					= $Iva10;
			$oFacturaCompra->Iva21					= $Iva21;
			$oFacturaCompra->Iva27					= $Iva27;
			$oFacturaCompra->PercepcionIva			= $PercepcionIva;
			$oFacturaCompra->PercepcionIB			= $PercepcionIB;
			$oFacturaCompra->PercepcionGanancias	= $PercepcionGanancias;
			$oFacturaCompra->NoGrabados				= $NoGrabados;
			$oFacturaCompra->IdConcepto				= $IdConcepto;
			$oFacturaCompra->Total					= $Total;
		
			$oPeriodo = $oPeriodos->GetPeriodoCorrespondienteAbierto($Fecha);
			if ($oPeriodo)
				$oFacturaCompra->IdPeriodo = $oPeriodo->IdPeriodo;
		
			$oFacturaCompra = $oFacturasCompras->Create($oFacturaCompra);
			
			$oOrdenTrabajoTarea	= new OrdenTrabajoTarea();
			$oOrdenTrabajoTarea->IdOrdenTrabajo 	= $IdOrdenTrabajo;
			$oOrdenTrabajoTarea->Importe		 	= $Importe;
			$oOrdenTrabajoTarea->Titulo 			= $Titulo;
			$oOrdenTrabajoTarea->Descripcion 		= $Descripcion;
			$oOrdenTrabajoTarea->IdTipoVenta		= $IdTipoVenta;
			$oOrdenTrabajoTarea->IdCategoria		= $IdCategoria;
			$oOrdenTrabajoTarea->IdFacturaCompra	= $oFacturaCompra->IdFacturaCompra;

			$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Create($oOrdenTrabajoTarea);
					
			$oOrdenesTrabajoTareas->Commit();
			
			header("Location: ordenestrabajotareas.php" . $strParams);
			exit();
		}
		catch (Exception $ex)
		{
			$oOrdenesTrabajoTareas->Rollback();
			header("Location: ordenestrabajotareas.php" . $strParams);
			exit();
		}
	}
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterProveedor(IdProveedor, Nombre)
{
	if ((IdProveedor == '') && (Nombre == ''))
	{		
		Get('Proveedor').value 			= '';
		Get('IdProveedor').value 		= '';
		Get('Cuit').value 				= '';
	}

	var oProveedor = GetProveedor(IdProveedor);
	if (!(oProveedor))
		return;
	
	Get('Proveedor').value 			= oProveedor.Empresa;
	Get('IdProveedor').value 		= oProveedor.IdProveedor;
	Get('Cuit').value 				= oProveedor.Cuit;
}

$j(document).ready(function() { 
	<?php
	if ($IdProveedor) {
	?>
		FilterProveedor(<?= $IdProveedor ?>, '');
	<?php
	}
	?>	
	
});
</script>


</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Trabajos de Terceros para OT N&deg; <?= $IdOrdenTrabajo ?> - Agregar</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="IdProveedor" id="IdProveedor" value="<?=$IdProveedor?>" />
					<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2">
														<table border="0" align="center" cellpadding="0" cellspacing="0">
															<tr>
																<td><div id="margen" align="left">Titulo:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" id="Titulo" name="Titulo" class="camporFormularioSimple" value="<?= $Titulo ?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 1024) { ?><li style="color:#FF0000;">Ingrese el titulo.</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div id="margen" align="left">Tipo Cargo:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<select name="IdTipoVenta" id="IdTipoVenta" class="camporFormularioSimple">
																		<?php
																		foreach (TipoVenta::GetAllOrdenTrabajo() as $oTipoVenta)
																		{
																			$selected = ($oTipoVenta['IdTipoVenta'] == $IdTipoVenta) ? 'selected="selected"' : '';																			
																		?>
																			<option value="<?= $oTipoVenta['IdTipoVenta'] ?>" <?= $selected ?>><?= $oTipoVenta['Nombre'] ?></option>
																		<?php
																		}
																		?>
																		</select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
																<td><div id="margen" align="left">Categor&iacute;a:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<select name="IdCategoria" id="IdCategoria" class="camporFormularioSimple">
																		<?php
																		foreach (Categorias::GetAll() as $oCategoria)
																		{
																			$selected = ($oCategoria['IdCategoria'] == $IdCategoria) ? 'selected="selected"' : '';																			
																		?>
																			<option value="<?= $oCategoria['IdCategoria'] ?>" <?= $selected ?>><?= $oCategoria['Nombre'] ?></option>
																		<?php
																		}
																		?>
																		</select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table width="80%" align="center" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
															<tbody><tr>
																<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
																<td height="40"><span class="tituloPagina">Datos Factura Tercero</span></td>
															</tr>
														</tbody></table>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<table border="0" width="80%" align="center" class="bordeGris" cellpadding="0" cellspacing="0">
															<tr><td>
															<table border="0" align="center" cellpadding="0" cellspacing="0">
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Tipo Comprobante:</div></td>
																</tr>
															<tr>
																<td>
																	<div align="left">
																		<select type="text" name="IdComprobanteTipo" id="IdComprobanteTipo" class="camporFormularioSimple">
																			<option value="">Seleccione un tipo de comprobante</option>
																			<?php
																			foreach ($arrComprobantesTipos as $oTipoComprobante)
																			{
																				$selected = '';
																				if ($oTipoComprobante['IdTipo'] == $IdComprobanteTipo)
																					$selected = 'selected="selected"';
																			?>
																			<option value="<?= $oTipoComprobante['IdTipo'] ?>" <?= $selected ?>><?= $oTipoComprobante['Descripcion'] ?></option>
																			<?php
																			}
																			?>
																		</select>
																	</div>
																</td>
																<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
															</tr>
															<tr>
																<td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el tipo de comprobante</li><?php } ?></td>
															</tr>
															<tr>
																<td><div id="margen" align="left">N&uacute;mero:</div></td>
																
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Numero" id="Numero" class="camporFormularioSimple" maxlength="128" value="<?=$Numero?>" onkeyup="javascript: StrToUpper(this.id);" />																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">
																	<?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el n&uacute;mero</li><?php } ?>
																	<?php if ($err & 64) { ?><li style="color:#FF0000;">La factura ya fue ingresada.</li><?php } ?>
																	<?php if ($err & 128) { ?><li style="color:#FF0000;">El número de factura debe ser de la forma 0000-00000000.</li><?php } ?>
																	&nbsp;
																</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Fecha:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Fecha" id="Fecha" class="camporFormularioSimple" value="<?=$Fecha?>" />
																		<script language="javascript">
															new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
															</script>
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la fecha</li><?php } ?></td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Proveedor:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Proveedor" id="Proveedor" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Proveedor?>" autocomplete="off">
																		<input type="button" id="btnAddProveedor" class="botonBasico"  onClick="javascript:AddProveedor();" value=" + " />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																		<script language="">												
																			SUGGESTRequest('Proveedores', 'GetAll', 'Proveedor', 'FilterProveedor', 'IdProveedor', 'Empresa', 'Filter_Empresa', null);
																		</script>
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Debe ingresar el proveedor.</li><?php } ?></td>
															</tr>
															<tr>
																<td><div id="margen" align="left">CUIT:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Cuit" id="Cuit" class="camporFormularioSimple" value="<?=$Cuit?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Debe ingresar un CUIT v&aacute;lido.</li><?php } ?></td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Importe Neto:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="ImporteNeto" id="ImporteNeto" class="camporFormularioSimple" value="<?=$ImporteNeto?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">IVA 10,5%:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Iva10" id="Iva10" class="camporFormularioSimple" value="<?=$Iva10?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">IVA 21%:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Iva21" id="Iva21" class="camporFormularioSimple" value="<?=$Iva21?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">IVA 27%:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Iva27" id="Iva27" class="camporFormularioSimple" value="<?=$Iva27?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Percepci&oacute;n IVA:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="PercepcionIva" id="PercepcionIva" class="camporFormularioSimple" value="<?=$PercepcionIva?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Percepci&oacute;n IIBB:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="PercepcionIB" id="PercepcionIB" class="camporFormularioSimple" value="<?=$PercepcionIB?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Percepci&oacute;n Ganancias:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="PercepcionGanancias" id="PercepcionGanancias" class="camporFormularioSimple" value="<?=$PercepcionGanancias?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">No Grabados:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="NoGrabados" id="NoGrabados" class="camporFormularioSimple" value="<?=$NoGrabados?>" />
																		
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">&nbsp;</td>
															</tr>
															<tr>
																<td><div id="margen" align="left">Total:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Total" id="Total" class="camporFormularioSimple" value="<?=$Total?>" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
															<tr>
																<td height="20">
																	<?php if ($err & 8) { ?><li style="color:#FF0000;">Debe ingresar el total.</li><?php } ?>
																	<?php if ($err & 256) { ?><li style="color:#FF0000;">La suma de los conceptos ingresados es diferente al total ingresado.</li><?php } ?>
																</td>
															</tr>
														</table>
														</td></tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="2">
														<table border="0" align="center" cellpadding="0" cellspacing="0">
															<tr>
                                                                <td><div id="margen" align="left">Descripción:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<textarea name="Descripcion" id="Descripcion" class="camporFormularioSimple" style="height: 75px" onkeyup="javascript: StrToUpper(this.id);"><?=$Descripcion?></textarea>
																	</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
																<td><div id="margen" align="left">Costo Total Tarea:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?= $Importe ?>">
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Debe ingresar el costo total de la tarea.</li><?php } ?></td>
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
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<?php
									if  (!$popup)
									{
									?>
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajotareas.php<?=$strParams?>';" value="Cancelar" />
									<?php
									}
									else
									{
									?>
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.close();" value="Cancelar" />
									<?php
									}
									?>
								</div>
							</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>


</body>
</html>