<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Id			= intval($_REQUEST['Id']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
	$filter['Modelo'] = trim($_REQUEST['FilterModelo']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaramos e instanciamos variables necesarias */
$arrData 	= array();
$oModelosPV = new ModelosPV();
$oPage 		= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oModelosPV->GetCountRows($filter), true);
$arrData = $oModelosPV->GetAll($filter, $oPage);

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

	frmData.FilterModelo.value = '';

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
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" />
    <input type="hidden" name="Id" id="Id" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloModeloPV">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Modelos</span></td>
                    </tr>
                </table>		
           	</td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                        <td height="40"><a href="modelospv_add.php<?=$strParams?>">Agregar</a></td>
                        <td width="10">&nbsp;</td>
                    </tr>
          </table>		</td>
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
                              <td class="tituloMenu"><table border="0" cellspacing="0" cellpadding="0">
                                <tr>                              
                                  <td class="tituloMenu"><div align="right">Modelo:</div></td>
                                  <td><input name="FilterModelo" id="FilterModelo" type="text" class="camporFormularioSimple" value="<?=$filter['Modelo']?>" maxlength="128"></td>
                                  <td><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                </tr>
                              </table></td>
                          </tr>
                        </table>
                    </div>
                    </div>				
                </td>
        </tr>
        <tr>
            <td>&nbsp;
            </td>
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
                    	<td width="505" height="25" class="bordeGrisTitulo"><div id="left"><strong>Modelo</strong></div></td>
                    	<td width="86" height="25"  class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                  	</tr>
          
                <?php foreach ($arrData as $oModeloPV) { ?>
          
               		<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                    	<td width="5" class="bordeGrisTitulo">&nbsp;</td>
                    	<td height="25"><div id="left"><?=$oModeloPV->Modelo?></div></td>
                    	<td width="86" height="25" valign="middle">
                            <div align="center">
                                <a href="modelospv_mod.php<?=$strParams?>&IdModeloPV=<?=$oModeloPV->IdModeloPV?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                                <a href="modelospv_del.php<?=$strParams?>&IdModeloPV=<?=$oModeloPV->IdModeloPV?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            </div>
                    	</td>
                	</tr>
                    <tr>
                        <td colspan="7"><div align="center">
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
        	<td>&nbsp;</td>
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
                </table>		</td>
        </tr>
          
    <?php } ?>
    
    </table>
</form>

</body>
</html>