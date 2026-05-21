<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
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
	$filter['IdMinuta'] 		= trim($_REQUEST['FilterIdMinuta']);
	$filter['IdUnidad'] 		= trim($_REQUEST['FilterIdUnidad']);
	$filter['NumeroVin'] 		= trim($_REQUEST['FilterNumeroVin']);
	$filter['Cliente'] 			= trim($_REQUEST['FilterCliente']);
	$filter['Usuario'] 			= trim($_REQUEST['FilterUsuario']);
	$filter['Reventa'] 			= trim($_REQUEST['FilterReventa']);
	$filter['FechaMinutaDesde'] = trim($_REQUEST['FilterFechaMinutaDesde']);
	$filter['FechaMinutaHasta'] = trim($_REQUEST['FilterFechaMinutaHasta']);
	$filter['NumeroPedido'] 	= trim($_REQUEST['FilterNumeroPedido']);
	$filter['IdOrigenCliente'] 	= trim($_REQUEST['FilterIdOrigenCliente']);
	$filter['Modelo'] 			= trim($_REQUEST['FilterModelo']);
}

if ($currentUser->IdPerfil == Perfil::Vendedor && false)
{
		$filter['IdUsuario'] = $currentUser->IdUsuario;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oMinutas 			= new Minutas();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oGestorias			= new Gestorias();
$oEstadosUnidad		= new EstadosUnidad();
$oUbicaciones		= new Ubicaciones();
$oFacturaUndiades 	= new FacturaUnidades();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oMinutas->GetCountRows($filter), true);
$arrData 	= $oMinutas->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&Select=' 	. (isset($_REQUEST['Select'])) ? 1 : '';
$strParams.= '&IdMinuta=' 	. (isset($_REQUEST['IdMinuta'])) ? $_REQUEST['IdMinuta'] : '';
$strParams.= '&filter=' 	. SendArray($filter);

