<?php 

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
$oUsuarios			= new Usuarios();
$oCostosManoObra	= new CostosManoObra();

$arrUbicaciones 	= $oUbicaciones->GetAll();

//$HorasTotales	= $oUsuarioJornadas->GetHorasEntreFechas($filter['FechaDesde'], $filter['FechaHasta']);

//$HorasAsignadas	= $oOrdenesTrabajo->GetHorasEntreFechas($filter['FechaDesde'], $filter['FechaHasta']);

$arrHorasAsignadas = $oOrdenesTrabajo->GetReporteHoras($filter['FechaDesde'], $filter['FechaHasta']);

$oCostoManoObra = $oCostosManoObra->GetAll();
$oCostoManoObra = $oCostoManoObra / 1.21;

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
	window.location.href='ordenestrabajo_horas_reporte.php<?= $strParams ?>';
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
        			<td height="40"><span class="tituloPagina">Reporte de Horas de Ordenes de Trabajo</span></td>
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
				<div id="Filter">
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
								<td>&nbsp;</td>
								<td class="tituloMenu">Fecha Hasta:</td>
								<td width="270">
									<input type="text" name="FilterFechaHasta" id="FilterFechaHasta" class="camporFormularioSuggest" value="<?= $filter['FechaHasta'] ?>" />
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
									</script>
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
        	</form>
      	</td>
  	</tr>	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table width="100%"  border="0" cellpadding="0" cellspacing="0">
							<tr class="bordeGrisFondo">
								<td width="30%" rowspan="2" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Mec&aacute;nico</strong></div></td>
								<td width="14%" colspan="2" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cliente</strong></div></td>
								<td width="14%" colspan="2"  height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Garant&iacute;a</strong></div></td>
								<td width="14%" colspan="2"  height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Venta Interna</strong></div></td>
								<td width="14%" colspan="2"  height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Da&ntilde;os Y Faltantes</strong></div></td>
								<td width="14%" colspan="2"  height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Horas</strong></div></td>
							</tr>
							<tr class="bordeGrisFondo">
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Real</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Real</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Real</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Real</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Real</strong></div></td>
								<td width="7%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
							</tr>
							<?php
							$TotalOR = 0;
							$TotalGarantia = 0;
							$TotalVentaInterna = 0;
							$TotalDaniosYFaltantes = 0;
							$Total = 0;
							$TotalORFacturadas = 0;
							$TotalGarantiaFacturadas = 0;
							$TotalVentaInternaFacturadas = 0;
							$TotalDaniosYFaltantesFacturadas = 0;
							$TotalFacturadoFinal = 0;
							
							foreach ($arrHorasAsignadas as $oHoraAsignada)
							{
								$TotalFacturadas = 0;
								$oUsuario = $oUsuarios->GetById($oHoraAsignada->IdUsuario);
								$TotalOR+= $oHoraAsignada->HorasOR;
								$TotalGarantia+= $oHoraAsignada->HorasGarantia;
								$TotalVentaInterna+= $oHoraAsignada->HorasVentaInterna;
								$TotalDaniosYFaltantes+= $oHoraAsignada->HorasDaniosYFaltantes;
								$Total+= $oHoraAsignada->Horas;
								
								$oReporteFacturadoOrdenReparacion = $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::OrdenReparacion, $filter['FechaDesde'], $filter['FechaHasta'], null, $oUsuario->IdUsuario);
								$oReporteFacturadoGarantia = $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::Garantia, $filter['FechaDesde'], $filter['FechaHasta'], null, $oUsuario->IdUsuario);
								$oReporteVentaInterna = $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::VentaInterna, $filter['FechaDesde'], $filter['FechaHasta'], null, $oUsuario->IdUsuario);
								$oReporteDaniosYFaltantes = $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::DaniosYFaltantes, $filter['FechaDesde'], $filter['FechaHasta'], null, $oUsuario->IdUsuario);
								$TotalFacturadas+= ($oReporteFacturadoOrdenReparacion->TotalManoObra + $oReporteFacturadoGarantia->TotalManoObra + $oReporteVentaInterna->TotalManoObra + $oReporteDaniosYFaltantes->TotalManoObra ) / $oCostoManoObra;
								
								$TotalORFacturadas+= $oReporteFacturadoOrdenReparacion->TotalManoObra / $oCostoManoObra;
								$TotalGarantiaFacturadas+= $oReporteFacturadoGarantia->TotalManoObra / $oCostoManoObra;
								$TotalVentaInternaFacturadas+= $oReporteVentaInterna->TotalManoObra / $oCostoManoObra;
								$TotalDaniosYFaltantesFacturadas+= $oReporteDaniosYFaltantes->TotalManoObra / $oCostoManoObra;
								
								$TotalFacturadoFinal+= $TotalFacturadas;
							?>
							<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
								<td height="25"><div id="margen"><a target="_blank" href="ordenestrabajo_horas_reporte_detalle.php<?= $strParams ?>&IdUsuario=<?= $oUsuario->IdUsuario ?>"><?= $oUsuario->IdUsuario ?> - <?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?> </a></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oHoraAsignada->HorasOR, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oReporteFacturadoOrdenReparacion->TotalManoObra / $oCostoManoObra, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oHoraAsignada->HorasGarantia, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oReporteFacturadoGarantia->TotalManoObra / $oCostoManoObra, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oHoraAsignada->HorasVentaInterna, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oReporteVentaInterna->TotalManoObra / $oCostoManoObra, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oHoraAsignada->HorasDaniosYFaltantes, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oReporteDaniosYFaltantes->TotalManoObra / $oCostoManoObra, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($oHoraAsignada->Horas, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalFacturadas, 2) ?></div></td>
							</tr>
							<tr>
								<td colspan="12"><div align="center">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
										</tr>
									</table>
								</div></td>
							</tr>
							<?php
							}
							?>
							<tr bgColor='#f3f3f3'>
								<td height="25"><div id="margen"><strong>Total</strong></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalOR, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalORFacturadas, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalGarantia, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalGarantiaFacturadas, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalVentaInterna, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalVentaInternaFacturadas, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalDaniosYFaltantes, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalDaniosYFaltantesFacturadas, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($Total, 2) ?></div></td>
								<td height="25"><div id="margen" align="center"><?= number_format($TotalFacturadoFinal, 2) ?></div></td>
							</tr>
						</table>					
					</td>
				</tr>
    		</table>
		</td>
	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
</table>
</body>
</html>