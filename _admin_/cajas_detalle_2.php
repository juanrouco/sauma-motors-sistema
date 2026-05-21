<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$filter			= ReceiveArray($_REQUEST['filter']);
$Page 			= intval($_REQUEST['Page']);
$PageSize 		= intval($_REQUEST['PageSize']);
$IdCajaDetalle	= intval($_REQUEST['IdCajaDetalle']);

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$arrData 			= array();
$oCajasMovimientos	= new CajasMovimientos();
$oCajasDetalles		= new CajasDetalles();
$oPage 				= new Page($Page, $PageSize);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
	$filter['FechaDesde'] 		 = $_REQUEST['FilterFechaDesde'];
	$filter['FechaHasta']		 = $_REQUEST['FilterFechaHasta'];
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['IdCajaDetalle'] = $IdCajaDetalle;

$Paginado	= Pageable::PrintPaginator($oPage, $oCajasMovimientos->GetCountRowsFechasMovimiento($filter), true);
$arrData 	= $oCajasMovimientos->GetAllFechasMovimiento($filter, $oPage);

$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>

<script language="javascript">

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
	frmData.submit();
}

function SetPageSize(PageSize)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	if (frmData.PageSize == undefined)
		return false;

	frmData.PageSize.value = PageSize;
	frmData.submit();
}

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.MainActino.value = '';
	frmData.Page.value = 0;
	
	return true;
}

