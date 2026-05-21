<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_REPF_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdReporteFacturacion	= intval($_REQUEST['IdReporteFacturacion']);
$filter					= ReceiveArray($_REQUEST['filter']);
$Page 					= intval($_REQUEST['Page']);
$PageSize 				= intval($_REQUEST['PageSize']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oColores				= new Colores();
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oReportesFacturacion	= new ReportesFacturacion();
$oFacturaUnidades		= new FacturaUnidades();
$oComprobantes 			= new Comprobantes();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oPage 					= new Page($Page, $PageSize);

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe la planilla */
if (!$oReporteFacturacion = $oReportesFacturacion->GetById($IdReporteFacturacion))
{	
	header("Location: reportesfacturacion.php" . $strParams);
	exit();
}

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVin'] = trim($_REQUEST['FilterNumeroVin']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* asigamos filtro de planilla */
$filter['IdReporteFacturacion'] = $IdReporteFacturacion;

$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);
$arrData 	= $oUnidades->GetAll($filter, $oPage);

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
	window.location.href = 'reportesfacturacion_details.php?IdReporteFacturacion=<?=$IdReporteFacturacion?>';
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

function SetNumeroVin(IdUnidad, NumeroVin)
{	
	Get('FilterNumeroVin').value = NumeroVin;
}

function UpdateUnidad(IdUnidad)
{
	var obj;
	var arr = new Array();
	var Lavado = Get('Lavado_' + IdUnidad);
	
	arr['IdUnidad'] = IdUnidad;
	arr['Lavado'] = (Lavado.checked) ? '1' : '0';
	
	obj = SendXMLRequest('Unidades', 'UpdateChecks', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	return true;
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdReporteFacturacion" id="IdReporteFacturacion" value="<?=$IdReporteFacturacion?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Reportes de Facturaci&oacute;n - Unidades</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td height="30" valign="top" align="center">
                <!-- Aca van los filtros -->				
                <div id="ShownFilter" class="bordeGrisFondo" style="<?=$filterMostrar;?> padding-top: 10px; padding-bottom: 10px;" align="center">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div id="HiddenFilter" style="<?=$filterStyle;?> padding-top: 10px; padding-bottom: 10px;" class="bordeGrisFondo" align="center">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
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
                <div id="Filter" align="center">		
                    <table border="0" class="bordeGrisFondo" align="center" cellpadding="2" cellspacing="2" width="100%">
                        <tr>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVin" id="FilterNumeroVin" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVin']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAll', 'FilterNumeroVin', 'SetNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
                                            </script>
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
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
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
                        <td width="92" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Llave</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Datos de Facturaci&oacute;n</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) { ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
                    <?php $oColor = $oColores->GetById($oUnidad->IdColor); ?>
                    <?php $oMinuta = $oMinutas->GetByUnidad($oUnidad); ?>
					<?php $oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion); ?>
                    <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
                    <?php $oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oMinuta->IdMinuta); ?>
                    <?php $oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante); ?>
                    <?php $Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oComprobante->Numero; ?>
					<?php $CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : ''; ?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="92" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="176" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="131" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="78" height="25"><div id="margen"><?=$CodigoLlaves?></div></td>
                        <td width="92" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="138" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="204" height="25">
                        	<div id="margen">
                            	<table width="100%" cellpadding="0" cellspacing="0">
                                	<tr>
                                    	<td width="45%"><span><strong>Fecha:</strong></span></td>
                                        <td width="55%"><?=CambiarFecha($oFacturaUnidad->Fecha)?></td>
                                    </tr>
                                	<tr>
                                    	<td><span><strong>Comprobante:</strong></span></td>
                                        <td><?=$Comprobante?></td>
                                    </tr>
                                	<tr>
                                    	<td><span><strong>Importe:</strong></span></td>
                                        <td>$ <?=number_format($oFacturaUnidad->Total, 2)?></td>
                                    </tr>
                                </table>
                         	</div>
                      	</td>
                    </tr>
                    <tr>
                        <td colspan="8">
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
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
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
                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="97%" height="30">
                            <div align="right">
                            	<input type="button" name="btnVolver" class="botonBasico" id="btnVolver" value="Volver a Reportes" onClick="javascript: window.location.href='reportesfacturacion.php<?=$strParams?>'" />
                            </div>
                        </td>
                        <td width="3%">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>La recepci&oacute;n no posee unidades cargadas.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
          
    <?php } ?>
    
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>