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
$IdTipoOperacion				= intval($_REQUEST['IdTipoOperacion']);
$IdCliente						= intval($_REQUEST['IdCliente']);
$IdComprobante					= intval($_REQUEST['IdComprobante']);
$IdComprobanteRemito			= intval($_REQUEST['IdComprobanteRemito']);
$NumeroComprobanteRemito		= strval($_REQUEST['NumeroComprobanteRemito']);
$NumeroComprobante				= strval($_REQUEST['NumeroComprobante']);
$Transporte						= strval($_REQUEST['Transporte']);
$TransporteClaveFiscalTipo		= intval($_REQUEST['TransporteClaveFiscalTipo']);
$TransporteClaveFiscalNumero	= strval($_REQUEST['TransporteClaveFiscalNumero']);
$articulosSeleccionados			= $_REQUEST['articulosSeleccionados'];
$IdCuponDescuento				= intval($_REQUEST['IdCuponDescuento']);
$PercepcionIIBB					= floatval($_REQUEST['PercepcionIIBB']);

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
$CuponesDescuento	= new CuponesDescuento();
$Ivas				= new Ivas();

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
		$oFactura								= $Comprobantes->GetById($IdComprobante);
		//$oRemito								= $Comprobantes->GetById($IdComprobanteRemito);
		$FechaCarga 							= date("Y-m-d");
		$FechaCarga 							= CambiarFecha($FechaCarga);
		$oCompra								= new Compra();
		$oCompra->IdUbicacion 					= $oUbicacion->IdUbicacion;
		$oCompra->TipoOperacion					= $IdTipoOperacion;
		$oCompra->FechaCarga					= $FechaCarga;		
		$oCompra->Transporte					= $Transporte;		
		$oCompra->TransporteClaveFiscalTipo		= $TransporteClaveFiscalTipo;		
		$oCompra->TransporteClaveFiscalNumero	= $TransporteClaveFiscalNumero;	
		$oCompra->IdCuponDescuento				= $IdCuponDescuento;
		$oCompra->IdTipoMovimiento 				= TipoMovimiento::Venta;
		if ($oFactura)
		{			
			$oCompra->IdFactura		= $oFactura->IdComprobante;
		}
		//$oCompra->IdRemito		= $oRemito->IdComprobante;
		if ($IdTipoOperacion == TipoVenta::Mostrador)
			$oCompra->IdCliente		= $oCliente->IdCliente;
		
		$Compras->Begin();
		try
		{
			if ($IdCuponDescuento)
			{
				$oCuponDescuento = $CuponesDescuento->GetById($IdCuponDescuento);
				$oCuponDescuento->IdEstado = ComprobanteEstados::Utilizado;
				$CuponesDescuento->Update($oCuponDescuento);
			}
			
			if ($oCompra = $Compras->Create($oCompra))
			{
				//$oRemito->IdEstado = ComprobanteEstados::Utilizado;			
				//$Comprobantes->Update($oRemito);
			}
			
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
				
				if ($IdTipoOperacion == TipoVenta::Mostrador)
				{
					$oCompraDetalle->ImporteUnidad 		= number_format($oArticulo->PrecioLista * ($oIva->Alicuota + 1), 2, '.' , '');
					$oCompraDetalle->ImporteCompraNeto 	= number_format($cantidad * $oArticulo->PrecioLista * ($oIva->Alicuota + 1), 2, '.' , '');
					$oCompraDetalle->PrecioCompra 		= $oArticulo->PrecioTerceros;
				}
				
				$TotalCompra += $oCompraDetalle->ImporteUnidad * $cantidad;
				if ($oIva->IdIva == Iva::Iva21)
					$TotalIva21 += ($oCompraDetalle->ImporteUnidad / 1.21) * 0.21 * $cantidad;
				elseif ($oIva->IdIva == Iva::Iva10)
					$TotalIva10 += ($oCompraDetalle->ImporteUnidad / 1.105) * 0.105 * $cantidad;
					
				
				
				$CompraDetalles->Create($oCompraDetalle);
				
				$oStockMovimiento = new StockMovimiento();
				$oStockMovimiento->IdArticulo			= $oArticulo->IdArticulo;
				$oStockMovimiento->IdUbicacion			= $oUbicacion->IdUbicacion;
				$oStockMovimiento->Remito 				= $oRemito->Numero;
				$oStockMovimiento->Fecha				= $FechaCarga;
				$oStockMovimiento->Cantidad				= $cantidad * -1;
				$oStockMovimiento->IdCompra				= $oCompra->IdCompra;
				
				$StockMovimientos->Create($oStockMovimiento);
				
				$oArticuloStock = $ArticuloStocks->GetByArticuloAndUbicacion($oArticulo->IdArticulo, $oUbicacion->IdUbicacion);
				$oArticuloStock->DisminuirStock($cantidad);
				$ArticuloStocks->Update($oArticuloStock);
			}
			
			$Subtotal = $TotalCompra - $TotalIva21 - $TotalIva10;
			$PercepcionTotal = 0;
			if ($oCliente->PercepcionIIBB && $oCliente->PercepcionIIBB > 0)
			{
				$PercepcionTotal = $Subtotal * $oCliente->PercepcionIIBB / 100;
			}
			$oCompra->Total = $TotalCompra + $PercepcionTotal;
			$oCompra->Iva21 = $TotalIva21;
			$oCompra->Iva10 = $TotalIva10;
			$oCompra->PercepcionIIBB = $PercepcionTotal;
			$Compras->Update($oCompra);
			
			if ($oFactura)
			{			
				$oFactura->IdEstado = ComprobanteEstados::Utilizado;			
				$oFactura->IdCliente = $IdCliente;			
				$oFactura->Importe = $TotalCompra;			
				$oFactura->Fecha = date('d-m-Y');
				$oFactura->ImporteIva21 = $TotalIva21;					
				$oFactura->ImporteIva10 = $TotalIva10;					
				$oFactura->PercepcionIIBB = $PercepcionTotal;					
				$Comprobantes->Update($oFactura);
			}
			
			$Compras->Commit();
		}
		catch (Exception $ex)
		{
			$Compras->Rollback();
		}
		
		
		header("Location: ventarepuestos.php" . $strParams);
		exit();
		
	}
}
else
{
	$IdUbicacion = 18;//$currentUser->IdUbicacion;
	$IdCliente = Cliente::IdConsumidorFinal;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
	var	IdTipoComprobanteRemito = '<?=ComprobanteTipos::Remito?>';
	var IdTipoComprobante = '';
	var arrParams = new Array();
	var arrParamsRemito = new Array();
	
	arrParams['Prefijo'] = '0002';

	function FilterCliente(IdCliente, Nombre)
	{
		if ((IdCliente == '') && (Nombre == ''))
		{		
			$j('#Cliente').val('');
			$j('#IdCliente').val('');
		}

		var oCliente = GetCliente(IdCliente);
		if (!(oCliente))
			return;
		
		$j('#Cliente').val(oCliente.RazonSocial);
		$j('#IdCliente').val(oCliente.IdCliente);
		$j('#DatoVenta').html(oCliente.RazonSocial);
		$j('#PercepcionIIBB').val(oCliente.PercepcionIIBB);
		
		//IdTipoComprobante = '<?=ComprobanteTipos::Remito?>';
		//arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
		
		//oComprobante = GetNextFactura(IdTipoComprobante, '0002');
		
		//SetNumeroComprobanteRemito(oComprobante.IdComprobante, oComprobante.Numero);
		
		if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
			return;
		
		/*if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaA?>')
		{		
			IdTipoComprobante = '<?=ComprobanteTipos::FacturaA?>';
			arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
		
			/* obtenemos la proxima factura */
			/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaA?>', '0002');
			
			Get('IdComprobante').value = oComprobante.IdComprobante;
			Get('NumeroComprobante').value = oComprobante.Numero;
			
			$j('#lblTipoFactura').html('FACTURA A');
			$j('#lblTipoFactura2').html('FACTURA A');
		}
		else if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaB?>')
		{
			IdTipoComprobante = '<?=ComprobanteTipos::FacturaB?>';
			arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

			/* obtenemos la proxima factura */
			/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaB?>', '0002');
			
			Get('IdComprobante').value = oComprobante.IdComprobante;
			Get('NumeroComprobante').value = oComprobante.Numero;
			$j('#lblTipoFactura').html('FACTURA B');
			$j('#lblTipoFactura2').html('FACTURA B');
		}*/
	}
	
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
	
	function SetNumeroComprobante(IdComprobante, NumeroComprobante)
	{
		Get('IdComprobante').value 		= IdComprobante;
		Get('NumeroComprobante').value 	= NumeroComprobante;
	}
	
	function SetNumeroComprobanteRemito(IdComprobante, NumeroComprobante)
	{
		Get('IdComprobanteRemito').value 		= IdComprobante;
		Get('NumeroComprobanteRemito').value 	= NumeroComprobante;
	}

	function GetNextFactura(IdTipoComprobante, prefijo)
	{
		var arr = new Array();
		var obj;
		var oComprobante;

		if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
			return;
					
		arr['IdTipoComprobante'] = IdTipoComprobante;
		arr['Prefijo'] = prefijo;
		obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
		if (obj.Status.Id != 0)
		{
			alert(obj.Status.Description);
			return;
		}
		
		oComprobante = obj.Response;

		return oComprobante;	
	}

	
	function validar(tab, busqueda)
	{	
		switch(tab) {
			case 0:
				if ($j('#IdTipoOperacion').val() == '0' && ($j('#IdCliente').val() == '' || $j('#IdCliente').val() == '0')) {
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
				break; /*
			case 3:
				if ($j('#IdComprobante').val() == '' || $j('#IdComprobante').val() == '0') {
					$j('.error_5').show();
					return false;
				} else {
					$j('.error_5').hide();
				}
				break;	*/			
			}

		return true;
	}
	
	function realizarBusqueda(page) {
		if (validar(2, true)) {
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
		
		var PercepcionIIBB = parseFloat($j('#PercepcionIIBB').val());
		if (PercepcionIIBB && PercepcionIIBB > 0)
		{
			var totalPercepcionIIBB = subtotal * PercepcionIIBB / 100;
			total+= totalPercepcionIIBB;
		}
		
		var numeroCupon = $j('#CuponDescuento').val();
		var descuento = 1;
		if (numeroCupon != '' && numeroCupon != undefined) {
			var oCupon = GetCupon(numeroCupon);
			if (oCupon) {
				descuento -= (oCupon.Descuento / 100);
				total = subtotal * descuento + iva + iva2;
			}
		}
		
		
		if (subtotal != 0)
		{
			$j('#lblSubtotalFin').html(roundVal(subtotal));
			if (PercepcionIIBB && PercepcionIIBB > 0)
			{
				$j('#lblPercepcionIIBBFin').html(roundVal(totalPercepcionIIBB) + ' (' + PercepcionIIBB + '%)');
			}
		}
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
		{
			$j('#lblSubtotal').html(roundVal(subtotal));
			var PercepcionIIBB = parseFloat($j('#PercepcionIIBB').val());
			if (PercepcionIIBB && PercepcionIIBB > 0)
			{
				var totalPercepcionIIBB = subtotal * PercepcionIIBB / 100;
				$j('#lblPercepcionIIBB').html(roundVal(totalPercepcionIIBB) + ' (' + PercepcionIIBB + '%)');
				total+= totalPercepcionIIBB;
			}
		}
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
				row += "		<input type=\"hidden\" id=\"stock_" + oArticulo.IdArticulo + "\" name=\"stock_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.Stocks.Rows[0].StockActual + "\" />";				
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
	
	function buscarCuponDescuento() {
		var numeroCupon = $j('#CuponDescuento').val();
		if (numeroCupon != '' && numeroCupon != undefined) {
			var oCupon = GetCupon(numeroCupon);
			if (oCupon) {
				$j('#IdCuponDescuento').val(oCupon.IdCuponDescuento);
				$j('#lblDescuentoFin').html(oCupon.Descuento);
				ActualizarTotalesFinal();
			}
		}
	}
	var finalizado = false;
	$j(document).ready(function() {
		$j( "#tabs" ).tabs({ 
			disabled: [1, 2] 
		});
		
		$j('#btnFinalizar').click(function() {
			if (validar(3, false)) {
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
			if (validar(proximoTab - 1, false)) {
				var arrDisabled = new Array();
				for (i = proximoTab + 1; i < 3; i++)
					arrDisabled.push(i);
				$j( "#tabs" ).tabs( "option", "disabled", arrDisabled);
				$j( "#tabs" ).tabs( "option", "selected", proximoTab);
				
					$j('#Codigo').focus();
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
		if ($IdCliente) {
		?>
			FilterCliente(<?= $IdCliente ?>, '');
		<?php
		}
		if ($IdUbicacion) {
		?>
			FilterUbicacion(<?= $IdUbicacion ?>, '');
		<?php
		}/*
		if ($IdComprobante) {
		?>	
			SetNumeroComprobante(<?= $IdComprobante ?>, <?= $NumeroComprobante ?>);
		<?php
		}*/
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
        			<td height="40"><span class="tituloPagina">VENTA DE REPUESTOS</span></td>
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
					<input type="hidden" name="PercepcionIIBB" id="PercepcionIIBB" value="<?=$PercepcionIIBB?>" />
					<input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?=$IdUbicacion?>" />
					<input type="hidden" name="articulosSeleccionados" id="articulosSeleccionados" value="" />
					<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?= $IdComprobante ?>" />
					<input type="hidden" name="IdComprobanteRemito" id="IdComprobanteRemito" value="<?= $IdComprobanteRemito ?>" />
					<input type="hidden" name="IdCuponDescuento" id="IdCuponDescuento" value="<?= $IdCuponDescuento ?>" />
					<input type="hidden" name="IdTipoOperacion" id="IdTipoOperacion" value="<?= TipoVenta::Mostrador ?>" />
					<ul>
						<li><a href="#cliente-unidad">DATOS CLIENTE</a></li>
						<li><a href="#repuestos">REPUESTOS</a></li>
						<li><a href="#facturas">FACTURACI&Oacute;N</a></li>
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
												<td height="40" align="left"><span class="tituloPagina">INGRESE EL CLIENTE</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<table width="80%"  border="0" cellpadding="0" cellspacing="0">		
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
										<tr>		
											<td width="20%" align="right">
												<strong>Cliente: &nbsp;</strong>
											</td>
											<td  width="80%">
												<input type="text" name="Cliente" id="Cliente" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Cliente ?>" autocomplete="off" style="width: 250px" />
												<input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddClienteResumen();" value=" + " />
												<span style="color:#FF0000;">&nbsp;(*)</span>
												<script language="">												
													SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
												</script>
											</td>
										</tr>
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
										
										<tr class="error_2" style="display:none">
											<td>&nbsp;</td>
											<td><li style="color:#FF0000;">Debe ingresar un cliente.</li></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_1" onclick="javascript: window.location.href = 'articulos.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico siguiente" alt="1" id="btnSiguiente_1" value="Siguiente" />									
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="repuestos">
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0"  class="bordeGris">
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
												<input type="text" name="Ubicacion" id="Ubicacion" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Ubicacion ?>" autocomplete="off" style="width: 250px" />												
											
											<script language="">												
												SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
											</script>
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
														<td width="75" height="25"><strong>Perc. IIBB:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblPercepcionIIBB">0</label></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_2" onclick="javascript: window.location.href = 'articulos.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico anterior" alt="2" id="btnAnterior_2" value="Anterior" />
									<input type="button" class="botonBasico siguiente" alt="2" id="btnSiguiente_2" value="Siguiente" />									
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id="facturas">
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0"  class="bordeGris">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">DATOS DEL CLIENTE</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<table width="90%%" border="0" cellpadding="0" cellspacing="0" align="center">	
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><label id="DatoVenta"></label></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><label id="lblTipoFactura"></label></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<div style="clear:both; height: 20px">&nbsp;</div>
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0"  class="bordeGris">
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
												<table bgcolor="transparent" border="0" align="center" cellpadding="0" cellspacing="0" width="715" style="margin-left: 86px">
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
														<td width="75" height="25"><strong>Perc. IIBB:&nbsp;</strong></td>
														<td width="75" height="25">$<label id="lblPercepcionIIBBFin">0</label></td>
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
						<div style="clear:both; height: 0px">&nbsp;</div>
						<table width="100%" border="0" cellpadding="5" cellspacing="0">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">DESCUENTO</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td class="bordeGris">
									<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
										<tr>
											<td width="98"><strong>Cup&oacute;n:&nbsp;</strong></td>
											<td>
												<input type="text" id="CuponDescuento" name="CuponDescuento" class="camporFormularioMedianoI" />
												<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar-cupon" style="margin-bottom: -6px" />
											</td>
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
						<div style="clear:both; height: 20px">&nbsp;</div>
						<div style="clear:both; height: 0px">&nbsp;</div>
						<?php /*
						<table width="100%" border="0" cellpadding="5" cellspacing="0"  class="bordeGris">
							<tr>
								<td style="padding: 0">
									<div align="center">
										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
											<tr>
												<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
												<td height="40" align="left"><span class="tituloPagina">DATOS FACTURACI&Oacute;N</span></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							
							<tr>
								<td>
									<table width="90%" border="0" cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>											
											<td>
												<strong><label id="lblTipoFactura2"></label>:&nbsp;</strong>
											</td>
											<td>
												<input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioMediano" maxlength="8" value="<?=$oComprobante->Numero?>" autocomplete="off" />
												<script language="javascript">
													arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
													SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
												</script>
												<span style="color:#FF0000;">&nbsp;(*)</span>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr class="error_5" style="display:none">											
											<td colspan="3"><li style="color:#FF0000;">Para finalizar la venta debe ingresar la factura.</li></td>
										</tr>
										
										<tr>
											<td>Trasporte:&nbsp;</td>
											<td>
												<textarea name="Transporte" id="Transporte" class="camporFormularioMediano" onkeyup="javascript: StrToUpper(this.id);" style="height: 75px"><?=$Transporte?></textarea>
											</td>
											<td>&nbsp;</td>
											<td>Transporte CUIT/CUIL:&nbsp;</td>
											<td>
												<select name="TransporteClaveFiscalTipo" id="TransporteClaveFiscalTipo" class="camporFormularioChico">
													<?php foreach (ClaveFiscalTipos::GetAll() as $oClaveFiscal) { ?>
													<option value="<?=$oClaveFiscal['IdTipo']?>" <?=($TransporteClaveFiscalTipo == $oClaveFiscal['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oClaveFiscal['Descripcion']?></option>
													<?php } ?>
												</select>
											</td>
											<td>&nbsp;</td>
											<td>
												<input type="text" name="TransporteClaveFiscalNumero" id="TransporteClaveFiscalNumero" class="camporFormularioMedianoI" maxlength="16" value="<?=$TransporteClaveFiscalNumero?>" />
											</td>
										</tr>									
									</table>
								</td>
							</tr>
							<tr>
								<td><div align="center"></div></td>
							</tr>
						</table>
						*/ ?>	
						<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
							<tr>
								<td height="30">
									<div align="right">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar_4" onclick="javascript: window.location.href = 'articulos.php<?=$strParams?>';" value="Cancelar" />
									<input type="button" class="botonBasico anterior" alt="3" id="btnAnterior_4" value="Anterior" />
									<input type="button" class="botonBasico" alt="3" id="btnFinalizar" value="Finalizar" />									
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