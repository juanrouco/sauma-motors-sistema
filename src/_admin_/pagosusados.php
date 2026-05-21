<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter				= ReceiveArray($_REQUEST['filter']);
$filterArchivos		= ReceiveArray($_REQUEST['filterArchivos']);
$Page				= intval($_REQUEST['Page']);
$PageSize 			= intval($_REQUEST['PageSize']);
$PageContacto		= intval($_REQUEST['PageContacto']);
$PageContactoSize 	= intval($_REQUEST['PageContactoSize']);
$IdMinuta			= intval($_REQUEST['IdMinuta']);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filterPago)) || (IsEmptyArray($filterPago)) || ($Submit))
{
	$filterPago = array();
	/*$filterPago['Nombre'] 	= trim($_REQUEST['FilterNombre']);
	$filterPago['Apellido'] = trim($_REQUEST['FilterApellido']);
	$filterPago['Email'] 	= trim($_REQUEST['FilterEmail']);*/
	$filterPago['IdMinutaUsado'] 	= trim($_REQUEST['IdMinuta']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filterPago)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filterPago)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oMinutas 			= new MinutasUsados();
$oPagos			 	= new Pagos();
$oPage 				= new Page($Page, $PageSize);

/* definimos cadena a mandar por get */
$strParams = '?';
$strParams.= '&IdMinuta=' 			. $IdMinuta;
$strParams.= '&Page='				. $Page;
$strParams.= '&PageSize='			. $PageSize;
$strParams.= '&PageContacto='		. $PageContacto;
$strParams.= '&PageContactoSize='	. $PageContactoSize;
if (!is_array($filter))
	$filter = array();
$strParams.= '&filter=' 			. SendArray($filter);
if (is_array($filterPago))
	$strParams.= '&filterPago=' 		. SendArray($filterPago);

if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{
	header('Location: cuentascorrienteusados.php' . $strParams);
	exit;
}

/* obtenemos el listado de datos a mostrar */
$Paginado	= Pageable::PrintPaginator($oPage, $oPagos->GetCountRows($filterPago), true);
$arrData 	= $oPagos->GetAll($filterPago, $oPage);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function SetPage(PageContacto)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.PageContacto.value = PageContacto;
	frmData.submit();
}

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.PageContacto.value = 0;
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

	frmData.PageContacto.value = 0;

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
    <input type="hidden" name="PageContacto" id="PageContacto" value="<?=$PageContacto?>" />
	<input type="hidden" name="PageContactoSize" id="PageContactoSize" value="<?=$PageContactoSize?>" />
	<input type="hidden" name="filter" id="filter" value="<?=SendArray($filter)?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="IdMinuta" id="IdMinuta" value="<?= $IdMinuta ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Usados - Pagos</span></td>
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
											<?php if (Session::CheckPerm(PERM_PAGO_CREATE)){ ?>
                                            <tr>
                                                <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                                <td><a href="pagosusados_add.php<?= $strParams ?>">Agregar</a></td>
                                            </tr>
											<?php } ?>
                                        </table>
                                    </td>
                                    <td><div align="right"><a href="cuentascorrienteusados.php<?= $strParams ?>" class="linkMenu"><strong>[Volver al listado de cuentas corriente]</strong></a></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>		
            </td>
        </tr>
       <?php /* <tr>
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
                                            <td width="8%" class="tituloMenu"><div align="right">Nombre:</div></td>
                                            <td width="30%"><input name="FilterNombre" id="FilterNombre" type="text" class="camporFormularioSimple" value="<?=$filter['Nombre']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                            <td width="8%" class="tituloMenu"><div align="right">Apellido:</div></td>
                                            <td width="27%"><input name="FilterApellido" id="FilterApellido" type="text" class="camporFormularioSimple" value="<?=$filter['Apellido']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                            <td width="27%">&nbsp;</td>
                                        </tr>
                                        <tr>                                  
                                            <td class="tituloMenu"><div align="right">Email:</div></td>
                                            <td><input name="FilterEmail" id="FilterEmail" type="text" class="camporFormularioSimple" value="<?=$filter['Email']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                            <td>&nbsp;</td>
                                            <td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>				
            </td>
        </tr> */ ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><b>Pagos del Interno <?=$oMinuta->IdUsado?>:</b></td>
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
                        <td width="297" height="25" class="bordeGrisTitulo"><div id="margen" align="cemter"><strong>Importe</strong></div></td>
                        <td width="290" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Tipo Pago</strong></div></td>
                        <td width="290" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Banco Destino</strong></div></td>
                        <td width="100" class="bordeGrisTitulo"><div align="left"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPago) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->Fecha)?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= number_format($oPago->Importe, 2, ',', '.') ?></div></td>
                        <td height="25"><div id="margen" align="left"><?=TipoPago::GetById($oPago->IdTipoPago) ?></div></td>
                        <td height="25"><div id="margen" align="left"><?=$oPago->BancoDestino?></div></td>
                        <td width="80" height="25"> 
                            <div align="left">
								<?php if (Session::CheckPerm(PERM_PAGO_UPDATE)){ ?>
                                <a href="pagosusados_mod.php<?=$strParams?>&IdPago=<?=$oPago->IdPago?>"><img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
                                <?php } ?>
								<?php if (Session::CheckPerm(PERM_PAGO_LIST)){ ?>
                                <a href="pagosusados_pdf.php<?=$strParams?>&IdPago=<?=$oPago->IdPago?>" target="_blank"><img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> - 
                                <?php } ?>
								<?php if (Session::CheckPerm(PERM_PAGO_DELETE) && false){ ?>
								<a href="pagosusados_del.php<?=$strParams?>&IdPago=<?=$oPago->IdPago?>"><img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
								<?php } ?>
							</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
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