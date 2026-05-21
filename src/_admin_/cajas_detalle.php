<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtenemos datos enviados */
$filter			= ReceiveArray($_REQUEST['filter']);
$Page 			= intval($_REQUEST['Page']);
$PageSize 		= intval($_REQUEST['PageSize']);
$IdCajaDetalle	= intval($_REQUEST['IdCajaDetalle']);
$Submit			= isset($_REQUEST['Submitted']);

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$arrData 			= array();
$oCajasMovimientos	= new CajasMovimientos();
$oCajasDetalles		= new CajasDetalles();
$oPagos				= new Pagos();
$oMinutas			= new Minutas();
$oMinutasUsados		= new MinutasUsados();
$oClientes			= new Clientes();
$oUnidades			= new Unidades();
$oUsados			= new Usados();
$oModelos			= new Modelos();
$oCuentasGestorias	= new CuentasGestoria();
$oUsuarios			= new Usuarios();
$oPage 				= new Page($Page, $PageSize);

$oUsuarioActivo 	= Session::GetCurrentUser();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST))
	Session::NoPerm();

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
	$filter['FechaDesde'] 		 = $_REQUEST['FilterFechaDesde'];
	$filter['FechaHasta']		 = $_REQUEST['FilterFechaHasta'];
	$filter['IdTipoMovimiento']	 = $_REQUEST['FilterIdTipoMovimiento'];
	$filter['IdConcepto']		 = $_REQUEST['FilterIdConcepto'];
	$filter['IdUsuario']		 = $_REQUEST['FilterIdUsuario'];
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['IdCajaDetalle'] = $IdCajaDetalle;

if ($filter['FechaDesde'] == '')
{
	$filter['FechaDesde'] = date('d-m-Y', strtotime('-1 day'));
}

$Paginado	= Pageable::PrintPaginator($oPage, $oCajasMovimientos->GetCountRows($filter), true);
$arrData 	= $oCajasMovimientos->GetAll($filter, $oPage);

$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle);

$arrUsuarios = $oUsuarios->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>

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

	frmData.MainActino.value = '';
	frmData.Page.value = 0;
	
	return true;
}

