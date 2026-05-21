<?php 

require_once('../inc_library.php'); 

/* secci�n exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACV_LIST))
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
	$filter['FechaDesde'] = trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FilterFechaHasta']);
	$filter['Cliente'] 		= trim($_REQUEST['FilterCliente']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oFacturaVarias 	= new FacturaVarias();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oComprobantes 		= new Comprobantes();
$oNotasCredito 		= new NotasCredito();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oFacturaVarias->GetCountRows($filter), true);
$arrData 	= $oFacturaVarias->GetAll($filter, $oPage);

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
	window.location.href = 'facturavarias.php';
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



    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas</span></td>
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
                                <?php if (Session::CheckPerm(PERM_FACV_CREATE)){ ?>
                                <tr>
                                    <!-- <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="facturavarias_add_paso1.php<?=$strParams?>">Agregar Factura de Talonario</a></td>
									<td width="10">&nbsp;</td> -->
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="facturavarias_facturaelectronica_add.php<?=$strParams?>">Agregar</a></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="12%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="facturavarias_exportar.php<?=$strParams?>">Exportar XLS</a></td>
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
					<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
					<input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
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
									</tr>
									<tr>
                                                          
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Factura:</div></td>
                                        <td><input name="FilterNumeroComprobante" id="FilterNumeroComprobante" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroComprobante']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td><td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td><input name="FilterCliente" id="FilterCliente" type="text" class="camporFormularioSimple" value="<?=$filter['Cliente']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                       
                                        <td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
					</form>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Anulada</strong></div></td>
                        <td width="126" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
					<?php
					foreach ($arrData as $oFacturaVaria)
					{
						$oCliente = $oClientes->GetById($oFacturaVaria->IdCliente);
						$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
						$oComprobante = $oComprobantes->GetById($oFacturaVaria->IdComprobante);
					?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="80" height="25"><div id="margen"><?=ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)?></div></td>
                        <td width="140" height="25"><div id="margen" align="center"><?=$oComprobante->Numero?></div></td>
                        <td width="320" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="130" height="25"><div id="margen"><?=CambiarFecha($oFacturaVaria->Fecha)?></div></td>
                        <td width="130" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaVaria->Total, 2)?></div></td>
                        <td width="87" height="25"><div id="margen" align="center"><?=($oComprobante->IdEstado == ComprobanteEstados::Anulado) ? 'SI' : 'NO'?></div></td>
                        <td width="126" height="25" valign="middle">
                            <div align="center">
							<?php 
							if ($oComprobante->IdEstado != ComprobanteEstados::Anulado)
							{
								if (!$oComprobante->Numero || $oComprobante->Numero == '00000000')
								{
							?>
                                <form action="facturavarias_afip.php" style="display: inline">
									<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaVaria->IdFactura ?>" />
									<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
								</form> - 
							<?php 
								}
								else	
								{
							?>
                                <a href="facturavarias_pdf.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
                                    <img src="images/iconos/pdf.png" alt="Generar Comprobante" border="0" /></a> - 
							<?php 
								}
							}
							else
							{
								$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
								$oComprobanteNC = $oComprobantes->GetById($oNotaCredito->IdComprobante);
								if (!$oComprobanteNC->Numero || $oComprobanteNC->Numero == '00000000')
								{
							?>
                                <form action="facturavarias_notascredito_afip.php" style="display: inline">
									<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaVaria->IdFactura ?>" />
									<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
								</form> - 
							<?php 
								}
								else	
								{
							?>
								<a href="facturavarias_notacredito_pdf.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
									 <img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> - 
							<?php 
								}
							}
							?>
							<a href="facturavarias_details.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
								<img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a> - 
							<?php
							if ($oComprobante->IdEstado != ComprobanteEstados::Anulado)
							{
								if (Session::CheckPerm(PERM_FACV_UPDATE))
								{
									if ($oComprobante->Numero && $oComprobante->Numero != '00000000')
									{
										if (!$oComprobante->Archivo || $oComprobante->Archivo == '')
										{
							?>
                                <a href="facturavarias_anular.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
                                    <img src="images/iconos/permisos.gif" alt="Anular" border="0" /></a>
							<?php
										}
										else
										{
							?>
                                <a href="facturavarias_facturaelectronica_anular.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
                                    <img src="images/iconos/permisos.gif" alt="Anular" border="0" /></a>
							<?php
										}
									}
									else
									{
							?>
                                <a href="facturavarias_del.php<?=$strParams?>&IdFactura=<?=$oFacturaVaria->IdFactura?>">
                                    <img src="images/iconos/del.gif" alt="Anular" border="0" /></a>
							<?php
									}
								}
							}
							?>
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

</body>
</html>