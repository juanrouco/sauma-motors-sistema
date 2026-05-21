<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para acreedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ACRE_LIST))
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
	$filter['IdTipoPersona'] 		= trim($_REQUEST['FilterTipoPersona']);
	$filter['RazonSocial'] 			= trim($_REQUEST['FilterRazonSocial']);
	$filter['Email'] 				= trim($_REQUEST['FilterEmail']);
	$filter['ClaveFiscalNumero'] 	= trim($_REQUEST['FilterClaveFiscalNumero']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 		= array();
$oAcreedores 	= new Acreedores();
$oPage 			= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oAcreedores->GetCountRows($filter), true);
$arrData 	= $oAcreedores->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

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
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	frmData.FilterTipoPersona.value = '';
	frmData.FilterRazonSocial.value = '';
	frmData.FilterEmail.value 		= '';
	frmData.FilterClaveFiscalNumero.value 	= '';

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

</script>

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
                        <td height="40"><span class="tituloPagina">Acreedores </span></td>
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
                                <?php if (Session::CheckPerm(PERM_ACRE_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="acreedores_add.php<?=$strParams?>">Agregar</a></td>
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
                <div class="bordeGrisFondo" id="ShownFilter" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-left: 10px; padding-bottom: 10px; padding-right: 10px; padding-top: 10px;">
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
                    <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
                        <tr>
                            <td class="tituloMenu">
                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="71" class="tituloMenu">Tipo:</td>
                                        <td width="262">
                                        	<select name="FilterTipoPersona" id="FilterTipoPersona" class="camporFormularioSimple">
                                        		<option value="">INDISTINTO</option>
                                                <?php foreach (PersonaTipos::GetAll() as $oPersonaTipo) { ?>
                                                <option value="<?=$oPersonaTipo['IdTipo']?>" <?=($filter['IdTipoPersona'] == $oPersonaTipo['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oPersonaTipo['Descripcion']?></option>
                                                <?php } ?>
                                        	</select>
                                     	</td>
                                        <td width="83" class="tituloMenu">Raz&oacute;n Social:</td>
                                        <td width="255">
                                        	<input name="FilterRazonSocial" id="FilterRazonSocial" type="text" class="camporFormularioSimple" value="<?=$filter['RazonSocial']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                        </td>
                                        <td width="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu">CUIT / CUIL:</td>
                                        <td valign="top"><input name="FilterClaveFiscalNumero" id="FilterClaveFiscalNumero" type="text" class="camporFormularioSimple" value="<?=$filter['ClaveFiscalNumero']?>" maxlength="30" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td class="tituloMenu">Email:</td>
                                        <td><input name="FilterEmail" id="FilterEmail" type="text" class="camporFormularioSimple" value="<?=$filter['Email']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td width="55" valign="middle"><div align="left">
                                        <input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
                                        </div></td>
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
                        <td width="6" class="bordeGrisTitulo">&nbsp;</td>
                      	<td width="225" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo de Persona</strong></div></td>
                     	<td width="287" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Raz&oacute;n Social</strong></div></td>
                      	<td width="288" height="25" class="bordeGrisTitulo"><div id="margen"><strong>CUIT / CUIL</strong></div></td>
                      	<td width="105" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                  </tr>
          
                <?php foreach ($arrData as $oAcreedor) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="6" class="bordeGrisTitulo">&nbsp;</td>
                        <td height="25"><div id="margen"><?=PersonaTipos::GetById($oAcreedor->IdTipoPersona)?></div></td>
                        <td height="25"><div id="margen"><?=$oAcreedor->RazonSocial?></div></td>
                        <td height="25"><div id="margen"><?=ClaveFiscalTipos::GetById($oAcreedor->ClaveFiscalTipo) . ': ' . $oAcreedor->ClaveFiscalNumero?></div></td>
                        <td width="105" height="25">
                            <div align="center">
                            <?php if (Session::CheckPerm(PERM_ACRE_UPDATE)){ ?>
                                <a href="acreedores_mod.php<?=$strParams?>&IdAcreedor=<?=$oAcreedor->IdAcreedor?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_ACRE_DELETE)){ ?>
                                <a href="acreedores_del.php<?=$strParams?>&IdAcreedor=<?=$oAcreedor->IdAcreedor?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10"><div align="center">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                </tr>
                            </table>
                        </div></td>
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