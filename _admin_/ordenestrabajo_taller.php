<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_TALLER))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['Fecha'] 				= trim($_REQUEST['FilterFecha']);
	$filter['FechaInicio']			= trim($_REQUEST['FilterFechaInicio']);
	$filter['FechaSalida'] 			= trim($_REQUEST['FilterFechaSalida']);	
	$filter['Dominio'] 				= trim($_REQUEST['FilterDominio']);	
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['IdEstadoOrden'] 		= EstadoOrden::Aceptada;
$filter['IdUsuarioAsignado'] 	= Session::GetCurrentUser()->IdUsuario;

$arrData 				= array();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenTrabajoHitos		= new OrdenTrabajoHitos();
$oModelos 				= new Modelos();
$oTallerUnidades		= new TallerUnidades();
$oUsuarios				= new Usuarios();
$oEstadosOrden			= new EstadosOrden();
$oClientes				= new Clientes();

$oPage 					= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oOrdenesTrabajo->GetCountRows($filter), true);
$arrData 	= $oOrdenesTrabajo->GetAll($filter, $oPage);

$arrEstadosOrden = $oEstadosOrden->GetAll();
$filterUsuarios = array();
$filterUsuarios['IdPerfil'] = Usuario::Taller;
$arrUsuarios = $oUsuarios->GetAll($filterUsuarios);

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrModelos 		= $oModelos->GetAll();

IncludeSUGGEST();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

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
	window.location.href = 'tareastrabajo.php?MainAction=<?=$Action?>';
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Ordenes de Trabajo</span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>                                   
									<td width="10" height="30" >&nbsp;</td>
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
                                        <td class="tituloMenu"><div align="right">Fecha:</div></td>
                                        <td>
                                        	<input name="FilterFecha" id="FilterFecha" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['Fecha']?>" readonly="readonly" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFecha'});
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Ingreso:</div></td>
										<td>
											<input name="FilterFechaInicio" id="FilterFechaInicio" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaInicio']?>" readonly="readonly" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaInicio'});
                                            </script>
										</td>
                                        <td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Fecha Salida:</div></td>
										<td>
											<input name="FilterFechaSalida" id="FilterFechaSalida" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaSalida']?>" readonly="readonly" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaSalida'});
                                            </script>
										</td>
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu"><div align="right">Dominio:</div></td>
                                        <td>
											<input type="text" name="FilterDominio" id="FilterDominio"  class="camporFormularioSimple" value="<?= $filter['Dominio'] ?>">												
										</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. OT</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Asignado</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ingreso</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Salida</strong></div></td>                        
						<td width="100" height="25"  class="bordeGrisTitulo"><div id="margen" align="center" ><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oOrdenTrabajo) 
					{
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						$oEstadoOrden = $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
						$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
						
						$Usuario = '';
						if ($oOrdenTrabajo->IdUsuarioAsignado)
						{
							$oUsuario = $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
							$Usuario = $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
						}
						
						$oOrdenTrabajoHito = $oOrdenTrabajoHitos->GetLastByIdOrdenTrabajoAndIdUsuario($oOrdenTrabajo->IdOrdenTrabajo, $oUsuario->IdUsuario);
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
						<td width="100" height="25"><div id="margen" align="center"><?=$oOrdenTrabajo->IdOrdenTrabajo?></div></td>
                        <td width="100" height="25"><div id="margen"><?=$oTallerUnidad->Dominio?></div></td>
						<td width="150" height="25"><div id="margen"><?=$oTallerUnidad->Modelo?></div></td>
						<td width="150" height="25"><div id="margen"><?=$Usuario?></div></td>
						<td width="100" height="25"><div id="margen"><?=CambiarFecha($oOrdenTrabajo->FechaInicio)?></div></td>
						<td width="100" height="25"><div id="margen"><?=CambiarFecha($oOrdenTrabajo->FechaFin)?></div></td>
						<td width="120" height="25" valign="middle">
                            <div id="margen" align="center">
								<a href="ordenestrabajo_taller_detail.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" title="Detalle">
                                    <img src="images/iconos/preview.png" alt="Detalle" border="0" /></a> -
								<a href="ordenestrabajo_comentarios_add.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" title="Agregar comentarios">
                                    <img src="images/iconos/ordenes.png" alt="Detalle" border="0" /></a> -
								<?php
								if (!$oOrdenTrabajoHito || $oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Detener)
								{
								?>
								<a href="ordenestrabajo_taller_iniciar.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" title="Iniciar">
                                    <img src="images/iconos/control_play.png" alt="Iniciar" border="0" /></a>
								<?php
								}
								else
								{
								?>
								<a href="ordenestrabajo_taller_detener.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" title="Detener">
                                    <img src="images/iconos/control_pause.png" alt="Detener" border="0" /></a> - <a href="ordenestrabajo_taller_finalizar.php<?=$strParams?>&IdOrdenTrabajo=<?=$oOrdenTrabajo->IdOrdenTrabajo?>" title="Finalizar">
                                    <img src="images/iconos/control_stop.png" alt="Finalizar" border="0" /></a>
								<?php
								}
								?>
                            </div>
                        </td>
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
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>