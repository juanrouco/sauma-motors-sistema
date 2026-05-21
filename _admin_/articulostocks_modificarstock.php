<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$TipoOperacion	= $_REQUEST['TipoOperacion'];	
$Codigo			= $_REQUEST['Codigo'];
$IdArticulo 	= $_REQUEST['IdArticulo'];
$IdUbicacion	= $_REQUEST['IdUbicacion'];
$Ubicacion		= $_REQUEST['Ubicacion'];
$Remito			= $_REQUEST['Remito'];
$Fecha			= $_REQUEST['Fecha'];

$articulosSeleccionados	= strval($_REQUEST['articulosSeleccionados']);

$Submit			= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err						= 0;
$oStockMovimiento			= new StockMovimiento();
$StockMovimientos			= new StockMovimientos();
$ArticuloStocks				= new ArticuloStocks();
$Articulos					= new Articulos();
$oPedidosRepuestosDetalles	= new PedidosRepuestosDetalles();
$oPedidosRepuestos			= new PedidosRepuestos();
$oUsuarios					= new Usuarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades			= new TallerUnidades();
$oClientes					= new Clientes();

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($IdUbicacion == '')
		$err += 1;
		
	if ($articulosSeleccionados == '')
		$err += 16;
		
	if ($Remito == '')
		$err += 2;	
	
	if ($Fecha == '')
		$err += 4;	
	
	$arrIdArticulos = explode(',', $articulosSeleccionados);
	foreach ($arrIdArticulos as $IdArticulo)
	{
		$oMovimientoExistente = $StockMovimientos->GetByArticuloAndUbicacionAndRemito($IdArticulo, $IdUbicacion, $Remito);
		if ($oMovimientoExistente && !($err & 32))
			$err += 32;
	}

	/* si no hay errores... */
	if ($err == 0)
	{	
		/*$from = "<servicios.aspenmotors@gmail.com>";
		//$to = "<juanmanuel.rouco@gmail.com>";
		
		
		$crlf = "\r\n";
		  
		$host = "ssl://smtp.gmail.com";
		$port = "465";
		$username = "servicios.aspenmotors@gmail.com";  //<> give errors
		$password = "Tolosa2013";*/
		
		foreach ($arrIdArticulos as $IdArticulo)
		{
			$oArticulo = $Articulos->GetById($IdArticulo);
			
			$arrPedidosRepuestosDetalles = $oPedidosRepuestosDetalles->GetAllPendientesByArticulo($oArticulo);
			
			foreach ($arrPedidosRepuestosDetalles as $oPedidoDetalle)
			{
				$oPedidoRepuesto = $oPedidosRepuestos->GetById($oPedidoDetalle->IdPedidoRepuesto);
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
				
				$oPedidoDetalle->Recibido = 1;
				$oPedidosRepuestosDetalles->Update($oPedidoDetalle);
				
				/*$mime = new Mail_mime($crlf);
				$subject = "Pedido de repuestos Nro " . $oPedidoDetalle->IdPedidoRepuesto . " - Repuesto " . $oArticulo->Codigo . " - " . $oArticulo->Descripcion . " recibido";
				$text = "El repuesto " . $oArticulo->Codigo . " - " . $oArticulo->Descripcion . ", solicitado con número " . $oPedidoDetalle->IdPedidoRepuesto . " ha sido recepcionado." . $crlf;
				$text.= "OT Nro: " . $oPedidoRepuesto->IdOrdenTrabajo . $crlf;
				$text.= "Modelo: " . $oTallerUnidad->Modelo . $crlf;
				$text.= "Dominio: " . $oPedidoRepuesto->Dominio . $crlf;
				$text.= "Cliente: " . $oCliente->RazonSocial . $crlf;

				$mime->setTXTBody($text);

				$body = $mime->get();

				//print_r($hdrs);exit;
				$smtp =& Mail::factory('smtp', array ('host' => $host,
							'port' => $port,
							'auth' => true,
							'username' => $username,
							'password' => $password));
							
							
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioGenerador = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioGenerador);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oUsuarioPedido = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioPedido);
				
				if ($oUsuario->Email)
				{	
					$headers = array ('From' => $from,
					  'To' => $oUsuario->Email,
					  'Subject' => $subject);
					$hdrs = $mime->headers($headers);
					$mail = $smtp->send($oUsuario->Email, $hdrs, $body);
				}
				
				$headers = array ('From' => $from,
					  'To' => 'ricardo.martin@victorhtolosa.com.ar',
					  'Subject' => $subject);
				$hdrs = $mime->headers($headers);
				$mail = $smtp->send('ricardo.martin@victorhtolosa.com.ar', $hdrs, $body);
				
				$headers = array ('From' => $from,
					  'To' => 'atilio.derango@victorhtolosa.com.ar',
					  'Subject' => $subject);
				$hdrs = $mime->headers($headers);
				$mail = $smtp->send('atilio.derango@victorhtolosa.com.ar', $hdrs, $body);
				
				$headers = array ('From' => $from,
					  'To' => 'leandro.tolosa@victorhtolosa.com.ar',
					  'Subject' => $subject);
				$hdrs = $mime->headers($headers);
				$mail = $smtp->send('leandro.tolosa@victorhtolosa.com.ar', $hdrs, $body);
				
				$headers = array ('From' => $from,
					  'To' => 'pablo.rewakowski@victorhtolosa.com.ar',
					  'Subject' => $subject);
				$hdrs = $mime->headers($headers);
				$mail = $smtp->send('pablo.rewakowski@victorhtolosa.com.ar', $hdrs, $body);
				
				$headers = array ('From' => $from,
					  'To' => 'mariano.mato@victorhtolosa.com.ar',
					  'Subject' => $subject);
				$hdrs = $mime->headers($headers);
				$mail = $smtp->send('mariano.mato@victorhtolosa.com.ar', $hdrs, $body);*/
				
			}
			
			$Cantidad	= $_REQUEST['cantidad_' . $IdArticulo];
			
			$oStockMovimiento->IdArticulo 	= $IdArticulo;
			$oStockMovimiento->IdUbicacion 	= $IdUbicacion;
			$oStockMovimiento->Remito		= $Remito;
			$oStockMovimiento->Fecha		= $Fecha;
			$oStockMovimiento->Cantidad		= $Cantidad;
			
			$oArticuloStocks = $ArticuloStocks->GetByArticuloAndUbicacion($IdArticulo, $IdUbicacion);
			if (!$oArticuloStocks)
			{
				$oArticuloStocks = new ArticuloStock();
				$oArticuloStocks->IdArticulo = $IdArticulo;
				$oArticuloStocks->IdUbicacion = $IdUbicacion;
				$oArticuloStocks->StockInicial = 0;
				$oArticuloStocks->StockActual = 0;
			}
			
			if ($TipoOperacion == '0')
				$oArticuloStocks->AumentarStock($Cantidad);
			else
			{
				$oStockMovimiento->Cantidad	= $Cantidad * -1;
				$oArticuloStocks->DisminuirStock($Cantidad);
			}
			
			if ($oArticuloStocks->IdArticuloStock)
				$oArticuloStocks = $ArticuloStocks->Update($oArticuloStocks);
			else
				$oArticuloStocks = $ArticuloStocks->Create($oArticuloStocks);
			
			/* crea el proveedor */
			$oStockMovimiento = $StockMovimientos->Create($oStockMovimiento);
		}
		
		header("Location: articulos.php");
		exit();
	}
}
else
{
	/* determinamos como fecha de alta */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
	$IdUbicacion = $currentUser->IdUbicacion;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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

function BuscarArticulo()
{
	realizarBusqueda(1);
}

function SetPage(page)
{
	realizarBusqueda(page);
}

function SetArticulo(IdArticulo)
{
	if (IdArticulo != '' && IdArticulo != null && IdArticulo != undefined) {
		if ($j('#id_' + IdArticulo).length == 0) {
			var articulosSeleccionados = $j('#articulosSeleccionados').val();
			if (articulosSeleccionados != '')
				articulosSeleccionados += ',';
			articulosSeleccionados += IdArticulo;
			$j('#articulosSeleccionados').val(articulosSeleccionados);
			
			oArticulo = GetArticulo(IdArticulo);
			 var oResult = GetPedidosRepuestos(oArticulo.IdArticulo);
			 
			 if (oResult.Resultado != '')
				alert(oResult.Resultado.replace('*', '\n'));

			var cantidad = 1;

			var row = "";
			row += "<tr id=\"row_" + oArticulo.IdArticulo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
			row += "	<td height=\"25\">";
			row += "		<div id=\"margen\">" + oArticulo.Codigo + "</div>";
			row += "		<input type=\"hidden\" id=\"id_" + oArticulo.IdArticulo + "\" name=\"id_" + oArticulo.IdArticulo + "\" value=\"" + oArticulo.IdArticulo + "\" />";			
			row += "	</td>";
			row += "	<td height=\"25\">" + oArticulo.Descripcion + "</td>";
			row += "	<td height=\"25\"><input type=\"text\" id=\"cantidad_" + oArticulo.IdArticulo + "\" name=\"cantidad_" + oArticulo.IdArticulo + "\"  value=\"" + cantidad + "\" style=\"width:50px;text-align: center\" /></td>";
			row += "	<td height=\"25\"><a href=\"javascript: QuitarArticulo(" + oArticulo.IdArticulo + ")\"><img src=\"images/iconos/del.gif\" /></a></td>";
			row += "</tr>";
			$j('#articulos').append(row);			
		} 
		$j('#modal-popup').dialog('close');
	}
}

function QuitarArticulo(IdArticulo) {
	if (IdArticulo != '' && IdArticulo != null && IdArticulo != undefined) {
		var articulosSeleccionados = $j('#articulosSeleccionados').val();
		var arrArticulos = articulosSeleccionados.split(',');
		
		articulosSeleccionados = '';
		for (var i = 0; i < arrArticulos.length; i++)
		{
			if (arrArticulos[i] != IdArticulo)
			{
				if (articulosSeleccionados != '')
					articulosSeleccionados += ',';
				articulosSeleccionados += IdArticulo;
			}
		}
		
		$j('#row_' + IdArticulo).remove();
		$j('#articulosSeleccionados').val(articulosSeleccionados);			
	}
}

function realizarBusqueda(page) {
	if ($j('#Codigo').val() != '') {
		var urlAjax = 'articulos_buscar_popup.php?FilterIdUbicacion=&FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=&Page=' + page;
		$j('body').addClass("loading"); 
		$j.ajax(urlAjax,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
				$j('.agregar').click(function() {
					var idArticulo = $j(this).attr('id').split('_')[1];							
					SetArticulo(idArticulo);
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

$j(document).ready(function() { 
	<?php
	if ($IdUbicacion) {
	?>
		FilterUbicacion(<?= $IdUbicacion ?>, '');
	<?php
	}
	?>	
	<?php
	if ($articulosSeleccionados && $articulosSeleccionados != '') 
	{
		$arrIdArticulos = split(',', $articulosSeleccionados);
		foreach ($arrIdArticulos as $IdArticulo)
		{
			$oArticulo = $Articulos->GetById($IdArticulo);
	?>
		
		SetArticulo('<?= $oArticulo->IdArticulo ?>');
	<?php
		}
	}
	?>	
	$j('#Codigo').keypress(function(e) {
		if (e.which == 13) {			
			BuscarArticulo();
			e.cancelBubble = true;
			e.returnValue = false;

			if (e.stopPropagation) {
				e.stopPropagation();
				e.preventDefault();
			} 
		}
	});
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
        			<td height="40"><span class="tituloPagina"><?= $TipoOperacion == '0'? 'Agregar Stock' : 'Quitar Stock' ?></span></td>
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
      		<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
	  			<input type="hidden" name="Submitted" id="Submitted" value="1" />
				<input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?= $IdUbicacion ?>" />
				<input type="hidden" name="articulosSeleccionados" id="articulosSeleccionados" value="" />

				<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
          			<tr>
            			<td class="bordeGris">
							<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td><div align="right">Sucursal:</div></td>
												<td>
													<div align="left">
														<input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" value="<?=$Ubicacion?>" autocomplete="off" />
														<input type="button" id="btnAddUbicacion" class="botonBasico"  onClick="javascript:AddUbicacion();" value=" + " />
														<span style="color:#FF0000;">&nbsp;(*)</span>
														<script language="">												
															SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
														</script>								
													</div>
												</td>
											</tr>
									   <?php if ($err & 1) { ?>
											<tr>
												<td>&nbsp;</td>
												<td><li style="color:#FF0000;">Ingrese la ubicaci&oacute;n</li></td>
											</tr>
									   <?php } ?>
											<tr>
												<td><div align="right">Remito:</div></td>
												<td>
													<div align="left">
														<input type="text" id="Remito" name="Remito"  class="camporFormularioSuggest" maxlength="255" value="<?=$Remito?>" />																						
														<span style="color:#FF0000;">&nbsp;(*)</span>
													</div>
												</td>
											</tr>
											<?php if ($err & 2) { ?>
											<tr>
												<td>&nbsp;</td>
												<td><li style="color:#FF0000;">Ingrese el remito</li></td>
											</tr>
											<?php } ?>
											<?php if ($err & 32) { ?>
											<tr>
												<td>&nbsp;</td>
												<td><li style="color:#FF0000;">El art&iacute;culo ya ha sido ingresado con este n&uacute;mero de remito.</li></td>
											</tr>
											<?php } ?>
											<tr>
												<td><div align="right">Fecha:</div></td>
												<td>
													<div align="left">
														<input type="text" name="Fecha" id="Fecha" class="camporFormularioSuggest" value="<?=$Fecha;?>" />											
														<script language="javascript">
															new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
														</script>
														<span style="color:#FF0000;">&nbsp;(*)</span>
													</div>
												</td>
											</tr>
											<?php if ($err & 4) { ?>
											<tr>
												<td>&nbsp;</td>
												<td><li style="color:#FF0000;">Debe ingresar una fecha</li></td>
											</tr>
											<?php } ?>
											<tr>
												<td><div align="right">C&oacute;digo Art&iacute;culo:</div></td>
												<td>
													<div align="left">
														<input type="text" name="Codigo" id="Codigo" class="camporFormularioSuggest" value="<?=$Codigo?>" />
														<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" onClick="javascript:BuscarArticulo();" style="margin-bottom: -6px;" />
														<span style="color:#FF0000;">&nbsp;(*)</span>																		
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
                                	<td>&nbsp;</td>
                                </tr>
								<tr>
									<td align="center">
										<table id="articulos" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="500">
											<tr class="bordeGrisFondo">
												<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
												<td width="200" height="25" class="bordeGrisTitulo"><strong>Descripci&oacute;n</strong></td>
												<td width="100" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>
												<td width="50" height="25" class="bordeGrisTitulo">&nbsp;</td>
											</tr>													
										</table>
									</td>
								</tr>
								<?php if ($err & 16) { ?>
                                <tr>
                                    <td><li style="color:#FF0000;">Debe ingresar un art&iacute;culo</li></td>
                                </tr>
								<?php } ?>
								<tr>
                                    <td>&nbsp;</td>
                                </tr>								
                        		<tr>
                                    <td>&nbsp;</td>
								</tr>

            				</table>						</td>
          			</tr>
        		</table>
				
   		        <table width="75%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="1"><div align="center"></div></td>
                  </tr>
                </table>
  <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          			<tr>
            			<td height="30">
              				<div align="center">
                				<input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
                				<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'articulos.php<?=$strParams?>';" value="Cancelar" />
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
<div id="modal-popup" style="display:none">
</div>
</body>
</html>