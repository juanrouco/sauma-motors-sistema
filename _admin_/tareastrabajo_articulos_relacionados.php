<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdTareaTrabajo			= intval($_REQUEST['IdTareaTrabajo']);
$Action 				= strval($_REQUEST['MainAction']);
$articulosSeleccionados	= strval($_REQUEST['articulosSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 						= 0;
$oArticulos 				= new Articulos();
$oTareasTrabajo		 		= new TareasTrabajo();
$oTareasTrabajoArticulos	= new TareasTrabajoArticulos();
$Ivas						= new Ivas();
$oModelosPV			 		= new ModelosPV();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oTareaTrabajo = $oTareasTrabajo->GetById($IdTareaTrabajo))
{
	header('Location: tareastrabajo.php' . $strParams);
	exit;
}

if ($oTareaTrabajo->IdModeloPV)
{
	$oModelo = $oModelosPV->GetById($oTareaTrabajo->IdModeloPV);
} 

/* si el formulario fue enviado */
if ($Submit)
{
	switch ($Action)
	{
		case 'Add':
			$Ids = split(',', $articulosSeleccionados);
			$oTareasTrabajoArticulos->Begin();
			try
			{
				foreach ($Ids as $Id)
				{
					$oArticulo 							= $oArticulos->GetById($Id);
					$oIva								= $Ivas->GetById($oArticulo->IdIva);
					$cantidad 							= floatval($_REQUEST['cantidad_' . $Id]);
					$update = true;
					if (!$oTareaTrabajoArticulo = $oTareasTrabajoArticulos->GetById($IdTareaTrabajo, $Id))
					{
						$oTareaTrabajoArticulo				= new TareaTrabajoArticulo();
						$update = false;
					}
					
					$oTareaTrabajoArticulo->IdTareaTrabajo = $oTareaTrabajo->IdTareaTrabajo;
					$oTareaTrabajoArticulo->IdArticulo = $oArticulo->IdArticulo;
					if (!$update)
						$oTareaTrabajoArticulo->Cantidad = $cantidad;
					else
						$oTareaTrabajoArticulo->Cantidad += $cantidad;
					if (!$update)
						$oTareasTrabajoArticulos->Create($oTareaTrabajoArticulo);
					else
						$oTareasTrabajoArticulos->Update($oTareaTrabajoArticulo);
				}
				$oTareasTrabajoArticulos->Commit();
			}
			catch (Exception $ex)
			{
				$oTareasTrabajoArticulos->Rollback();
			}
		
			break;
			
		case 'Delete':
			$oTareasTrabajoArticulos->Delete($oTareaTrabajo->IdTareaTrabajo, $Id);
			break;
			
		default:
			break;
	}
}
$arrIvas 			= $Ivas->GetAll();
$arrArticulos 		= $oTareasTrabajoArticulos->GetAllByTareaTrabajo($oTareaTrabajo);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript" src="../library/html_editor/tiny_mce.js"></script>
<script type="text/javascript" src="../library/html_editor/editor_html.js"></script>

<script language="javascript">

function validar(busqueda)
{
	if (busqueda) {
		$j('.error_3').hide();
		if ($j('#IdUbicacion').val() == '' || $j('#IdUbicacion').val() == '0' || ($j('#Codigo').val() == '' && $j('#Descripcion').val() == '')) {
			$j('.error_3').show();
			return false;
		} 
	}else {
		$j('.error_3').hide();
		if ($j('#articulosSeleccionados').val() == '' && !busqueda) {
			$j('.error_4').show();
			return false;
		} else {
			$j('.error_4').hide();
		}
	}

	return true;
}

function realizarBusqueda(page) {
	if (validar(true)) {
		var urlAjax = 'articulos_buscar_popup.php?FilterIdUbicacion=' + $j('#IdUbicacion').val() + '&FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=' + $j('#Descripcion').val() + '&Page=' + page;
		$j('body').addClass("loading"); 
		$j.ajax(urlAjax,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
				$j('.agregar').click(function() {
					var idArticulo = $j(this).attr('id').split('_')[1];							
					AgregarArticulo(idArticulo);
				});						
				
				$j('#modal-popup').dialog({
					closeOnEscape: true,
					title: 'Repuestos encontrados',
					width: 700,
					height: 550,
					modal: true
				});
			}
		});				
	}
}

function SetPage(page)
{
	realizarBusqueda(page);
}

