<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PART_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
	$filter['Nombre'] 		= trim($_REQUEST['FilterNombre']);
	$filter['IdPais'] 		= trim($_REQUEST['FilterPais']);
	$filter['IdProvincia'] 	= trim($_REQUEST['FilterProvincia']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 		= array();
$oPaises 		= new Paises();
$oProvincias 	= new Provincias();
$oPartidos 		= new Partidos();
$oPage 			= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oPartidos->GetCountRows($filter), true);
$arrData 	= $oPartidos->GetAll($filter, $oPage);

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

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

function ClearFilter()
{
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	frmData.FilterNombre.value 		= '';
	frmData.FilterPais.value 		= '';
	frmData.FilterProvincia.value 	= '';

	frmData.Page.value = 0;

	frmData.submit();
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

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = 0;
	frmData.submit();
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
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Partidos</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <?php if (Session::CheckPerm(PERM_PROV_CREATE)){ ?>
                    <tr>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td height="40"><a href="partidos_add.php<?=$strParams?>">Agregar</a></td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
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
                <div id="Filter">					
                    <table border="0" class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
                        <tr>
                          	<td class="tituloMenu">
                            	<table border="0" cellspacing="0" cellpadding="0">
                            		<tr>
                              			<td class="tituloMenu"><div align="right">Pa&iacute;s:</div></td>
                              			<td>
                                        	<select name="FilterPais" id="FilterPais"  class="camporFormularioSimple" onchange="javascript:LoadProvincias('FilterProvincia', this.value, '');">
                                				<option value="" >INDISTINTO</option>
                                				<?php foreach ($arrPaises as $oPais) { ?>
                                				<option value="<?=$oPais->IdPais?>" <? if ($oPais->IdPais == $filter['IdPais']) echo "selected='selected'"; ?> ><?=$oPais->Nombre?></option>
                                				<?php } ?>
                              				</select>
                                      	</td>
                              			<td width="20" class="tituloMenu">&nbsp;</td>
                              			<td class="tituloMenu"><div align="right">Nombre:</div></td>
                              			<td><input name="FilterNombre" id="FilterNombre" type="text" class="camporFormularioSimple" value="<?=$filter['Nombre']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                            		</tr>
                            		<tr>
                              			<td class="tituloMenu"><div align="right">Provincia:</div></td>
                              			<td>
                                        	<select name="FilterProvincia" id="FilterProvincia" class="camporFormularioSimple">
                                				<option value="">INDISTINTO</option>
                              				</select>
                                      	</td>
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
                        <td width="5" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="200" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Pa&iacute;s</strong></div></td>
                     	<td class="bordeGrisTitulo"><strong>Provincia</strong></td>
                     	<td class="bordeGrisTitulo"><strong>Partido</strong></td>
                        <td width="100" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPartido) { ?>
                    <?php $oPais = $oPaises->GetById($oPartido->IdPais); ?>
                    <?php $oProvincia = $oProvincias->GetById($oPartido->IdProvincia); ?>
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="5" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="200" height="25"><div id="margen"><?=($PaisAnterior != $oPais->Nombre) ? $oPais->Nombre : "";?></div></td>
                        <td width="200" height="25"><?=($ProvinciaAnterior != $oProvincia->Nombre) ? $oProvincia->Nombre : "";?></td>
                      	<td height="25"><?=$oPartido->Nombre?></td>
                        <td width="100" height="25">
                            <div align="center">
                            <?php if (Session::CheckPerm(PERM_PART_UPDATE)){ ?>
                                <a href="partidos_mod.php<?=$strParams?>&IdPartido=<?=$oPartido->IdPartido?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_PART_DELETE)){ ?>
                                <a href="partidos_del.php<?=$strParams?>&IdPartido=<?=$oPartido->IdPartido?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><div align="center">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                </tr>
                            </table>
                        </div></td>
                    </tr>
          
                    <?php $PaisAnterior = $oPais->Nombre; ?>
                    <?php $ProvinciaAnterior = $oProvincia->Nombre; ?>
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