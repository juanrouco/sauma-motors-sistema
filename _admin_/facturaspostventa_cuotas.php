<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_LIBRO_IVA))
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
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['IdTipoComprobante'] = trim($_REQUEST['FilterIdTipoComprobante']);
	$filter['NumeroComprobante'] = trim($_REQUEST['FilterNumeroComprobante']);
}

$filter['Prefijo'] = '0002';

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 		= array();
$oComprobantes 	= new Comprobantes();
$oClientes		= new Clientes();
$oTiposIva		= new TiposIva();
$oProveedores	= new Proveedores();
$oPage 			= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oComprobantes->GetCountRowsCuotas($filter), true);
$arrData = $oComprobantes->GetAllCuotas($filter, $oPage);

$arrComprobantesTipos = ComprobanteTipos::GetAllVentas();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
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
	window.location.href = 'facturaspostventa.php';
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
                        <td height="40"><span class="tituloPagina">Facturas Post Venta</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                   <?php /* <tr>
                        <td width="30" height="40">&nbsp;</td>
                        <td height="40">&nbsp;</td>
                        <td width="200">&nbsp;</td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivaventas_reportepv_exportar.php<?=$strParams?>">Reporte PostVenta</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/txt.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivaventas_percepciones_exportar_text_txt.php<?=$strParams?>">Obtener Percepciones</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivaventas_exportar.php<?=$strParams?>">Exportar</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivaventas_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
                    </tr> */ ?>
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
                                        <td><input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioSimple" value="<?=$filter['FechaDesde']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                        <td><input name="FilterFechaHasta" id="FilterFechaHasta"  class="camporFormularioSimple" type="text" maxlength="12" value="<?= $filter['FechaHasta']?>" />
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                                </script>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
									<tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero:</div></td>
                                        <td><input name="FilterNumeroComprobante" id="FilterNumeroComprobante" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroComprobante']?>" maxlength="12">
									
										</td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Raz&oacute;n Social</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cond.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>CUIT</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Neto Grav.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 21%</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 10,5%</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Perc. IIBB</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>No Grav.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oComprobante)
					{	
						$oCliente = $oClientes->GetById($oComprobante->IdCliente);
						$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
						
						$totalIva10 = $oComprobante->ImporteIva10 * 100 / 10.5;
						$totalIva21 = $oComprobante->ImporteIva21 * 100 / 21;
						$NoGrabado = $oComprobante->Importe - ($oComprobante->PercepcionIIBB + $totalIva10 + $totalIva21 + $oComprobante->ImporteIva21 + $oComprobante->ImporteIva10);
						if ($NoGrabado < 0.5)
							$NoGrabado = 0;
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
						<td width="75" height="25"><div id="margen"><?=CambiarFecha($oComprobante->Fecha)?></div></td>
						<td width="50" height="25"><div id="margen"><?= ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) ?>V</div></td>
						<td width="120" height="25"><div id="margen"><?=ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) ?><?= $oComprobante->Prefijo?>-<?= $oComprobante->Numero?></div></td>
                        <td width="180" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
						<td width="75" height="25"><div id="margen"><?=$oTipoIva->Codigo?></div></td>
						<td width="120" height="25"><div id="margen"><?=$oCliente->ClaveFiscalNumero != '' ? substr_replace(substr_replace(str_replace('-', '', $oCliente->ClaveFiscalNumero), '-', 10, 0), '-', 2, 0) : ''?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($totalIva10 + $totalIva21, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->ImporteIva21, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->ImporteIva10, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->PercepcionIIBB, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($NoGrabado, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) ?><?=number_format($oComprobante->Importe, 2, ',', '.')?></div></td>
                    </tr>
                    <tr>
                        <td colspan="13">
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