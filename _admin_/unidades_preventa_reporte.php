<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
	$filter['FechaArriboDesde'] = trim($_REQUEST['FilterFechaArriboDesde']);
	$filter['FechaArriboHasta'] = trim($_REQUEST['FilterFechaArriboHasta']);
	$filter['IdEstado'] 		= array();
	$filter['IdEstado'][0] 		= EstadoUnidad::PreVenta;
	$filter['IdEstado'][1] 		= EstadoUnidad::PreVentaReservado;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oColores				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oEstadosUnidad 		= new EstadosUnidad();
$oPage 					= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);
$arrData 	= $oUnidades->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&FilterModelo=' 		. $filter['IdModelo'];
$strParams.= '&FilterUbicacion=' 	. $filter['IdUbicacion'];
$strParams.= '&FilterFechaArriboDesde=' 	. $filter['FechaArriboDesde'];
$strParams.= '&FilterFechaArriboHasta=' 	. $filter['FechaArriboHasta'];

if (Session::CheckPerm(PERM_UNID_CREATE))
	$strParams.= '&fullpermisos=1';

$arrModelos 		= $oModelos->GetAllOrdered();
$arrUbicaciones 	= $oUbicaciones->GetAll();
$arrEstadosUnidad 	= $oEstadosUnidad->GetAll();

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

function ClearFilter()
{	
	window.location.href = 'unidades.php?MainAction=<?=$Action?>';
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

function SetNumeroVin(IdUnidad, NumeroVin)
{
	Get('FilterNumeroVin').value = NumeroVin;
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
                        <td height="40"><span class="tituloPagina">Reporte de Unidades de Preventa</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="30" valign="top">
				<table border="0" align="right" cellpadding="0" cellspacing="0">
					<tr>
						<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
						<td><a href="unidades_preventa_reporte_exportar.php<?=$strParams?>">Exportar XLS</a></td>
					</tr>
				</table>
			</td>
		</tr>
        <tr>
            <td height="30" valign="top">                
                <div id="FilterMain" class="">
                <div id="Filter" >		
                    <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td><span class="tituloPagina">Filtro</span></td>
						</tr>
                        <tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td class="tituloMenu">
											<div align="right">Modelo:</div>
										</td>
                                        <td>
											<select name="FilterModelo" id="FilterModelo"  class="camporFormularioSimple">
												<option value="">INDISTINTO</option>
												<?php 
												foreach ($arrModelos as $oModelo) 
												{
												?>
												<option value="<?=$oModelo->IdModelo?>" <? if ($oModelo->IdModelo == $filter['IdModelo']) echo "selected='selected'"; ?> ><?=$oModelo->DenominacionComercial?></option>
												<?php
												}
												?>
											</select>
										</td>
										<td width="10">&nbsp;</td>
                                        <td class="tituloMenu">
											<div align="right">Ubicaci&oacute;n:</div>
										</td>
                                        <td>
											<select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioSimple">
											<option value="" >INDISTINTO</option>
											<?php 
											foreach ($arrUbicaciones as $oUbicacion) 
											{
											?>
											<option value="<?=$oUbicacion->IdUbicacion?>" <? if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
											<?php
											}
											?>
											</select>
										</td>
										<td width="10">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
										<td class="tituloMenu">
											<div align="right">Desde Fecha de Arribo:</div>
										</td>
                                        <td>
											<input type="text" name="FilterFechaArriboDesde" id="FilterFechaArriboDesde"  class="camporFormularioSimple" value="<?= $filter['FechaArriboDesde'] ?>" size="12" maxlength="12" />
											<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FilterFechaArriboDesde'});
											</script>
										</td>
										<td width="10">&nbsp;</td>
                                        <td class="tituloMenu">
											<div align="right">Hasta Fecha de Arribo:</div>
										</td>
                                        <td>
											<input type="text" name="FilterFechaArriboHasta" id="FilterFechaArriboHasta"  class="camporFormularioSimple" value="<?= $filter['FechaArriboHasta'] ?>" size="12" maxlength="12" />
											<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FilterFechaArriboHasta'});
											</script>
										</td>
										<td width="10">&nbsp;</td>
                                        <td>
											<input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
										</td>
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
                        <td>&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>                        
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Pedido</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>						
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>A&ntilde;o</strong></div></td>												
						<td width="71" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estima Arribo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) 
				{
                    $oModelo = $oModelos->GetById($oUnidad->IdModelo);
                    $oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
                    $oEstadoUnidad = $oEstadosUnidad->GetById($oUnidad->IdEstado);
					$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
					$oColor	= $oColores->GetById($oUnidad->IdColor);
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td bgcolor="<?=$oEstadoUnidad->Color?>" width="7">&nbsp;</td>
                        <td width="96" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>                        
                        <td width="81" height="25"><div id="margen"><?=$oUnidad->NumeroPedido?></div></td>
                        <td width="261" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						<td width="150" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="128" height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>						
                        <td width="71" height="25"><div id="margen"><?=$oUnidad->Anio?></div></td>						
						<td width="71" height="25"><div id="margen"><?=CambiarFecha($oUnidad->FechaArriboEstimada)?></div></td>
						<td width="71" height="25"><div id="margen"><?=$oEstadoUnidad->Nombre?></div></td>						
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