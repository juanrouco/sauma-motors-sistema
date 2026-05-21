<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CUECOR_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= isset($_REQUEST['Submitted']) ? intval($_REQUEST['PageSize']) : 20;
$Action 	= strval($_REQUEST['MainAction']);
$Target		= $_REQUEST['Target'];
$IdCliente	= $_REQUEST['IdCliente'];
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdMinuta'] 		= trim($_REQUEST['FilterIdMinuta']);
	$filter['IdUsado'] 			= trim($_REQUEST['FilterIdUsado']);
	$filter['Dominio'] 			= trim($_REQUEST['FilterDominio']);
	$filter['Reventa'] 			= trim($_REQUEST['FilterReventa']);
	$filter['Cliente'] 			= trim($_REQUEST['FilterCliente']);
	$filter['Usuario'] 			= trim($_REQUEST['FilterUsuario']);
	$filter['FechaMinutaDesde'] = trim($_REQUEST['FilterFechaMinutaDesde']);
	$filter['FechaMinutaHasta'] = trim($_REQUEST['FilterFechaMinutaHasta']);
	$filter['NumeroPedido'] 	= trim($_REQUEST['FilterNumeroPedido']);
	if ($currentUser->IdPerfil == 2)
		$filter['IdUsuario'] = $currentUser->IdUsuario;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

if ($IdCliente)
	$filter['IdCliente'] = $IdCliente;

/* declaracion de variables */
$arrData 			= array();
$oMinutas 			= new MinutasUsados();
$oUsados 			= new Usados();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oGestorias			= new Gestorias();
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
	window.location.href = 'cuentascorrienteusados.php?MainAction=<?=$Action?>';
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

function SetNumeroVin(IdUsado, NumeroVin)
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Usados</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
		<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30%" height="40">
							&nbsp;
                        </td>
						<td width="35%" height="40">
                            &nbsp;
                        </td>
                        <td width="35%" height="40">
                            <?php /*<table border="0" align="right" cellpadding="0" cellspacing="0">
								<tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="cuentascorrienteusados_exportar.php<?=$strParams?>">Exportar Saldo XLS</a></td>
									<td width="30">&nbsp;</td>
									<td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="cuentascorrienteusados_exportar_pdf.php<?=$strParams?>">Exportar Saldo PDF</a></td>
                                </tr>
                            </table>*/ ?>
                        </td>
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
                                        <td><input name="FilterIdUsado" id="FilterIdUsado" type="text" class="camporFormularioSimple" value="<?=$filter['IdUsado']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu"><div align="right">Dominio:</div></td>
                                        <td>
                                        	<input name="FilterDominio" id="FilterDominio" type="text" class="camporFormularioSimple" value="<?=$filter['Dominio']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off">
                                       	</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vendedor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acreditado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Saldo</strong></div></td>
                        <td width="103" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oMinuta) 
					{ 
						$oUsado = $oUsados->GetById($oMinuta->IdUsado);
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
                        <td width="75" height="25"><div id="margen" align="center">U-<?=$oMinuta->IdMinuta?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
                        <td width="168" height="25"><div id="margen"><?=$oUsado->Modelo?></div></td>
                        <td width="168" height="25"><div id="margen"><?=$oUsado->Dominio?></div></td>
                        <td width="134" height="25"><div id="margen"><?=$cliente?></div></td>
                        <td width="153" height="25"><div id="margen"><?=$oUsuario->Nombre . ', ' . $oUsuario->Apellido?></div></td>
                        <td width="85" height="25"><div id="margen" align="center"><strong>$<?= number_format($oMinuta->GetCostoTotal(), 2, ',', '.')?></strong></div></td>
                        <td width="85" height="25"><div id="margen" align="center"><strong>$<?= number_format($oMinuta->GetTotalAcreditado(), 2, ',', '.')?></strong></div></td>
                        <td width="85" height="25"><div id="margen" align="center"><strong>$<?= number_format($oMinuta->GetTotalPendiente(), 2, ',', '.')?></strong></div></td>
                        <td width="103" height="25" valign="middle">
                            <div align="center">
	                     	
								<?php if (Session::CheckPerm(PERM_CUECOR_DETAIL)){ ?>
								<a href="cuentascorrienteusados_detail.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/preview.png" alt="Detalle" title="Detalle" border="0" /></a> - 
								<a href="cuentascorrienteusados_pdf.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/pdf.png" alt="Imprimir" title="Imprimir" border="0" /></a> - 
                                <?php } ?>
								<?php if (Session::CheckPerm(PERM_PAGO_LIST)){ ?>
								<a href="pagosusados.php<?=$strParams?>&IdMinuta=<?=$oMinuta->IdMinuta?>">
                                    <img src="images/iconos/facturacion.png" alt="Pagos" title="Pagos" border="0" /></a>
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