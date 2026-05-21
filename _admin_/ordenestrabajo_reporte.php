<?php 

//require_once('ssi_errores.php'); 
require_once('../inc_library.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TALL_REPORTES))
	Session::NoPerm();

$filter = array();
$filter['FechaDesde'] 		= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']		= $_REQUEST['FilterFechaHasta'];
$filter['IdUbicacion']		= $_REQUEST['FilterIdUbicacion'];

$IdUbicacion 	= $_REQUEST['FilterIdUbicacion'] ;

$filterStyle = "display:none;";
$filterMostrar = "";

if (!isset($filter['FechaHasta']) || $filter['FechaHasta'] == '')
{
	$filter['FechaHasta'] = date('d-m-Y');
}

if (!isset($filter['FechaDesde']) || $filter['FechaDesde'] == '')
{
	$filter['FechaDesde'] = date('d-m-Y', strtotime("-7 days"));
}

if ($filter['FechaDesde'] != '' || $filter['FechaHasta'] != '' || $filter['IdUbicacion'] != '')
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}



$arr = array();

$oUbicaciones 		= new Ubicaciones();
$oUsuarioJornadas	= new UsuarioJornadas();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$CostosManoObra 	= new CostosManoObra();
$StockMovimientos 	= new StockMovimientos();

$arrUbicaciones 	= $oUbicaciones->GetAll();

$oCostoManoObra = $CostosManoObra->GetAll();

$oCostoManoObra = $oCostoManoObra / 1.21;

$HorasTotalesFacturadas = 0;
$oReporteTotalORFacturado	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::OrdenReparacion, $filter['FechaDesde'], $filter['FechaHasta']);
$HorasORFacturadas = $oReporteTotalORFacturado->TotalManoObra / $oCostoManoObra;
$HorasTotalesFacturadas += $HorasORFacturadas;

$oReporteTotalGFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::Garantia, $filter['FechaDesde'], $filter['FechaHasta']);
$HorasGFacturadas = $oReporteTotalGFacturado->TotalManoObra / $oCostoManoObra;
$HorasTotalesFacturadas += $HorasGFacturadas;

$oReporteTotalVIFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::VentaInterna, $filter['FechaDesde'], $filter['FechaHasta']);
$oReporteTotalVI 	= $oOrdenesTrabajo->GetReporteOT(TipoVenta::VentaInterna, $filter['FechaDesde'], $filter['FechaHasta']);
$HorasVIFacturadas = $oReporteTotalVIFacturado->TotalManoObra / $oCostoManoObra;
$HorasTotalesFacturadas += $HorasVIFacturadas;

$oReporteTotalDaniosFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::DaniosYFaltantes, $filter['FechaDesde'], $filter['FechaHasta']);
$oReporteTotalDanios 	= $oOrdenesTrabajo->GetReporteOT(TipoVenta::DaniosYFaltantes, $filter['FechaDesde'], $filter['FechaHasta']);
$HorasDaniosFacturadas = $oReporteTotalDaniosFacturado->TotalManoObra / $oCostoManoObra;
$HorasTotalesFacturadas += $HorasDaniosFacturadas;

$filterVI = array();
$filterVI['FechaDesde'] 		= $filter['FechaDesde'];
$filterVI['FechaHasta']		= $filter['FechaHasta'];
$filterVI['IdUbicacion']		= $_REQUEST['FilterIdUbicacion'];
$filterVI['OrdenTrabajo'] = true;
$filterVI['TipoOperacion'] = TipoVenta::VentaInterna;

$oReporteTotalVentaInterna 	= $StockMovimientos->GetTotalReporte($filterVI);

$filterVI['TipoOperacion'] = TipoVenta::DaniosYFaltantes;

$oReporteTotalDanios 	= $StockMovimientos->GetTotalReporte($filterVI);

$oReporteHorasVI = $oOrdenesTrabajo->GetReporteHorasExplicado($filter['FechaDesde'], $filter['FechaHasta'], TipoVenta::VentaInterna);
$oReporteHorasDanios = $oOrdenesTrabajo->GetReporteHorasExplicado($filter['FechaDesde'], $filter['FechaHasta'], TipoVenta::DaniosYFaltantes);


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 					. $Page;
$strParams.= '&FilterFechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&FilterIdUbicacion=' 		. $filter['IdUbicacion'];


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

</script>


