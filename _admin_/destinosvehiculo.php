<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MARC_LIST))
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
	$filter['Nombre'] = trim($_REQUEST['FilterNombre']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 	= array();
$oPage 		= new Page($Page, $PageSize);
$oDestinosVehiculo 	= new DestinosVehiculo();

$Paginado	= Pageable::PrintPaginator($oPage, $oDestinosVehiculo->GetCountRows($filter), true);
$arrData 	= $oDestinosVehiculo->GetAll($filter, $oPage);

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
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	frmData.FilterNombre.value = '';

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

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$_SEVER['PHP_SELF']?>" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Destinos Veh&iacute;culo</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <?php if (Session::CheckPerm(PERM_MARC_CREATE)){ ?>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td height="40"><a href="destinosvehiculo_add.php<?=$strParams?>">Agregar</a></td>
                    <?php } ?>
                        <td width="10">&nbsp;</td>
                    </tr>
                </table>
          </td>
        </tr>
        <tr>
            <td height="30" valign="top">				
                <!-- Aca van los filtros -->
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
                        <table border="0" class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
                            <tr>
                              	<td class="tituloMenu">
                                	<table border="0" cellspacing="0" cellpadding="0">
                                		<tr>
                                  			<td class="tituloMenu"><div align="right">Nombre:</div></td>
                                  			<td><input name="FilterNombre" id="FilterNombre" type="text" class="camporFormularioSimple" value="<?=$filter['Nombre']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
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
                  		<td width="195" height="25" class="bordeGrisTitulo"><div><div id="margen"><strong>c&oacute;digo</strong></div></div></td>
                  		<td width="195" height="25" class="bordeGrisTitulo"><div><div align="left"><strong>Destino Veh&iacute;culo</strong></div></div></td>
                  		<td width="92" height="25" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                	</tr>
          
                <?php foreach ($arrData as $oDestinoVehiculo) { ?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                      	<td width="307" valign="middle"><div id="margen"><?=$oDestinoVehiculo->Codigo?></a></div></td>				  
                    	<td width="195" valign="middle"><div align="left"><?=$oDestinoVehiculo->Nombre?></div>
                      	<td width="92" valign="middle">
                        	<div align="center">
							<?php if (Session::CheckPerm(PERM_MARC_UPDATE)){ ?>
                                <a href="destinosvehiculo_mod.php<?=$strParams?>&IdDestinoVehiculo=<?=$oDestinoVehiculo->IdDestinoVehiculo?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_MARC_DELETE)){ ?>
                                <a href="destinosvehiculo_del.php<?=$strParams?>&IdDestinoVehiculo=<?=$oDestinoVehiculo->IdDestinoVehiculo?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            <?php } ?>
                        	</div>
                       	</td>
                  	</tr>
                    <tr>
                        <td colspan="8">
                          	<div align="left">
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