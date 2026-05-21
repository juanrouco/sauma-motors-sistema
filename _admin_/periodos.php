<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PERIOD_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

$filter	= array();
$filter['FechaDesde'] 	= date('d-m-Y');
/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* agregamos filtro oculto para tipo de comprobante */
/* declaracion de variables */
$arrData 			= array();
$oPeriodos 		= new Periodos();
$oPage 				= new Page($Page, $PageSize);


$Paginado	= Pageable::PrintPaginator($oPage, $oPeriodos->GetCountRows($filter), true);
$arrData = $oPeriodos->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&filter=' 			. SendArray($filter);

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
	window.location.href = 'periodos.php';
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
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Periodos Contables</span></td>
                    </tr>
                </table>		
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
                        <td width="3" class="bordeGrisTitulo">&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nombre</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Inicio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Fin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
                        <td width="78" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPeriodo) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="3" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="537" height="25"><div id="margen"><?=$oPeriodo->Nombre?></div></td>
                        <td width="80" height="25"><div id="margen"><?=CambiarFecha($oPeriodo->FechaInicio)?></div></td>
                        <td width="80" height="25"><div id="margen"><?=CambiarFecha($oPeriodo->FechaFin)?></div></td>
                        <td width="100" height="25"><div id="margen"><?=($oPeriodo->Cerrado) ? 'Cerrado' : 'Abierto'?></div></td>
                        <td width="78" height="25" valign="middle">
                            <div id="margen" align="center"> 
						    <?php if (Session::CheckPerm(PERM_PERIOD_UPDATE)){ ?>
                                <a href="periodos_cerrar.php<?=$strParams?>&IdPeriodo=<?=$oPeriodo->IdPeriodo?>">
                                    <img src="images/iconos/permisos.gif" alt="Liberar" border="0" /></a>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
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