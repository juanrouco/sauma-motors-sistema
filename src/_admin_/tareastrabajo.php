<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TARE_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVinPrefijo'] = trim($_REQUEST['FilterNumeroVinPrefijo']);
	$filter['IdModeloPV'] 		= trim($_REQUEST['FilterIdModeloPV']);
	$filter['Anio'] 			= trim($_REQUEST['FilterAnio']);
	$filter['PalabraClave'] 	= trim($_REQUEST['FilterPalabraClave']);	
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$arrData 				= array();
$oTareasTrabajo			= new TareasTrabajo();
$oModelosPV				= new ModelosPV();
$oPage 					= new Page($Page, $PageSize);
$Paginado	= Pageable::PrintPaginator($oPage, $oTareasTrabajo->GetCountRows($filter), true);
$arrData 	= $oTareasTrabajo->GetAll($filter, $oPage);

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$filter = array();
$filter['Disponible'] = '1';

$arrModelos 		= $oModelosPV->GetAll($filter);

IncludeSUGGEST();

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
	window.location.href = 'tareastrabajo.php?MainAction=<?=$Action?>';
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

</script>

<?php include('include/head.inc.php'); ?>

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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Tareas de Taller</span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_TARE_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="tareastrabajo_add.php<?=$strParams?>">Agregar</a></td>
									<td width="10" height="30" >&nbsp;</td>
									<td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Cargar Service" border="0"></div></td>
                                    <td><a href="tareastrabajo_cargar_service.php<?=$strParams?>">Cargar Service</a></td>
									<td width="10" height="30" >&nbsp;</td>
									<td width="30"><div align="center"><img src="images/iconos/excel.png" alt="Descargar Archivo Base" border="0"></div></td>
                                    <td><a target="_blank" href="tareastrabajo_xls.php">Archivo Base</a></td>
									<td width="10" height="30" >&nbsp;</td>
									<td width="30"><div align="center"><img src="images/iconos/relacion.png" alt="Actualizar" border="0"></div></td>
                                    <td><a href="tareastrabajo_import.php">Actualizar</a></td>
									<td width="10" height="30" >&nbsp;</td>
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
                                        <td class="tituloMenu"><div align="right">Palabra Clave:</div></td>
                                        <td><input name="FilterPalabraClave" id="FilterPalabraClave" type="text" class="camporFormularioSimple" value="<?=$filter['PalabraClave']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
										<td>
											<select name="FilterIdModeloPV" id="FilterIdModeloPV"  class="camporFormularioSimple">
												<option value="">INDISTINTO</option>
												<?php 
												foreach ($arrModelos as $oModeloPV) 
												{ 
												?>
												<option value="<?=$oModeloPV->IdModeloPV?>" <?php if ($oModeloPV->IdModeloPV == $filter['IdModeloPV']) echo "selected='selected'"; ?> ><?=$oModeloPV->Modelo?></option>
												<?php 
												} 
												?>
											</select>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nombre</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Hs. Estimadas</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Mano Obra</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuestos</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>
                        <td width="100" height="25"  class="bordeGrisTitulo"><div id="margen" ><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oTareaTrabajo) 
				{
                    $oModeloPv = $oModelosPV->GetById($oTareaTrabajo->IdModeloPV); 
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="250" height="25"><div id="margen"><?=$oModeloPv->Modelo?></div></td>
						<td width="250" height="25"><div id="margen"><?=$oTareaTrabajo->Titulo?></div></td>
						<td width="100" height="25"><div id="margen">&nbsp;&nbsp;<?=$oTareaTrabajo->HorasEstimadas?></div></td>						
                        <td width="100" height="25"><div id="margen"><?=$oTareaTrabajo->IdTipoCosto == TipoCosto::CostoFijo ? 'N/C' : '$' . $oTareaTrabajo->ImporteManoObra()?></div></td>
						<td width="100" height="25"><div id="margen"><?=$oTareaTrabajo->IdTipoCosto == TipoCosto::CostoFijo ? 'N/C' : '$' . $oTareaTrabajo->ImporteRepuestos()?></div></td>						
						<td width="100" height="25"><div id="margen">$<?=$oTareaTrabajo->ImporteTotal()?></div></td>
						<td width="100" height="25" valign="middle">
                            <div align="center">                            
						    <?php 
							if (Session::CheckPerm(PERM_TARE_UPDATE))
							{ 
							?>
								<a href="tareastrabajo_articulos_relacionados.php<?=$strParams?>&IdTareaTrabajo=<?=$oTareaTrabajo->IdTareaTrabajo?>">
                                    <img src="images/iconos/relacion.png" alt="Art&iacute;culos Relacionados" border="0" /></a> - 
								<a href="tareastrabajo_codigostrabajo_relacionados.php<?=$strParams?>&IdTareaTrabajo=<?=$oTareaTrabajo->IdTareaTrabajo?>">
                                    <img src="images/iconos/adm_general.png" alt="Codigos de Trabajo" border="0" /></a> - 
                                <a href="tareastrabajo_mod.php<?=$strParams?>&IdTareaTrabajo=<?=$oTareaTrabajo->IdTareaTrabajo?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php 
							} 
							?>
                            <?php if (Session::CheckPerm(PERM_TARE_DELETE)){ ?>
                                <a href="tareastrabajo_del.php<?=$strParams?>&IdTareaTrabajo=<?=$oTareaTrabajo->IdTareaTrabajo?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
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