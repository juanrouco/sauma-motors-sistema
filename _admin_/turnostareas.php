<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TURNO_CREATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdTurno				= intval($_REQUEST['IdTurno']);
$Action 				= strval($_REQUEST['MainAction']);
$tareasSeleccionados	= strval($_REQUEST['tareasSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Modelo					= strval($_REQUEST['Modelo']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oTareasTrabajo					= new TareasTrabajo();
$oTurnos		 				= new Turnos();
$oTurnosTareas					= new TurnosTareas();
$oTurnosTareasArticulos			= new TurnosTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oArticulos 					= new Articulos();
$oIvas 							= new Ivas();
$oTiposCosto					= new TiposCosto();
$oModelos						= new Modelos();
$oTallerUnidades				= new TallerUnidades();
$oModelosPV						= new ModelosPV();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oTurno = $oTurnos->GetById($IdTurno))
{
	header('Location: ordenestrabajo.php' . $strParams);
	exit;
}

/* obtiene los datos del curso */
if (!$oTallerUnidad = $oTallerUnidades->GetById($oTurno->IdTallerUnidad))
{
	header('Location: ordenestrabajo.php' . $strParams);
	exit;
}

/* si el formulario fue enviado */
if ($Submit)
{
	switch ($Action)
	{
		case 'Add':
			$Ids = split(',', $tareasSeleccionados);
			$oTurnosTareas->Begin();
			try
			{
				foreach ($Ids as $Id)
				{
					$oTareaTrabajo = $oTareasTrabajo->GetById($Id);
					if (!$oTurnoTarea = $oTurnosTareas->GetById($IdTurno, $Id))
					{
						$oTurnoTarea					= new TurnoTarea();
						$oTurnoTarea->IdTareaTrabajo 	= $oTareaTrabajo->IdTareaTrabajo;
						$oTurnoTarea->IdTurno 			= $IdTurno;
						$oTurnoTarea->Importe		 	= $oTareaTrabajo->ImporteTotal();
						$oTurnoTarea->Titulo 			= $oTareaTrabajo->Titulo;
						$oTurnoTarea->Descripcion 		= $oTareaTrabajo->Descripcion;
						$oTurnoTarea->HorasEstimadas 	= $oTareaTrabajo->HorasEstimadas;
						$oTurnoTarea->IdTipoVenta		= intval($_REQUEST['IdTipoVenta_' . $Id]);

						$oTurnoTarea = $oTurnosTareas->Create($oTurnoTarea);

						$arrRelacionados = $oTareasTrabajoArticulos->GetAllByTareaTrabajo($oTareaTrabajo);
		
						foreach ($arrRelacionados as $oRelacion)
						{
							$oTurnoTareaArticulo = new TurnoTareaArticulo();
							$oArticulo = $oArticulos->GetById($oRelacion->IdArticulo);
							$oTurnoTareaArticulo->IdArticulo = $oArticulo->IdArticulo;
							$oTurnoTareaArticulo->Cantidad 	= $oRelacion->Cantidad;
							$oTurnoTareaArticulo->IdTurnoTarea = $oTurnoTarea->IdTurnoTarea;
							$oIva = $oIvas->GetById($oArticulo->IdIva);
							$oTurnoTareaArticulo->PrecioTotal = $oArticulo->PrecioLista * (1 + $oIva->Alicuota) * $oRelacion->Cantidad;
							
							$oTurnosTareasArticulos->Create($oTurnoTareaArticulo);
						}
		
					}
					
					
				}
				$oTurnosTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oTurnosTareas->Rollback();
			}
		
			break;
			
		case 'AddNueva':
			$oTurnosTareas->Begin();
			try
			{
				$oTurnoTarea	= new TurnoTarea();
				$oTurnoTarea->IdTurno 			= $IdTurno;
				$oTurnoTarea->Importe		 	= 0;
				$oTurnoTarea->Titulo 			= $_REQUEST['NuevaTarea'];
				$oTurnoTarea->Descripcion 		= $_REQUEST['NuevaDescripcion'];
				$oTurnoTarea->IdTipoVenta		= intval($_REQUEST['NuevoIdTipoVenta']);
				$oTurnoTarea->IdCodigoTrabajo	= intval($_REQUEST['NuevaIdCodigoTrabajo']);

				$oTurnoTarea = $oTurnosTareas->Create($oTurnoTarea);
				
				$oTurnosTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oTurnosTareas->Rollback();
			}
		
			break;
			
		case 'Delete':
			$oTurnosTareas->Begin();
			try
			{
				$oTurnoTarea = $oTurnosTareas->GetByIdIncrement($Id);
				$oTurnosTareasArticulos->DeleteByTurnoTarea($oTurnoTarea);
				$oTurnosTareas->DeleteIncrement($Id);
				$oTurnosTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oTurnosTareas->Rollback();
			}
			break;
			
		default:
			break;
	}
}

$arrTurnosTareas = $oTurnosTareas->GetAllByTurno($oTurno);
$arrTiposCosto	= $oTiposCosto->GetAll();

$filter = array();
$filter['Disponible'] = '1';
$arrModelos = $oModelosPV->GetAll($filter);
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterCodigoTrabajo(IdCodigoTrabajo, CodigoTrabajo) {
	$j('#NuevaIdCodigoTrabajo').val(IdCodigoTrabajo);
	$j('#NuevaCodigoTrabajo').val(CodigoTrabajo);
	
	$j('#modal-popup').dialog('close');
}

function SetNumeroVinPrefijo(IdModelo, NumeroVinPrefijo)
{
	Get('NumeroVinPrefijo').value = NumeroVinPrefijo;
}

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
	if ($j('#NuevaTarea').val() == '') {
		$j('.error_10').show();
		return false;
	}

	return true;
}

