<?php 

require_once('../inc_library.php');

/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['FechaDesde'] 		= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']		= $_REQUEST['FilterFechaHasta'];
$filter['IdUbicacion']		= $_REQUEST['FilterIdUbicacion'];
$filter['TipoOperacion']	= $_REQUEST['FilterTipoOperacion'];
$filter['Codigo']			= $_REQUEST['FilterCodigo'];
$filter['IdArticulo']		= $_REQUEST['IdArticulo'];
$filter['IdTipoPago']		= $_REQUEST['FilterIdTipoPago'];

$IdArticulo 	= $_REQUEST['IdArticulo'] ;
$IdUbicacion 	= $_REQUEST['FilterIdUbicacion'] ;

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if ($filter['FechaDesde'] != '' || $filter['FechaHasta'] != '' || $filter['IdUbicacion'] != '')
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

/* declaracion de variables */
$arr = array();

$Ubicaciones 		= new Ubicaciones();
$Articulos	 		= new Articulos();
$ArticuloStocks 	= new ArticuloStocks();
$StockMovimientos 	= new StockMovimientos();
$Comprobantes	 	= new Comprobantes();
$Compras			= new Compras();
$Clientes			= new Clientes();
$TallerUnidades		= new TallerUnidades();
$oCompraDetalles	= new CompraDetalles();

$oPage 				= new Page($Page);
$oPage->Size 		= 20;

$arrUbicaciones 	= $Ubicaciones->GetAll();

/* SOLUCION TEMPORAL PARA EL PAGINADOR */
/*
if ($Page > $StockMovimientos->GetPagesCountReporte($oPage, $filter))
	$Page = $StockMovimientos->GetPagesCountReporte($oPage, $filter);

$oPage 		= new Page($Page);
$oPage->Size = 20;*/
//$arr 			= $StockMovimientos->GetAllReporte($filter, $oPage);
//$CountRows		= $StockMovimientos->GetCountRowsReporte($filter);
//$Paginado		= Pageable::PrintPaginator($oPage, $StockMovimientos->GetPagesCountReporte($oPage, $filter), $CountRows);
$filter['NotOrdenTrabajo'] = true;
$filter['TipoOperacion'] = TipoVenta::Mostrador;
//$oReporteTotal 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);

$filter['NotOrdenTrabajo'] = null;
$filter['OrdenTrabajo'] = true;
$filter['TipoOperacion'] = TipoVenta::OrdenReparacion;
$oReporteTotalCliente 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalClienteFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);

$filter['TipoOperacion'] = TipoVenta::Garantia;
$oReporteTotalGarantia 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalGarantiaFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);

$filter['TipoOperacion'] = TipoVenta::VentaInterna;
$oReporteTotalVentaInterna 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalVentaInternaFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);

$filter['TipoOperacion'] = TipoVenta::DaniosYFaltantes;
$oReporteTotalDanios 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalDaniosFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);
/*
$filter['TipoOperacion'] = TipoVenta::PreEntrega;
$oReporteTotalPreEntrega 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::ChapaYPintura;
$oReporteTotalChapaYPintura 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::Accesorios;
$oReporteTotalAccesorios 	= $StockMovimientos->GetTotalReporte($filter);*/

$oTipoVenta	= TipoVenta::GetById($filter['TipoOperacion']);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 					. $Page;
$strParams.= '&FilterFechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&FilterIdUbicacion=' 		. $filter['IdUbicacion'];
$strParams.= '&FilterTipoOperacion=' 	. $filter['TipoOperacion'];
$strParams.= '&FilterCodigo=' 			. $filter['Codigo'];
$strParams.= '&FilterIdTipoPago=' 			. $filter['IdTipoPago'];
$strParams.= '&IdArticulo=' 			. $filter['IdArticulo'];

$RentabilidadFord = $TotalFord / $CompraFord * 100 - 100;
if ($CompraTerceros != 0)
	$RentabilidadTerceros = $TotalTerceros / $CompraTerceros * 100 - 100;
else 
	$RentabilidadTerceros = 0;
$RentabilidadTotal = $Total / $Compra * 100 - 100;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript">
function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
	frmData.submit();
}

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = 0;
	frmData.submit();
}

function ClearCampos()
{
	var frmData = Get('frmData');

	frmData.FilterIdUbicacion.value = '';	
	
	return true;
}

function ClearFilter()
{
	window.location.href='stockmovimientos.php?IdArticulo=<?= $IdArticulo ?>';
}								

function ShowFilter()
{
	HideSection('ShownFilter');
	ShowSection('HiddenFilter');
	ShowSection('FilterMain');
}

function HideFilter()
{
	ShowSection('ShownFilter');
	HideSection('HiddenFilter');
	HideSection('FilterMain');
}

