<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACTPV_LIST))
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
	$filter['NumeroComprobante'] = trim($_REQUEST['FilterNumeroComprobante']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['Cliente'] = trim($_REQUEST['FilterCliente']);
	$filter['Cuil'] = trim($_REQUEST['FilterCuil']);
}
$filter['CuentaCorriente'] = 1;
$filter['NoAnulado'] = 1;

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oFacturasPostVentas 	= new FacturasPostVentas();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oComprobantes 			= new Comprobantes();
$oPage 					= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oFacturasPostVentas->GetCountRows($filter), true);
$arrData 	= $oFacturasPostVentas->GetAll($filter, $oPage);

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
	window.location.href = 'facturaspostventas_ctas_ctes.php';
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Post Venta</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">&nbsp;</td>
                        <td width="12%" height="40">&nbsp;</td>
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
                                        <td><input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioMediano" value="<?=$filter['FechaDesde']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                        <td><input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioMediano" value="<?=$filter['FechaHasta']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Factura:</div></td>
                                        <td><input name="FilterNumeroComprobante" id="FilterNumeroComprobante" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroComprobante']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
									</tr>
									<tr>
									    <td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td><input name="FilterCliente" id="FilterCliente" type="text" class="camporFormularioSimple" value="<?=$filter['Cliente']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
										<td>&nbsp;</td>
									    <td class="tituloMenu"><div align="right">CUIL:</div></td>
                                        <td><input name="FilterCuil" id="FilterCuil" type="text" class="camporFormularioSimple" value="<?=$filter['Cuil']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cuil</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&deg; OT</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe Pago</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe Adeudado</strong></div></td>
                        <td width="126" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php $DeudaTotal = 0; ?>
                <?php foreach ($arrData as $oFacturaPostVenta) { ?>
                    <?php $oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente); ?>
                    <?php $oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva); ?>
                    <?php $oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante); ?>
                    <?php $Deuda = $oFacturaPostVenta->ImporteBruto - $oFacturaPostVenta->TotalPago; ?>
                    <?php $DeudaTotal+= $Deuda; ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="130" height="25"><div id="margen"><?=CambiarFecha($oFacturaPostVenta->Fecha)?></div></td>
                        <td width="80" height="25"><div id="margen"><?=ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)?></div></td>
                        <td width="140" height="25"><div id="margen" align="center"><?=$oComprobante->Numero?></div></td>
                        <td width="320" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="120" height="25"><div id="margen"><?=$oCliente->ClaveFiscalNumero?></div></td>
                        <td width="100" height="25"><div id="margen"><?=$oFacturaPostVenta->IdOrdenTrabajo ? '<a href="ordenestrabajo_detail.php?IdOrdenTrabajo=' . $oFacturaPostVenta->IdOrdenTrabajo . '" target="_blank">' . $oFacturaPostVenta->IdOrdenTrabajo . '</a>' : 'NC' ?></div></td>
                        <td width="130" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaPostVenta->ImporteBruto, 2)?></div></td>
                        <td width="130" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaPostVenta->TotalPago, 2)?></div></td>
                        <td width="130" height="25"><div id="margen" align="center">$ <?=number_format($Deuda, 2)?></div></td>
                        <td width="126" height="25" valign="middle">
                            <div id="margen" align="center">
                                <a href="facturaspostventas_pagos.php<?=$strParams?>&IdFacturaPostVenta=<?=$oFacturaPostVenta->IdFacturaPostVenta?>">
                                    <img src="images/iconos/facturacion.png" alt="Pagar Factura" border="0" /></a>
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
					<tr bgColor='#f3f3f3'>
                        <td width="130" height="25">&nbsp;</td>
                        <td width="80" height="25">&nbsp;</td>
                        <td width="140" height="25">&nbsp;</td>
                        <td width="320" height="25">&nbsp;</td>
                        <td width="120" height="25">&nbsp;</td>
                        <td width="100" height="25">&nbsp;</td>
                        <td width="130" height="25">&nbsp;</td>
                        <td width="130" height="25"><div id="margen" align="center"><strong>Total Adeudado: </strong></div></td>
                        <td width="130" height="25"><div id="margen" align="center"><strong>$ <?=number_format($DeudaTotal, 2)?></strong></div></td>
                        <td width="126" height="25" valign="middle">&nbsp;</td>
                    </tr>     
                
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