<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Reporte de Facturaci&oacute;n de Ordenes de Trabajo</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
  		<td height="30" valign="top"></td>
  	</tr>
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
								<td width="10">&nbsp;</td>
								<td class="tituloMenu">Fecha Hasta:</td>
								<td width="270">
									<input type="text" name="FilterFechaHasta" id="FilterFechaHasta" class="camporFormularioSuggest" value="<?= $filter['FechaHasta'] ?>" />
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
									</script>
								</td>
								<td width="10">&nbsp;</td>	
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
                            </tr>
							<tr>
								<td class="tituloMenu">&nbsp;</td>
								<td width="270">&nbsp;</td>
								<td>&nbsp;</td>
								<td class="tituloMenu">&nbsp;</td>
								<td width="270">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td valign="middle">
									<div align="right">
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
  		<td><span class="tituloPagina">Total</span></td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="130" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cargo</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cantidad Ordenes</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Mano Obra (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Hs. Facturadas</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Repuestos (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Total (S/I)</strong></div></td>
					<?php /*<td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad Ventas</strong></div></td>*/ ?>
				</tr>
				
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">CLIENTE</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalORFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasORFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalORFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">GARANTIA</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalGFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasGFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalManoObra + $oReporteTotalG->TotalRepuestos, 2, ',', '.') ?></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">VENTA INTERNA</div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= $oReporteTotalVI->CantidadOT ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalVIFacturado->TotalManoObra, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= number_format($oReporteHorasVI->Horas, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalVentaInterna->CostoTotal, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalVIFacturado->TotalManoObra + $oReporteTotalVentaInterna->CostoTotal, 2, ',', '.') ?></span></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">DAN&Ntilde;OS Y FALTANTES</div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= $oReporteTotalDanios->CantidadOT ? $oReporteTotalDanios->CantidadOT : '0' ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalDaniosFacturado->TotalManoObra, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= number_format($oReporteHorasDanios->Horas, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalDanios->CostoTotal, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalDaniosFacturado->TotalManoObra + $oReporteTotalDanios->CostoTotal, 2, ',', '.') ?></span></div></td>
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
      			</tr>
	  			<tr>
        			<td colspan="6"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr bgColor='#f3f3f3'>
					<td width="250" height="25"><div id="margen">TOTAL</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalORFacturado->CantidadOT + $oReporteTotalGFacturado->CantidadOT + $oReporteTotalVIFacturado->CantidadOT + $oReporteTotalPFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalManoObra + $oReporteTotalVIFacturado->TotalManoObra + $oReporteTotalPFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasTotalesFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalRepuestos + $oReporteTotalVIFacturado->TotalRepuestos + $oReporteTotalPFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalRepuestos + $oReporteTotalVIFacturado->TotalManoObra + $oReporteTotalVIFacturado->TotalRepuestos + $oReporteTotalP->TotalManoObra + $oReporteTotalP->TotalRepuestos, 2, ',', '.') ?></div></td>
					<?php /*<td height="25"><div id="margen" align="center"><?= $oReporteTotal->CantidadCompras ?></div></td>*/ ?>					
				</tr>
			</table>
	  </td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<?php
	foreach (Categorias::GetAll() as $oCategoria)
	{
		$HorasTotalesFacturadas = 0;
		$oReporteTotalORFacturado	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::OrdenReparacion, $filter['FechaDesde'], $filter['FechaHasta'], $oCategoria['IdCategoria']);
		$HorasORFacturadas = $oReporteTotalORFacturado->TotalManoObra / $oCostoManoObra;
		$HorasTotalesFacturadas += $HorasORFacturadas;

		$oReporteTotalGFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::Garantia, $filter['FechaDesde'], $filter['FechaHasta'], $oCategoria['IdCategoria']);
		$HorasGFacturadas = $oReporteTotalGFacturado->TotalManoObra / $oCostoManoObra;
		$HorasTotalesFacturadas += $HorasGFacturadas;

		$oReporteTotalVIFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::VentaInterna, $filter['FechaDesde'], $filter['FechaHasta'], $oCategoria['IdCategoria']);
		$oReporteTotalVI 	= $oOrdenesTrabajo->GetReporteOT(TipoVenta::VentaInterna, $filter['FechaDesde'], $filter['FechaHasta'], $oCategoria['IdCategoria']);
		$HorasVIFacturadas = $oReporteTotalVIFacturado->TotalManoObra / $oCostoManoObra;
		$HorasTotalesFacturadas += $HorasVIFacturadas;

		$oReporteTotalDaniosFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::DaniosYFaltantes, $filter['FechaDesde'], $filter['FechaHasta']);
		$oReporteTotalDanios 	= $oOrdenesTrabajo->GetReporteOT(TipoVenta::DaniosYFaltantes, $filter['FechaDesde'], $filter['FechaHasta']);
		$HorasDaniosFacturadas = $oReporteTotalDaniosFacturado->TotalManoObra / $oCostoManoObra;
		$HorasTotalesFacturadas += $HorasDaniosFacturadas;
		
		$RepuestosVI = 0;
		if ($oCategoria['IdCategoria'] == Categorias::Taller)
			$RepuestosVI = $oReporteTotalVentaInterna->CostoTotalTaller;
		elseif ($oCategoria['IdCategoria'] == Categorias::ChapaYPintura)
			$RepuestosVI = $oReporteTotalVentaInterna->CostoTotalChapa;
		elseif ($oCategoria['IdCategoria'] == Categorias::PreEntrega)
			$RepuestosVI = $oReporteTotalVentaInterna->CostoTotalPreEntrega;
		elseif ($oCategoria['IdCategoria'] == Categorias::Accesorios)
			$RepuestosVI = $oReporteTotalVentaInterna->CostoTotalAccesorios;
		
		$RepuestosDanios = 0;
		if ($oCategoria['IdCategoria'] == Categorias::Taller)
			$RepuestosDanios = $oReporteTotalDanios->CostoTotalTaller;
		elseif ($oCategoria['IdCategoria'] == Categorias::ChapaYPintura)
			$RepuestosDanios = $oReporteTotalDanios->CostoTotalChapa;
		elseif ($oCategoria['IdCategoria'] == Categorias::PreEntrega)
			$RepuestosDanios = $oReporteTotalDanios->CostoTotalPreEntrega;
		elseif ($oCategoria['IdCategoria'] == Categorias::Accesorios)
			$RepuestosDanios = $oReporteTotalDanios->CostoTotalAccesorios;
		
		$oReporteHorasVI = $oOrdenesTrabajo->GetReporteHorasExplicado($filter['FechaDesde'], $filter['FechaHasta'], TipoVenta::VentaInterna, $oCategoria['IdCategoria']);
		$oReporteHorasDanios = $oOrdenesTrabajo->GetReporteHorasExplicado($filter['FechaDesde'], $filter['FechaHasta'], TipoVenta::DaniosYFaltantes, $oCategoria['IdCategoria']);

	?>
  	<tr>
  		<td><span class="tituloPagina"><?= $oCategoria['Nombre'] ?></span></td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="130" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cargo</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cantidad Ordenes</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Mano Obra (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Hs. Facturadas</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Repuestos (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Total (S/I)</strong></div></td>
					<?php /*<td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad Ventas</strong></div></td>*/ ?>
				</tr>
				
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">CLIENTE</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalORFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasORFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalORFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">GARANTIA</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalGFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasGFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalGFacturado->TotalManoObra + $oReporteTotalG->TotalRepuestos, 2, ',', '.') ?></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">VENTA INTERNA</div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= $oReporteTotalVI->CantidadOT ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalVIFacturado->TotalManoObra, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= number_format($oReporteHorasVI->Horas, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($RepuestosVI, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalVIFacturado->TotalManoObra + $RepuestosVI, 2, ',', '.') ?></span></div></td>
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
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td width="130" height="25"><div id="margen">DAN&Ntilde;OS Y FALTANTES</div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= $oReporteTotalDanios->CantidadOT ? $oReporteTotalDanios->CantidadOT : '0' ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalDaniosFacturado->TotalManoObra, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red"><?= number_format($oReporteHorasDanios->Horas, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($RepuestosDanios, 2, ',', '.') ?></span></div></td>
					<td height="25"><div id="margen"><span style="color: red">$<?= number_format($oReporteTotalDaniosFacturado->TotalManoObra + $RepuestosDanios, 2, ',', '.') ?></span></div></td>
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
      			</tr>
	  			<tr>
        			<td colspan="6"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr bgColor='#f3f3f3'>
					<td width="250" height="25"><div id="margen">TOTAL</div></td>
					<td height="25"><div id="margen"><?= $oReporteTotalORFacturado->CantidadOT + $oReporteTotalGFacturado->CantidadOT + $oReporteTotalVIFacturado->CantidadOT + $oReporteTotalPFacturado->CantidadOT ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalManoObra + $oReporteTotalVIFacturado->TotalManoObra + $oReporteTotalPFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen"><?= number_format($HorasTotalesFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalRepuestos + $oReporteTotalVIFacturado->TotalRepuestos + $oReporteTotalPFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalRepuestos + $oReporteTotalVIFacturado->TotalManoObra + $oReporteTotalVIFacturado->TotalRepuestos + $oReporteTotalP->TotalManoObra + $oReporteTotalP->TotalRepuestos, 2, ',', '.') ?></div></td>
					<?php /*<td height="25"><div id="margen" align="center"><?= $oReporteTotal->CantidadCompras ?></div></td>*/ ?>					
				</tr>
			</table>
	  </td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<?php
	}
	?>
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</table>
</body>
</html>