<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page							= intval($_REQUEST['Page']);
$IdUbicacion					= intval($_REQUEST['IdUbicacion']);
$IdTipoOperacion				= 2;
$IdCliente						= intval($_REQUEST['IdCliente']);
$IdOrdenTrabajo					= intval($_REQUEST['IdOrdenTrabajo']);
$IdOrdenTrabajoTarea			= intval($_REQUEST['IdOrdenTrabajoTarea']);
$Dominio						= strval($_REQUEST['Dominio']);
$articulosSeleccionados			= $_REQUEST['articulosSeleccionados'];

$Submit							= $_REQUEST['Submitted'];

/* declaracion de variables */
$err				= 0;
$Ubicaciones		= new Ubicaciones();
$Clientes			= new Clientes();
$TallerUnidades		= new TallerUnidades();
$Compras			= new Compras();
$Articulos			= new Articulos();
$CompraDetalles 	= new CompraDetalles();
$StockMovimientos	= new StockMovimientos();
$ArticuloStocks		= new ArticuloStocks();
$Comprobantes		= new Comprobantes();
$OrdenesTrabajo		= new OrdenesTrabajo();
$CuponesDescuento	= new CuponesDescuento();
$Ivas				= new Ivas();
$oGeneradorNotasCreditoVentas	= new GeneradorNotasCreditoVentas();

$arrIvas 			= $Ivas->GetAll();

$strParams = '';
$strParams.= '?Page=' 			. $Page;