function AgregarArticulo(IdArticulo) {
	if (IdArticulo != '' && IdArticulo != null && IdArticulo != undefined) {
		if ($j('#id_' + IdArticulo).length == 0) {
			var articulosSeleccionados = $j('#articulosSeleccionados').val();
			if (articulosSeleccionados != '')
				articulosSeleccionados += ',';
			articulosSeleccionados += IdArticulo;
			$j('#articulosSeleccionados').val(articulosSeleccionados);
			
			$j('#modal-popup').dialog('close');
			oArticulo = GetArticulo(IdArticulo);
			oIva = 0;
			<?php
			foreach ($arrIvas as $oIva)
			{
			?>
			
			if (oArticulo.IdIva == <?= $oIva->IdIva ?>)
				oIva = <?= $oIva->Alicuota ?> + 1;
			<?php
			}
			?>
			var cantidad = 1;
			if (oArticulo.UnidadVenta)
				cantidad *= oArticulo.UnidadVenta;

				var row = "";
			row += "<tr id=\"row_" + oArticulo.IdArticulo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
			row += "	<td height=\"25\">";
			row += "		<div id=\"margen\">" + oArticulo.Codigo + "</div>";
			row += "		<input type=\"hidden\" id=\"id_" + oArticulo.IdArticulo + "\" name=\"id_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.IdArticulo + "\" />";
			row += "		<input type=\"hidden\" id=\"unidadventa_" + oArticulo.IdArticulo + "\" name=\"unidadventa_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.UnidadVenta + "\" style=\"width=50px\" />";
			row += "	</td>";
			row += "	<td height=\"25\">" + oArticulo.Descripcion + "</td>";
			row += "	<td height=\"25\">$" + format_number(oArticulo.PrecioLista * oIva, 2) + "<input type=\"hidden\" id=\"PrecioUnitario_" + oArticulo.IdArticulo + "\" name=\"PrecioUnitario_" + oArticulo.IdArticulo + "\" value=\"" + format_number(oArticulo.PrecioLista * oIva, 2) + "\" /></td>";
			row += "	<td height=\"25\"><input type=\"text\" id=\"cantidad_" + oArticulo.IdArticulo + "\" name=\"cantidad_" + oArticulo.IdArticulo + "\" value=\"" + cantidad + "\" style=\"width:50px\" /></td>";
			row += "	<td height=\"25\">$<label id=\"Precio_" + oArticulo.IdArticulo + "\">" + format_number(oArticulo.PrecioLista * oIva * cantidad, 2) + "</label></td>";
			row += "</tr>";
			$j('#articulos').append(row);
			$j('#cantidad_' + oArticulo.IdArticulo).keyup(function(e) {
				var idAux = parseInt($j(this).attr('id').split('_')[1]);
				var cantidad = parseInt($j(this).val());
				var precio = parseFloat($j('#PrecioUnitario_' + idAux).val());
				var unidadventa = parseInt($j('#unidadventa_' + idAux).val());
				if (cantidad && cantidad % unidadventa != 0)
				{
					alert('Atención: Este artículo se vende en multiplos de ' + unidadventa + '.');
					$j(this).val(cantidad_anterior);
				}
				else
				{
					if (cantidad && precio) {
						$j('#Precio_' + idAux).html(format_number(precio * cantidad, 2));
					}
				}
			});
		} else {					
			$j('#cantidad_' + IdArticulo).val(parseInt($j('#cantidad_' + IdArticulo).val()) + 1);
			var cantidad = parseInt($j('#cantidad_' + IdArticulo).val());
			var precio = parseFloat($j('#PrecioUnitario_' + IdArticulo).val());
			if (cantidad && precio) {
				$j('#Precio_' + IdArticulo).html(format_number(precio * cantidad, 2));
			}
			$j('#modal-popup').dialog('close');
		}
	}
}

