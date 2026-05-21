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
$IdProveedor			= strval($_REQUEST['IdProveedor']);
$CostoTotal				= floatval(str_replace(',', '.', $_REQUEST['CostoTotal']));
$IdConcepto				= Concepto::TallerTerceros;
$Titulo					= strval($_REQUEST['Titulo']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$Importe				= floatval($_REQUEST['Importe']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();

$arrComprobantesTipos = ComprobanteTipos::GetAllCompras();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];
if ($Submit)
{
	/* validaciones... */
	if (!$Importe)
		$err |= 1;
	if ($Titulo == '')
		$err |= 2;
	if (!$CostoTotal)
		$err |= 4;
	if (!$IdProveedor)
		$err |= 8;
	
	if ($err == 0)
	{
		
		$oOrdenesTrabajoTareas->Begin();
		try
		{
			$oOrdenTrabajoTarea	= new OrdenTrabajoTarea();
			$oOrdenTrabajoTarea->IdOrdenTrabajo 	= $IdOrdenTrabajo;
			$oOrdenTrabajoTarea->Importe		 	= $Importe;
			$oOrdenTrabajoTarea->Titulo 			= $Titulo;
			$oOrdenTrabajoTarea->Tarea	 			= $Titulo;
			$oOrdenTrabajoTarea->Descripcion 		= $Descripcion;
			$oOrdenTrabajoTarea->IdTipoVenta		= $IdTipoVenta;
			$oOrdenTrabajoTarea->IdCategoria		= $IdCategoria;
			$oOrdenTrabajoTarea->CostoTotal			= $CostoTotal;
			$oOrdenTrabajoTarea->IdProveedor		= $IdProveedor;
			$oOrdenTrabajoTarea->Terceros			= 1;

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
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el titulo.</li><?php } ?></td>
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
																<td><div id="margen" align="left">Costo Trabajo:</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="CostoTotal" id="CostoTotal" class="camporFormularioSimple" value="<?= $CostoTotal ?>">
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Debe ingresar el costo de la tarea.</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div id="margen" align="left">Valor a Cobrar:</div></td>
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
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Debe ingresar el costo total de la tarea.</li><?php } ?></td>
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