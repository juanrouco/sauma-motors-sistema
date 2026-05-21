<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaDesde']			= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta']			= trim($_REQUEST['FilterFechaHasta']);	
	$filter['Dominio'] 				= trim($_REQUEST['FilterDominio']);	
	$filter['IdOrdenTrabajo']		= trim($_REQUEST['FilterIdOrdenTrabajo']);
	$filter['IdCategoria']			= trim($_REQUEST['FilterIdCategoria']);
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['IdTipoVenta'] 			= TipoVenta::VentaInterna;

$arrData 				= array();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
$oModelos 				= new Modelos();
$oTallerUnidades		= new TallerUnidades();
$oUsuarios				= new Usuarios();
$oEstadosOrden			= new EstadosOrden();
$oClientes				= new Clientes();
$oArticulos				= new Articulos();
$oCompras				= new Compras();

$oPage 					= new Page($Page, $PageSize);

$Paginado				= Pageable::PrintPaginator($oPage, $oCompras->GetCountRowsReporteRepuestosAsignados($filter), true);
$arrData 				= $oCompras->GetAllReporteRepuestosAsignados($filter, $oPage);

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

IncludeSUGGEST();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

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

	frmData.Page.value = 0;
	frmData.submit();
}

function ClearFilter()
{	
	window.location.href = 'ventarepuestos_ot_cargointerno.php?MainAction=<?=$Action?>';
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

function SetNumeroVinPrefijo(IdModelo, NumeroVinPrefijo)
{
	Get('FilterNumeroVinPrefijo').value = NumeroVinPrefijo;
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
					title: 'Detalle del Cliente',
					width: 700,
					height: 350,
					modal: true
				});
			}
		});	
		return false;
	});
});

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Repuestos asignados en cargo interno</span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>
									<td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" title="Exportar" border="0"></div></td>
                                    <td><a href="ventasrepuestos_ot_cargointerno_exportar.php<?=$strParams?>">Exportar</a></td>
									<td width="10" height="30" >&nbsp;</td>
								</tr>
                            </table>
                        </td>                        
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <!-- Aca van los filtros -->				
                <div id="ShownFilter" class="bordeGrisFondo" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div id="HiddenFilter" style="<?=$filterStyle;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;" class="bordeGrisFondo" >
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
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>
										<td class="tituloMenu"><div align="right">Nro. OT:</div></td>
                                        <td>
                                        	<input name="FilterIdOrdenTrabajo" id="FilterIdOrdenTrabajo" type="text" class="camporFormularioSimple" value="<?=$filter['IdOrdenTrabajo']?>" />
                                       	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                        <td>
                                        	<input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaDesde']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
										<td>
											<input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaHasta']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                            </script>
										</td>
                                        
                                    </tr>
									<tr>
										<td class="tituloMenu"><div align="right">Sector:</div></td>
                                        <td>
                                        	<select name="FilterIdCategoria" id="FilterIdCategoria" class="camporFormularioSimple">
												<option value="">[INDISTINTO]</option>
												<?php
												foreach (Categorias::GetAll() as $oCategoria)
												{
													$selected = '';
													if ($oCategoria['IdCategoria'] == $filter['IdCategoria'])
														$selected = 'selected="selected"';
												?>
												<option value="<?= $oCategoria['IdCategoria'] ?>" <?= $selected ?>><?= $oCategoria['Nombre'] ?></option>
												<?php
												}
												?>
											</select>
                                       	</td>
										<td colspan="7" align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
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
            <td>&nbsp;</td>
        </tr>
          
    <?php if ($arrData != NULL) { ?>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><div align="right"><?php print ($Paginado) ?></div></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuesto</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Precio</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. OT</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sector</strong></div></td>               
                    </tr>
          
                <?php foreach ($arrData as $oCompraDetalle) 
					{
						$oCompra 			= $oCompras->GetById($oCompraDetalle->IdCompra);
						$oOrdenTrabajo		= $oOrdenesTrabajo->GetById($oCompra->IdOrdenTrabajo);
						$oOrdenTrabajoTarea	= $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
						$oTallerUnidad 		= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						$oEstadoOrden 		= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
						$oCliente 			= $oClientes->GetById($oTallerUnidad->IdCliente);
						$oArticulo 			= $oArticulos->GetById($oCompraDetalle->IdArticulo);
						$oCategoria 		= Categorias::GetById($oOrdenTrabajoTarea->IdCategoria);
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="75" height="25"><div id="margen"><?=CambiarFecha($oOrdenTrabajo->FechaFin)?></div></td>
						<td width="60" height="25"><div id="margen"><?=$oArticulo->Codigo?> - <?= $oArticulo->Descripcion ?></div></td>
						<td width="60" height="25"><div id="margen" align="center"><?=$oCompraDetalle->Cantidad?></div></td>
						<td width="60" height="25"><div id="margen" align="center">$<?=$oCompraDetalle->ImporteCompraNeto?></div></td>
						<td width="60" height="25">
							<div id="margen" align="center">
								<a href="ordenestrabajo_detail.php?IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" target="_blank"><?= $oOrdenTrabajo->IdOrdenTrabajo ?></a>
							</div></td>
                        <td width="60" height="25"><div id="margen"><?=$oTallerUnidad->Dominio?></div></td>
						<td width="170" height="25"><div id="margen"><?=$oTallerUnidad->Modelo?></div></td>
						<td width="170" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <?php /*<td width="150" height="25"><div id="margen"><?=$Usuario?></div></td>*/ ?>
						<td width="75" height="25"><div id="margen"><?=$oCategoria['Nombre']?></div></td>
                    </tr>
                    <tr>
                        <td colspan="10">
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
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><div align="right"><?php print ($Paginado) ?></div></td>
                    </tr>
                </table>
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
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>