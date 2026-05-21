<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CPRE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdMinuta'] 			= trim($_REQUEST['FilterIdMinuta']);
	$filter['NumeroContrato']	 	= trim($_REQUEST['FilterNumeroContrato']);
	$filter['FechaDesde'] 			= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 			= trim($_REQUEST['FilterFechaHasta']);
	$filter['IdAcreedor'] 			= trim($_REQUEST['FilterIdAcreedor']);
	$filter['IdEstado']	 			= trim($_REQUEST['FilterIdEstado']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oContratosPrendas 	= new ContratosPrendas();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oAcreedores		= new Acreedores();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oContratosPrendas->GetCountRows($filter), true);
$arrData 	= $oContratosPrendas->GetAll($filter, $oPage);

$arrAcreedores = $oAcreedores->GetAll();

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
	window.location.href = 'contratosprendas.php?MainAction=<?=$MainAction?>';
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

function Select(IdFactura)
{
	window.opener.SetFactura(IdFactura);
	window.close();
}

</script>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Contratos de Prenda</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
       
	  	<?php if ($Action != 'Select') { ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_CPRE_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="contratosprendas_add.php<?=$strParams?>">Agregar</a></td>
									 <td width="30"><div align="center">&nbsp;</div></td>
                                    <td width="30"><div align="center">&nbsp;</div></td>									                                     
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="12%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
								
                                <tr>
									<td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="contratosprendas_reporte.php<?=$strParams?>">Exportar Reporte</a></td>
									<td>&nbsp;</td>
									<?php if (Session::CheckPerm(PERM_CPRE_REPORT)){ ?>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="contratosprendas_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
									<td>&nbsp;</td>
									<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="contratosprendas_export.php<?=$strParams?>">Exportar Reporte</a></td>
									<?php } ?>
								</tr>
								
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php } else { ?>
        <tr>
        	<td>&nbsp;</td>
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
                                        <td class="tituloMenu"><div align="right">Fecha Liq. Desde:</div></td>
                                        <td><input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioChico" value="<?=$filter['FechaDesde']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Liq. Hasta:</div></td>
                                        <td><input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioChico" value="<?=$filter['FechaHasta']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Acreedor:</div></td>
                                        <td><select name="FilterIdAcreedor" id="FilterIdAcreedor" class="camporFormularioSimple">
										<option value="">INDISTINTO</option>
										<?php
										foreach ($arrAcreedores as $oAcreedor)
										{
											$selected = '';
											if ($oAcreedor->IdAcreedor == $filter['IdAcreedor'])
												$selected = 'selected="selected"';
										?>
										<option value="<?= $oAcreedor->IdAcreedor ?>" <?= $selected?>><?= $oAcreedor->RazonSocial ?></option>
										<?php
										}
										?>
										</select>
                                                
										</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&deg; Contrato:</div></td>
                                        <td><input name="FilterNumeroContrato" id="FilterNumeroContrato" type="text" class="camporFormularioMediano" value="<?=$filter['NumeroContrato']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">N&deg; Carpeta:</div></td>
                                        <td><input name="FilterIdMinuta" id="FilterIdMinuta" type="text" class="camporFormularioMediano" value="<?=$filter['IdMinuta']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td><select name="FilterIdEstado" id="FilterIdEstado" class="camporFormularioSimple">
										<option value="">INDISTINTO</option>
										<?php
										foreach (EstadosPrendas::GetAll() as $oEstado)
										{
											$selected = '';
											if ($oEstado['IdEstado'] == $filter['IdEstado'])
												$selected = 'selected="selected"';
										?>
										<option value="<?= $oEstado['IdEstado'] ?>" <?= $selected?>><?= $oEstado['Descripcion'] ?></option>
										<?php
										}
										?>
										</select>
                                                
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Carpeta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Contrato</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Acreedores</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Liquidaci&oacute;n</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Aprob</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Rechazo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Solicitado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acreditado</strong></div></td>
                        <td width="91" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oContratoPrenda) 
					{ 
						$oMinuta = $oMinutas->GetById($oContratoPrenda->IdMinuta);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$cliente = $oCliente->RazonSocial;
						$oAcreedor = $oAcreedores->GetById($oContratoPrenda->IdAcreedor);
						if ($oMinuta->Condominio)
						{
							$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
							$cliente.= " / " . $oClienteCondominio->RazonSocial;
						}
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="85" height="25"><div id="margen" align="center"><?=$oContratoPrenda->IdMinuta?></div></td>
                        <td width="84" height="25"><div id="margen" align="center"><?=$oContratoPrenda->NumeroContrato?></div></td>
                        <td width="85" height="25"><div id="margen" align="left"><?=EstadosPrendas::GetById($oContratoPrenda->IdEstado)?></div></td>
                        <td width="178" height="25"><div id="margen"><?=$cliente?></div></td>
                        <td width="178" height="25"><div id="margen"><?=$oAcreedor->RazonSocial?></div></td>
                        <td width="110" height="25"><div id="margen"><?=CambiarFecha($oContratoPrenda->FechaLiquidacion)?></div></td>
                        <td width="110" height="25"><div id="margen"><?=CambiarFecha($oContratoPrenda->FechaAprobado)?></div></td>
                        <td width="110" height="25"><div id="margen"><?=CambiarFecha($oContratoPrenda->FechaRechazado)?></div></td>
                        <td width="123" height="25"><div id="margen" align="center">$ <?=number_format($oContratoPrenda->MontoSolicitado, 2)?></div></td>
                        <td width="123" height="25"><div id="margen" align="center">$ <?=number_format($oContratoPrenda->MontoAcreditado, 2)?></div></td>
                        <td width="91" height="25" valign="middle">
                            <div align="center">
                                <?php if (Session::CheckPerm(PERM_CPRE_UPDATE)){ ?>
								<a href="contratosprendas_mod.php?IdContratoPrenda=<?=$oContratoPrenda->IdContratoPrenda?>">
									<img src="images/iconos/mod.gif" alt="Eliminar" border="0" />
								</a> - <a href="contratosprendas_del.php?IdContratoPrenda=<?=$oContratoPrenda->IdContratoPrenda?>">
									<img src="images/iconos/del.gif" alt="Eliminar" border="0" />
								</a>
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