$j(document).ready(function() {
	$j('.cliente-link').click(function() {
		var href = $j(this).attr('href');
		$j('body').addClass("loading"); 
		$j.ajax(href,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
					
				$j('#modal-popup').dialog({
					closeOnEscape: true,
					title: 'Detalle Orden Trabajo',
					width: 700,
					height: 550,
					modal: true
				});
			}
		});	
		return false;
	});
});

</script>


<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Reporte de Ventas de Repuestos</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr><?php /*
	<tr>
  		<td height="30" valign="top">
			<table border="0" align="right" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
					<td><a href="stockmovimientos_exportar.php<?=$strParams?>">Exportar XLS</a></td>
				</tr>
			</table>
		</td>
  	</tr>*/ ?>
  	<tr>
    	<td height="30" valign="top">
			<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
				<input type="hidden" name="MainAction" id="MainAction" />
				<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?= $IdArticulo ?>" />
				<input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
				<input type="hidden" name="FilterTipoOperacion" id="FilterTipoOperacion" value="<?= $filter['TipoOperacion'] ?>" />
				
				
                <div align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1"><div align="center"></div></td>
                    </tr>
                  </table>
                </div>
				<div id="FilterMain"class="">
				<div id="Filter" >
					<table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td><span class="tituloPagina">Filtro</span></td>
						</tr>
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						  <td class="tituloMenu"><table border="0" align="left" cellpadding="0" cellspacing="0">
							<tr>
								<td class="tituloMenu">Fecha Desde:</td>
								<td width="270">
									<input type="text" name="FilterFechaDesde" id="FilterFechaDesde" class="camporFormularioSuggest" value="<?= $filter['FechaDesde'] ?>" />										
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
									</script>
								</td>
								<td class="tituloMenu">Fecha Hasta:</td>
								<td width="270">
									<input type="text" name="FilterFechaHasta" id="FilterFechaHasta" class="camporFormularioSuggest" value="<?= $filter['FechaHasta'] ?>" />
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
									</script>
								</td>
								<td class="tituloMenu">Forma Pago:</td>
								<td width="270">
								
									<select name="FilterIdTipoPago" id="FilterIdTipoPago" class="camporFormularioSuggest">
										<option value="" >Indistinto</option>
											<?php foreach (TipoPago::GetAllPV() as $oTipoPago) { ?>
												<option value="<?=$oTipoPago['IdTipoPago']?>" <?php echo ($oTipoPago['IdTipoPago'] == $filter['IdTipoPago']) ? "selected='selected'" : "" ?> >
													<?=$oTipoPago['Descripcion']?>
												</option>
											<?php } ?>
									</select>
								</td>								
                            </tr>
							<tr>
								<td class="tituloMenu">Ubicaci&oacute;n:</td>
								<td width="270">
									<select name="FilterIdUbicacion" id="FilterIdUbicacion" class="camporFormularioSuggest">
										<option value="" >Indistinto</option>
										<?php if ($arrUbicaciones){ ?>
											<?php foreach ($arrUbicaciones as $oUbicacion) { ?>
												<option value="<?=$oUbicacion->IdUbicacion?>" <?php echo ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) ? "selected='selected'" : "" ?> >
													<?=$oUbicacion->Nombre;?>
												</option>
											<?php } ?>
										<?php } ?>
									</select>
								</td>
								<td class="tituloMenu">C&oacute;digo:</td>
								<td width="270">
									<input type="text" name="FilterCodigo" id="FilterCodigo" class="camporFormularioSuggest" value="<?= $filter['Codigo'] ?>" />
									
								</td>	
								<td>&nbsp;</td>
								<td valign="middle">
									<div align="left">
										<input type="submit" name="button" id="button" class="botonBasico" value="Buscar" />
									</div>
								</td>
                            </tr>
                          </table></td>
					  </tr>
					</table>
				</div>
				</div>
        	</form>
      	</td>
  	</tr>	
	<tr>
  		<td>&nbsp;</td>
  	</tr>
  	
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td colspan="2" width="20%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ventas</strong></div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
					?>
					<td width="12%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Reparaci&oacute;n</strong></div></td>
					<?php
					}
					?>
					<td width="20%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Costo / Facturado (S/I)</strong></div></td>
				</tr>
				<?php
				$CantidadTotal = $oReporteTotalFacturado->CantidadTotal;
				$TotalFacturado = $oReporteTotalFacturado->CostoTotal;
				$TotalCompra = $oReporteTotalFacturado->CostoCompraTotal;
				?>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td colspan="2" height="25"><div id="margen">MOSTRADOR</div></td>
					<td height="25"><div id="margen">(0 Unidades)<br>$0,00 / $0,00</div></td>
					<td height="25"><div id="margen">(0 Unidades)<br>$0,00 / $0,00</div></td>
					<td height="25"><div id="margen">(0 Unidades)<br>$0,00 / $0,00</div></td>
					<td height="25"><div id="margen">(0 Unidades)<br>$0,00 / $0,00</div></td>
					<td height="25"><div id="margen">(0 Unidades)<br>$0,00 / $0,00</div></td>
					<td height="25"><div id="margen">(<?= number_format($oReporteTotalFacturado->CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalFacturado->CostoCompraTotal, 2, ',', '.') ?> / $<?= number_format($oReporteTotalFacturado->CostoTotal, 2, ',', '.') ?></div></td>
				</tr>
	  			<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td rowspan="8" width="5%" bgColor='#f3f3f3' valign="middle"><div id="margen">OT</div></td>
					<td width="15%" height="25"><div id="margen">CLIENTE</div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
						$VarCosto = 'CostoTotal' . $oCategoria['NombreColumna'];
						$VarCostoCompra = 'CostoCompra' . $oCategoria['NombreColumna'];
						$VarCantidad = 'Cantidad' . $oCategoria['NombreColumna'];
						
						$VarCantidadTotal = 'CantidadTotal' . $oCategoria['NombreColumna'];
						$VarTotalFacturado = 'Total' . $oCategoria['NombreColumna'] . 'Facturado';
						$VarCompraTotal = 'TotalCompra' . $oCategoria['NombreColumna'];
						$$VarTotalFacturado+= $oReporteTotalClienteFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalClienteFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalClienteFacturado->$VarCantidad;
						
						$VarCantidadTotal = 'CantidadTotal';
						$VarTotalFacturado = 'TotalFacturado';
						$VarCompraTotal = 'TotalCompra';
						$$VarTotalFacturado+= $oReporteTotalClienteFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalClienteFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalClienteFacturado->$VarCantidad;
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalClienteFacturado->$VarCantidad, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalClienteFacturado->$VarCostoCompra, 2, ',', '.') ?> / $<?= number_format($oReporteTotalClienteFacturado->$VarCosto, 2, ',', '.') ?></div></td>
					<?php
					}
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalClienteFacturado->CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalClienteFacturado->CostoCompraTotal, 2, ',', '.') ?> / $<?= number_format($oReporteTotalClienteFacturado->CostoTotal, 2, ',', '.') ?></div></td>
	  			</tr>
				<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen">GARANTIA</div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
						$VarCosto = 'CostoTotal' . $oCategoria['NombreColumna'];
						$VarCostoCompra = 'CostoCompra' . $oCategoria['NombreColumna'];
						$VarCantidad = 'Cantidad' . $oCategoria['NombreColumna'];
						
						$VarCantidadTotal = 'CantidadTotal' . $oCategoria['NombreColumna'];
						$VarTotalFacturado = 'Total' . $oCategoria['NombreColumna'] . 'Facturado';
						$VarCompraTotal = 'TotalCompra' . $oCategoria['NombreColumna'];
						$$VarTotalFacturado+= $oReporteTotalGarantiaFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalGarantiaFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalGarantiaFacturado->$VarCantidad;
						
						$VarCantidadTotal = 'CantidadTotal';
						$VarTotalFacturado = 'TotalFacturado';
						$VarCompraTotal = 'TotalCompra';
						$$VarTotalFacturado+= $oReporteTotalGarantiaFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalGarantiaFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalGarantiaFacturado->$VarCantidad;
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalGarantiaFacturado->$VarCantidad, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalGarantiaFacturado->$VarCostoCompra, 2, ',', '.') ?> / $<?= number_format($oReporteTotalGarantiaFacturado->$VarCosto, 2, ',', '.') ?></div></td>
					<?php
					}
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalGarantiaFacturado->CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalGarantiaFacturado->CostoCompraTotal, 2, ',', '.') ?> / $<?= number_format($oReporteTotalGarantiaFacturado->CostoTotal, 2, ',', '.') ?></div></td>
	  			</tr>
				<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen">VENTA INTERNA</div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
						$VarCosto = 'CostoTotal' . $oCategoria['NombreColumna'];
						$VarCostoCompra = 'CostoCompra' . $oCategoria['NombreColumna'];
						$VarCantidad = 'Cantidad' . $oCategoria['NombreColumna'];
						
						$VarCantidadTotal = 'CantidadTotal' . $oCategoria['NombreColumna'];
						$VarTotalFacturado = 'Total' . $oCategoria['NombreColumna'] . 'Facturado';
						$VarCompraTotal = 'TotalCompra' . $oCategoria['NombreColumna'];
						$$VarTotalFacturado+= $oReporteTotalVentaInternaFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalVentaInternaFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalVentaInternaFacturado->$VarCantidad;
						
						$VarCantidadTotal = 'CantidadTotal';
						$VarTotalFacturado = 'TotalFacturado';
						$VarCompraTotal = 'TotalCompra';
						$$VarTotalFacturado+= $oReporteTotalVentaInternaFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalVentaInternaFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalVentaInternaFacturado->$VarCantidad;
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalVentaInternaFacturado->$VarCantidad, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalVentaInternaFacturado->$VarCostoCompra, 2, ',', '.') ?> / $<?= number_format($oReporteTotalVentaInternaFacturado->$VarCosto, 2, ',', '.') ?></div></td>
					<?php
					}
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalVentaInternaFacturado->CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalVentaInternaFacturado->CostoCompraTotal, 2, ',', '.') ?> / $<?= number_format($oReporteTotalVentaInternaFacturado->CostoTotal, 2, ',', '.') ?></div></td>
	  			</tr>
				<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen">DA&Ntilde;OS Y FALTANTES</div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
						$VarCosto = 'CostoTotal' . $oCategoria['NombreColumna'];
						$VarCostoCompra = 'CostoCompra' . $oCategoria['NombreColumna'];
						$VarCantidad = 'Cantidad' . $oCategoria['NombreColumna'];
						
						$VarCantidadTotal = 'CantidadTotal' . $oCategoria['NombreColumna'];
						$VarTotalFacturado = 'Total' . $oCategoria['NombreColumna'] . 'Facturado';
						$VarCompraTotal = 'TotalCompra' . $oCategoria['NombreColumna'];
						$$VarTotalFacturado+= $oReporteTotalDaniosFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalDaniosFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalDaniosFacturado->$VarCantidad;
						
						$VarCantidadTotal = 'CantidadTotal';
						$VarTotalFacturado = 'TotalFacturado';
						$VarCompraTotal = 'TotalCompra';
						$$VarTotalFacturado+= $oReporteTotalDaniosFacturado->$VarCosto;
						$$VarCompraTotal+= $oReporteTotalDaniosFacturado->$VarCostoCompra;
						$$VarCantidadTotal+= $oReporteTotalDaniosFacturado->$VarCantidad;
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalDaniosFacturado->$VarCantidad, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalDaniosFacturado->$VarCostoCompra, 2, ',', '.') ?> / $<?= number_format($oReporteTotalDaniosFacturado->$VarCosto, 2, ',', '.') ?></div></td>
					<?php
					}
					?>
					<td><div id="margen">(<?= number_format($oReporteTotalDaniosFacturado->CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($oReporteTotalDaniosFacturado->CostoCompraTotal, 2, ',', '.') ?> / $<?= number_format($oReporteTotalDaniosFacturado->CostoTotal, 2, ',', '.') ?></div></td>
	  			</tr>
				<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
	  			<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr bgColor='#f3f3f3'>
					<td colspan="2" height="25"><div id="margen"><strong>TOTAL</strong></div></td>
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{	
						$VarCantidadTotal = 'CantidadTotal' . $oCategoria['NombreColumna'];
						$VarTotalFacturado = 'TotalFacturado' . $oCategoria['NombreColumna'];
						$VarCompraTotal = 'TotalCompra' . $oCategoria['NombreColumna'];
					?>
					<td height="25"><div id="margen"><strong>(<?= number_format($$VarCantidadTotal, 0) ?> Unidades)<br>$<?= number_format($$VarCompraTotal, 2, ',', '.') ?> / $<?= number_format($$VarTotalFacturado, 2, ',', '.') ?></strong></div></td>					
					<?php
					}
					?>
					<td height="25"><div id="margen"><strong>(<?= number_format($CantidadTotal, 0) ?> Unidades)<br>$<?= number_format($TotalCompra, 2, ',', '.') ?> / $<?= number_format($TotalFacturado, 2, ',', '.') ?></strong></div></td>					
				</tr>
				<tr>
        			<td colspan="6"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr><?php /*
				<tr bgColor='#f3f3f3'>
					<td width="250" height="25"><div id="margen">RENTABILIDAD</div></td>
					<td height="25"><div id="margen">&nbsp;</div></td>
					<td height="25"><div id="margen"><?= number_format($RentabilidadFord, 2, ',', '.') ?>%</div></td>
					<td height="25"><div id="margen">&nbsp;</div></td>
					<td height="25"><div id="margen"><?= number_format($RentabilidadTerceros, 2, ',', '.') ?>%</div></td>
					<td height="25"><div id="margen"><?= number_format($RentabilidadTotal, 2, ',', '.') ?>%</div></td>
					<?php /*<td height="25"><div id="margen" align="center"><?= $oReporteTotal->CantidadCompras ?></div></td>				
				</tr>*/ ?>	
			</table>
	  </td>
  	</tr>
  	<tr>
    	<td>
			&nbsp;
		</td>
  	</tr>
	
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</table>
</body>
</html>