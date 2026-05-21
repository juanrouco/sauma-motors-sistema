<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_REPF_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action		= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaFacturaDesde'] 	= trim($_REQUEST['FechaFacturaDesde']);
	$filter['FechaFacturaHasta'] 	= trim($_REQUEST['FechaFacturaHasta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oUnidades 				= new Unidades();
$oPage 					= new Page($Page, $PageSize);

if ($Submit)
{
	$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);
	$arrData 	= $oUnidades->GetAll($filter, $oPage);
}

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
	window.location.href = 'reportesfacturacion.php';
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

function Generate()
{
	var frmData = Get('frmData');
	var Action = Get('MainAction');
	
	if (frmData == undefined)
		return false;
		
	Action.value = 'Generate';
	frmData.submit();
	return true;
}

</script>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="reportesfacturacion_reporte_pdf.php">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Reportes de Facturaci&oacute;n - Reporte</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
      <tr>
            <td height="30" valign="top">
                &nbsp;	
            </td>
        </tr>
       
        <tr>
            <td height="30" valign="top">
                <!-- Aca van los filtros -->				
                <div id="FilterMain" class="">
                <div id="Filter" >		
                    <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						
						<tr>
                            <td class="tituloMenu">&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Fecha Facturaci&oacute;n Desde:</div></td>
                                        <td><input name="FechaFacturaDesde" id="FechaFacturaDesde" type="text" class="camporFormularioSimple" value="<?=$filter['FechaFacturaDesde']?>" size="12" maxlength="12" />
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaFacturaDesde'});
                                                </script>
										</td>
                                        <td width="30">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Facturaci&oacute;n Hasta:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="FechaFacturaHasta" type="text" class="camporFormularioMediano" id="FechaFacturaHasta" value="<?=$filter['FechaFacturaHasta']?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaFacturaHasta'});
                                                </script>
                                            </div>
                                        </td>
                                        <td width="30"Ç>&nbsp;</td>
                                        <td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Generar Reporte"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
						<tr>
                            <td class="tituloMenu">&nbsp;
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
          
    
    </table>
</form>

</body>
</html>