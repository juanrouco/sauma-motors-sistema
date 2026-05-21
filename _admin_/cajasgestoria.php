<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJGES_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);
$PageSize 			= intval($_REQUEST['PageSize']);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
	/*$filter['Nombre'] 	= trim($_REQUEST['FilterNombre']);
	$filter['Apellido'] = trim($_REQUEST['FilterApellido']);*/
	$filter['IdTipoMovimiento'] = trim($_REQUEST['FilterIdTipoMovimiento']);
	$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);
	$filter['IdMinuta'] 		= trim($_REQUEST['FilterIdMinuta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oCajasGestoria		= new CajasGestoria();
$oCuentasGestoria 	= new CuentasGestoria();
$oUsuarios		 	= new Usuarios();
$oPage 				= new Page($Page, $PageSize);

/* definimos cadena a mandar por get */
$strParams = '?';
$strParams.= '&IdMinuta=' 			. $IdMinuta;
$strParams.= '&Page='				. $Page;
$strParams.= '&PageSize='			. $PageSize;
$strParams.= '&filter=' 			. SendArray($filter);

/* obtenemos el listado de datos a mostrar */
$Paginado	= Pageable::PrintPaginator($oPage, $oCajasGestoria->GetCountRows($filter), true);
$arrData 	= $oCajasGestoria->GetAll($filter, $oPage);

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

	frmData.FilterNombre.value 		= '';
	frmData.FilterApellido.value 	= '';
	frmData.FilterEmail.value 		= '';

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

<form name="frmData" id="frmData" method="post" action="" onSubmit="Filtrar();">
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Caja de Gestor&iacute;a</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="40">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
											<?php if (Session::CheckPerm(PERM_CAJGES_CREATE)){ ?>
                                            <tr>
                                                <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                                <td><a href="cajasgestoria_add.php<?= $strParams ?>">Agregar</a></td>
                                            </tr>
											<?php } ?>
                                        </table>
                                    </td>
                                </tr>
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
                <div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-right: 10px; padding-left: 10px; padding-bottom: 10px; padding-top: 10px;">
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
                                <td width="50" class="tituloMenu">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>                                  
                                            <td width="8%" class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                            <td width="30%">
												<input name="FilterFechaDesde" type="text" class="camporFormularioMediano" id="FilterFechaDesde" value="<?=$filter['FechaDesde']?>" size="12" maxlength="12" />
												<script language="javascript">
												new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
												</script>
											</td>
                                            <td width="8%" class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                            <td width="27%">
												<input name="FilterFechaHasta" type="text" class="camporFormularioMediano" id="FilterFechaHasta" value="<?=$filter['FechaHasta']?>" size="12" maxlength="12" />
												<script language="javascript">
												new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
												</script>
											</td>	
                                            <td width="27%">&nbsp;</td>
                                            <td width="27%">&nbsp;</td>
                                        </tr>
                                        <tr>                                  
                                            <td class="tituloMenu"><div align="right">Tipo Movimiento:</div></td>
                                            <td>
												<select name="FilterIdTipoMovimiento" id="FilterIdTipoMovimiento" class="camporFormularioSimple">
													<option value="">Indistinto</option>
													<?php
													foreach (TiposMovimientosCaja::GetAll() as $oTipoMovimiento)
													{
														$selected = '';
														if ($oTipoMovimiento['IdTipo'] == $filter['IdTipoMovimiento'])
															$selected = 'selected="selected"';
													?>
													<option value="<?= $oTipoMovimiento['IdTipo'] ?>" <?= $selected ?>><?= $oTipoMovimiento['Descripcion'] ?></option>
													<?php
													}
													?>
												</select>
											</td>
                                             <td width="8%" class="tituloMenu"><div align="right">Nro. Interno:</div></td>
                                            <td width="27%">
												<input name="FilterIdMinuta" type="text" class="camporFormularioMediano" id="FilterIdMinuta" value="<?=$filter['IdMinuta']?>" />
											
											</td>	
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
                        <td width="288" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha</strong></div></td>
                        <td width="297" height="25" class="bordeGrisTitulo"><div id="margen" align="cemter"><strong>Tipo Movimiento</strong></div></td>
                        <td width="297" height="25" class="bordeGrisTitulo"><div id="margen" align="cemter"><strong>Interno</strong></div></td>
                        <td width="290" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Ingreso</strong></div></td>
                        <td width="290" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Egreso</strong></div></td>
                        <td width="290" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Saldo</strong></div></td>
                        <td width="100" class="bordeGrisTitulo"><div align="left"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oCajaGestoria) 
					{ 
						$Interno = '-';
						if ($oCajaGestoria->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente || $oCajaGestoria->IdTipoMovimiento == TiposMovimientosCaja::Rendicion)
						{
							$oCuentaGestoria = $oCuentasGestoria->GetById($oCajaGestoria->IdEntidad);
							$Interno = $oCuentaGestoria->IdMinuta;
						}
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oCajaGestoria->Fecha)?></div></td>
                        <td height="25"><div id="margen" align="left"><?=TiposMovimientosCaja::GetById($oCajaGestoria->IdTipoMovimiento) ?></div></td>
                        <td height="25"><div id="margen" align="left"><?= $Interno ?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= $oCajaGestoria->Monto > 0 ? number_format($oCajaGestoria->Monto, 2, ',', '.') : '0,00' ?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= $oCajaGestoria->Monto < 0 ? number_format($oCajaGestoria->Monto, 2, ',', '.') : '0,00' ?></div></td>
                        <td height="25"><div id="margen" align="left"><?=number_format($oCajaGestoria->Disponible, 2, ',', '.')?></div></td>
                        <td width="80" height="25"> 
                            <div align="left">
								<?php if (Session::CheckPerm(PERM_CAJGES_UPDATE) && !$oCajaGestoria->IdEntidad){ ?>
                                <a href="cajasgestoria_mod.php<?=$strParams?>&IdCajaGestoria=<?=$oCajaGestoria->IdCajaGestoria?>"><img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                                <?php } ?>
								<?php if (Session::CheckPerm(PERM_CAJGES_DELETE) && !$oCajaGestoria->IdEntidad){ ?>
								<a href="cajasgestoria_del.php<?=$strParams?>&IdCajaGestoria=<?=$oCajaGestoria->IdCajaGestoria?>"><img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
								<?php } ?>
							</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
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