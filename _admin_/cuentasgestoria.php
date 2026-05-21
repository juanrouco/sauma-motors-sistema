<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GESCUE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= isset($_REQUEST['PageSize']) ? intval($_REQUEST['PageSize']) : 100;
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdMinuta'] 			= trim($_REQUEST['FilterIdMinuta']);
	$filter['IdGestor']			 	= trim($_REQUEST['FilterIdGestor']);
	$filter['FechaDesde'] 			= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 			= trim($_REQUEST['FilterFechaHasta']);
	$filter['SinRendir'] 			= trim($_REQUEST['FilterSinRendir']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oCuentasGestoria 	= new CuentasGestoria();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oGestores 			= new Gestores();
$oPage 				= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oCuentasGestoria->GetCountRowsHeaders($filter), true);
$arrData 	= $oCuentasGestoria->GetAllHeaders($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrGestores = $oGestores->GetAll();

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
	window.location.href = 'cuentasgestoria.php?MainAction=<?=$MainAction?>';
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

function Select(IdFactura)
{
	window.opener.SetFactura(IdFactura);
	window.close();
}

function Desplegar(id)
{
	$j('#table_' + id).toggle(0);
	return false;
}

var timer = null;
$j(document).ready(function() {
	$j(".comentarios-guardar").on('input',function() {
		
		var IdCuentaGestoria = $j(this).attr('data');
		var Comentarios = $j(this).val();
		clearTimeout(timer); 
		timer = setTimeout(function() {
			SaveComentariosCuentaGestoria(IdCuentaGestoria, Comentarios);
		}, 1000);
		
	});
});


</script>


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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Gestor&iacute;a</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
       
	  	<?php if ($Action != 'Select') { ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="8%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_GESCUE_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="cuentasgestoria_seleccionar_minuta.php<?=$strParams?>">Agregar</a></td>
									 <td width="30"><div align="center">&nbsp;</div></td>
                                    <td width="30"><div align="center">&nbsp;</div></td>									                                     
                                </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="76%" height="40">&nbsp;</td>
                        <td width="16%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_GESCUE_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                                    <td><a href="cuentasgestoria_exportar.php<?=$strParams?>">Exportar Rentabilidad</a></td>									                                     
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
                                        <td class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                        <td><input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioChico" value="<?=$filter['FechaDesde']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                                </script>
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
                                        <td><input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioChico" value="<?=$filter['FechaHasta']?>" maxlength="12">
										<script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                                </script>
										</td>
                                        <td><input type="checkbox" id="FilterSinRendir" name="FilterSinRendir" <?= $filter['SinRendir'] == '1' ? 'checked="checked"' : '' ?> value="1" /><strong>Sin Rendir</strong></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>  
										<td class="tituloMenu"><div align="right">N&uacute;mero Carpeta:</div></td>
                                        <td><input name="FilterIdMinuta" id="FilterIdMinuta" type="text" class="camporFormularioSimple" value="<?=$filter['IdMinuta']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Gestor:</div></td>
                                        <td>
											<select name="FilterIdGestor" id="FilterIdGestor" class="camporFormularioSimple">
												<option value="">Indistinto</option>
												<?php
												foreach ($arrGestor as $oGestor)
												{
													$selected = '';
													if ($oGestor->IdGestor == $filter['IdGestor'])
														$selected = 'selected="selected"';
												?>
													<option value="<?= $oGestor->IdGestor ?>" <?= $selected ?>><?= $oGestor->RazonSocial ?></option>
												<?php
												}
												?>
											</select>
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
				<?php 
					$count = 0;
					foreach ($arrData as $oHeader) 
					{ 
						$count++;
						$lista = '';
						foreach ($oHeader['CuentasCorriente'] as $oCuentaGestoria) 
						{ 
							if ($lista != '') $lista.= ', ';
							$lista.= $oCuentaGestoria->IdMinuta;
						}
				?>
				
                    <tr class="bordeGrisFondo">
						<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
						<td width="100"><span class="tituloPagina"><?= CambiarFecha($oHeader['Fecha']) ?></span></td>
						<td align="left"><span class="tituloPagina">[<?= $lista ?>]</span></td>
						<td align="center">
							<a href="cuentasgestoria_pdf.php?Fecha=<?= CambiarFecha($oHeader['Fecha']) ?>">
								<img src="images/iconos/pdf.png" alt="Eliminar" border="0" /></a> - 
							<?php if (Session::CheckPerm(PERM_GESCUE_UPDATE)){ ?>
							<a href="cuentasgestoria_lote_mod.php?Fecha=<?= CambiarFecha($oHeader['Fecha']) ?>">
								<img src="images/iconos/mod.gif" alt="Eliminar" border="0" /></a>
							<?php } ?>
							
						</td>
						<td width="20" height="40" class="TituloGrupo"><a href="#" onclick="Desplegar('<?= $count ?>');"><img src="images/iconos/descarga.gif" alt="Ver mas" title="Ver mas" /></a></td>
						
					</tr>
					<tr>
					<td colspan="5">
				<table id="table_<?= $count ?>" width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris" style="display: none">
                    <tr class="bordeGrisFondo">
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Carpeta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Gestor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Rendici&oacute;n</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td width="91" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($oHeader['CuentasCorriente'] as $oCuentaGestoria) 
					{ 
						$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$cliente = $oCliente->RazonSocial;
						if ($oMinuta->Condominio)
						{
							$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
							$cliente.= " / " . $oClienteCondominio->RazonSocial;
						}
						$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oGestor = $oGestores->GetById($oCuentaGestoria->IdGestor);
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="85" height="25"><div id="margen" align="center"><?=$oCuentaGestoria->IdMinuta?></div></td>
						<td colspan="6">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="bordeGris">
								<tr>
									<td width="85" height="25"><div id="margen"><?=CambiarFecha($oCuentaGestoria->Fecha) ?></div></td>
									<td width="178" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
									<td width="178" height="25"><div id="margen"><?=$cliente?></div></td>
									<td width="178" height="25"><div id="margen"><?=$oGestor->RazonSocial?></div></td>
									<td width="110" height="25"><div id="margen"><?=CambiarFecha($oCuentaGestoria->FechaRendicion)?></div></td>
									<td width="123" height="25"><div id="margen" align="center">$ <?= $oCuentaGestoria->TotalFinal ? number_format($oCuentaGestoria->TotalFinal, 2) : number_format($oCuentaGestoria->TotalCalculado, 2)?></div></td>
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
								<tr>
									<td colspan="6">
										<div align="left">
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td width="100"><div align="center">Comentarios:&nbsp;</div></td>
													<td><textarea data="<?= $oCuentaGestoria->IdCuentaGestoria ?>" class="comentarios-guardar" id="Comentarios-<?= $oCuentaGestoria->IdMinuta ?>" rows="1" style="width:100%"><?= $oCuentaGestoria->Comentarios ?></textarea></td>
													<td width="20">&nbsp;</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</td>
                        <td width="91" height="25" valign="middle">
                            <div align="center">
                                <?php if (Session::CheckPerm(PERM_GESCUE_UPDATE)){ ?>
								<a href="cuentasgestoria_mod.php?IdCuentaGestoria=<?=$oCuentaGestoria->IdCuentaGestoria?>">
									<img src="images/iconos/mod.gif" alt="Eliminar" border="0" /></a> - 
								<?php } ?>
								<?php if (Session::CheckPerm(PERM_GESCUE_RENDICION)){ ?>
								<a href="cuentasgestoria_rendicion.php?IdCuentaGestoria=<?=$oCuentaGestoria->IdCuentaGestoria?>">
									<img src="images/iconos/facturacion.png" alt="Eliminar" border="0" /></a> - 
								<?php } ?>
								<?php if (Session::CheckPerm(PERM_GESCUE_DELETE)){ ?>
								<a href="cuentasgestoria_del.php?IdCuentaGestoria=<?=$oCuentaGestoria->IdCuentaGestoria?>">
									<img src="images/iconos/del.gif" alt="Eliminar" border="0" />
								</a>
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