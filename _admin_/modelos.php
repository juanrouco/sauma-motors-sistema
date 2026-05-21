<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MODE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));
$MainAction	= strval($_REQUEST['MainAction']);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVinPrefijo'] = trim($_REQUEST['FilterNumeroVinPrefijo']);
	$filter['DenominacionComercial'] 	= trim($_REQUEST['FilterDenominacion']);
	$filter['IdTipoModelo'] 	= trim($_REQUEST['FilterTipoModelo']);
	$filter['IdMarcaVehiculo'] 	= trim($_REQUEST['FilterMarcaVehiculo']);
	$filter['ConStock'] 		= trim($_REQUEST['FilterConStock']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oModelos 			= new Modelos();
$oTiposModelo 		= new TiposModelo();
$oCategoriasModelo 	= new CategoriasModelo();
$oMarcas 			= new Marcas();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oModelos->GetCountRows($filter), true);
$arrData 	= $oModelos->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrTiposModelo = $oTiposModelo->GetAll();
$arrMarcas = $oMarcas->GetAll();

/* incluimkos funcion para armar suggest */
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


function Select(IdModelo)
{
	window.opener.FilterNumeroVinPrefijo(IdModelo, '');
	window.close();
}

function ClearFilter()
{	
	window.location.href = 'modelos.php';
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
    <input type="hidden" name="MainAction" id="MainAction" />
    <input type="hidden" name="Id" id="Id" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Modelos</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="78%" height="40">
                            <table width="92%" border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>
                                    <?php if (Session::CheckPerm(PERM_MODE_CREATE)){ ?>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td width="49"><a href="modelos_add.php<?=$strParams?>">Agregar</a></td>
                                    <td height="30" >&nbsp;</td>
                                    <?php } ?>
                                    <?php if (Session::CheckPerm(PERM_MODE_IMPORT) && false){ ?>
                                    <td width="30" height="30"><div align="center"><img src="images/iconos/subir_csv.gif" alt="Importar XLS" width="16" border="0"></div></td>
                                    <td width="70" height="30" ><a href="modelos_importar.php">Importar XLS</a></td>
                                    <td width="10" height="30" >&nbsp;</td>
                                    <?php } ?>
                                    <?php if (Session::CheckPerm(PERM_MODE_IMPORT) && false){ ?>
                                    <td width="30" height="30"><div align="center"><img src="images/iconos/descarga.gif" alt="Descargar archivo base" width="16" border="0"></div></td>
                                    <td width="73" height="30" ><a href="modelos_exportar_mod.php">Archivo base</a></td>
                                    <td width="10" height="30" >&nbsp;</td>
                                    <?php } ?>
                                    <?php if (Session::CheckPerm(PERM_MODE_IMPORT) && false){ ?>
                                    <td width="30" height="30"><div align="center"><img src="images/iconos/referencias.png" alt="Referencias" width="16" border="0"></div></td>
                                    <td width="394" height="30" ><a href="modelos_ref.php<?=$strParams?>">Referencias</a></td>
                                    <?php } ?>
                                </tr>
                            </table>
                        </td>
                        <td width="12%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="24"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td width="70"><a href="modelos_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="10%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="23"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td width="67"><a href="modelos_exportar.php<?=$strParams?>">Exportar XLS</a></td>
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
                    <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
                        <tr>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Tipo:</div></td>
                                        <td><select name="FilterTipoModelo" id="FilterTipoModelo"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrTiposModelo as $oTipoModelo) { ?>
                                        <option value="<?=$oTipoModelo->IdTipoModelo?>" <?php if ($oTipoModelo->IdTipoModelo == $filter['IdTipoModelo']) echo "selected='selected'"; ?> ><?=$oTipoModelo->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Marca Vehic.:</div></td>
                                        <td><select name="FilterMarcaVehiculo" id="FilterMarcaVehiculo"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Stock:</div></td>
                                        <td><select name="FilterConStock" id="FilterConStock"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['ConStock']) echo "selected='selected'"; ?> >Sin Stock</option>
                                        <option value="1" <?php if ('1' == $filter['ConStock']) echo "selected='selected'"; ?> >Con Stock</option>
                                        </select></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Prefijo Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVinPrefijo" id="FilterNumeroVinPrefijo" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVinPrefijo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Modelos', 'GetAll', 'FilterNumeroVinPrefijo', 'SetNumeroVinPrefijo', 'IdModelo', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', null);
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Denominaci&oacute;n:</div></td>
                                        <td><input name="FilterDenominacion" id="FilterDenominacion" type="text" class="camporFormularioSimple" value="<?=$filter['DenominacionComercial']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Comercial</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Prefijo Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>A&ntilde;o</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Categor&iacute;a</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Marca Vehic.</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" ><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oModelo) { ?>
                    <?php $oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo); ?>
                    <?php $oCategoriaModelo = $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo); ?>
                    <?php $oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="272" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="150" height="25"><div id="margen"><?=$oModelo->NumeroVinPrefijo?></div></td>
                        <td width="100" height="25"><div id="margen"><?=$oModelo->Anio?></div></td>
                        <td width="145" height="25"><div id="margen"><?=$oTipoModelo->Nombre?></div></td>
                        <td width="92" height="25"><div id="margen"><?=$oCategoriaModelo->Nombre?></div></td>
                        <td width="133" height="25"><div id="margen"><?=$oMarca->Nombre?></div></td>
                        <td width="84" height="25" valign="middle">
                            <div align="center"> 
							<?php
							if ($MainAction == 'Select')
							{
							?>
                                <a href="#" onClick="javascript: Select('<?=$oModelo->IdModelo?>');">
                                    <img src="images/iconos/preview.png" alt="Seleccionar" border="0" /></a>
							<?php
							}
							else
							{
                             if (Session::CheckPerm(PERM_MODE_UPDATE)){ ?>
                                <a href="modelos_mod.php<?=$strParams?>&IdModelo=<?=$oModelo->IdModelo?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_MODE_DELETE)){ ?>
                                <a href="modelos_del.php<?=$strParams?>&IdModelo=<?=$oModelo->IdModelo?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            <?php }
							}							?>
                            </div>
                        </td>
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