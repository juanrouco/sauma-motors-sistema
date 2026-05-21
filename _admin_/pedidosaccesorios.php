<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PACC_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdUnidad'] = trim($_REQUEST['FilterIdUnidad']);
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oPedidosAccesorios = new PedidosAccesorios();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oPedidosAccesorios->GetCountRows($filter), true);
$arrData 	= $oPedidosAccesorios->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

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

	frmData.Page.value = 0;
	frmData.submit();
}

function ClearFilter()
{	
	window.location.href = 'pedidosaccesorios.php';
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

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Pedidos de Accesorios</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_PACC_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="pedidosaccesorios_add.php<?=$strParams?>">Agregar</a></td>
                                </tr>
                                <?php } ?>
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
                                        <td class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaDesde" type="text" class="camporFormularioMediano" id="FilterFechaDesde" value="<?=$filter['FechaDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaHasta" type="text" class="camporFormularioMediano" id="FilterFechaHasta" value="<?=$filter['FechaHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Interno:</div></td>
                                        <td><input name="FilterIdUnidad" id="FilterIdUnidad" type="text" class="camporFormularioSimple" value="<?=$filter['IdUnidad']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td width="106" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPedidoAccesorios) { ?>
                    <?php $oMinuta = $oMinutas->GetById($oPedidoAccesorios->IdMinuta); ?>
                    <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
                    <?php $oUnidad = $oUnidades->GetById($oMinuta->IdUnidad); ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="99" height="25"><div id="margen" align="center"><?=$oMinuta->IdUnidad?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="282" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="266" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="93" height="25"><div id="margen"><?=CambiarFecha($oPedidoAccesorios->Fecha)?></div></td>
                        <td width="106" height="25" valign="middle">
                            <div align="center">
                                <a href="pedidosaccesorios_pdf.php<?=$strParams?>&IdPedido=<?=$oPedidoAccesorios->IdPedido?>" target="_blank"><img src="images/iconos/pdf.png" alt="Generar Comprobante" border="0" /></a> - 
                                <a href="pedidosaccesorios_details.php<?=$strParams?>&IdPedido=<?=$oPedidoAccesorios->IdPedido?>"><img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a> - 
                            <?php if (Session::CheckPerm(PERM_PACC_DELETE)){ ?>
                                <a href="pedidosaccesorios_del.php<?=$strParams?>&IdPedido=<?=$oPedidoAccesorios->IdPedido?>"><img src="images/iconos/del.gif" alt="Anular" border="0" /></a>
                            <?php } ?>
                            </div>
                        </td>
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

</body>
</html>