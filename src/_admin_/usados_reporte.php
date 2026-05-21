<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= 1000;//intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdMarca'] 			= trim($_REQUEST['FilterIdMarca']);
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
	$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);	
	$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);
	$filter['Reportado'] 		= trim($_REQUEST['FilterReportado']);		
	//$filter['IdEstado'] 		= trim($_REQUEST['FilterIdEstado']);//EstadoUnidad::Stock;
}

if (!$_REQUEST['EnStock'] && $filter['FechaDesde'] == '' && $filter['FechaHasta'] == '')
{
	$filter['FechaHasta'] = date('d-m-Y');
	$filter['FechaDesde'] = '01' . date('-m-Y');
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oUsados 				= new Usados();
$oMarcas 				= new Marcas();
$oColores				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oEstadosUnidad 		= new EstadosUnidad();
$oMinutasUsados 		= new MinutasUsados();
$oClientes				= new Clientes();
$oCuentasGestoriaUsados	= new CuentasGestoriaUsados();
$oPagos					= new Pagos();
$oMinutasFinanciacion	= new MinutasUsadosFinanciacion();
$oPedidosAccesorios		= new PedidosAccesorios();
$oPage 					= new Page($Page, $PageSize);

if ($_REQUEST['EnStock'])
{
	$Paginado	= Pageable::PrintPaginator($oPage, $oUsados->GetCountRowsReporteStock($filter), true);
	$arrData 	= $oUsados->GetAllReporteStock($filter, $oPage);

	$oReporteTotal = $oUsados->GetTotalReporteStock($filter);
}
else
{
	$Paginado	= Pageable::PrintPaginator($oPage, $oUsados->GetCountRowsReporteVendidos($filter), true);
	$arrData 	= $oUsados->GetAllReporteVendidos($filter, $oPage);

	$oReporteTotal = $oUsados->GetTotalReporteVendidos($filter);
}

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&FilterModelo=' 		. $filter['IdModelo'];
$strParams.= '&FilterUbicacion=' 	. $filter['IdUbicacion'];
$strParams.= '&FilterReportado=' 	. $filter['Reportado'];
$strParams.= '&FilterFechaDesde=' 	. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 	. $filter['FechaHasta'];
$strParams.= '&EnStock=' 			. $_REQUEST['EnStock'];

if (Session::CheckPerm(PERM_UNID_CREATE))
	$strParams.= '&fullpermisos=1';

$arrMarcas	 		= $oMarcas->GetAll();
$arrUbicaciones 	= $oUbicaciones->GetAll();
$arrEstadosUnidad 	= $oEstadosUnidad->GetAll();

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

function printFrame()
{
	window.print();
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
	window.location.href = 'usados_reporte.php?MainAction=<?=$Action?>';
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

function SetNumeroVinPrefijo(IdModelo, NumeroVinPrefijo)
{
	Get('FilterNumeroVinPrefijo').value = NumeroVinPrefijo;
}

function SetNumeroVin(IdUnidad, NumeroVin)
{
	Get('FilterNumeroVin').value = NumeroVin;
}

function UpdateReportado(checked, IdMinuta)
{
	var obj;
	var arr 	= new Array();
	
	arr['IdMinuta'] 	= IdMinuta;
	arr['Reportado'] = (checked) ? '1' : '0';

	obj = SendXMLRequest('MinutasUsados', 'UpdateReportado', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	return true;
}

</script>

<?php include('include/head.inc.php'); ?>

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
                        <td height="40"><span class="tituloPagina">Reporte de Usados en <?= $_REQUEST['EnStock'] ? 'Stock' : 'Vendidos' ?></span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="30" valign="top">
				<table border="0" align="right" cellpadding="0" cellspacing="0">
					<tr>
						<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar" border="0"></div></td>
						<td width="30"><div align="center"><a href="usados_reporte_exportar.php<?=$strParams?>">Exportar</a></div></td>
						<td width="20">&nbsp;</td>
						<td width="30"><div align="center"><img src="images/iconos/imprimir.png" alt="Imprimir" border="0"></div></td>
						<td width="30"><div align="center"><a href="#" onclick="printFrame(); return false;">Imprimir</a></div></td>
					</tr>
				</table>
			</td>
		</tr>
        <tr>
            <td height="30" valign="top">                
                <div id="FilterMain" class="">
                <div id="Filter" >		
                    <table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td><span class="tituloPagina">Filtro</span></td>
						</tr>
                        <tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
									<?php
									if (!$_REQUEST['EnStock'])
									{
									?>
                                    <tr>
										<td class="tituloMenu"><div align="right">Fecha Minuta Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaDesde" type="text" class="camporFormularioMediano" id="FilterFechaDesde" value="<?=$filter['FechaDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
                                            </script>
                                      	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Minuta Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaHasta" type="text" class="camporFormularioMediano" id="FilterFechaHasta" value="<?=$filter['FechaHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Visto:</div></td>
                                        <td>
                                            <select name="FilterReportado" class="camporFormularioMediano" id="FilterReportado">
												<option value="">[Indistinto]</option>
												<option value="0" <?= $filter['Reportado'] == '0' ? 'selected="selected"' : '' ?>>NO</option>
												<option value="1" <?= $filter['Reportado'] == '1' ? 'selected="selected"' : '' ?>>SI</option>
										</td>
                                    </tr>
									<?php
									}
									?>
									<tr>
										 <td class="tituloMenu"><div align="right">Marca:</div></td>
                                        <td><select name="FilterIdMarca" id="FilterIdMarca"  class="camporFormularioSimple">
                                        <option value="">INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                                        <td><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
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
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Datos Totales</span></td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td>
							<table width="100%"  border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="tituloPagina" width="250">Cantidad Unidades <?= $_REQUEST['EnStock'] ? 'en Stock' : 'Vendidas' ?>:</td>
									<td class="tituloPagina" align="left"><?= $oReporteTotal->CantidadTotal ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170"><?= $_REQUEST['EnStock'] ? 'Valuaci&oacute;n de Stock' : 'Valuaci&oacute;n Ventas' ?>:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotal->CostoTotal, 2) ?></td>
								</tr>
							</table>					
						</td>
					</tr>
				</table>
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
                        <td>&nbsp;</td>
						<?php
						if (!$_REQUEST['EnStock'] == '1')
						{
						?>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha</strong></div></td>                        
                        <?php
						}
						?>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>                        
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td><?php
						if (!$_REQUEST['EnStock'] == '1')
						{
						?>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio Venta</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Costo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Patent.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Acc.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Gastos</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tarshop</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Credilogros</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Confina</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tarjeta</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Debito</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Usada</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Transf.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Deposito</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Pagare</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cheque</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Efectivo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>MP</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ganancia</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Visto</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                        <?php
						}
						else
						{
						?>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>						
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>A&ntilde;o</strong></div></td>		
						<?php
						}
						?>						
                    </tr>
          
                <?php foreach ($arrData as $oUsado) 
				{
                    $oUbicacion = $oUbicaciones->GetById($oUsado->IdUbicacion);
                    $oEstadoUnidad = $oEstadosUnidad->GetById($oUsado->IdEstado);
					$oColor	= $oColores->GetById($oUsado->IdColor);
					$oMinuta	= $oMinutasUsados->GetById($oUsado->IdUsado);
					$oCliente	= $oClientes->GetById($oMinuta->IdCliente);
					if (!$_REQUEST['EnStock'] == 1 && $oMinuta->GetTotalPendiente() > 0)
					{
				?>          
                    <tr bgColor="#F4DA80" onMouseOver="bgColor='#F4DA80'" onMouseOut="bgColor='#F4DA80'">
				<?php
					}
					else
					{	
				?>
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
				<?php
					}
				?>
                        <td bgcolor="<?=$oEstadoUnidad->Color?>" width="7">&nbsp;</td>
                        <?php
						if (!$_REQUEST['EnStock'] == 1)
						{
						?>
						<td width="180" height="25"><div id="margen" align="center"><?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>  
                        <?php
						}
						?>
                        <td width="96" height="25"><div id="margen" align="center"><?=$oUsado->IdUsado?></div></td>                        
                        <td width="261" height="25"><div id="margen"><?=$oUsado->Modelo?></div></td>
						<td width="150" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <?php
						if (!$_REQUEST['EnStock'] == '1')
						{
							$oCuentaGestoria	= $oCuentasGestoriaUsados->GetByIdMinuta($oMinuta->IdMinuta);
							$PrecioUsado = 0;
							$Efectivo = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Efectivo);
							$Transferencia = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Transferencia);
							$Pagare = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Pagare);
							$Tarshop = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Tarshop);
							$Confina = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Confina);
							$Credilogros = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
							$AM = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Credito);
							$Visa = 0;//$oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Visa);
							$MC = 0;//$oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::MC);
							$DepositoEfectivo = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoEfectivo);
							$DepositoCheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoCheque);
							$Debito = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Debito);
							$Cheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Cheque);
							$MP = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::MercadoPago);
							$oUsadoVendido = $oUsados->GetById($oMinuta->IdMinuta);
							
							$PrecioVentaTotal = $oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento;
							
							$Tarjeta = $AM + $Visa + $MC;
							$Deposito = $DepositoEfectivo + $DepositoCheque;
							
							$oPedidoAccesorio = $oPedidosAccesorios->GetByIdMinuta($oMinuta->IdMinuta);
							$CostoAccesorios = 0;
							if ($oPedidoAccesorio)
								$CostoAccesorios = $oPedidoAccesorio->GetCosto();
							
							if ($oMinuta->EntregaUsado) 
							{
								$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinuta->IdMinuta);
								
								$oUsado = $arrUsados[0];
								if (count($arrUsados) > 1)
								{
									$oUsado2 = $arrUsados[1];
									$PrecioUsado+= $oUsado2->Valuacion;
								}
								
								$PrecioUsado+= $oUsado->Valuacion;
							}
						?>
                        <td width="128" height="25"><div id="margen"><?= substr($oCliente->RazonSocial, 0, 50) ?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioVentaTotal, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($oUsadoVendido->Valuacion, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($oCuentaGestoria->PatentamientoFinal, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($CostoAccesorios, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format(0, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Tarshop, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Credilogros, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Confina, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Tarjeta, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Debito, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioUsado, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Transferencia, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Deposito, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Pagare, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Cheque, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($Efectivo, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($MP, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioVentaTotal - $oUsadoVendido->Valuacion - $oCuentaGestoria->PatentamientoFinal - $CostoAccesorios, 0, ',', '.')?></div></td>
                        <td height="25">
							<div id="margen" align="center">
								<input type="checkbox" id="Reportado[]" name="Reportado[]" value="1" onClick="javascript: UpdateReportado(this.checked, '<?=$oMinuta->IdMinuta?>');" <?= $oMinuta->Reportado ? 'checked="checked"' : '' ?> />
							</div>
						</td>
						<td width="128" height="25">
							<div id="margen" align="center">
								<a target="_blank" href="minutasusados_detail.php?IdMinuta=<?= $oMinuta->IdMinuta ?>"><img src="images/iconos/preview.png" alt="Ver Detalle" title="Ver Detalle" /></a> - 
								<a target="_blank" href="cuentascorrienteusados_detail.php?IdMinuta=<?= $oMinuta->IdMinuta ?>"><img src="images/iconos/calculadora.png" alt="Cuenta Corriente" title="Cuenta Corriente" /></a>
							</div>
						</td>
						<?php
						}
						else
						{
						?>						
                        <td width="128" height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>						
                        <td width="71" height="25"><div id="margen"><?=$oUnidad->Anio?></div></td>
						<?php
						}
						?>
                    </tr>
                    <tr>
                        <td colspan="20">
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