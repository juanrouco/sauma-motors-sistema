<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PRESUP_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= isset($_REQUEST['Submitted']) ? intval($_REQUEST['PageSize']) : 20;
$Action 	= strval($_REQUEST['MainAction']);
$Target		= $_REQUEST['Target'];
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['Modelo'] 			= trim($_REQUEST['FilterModelo']);
	$filter['Cliente'] 			= trim($_REQUEST['FilterCliente']);
	$filter['Usuario'] 			= trim($_REQUEST['FilterUsuario']);
	$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);
	$filter['IdEstado'] 		= trim($_REQUEST['FilterIdEstado']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oPresupuestos		= new Presupuestos();
$oModelos 			= new Modelos();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oPage 				= new Page($Page, $PageSize);

$oPresupuestos->ActualizarEstados();

$Paginado	= Pageable::PrintPaginator($oPage, $oPresupuestos->GetCountRows($filter), true);
$arrData 	= $oPresupuestos->GetAllOrdered($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&Select=' 	. (isset($_REQUEST['Select'])) ? 1 : '';
$strParams.= '&IdPresupuesto=' 	. (isset($_REQUEST['IdPresupuesto'])) ? $_REQUEST['IdPresupuesto'] : '';
$strParams.= '&filter=' 	. SendArray($filter);

/* armamos cadena con parametros a mandar para armado de minuta */
$strParamsSelect = '';
$strParamsSelect.= '?filter=' . SendArray($filter);

/* obtenemos listados para armar los filtros */
$arrClientes = $oClientes->GetAll();
$arrUsuarios = $oUsuarios->GetAll();

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
	window.location.href = 'presupuestos.php?MainAction=<?=$Action?>';
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

function SetUsuario(IdUsuario, Nombre)
{
	var oUsuario = GetUsuario(IdUsuario);

	if (!(oUsuario))
		return;

	Get('FilterUsuario').value = (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function SetCliente(IdCliente, RazonSocial)
{
	Get('FilterCliente').value = RazonSocial;
}

function Select(IdMinuta)
{
	window.opener.SetMinuta(IdMinuta);
	window.close();
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
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Tareas</span></td>
                    </tr>
                </table>		
            </td>
        </tr>

        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_PRESUP_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="presupuestos_add.php">Agregar</a></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="80%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <td width="12%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30">&nbsp;</td>
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
                                        <td class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaDesde" type="text" class="camporFormularioMediano" id="FilterFechaDesde" value="<?=$filter['FechaDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaHasta" type="text" class="camporFormularioMediano" id="FilterFechaHasta" value="<?=$filter['FechaHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
										<td><input name="FilterModelo" id="FilterModelo" type="text" class="camporFormularioSimple" value="<?=$filter['Modelo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td>
                                        	<input name="FilterCliente" id="FilterCliente" type="text" class="camporFormularioSimple" value="<?=$filter['Cliente']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" />
                                            <script language="">
                                            SUGGESTRequest('Clientes', 'GetAll', 'FilterCliente', 'SetCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                            </script>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Vendedor:</div></td>
                                        <td>
                                        	<input name="FilterUsuario" id="FilterUsuario" type="text" class="camporFormularioSimple" value="<?=$filter['Usuario']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" />
                                            <script language="javascript">
											var arrParams = new Array();
											arrParams['FilterIdPerfil'] = '<?=Usuario::Vendedor?>';
                                            SUGGESTRequest('Usuarios', 'GetAllSuggest', 'FilterUsuario', 'SetUsuario', 'IdUsuario', 'Nombre', 'FilterNombre', arrParams);
											</script>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td>
											<select id="FilterIdEstado" name="FilterIdEstado" class="camporFormularioSimple">
												<option value="">Indistinto</option>
												<?php 
												foreach (PresupuestoEstados::GetAll() as $oPresupuestoEstado) 
												{ 
													$selected = '';
													if ($oPresupuestoEstado['IdEstado'] == $filter['IdEstado'])
														$selected = 'selected="selected"';
												?>
												<option value="<?= $oPresupuestoEstado['IdEstado'] ?>" <?= $selected ?>><?= $oPresupuestoEstado['Descripcion'] ?></option>
												<?php 
												}?>
											</select>
										</td>
                                    </tr>
									<tr>                              
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
					<td align="center">
                    	<table width="100%" cellpadding="0" cellspacing="0">
                        	<tr>
								<?php foreach (PresupuestoEstados::GetAll() as $oPresupuestoEstado) { ?>
                                <td bgcolor="<?=PresupuestoEstados::GetOnlyColorById($oPresupuestoEstado['IdEstado'])?>" width="40" height="20"><div align="left"></div></td>
                                <td width="40">&nbsp;</td>
                                <td width="270"><div align="left"><?=$oPresupuestoEstado['Descripcion']?></div></td>
                                <?php } ?>
							</tr>
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
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
						<td width="25" height="10" ></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Presupuesto</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Precio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Vencimiento</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vendedor</strong></div></td>
                        <td width="103" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oPresupuesto) 
					{ 
						$oModelo = $oModelos->GetById($oPresupuesto->IdModelo);
						$oCliente = $oClientes->GetById($oPresupuesto->IdCliente);
						$oUsuario = $oUsuarios->GetById($oPresupuesto->IdUsuario);
						$cliente = $oCliente->RazonSocial;
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
						<td bgcolor="<?=PresupuestoEstados::GetOnlyColorById($oPresupuesto->IdEstado)?>"></td>
                        <td width="75" height="25"><div id="margen" align="center"><?=$oPresupuesto->IdPresupuesto?></div></td>
						 <td width="134" height="25"><div id="margen"><?=$cliente?></div></td>
						 <td width="168" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						 <td width="79" height="25"><div id="margen" align="center"><?=number_format($oPresupuesto->Precio, 2)?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oPresupuesto->Fecha)?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oPresupuesto->FechaVencimiento)?></div></td>
                        <td width="79" height="25"><div id="margen"><?=PresupuestoEstados::GetColorById($oPresupuesto->IdEstado)?></div></td>
                        <td width="153" height="25"><div id="margen"><?=$oUsuario->Nombre . ', ' . $oUsuario->Apellido?></div></td>
                        <td width="103" height="25" valign="middle">
                            <div align="center">
								<?php if (Session::CheckPerm(PERM_TAREAS_UPDATE)){ ?>
								<a href="tareas_descripcion.php?IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>" target="_blank" onClick="window.open(this.href, this.target, 'width=1000,height=1000'); return false;" class="linkMenu"><img alt="Detalles" title="Detalles" src="images/iconos/preview.png" width="16" height="16" border="0"></a> -
							
								
								<?php } ?>
								<?php
									if (Session::CheckPerm(PERM_VENT_CREATE)) {
								?>
									<a href="unidades.php<?=$strParamsSelect?>&MainAction=Select&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/facturacion.png" alt="Generar Minuta" border="0" /></a> - 
                                <?php 
									}
									if (Session::CheckPerm(PERM_PRESUP_UPDATE)){ ?>
                                <a href="presupuestos_mod.php<?=$strParams?>&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a>
            	                <?php } ?>
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

</body>
</html>