<?php 
require_once('../inc_library.php'); 
require_once('../library/class.tipopago.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_CAJADEFT_LIST))
	Session::NoPerm();

$filter			= ReceiveArray($_REQUEST['filter']);
$Page 			= intval($_REQUEST['Page']);
$PageSize 		= isset($_REQUEST['Submitted']) ? intval($_REQUEST['PageSize']) : 20;
$Action 		= strval($_REQUEST['MainAction']);
$Submit			= (isset($_REQUEST['Submitted']));
$header 		= "Listado Cajas Default";

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdTipoPago']	= trim($_REQUEST['FilterTipoPago']);
	$filter['IdUbicacion']	= trim($_REQUEST['FilterUbicacion']);
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";


$arrData 				= array();
$arrTiposPago			= array();
$arrUbicaciones			= array();
$oPage 					= new Page($Page, $PageSize);
$oCajasDetallesDefault	= new CajasDetallesDefault();
$oUbicaciones			= new Ubicaciones();
$oCajasDetalles			= new CajasDetalles();

$Paginado		= Pageable::PrintPaginator($oPage, $oCajasDetallesDefault->GetCountRows($filter), true);
$arrData 		= $oCajasDetallesDefault->GetAll($filter, $oPage);
$arrTiposPago	= TipoPago::GetAll();
$arrUbicaciones	= $oUbicaciones->GetAll();

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

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
	window.location.href = 'cajasdefault_listado.php?MainAction=<?=$Action?>';
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

$j(document).ready(function() {	
});

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
                        <td height="40"><span class="tituloPagina"><?= $header ?></span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
								<tr>
                                
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="cajasdefault_abm.php">Agregar</a></td>
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
											<td><div id="margen" align="right" class="tituloMenu">Tipo de Pago:</div></td>
											<td><div id="margen">
												<select id="FilterTipoPago" name="FilterTipoPago" class="camporFormularioSimple" value="<?=$filter['IdTipoPago']?>">
													<option value="" >Indistinto</option>
													<?php foreach($arrTiposPago as $rowTipoPago) { ?>
														<option value="<?=$rowTipoPago['IdTipoPago']?>" ><?=$rowTipoPago['Descripcion']?></option>
													<?php } ?>
												</select>
											</div></td>
											<td>&nbsp;</td>
											<td><div id="margen" align="right" class="tituloMenu">Sucursal:</div></td>
											<td><div id="margen">
												<select id="FilterUbicacion" name="FilterUbicacion" class="camporFormularioSimple" value="<?=$filter['IdUbicacion']?>">
													<option value="" >Indistinto</option>
													<?php foreach($arrUbicaciones as $oUbicacion) { ?>
														<option value="<?=$oUbicacion->IdUbicacion?>" ><?=$oUbicacion->Nombre?></option>
													<?php } ?>
												</select>
											</div></td>
											<td>&nbsp;</td>
											<td colspan="2" align="right">
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
						<td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo de Pago</strong></div></td>
						<td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sucursal</strong></div></td>
						<td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Caja Administraci&oacute;n</strong></div></td>
						<td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Caja Taller</strong></div></td>
						<td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Caja Repuestos</strong></div></td>                        
                        <td width="10%" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oCajaDetalleDefault) 
					{
						$oUbicacion 	= $oUbicaciones->GetById($oCajaDetalleDefault->IdUbicacion);
						$oCajaAdmin 	= $oCajasDetalles->GetById($oCajaDetalleDefault->IdCajaAdministracion);
						$oCajaTaller	= $oCajasDetalles->GetById($oCajaDetalleDefault->IdCajaTaller);
						$oCajaRepuestos = $oCajasDetalles->GetById($oCajaDetalleDefault->IdCajaRepuestos);
				?>          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=TipoPago::GetById($oCajaDetalleDefault->IdTipoPago)?></div></td>
                        <td height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>
                        <td height="25"><div id="margen"><?=$oCajaAdmin->Nombre?></div></td>
                        <td height="25"><div id="margen"><?=$oCajaTaller->Nombre?></div></td>
                        <td height="25"><div id="margen"><?=$oCajaRepuestos->Nombre?></div></td>
						<td height="25" valign="middle">
                            <div align="center">
                                <a href="cajasdefault_abm.php?IdTipoPago=<?=$oCajaDetalleDefault->IdTipoPago?>&IdUbicacion=<?=$oCajaDetalleDefault->IdUbicacion?>" title="Modificar">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9">
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
		<tr>
			<td>&nbsp;</td>
		</tr>
          
    <?php } ?>

    </table>
</form>
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>
