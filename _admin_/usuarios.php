<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_USUA_LIST))
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
	$filter['IdSector'] = trim($_REQUEST['FilterSector']);
	$filter['IdPerfil'] = trim($_REQUEST['FilterPerfil']);
	
	/* En caso de querer agregar una venta, solo levanta los autos en stock */
	if ($_REQUEST['FilterMinutasAdd'] == '1')
		$filter['IdEstado'] = EstadoUnidad::Stock;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 	= array();
$oPerfiles 	= new Perfiles();
$oSectores 	= new Sectores();
$oUsuarios 	= new Usuarios();
$oPage 		= new Page($Page, $PageSize);

/* obtenemos listado de usuarios */
$Paginado	= Pageable::PrintPaginator($oPage, $oUsuarios->GetCountRows($filter), true);
$arrData 	= $oUsuarios->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrSectores = $oSectores->GetAll();
$arrPerfiles = $oPerfiles->GetAll();

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
	window.location.href = 'usuarios.php';
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
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Usuarios</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
    
        <?php if (Session::CheckPerm(PERM_USUA_CREATE)){ ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td height="40"><a href="usuarios_add.php" >Agregar</a></td>
                    </tr>
                </table>		
             </td>
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
                                        <td class="tituloMenu"><div align="right">Sector:</div></td>
                                        <td><select name="FilterSector" id="FilterSector"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrSectores as $oSector) { ?>
                                        <option value="<?=$oSector->IdSector?>" <?php if ($oSector->IdSector == $filter['IdSector']) echo "selected='selected'"; ?> ><?=$oSector->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Perfil:</div></td>
                                        <td><select name="FilterPerfil" id="FilterPerfil"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrPerfiles as $oPerfil) { ?>
                                        <option value="<?=$oPerfil->IdPerfil?>" <?php if ($oPerfil->IdPerfil == $filter['IdPerfil']) echo "selected='selected'"; ?> ><?=$oPerfil->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
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
                        <td width="5%" height="25" class="bordeGrisTitulo">
                            <div id="margen"><div align="left"><strong>Nro.</strong></div></div>
                        </td>
                        <td width="22%" height="25" class="bordeGrisTitulo">
                            <div id="margen"><div align="left"><strong>Nombre</strong></div></div>
                        </td>
                        <td width="22%" height="25" class="bordeGrisTitulo"><div align="left"><strong>Apellido</strong></div></td>
                        <td width="22%" height="25" class="bordeGrisTitulo"><div align="left"><strong>Sector</strong></div></td>
                        <td width="22%" height="25" class="bordeGrisTitulo"><div align="left"><strong>Perfil</strong></div></td>
                        <td width="12%" class="bordeGrisTitulo"><div align="left"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUsuario) { ?>
                    <?php $oPerfil = $oPerfiles->GetById($oUsuario->IdPerfil)?>
                    <?php $oSector = $oSectores->GetById($oUsuario->IdSector)?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25">
                            <div align="left" id="margen"><div align="left"><?=$oUsuario->IdUsuario?></div></div>
                        </td>
                        <td height="25">
                            <div align="left" id="margen"><div align="left"><?=$oUsuario->Nombre?></div></div>
                        </td>
                        <td height="25"><div align="left"><?=$oUsuario->Apellido?></div></td>
                        <td height="25"><div align="left"><?=$oSector->Nombre?></div></td>
                        <td height="25"><div align="left"><?=$oPerfil->Nombre?></div></td>
                        <td width="80" height="25"> 
                            <div align="left">
							<?php if (Session::CheckPerm(PERM_USUA_UPDATE)){ ?>
                                <a href="usuariojornadas.php?IdUsuario=<?=$oUsuario->IdUsuario?>"><img src="images/iconos/referencias.png" alt="Horarios" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_USUA_UPDATE)){ ?>
                                <a href="usuarios_mod.php?IdUsuario=<?=$oUsuario->IdUsuario?>"><img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
								<a href="cajasusuarios.php?IdUsuario=<?=$oUsuario->IdUsuario?>"><img src="images/iconos/detalles.png" alt="Modificar" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_USUA_DELETE)){ ?>
                                <?php if ($oUsuario->IdUsuario != $currentUser->IdUsuario){ ?>
                                <a href="usuarios_del.php?IdUsuario=<?=$oUsuario->IdUsuario?>"><img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                                <?php } ?>
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