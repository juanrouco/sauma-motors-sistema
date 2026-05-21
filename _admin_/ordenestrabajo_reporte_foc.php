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

$filter['NotOrdenTrabajo'] = true;
$filter['TipoOperacion'] = TipoVenta::Mostrador;
//$oReporteTotal 	= $StockMovimientos->GetTotalReporte($filter);
$oReporteTotalFacturado 	= $StockMovimientos->GetTotalReporteFacturado($filter);

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
        			<td height="40"><span class="tituloPagina">Reporte de FOC</span></td>
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
  		<td><span class="tituloPagina">Taller</span></td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Hs. M/O</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Valor M/O</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Fact. M/O (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Fact. Rep (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Costo Rep. (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Fact. Terc. (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Costo Terc. (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Total (S/I)</strong></div></td>
				</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen" align="center"><?= number_format($HorasTotalesFacturadas, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oCostoManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalManoObra, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->CostoRepuestos + $oReporteTotalGFacturado->TotalRepuestos, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->TotalTerceros + $oReporteTotalGFacturado->TotalTerceros, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->CostoTerceros + $oReporteTotalGFacturado->CostoTerceros, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalORFacturado->TotalManoObra + $oReporteTotalORFacturado->TotalRepuestos + $oReporteTotalORFacturado->TotalTerceros + $oReporteTotalGFacturado->TotalManoObra + $oReporteTotalGFacturado->TotalRepuestos + $oReporteTotalGFacturado->TotalTerceros, 2, ',', '.') ?></div></td>
				</tr>
			</table>
	  </td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
  	<tr>
  		<td><span class="tituloPagina">Repuestos</span></td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="50%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Fact. Most. (S/I)</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>$ Costo Most. (S/I)</strong></div></td>
				</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalFacturado->CostoTotal, 2, ',', '.') ?></div></td>
					<td height="25"><div id="margen" align="center">$<?= number_format($oReporteTotalFacturado->CostoCompraTotal, 2, ',', '.') ?></div></td>
				</tr>
			</table>
	  </td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
  	<tr>
  		<td><span class="tituloPagina">Peraciones de Servicio</span></td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
					?>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cant. OT <?= $oCategoria['Nombre'] ?></strong></div></td>
					<?php
					}
					?>
				</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<?php
					foreach (Categorias::GetAll() as $oCategoria)
					{
						$filter['IdCategoria'] = $oCategoria['IdCategoria'];
						$oReporteTotalORFacturado	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::OrdenReparacion, $filter['FechaDesde'], $filter['FechaHasta'], $filter['IdCategoria']);
						
						$oReporteTotalGFacturado 	= $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::Garantia, $filter['FechaDesde'], $filter['FechaHasta'], $filter['IdCategoria']);
						
						$Cantidad = $oReporteTotalORFacturado->CantidadOT + $oReporteTotalGFacturado->CantidadOT;;
					?>
					<td height="25"><div id="margen" align="center"><?= $Cantidad ?></div></td>
					<?php
					}
					?>
				</tr>
			</table>
	  </td>
  	</tr>
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</table>
</body>
</html>