function ClearFilter()
{
	window.location.href = 'cajas_detalles.php?IdCajaDetalle=<?= $IdCajaDetalle ?>';
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

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$_SEVER['PHP_SELF']?>" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
	<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?= $IdCajaDetalle ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Caja <?= $oCajaDetalle->Nombre ?> - Detalle</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                      <td><a href="cajasmovimientos_add.php<?=$strParams?>&IdCajaDetalle=<?= $IdCajaDetalle ?>">Agregar Movimiento</a></td>
                    </tr>
                  </table></td>
                  <td height="40"><table border="0" align="right" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="30">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td width="20">&nbsp;</td>
                      <td width="30">&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                    <div class="bordeGrisFondo" id="ShownFilter" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                                <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                            </tr>
                        </table>
                    </div>
                    <div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-left: 10px; padding-bottom: 10px; padding-right: 10px; padding-top: 10px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>[-] <a href="#bottom" class="linkMenu" onClick="javascript: HideFilter();"> <b>Ocultar b&uacute;squeda y filtros</b></a></td>
                                <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                            </tr>
                        </table>
                    </div>
                    <div align="center">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td height="1"><div align="center"></div></td>
                        </tr>
                      </table>
                    </div>
                    <div id="FilterMain" style="<?=$filterStyle;?>" class="">
                    <div id="Filter" >
                        <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
                            <tr>
                              	<td class="tituloMenu">
                                	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                            			<tr>
                                			<td width="88" class="tituloMenu">Fecha Desde:</td>
                                			<td width="270" align="left">
                                                <input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioMediano" value="<?=$filter['FechaDesde']?>" />
                                                <script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmData',
                                                        'controlname': 'FilterFechaDesde'
                                                    });
                                                </script>
                                            </td>
                                            <td width="79" class="tituloMenu">Fecha Hasta:</td>
                                            <td width="263" align="left">
                                                <input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioMediano" value="<?=$filter['FechaHasta']?>" />
                                                <script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmData',
                                                        'controlname': 'FilterFechaHasta'
                                                    });
                                                </script>
                                            </td>
											<td>&nbsp;</td>
                                  			<td align="right"><div align="middle">
                                    			<input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
                                  			</div></td>
                                		</tr>
                              		</table>
                              	</td>
                            </tr>
                        </table>
                    </div>
                    </div>
            </td>
        </tr>	
        <tr>
            <td></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" height="30" valign="top">
            	<table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
            		<tr>
            			<td colspan="3">&nbsp;</td>
            		</tr>
            		<tr>
            			<td width="10">&nbsp;</td>
            			<td width="150"><strong>Total: $<?= $oCajaDetalle->Total ?></strong></td>
            			<td>&nbsp;</td>
            		</tr>
            		<tr>
            			<td colspan="3">&nbsp;</td>
            		</tr>
            	</table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
        </tr>
      
    <?php if ($arrData != NULL) { ?>
        
        <tr>
            <td>
                <div align="right"><? print ($Paginado) ?></div>
                <br>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="10%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td width="10%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Turno</strong></div></td>
                        <td width="40%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Concepto</strong></div></td>
                        <td width="20%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total Item</strong></div></td>
                        <td width="10%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td width="10%" height="25" class="bordeGrisTitulo"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oCajaMovimiento)
					{
						$TotalDiario = 0;
						$filterVentasManiana = array();
						$filterVentasManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterVentasManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterVentasManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterVentasManiana['Ingresos'] = 1;
						$filterVentasManiana['IdTipoMovimiento'] = TiposMovimientosCaja::Venta;
						$filterVentasManiana['IdTurno'] = 1;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterVentasManiana);
						$TotalVentasManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalVentasManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalVentasManiana;
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">MA&Ntilde;ANA</div></td>
                        <td height="25"><div id="margen">VENTAS</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalVentasManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <?php /*<td width="77" height="25">
							<div align="center">
								<a href="<?=$oCajaMovimiento->TipoMovimiento == 'INGRESOS' ? 'cajas_detalle_ingresos.php?IdCajaDetalle=' . $IdCajaDetalle . '&FilterFechaDesde=' . $oCajaMovimiento->Fecha . '&FilterFechaHasta=' . $oCajaMovimiento->Fecha : 'cajas_detalle_egresos.php?IdCajaDetalle=' . $IdCajaDetalle . '&FechaDesde=' . $oCajaMovimiento->Fecha . '&FechaHasta=' . $oCajaMovimiento->Fecha ?>"><img src="images/iconos/preview.gif" alt="Ver Detalle" title="Ver Detalle" border="0" /></a>
							</div>
						</td>*/ ?>
                    </tr>
                    <tr>
                        <td colspan="5">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterPagoProveedoresManiana = array();
						$filterPagoProveedoresManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterPagoProveedoresManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterPagoProveedoresManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterPagoProveedoresManiana['IdTipoMovimiento'] = TiposMovimientosCaja::PagoProveedores;
						$filterPagoProveedoresManiana['IdTurno'] = 1;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterPagoProveedoresManiana);
						$TotalPagoProveedoresManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalPagoProveedoresManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalPagoProveedoresManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">MA&Ntilde;ANA</div></td>
                        <td height="25"><div id="margen">PAGO PROVEEDORES</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalPagoProveedoresManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterSeniasManiana = array();
						$filterSeniasManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterSeniasManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterSeniasManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterSeniasManiana['IdTipoMovimiento'] = TiposMovimientosCaja::SeniaVencida;
						$filterSeniasManiana['IdTurno'] = 1;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresosSenias($filterSeniasManiana);
						$TotalSeniasManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalSeniasManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalSeniasManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">MA&Ntilde;ANA</div></td>
                        <td height="25"><div id="margen">SE&Ntilde;AS</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalSeniasManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterCobroCCManiana = array();
						$filterCobroCCManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterCobroCCManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterCobroCCManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterCobroCCManiana['IdTipoMovimiento'] = TiposMovimientosCaja::PagoCuentaCorriente;
						$filterCobroCCManiana['IdTurno'] = 1;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterCobroCCManiana);
						$TotalCobroCCManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalCobroCCManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalSeniasManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">MA&Ntilde;ANA</div></td>
                        <td height="25"><div id="margen">COBRO CUENTA CORRIENTE</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalCobroCCManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
					/*--------------- TARDE -----------------*/
					//$TotalDiario = 0;
						$filterVentasManiana = array();
						$filterVentasManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterVentasManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterVentasManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterVentasManiana['Ingresos'] = 1;
						$filterVentasManiana['IdTipoMovimiento'] = TiposMovimientosCaja::Venta;
						$filterVentasManiana['IdTurno'] = 2;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterVentasManiana);
						$TotalVentasManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalVentasManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalVentasManiana;
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">TARDE</div></td>
                        <td height="25"><div id="margen">VENTAS</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalVentasManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterPagoProveedoresManiana = array();
						$filterPagoProveedoresManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterPagoProveedoresManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterPagoProveedoresManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterPagoProveedoresManiana['IdTipoMovimiento'] = TiposMovimientosCaja::PagoProveedores;
						$filterPagoProveedoresManiana['IdTurno'] = 2;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterPagoProveedoresManiana);
						$TotalPagoProveedoresManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalPagoProveedoresManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalPagoProveedoresManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">TARDE</div></td>
                        <td height="25"><div id="margen">PAGO PROVEEDORES</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalPagoProveedoresManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterSeniasManiana = array();
						$filterSeniasManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterSeniasManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterSeniasManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterSeniasManiana['IdTipoMovimiento'] = TiposMovimientosCaja::SeniaVencida;
						$filterSeniasManiana['IdTurno'] = 2;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresosSenias($filterSeniasManiana);
						$TotalSeniasManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalSeniasManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalSeniasManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">TARDE</div></td>
                        <td height="25"><div id="margen">SE&Ntilde;AS</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalSeniasManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<?php
						$filterCobroCCManiana = array();
						$filterCobroCCManiana['FechaDesde'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterCobroCCManiana['FechaHasta'] = CambiarFecha($oCajaMovimiento->Fecha);
						$filterCobroCCManiana['IdCajaDetalle'] = $IdCajaDetalle;
						$filterCobroCCManiana['IdTipoMovimiento'] = TiposMovimientosCaja::PagoCuentaCorriente;
						$filterCobroCCManiana['IdTurno'] = 2;
						$arrCajasMovimientosDetalles = $oCajasMovimientos->GetAllEgresos($filterCobroCCManiana);
						$TotalCobroCCManiana = 0;
						if ($arrCajasMovimientosDetalles)
							$TotalCobroCCManiana = $arrCajasMovimientosDetalles[0]->Total;
						$TotalDiario += $TotalCobroCCManiana;
					?>
					<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">TARDE</div></td>
                        <td height="25"><div id="margen">COBRO CUENTA CORRIENTE</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $TotalCobroCCManiana ?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
					<tr bgColor='#f3f3f3'>
                        <td height="25"><div id="margen"><?=CambiarFecha($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen">-</div></td>
                        <td height="25"><div id="margen">Otros</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $oCajaMovimiento->Total - $TotalDiario ?></div></td>
                        <td height="25"><div id="margen" align="center"><strong>$ <?= $oCajaMovimiento->Total ?></strong></div></td>
                        <td height="25">
                        	<div id="margen" align="center">
                        		<a href="cajas_detalle_3.php?IdCajaDetalle=1&Fecha=<?= CambiarFecha($oCajaMovimiento->Fecha) ?>">
	                        		<img src="images/iconos/preview.gif" alt="Detalle diario" title="Detalle diario" />
                        		</a>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
                <?php } ?>      
                
                </table>
          </td>
        </tr>
        <tr>
            <td>
                <br>
                <div align="right"><? print ($Paginado) ?></div>
                <br>    
            </td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>No hay registros disponibles.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
          
    <?php } ?>
    
    </table>
</form>

</body>
</html>