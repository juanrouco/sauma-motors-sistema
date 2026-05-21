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
	$filter['IdComprobanteTipo'] = trim($_REQUEST['FilterIdTipoComprobante']);
	$filter['IdPeriodo'] = trim($_REQUEST['FilterIdPeriodo']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 			= array();
$oFacturasCompras 	= new FacturasCompras();
$oProveedores		= new Proveedores();
$oTiposIva			= new TiposIva();
$oPeriodos			= new Periodos();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oFacturasCompras->GetLibroComprasCountRows($filter), true);
$arrData = $oFacturasCompras->GetLibroCompras($filter, $oPage);

$filterPeriodos = array();
$filterPeriodos['FechaDesde'] = date('d-m-Y');
$arrPeriodos = $oPeriodos->GetAll($filterPeriodos);

$oTotales = $oFacturasCompras->GetLibroComprasTotales($filter, $oPage);

$arrComprobantesTipos = ComprobanteTipos::GetAllCompras();

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
	window.location.href = 'libroivacompras.php';
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
                        <td height="40"><span class="tituloPagina">Libro de IVA Compras</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30" height="40">&nbsp;</td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/txt.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivacompras_citi_exportar_text_txt.php<?=$strParams?>">Libro IVA Digital</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/txt.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivacompras_citi_alicuotas_exportar_text_txt.php<?=$strParams?>">Libro IVA Digital Alicuotas</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivacompras_exportar.php<?=$strParams?>">Exportar</a></td>
						<td width="30" height="40"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="libroivacompras_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
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
                                        <td class="tituloMenu"><div align="right">Periodo:</div></td>
                                        <td>
											<select id="FilterIdPeriodo" name="FilterIdPeriodo" class="camporFormularioSimple">
												<option value="">Indistinto</option>
												<?php
												foreach ($arrPeriodos as $oPeriodo)
												{
													$selected = '';
													if ($oPeriodo->IdPeriodo == $filter['IdPeriodo'])
														$selected = 'selected="selected"';
												?>
													<option value="<?= $oPeriodo->IdPeriodo ?>" <?= $selected ?>><?= $oPeriodo->Nombre ?></option>
												<?php
												}
												?>
											</select>
										</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Neto Gravado:</strong> $<?= number_format($oTotales->NetoGravado, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Neto Gravado 10.50:</strong> $<?= number_format($oTotales->Iva10 * 100 / 10.5, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Neto Gravado 21.00:</strong> $<?= number_format($oTotales->Iva21 * 100 / 21, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Neto Gravado 27.00:</strong> $<?= number_format($oTotales->Iva27 * 100 / 27, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 10,5%:</strong> $<?= number_format($oTotales->Iva10, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 21%:</strong> $<?= number_format($oTotales->Iva21, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 27%:</strong> $<?= number_format($oTotales->Iva27, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Exento:</strong> $0,00</div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>No Gravado:</strong> $<?= number_format($oTotales->NoGrabados, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Imp. Interno:</strong> $0,00</div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Percep. IVA:</strong> $<?= number_format($oTotales->PercepcionIva, 2, ',', '.') ?></div></td>
					</tr>
					<tr class="bordeGrisFondo">                        
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Percep. IB:</strong> $<?= number_format($oTotales->PercepcionIB, 2, ',', '.') ?></div></td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>IVA 27%</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Iva 10,5%</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Perc. IB</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Percep.</strong></div></td>						
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>No Grav.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Imp. Interno</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oFacturaCompra)
					{	
						$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor);
						//$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
						<td width="75" height="25"><div id="margen"><?=CambiarFecha($oFacturaCompra->Fecha)?></div></td>
						<td width="50" height="25"><div id="margen"><?= ComprobanteTipos::GetTipoById($oFacturaCompra->IdComprobanteTipo) ?>C</div></td>
						<td width="120" height="25"><div id="margen"><?=ComprobanteTipos::GetLetraById($oFacturaCompra->IdComprobanteTipo) ?><?= $oFacturaCompra->Numero?></div></td>
                        <td width="180" height="25"><div id="margen"><?=$oProveedor->Empresa?></div></td>
						<td width="75" height="25"><div id="margen">RI</div></td>
						<td width="120" height="25"><div id="margen"><?=substr_replace(substr_replace(str_replace('-', '', $oProveedor->Cuit), '-', 10, 0), '-', 2, 0)?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->ImporteNeto, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->Iva21, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->Iva27, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->Iva10, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->PercepcionIB, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->PercepcionIva, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->NoGrabados, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format(0, 2, ',', '.')?></div></td>
						<td width="120" height="25"><div id="margen">$<?= ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) ?><?=number_format($oFacturaCompra->Total, 2, ',', '.')?></div></td>
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