function ClearFilter()
{
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	window.location.href = 'cajas_detalle.php?IdCajaDetalle=<?= $IdCajaDetalle ?>';
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

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$_SEVER['PHP_SELF']?>" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
	<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?= $IdCajaDetalle ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina"><?= $oCajaDetalle->Nombre ?> - Detalle</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                      <td><a href="cajasmovimientos_add.php<?=$strParams?>&IdCajaDetalle=<?= $IdCajaDetalle ?>">Agregar Movimiento</a></td>
					  <td>&nbsp;</td>
                      <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Resumen" border="0"></div></td>
                      <td><a href="cajas_detalle_diario_pdf.php<?=$strParams?>&IdCajaDetalle=<?= $IdCajaDetalle ?>">Imprimir Resumen Diario</a></td>
					  <td>&nbsp;</td>
                      <td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Exportar" border="0"></div></td>
                      <td><a href="cajas_detalle_exportar.php<?=$strParams?>&IdCajaDetalle=<?= $IdCajaDetalle ?>">Exportar</a></td>
                    </tr>
                  </table></td>
                  <td height="40"><table border="0" align="right" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="30">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td width="20">&nbsp;</td>
                      <td width="30">&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
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
                    <div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-left: 10px; padding-bottom: 10px; padding-right: 10px; padding-top: 10px;">
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
                                	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                            			<tr>
                                			<td width="88" align="center" class="tituloMenu">Fecha Desde:</td>
                                			<td width="270" align="left">
                                                <input name="FilterFechaDesde" id="FilterFechaDesde" type="text" class="camporFormularioMediano" value="<?=$filter['FechaDesde']?>" />
                                                <script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmData',
                                                        'controlname': 'FilterFechaDesde'
                                                    });
                                                </script>
                                            </td>
											<td width="20">&nbsp;</td>
                                            <td width="79" class="tituloMenu">Fecha Hasta:</td>
                                            <td width="263" align="left">
                                                <input name="FilterFechaHasta" id="FilterFechaHasta" type="text" class="camporFormularioMediano" value="<?=$filter['FechaHasta']?>" />
                                                <script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmData',
                                                        'controlname': 'FilterFechaHasta'
                                                    });
                                                </script>
                                            </td>
											<td width="20">&nbsp;</td>
                            			</tr>
                            			<tr>
                                            <td  align="center" class="tituloMenu">Tipo Movimiento:</td>
                                  			<td>
                                            	<select name="FilterIdTipoMovimiento" id="FilterIdTipoMovimiento" class="camporFormularioSimple">
                                                	<option value="">[Indistinto]</option>
                                                    <?php foreach (TiposMovimientosCaja::GetAllEditable() as $oTipoMovimiento) { ?>
                                                    <option value="<?= $oTipoMovimiento['IdTipo'] ?>" <?=($filter['IdTipoMovimiento'] == $oTipoMovimiento['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oTipoMovimiento['Descripcion']?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
											<td>&nbsp;</td>
											<td class="tituloMenu">Concepto:</td>
                                  			<td>
                                            	<select name="FilterIdConcepto" id="FilterIdConcepto" class="camporFormularioSimple">
                                                	<option value="">[Indistinto]</option>
                                                    <?php foreach (ConceptosCajas::GetAll() as $oConcepto) { ?>
                                                    <option value="<?= $oConcepto['IdConcepto'] ?>" <?=($filter['IdConcepto'] == $oConcepto['IdConcepto']) ? 'selected="selected"' : ''?> ><?=$oConcepto['Descripcion']?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
											<td width="20">&nbsp;</td>
                            			</tr>
                            			<tr>
                                            <td  align="center" class="tituloMenu">Usuario:</td>
                                  			<td>
                                            	<select name="FilterIdUsuario" id="FilterIdUsuario" class="camporFormularioSimple">
                                                	<option value="">[Indistinto]</option>
                                                    <?php foreach ($arrUsuarios as $oUsuario) { ?>
                                                    <option value="<?= $oUsuario->IdUsuario ?>" <?=($filter['IdUsuario'] == $oUsuario->IdUsuario) ? 'selected="selected"' : ''?> ><?= $oUsuario->Nombre . ' '. $oUsuario->Apellido ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
											<td colspan="3">&nbsp;</td>
                                  			<td align="right"><div align="middle">
                                    			<input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
                                  			</div></td>
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
            <td></td>
            <td>&nbsp;</td>
        </tr>
      
    <?php if ($arrData != NULL) { ?>
        
        <tr>
            <td>
                <div align="right"><? print ($Paginado) ?></div>
                <br>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="12%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                        <td width="13%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Movimiento</strong></div></td>
                        <td width="13%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Concepto</strong></div></td>
                        <td width="13%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Usuario</strong></div></td>
                        <td width="14%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Detalle</strong></div></td>
                        <td width="12%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Ingreso</strong></div></td>
                        <td width="12%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Egreso</strong></div></td>
                        <td width="12%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Saldo</strong></div></td>
                        <td width="12%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					$Ingreso = 0;
					$Egreso = 0;
					foreach ($arrData as $oCajaMovimiento) 
					{ 
						$oUsado = null;
						if ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Pago)
						{
							$oPago = $oPagos->GetById($oCajaMovimiento->IdEntidad);
							if ($oPago->IdMinuta)
								$oMinuta = $oMinutas->GetById($oPago->IdMinuta);
						else
							{
								$oMinuta = $oMinutasUsados->GetById($oPago->IdMinutaUsado);
								$oUsado = $oUsados->GetById($oMinuta->IdMinuta);
							}
						}
						elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
						{
							$oCuentaGestoria = $oCuentasGestorias->GetById($oCajaMovimiento->IdEntidad);
							$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
						}
						
						$Ingreso+= $oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0;
						$Egreso+= $oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0;
						
						$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$oUsuario = $oUsuarios->GetById($oCajaMovimiento->IdUsuario);
						
						$Usuario = 'N/A';
						if ($oUsuario)
							$Usuario = $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
						if ($filter['FechaDesde'] || $filter['FechaDesde'] || $oCajaMovimiento->Fecha > date('Y-m-d'))
						{
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen"><?=CambiarFechaHora($oCajaMovimiento->Fecha)?></div></td>
                        <td height="25"><div id="margen"><?=TiposMovimientosCaja::GetById($oCajaMovimiento->IdTipoMovimiento)?></div></td>
                        <td height="25"><div id="margen"><?= $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Egreso || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Gastos ? ConceptosCajas::GetById($oCajaMovimiento->IdConcepto) : 'N/A'?></div></td>
                        <td height="25"><div id="margen"><?= $Usuario ?></div></td>
                        <td height="25">
							<div id="margen">
							<?php
							if  ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Pago)
							{
								if ($oPago->IdMinuta)
								{
							?>
								<?=  'Pago Unidad: ' . $oPago->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' - ' . $oPago->Observaciones . ' ' ?> 
							<?php
								}
								else
								{
							?>
								<?=  'Pago Unidad: U-' . $oPago->IdMinutaUsado . ' (' . $oUsado->Modelo . ') - Cliente: ' . $oCliente->RazonSocial . ' - ' . $oPago->Observaciones . ' ' ?> 
							<?php
								}
							}
							elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
							{
							?>	<?=  'Nro. Carpeta: ' . $oMinuta->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' ' ?> 
							<?php
							}
							elseif  ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::PagoPV)
							{
								echo $oCajaMovimiento->GetDetalle();
							}
							?>
							<?= $oCajaMovimiento->Comentarios ?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0 ?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0?></div></td>
                        <td height="25"><div id="margen" align="center">&nbsp;</div></td>
                        <td height="25">
							<div id="margen" align="center">
							<?php
							if (($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Egreso || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Gastos) && $IdCajaDetalle != CajaDetalle::BancoHonda && $IdCajaDetalle != CajaDetalle::BancoPatagonia && $IdCajaDetalle != CajaDetalle::BancoGalicia && $IdCajaDetalle != CajaDetalle::BancoGalicia)
							{
							?>
							<a href="cajasmovimientos_pdf.php?IdCajaMovimiento=<?= $oCajaMovimiento->IdCajaMovimiento ?>" target="_blank"><img src="images/iconos/pdf.png" /></a>
							<?php
							}
							?>
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
          
						<?php }} ?> 
					<tr bgColor='#f3f3f3'>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= $Ingreso ?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$Egreso?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$oCajaDetalle->Total?></div></td>
                        <td height="25"><div id="margen">&nbsp;</div></td>
                    </tr>				
                
                </table>
          </td>
        </tr>
        <tr>
            <td>
                <br>
                <div align="right"><? print ($Paginado) ?></div>
                <br>    
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