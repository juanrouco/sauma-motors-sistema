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
$filter['IdArticulo']		= $_REQUEST['IdArticulo'];

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
$oReporteTotal 	= $StockMovimientos->GetTotalReporte($filter);

$filter['NotOrdenTrabajo'] = null;
$filter['OrdenTrabajo'] = true;
$filter['TipoOperacion'] = TipoVenta::OrdenReparacion;
$oReporteTotalCliente 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::Garantia;
$oReporteTotalGarantia 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::VentaInterna;
$oReporteTotalVentaInterna 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::PreEntrega;
$oReporteTotalPreEntrega 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::ChapaYPintura;
$oReporteTotalChapaYPintura 	= $StockMovimientos->GetTotalReporte($filter);

$filter['TipoOperacion'] = TipoVenta::Accesorios;
$oReporteTotalAccesorios 	= $StockMovimientos->GetTotalReporte($filter);

$oTipoVenta	= TipoVenta::GetById($filter['TipoOperacion']);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 					. $Page;
$strParams.= '&FilterFechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&FilterIdUbicacion=' 		. $filter['IdUbicacion'];
$strParams.= '&FilterTipoOperacion=' 	. $filter['TipoOperacion'];
$strParams.= '&IdArticulo=' 			. $filter['IdArticulo'];


$TotalFord = $oReporteTotal->CostoTotalFord + $oReporteTotalPreEntrega->CostoTotalFord + $oReporteTotalVentaInterna->CostoTotalFord + $oReporteTotalGarantia->CostoTotalFord + $oReporteTotalCliente->CostoTotalFord + $oReporteTotalAccesorios->CostoTotalFord + $oReporteTotalChapaYPintura->CostoTotalFord;
$CompraFord = $oReporteTotal->CostoCompraFord + $oReporteTotalPreEntrega->CostoCompraFord + $oReporteTotalVentaInterna->CostoCompraFord + $oReporteTotalGarantia->CostoCompraFord + $oReporteTotalCliente->CostoCompraFord + $oReporteTotalChapaYPintura->CostoCompraFord + $oReporteTotalAccesorios->CostoCompraFord;
$TotalTerceros = $oReporteTotal->CostoTotalTerceros + $oReporteTotalPreEntrega->CostoTotalTerceros + $oReporteTotalVentaInterna->CostoTotalTerceros + $oReporteTotalGarantia->CostoTotalTerceros + $oReporteTotalCliente->CostoTotalTerceros + $oReporteTotalChapaYPintura->CostoTotalTerceros + $oReporteTotalAccesorios->CostoTotalTerceros;
$CompraTerceros = $oReporteTotal->CostoCompraTerceros + $oReporteTotalPreEntrega->CostoCompraTerceros + $oReporteTotalVentaInterna->CostoCompraTerceros + $oReporteTotalGarantia->CostoCompraTerceros + $oReporteTotalCliente->CostoCompraTerceros + $oReporteTotalChapaYPintura->CostoCompraTerceros + $oReporteTotalAccesorios->CostoCompraTerceros;
$Total = $oReporteTotal->CostoTotal + $oReporteTotalPreEntrega->CostoTotal + $oReporteTotalVentaInterna->CostoTotal + $oReporteTotalGarantia->CostoTotal + $oReporteTotalCliente->CostoTotal + $oReporteTotalChapaYPintura->CostoTotal + $oReporteTotalAccesorios->CostoTotal;
$Compra = $CompraFord + $CompraTerceros;

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
        			<td height="40"><span class="tituloPagina">Reporte de Movimientos de Stock</span></td>
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
								<td class="tituloMenu">&nbsp;</td>
								<td width="270">&nbsp;</td>
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
			<table width="750" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="75%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ventas</strong></div></td>
					<?php h/*<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Ford (S/I)</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Compra Ford</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Terceros (S/I)</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Compra Terceros</strong></div></td>*/ ?>
					<td width="25%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Total (S/I)</strong></div></td>
					<?php /*<td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad Ventas</strong></div></td>*/ ?>
				</tr>
				
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="75%" height="25"><div id="margen">MOSTRADOR</div></td>
					<?php /*<td height="25"><div id="margen">$<?= number_format($oReporteTotal->CostoTotalFord, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotal->CostoCompraFord, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotal->CostoTotalTerceros, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotal->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotal->CostoTotal, 2, ',', '.') ?></div></td>
					<?php /*<td height="25"><div id="margen" align="center"><?= $oReporteTotal->CantidadCompras ?></div></td>*/ ?>					
				</tr>
	  			<tr>
        			<td colspan="2"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr>
					<td colspan="2">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
								<td width="5%" bgColor='#f3f3f3' valign="middle"><div id="margen">OT</div></td>
								<td width="95%" colspan="5">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">CLIENTE</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalCliente->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalCliente->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalCliente->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalCliente->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalCliente->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalCliente->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">GARANTIA</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalGarantia->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalGarantia->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalGarantia->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalGarantia->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalGarantia->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalGarantia->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">VENTA INTERNA</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalVentaInterna->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalVentaInterna->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalVentaInterna->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalVentaInterna->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalVentaInterna->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalVentaInterna->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">PREENTREGA</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalPreEntrega->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalPreEntrega->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalPreEntrega->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalPreEntrega->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalPreEntrega->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalPreEntrega->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">CHAPA Y PINTURA</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalChapaYPintura->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalChapaYPintura->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalChapaYPintura->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalChapaYPintura->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalChapaYPintura->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalPreEntrega->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
										<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
											<td width="74%" height="25"><div id="margen">ACCESORIOS</div></td>
											<?php /*<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalAccesorios->CostoTotalFord, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalAccesorios->CostoCompraFord, 2, ',', '.') ?></div></td>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalAccesorios->CostoTotalTerceros, 2, ',', '.') ?></div></td>
											<td height="25"><div id="margen">$<?= number_format($oReporteTotalAccesorios->CostoCompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
											<td width="100" height="25"><div id="margen">$<?= number_format($oReporteTotalAccesorios->CostoTotal, 2, ',', '.') ?></div></td>
											<?php /*<td width="100" height="25"><div id="margen" align="center"><?= $oReporteTotalPreEntrega->CantidadCompras ?></div></td>	*/ ?>
										</tr>
										<tr>
											<td colspan="2"><div align="center">
												<table width="100%"  border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</div></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
	  			<tr>
        			<td colspan="3"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr bgColor='#f3f3f3'>
					<td width="250" height="25"><div id="margen">TOTAL</div></td>
					<?php /*<td height="25"><div id="margen">$<?= number_format($TotalFord, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($CompraFord, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($TotalTerceros, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($CompraTerceros, 2, ',', '.') ?></div></td>*/ ?>
					<td height="25"><div id="margen">$<?= number_format($Total, 2, ',', '.') ?></div></td>
					<?php /*<td height="25"><div id="margen" align="center"><?= $oReporteTotal->CantidadCompras ?></div></td>*/ ?>					
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