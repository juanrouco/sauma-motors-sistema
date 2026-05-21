<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$Action 				= strval($_REQUEST['MainAction']);
$tareasSeleccionados	= strval($_REQUEST['tareasSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oTareasTrabajo					= new TareasTrabajo();
$oOrdenesTrabajo		 		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oArticulos 					= new Articulos();
$oIvas 							= new Ivas();
$oTiposCosto					= new TiposCosto();
$oModelos						= new Modelos();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
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
			$oOrdenesTrabajoTareas->Begin();
			try
			{
				foreach ($Ids as $Id)
				{
					$oTareaTrabajo = $oTareasTrabajo->GetById($Id);
					if (!$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetById($IdOrdenTrabajo, $Id))
					{
						$oOrdenTrabajoTarea	= new OrdenTrabajoTarea();
						$oOrdenTrabajoTarea->IdTareaTrabajo 	= $oTareaTrabajo->IdTareaTrabajo;
						$oOrdenTrabajoTarea->IdOrdenTrabajo 	= $IdOrdenTrabajo;
						$oOrdenTrabajoTarea->Importe		 	= $oTareaTrabajo->ImporteTotal();
						$oOrdenTrabajoTarea->Titulo 			= $oTareaTrabajo->Titulo;
						$oOrdenTrabajoTarea->Descripcion 		= $oTareaTrabajo->Descripcion;
						$oOrdenTrabajoTarea->HorasEstimadas 	= $oTareaTrabajo->HorasEstimadas;
						$oOrdenTrabajoTarea->IdTipoVenta		= intval($_REQUEST['IdTipoVenta_' . $Id]);

						$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Create($oOrdenTrabajoTarea);

						$arrRelacionados = $oTareasTrabajoArticulos->GetAllByTareaTrabajo($oTareaTrabajo);
		
						foreach ($arrRelacionados as $oRelacion)
						{
							$oOrdenTrabajoTareaArticulo = new OrdenTrabajoTareaArticulo();
							$oArticulo = $oArticulos->GetById($oRelacion->IdArticulo);
							$oOrdenTrabajoTareaArticulo->IdArticulo = $oArticulo->IdArticulo;
							$oOrdenTrabajoTareaArticulo->Cantidad 	= $oRelacion->Cantidad;
							$oOrdenTrabajoTareaArticulo->IdOrdenTrabajoTarea = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
							$oIva = $oIvas->GetById($oArticulo->IdIva);
							$oOrdenTrabajoTareaArticulo->PrecioTotal = $oArticulo->PrecioLista * (1 + $oIva->Alicuota) * $oRelacion->Cantidad;
							
							$oOrdenesTrabajoTareasArticulos->Create($oOrdenTrabajoTareaArticulo);
						}
		
					}
					
					
				}
				$oOrdenesTrabajoTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oOrdenesTrabajoTareas->Rollback();
			}
		
			break;
			
		case 'AddNueva':
			$oOrdenesTrabajoTareas->Begin();
			try
			{
				$oOrdenTrabajoTarea	= new OrdenTrabajoTarea();
				$oOrdenTrabajoTarea->IdOrdenTrabajo 	= $IdOrdenTrabajo;
				$oOrdenTrabajoTarea->Importe		 	= 0;
				$oOrdenTrabajoTarea->Titulo 			= $_REQUEST['NuevaTarea'];
				$oOrdenTrabajoTarea->Descripcion 		= $_REQUEST['NuevaDescripcion'];
				$oOrdenTrabajoTarea->IdTipoVenta		= intval($_REQUEST['NuevoIdTipoVenta']);

				$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Create($oOrdenTrabajoTarea);
				
				$oOrdenesTrabajoTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oOrdenesTrabajoTareas->Rollback();
			}
		
			break;
			
		case 'Delete':
			$oOrdenesTrabajoTareas->Begin();
			try
			{
				$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($Id);
				$oOrdenesTrabajoTareasArticulos->DeleteByOrdenTrabajoTarea($oOrdenTrabajoTarea);
				$oOrdenesTrabajoTareas->DeleteIncrement($Id);
				$oOrdenesTrabajoTareas->Commit();
			}
			catch (Exception $ex)
			{
				$oOrdenesTrabajoTareas->Rollback();
			}
			break;
			
		default:
			break;
	}
}
$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAllByOrdenTrabajo($oOrdenTrabajo);
$arrTiposCosto	= $oTiposCosto->GetAll();
$arrModelos = $oModelos->GetAllNumeroLista();
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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
		var urlAjax = 'tareastrabajo_buscar_popup.php?FilterPalabraClave=' + $j('#Titulo').val() + '&FilterCodigoComercial=' + $j('#CodigoComercial').val() + '&Page=' + page;
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
	ShowSection('AddRelacionMain');
}

function HideAddRelacion()
{
	ShowSection('ShownAddRelacion');
	ShowSection('ShownAddNueva');
	HideSection('AddRelacionMain');
}

function ShowAddNueva()
{
	HideSection('ShownAddRelacion');
	HideSection('ShownAddNueva');
	ShowSection('AddNuevaMain');
}

function HideAddNueva()
{
	ShowSection('ShownAddRelacion');
	ShowSection('ShownAddNueva');
	HideSection('AddNuevaMain');
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
		<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		<input type="hidden" name="tareasSeleccionados" id="tareasSeleccionados" value="" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Orden de Trabajo N&deg; <?= $IdOrdenTrabajo ?>: Seleccionar tareas a realizar</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<?php 
			if ($arrOrdenesTrabajoTareas != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Tareas asignadas a la OT N&deg; <?= $IdOrdenTrabajo ?>:</span></td>
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
						foreach ($arrOrdenesTrabajoTareas as $oRelacion) 
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
								<?php
								if ($oRelacion->IdEstado == OrdenTrabajoTarea::IdEstadoFinalizado)
								{
								?>
									<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'ordenestrabajotareas_abrir_procesar.php<?=$strParams?>&IdOrdenTrabajoTarea=<?= $oRelacion->IdOrdenTrabajoTarea ?>';" value="Reabrir" />
								<?php
								}
								else
								{
								?>
									<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'ordenestrabajotareas_cerrar_procesar.php<?=$strParams?>&IdOrdenTrabajoTarea=<?= $oRelacion->IdOrdenTrabajoTarea ?>';" value="Cerrar" />
								<?php
								}
								?>
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
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'ordenestrabajo_detail.php<?=$strParams?>';" value="Volver a ordenes de trabajo" />
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