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
$PageSize 	= isset($_REQUEST['Submitted']) ? intval($_REQUEST['PageSize']) : 500;
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
	$filter['FechaMinutaDesde'] = trim($_REQUEST['FilterFechaMinutaDesde']);
	$filter['FechaMinutaHasta'] = trim($_REQUEST['FilterFechaMinutaHasta']);
	$filter['NumeroPedido'] 	= trim($_REQUEST['FilterNumeroPedido']);
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
$oFacturaUnidades 	= new FacturaUnidades();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oMinutas->GetCountRowsPatentables($filter), true);
$arrData 	= $oMinutas->GetAllPatentables($filter, $oPage);

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


    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Gestoria - Selecci&oacute;n de Minutas</span></td>
                    </tr>
                </table>		
            </td>
        </tr>

	  	
        <tr>
        	<td>&nbsp;</td>
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
					
				<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
					<input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
					<input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
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
                                        <td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
					</form>
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
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;">
                    <tr>
                        <td>&nbsp;</td>
                        <td><span><strong>Seleccione la operaci&oacute;n que desea procesar.</span>
                    </td>
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
				<form name="frmData1" id="frmData1" method="post" action="cuentasgestoria_add.php">
					<input type="hidden" id="MainAction" name="MainAction" value="Select" />
					<input type="hidden" id="Submitted" name="Submitted" value="1" />
				<table width="100%" align="center" cellpadding="0" cellspacing="0" class="borderGris">
					<tr>
						<td width="33%" align="center">&nbsp;</td>
						<td width="34%" align="center">&nbsp;</td>
						<td width="33%" align="right"><input type="submit" name="button1" id="button1" class="botonBasico" style="float: right" value="Confirmar"></td>
					</tr>
				</table>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Venta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vendedor</strong></div></td>
                        <td width="103" height="25" class="bordeGrisTitulo">&nbsp;</td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oMinuta) 
					{ 
						$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario);
						$cliente = $oCliente->RazonSocial;
						$oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oMinuta->IdMinuta);
						if ($oMinuta->Condominio)
						{
							$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
							$cliente.= " / " . $oClienteCondominio->RazonSocial;
						}
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="75" height="25"><div id="margen" align="center"><?=$oMinuta->IdMinuta?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
                        <td width="200" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="170" height="25"><div id="margen"><?=$cliente?></div></td>
                        <td width="180" height="25"><div id="margen"><?=$oUsuario->Nombre . ', ' . $oUsuario->Apellido?></div></td>
                        <td width="84" height="25" valign="middle">
                            <div align="center">
								<input class="check-change" type="checkbox" id="IdMinuta[]" name="IdMinuta[]" value="<?= $oMinuta->IdMinuta ?>" />
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
				</form>
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

</body>
</html>