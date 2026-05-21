<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PEDREP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page							= intval($_REQUEST['Page']);
$Fecha							= strval($_REQUEST['Fecha']);
$IdUsuario						= intval($_REQUEST['IdUsuario']);
$Usuario						= strval($_REQUEST['Usuario']);
$IdSector						= intval($_REQUEST['IdSector']);
$IdOrdenTrabajo					= intval($_REQUEST['IdOrdenTrabajo']);
$IdModalidad					= intval($_REQUEST['IdModalidad']);
$articulosSeleccionados			= strval($_REQUEST['articulosSeleccionados']);
$Submit							= $_REQUEST['Submitted'];

/* declaracion de variables */
$err						= 0;
$oUsuarios					= new Usuarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades			= new TallerUnidades();
$oArticulos					= new Articulos();
$oPedidosRepuestos			= new PedidosRepuestos();
$oPedidosRepuestosDetalles	= new PedidosRepuestosDetalles();

$strParams = '';
$strParams.= '?Page=' 			. $Page;

if ($Submit)
{
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oUsuario								= $oUsuarios->GetById($IdUsuario);
		$oOrdenTrabajo							= $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
		$oTallerUnidad							= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
		
		$cur_time = date("d-m-Y H:i:s");
		
		if ($IdModalidad == Modalidades::Normal)
			$duration = '+72 hours';
		elseif ($IdModalidad == Modalidades::Urgente)
			$duration = '+36 hours';
		else 
			$duration = '+24 hours';
			
		//$FechaVencimiento						= date('Y-m-d H:i:s', strtotime($duration, strtotime($cur_time)));
		$oPedidoRepuesto						= new PedidoRepuesto();
		$oPedidoRepuesto->Fecha		 			= $Fecha;
		$oPedidoRepuesto->IdUsuario				= $oUsuario->IdUsuario;
		$oPedidoRepuesto->IdSector				= $IdSector;		
		$oPedidoRepuesto->IdOrdenTrabajo		= $oOrdenTrabajo->IdOrdenTrabajo;		
		$oPedidoRepuesto->Dominio				= $oTallerUnidad->Dominio;		
		$oPedidoRepuesto->IdModalidad			= $IdModalidad;	
		$oPedidoRepuesto->IdUsuarioGenerador	= $currentUser->IdUsuario;	
		
		
		$oPedidosRepuestos->Begin();
		try
		{	
			if ($oPedidoRepuesto = $oPedidosRepuestos->Create($oPedidoRepuesto))
			{
				$Ids = explode(',', $articulosSeleccionados);
			
				foreach ($Ids as $Id)
				{
					$oArticulo 							= $oArticulos->GetById($Id);
					$Cantidad 							= floatval($_REQUEST['cantidad_' . $Id]);
					$Precio 							= floatval($_REQUEST['precio_' . $Id]);
					$IdCargo 							= intval($_REQUEST['cargo_' . $Id]);
					$NumeroSap 							= strval($_REQUEST['NroSap_' . $Id]);
					
					$oPedidoRepuestoDetalle						= new PedidoRepuestoDetalle();
					$oPedidoRepuestoDetalle->IdPedidoRepuesto 	= $oPedidoRepuesto->IdPedidoRepuesto;
					$oPedidoRepuestoDetalle->IdArticulo 		= $oArticulo->IdArticulo;
					$oPedidoRepuestoDetalle->Cantidad 			= $Cantidad;
					$oPedidoRepuestoDetalle->Precio 			= $Precio;
					$oPedidoRepuestoDetalle->IdCargo 			= $IdCargo;
					$oPedidoRepuestoDetalle->NumeroSap 			= $NumeroSap;
					
					$oPedidosRepuestosDetalles->Create($oPedidoRepuestoDetalle);
				}
				
				$oPedidosRepuestos->Commit();
			}
		}
		catch (Exception $ex)
		{
			$oPedidosRepuestos->Rollback();
		}
		
		
		header("Location: pedidosrepuestos.php" . $strParams);
		exit();
		
	}
}
else
{
	$Fecha = date('d-m-Y');
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
	
	function FilterUsuario(IdUsuario, Nombre)
	{
		if ((IdUsuario == '') && (Nombre == ''))
		{
			Get('IdUsuario').value 	= '';
			Get('Usuario').value 	= '';
		}

		var oUsuario = GetUsuario(IdUsuario);
		if (!(oUsuario))
			return;

		Get('IdUsuario').value 	= oUsuario.IdUsuario;
		Get('Usuario').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
	}
	
	function FilterOrdenTrabajo(IdOrdenTrabajo, Dominio)
	{
		if ((IdOrdenTrabajo == '') && (Dominio == ''))
		{		
			$j('#Dominio').val('');
			$j('#IdOrdenTrabajo').val('');
		}

		var oOrdenTrabajo = GetOrdenTrabajo(IdOrdenTrabajo);
		if (!(oOrdenTrabajo))
			return;
			
		var oTallerUnidad = GetTallerUnidad(oOrdenTrabajo.IdTallerUnidad);
		if (!(oTallerUnidad))
			return;
			
		var oCliente = GetCliente(oTallerUnidad.IdCliente);
		if (!(oCliente))
			return;
		
		$j('#Dominio').val(oTallerUnidad.Dominio);
		$j('#Modelo').val(oTallerUnidad.Modelo);
		$j('#IdOrdenTrabajo').val(oOrdenTrabajo.IdOrdenTrabajo);
		$j('#NumeroVin').val(oTallerUnidad.NumeroVin);
		$j('#Kilometros').val(oOrdenTrabajo.Kilometros);
		$j('#FechaInicioGarantia').val(oTallerUnidad.FechaInicioGarantia);
		$j('#Cliente').val(oCliente.RazonSocial);
	}
	
	function validar(busqueda)
	{
		var result = true;
		
		$j('#trUsuarioError').hide();
		$j('#trSectorError').hide();
		$j('#trOrdenTrabajoError').hide();
		$j('#trModalidadError').hide();
		$j('#trArticulosError').hide();
		$j('.error_3').hide();
		
		if (!busqueda)
		{
			if ($j('#IdUsuario').val() == '' || $j('#IdUsuario').val() == '0')
			{
				$j('#trUsuarioError').show();
				result = false;
			}
			
			if ($j('#IdSector').val() == '' || $j('#IdSector').val() == '0')
			{
				$j('#trSectorError').show();
				result = false;
			}
			
			if (($j('#IdOrdenTrabajo').val() == '' || $j('#IdOrdenTrabajo').val() == '0') && $j('#IdSector').val() != '5' && $j('#IdSector').val() != '6')
			{
				$j('#trOrdenTrabajoError').show();
				result = false;
			}
			
			if ($j('#IdModalidad').val() == '' || $j('#IdModalidad').val() == '0')
			{
				$j('#trModalidadError').show();
				result = false;
			}
			
			if ($j('#articulosSeleccionados').val() == '' && !busqueda)
			{
				$j('#trArticulosError').show();
				result = false;
			}
		}
		else
		{
			if (($j('#Codigo').val() == '' && $j('#Descripcion').val() == ''))
			{
				$j('.error_3').show();
				result = false;
			}
		}
		
		return result;
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
		oArticulo = GetArticulo(IdArticulo);
		if (IdArticulo != '' && IdArticulo != null && IdArticulo != undefined) {
			if ($j('#id_' + IdArticulo).length == 0) {
				var articulosSeleccionados = $j('#articulosSeleccionados').val();
				if (articulosSeleccionados != '')
					articulosSeleccionados += ',';
				articulosSeleccionados += IdArticulo;
				$j('#articulosSeleccionados').val(articulosSeleccionados);
				
				$j('#modal-popup').dialog('close');
				
				var disponiblidad = "NO";
				
				if (oArticulo.Stocks.Rows[0].StockActual > 0)
					disponiblidad = "SI";
				
				var cantidad = 1;
				if (oArticulo.UnidadVenta)
					cantidad *= oArticulo.UnidadVenta;
				var row = "";
				row += "<tr id=\"row_" + oArticulo.IdArticulo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
				row += "	<td height=\"25\">";
				row += "		<div id=\"margen\">" + oArticulo.Codigo + "</div>";
				row += "		<input type=\"hidden\" id=\"id_" + oArticulo.IdArticulo + "\" name=\"id_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.IdArticulo + "\" />";
				row += "	</td>";
				row += "	<td height=\"25\">" + oArticulo.Descripcion + "</td>";
				row += "	<td height=\"25\">";
				row += "		<input type=\"text\" id=\"cantidad_" + oArticulo.IdArticulo + "\" name=\"cantidad_" + oArticulo.IdArticulo + "\" value=\"" + cantidad + "\" style=\"width:75px\" />";
				row += "		<input type=\"hidden\" id=\"unidadventa_" + oArticulo.IdArticulo + "\" name=\"unidadventa_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.UnidadVenta + "\" />";
				row += "	</td>";
				row += "	<td height=\"25\">";
				row += "		$<input type=\"text\" id=\"precio_" + oArticulo.IdArticulo + "\" name=\"precio_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.PrecioLista + "\" style=\"width:75px\" />";
				row += "	</td>";
				row += "	<td height=\"25\">" + disponiblidad + "</td>";
				row += "	<td height=\"25\">";
				row += "		<select id=\"cargo_" + oArticulo.IdArticulo + "\" name=\"cargo_" + oArticulo.IdArticulo + "\" style=\"width:75px\" >";
				<?php
				foreach (TipoVenta::GetAllPedidosRepuestos() as $oTipoVenta)
				{
				?>
				row += "			<option value=\"<?= $oTipoVenta['IdTipoVenta'] ?>\"><?= $oTipoVenta['Nombre'] ?></option>";
				<?php
				}
				?>
				row += "		</select>";
				row += "	</td>";
				row += "	<td height=\"25\">&nbsp;</td>";
				row += "	<td height=\"25\"><a href=\"javascript:QuitarArticulo(" + oArticulo.IdArticulo + ")\"><img src=\"images/iconos/del.gif\" alt=\"Quitar\" /></a></td>";
				row += "</tr>";
				
				$j('#articulos').append(row);
			}
		}
	}
	
	function QuitarArticulo(IdArticulo) {
		var articulosSeleccionados = $j('#articulosSeleccionados').val();
		var arrArticulos = articulosSeleccionados.split(',');
		
		articulosSeleccionados = '';
		for (var i = 0; i < arrArticulos.length; i++)
		{
			if (arrArticulos[i] != IdArticulo)
			{
				if (articulosSeleccionados != '')
					articulosSeleccionados += ',';
				articulosSeleccionados += arrArticulos[i];
			}
		}
		
		$j('#row_' + IdArticulo).remove();
		$j('#row_resumen_' + IdArticulo).remove();
		$j('#articulosSeleccionados').val(articulosSeleccionados);	
	}
	
	var finalizado = false;
	$j(document).ready(function() {
		
		$j('#btnFinalizar').click(function() {
			if (validar(false)) {
				if (!finalizado)
				{
					finalizado = true;
					$j(this).attr("disabled", "disabled");
					$j('#frmData').submit();					
				}
			}
		});
		
		$j('.siguiente').click(function() {
			var proximoTab = parseInt($j(this).attr('alt'));
			if (validar(false)) {
				var arrDisabled = new Array();
				for (i = proximoTab + 1; i < 3; i++)
					arrDisabled.push(i + 1);
				$j( "#tabs" ).tabs( "option", "disabled", arrDisabled);
				$j( "#tabs" ).tabs( "option", "selected", proximoTab);
			}
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
		
		$j('#Descripcion, #Codigo').keyup(function(key) {
			if (key.keyCode == 13)
				realizarBusqueda(1); 
		});
		$j('.buscar').click(function() { realizarBusqueda(1); });		
				
		<?php
		if ($IdOrdenTrabajo) {
		?>	
			FilterOrdenTrabajo(<?= $IdOrdenTrabajo ?>, <?= $Dominio ?>);
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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">CREAR PEDIDO DE REPUESTOS</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div id="tabs" align="center">
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="IdUsuario" id="IdUsuario" value="<?=$IdUsuario?>" />
					<input type="hidden" name="articulosSeleccionados" id="articulosSeleccionados" value="<?=$articulosSeleccionados?>" />
					
					<table width="90%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="33%">
								<table width="95%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><strong>Datos del Solicitante</strong></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
												<tr>		
													<td>
														<strong>Fecha: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMediano" name="Fecha" id="Fecha" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Fecha ?>" autocomplete="off" />											
														<script language="">												
															new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
														</script>												
														<span style="color:#FF0000;">&nbsp;(*)</span>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Solicitante: &nbsp;</strong>
													</td>
													<td>
														 <input type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="Off" />
														 <script language="javascript">
														 SUGGESTRequest('Usuarios', 'GetAllSuggest', 'Usuario', 'FilterUsuario', 'IdUsuario', 'Nombre', 'FilterUsuario');
														 </script>								
														<span style="color:#FF0000;">&nbsp;(*)</span>
													</td>
												</tr>
												<tr>
													<td colspan="2"><li id="trUsuarioError" style="color: red; display: none">Debe ingresar el usuario</li>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Area: &nbsp;</strong>
													</td>
													<td>
														 <select name="IdSector" id="IdSector" class="camporFormularioSuggest">
															<option value="">Seleccione el Area</option>
															<?php
															foreach (SectoresPostVenta::GetAll() as $oSectorPostVenta)
															{
																$selected = '';
																if ($oSectorPostVenta['IdSectorPostVenta'] == $IdSector)
																	$selected = 'selected="selected"';
															?>
															<option value="<?= $oSectorPostVenta['IdSectorPostVenta'] ?>" <?= $selected ?>><?= $oSectorPostVenta['Nombre'] ?></option>
															<?php
															}
															?>
														 </select>							
														<span style="color:#FF0000;">&nbsp;(*)</span>
													</td>
												</tr>
												<tr>
													<td colspan="2"><li id="trSectorError" style="color: red; display: none">Debe seleccionar el &aacute;rea</li>&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td valign="top" width="34%">
								<table width="95%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><strong>Datos de la unidad</strong></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<table width="100%" cellpadding="0" cellspacing="0" border="0">
												<tr>		
													<td>
														<strong>OT N&deg;: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="CamporFormularioMediano" name="IdOrdenTrabajo" id="IdOrdenTrabajo" onkeyup="javascript: StrToUpper(this.id);" value="<?= $IdOrdenTrabajo ?>" autocomplete="off" />												
														<span style="color:#FF0000;">&nbsp;(*)</span>											
														<script language="">												
															SUGGESTRequest('OrdenesTrabajo', 'GetAllPedidos', 'IdOrdenTrabajo', 'FilterOrdenTrabajo', 'IdOrdenTrabajo', 'Dominio', 'FilterIdOrdenTrabajo', null);
														</script>
													</td>
												</tr>
												<tr>
													<td colspan="2"><li id="trOrdenTrabajoError" style="color: red; display: none">Debe ingresar la OT</li>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Modelo: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="Modelo" id="Modelo" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Modelo ?>" readonly="readonly" />												
														
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Dominio: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="Dominio" id="Dominio" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Dominio ?>" readonly="readonly" />												
														
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Chasis: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="NumeroVin" id="NumeroVin" onkeyup="javascript: StrToUpper(this.id);" value="<?= $NumeroVin ?>" readonly="readonly" />												
														
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td width="33%" valign="top">
								<table width="95%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><strong>Modalidad</strong></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<table width="100%" cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td>
														<strong>Modalidad</strong>
													</td>
													<td>
														<select class="camporFormularioMediano" id="IdModalidad" name="IdModalidad">
															<option value="">Seleccione la modalidad</option>
															<?php
															foreach (Modalidades::GetAll() as $oModalidad)
															{
																$selected = '';
																if ($oModalidad['IdModalidad'] == $IdModalidad)
																	$selected = 'selected="selected"';
															?>
															<option value="<?= $oModalidad['IdModalidad'] ?>" <?= $selected ?>><?= $oModalidad['Nombre'] ?></option>
															<?php
															}
															?>
														</select>
														<span style="color:#FF0000;">&nbsp;(*)</span>	
													</td>
												</tr>
												<tr>
													<td colspan="2"><li id="trModalidadError" style="color: red; display: none">Debe seleccionar la modalidad</li>&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Kil&oacute;metros: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="Kilometros" id="Kilometros" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Kilometros ?>" readonly="readonly" />												
														
													</td>
												</tr>
												<tr>		
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Inicio Gtia.: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="FechaInicioGarantia" id="FechaInicioGarantia" onkeyup="javascript: StrToUpper(this.id);" value="<?= $FechaInicioGarantia ?>" readonly="readonly" />												
													</td>
												<tr>		
													<td colspan="2">&nbsp;</td>
												</tr>
												<tr>		
													<td>
														<strong>Cliente: &nbsp;</strong>
													</td>
													<td>
														<input type="text" class="camporFormularioMedianoDisabled" name="Cliente" id="Cliente" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Cliente ?>" readonly="readonly" />												
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="20">&nbsp;</td>
						</tr>
					</table>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
						
						<tr>
							<td height="20">&nbsp;</td>
						</tr>
						<tr>
							<td>
								<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td colspan="2">
											<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
												<tr>
													<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
													<td height="40" align="left"><span class="tituloPagina">SELECCIONE LOS REPUESTOS</span></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td align="center">
											<table border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td>
														<strong>C&oacute;digo:&nbsp;</strong>
													</td>
													<td>
														<input type="text" name="Codigo" id="Codigo" value="<?= $Codigo ?>" style="width: 250px" />
													</td>
												</tr>
											</table>
										</td>
										<td align="center">
											<table border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td>
														<strong>Descripci&oacute;n:&nbsp;</strong>
													</td>
													<td>
														<input type="text" name="Descripcion" id="Descripcion" value="<?= $Descripcion ?>" style="width: 250px" />
														<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" />
													</td>
												</tr>
											</table>
										</td>
										
									</tr>										
									<tr class="error_3" style="display:none">											
										<td align="center" colspan="6"><li style="color:#FF0000;">Para realizar la busqueda debe ingresar un c&oacute;digo y/o descripci&oacute;n</li></td>
									</tr>
									<tr class="error_4" style="display:none">											
										<td colspan="6"><li style="color:#FF0000;">Debe ingresar al menos un repuesto.</li></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="6">
											<table id="articulos" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="95%">
												<tr class="bordeGrisFondo">
													<td width="10%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
													<td width="30%" height="25" class="bordeGrisTitulo"><strong>Descripci&oacute;n</strong></td>	
													<td width="10%" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>				
													<td width="10%" height="25" class="bordeGrisTitulo"><strong>Precio</strong></td>
													<td width="10%" height="25" class="bordeGrisTitulo"><strong>Disp.</strong></td>
													<td width="10%" height="25" class="bordeGrisTitulo"><strong>Cargo</strong></td>
													<td width="10%" height="25" class="bordeGrisTitulo"><strong>Nro Pedido SAP</strong></td>
													<td width="10%" height="25" class="bordeGrisTitulo" align="center">&nbsp;</td>
												</tr>													
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2"><li id="trArticulosError" style="color: red; display: none">Debe seleccionar al menos un articulo</li>&nbsp;</td>
									</tr>									
								</table>
							</td>
						</tr>
						<tr>
							<td height="40">&nbsp;</td>
						</tr>
					</table>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30" align="center">
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pedidosrepuestos.php<?=$strParams?>';" value="Cancelar" />
								<input type="button" class="botonBasico" alt="4" id="btnFinalizar" value="Aceptar" />	
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

<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>

</body>
</html>