if ($Submit)
{
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oUbicacion 							= $Ubicaciones->GetById($IdUbicacion);
		$oCliente								= $Clientes->GetById($IdCliente);
		$oOrdenTrabajo							= $OrdenesTrabajo->GetById($IdOrdenTrabajo);
		$oTallerUnidad							= $TallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
		$FechaCarga 							= date("Y-m-d");
		$FechaCarga 							= CambiarFecha($FechaCarga);
		$oCompra								= new Compra();
		$oCompra->IdUbicacion 					= $oUbicacion->IdUbicacion;
		$oCompra->TipoOperacion					= $IdTipoOperacion;
		$oCompra->FechaCarga					= $FechaCarga;		
		$oCompra->IdTipoMovimiento 				= TipoMovimiento::Devolucion;
		$NumeroVale = $Compras->GetNextNumeroVale();
		$oCompra->NumeroVale					 = $NumeroVale;
		
		$oCompra->IdTallerUnidad		= $oTallerUnidad->IdTallerUnidad;
		$oCompra->IdOrdenTrabajo 		= $oOrdenTrabajo->IdOrdenTrabajo;
		$oCompra->IdOrdenTrabajoTarea	= $IdOrdenTrabajoTarea;
		$Compras->Begin();
		try
		{
			$oCompra = $Compras->Create($oCompra);
			
			
			$Ids = explode(',', $articulosSeleccionados);
			$TotalCompra = 0;
			$TotalIva21 = 0;
			$TotalIva10 = 0;
			
			foreach ($Ids as $Id)
			{
				$oArticulo 							= $Articulos->GetById($Id);
				$oIva								= $Ivas->GetById($oArticulo->IdIva);
				$cantidad 							= floatval($_REQUEST['cantidad_' . $Id]);
				$oCompraDetalle						= new CompraDetalle();
				$oCompraDetalle->IdCompra 			= $oCompra->IdCompra;
				
				$oCompraDetalle->IdArticulo 		= $oArticulo->IdArticulo;
				$oCompraDetalle->Cantidad 			= $cantidad;
				
				$oCompraDetalleAux = $CompraDetalles->GetAllByTareaAndArticulo($IdOrdenTrabajoTarea, $oArticulo->IdArticulo);
					
				$oCompraDetalle->ImporteUnidad 		= $oCompraDetalleAux->ImporteUnidad;
				$oCompraDetalle->ImporteCompraNeto 	= $oCompraDetalleAux->ImporteUnidad * $cantidad;
				
				$TotalCompra += $oCompraDetalle->ImporteUnidad * $cantidad;
				if ($oIva->Alicuota == 0.21)
					$TotalIva21 += ($oCompraDetalle->ImporteUnidad / 1.21) * 0.21 * $cantidad;
				else
					$TotalIva10 += ($oCompraDetalle->ImporteUnidad / 1.105) * 0.105 * $cantidad;
				
				$CompraDetalles->Create($oCompraDetalle);
				
				$oStockMovimiento = new StockMovimiento();
				$oStockMovimiento->IdArticulo			= $oArticulo->IdArticulo;
				$oStockMovimiento->IdUbicacion			= $oUbicacion->IdUbicacion;
				$oStockMovimiento->Remito 				= $oRemito->Numero;
				$oStockMovimiento->Fecha				= $FechaCarga;
				$oStockMovimiento->Cantidad				= $cantidad;
				$oStockMovimiento->IdCompra				= $oCompra->IdCompra;
				
				$StockMovimientos->Create($oStockMovimiento);
				
				if ($oArticuloStock = $ArticuloStocks->GetByArticuloAndUbicacion($oArticulo->IdArticulo, $oUbicacion->IdUbicacion)) {
					$oArticuloStock->AumentarStock($cantidad);
					$ArticuloStocks->Update($oArticuloStock);
				}
			}
			$oCompra->Total = $TotalCompra;
			$oCompra->Iva21 = $TotalIva21;
			$oCompra->Iva10 = $TotalIva10;
			$Compras->Update($oCompra);
			
			$Compras->Commit();
			
			
			print_r("<script>window.open('ventarepuestos_vale_pdf.php?IdCompra=" . $oCompra->IdCompra . "');window.location.href='ventarepuestos_ot.php" . $strParams . "';</script>");
			exit;
			
		}
		catch (Exception $ex)
		{
			$Compras->Rollback();
		}
		
		
		header("Location: ventarepuestos_ot.php" . $strParams);
		exit();
		
	}
}
else
{
	$IdUbicacion = Ubicacion::Libertador;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
	var arrParams = new Array();
	var arrParamsRemito = new Array();

	function FilterUbicacion(IdUbicacion, Nombre)
	{
		if ((IdUbicacion == '') && (Nombre == ''))
		{		
			$j('#Ubicacion').val('');
			$j('#IdUbicacion').val('');
		}

		var oUbicacion = GetUbicacion(IdUbicacion);
		if (!(oUbicacion))
			return;
		
		$j('#Ubicacion').val(oUbicacion.Nombre);
		$j('#IdUbicacion').val(oUbicacion.IdUbicacion);
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
		if (!(oOrdenTrabajo))
			return;
		
		$j('#Dominio').val(oOrdenTrabajo.IdOrdenTrabajo);
		$j('#IdOrdenTrabajo').val(oOrdenTrabajo.IdOrdenTrabajo);
		$j('#lblTallerUnidad').html('OT N&deg;: ' + oOrdenTrabajo.IdOrdenTrabajo + ' - ' + oTallerUnidad.Modelo);
		$j('#DatoVenta').html('OT N&deg;: ' + oOrdenTrabajo.IdOrdenTrabajo + ' - ' + oTallerUnidad.Modelo);
		
		
		
		GetTareas(IdOrdenTrabajo);
	}
	
	function GetTareas(IdOrdenTrabajo)
	{
		if (!IdOrdenTrabajo)
			return;
		
		var arrTareas = GetOrdenesTrabajoTareas(IdOrdenTrabajo);
		var tareas = '<tr><td colspan="2"><strong>Tareas de la OT:</strong></td></tr>';
		
		for (var i=0; arrTareas && i<arrTareas.length; i++)
		{
			tareas += "	<tr>";
			tareas += "		<td width=\"20\">&nbsp;</td>";
			tareas += "		<td>";
			tareas += "			<label><input type=\"radio\" id=\"IdOrdenTrabajoTarea\" name=\"IdOrdenTrabajoTarea\" value=\"" + arrTareas[i].IdOrdenTrabajoTarea + "\" />&nbsp;" + arrTareas[i].Titulo + "</label>";
			tareas += "		</td>";
			tareas += "	</tr>";
		}
		$j('#tareasTrabajo').children().remove();
		$j('#tareasTrabajo').append(tareas);
	}
	
	
	function validar(tab, busqueda)
	{
		switch(tab) {
			case 0:
				if ($j('#IdTipoOperacion:checked').val() == '0' && ($j('#IdCliente').val() == '' || $j('#IdCliente').val() == '0') || $j('#IdTipoOperacion:checked').val() != '0' && ($j('#IdOrdenTrabajo').val() == '' || $j('#IdOrdenTrabajo').val() == '0' || $j('#IdOrdenTrabajoTarea:checked').length == 0)) {
					$j('.error_2').show();
					return false;
				} else {
					$j('.error_2').hide();
				}
				break;	
			case 1:
				if ($j('#IdUbicacion').val() == '' || $j('#IdUbicacion').val() == '0' || ($j('#Codigo').val() == '' && $j('#Descripcion').val() == '')) {
					$j('.error_3').show();
					return false;
				} else {
					$j('.error_3').hide();
					if ($j('#articulosSeleccionados').val() == '' && !busqueda) {
						$j('.error_4').show();
						return false;
					} else {
						$j('.error_4').hide();
						ActualizarTotalesFinal();
					}
				}
				break;
			case 2:
				$j('.error_5').hide();
				break;				
			}

		return true;
	}
	
	function realizarBusqueda(page) {
		if (validar(1, true)) {
			var urlAjax = 'articulos_buscar_popup.php?FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=' + $j('#Descripcion').val() + '&Page=' + page;
			if ($j('#IdOrdenTrabajo').val() && $j('#IdOrdenTrabajo').val() !='')
				urlAjax = 'articulos_buscar_popup_ot.php?FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=' + $j('#Descripcion').val() + '&Page=' + page + '&IdOrdenTrabajo=' + $j('#IdOrdenTrabajo').val() + '&IdOrdenTrabajoTarea=' + $j('#IdOrdenTrabajoTarea:checked').val();
			$j('body').addClass("loading"); 
			$j.ajax(urlAjax,{
				success: function(data) {
					$j('#modal-popup').html(data);	
					$j('body').removeClass("loading"); 
					$j('.agregar').click(function() {
						var idArticulo = $j(this).attr('id').split('_')[1];	
						if ($j('#IdOrdenTrabajo').val() && $j('#IdOrdenTrabajo').val() !='')
						{
							oArticulo = GetArticulo(idArticulo);
			
							var Cantidad = $j('#Cantidad_' + idArticulo).val();	
							var CantidadActual = 0;
							if ($j('#id_' + idArticulo).length != 0) 
								CantidadActual = parseInt($j('#cantidad_' + idArticulo).val());	
							CantidadActual+= parseInt(oArticulo.UnidadVenta);
							if (Cantidad < CantidadActual)
								alert('Puede devolver hasta ' + Cantidad + ' ' + oArticulo.Descripcion);
							else
								AgregarArticulo(idArticulo, Cantidad);
						}
						else
						{
							AgregarArticulo(idArticulo, 0);
						}
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
	
	function ActualizarTotalesFinal() {
		var articulosSeleccionados = $j('#articulosSeleccionados').val();
		var arrArticulos = articulosSeleccionados.split(',');
		var total = 0;
		var subtotal = 0;
		var iva = 0;
		var iva2 = 0;
		for (var i = 0; i < arrArticulos.length; i++)
		{
			if (arrArticulos[i] != '') {
				subtotal += parseFloat($j('#subtotal_' + arrArticulos[i]).val());
				iva += parseFloat($j('#iva_' + arrArticulos[i]).val());
				iva2 += parseFloat($j('#iva2_' + arrArticulos[i]).val());
				total += parseFloat($j('#total_' + arrArticulos[i]).val());
			}
		}
		
		if (subtotal != 0)
			$j('#lblSubtotalFin').html(roundVal(subtotal));
		else
			$j('#lblSubtotalFin').html('0');
			
		if (total != 0)
			$j('#lblTotalFin').html(roundVal(total));
		else
			$j('#lblTotalFin').html('0');
			
		if (iva != 0)
			$j('#lblIvaFin').html(roundVal(iva));
		else
			$j('#lblIvaFin').html('0');
			
		if (iva2 != 0)
			$j('#lblIva2Fin').html(roundVal(iva2));
		else
			$j('#lblIva2Fin').html('0');
	}
	
	function ActualizarTotales() {
		var articulosSeleccionados = $j('#articulosSeleccionados').val();
		var arrArticulos = articulosSeleccionados.split(',');
		var total = 0;
		var subtotal = 0;
		var iva = 0;
		var iva2 = 0;
		for (var i = 0; i < arrArticulos.length; i++)
		{
			if (arrArticulos[i] != '') {
				subtotal += parseFloat($j('#subtotal_' + arrArticulos[i]).val());
				iva += parseFloat($j('#iva_' + arrArticulos[i]).val());
				iva2 += parseFloat($j('#iva2_' + arrArticulos[i]).val());
				total += parseFloat($j('#total_' + arrArticulos[i]).val());
			}
		}
		
		if (subtotal != 0)
			$j('#lblSubtotal').html(roundVal(subtotal));
		else
			$j('#lblSubtotal').html('0');
			
		if (total != 0)
			$j('#lblTotal').html(roundVal(total));
		else
			$j('#lblTotal').html('0');
			
		if (iva != 0)
			$j('#lblIva').html(roundVal(iva));
		else
			$j('#lblIva').html('0');
			
		if (iva2 != 0)
			$j('#lblIva2').html(roundVal(iva2));
		else
			$j('#lblIva2').html('0');
	}
	
	function AgregarArticulo(IdArticulo, CantidadMaxima) {
		oArticulo = GetArticulo(IdArticulo);
		if (IdArticulo != '' && IdArticulo != null && IdArticulo != undefined) {
			if ($j('#id_' + IdArticulo).length == 0) {
				var articulosSeleccionados = $j('#articulosSeleccionados').val();
				if (articulosSeleccionados != '')
					articulosSeleccionados += ',';
				articulosSeleccionados += IdArticulo;
				$j('#articulosSeleccionados').val(articulosSeleccionados);
				
				$j('#modal-popup').dialog('close');
				
				
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
				row += "		<input type=\"hidden\" id=\"subtotal_" + oArticulo.IdArticulo + "\" name=\"subtotal_" + oArticulo.IdArticulo + "\" value=\"" + (oArticulo.PrecioLista * cantidad) + "\" />";
				if (oArticulo.IdIva == 1) {
					row += "		<input type=\"hidden\" id=\"iva_" + oArticulo.IdArticulo + "\" name=\"iva_" + oArticulo.IdArticulo + "\" value=\"" + roundVal(oArticulo.PrecioLista * (oIva - 1) * cantidad) + "\" />";
					row += "		<input type=\"hidden\" id=\"iva2_" + oArticulo.IdArticulo + "\" name=\"iva2_" + oArticulo.IdArticulo + "\" value=\"0\" />";
				} else {
					row += "		<input type=\"hidden\" id=\"iva_" + oArticulo.IdArticulo + "\" name=\"iva_" + oArticulo.IdArticulo + "\" value=\"0\" />";
					row += "		<input type=\"hidden\" id=\"iva2_" + oArticulo.IdArticulo + "\" name=\"iva2_" + oArticulo.IdArticulo + "\" value=\"" + roundVal(oArticulo.PrecioLista * (oIva - 1) * cantidad) + "\" />";
				}
				row += "		<input type=\"hidden\" id=\"total_" + oArticulo.IdArticulo + "\" name=\"total_" + oArticulo.IdArticulo + "\" value=\"" + roundVal(oArticulo.PrecioLista * oIva * cantidad) + "\" />";
				row += "		<input type=\"hidden\" id=\"cantidad_anterior_" + oArticulo.IdArticulo + "\" name=\"cantidad_anterior_" + oArticulo.IdArticulo + "\" value=\"" + cantidad + "\" />";
				if ($j('#IdOrdenTrabajo').val() && $j('#IdOrdenTrabajo').val() !='') {
					row += "		<input type=\"hidden\" id=\"cantidadMaxima_" + oArticulo.IdArticulo + "\" name=\"cantidadMaxima_" + oArticulo.IdArticulo + "\" value=\"" + CantidadMaxima + "\" />";	
				} else {
					row += "		<input type=\"hidden\" id=\"stock_" + oArticulo.IdArticulo + "\" name=\"stock_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.Stocks.Rows[0].StockActual + "\" />";		
				}				
				row += "	</td>";
				row += "	<td height=\"25\">" + oArticulo.Descripcion + "</td>";
				row += "	<td height=\"25\" align=\"center\">$" + roundVal(oArticulo.PrecioLista) + "<input type=\"hidden\" id=\"PrecioUnitario_" + oArticulo.IdArticulo + "\" name=\"PrecioUnitario_" + oArticulo.IdArticulo + "\" value=\"" + roundVal(oArticulo.PrecioLista * oIva) + "\" /></td>";
				row += "	<td height=\"25\" align=\"center\">$" + roundVal(oArticulo.PrecioLista * oIva) + "</td>";
				row += "	<td height=\"25\">";
				row += "		<input type=\"text\" id=\"cantidad_" + oArticulo.IdArticulo + "\" name=\"cantidad_" + oArticulo.IdArticulo + "\" value=\"" + cantidad + "\" style=\"width:50px\" />";
				row += "		<input type=\"hidden\" id=\"unidadventa_" + oArticulo.IdArticulo + "\" name=\"unidadventa_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.UnidadVenta + "\" style=\"width=50px\" />";
				row += "	</td>";
				row += "	<td height=\"25\" align=\"center\">$<label id=\"Precio_" + oArticulo.IdArticulo + "\">" + roundVal(oArticulo.PrecioLista * cantidad) + "</label></td>";
				row += "	<td height=\"25\"><a href=\"javascript:QuitarArticulo(" + oArticulo.IdArticulo + ")\"><img src=\"images/iconos/del.gif\" alt=\"Quitar\" /></a></td>";
				row += "</tr>";
				
				
				var row_resumen = "";
				row_resumen += "<tr id=\"row_resumen_" + oArticulo.IdArticulo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
				row_resumen += "	<td height=\"25\">";
				row_resumen += "		<div id=\"margen\">" + oArticulo.Codigo + "</div>";
				row_resumen += "	</td>";
				row_resumen += "	<td height=\"25\">" + oArticulo.Descripcion + "</td>";
				row_resumen += "	<td height=\"25\" align=\"center\">$" + roundVal(oArticulo.PrecioLista) + "</td>";
				row_resumen += "	<td height=\"25\" align=\"center\">$" + roundVal(oArticulo.PrecioLista * oIva) + "</td>";
				row_resumen += "	<td height=\"25\">";
				row_resumen += "		<label id=\"cantidad_resumen_" + oArticulo.IdArticulo + "\">" + cantidad + "</label>";
				row_resumen += "	</td>";
				row_resumen += "	<td height=\"25\" align=\"center\">$<label id=\"Precio_resumen_" + oArticulo.IdArticulo + "\">" + roundVal(oArticulo.PrecioLista * cantidad) + "</label></td>";
				row_resumen += "	<td height=\"25\" align=\"center\"><a href=\"javascript:QuitarArticulo(" + oArticulo.IdArticulo + ")\"><img src=\"images/iconos/del.gif\" alt=\"Quitar\" /></a></td>";
				row_resumen += "</tr>";
				
				
				$j('#articulos').append(row);
				$j('#articulos_resumen').append(row_resumen);
				$j('#cantidad_' + oArticulo.IdArticulo).change(function(e) {
					var idAux = parseInt($j(this).attr('id').split('_')[1]);
					var cantidad = parseInt($j(this).val());
					var precio = parseFloat($j('#PrecioUnitario_' + idAux).val());
					var unidadventa = parseInt($j('#unidadventa_' + idAux).val());
					var cantidad_anterior = parseInt($j('#cantidad_anterior_' + idAux).val());
					if (cantidad && cantidad % unidadventa != 0)
					{
						alert('Atención: Este artículo se vende en multiplos de ' + unidadventa + '.');
						$j(this).val(cantidad_anterior);
					}
					else
					{
						if ($j('#IdOrdenTrabajo').val() && $j('#IdOrdenTrabajo').val() !='') {
							var cantidadMaxima = parseInt($j('#cantidadMaxima_' + idAux).val());
							if (cantidad > cantidadMaxima) {
								alert('Puede devolver hasta ' + cantidadMaxima + ' ' + oArticulo.Descripcion);
								$j(this).val(cantidad_anterior);
							} else {
									$j('#cantidad_resumen_' + oArticulo.IdArticulo).html($j(this).val());	
									$j('#Precio_' + idAux).html(roundVal(precio * cantidad));
									$j('#Precio_resumen_' + idAux).html(roundVal(precio * cantidad));
									
									$j('#cantidad_anterior_' + idAux).val(cantidad);
									
									var subtotal = parseFloat($j('#subtotal_' + idAux).val());
									subtotal = (subtotal / cantidad_anterior) * cantidad;
									$j('#subtotal_' + idAux).val(roundVal(subtotal));
									
									var iva = parseFloat($j('#iva_' + idAux).val());
									iva = (iva / cantidad_anterior) * cantidad;
									$j('#iva_' + idAux).val(roundVal(iva));
									
									var total = parseFloat($j('#total_' + idAux).val());
									total = (total / cantidad_anterior) * cantidad;
									$j('#total_' + idAux).val(roundVal(total));
								}
						}
						else
						{
							if (cantidad && precio) {
								var stock = parseInt($j('#stock_' + idAux).val());
								
								if (stock < cantidad) {
									$j(this).val(cantidad_anterior);
									alert('La cantidad ingresada es mayor al stock actual de ' + stock + ' unidades.');
								} else {
									$j('#cantidad_resumen_' + oArticulo.IdArticulo).html($j(this).val());	
									$j('#Precio_' + idAux).html(roundVal(precio * cantidad));
									$j('#Precio_resumen_' + idAux).html(roundVal(precio * cantidad));
									
									$j('#cantidad_anterior_' + idAux).val(cantidad);
									
									var subtotal = parseFloat($j('#subtotal_' + idAux).val());
									subtotal = (subtotal / cantidad_anterior) * cantidad;
									$j('#subtotal_' + idAux).val(roundVal(subtotal));
									
									var iva = parseFloat($j('#iva_' + idAux).val());
									iva = (iva / cantidad_anterior) * cantidad;
									$j('#iva_' + idAux).val(roundVal(iva));
									
									var total = parseFloat($j('#total_' + idAux).val());
									total = (total / cantidad_anterior) * cantidad;
									$j('#total_' + idAux).val(roundVal(total));
								}
							}
						}
						ActualizarTotales();
					}
				});
			} else {					
				$j('#cantidad_' + IdArticulo).val(parseInt($j('#cantidad_' + IdArticulo).val()) + parseInt(oArticulo.UnidadVenta));
				var cantidad = parseInt($j('#cantidad_' + IdArticulo).val());
				var precio = parseFloat($j('#PrecioUnitario_' + IdArticulo).val());
				if (cantidad && precio) {
					$j('#Precio_' + IdArticulo).html(roundVal(precio * cantidad));
				}
				$j('#modal-popup').dialog('close');
			}
			ActualizarTotales();
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
		ActualizarTotales();
		ActualizarTotalesFinal();
	}
	
	$j(document).ready(function() {
		$j( "#tabs" ).tabs({ 
			disabled: [1, 2] 
		});
		
		$j('#btnFinalizar').click(function() {
			if (validar(2, false)) {
				$j('#frmData').submit();
			}
		});
		
		$j('.siguiente').click(function() {
			var proximoTab = parseInt($j(this).attr('alt'));
			if (validar(proximoTab - 1, false)) {
				var arrDisabled = new Array();
				for (i = proximoTab + 1; i < 3; i++)
					arrDisabled.push(i + 1);
				$j( "#tabs" ).tabs( "option", "disabled", arrDisabled);
				$j( "#tabs" ).tabs( "option", "selected", proximoTab);
			}
		});
		
		$j('.anterior').click(function() {
			var proximoTab = parseInt($j(this).attr('alt'));						
			$j( "#tabs" ).tabs( "option", "selected", proximoTab - 2);			
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
		$j('.buscar-cupon').click(function() { buscarCuponDescuento(); });
				
		<?php
		if ($IdUbicacion) {
		?>
			FilterUbicacion(<?= $IdUbicacion ?>, '');
		<?php
		}
		
		if ($IdOrdenTrabajo) {
		?>	
			FilterOrdenTrabajo('<?= $IdOrdenTrabajo ?>', '<?= $Dominio ?>');
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
        			<td height="40"><span class="tituloPagina">Devoluci&oacute;n de Repuestos</span></td>
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
					<input type="hidden" name="IdCliente" id="IdCliente" value="<?=$IdCliente?>" />
					<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
					<input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?=$IdUbicacion?>" />
					<input type="hidden" name="articulosSeleccionados" id="articulosSeleccionados" value="" />
					<input type="hidden" name="IdCuponDescuento" id="IdCuponDescuento" value="<?= $IdCuponDescuento ?>" />
					<ul>
						<li><a href="#cliente-unidad">ORDEN DE REPUESTO</a></li>
						<li><a href="#repuestos">REPUESTOS</a></li>
						<li><a href="#facturas">CONFIRMAR</a></li>
					</ul>
					
					<div id="cliente-unidad">
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%"  border="0" cellpadding="5" cellspacing="0"  class="bordeGris">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">INGRESE LA ORDEN DE TRABAJO</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<table width="90%"  border="0" cellpadding="0" cellspacing="0" align="center">
										
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>		
											<td>
												<strong>OT N&deg;: &nbsp;</strong>
											</td>
											<td>
												<input type="text" name="Dominio" id="Dominio" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Dominio ?>" autocomplete="off" style="width: 250px" />												
												<span style="color:#FF0000;">&nbsp;(*)</span>
												<br />
												<label for="Dominio" id="lblTallerUnidad"></label>												
												<script language="">												
													SUGGESTRequest('OrdenesTrabajo', 'GetAll', 'Dominio', 'FilterOrdenTrabajo', 'IdOrdenTrabajo', 'Dominio', 'FilterIdOrdenTrabajo', null);
												</script>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="2">
												<table id="tareasTrabajo" border="0" cellpadding="0" cellspacing="0">
												</table>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr class="error_2" style="display:none">											
											<td colspan="3"><li style="color:#FF0000;">Debe seleccionar una OT y seleccionar una tarea.</li></td>
										</tr>															
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><div align="center"></div></td>
							</tr>
						</table>
						<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
							<tr>
								<td height="30">
									<div align="right">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_2" onclick="javascript: window.location.href = 'ventarepuestos_ot.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico siguiente" alt="1" id="btnSiguiente_2" value="Siguiente" />									
									</div>
								</td>
							</tr>
						</table>
					</div>
					
					<div id="repuestos">
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bordeGris">
						<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">SELECCIONE LOS REPUESTOS</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">										
										<tr>											
											<td>
												<strong>Sucursal:&nbsp;</strong>
											</td>
											<td>
												<input type="text" name="Ubicacion" id="Ubicacion" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Ubicacion ?>" autocomplete="off" style="width: 250px" readonly="yes" />												
											</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
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
											<td colspan="6"><li style="color:#FF0000;">Para realizar la busqueda debe ingresar una sucursal; un c&oacute;digo y/o descripci&oacute;n</li></td>
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
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Unitario</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Unitario (c/IVA)</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Subtotal</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center">&nbsp;</td>
													</tr>													
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="6">
												<table bgcolor="transparent" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="715" style="margin-left: 75px">
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Subtotal:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblSubtotal">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Iva 21%:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblIva">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Iva 10,5%:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblIva2">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Total:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblTotal">0</label></td>
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
						<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
							<tr>
								<td height="30">
									<div align="right">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_3" onclick="javascript: window.location.href = 'ventarepuestos_ot.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico anterior" alt="2" id="btnAnterior_3" value="Anterior" />
									<input type="button" class="botonBasico siguiente" alt="2" id="btnSiguiente_3" value="Siguiente" />									
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="facturas">
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bordeGris">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">DATOS DE LA ORDEN DE TRABAJO</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td class="bordeGris">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">																				
										<tr>
											<td><label id="TipoVenta"></label></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><label id="DatoVenta"></label></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<div style="clear:both; height: 20px">&nbsp;</div>
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bordeGris">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">REPUESTOS ASIGNADOS</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">																				
										<tr>
											<td>
												<table id="articulos_resumen" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
													<tr class="bordeGrisFondo">
														<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
														<td width="150" height="25" class="bordeGrisTitulo"><strong>Descripci&oacute;n</strong></td>					
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Unitario</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Unitario (c/IVA)</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center"><strong>Subtotal</strong></td>
														<td width="75" height="25" class="bordeGrisTitulo" align="center">&nbsp;</td>
													</tr>													
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<table bgcolor="transparent" border="0" align="center" cellpadding="0" cellspacing="0" width="90%">
													<tr>
														<td colspan="5" height="25">&nbsp;</td>														
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Subtotal:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblSubtotalFin">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Descuento:&nbsp;</strong></td>
														<td width="75" height="25"><label id="lblDescuentoFin">0</label>%</td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Iva 21%:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblIvaFin">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Iva 10,5%:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblIva2Fin">0</label></td>
													</tr>
													<tr>
														<td width="150" height="25"><div id="margen">&nbsp;</div></td>
														<td width="150" height="25">&nbsp;</td>					
														<td width="75" height="25">&nbsp;</td>
														<td width="75" height="25"><strong>Total:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblTotalFin">0</label></td>
													</tr>													
												</table>
											</td>
										</tr>	
									</table>
								</td>
							</tr>
						</table>
						<div style="clear:both; height: 20px">&nbsp;</div>
						<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
							<tr>
								<td height="30">
									<div align="right">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_4" onclick="javascript: window.location.href = 'ventarepuestos_ot.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico anterior" alt="4" id="btnAnterior_4" value="Anterior" />
									<input type="button" class="botonBasico" alt="4" id="btnFinalizar" value="Finalizar" />									
									</div>
								</td>
							</tr>
						</table>
					</div>
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