function Add()
{
	if (validar(false)) {
		var frmData 	= Get('frmData');
		var MainAction 	= Get('MainAction');
						
		if (frmData == undefined)
			return false;

		MainAction.value = 'Add';
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

function ShowAddRelacion()
{
	HideSection('ShownAddRelacion');
	ShowSection('AddRelacionMain');
}

function HideAddRelacion()
{
	ShowSection('ShownAddRelacion');
	HideSection('AddRelacionMain');
}

$j(document).ready(function() {
	$j('#Codigo').keypress(function(e) {
		if (e.which == 13) {	
			realizarBusqueda(1);
			//$j('#Descripcion').focus();
			e.cancelBubble = true;
			e.returnValue = false;

			if (e.stopPropagation) {
				e.stopPropagation();
				e.preventDefault();
			} 
			$j('#Codigo').val('');
		}
	});
	
	$j('.buscar').click(function() { realizarBusqueda(1); });
	
	$j('#modal-popup').bind('dialogclose', function(event) {
		 $j('#Codigo').focus();
	 });
});

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdTareaTrabajo" id="IdTareaTrabajo" value="<?=$IdTareaTrabajo?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		<input type="hidden" name="articulosSeleccionados" id="articulosSeleccionados" value="" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Relacionar Repuestos</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>							
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><strong>Relaci&oacute;n de repuestos para la tarea:</strong></td>
									</tr>
									<tr>
										<td>
											<span class="tituloCategoriaMenu">
												<strong>
													<?=$oTareaTrabajo->Titulo?><?= $oModelo ?  ' - ' . $oModelo->Modelo : ''?>
												</strong>
											</span>
										</td>
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
				<td>
					<div align="center">
						<div id="ShownAddRelacion" class="bordeGrisFondo" style="padding: 5px"><font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowAddRelacion();"><b> A&ntilde;adir Relaci&oacute;n</b></a></font></div>
						<div id="AddRelacionMain" style="display: none;">
							<div id="AddRelacion">	
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<div class="titulo-seccion">Seleccione los repuestos:</div>
											<div style="clear:both; height: 0px">&nbsp;</div>
											<table width="100%" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td class="bordeGris">
														<table width="100%" border="0" cellpadding="0" cellspacing="0">															
															<tr>
																<td>
																	<strong>C&oacute;digo:&nbsp;</strong>
																</td>
																<td>
																	<input type="text" name="Codigo" id="Codigo" value="<?= $Codigo ?>" style="width: 250px" />
																</td>
																<td>
																	<strong>Descripci&oacute;n:&nbsp;</strong>
																</td>
																<td>
																	<input type="text" name="Descripcion" id="Descripcion" value="<?= $Descripcion ?>" style="width: 250px" />
																	<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" />
																</td>
																<td>&nbsp;</td>
																<td>&nbsp;</td>
															</tr>										
															<tr class="error_3" style="display:none">											
																<td colspan="6"><li style="color:#FF0000;">Para realizar la busqueda debe ingresar un c&oacute;digo y/o descripci&oacute;n</li></td>
															</tr>
															<tr class="error_4" style="display:none">											
																<td colspan="6"><li style="color:#FF0000;">Debe ingresar al menos un repuesto.</li></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td colspan="6">
																	<table id="articulos" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="715" style="margin-left: 75px">
																		<tr class="bordeGrisFondo">
																			<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
																			<td width="150" height="25" class="bordeGrisTitulo"><strong>Descripci&oacute;n</strong></td>					
																			<td width="75" height="25" class="bordeGrisTitulo"><strong>Unitario (c/IVA)</strong></td>
																			<td width="75" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>
																			<td width="75" height="25" class="bordeGrisTitulo"><strong>Subtotal</strong></td>
																		</tr>													
																	</table>
																</td>
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
															<input type="button" name="btnAceptar" class="botonBasico" value="Aceptar" onclick="javascript: Add();" />
															<input type="button" name="btnCancelar" class="botonBasico" onclick="javascript: HideAddRelacion();" value="Cancelar" />
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
			if ($arrArticulos != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Repuestos relacionados:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="150" height="25"><div id="margen"><strong>C&oacute;digo</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Repuesto</strong></div></td>							
							<td width="150" height="25"><div id="margen"><strong>Cantidad</strong></div></td>                        
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrArticulos as $oRelacion) 
						{ 
							$oArticulo = $oArticulos->GetById($oRelacion->IdArticulo);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="150"><div id="margen"><?=$oArticulo->Codigo?></div></td>
							<td><div id="margen"><?=$oArticulo->Descripcion?></div></td>
							<td><div id="margen"><?=$oRelacion->Cantidad?></div></td>                  
							<td>
								<div align="center">
									<a href="#bottom" onClick="javascript: Delete(<?=$oRelacion->IdArticulo?>)">
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
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'tareastrabajo.php<?=$strParams?>';" value="Volver a tareas de trabajo" />
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