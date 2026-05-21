<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);
$PageSize 			= intval($_REQUEST['PageSize']);
$IdMinuta			= intval($_REQUEST['IdMinuta']);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filterPago)) || (IsEmptyArray($filterPago)) || ($Submit))
{
	$filter = array();
	$filter['FechaDesde'] 	= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 	= trim($_REQUEST['FilterFechaHasta']);
	$filter['Interno'] 		= trim($_REQUEST['FilterInterno']);
	$filter['Pago'] 		= '0';
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";
$IdTipoPago = $_REQUEST['IdTipoPago'];
$filter['IdTipoPago'] 	= trim($_REQUEST['IdTipoPago']);

/* declaracion de variables */
$arrData 			= array();
$oPagos			 	= new Pagos();
$oPage 				= new Page($Page, $PageSize);

/* definimos cadena a mandar por get */
$strParams = '?';
$strParams.= '&IdTipoPago=' 			. $filter['IdTipoPago'];
$strParams.= '&Page='				. $Page;
$strParams.= '&PageSize='			. $PageSize;


/* obtenemos el listado de datos a mostrar */
$Paginado	= Pageable::PrintPaginator($oPage, $oPagos->GetCountRows($filter), true);
$arrData 	= $oPagos->GetAllOrdered($filter, $oPage);

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
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="IdMiIdTipoPagonuta" id="IdTipoPago" value="<?= $IdTipoPago ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente - Listado de <?= TipoPago::GetById($IdTipoPago) ?></span></td>
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
                                    <td>&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>		
            </td>
        </tr>
       <tr style="display: none">
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
                                            <td>&nbsp;</td>
                                            
                                        </tr>
										<tr>                                  
                                            <td class="tituloMenu"><div align="right">Interno:</div></td>
                                            <td><input name="FilterInterno" id="FilterInterno" type="text" class="camporFormularioSimple" value="<?=$filter['Interno']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                            <td>&nbsp;</td>
											<td class="tituloMenu"><div align="right">Pagado:</div></td>
                                            <td>
												<select name="FilterPago" id="FilterPago" class="camporFormularioSimple">
													<option value="">INDISTINTO</option>
													<option value="1" <?= $filter['Pago'] == '1' ? 'selected="selected"' : '' ?>>SI</option>
													<option value="0" <?= $filter['Pago'] == '0' ? 'selected="selected"' : '' ?>>NO</option>
												</select>
											</td>
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
                        <td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Banco</strong></div></td>
                        <td width="80" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Cheque</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha Emisi&oacute;n</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha Deposito</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Importe</strong></div></td>
                    </tr>
          
                <?php 
					$Total = 0;
					foreach ($arrData as $oPago) 
					{
						$Interno = $oPago->IdMinuta;
						if (!$Interno)
							$Interno = 'U-' . $oPago->IdMinutaUsado;
						
						$Total+= $oPago->Importe;
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen" align="left"><?=$oPago->BancoDesde?></div></td>
                        <td height="25"><div id="margen" align="center"><?=$oPago->NumeroCheque?></div></td>
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->FechaEmision) ?></div></td>
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->FechaDeposito) ?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= number_format($oPago->Importe, 2, ',', '.') ?></div></td>
                     </tr>
                    <tr>
                        <td colspan="6">
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
					<tr>
                        <td  colspan="4" height="25"><div id="margen" align="right"><strong>Total: </strong></div></td>
                        <td height="25"><div id="margen" align="cemter"><strong>$<?= number_format($Total, 2, ',', '.') ?></strong></div></td>
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