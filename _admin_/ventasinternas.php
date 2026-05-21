<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaCargaDesde']		= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaCargaHasta']		= trim($_REQUEST['FilterFechaHasta']);
	$filter['IdOrdenTrabajo']		= trim($_REQUEST['FilterIdOrdenTrabajo']);
	$filter['FechaHasta']			= trim($_REQUEST['FilterFechaHasta']);
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

//$filter['IdTipoVenta'] = 1;

$arrData 				= array();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oTallerUnidades		= new TallerUnidades();
$oUsuarios				= new Usuarios();
$oEstadosOrden			= new EstadosOrden();
$oClientes				= new Clientes();
$oCompras				= new Compras();
$oCompraDetalles		= new CompraDetalles();
$oArticulos				= new Articulos();

$oPage 					= new Page($Page, $PageSize);

$arrData 	= $oCompras->GetAllVI($filter);

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
	window.location.href = 'ventasinternas.php?MainAction=<?=$Action?>';
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
                        <td height="40"><span class="tituloPagina">Reporte de repuestos vendidos por c&oacute;digo</span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
								<tr>
                                    <td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Agregar" border="0"></div></td>
                                    <td><a href="ventasinternas_exportar.php<?=$strParams?>">Exportar</a></td>
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
                                        	<input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaCargaDesde']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
										<td>
											<input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaCargaHasta']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                            </script>
										</td>
                                        
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuesto</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oCompraDetalle) 
					{
						$oCompra = $oCompras->GetById($oCompraDetalle->IdCompra);
						$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oCompra->IdOrdenTrabajo);
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						$oEstadoOrden = $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
						$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
						$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
						
						$Usuario = '';
						if ($oOrdenTrabajo->IdUsuarioAsignado)
						{
							$oUsuario = $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
							$Usuario = $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
						}
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="75" height="25"><div id="margen"><?= $oArticulo->Codigo ?></div></td>
                        <td width="75" height="25"><div id="margen"><?= $oArticulo->Descripcion ?></div></td>
						<td width="60" height="25"><div id="margen" align="center"><?=number_format($oCompraDetalle->CantidadTotal, 0)?></div></td>
						
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