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

$Iva = 1.21;

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
	window.location.href='ordenestrabajo_facturacion_reporte.php<?= $strParams ?>';
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
        			<td height="40"><span class="tituloPagina">Reporte de Facturaci&oacute;n de Ordenes de Trabajo - Mec&aacute;nicos</span></td>
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
								<td width="30%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Mec&aacute;nico</strong></div></td>
								<td width="14%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad OT</strong></div></td>
								<td width="14%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact. MO</strong></div></td>
								<td width="14%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact. Rep.</strong></div></td>
								<td width="14%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
							</tr>
							<?php
							$TotalMO = 0;
							$TotalRep = 0;
							$CantidadOT = 0;
							$TotalORFacturadas = 0;
							
							foreach ($arrHorasAsignadas as $oHoraAsignada)
							{
								$TotalFacturadas = 0;
								$oUsuario = $oUsuarios->GetById($oHoraAsignada->IdUsuario);
								
								$oReporteFacturadoOrdenReparacion = $oOrdenesTrabajo->GetReporteOTFacturadas(TipoVenta::OrdenReparacion, $filter['FechaDesde'], $filter['FechaHasta'], Categorias::Taller, $oUsuario->IdUsuario);
								
								$TotalMO+= $oReporteFacturadoOrdenReparacion->TotalManoObra;
								$TotalRep+= $oReporteFacturadoOrdenReparacion->TotalRepuestos;
								$CantidadOT+= $oReporteFacturadoOrdenReparacion->CantidadOT;
							?>
							<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
								<td height="25"><div id="margen"><?= $oUsuario->IdUsuario ?> - <?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?></div></td>
								<td height="25"><div id="margen" align="center"><?= $oReporteFacturadoOrdenReparacion->CantidadOT ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($oReporteFacturadoOrdenReparacion->TotalManoObra, 2, ',', '.') ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($oReporteFacturadoOrdenReparacion->TotalRepuestos, 2, ',', '.') ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($oReporteFacturadoOrdenReparacion->TotalManoObra + $oReporteFacturadoOrdenReparacion->TotalRepuestos, 2, ',', '.') ?></div></td>
							</tr>
							<tr>
								<td colspan="5"><div align="center">
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
								<td height="25"><div id="margen" align="center"><?= $CantidadOT ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($TotalMO, 2, ',', '.') ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($TotalRep, 2, ',', '.') ?></div></td>
								<td height="25"><div id="margen" align="center">$<?= number_format($TotalMO + $TotalRep, 2, ',', '.') ?></div></td>
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