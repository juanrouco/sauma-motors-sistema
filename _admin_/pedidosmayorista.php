<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PEDMAY_LIST))
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
	$filter['IdPedidoMayorista'] 	= trim($_REQUEST['FilterIdPedidoMayorista']);
	$filter['Cliente'] 				= trim($_REQUEST['FilterCliente']);
	$filter['FechaPedidoMayoristaDesde'] = trim($_REQUEST['FilterFechaPedidoMayoristaDesde']);
	$filter['FechaPedidoMayoristaHasta'] = trim($_REQUEST['FilterFechaPedidoMayoristaHasta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oPedidosMayorista 		= new PedidosMayorista();
$oClientes		 		= new Clientes();
$oPage 					= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oPedidosMayorista->GetCountRows($filter), true);
$arrData 	= $oPedidosMayorista->GetAll($filter, $oPage);

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
	window.location.href = 'pedidosmayorista.php';
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Pedidos Mayoristas</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
    
        <?php if (Session::CheckPerm(PERM_PEDMAY_CREATE)){ ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td width="809" height="40"><a href="pedidosmayorista_add_paso1.php<?=$strParams?>">Agregar</a></td>
                        <td width="102">&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
        <?php } ?>
            
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
										<td class="tituloMenu"><div align="right">Fecha Pedido Desde:</div></td>
										<td>
                                            <div align="left">
                                                <input name="FilterFechaPedidoMayoristaDesde" type="text" class="camporFormularioMediano" id="FilterFechaPedidoMayoristaDesde" value="<?=$filter['FechaPedidoMayoristaDesde']?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaPedidoMayoristaDesde'});
                                                </script>
                                            </div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Pedido Hasta:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="FilterFechaPedidoMayoristaHasta" type="text" class="camporFormularioMediano" id="FilterFechaPedidoMayoristaHasta" value="<?=$filter['FechaPedidoMayoristaHasta']?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaPedidoMayoristaHasta'});
                                                </script>
                                            </div>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td><input name="FilterCliente" id="FilterCliente" type="text" class="camporFormularioSimple" value="<?=$filter['Cliente']?>" maxlength="128 onkeyup="javascript: StrToUpper(this.id);""></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Pedido:</div></td>
                                        <td><input name="FilterIdPedidoMayorista" id="FilterIdPedidoMayorista" type="text" class="camporFormularioSimple" value="<?=$filter['IdRececpion']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        
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
                        <?php foreach (PedidosMayoristaEstados::GetAll() as $oPedidoMayoristaEstado) { ?>
                        <td bgcolor="<?=PedidosMayoristaEstados::GetColorById($oPedidoMayoristaEstado['IdEstado'])?>" width="20" height="20"><div align="left"></div></td>
                        <td>&nbsp;</td>
                        <td><div align="left"><?=$oPedidoMayoristaEstado['Descripcion']?></div></td>
                        <?php } ?>
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
                        <td>&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Pedido</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Pedido</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cant. Unidades</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acreditado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Saldo</strong></div></td>
                        <td width="116" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPedidoMayorista) { ?>
                <?php 	$oCliente = $oClientes->GetById($oPedidoMayorista->IdCliente); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td bgcolor="<?=RecepcionEstados::GetColorById($oPedidoMayorista->IdEstado)?>" width="11">&nbsp;</td>
                        <td width="100" height="25"><div id="margen" align="center"><?=$oPedidoMayorista->IdPedidoMayorista?></div></td>
                        <td width="251" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="100" height="25"><div id="margen"><?=CambiarFecha($oPedidoMayorista->FechaPedidoMayorista)?></div></td>
                        <td width="100" height="25"><div id="margen" align="center"><?=$oPedidoMayorista->GetCountUnidades();?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oPedidoMayorista->GetCostoTotal(), 2);?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oPedidoMayorista->GetTotalAcreditado(), 2);?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oPedidoMayorista->GetTotalPendiente(), 2);?></div></td>
                        <td width="116" height="25" valign="middle">
                            <div align="center">
							<a href="pedidosmayorista_detalles.php<?=$strParams?>&IdPedidoMayorista=<?=$oPedidoMayorista->IdPedidoMayorista?>">
                                    <img src="images/iconos/preview.png" alt="Detalles" border="0" /></a> - 
                            <?php if (Session::CheckPerm(PERM_PEDMAY_UPDATE)){ ?>
                                <a href="pedidosmayorista_mod_paso1.php<?=$strParams?>&IdPedidoMayorista=<?=$oPedidoMayorista->IdPedidoMayorista?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php } ?>	
                            <?php if (Session::CheckPerm(PERM_PAGO_LIST)){ ?>
                                <a href="pagos_pedidosmayorista.php<?=$strParams?>&IdPedidoMayorista=<?=$oPedidoMayorista->IdPedidoMayorista?>">
                                    <img src="images/iconos/facturacion.png" alt="Modificar" border="0" /></a>
                            <?php } ?>							 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
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