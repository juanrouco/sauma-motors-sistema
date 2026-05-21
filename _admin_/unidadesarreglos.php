<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_UPDATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdUnidad				= intval($_REQUEST['IdUnidad']);
$Action 				= strval($_REQUEST['MainAction']);
$tareasSeleccionados	= strval($_REQUEST['tareasSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Modelo					= strval($_REQUEST['Modelo']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oUnidades						= new Unidades();
$oUnidadesArreglos				= new UnidadesArreglos();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oUnidad = $oUnidades->GetById($IdUnidad))
{
	header('Location: Unidades.php' . $strParams);
	exit;
}

/* si el formulario fue enviado */
if ($Submit)
{
	switch ($Action)
	{
		case 'AddNueva':
			try
			{
				$oUnidadArreglo	= new UnidadArreglo();
				$oUnidadArreglo->IdUnidad = $IdUnidad;
				$oUnidadArreglo->Importe	= floatval($_REQUEST['Importe']);;
				$oUnidadArreglo->Detalle = $_REQUEST['Detalle'];
				
				$oUnidadArreglo = $oUnidadesArreglos->Create($oUnidadArreglo);
				
			}
			catch (Exception $ex)
			{
			}
		
			break;
			
		case 'Delete':
			try
			{
				$oUnidadArreglo = $oUnidadesArreglos->GetById($Id);
				
				$oUnidadesArreglos->Delete($oUnidadArreglo->IdUnidadArreglo);
			}
			catch (Exception $ex)
			{
			}
			break;
			
		default:
			break;
	}
}

$arrUnidadesArreglos = $oUnidadesArreglos->GetAllByUnidad($oUnidad);
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function validar(busqueda)
{
	if ($j('#NumeroVinPrefijo').val() == '' && $j('#Titulo').val() == '') {
		$j('.error_3').show();
		return false;
	} else {
		$j('.error_3').hide();
		if ($j('#tareasSeleccionados').val() == '' && !busqueda) {
			$j('.error_4').show();
			return false;
		} else {
			$j('.error_4').hide();
		}
	}

	return true;
}

function validarNueva(busqueda)
{
	if ($j('#Detalle').val() == '') {
		$j('.error_10').show();
		return false;
	}
	if ($j('#Importe').val() == '') {
		$j('.error_10').show();
		return false;
	}

	return true;
}

function SetPage(page)
{
	realizarBusqueda(page);
}

function AddNueva()
{
	if (validarNueva(false)) {
		var frmData 	= Get('frmData');
		var MainAction 	= Get('MainAction');
						
		if (frmData == undefined)
			return false;

		MainAction.value = 'AddNueva';
		frmData.submit();
		return true;
	}
	return false;
}

function Delete(Id)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	MainAction.value = 'Delete';
	IdField.value = Id;
	frmData.submit();
	return true;
}

function ShowAddNueva()
{
	HideSection('ShownAddNueva');
	ShowSection('AddNuevaMain');
}

function HideAddNueva()
{
	ShowSection('ShownAddNueva');
	HideSection('AddNuevaMain');
}

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdUnidad" id="IdUnidad" value="<?=$IdUnidad?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Unidad N&deg; <?= $IdUnidad ?>: Arreglos</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>	
<tr>
				<td>
					<div align="left">
						<div id="ShownAddNueva">
							<table border="0" align="cener" cellpadding="0" cellspacing="0">
								<tr>
									<td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar PDF" border="0"></div></td>
									<td><a href="javascript: ShowAddNueva();">Agregar</a></td>
									<td width="10">&nbsp;</td>
								</tr>
							</table>
						</div>
						<div id="AddNuevaMain" style="display: none;">
							<div id="AddNueva">	
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td class="bordeGris">
											<div style="border: none;height: 20px; padding-left: 20px; padding-top: 5px" class="bordeGrisFondo"><font><b>Ingrese un nuevo arreglo:</b></font></div>
											<div style="clear:both; height: 20px">&nbsp;</div>
											<table width="100%" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td>
														<table width="70%" border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>
																<td width="40%" class="tituloMenu"><div align="right">Detalle:&nbsp;&nbsp;</div></td>
																<td width="60%">
																	<div align="left"><input type="text" name="Detalle" id="Detalle" value="<?= $Detalle ?>" onkeyup="javascript: StrToUpper(this.id);" style="width:250px" /></div>
																</td>
															</tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															<tr>
																<td class="tituloMenu"><div align="right">Importe:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="Importe" name="Importe" value="<?= $Importe ?>" class="camporFormularioSimple" style="width: 225px" />
																	</div>																				
																</td>                                                                            
                                                            </tr>  
															<tr class="error_10" style="display:none">
																<td>&nbsp;</td>
																<td><li style="color:#FF0000;">Debe ingresar el detalle e importe</li></td>
															</tr>															
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
														</table>
													</td>
												</tr>
												<tr>
													<td><div align="center"></div></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
												<tr>
													<td height="30">
														<div align="center">
															<input type="button" name="btnAceptar" class="botonBasico" value="Aceptar" onclick="javascript: AddNueva();" />
															<input type="button" name="btnCancelar" class="botonBasico" onclick="javascript: HideAddNueva();" value="Cancelar" />
														</div>
														<div align="center"></div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</td>
			</tr>		
			<?php 
			if ($arrUnidadesArreglos != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Arreglos cargados al Unidad N&deg; <?= $IdUnidad ?>:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="150" height="25"><div id="margen"><strong>Detalle</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Importe</strong></div></td>
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						$Total = 0;
						foreach ($arrUnidadesArreglos as $oRelacion) 
						{ 
							$Total+= $oRelacion->Importe;
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td height="25" width="70%"><div id="margen"><?=$oRelacion->Detalle?></div></td>
							<td><div id="margen">$<?=$oRelacion->Importe ?></div></td>
							<td>
								<div align="center">
									<a href="#bottom" onClick="javascript: Delete(<?=$oRelacion->IdUnidadArreglo?>)">
										<img src="images/iconos/del.gif" alt="Eliminar" border="0" />
									</a>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<div align="center">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<?php 
						} 
						?> 
						<tr class="bordeGrisFondo">							
							<td width="150" height="25"><div id="margen"><strong>Total</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>$<?= number_format($Total, 2) ?></strong></div></td>
							<td width="100"><div align="center"><strong>&nbsp;</strong></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			} 
			else 
			{ 
			?>
			<tr height="20">
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
						</tr>
						<tr>
							<td><div align="center"><strong>No hay ninguna relaci&oacute;n establecida.</strong></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
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
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="right">
									<label>
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'Unidades.php<?=$strParams?>';" value="Finalizar" />
									</label>
								</div>
							</td>
							<td width="10" height="30">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<div id="modal-popup" style="display:none">
	</div>
</body>
</html>