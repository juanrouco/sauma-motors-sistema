<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_UNIDADES))
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
	$filter['FechaDesde'] 	= trim($_REQUEST['FechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FechaHasta']);
	$filter['Numero'] = trim($_REQUEST['FilterNumero']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* agregamos filtro oculto para tipo de comprobante */
$filter['IdTipoComprobante'] 	= trim($_REQUEST['IdTipoComprobante']);
$filter['Prefijo'] 				= '0002';

/* declaracion de variables */
$arrData 		= array();
$oComprobantes 	= new Comprobantes();
$oClientes 		= new Clientes();
$oPage 			= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oComprobantes->GetCountRows($filter), true);
$arrData = $oComprobantes->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&IdTipoComprobante=' 	. $filter['IdTipoComprobante'];
$strParams.= '&Prefijo=' 			. $filter['Prefijo'];
$strParams.= '&FechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&filter=' 			. SendArray($filter);

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
	window.location.href = 'contabilidad_reporte.php?IdTipoComprobante=<?=$_REQUEST['IdTipoComprobante']?>';
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
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Reporte de Contabilidad de Post Venta - <?=ComprobanteTipos::GetDescripcionById($filter['IdTipoComprobante'])?></span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/excel.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="contabilidad_reporte_xls.php<?=$strParams?>">Exportar</a></td>
                        <td width="10">&nbsp;</td>
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
                                        <td><input name="FechaDesde" id="FechaDesde" type="text" class="camporFormularioSimple" value="<?=$filter['FechaDesde']?>" size="12" maxlength="12" />
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaDesde'});
                                                </script>
										</td>
										<td width="10">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                       <td><input name="FechaHasta" id="FechaHasta" type="text" class="camporFormularioSimple" value="<?=$filter['FechaHasta']?>" size="12" maxlength="12" />
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaHasta'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero:</div></td>
                                        <td><input name="FilterNumero" id="FilterNumero" type="text" class="camporFormularioSimple" value="<?=$filter['Numero']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
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
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Importe</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>CUIL</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oComprobante) 
					{
						$oCliente = $oClientes->GetById($oComprobante->IdCliente);
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="100" height="25"><div id="margen"><?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
						<td width="100" height="25"><div id="margen"><?=ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)?></div></td>
                        <td width="75" height="25"><div id="margen"><?=CambiarFecha($oComprobante->Fecha)?></div></td>
						<td width="75" height="25"><div id="margen">$<?=number_format($oComprobante->Importe, 2, ',', '.')?></div></td>
						<td width="128" height="25"><div id="margen"><?= $oCliente->RazonSocial ?></div></td>
						<td width="128" height="25"><div id="margen"><?= $oCliente->ClaveFiscalNumero ?></div></td>
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