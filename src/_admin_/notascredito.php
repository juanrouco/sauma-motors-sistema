<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CMPB_LIST))
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
	$filter['Numero'] 	= trim($_REQUEST['FilterNumero']);
	$filter['Cliente'] = trim($_REQUEST['FilterCliente']);
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['IdTipoComprobante'] = trim($_REQUEST['IdTipoComprobante']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


/* declaracion de variables */
$arrData 			= array();
$oNotasCredito 		= new NotasCredito();
$oFacturaUnidades 	= new FacturaUnidades();
$oFacturaVarias 	= new FacturaVarias();
$oComprobantes 		= new Comprobantes();
$oClientes			= new Clientes();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oNotasCredito->GetCountRows($filter), true);
$arrData = $oNotasCredito->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&IdTipoComprobante=' 	. $filter['IdTipoComprobante'];
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
	window.location.href = 'comprobantes.php?IdTipoComprobante=<?=$_REQUEST['IdTipoComprobante']?>';
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Notas de Cr&eacute;dito</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <?php if (Session::CheckPerm(PERM_CMPB_CREATE)){ ?>
                    <tr>
					<?php /*
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td height="40"><a href="notascredito_add_paso1.php<?=$strParams?>">Agregar</a></td>
                        <td width="10">&nbsp;</td> */ ?>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                        <td height="40"><a href="notascredito_exportar.php<?=$strParams?>">Exportar</a></td>
                    </tr>
                <?php } ?>
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
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
						<tr>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero:</div></td>
                                        <td><input name="FilterNumero" id="FilterNumero" type="text" class="camporFormularioSimple" value="<?=$filter['Numero']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td><input name="FilterCliente" id="FilterCliente"  class="camporFormularioSimple" type="text" value="<?= $filter['Cliente']?>" />
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
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Factura</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Importe</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Anulada</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oNotaCredito)
					{ 
						$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante);
						$oFactura = $oComprobantes->GetById($oNotaCredito->IdFactura);
						$oCliente = $oClientes->GetById($oNotaCredito->IdCliente);
						if ($oNotaCredito->IdMinuta)
							$oFacturaUnidad = $oFacturaUnidades->GetByIdComprobante($oNotaCredito->IdFactura);
							
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="4" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="75" height="25"><div id="margen"><?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
						<td width="75" height="25"><div id="margen"><?=CambiarFecha($oNotaCredito->Fecha)?></div></td>
                        <td width="200" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
						<td width="75" height="25"><div id="margen"><?=ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)?></div></td>
						<td width="75" height="25"><div id="margen"><?=$oFactura->Prefijo . ' - ' . $oFactura->Numero?></div></td>
                        <td width="75" height="25"><div id="margen">$<?=number_format($oNotaCredito->Importe, 2, ',', '')?></div></td>
						<td width="55" height="25"><div id="margen" align="center"><?=($oComprobante->IdEstado == ComprobanteEstados::Anulado) ? 'SI' : 'NO'?></div></td>
						<td width="75" height="25">
							<div align="center">
								<?php
								if (trim($oComprobante->Prefijo) == '0001' || trim($oComprobante->Prefijo) == '0002')
								{
								?>
								<?php if (!$oNotaCredito->IdMinuta){ ?>
									<?php if ($oFacturaVaria = $oFacturaVarias->GetByIdComprobante($oNotaCredito->IdFactura)){ ?>
								<a href="facturavarias_notacredito_pdf.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
                                    <img src="images/iconos/pdf.png" alt="Generar Comprobante" border="0" /></a>
								<?php } else { ?>
								<a href="notascredito_pdf.php<?=$strParams?>&IdNotaCredito=<?=$oNotaCredito->IdNotaCredito?>">
                                    <img src="images/iconos/pdf.png" alt="Generar Comprobante" border="0" /></a>
								<?php } } else{ ?>
								<a href="facturaunidades_notacredito_pdf.php<?=$strParams?>&IdFactura=<?=$oFacturaUnidad->IdFactura?>">
									 <img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> 
								<?php } ?>
									<?php if ($oComprobante->IdEstado != ComprobanteEstados::Anulado){ ?> - <a href="notascredito_anular.php<?=$strParams?>&IdNotaCredito=<?=$oNotaCredito->IdNotaCredito?>">
                                    <img src="images/iconos/permisos.gif" alt="Anular" border="0" /></a>
								<?php
									}
								}
								else
								{
								?>&nbsp;
								<?php
								}
								?>
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