/* armamos cadena con parametros a mandar para armado de minuta */
$strParamsSelect = '';
$strParamsSelect.= '?filter=' . SendArray($filter);

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
	window.location.href = 'minutas.php?MainAction=<?=$Action?>';
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas</span></td>
                    </tr>
                </table>		
            </td>
        </tr>

	  	<?php if (($Action != 'Select') && ($Action != 'SelectFacturacion')) { ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_VENT_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="unidades.php<?=$strParamsSelect?>&MainAction=Select">Agregar</a></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
						<td width="52%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td><a href="minutas_entrega_exportar_pdf.php<?=$strParams?>">Exportar Planilla</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="20%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
								<?php if (Session::CheckPerm(PERM_VENT_REPORT)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td><a href="minutas_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
                                </tr>
								<?php } ?>
                            </table>
                        </td>
                        <td width="20%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
								<?php if (Session::CheckPerm(PERM_VENT_REPORT)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="minutas_exportar.php<?=$strParams?>">Exportar XLS</a></td>
                                </tr>
								<?php } ?>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php } else { ?>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <?php } ?>
        
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
                                        <td class="tituloMenu"><div align="right">Fecha Minuta Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaMinutaDesde" type="text" class="camporFormularioMediano" id="FilterFechaMinutaDesde" value="<?=$filter['FechaMinutaDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaMinutaDesde'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Minuta Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaMinutaHasta" type="text" class="camporFormularioMediano" id="FilterFechaMinutaHasta" value="<?=$filter['FechaMinutaHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaMinutaHasta'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Carpeta:</div></td>
                                        <td><input name="FilterIdMinuta" id="FilterIdMinuta" type="text" class="camporFormularioSimple" value="<?=$filter['IdMinuta']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">N&uacute;mero Interno:</div></td>
                                        <td><input name="FilterIdUnidad" id="FilterIdUnidad" type="text" class="camporFormularioSimple" value="<?=$filter['IdUnidad']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVin" id="FilterNumeroVin" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVin']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off">
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAll', 'FilterNumeroVin', 'SetNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
                                            </script>
                                       	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Pedido:</div></td>
                                        <td>
                                        	<input name="FilterNumeroPedido" id="FilterNumeroPedido" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroPedido']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off">
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
										<?php
										if ($currentUser->IdPerfil != 2)
										{
										?>
                                        <td class="tituloMenu"><div align="right">Vendedor:</div></td>
                                        <td>
                                        	<input name="FilterUsuario" id="FilterUsuario" type="text" class="camporFormularioSimple" value="<?=$filter['Usuario']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" />
                                            <script language="javascript">
											var arrParams = new Array();
											arrParams['FilterIdPerfil'] = '<?=Usuario::Vendedor?>';
                                            SUGGESTRequest('Usuarios', 'GetAllSuggest', 'FilterUsuario', 'SetUsuario', 'IdUsuario', 'Nombre', 'FilterNombre', arrParams);
											</script>
                                        </td>
										<?php
										}
										else
										{
										?>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php
										}
										?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
									 <tr>                              
                                        <td class="tituloMenu"><div align="right">Reventa:</div></td>
                                        <td>
                                        	<input name="FilterReventa" id="FilterReventa" type="text" class="camporFormularioSimple" value="<?=$filter['Reventa']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" />
                                           
                                        </td>
                                        <td>&nbsp;</td>             
                                        <td class="tituloMenu"><div align="right">Origen de Cliente:</div></td>
                                        <td>
                                        	 <select name="FilterIdOrigenCliente" class="camporFormularioSimple" id="FilterIdOrigenCliente">
												<option value="">[Indistinto]</option>
												<?php
												foreach (OrigenesCliente::GetAll() as $oOrigenCliente)
												{
													$selected = '';
													if ($oOrigenCliente['IdOrigenCliente'] == $filter['IdOrigenCliente'])
														$selected = 'selected="selected"';
												?>
												<option value="<?= $oOrigenCliente['IdOrigenCliente'] ?>" <?= $selected ?>><?= $oOrigenCliente['Nombre'] ?></option>
												<?php
												}
												?>
												</select>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
									 <tr>                              
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
                                        <td>
                                        	<input name="FilterModelo" id="FilterModelo" type="text" class="camporFormularioSimple" value="<?=$filter['Modelo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" />
                                           
                                        </td>
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
            
	  	<?php if (($Action == 'Select') || ($Action == 'SelectFacturacion')) { ?>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;">
                    <tr>
                        <td>&nbsp;</td>
                        <td><span><strong>Seleccione la operaci&oacute;n que desea procesar. Para ello haga clic sobre el s&iacute;mbolo </strong></span> <img src="images/iconos/preview.png" width="16" height="16" border="0" /> <span><strong>de la minuta correspondiente.</strong></span>
                    </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php } ?>

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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Minuta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Vin</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vendedor</strong></div></td>
                        <td width="103" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oMinuta) 
					{ 
						$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
						$oEstado = $oEstadosUnidad->GetById($oUnidad->IdEstado);
						$oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario);
						$cliente = $oCliente->RazonSocial;
						if ($oMinuta->Condominio)
						{
							$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
							$cliente.= " / " . $oClienteCondominio->RazonSocial;
						}
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="75" height="25"><div id="margen" align="center"><?=$oMinuta->IdMinuta?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
                        <td width="64" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
						<td width="64" height="25"><div id="margen"><?=$oEstado->Nombre?></div></td>
						<td width="64" height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>
                        <td width="168" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="134" height="25"><div id="margen"><?=$cliente?></div></td>
                        <td width="153" height="25"><div id="margen"><?=$oUsuario->Nombre . ', ' . $oUsuario->Apellido?></div></td>
                        <td width="103" height="25" valign="middle">
                            <div align="center">
	                     	<?php 
								if (($Action == 'Select') || ($Action == 'SelectFacturacion')) 
								{
									$oGestoria = $oGestorias->GetByMinuta($oMinuta);
									
									if (($Action == 'Select') || ($Action == 'SelectFacturacion' && $oUnidad->IdEstado != EstadoUnidad::Facturado)) 
									{ 
										if ($Target != 'Formulario' || !$oGestoria)
										{
							?>
                                <a href="#" onClick="javascript: Select('<?=$oMinuta->IdMinuta?>');">
                                    <img src="images/iconos/preview.png" alt="Seleccionar" border="0" /></a>
                            <?php 	
										}
									} 
								} 
								else 
								{ 
							?>
								<a href="minutas_detail.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/preview.png" alt="Detalle" title="Detalle" border="0" /></a> - 
                                <a href="minutas_pdf.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>" target="_blank">
                                    <img src="images/iconos/pdf.png" alt="Generar Comprobante" border="0" /></a> - 
								<?php if (Session::CheckPerm(PERM_VENT_UPDATE)){ ?>
        	                    <a href="minutas_mod.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a> - 
								<?php } ?>
                	            <a href="cuentascorriente_detail.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/calculadora.png" alt="Cuenta Corriente" title="Cuenta Corriente" border="0" /></a> - 
                	            <?php if (Session::CheckPerm(PERM_VENT_DELETE)){ ?>
								<?php if ($oMinuta->CanDelete()) { ?>
                                <a href="minutas_del.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" border="0" /></a>
								<?php } else { ?>
								<a href="minutas_anular.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/permisos.gif" alt="Anular" title="Anular" border="0" /></a>
                    	        <?php } ?>
                    	        <?php } ?>
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