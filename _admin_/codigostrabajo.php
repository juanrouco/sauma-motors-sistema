<?php 
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_CODTRA_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdModeloPV']		= trim($_REQUEST['FilterIdModeloPV']);
	$filter['CodigoHistorico'] 	= trim($_REQUEST['FilterCodigoHistorico']);
	$filter['Codigo'] 			= trim($_REQUEST['FilterCodigo']);
	$filter['Descripcion'] 		= trim($_REQUEST['FilterDescripcion']);	
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$arrData 				= array();
$oCodigosTrabajo		= new CodigosTrabajo();
$oModelosPV				= new ModelosPV();
$oPage 					= new Page($Page, $PageSize);

$Paginado		= Pageable::PrintPaginator($oPage, $oCodigosTrabajo->GetCountRows($filter), true);
$arrData 		= $oCodigosTrabajo->GetAll($filter, $oPage);
$arrModelosPV 	= $oModelosPV->GetAll();

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

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
	window.location.href = 'codigostrabajo.php?MainAction=<?=$Action?>';
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
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de C&oacute;digos de Trabajo</span></td>
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
                                <?php if (Session::CheckPerm(PERM_CODTRA_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="codigostrabajo_add.php<?=$strParams?>">Agregar</a></td>
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
                                        <td class="tituloMenu"><div align="right">Descripci&oacute;n:</div></td>
                                        <td><input name="FilterDescripcion" id="FilterDescripcion" type="text" class="camporFormularioSimple" value="<?=$filter['Descripcion']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
										<td>
											<select name="FilterIdModeloPV" id="FilterIdModeloPV"  class="camporFormularioSimple">
												<option value="">[Indistinto]</option>
												<?php
												foreach ($arrModelosPV as $oModeloPV)
												{
													$selected = '';
													if ($oModeloPV->IdModeloPV == $filter['IdModeloPV'])
														$selected = 'selected="selected"';
												?>
												<option value="<?= $oModeloPV->IdModeloPV ?>" <?= $selected ?>><?= $oModeloPV->Modelo ?></option>
												<?php
												}
												?>
											</select>
										</td>
                                        <td>&nbsp;</td>
                                    </tr>  
									<tr>                              
                                        <td class="tituloMenu"><div align="right">C&oacute;digo:</div></td>
                                        <td><input name="FilterCodigo" id="FilterCodigo" type="text" class="camporFormularioSimple" value="<?=$filter['Codigo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" /></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">C&oacute;digo Hist&oacute;rico:</div></td>
										<td>
											<input name="FilterCodigoHistorico" id="FilterCodigoHistorico"  class="camporFormularioSimple" value="<?= $filter['CodigoHistorico'] ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo Hist&oacute;rico</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
                        <td width="100" height="25"  class="bordeGrisTitulo"><div align="center" ><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oCodigoTrabajo) 
				{
					$oModeloPV = $oModelosPV->GetById($oCodigoTrabajo->IdModeloPV);
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="300" height="25"><div id="margen"><?=$oModeloPV->Modelo?></div></td>
						<td width="100" height="25"><div id="margen"><?=$oCodigoTrabajo->Codigo?></div></td>						
                        <td width="100" height="25"><div id="margen"><?=$oCodigoTrabajo->CodigoHistorico?></div></td>
						<td width="450" height="25"><div id="margen"><?=$oCodigoTrabajo->Descripcion?></div></td>
						<td width="100" height="25" valign="middle">
                            <div align="center">                            
						    <?php 
							if (Session::CheckPerm(PERM_CODTRA_UPDATE))
							{ 
							?>
                                <a href="codigostrabajo_mod.php<?=$strParams?>&IdCodigoTrabajo=<?=$oCodigoTrabajo->IdCodigoTrabajo?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php 
							} 
							?>
                            <?php if (Session::CheckPerm(PERM_CODTRA_DELETE)){ ?>
                                <a href="codigostrabajo_del.php<?=$strParams?>&IdCodigoTrabajo=<?=$oCodigoTrabajo->IdCodigoTrabajo?>">
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