function realizarBusqueda(page) {
	if (validar(true)) {
		var urlAjax = 'tareastrabajo_buscar_popup.php?FilterPalabraClave=' + $j('#Titulo').val() + '&IdModeloPV=' + $j('#IdModeloPV').val() + '&Page=' + page;
		$j('body').addClass("loading"); 
		$j.ajax(urlAjax,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
				$j('.agregar').click(function() {
					var idTarea = $j(this).attr('id').split('_')[1];							
					AgregarTarea(idTarea);
				});						
				
				$j('#modal-popup').dialog({
					closeOnEscape: true,
					title: 'Tareas encontradas',
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

function AgregarTarea(idTarea) {
	if (idTarea != '' && idTarea != null && idTarea != undefined) {
		if ($j('#id_' + idTarea).length == 0) {
			var tareasSeleccionados = $j('#tareasSeleccionados').val();
			if (tareasSeleccionados != '')
				tareasSeleccionados += ',';
			tareasSeleccionados += idTarea;
			$j('#tareasSeleccionados').val(tareasSeleccionados);
			
			
			oTareaTrabajo = GetTareaTrabajo(idTarea);
			
			var row = "";
			row += "<tr id=\"row_" + oTareaTrabajo.IdTareaTrabajo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
			row += "	<td height=\"25\">";
			row += "		<div id=\"margen\">" + oTareaTrabajo.Titulo + "</div>";
			row += "		<input type=\"hidden\" id=\"id_" + oTareaTrabajo.IdTareaTrabajo + "\" name=\"id_" + oTareaTrabajo.IdTareaTrabajo + "\" value=\"" + oTareaTrabajo.IdTareaTrabajo + "\" />";
			row += "	</td>";
			row += "	<td height=\"25\"> $" + format_number(oTareaTrabajo.TotalImporte, 2) + "</td>";
			row += "	<td height=\"25\">";
			row += "		<select id=\"IdTipoVenta_" + oTareaTrabajo.IdTareaTrabajo + "\" name=\"IdTipoVenta_" + oTareaTrabajo.IdTareaTrabajo + "\">";
			<?php
				foreach (TipoVenta::GetAllOrdenTrabajo() as $oTipoVenta)
				{
			?>
			row += "			<option value=\"<?= $oTipoVenta['IdTipoVenta'] ?>\"><?= $oTipoVenta['Nombre'] ?></option>";
			<?php
				}
			?>
			row += "		</select>";
			row += "	</td>";
			row += "	<td height=\"25\" align=\"center\"><a href=\"javascript: QuitarTarea(" + oTareaTrabajo.IdTareaTrabajo + ");\"><img src=\"images/iconos/del.gif\" alt=\"Quitar Tarea\" /></a></td>";
			row += "</tr>";
			$j('#tareas').append(row);			
		}
		$j('#modal-popup').dialog('close');		
	}
}

function QuitarTarea(idTarea) {
	if (idTarea != '' && idTarea != null && idTarea != undefined) {
		var tareasSeleccionados = $j('#tareasSeleccionados').val();
		var arrTareas = tareasSeleccionados.split(',');
		
		tareasSeleccionados = '';
		for (var i = 0; i < arrTareas.length; i++)
		{
			if (arrTareas[i] != idTarea)
			{
				if (tareasSeleccionados != '')
					tareasSeleccionados += ',';
				tareasSeleccionados += idTarea;
			}
		}
		
		$j('#row_' + idTarea).remove();
		$j('#tareasSeleccionados').val(tareasSeleccionados);			
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

function ShowAddRelacion()
{
	HideSection('ShownAddRelacion');
	HideSection('ShownAddNueva');
	HideSection('ShownAddTerceros');
	ShowSection('AddRelacionMain');
}

function HideAddRelacion()
{
	ShowSection('ShownAddRelacion');
	ShowSection('ShownAddNueva');
	ShowSection('ShownAddTerceros');
	HideSection('AddRelacionMain');
}

function ShowAddNueva()
{
	HideSection('ShownAddRelacion');
	HideSection('ShownAddNueva');
	HideSection('ShownAddTerceros');
	ShowSection('AddNuevaMain');
}

function HideAddNueva()
{
	ShowSection('ShownAddRelacion');
	ShowSection('ShownAddNueva');
	ShowSection('ShownAddTerceros');
	HideSection('AddNuevaMain');
}

$j(document).ready(function() {
	$j('#buscar-codigos').click(function(e) {
		e.preventDefault();
		
		RealizarBusquedaPopup('codigostrabajo_buscar_popup.php', {}, 'C&oacute;digos de Trabajo');
	});
	
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
		}
	});
	
	$j('.buscar').click(function() { realizarBusqueda(1); });
	$j('#Titulo').keypress(function(e) { 
		if (e.keyCode == 13) {
			realizarBusqueda(1); 
			return false;
		}
	});
});

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdTurno" id="IdTurno" value="<?=$IdTurno?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		<input type="hidden" name="tareasSeleccionados" id="tareasSeleccionados" value="" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Turno: Seleccionar tareas a realizar</span></td>
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
						<div id="ShownAddRelacion" style="width:50%; border: none; float: left; height: 20px; padding-top: 5px" class="bordeGrisFondo"><font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowAddRelacion();"><b> A&ntilde;adir Tareas Predeterminadas</b></a></font></div>
						<div id="ShownAddNueva" style="width:50%; border: none; float: left; height: 20px; padding-top: 5px" class="bordeGrisFondo"><font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowAddNueva();"><b> A&ntilde;adir Nueva Tarea</b></a></font></div>
						<div id="AddRelacionMain" style="display: none;">
							<div id="AddRelacion">	
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td class="bordeGris">
											<div style="border: none;height: 20px; padding-left: 20px; padding-top: 5px" class="bordeGrisFondo"><font><b> Seleccione las tareas predeterminadas:</b></font></div>
											<div style="clear:both; height: 20px">&nbsp;</div>
											<table width="100%" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td>
														<table width="80%" border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>																
																<td class="tituloMenu"><div align="right">Modelo:</div></td>
																<td>
																	<select name="IdModeloPV" id="IdModeloPV"class="camporFormularioSimple">
																		<option value="">[Indistinto]</option>
																		<?php
																		foreach ($arrModelos as $oModeloPV)
																		{
																			$selected = '';
																			if ($oModeloPV->IdModeloPV == $IdModeloPV)
																				$selected = 'selected="selected"';
																		?>
																		<option value="<?= $oModeloPV->IdModeloPV ?>" <?= $selected ?>><?= $oModeloPV->Modelo ?></option>
																		<?php
																		}
																		?>
																	</select>
																</td>
																<td>&nbsp;</td>
																<td class="tituloMenu"><div align="right">Nombre Tarea:&nbsp;</div></td>
																<td>
																	<input type="text" name="Titulo" id="Titulo" value="<?= $Titulo ?>" style="width: 250px" />
																	<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -4px" />
																</td>
																<td>&nbsp;</td>
															</tr>										
															<tr class="error_3" style="display:none">											
																<td colspan="6"><li style="color:#FF0000;">Para realizar la busqueda debe ingresar prefijo de Vin o Nombre de Tarea</li></td>
															</tr>
															<tr class="error_4" style="display:none">											
																<td colspan="6"><li style="color:#FF0000;">Debe ingresar una tarea.</li></td>
															</tr>
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td colspan="6">
																	<table id="tareas" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0">
																		<tr class="bordeGrisFondo">
																			<td width="350" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Titulo</strong></div></td>
																			<td width="100" height="25" class="bordeGrisTitulo"><strong>Precio</strong></td>
																			<td width="100" height="25" class="bordeGrisTitulo"><strong>Tipo Cargo</strong></td>
																			<td width="75" height="25" class="bordeGrisTitulo">&nbsp;</td>
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
						<div id="AddNuevaMain" style="display: none;">
							<div id="AddNueva">	
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td class="bordeGris">
											<div style="border: none;height: 20px; padding-left: 20px; padding-top: 5px" class="bordeGrisFondo"><font><b>Ingrese una nueva tarea:</b></font></div>
											<div style="clear:both; height: 20px">&nbsp;</div>
											<table width="100%" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td>
														<table width="70%" border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>
																<td width="40%" class="tituloMenu"><div align="right">Tarea:&nbsp;&nbsp;</div></td>
																<td width="60%">
																	<div align="left"><input type="text" name="NuevaTarea" id="NuevaTarea" value="<?= $NuevaTarea ?>" onkeyup="javascript: StrToUpper(this.id);" style="width:250px" /></div>
																</td>
															</tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															<tr>
																<td><div align="right">Codigo de Trabajo:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="NuevaCodigoTrabajo" name="NuevaCodigoTrabajo" value="<?= $$_REQUEST['NuevaCodigoTrabajo'] ?>" class="camporFormularioSimpleDisabled" style="width: 225px" readonly="readonly" />
																		<a id="buscar-codigos" href="#"><img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" /></a>
																		<input type="hidden" id="NuevaIdCodigoTrabajo" name="NuevaIdCodigoTrabajo" value="<?= $NuevaIdCodigoTrabajo ?>" />
																		<span style="color:#FF0000;">&nbsp;</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>   
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															</tr>
																<td class="tituloMenu"><div align="right">Descripci&oacute;n:&nbsp;&nbsp;</div></td>
																<td>
																	<textarea name="NuevaDescripcion" id="NuevaDescripcion" onkeyup="javascript: StrToUpper(this.id);" style="width: 250px; height:100px"><?= $NuevaDescripcion ?></textarea>
																</td>
															</tr>
															<tr>
																<td colspan="2">&nbsp;</td>
															</tr>
															</tr>
																<td class="tituloMenu"><div align="right">Tipo Cargo:&nbsp;&nbsp;</div></td>
																<td>
																	<select id="NuevoIdTipoVenta" name="NuevoIdTipoVenta" style="width: 250px">
																	<?php
																	foreach (TipoVenta::GetAllOrdenTrabajo() as $oTipoVenta)
																	{
																	?>
																		<option value="<?= $oTipoVenta['IdTipoVenta'] ?>"><?= $oTipoVenta['Nombre'] ?></option>
																	<?php
																	}
																	?>
																	</select>
																</td>
															</tr>										
															<tr class="error_10" style="display:none">											
																<td colspan="2"><li style="color:#FF0000;">Debe ingresar la nueva tarea</li></td>
															</tr>
															<tr>
																<td colspan="2">&nbsp;</td>
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
			if ($arrTurnosTareas != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Tareas asignadas al Turno:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="150" height="25"><div id="margen"><strong>Nombre</strong></div></td>
							<td width="150" height="25"><div id="margen"><strong>Tipo Cargo</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Repuestos</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Total</strong></div></td>
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrTurnosTareas as $oRelacion) 
						{ 
							$oTipoVenta = TipoVenta::GetById($oRelacion->IdTipoVenta);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="350"><div id="margen"><?=$oRelacion->Titulo?></div></td>
							<td width="350"><div id="margen"><?= $oTipoVenta['Nombre'] ?></div></td>
							<td width="100" height="25"><div id="margen">$<?=$oRelacion->ImporteRepuestos()?></div></td>	
							<td><div id="margen">$<?=$oRelacion->Importe ?></div></td>
							<td>
								<div align="center">
									<a href="turnostareas_mod.php<?=$strParams?>&IdTurnoTarea=<?= $oRelacion->IdTurnoTarea ?>">
										<img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
									<a href="#bottom" onClick="javascript: Delete(<?=$oRelacion->IdTurnoTarea?>)">
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
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'turnos_detail.php<?=$strParams?>';" value="Finalizar" />
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