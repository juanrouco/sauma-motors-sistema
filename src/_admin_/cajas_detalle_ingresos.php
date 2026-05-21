<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(Modulo::Cajas))
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
	$filter['IdTipoMovimiento']	 = $_REQUEST['FilterIdTipoMovimiento'];
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['IdCajaDetalle'] = $IdCajaDetalle;
$filter['Ingresos'] = '1';

$Paginado	= Pageable::PrintPaginator($oPage, $oCajasMovimientos->GetCountRows($filter), true);
$arrData 	= $oCajasMovimientos->GetAll($filter, $oPage);

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
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	frmData.FilterUsuario.value 	= '';

	frmData.Page.value = 0;

	frmData.submit();	
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
                        <td height="40"><span class="tituloPagina">Caja <?= $oCajaDetalle->Nombre ?> - Detalle Ingresos</span></td>
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
                            			</tr>
                            			<tr>
											<td class="tituloMenu">Tipo Movimiento:</td>
                                  			<td>
                                            	<select name="FilterIdTipoMovimiento" id="FilterIdTipoMovimiento" class="camporFormularioSimple">
                                                	<option value="">[Indistinto]</option>
                                                    <?php foreach (TiposMovimientosCaja::GetAll() as $oTipoMovimiento) { ?>
                                                    <option value="<?= $oTipoMovimiento['IdTipoMovimiento'] ?>" <?=($filter['IdTipoMovimiento'] == $oTipoMovimiento['IdTipoMovimiento']) ? 'selected="selected"' : ''?> ><?=$oTipoMovimiento['Descripcion']?></option>
                                                    <?php } ?>
                                                </select>
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
      
    <?php if ($arrData != NULL) { ?>
        
        <tr>
            <td>
                <div align="right"><? print ($Paginado) ?></div>
                <br>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="25%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td width="25%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Movimiento</strong></div></td>
                        <td width="25%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Comentarios</strong></div></td>
                        <td width="25%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td width="25%" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
				foreach ($arrData as $oCajaMovimiento)
				{
					$Nombre = TiposMovimientosCaja::GetByCliente($oCajaMovimiento->IdTipoMovimiento, $oCajaMovimiento->IdEntidad);
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFechaHora($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen"><?= $Nombre ? $Nombre . ' - ' : '' ?><?=TiposMovimientosCaja::GetById($oCajaMovimiento->IdTipoMovimiento)?></div></td>
                        <td height="25"><div id="margen"><?= $oCajaMovimiento->Comentarios ?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$oCajaMovimiento->Total?></div></td>
                        <td width="77" height="25">
							<div align="center">
								<a target="_blank" href="<?=TiposMovimientosCaja::GetByUrl($oCajaMovimiento->IdTipoMovimiento, $oCajaMovimiento->IdEntidad)?>"><img src="images/iconos/preview.gif" alt="Ver Detalle" title="Ver Detalle" border="0" /></a>
							</div>
						</td>
                    </tr>
                    <tr>
                        <td colspan="7">
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