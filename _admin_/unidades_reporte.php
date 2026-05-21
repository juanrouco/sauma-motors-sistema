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
$PageSize 	= 1000;// intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
	$filter['IdMarca'] 			= trim($_REQUEST['FilterIdMarca']);
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
	$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);	
	$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);	
	$filter['Reportado'] 		= trim($_REQUEST['FilterReportado']);	
	$filter['IdOrigenCliente'] 	= trim($_REQUEST['FilterIdOrigenCliente']);	
	//$filter['IdEstado'] 		= trim($_REQUEST['FilterIdEstado']);//EstadoUnidad::Stock;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

if (!$_REQUEST['EnStock'] && $filter['FechaDesde'] == '' && $filter['FechaHasta'] == '')
{
	$filter['FechaHasta'] = date('d-m-Y');
	$filter['FechaDesde'] = '01' . date('-m-Y');
}

/* declaracion de variables */
$arrData 				= array();
$oUnidades 				= new Unidades();
$oMarcas 				= new Marcas();
$oModelos 				= new Modelos();
$oColores				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oEstadosUnidad 		= new EstadosUnidad();
$oMinutas		 		= new Minutas();
$oUsados		 		= new Usados();
$oCuentasGestoria		= new CuentasGestoria();
$oPagos					= new Pagos();
$oMinutasFinanciacion	= new MinutasFinanciacion();
$oClientes				= new Clientes();
$oCajas					= new Cajas();
$oPedidosAccesorios		= new PedidosAccesorios();
$oPage 					= new Page($Page, $PageSize);

$oCaja = $oCajas->GetById(1);
if ($_REQUEST['EnStock'])
{
	$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRowsReporteStock($filter), true);
	$arrData 	= $oUnidades->GetAllReporteStock($filter, $oPage);

	$oReporteTotal = $oUnidades->GetTotalReporteStock($filter);
}
else
{
	$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRowsReporteVendidos($filter), true);
	$arrData 	= $oUnidades->GetAllReporteVendidos($filter, $oPage);

	$filter['NotIdTipoModelo'] = 40;
	$oReporteTotal = $oUnidades->GetTotalReporteVendidos($filter);
	$filter['IdTipoModelo'] = 40;
	$filter['NotIdTipoModelo'] = null;
	$oReporteTotalFuerza = $oUnidades->GetTotalReporteVendidos($filter);
}

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&FilterModelo=' 		. $filter['IdModelo'];
$strParams.= '&FilterUbicacion=' 	. $filter['IdUbicacion'];
$strParams.= '&FilterFechaDesde=' 	. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 	. $filter['FechaHasta'];
$strParams.= '&FilterReportado=' 	. $filter['Reportado'];
$strParams.= '&FilterIdOrigenCliente=' 	. $filter['IdOrigenCliente'];
$strParams.= '&EnStock=' 			. $_REQUEST['EnStock'];

if (Session::CheckPerm(PERM_UNID_CREATE))
	$strParams.= '&fullpermisos=1';

$arrModelos 		= $oModelos->GetAllOrdered();
$arrUbicaciones 	= $oUbicaciones->GetAll();
$arrMarcas	 		= $oMarcas->GetAll();
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
	window.location.href = 'unidades_reporte.php?MainAction=<?=$Action?>';
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

	obj = SendXMLRequest('Minutas', 'UpdateReportado', null, arr);
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
                        <td height="40"><span class="tituloPagina">Reporte de Unidades en <?= $_REQUEST['EnStock'] ? 'Stock' : 'Vendidos' ?></span></td>
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
						<td width="30"><div align="center"><a href="unidades_reporte_exportar.php<?=$strParams?>">Exportar</a></div></td>
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
                                        <td>&nbsp;</td>
										
                                        <td>&nbsp;</td>
                                    </tr>
									<?php
									}
									?>
									<tr>
										 <td class="tituloMenu"><div align="right">Modelo:</div></td>
                                        <td><select name="FilterModelo" id="FilterModelo"  class="camporFormularioMediano">
                                        <option value="">INDISTINTO</option>
                                        <?php foreach ($arrModelos as $oModelo) { ?>
                                        <option value="<?=$oModelo->IdModelo?>" <?php if ($oModelo->IdModelo == $filter['IdModelo']) echo "selected='selected'"; ?> ><?=$oModelo->DenominacionComercial?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioMediano">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td> 
									<?php
									if (!$_REQUEST['EnStock'])
									{
									?>            
                                        <td class="tituloMenu"><div align="right">Origen de Cliente:</div></td>
                                        <td>
                                        	 <select name="FilterIdOrigenCliente" class="camporFormularioMediano" id="FilterIdOrigenCliente">
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
									<?php
									}
									?> <?php /*
										<td class="tituloMenu"><div align="right">Marca:</div></td>
                                        <td><select name="FilterIdMarca" id="FilterIdMarca"  class="camporFormularioSimple">
                                        <option value="">INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>*/ ?>
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
									<td class="tituloPagina" width="250">Cantidad <?= $_REQUEST['EnStock'] ? 'en Stock' : 'Motos Vendidas' ?>:</td>
									<td class="tituloPagina" align="left"><?= $oReporteTotal->CantidadTotal ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Valuaci&oacute;n Galp&oacute;n:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotal->CostoTotal, 2, ',', '.') ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Valuaci&oacute;n Ventas:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotal->VentaTotal, 2, ',', '.') ?></td>
									<?php
									if ($oReporteTotal->VentaCreditoTotal)
									{
									?>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Valuaci&oacute;n Cr&eacute;dito:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotal->VentaCreditoTotal, 2, ',', '.') ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Disponible:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oCaja->TotalDetalles, 2, ',', '.') ?></td>
									<?php
									}
									?>
								</tr>
								<?php
								if ($oReporteTotalFuerza)
								{
								?>
								<tr>
									<td class="tituloPagina" width="250">Cantidad <?= $_REQUEST['EnStock'] ? 'en Stock' : 'Fuerza Vendidas' ?>:</td>
									<td class="tituloPagina" align="left"><?= $oReporteTotalFuerza->CantidadTotal ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Valuaci&oacute;n Galp&oacute;n:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotalFuerza->CostoTotal, 2, ',', '.') ?></td>
									<td width="100">&nbsp;</td>
									<td class="tituloPagina" width="170">Valuaci&oacute;n Ventas:</td>
									<td class="tituloPagina" align="left">$<?= number_format($oReporteTotalFuerza->VentaTotal, 2, ',', '.') ?></td>
									
								</tr>
								<?php
								}
								?>
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
						<?php
						if (!$_REQUEST['EnStock'] == '1')
						{
						?>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio Venta</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Costo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Patent.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Acc.</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Gastos</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Credicuotas</strong></div></td>
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>TP</strong></div></td>
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
          
                <?php foreach ($arrData as $oUnidad) 
				{
                    $oModelo = $oModelos->GetById($oUnidad->IdModelo);
                    $oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
                    $oEstadoUnidad = $oEstadosUnidad->GetById($oUnidad->IdEstado);
					$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
					$oColor	= $oColores->GetById($oUnidad->IdColor);
					$oMinuta	= $oMinutas->GetById($oUnidad->IdUnidad);
					$oCliente	= $oClientes->GetById($oMinuta->IdCliente);
					
					if (!$_REQUEST['EnStock'] == 1 && $oMinuta->GetTotalPendiente() > 0.1)
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
						<td width="96" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>  
                        <td width="150" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						<?php
						if (!$_REQUEST['EnStock'] == '1')
						{
							$oCuentaGestoria	= $oCuentasGestoria->GetByIdMinuta($oMinuta->IdMinuta);
							$PrecioUsado = 0;
							$Efectivo = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Efectivo);
							$Transferencia = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Transferencia);
							$Pagare = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Pagare);
							$Tarshop = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credicuotas);
							$Confina = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Confina);
							$Credilogros = $oMinutasFinanciacion->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
							$Credilogros = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
							$AM = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Credito);
							$Visa = 0;//$oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Visa);
							$MC = 0;//$oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::MC);
							$DepositoEfectivo = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoEfectivo);
							$DepositoCheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoCheque);
							$Debito = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Debito);
							$Cheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Cheque);
							$MP = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::MercadoPago);
							$TP = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::TodoPago);
							
							$PrecioVentaTotal = $oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento;
							
							$Tarjeta = $AM + $Visa + $MC;
							$Deposito = $DepositoEfectivo + $DepositoCheque;
							
							$oPedidoAccesorio = $oPedidosAccesorios->GetByIdMinuta($oMinuta->IdMinuta);
							$CostoAccesorios = 0;
							if ($oPedidoAccesorio)
								$CostoAccesorios = $oPedidoAccesorio->GetCosto();
							
							if ($oMinuta->EntregaUsado) 
							{
								$arrUsados = $oUsados->GetAllByIdMinuta($oMinuta->IdMinuta);
								
								$oUsado = $arrUsados[0];
								if (count($arrUsados) > 1)
								{
									$oUsado2 = $arrUsados[1];
									$PrecioUsado+= $oUsado2->Valuacion;
								}
								
								$PrecioUsado+= $oUsado->Valuacion;
							}
							$PrecioCompra = $oUnidad->ImporteCompraNeto && $oUnidad->ImporteCompraNeto != 0 ? $oUnidad->ImporteCompraNeto : $oModelo->PrecioCompra;
						?>
                        <td width="128" height="25"><div id="margen"><?= substr($oCliente->RazonSocial, 0, 50) ?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioVentaTotal, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioCompra, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($oCuentaGestoria->TotalFinal, 0, ',', '.')?></div></td>
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
                        <td width="128" height="25"><div id="margen">$<?=number_format($TP, 0, ',', '.')?></div></td>
                        <td width="128" height="25"><div id="margen">$<?=number_format($PrecioVentaTotal - $PrecioCompra - $oCuentaGestoria->TotalFinal - $CostoAccesorios, 0, ',', '.')?></div></td>
                        <td height="25">
							<div id="margen" align="center">
								<input type="checkbox" id="Reportado[]" name="Reportado[]" value="1" onClick="javascript: UpdateReportado(this.checked, '<?=$oMinuta->IdMinuta?>');" <?= $oMinuta->Reportado ? 'checked="checked"' : '' ?> />
							</div>
						</td>
                        <td width="128" height="25">
							<div id="margen" align="center">
								<a target="_blank" href="minutas_detail.php?IdMinuta=<?= $oMinuta->IdMinuta ?>"><img src="images/iconos/preview.png" alt="Ver Detalle" title="Ver Detalle" /></a> - 
								<a target="_blank" href="cuentascorriente_detail.php?IdMinuta=<?= $oMinuta->IdMinuta ?>"><img src="images/iconos/calculadora.png" alt="Cuenta Corriente" title="Cuenta Corriente" /></a>
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
                        <td colspan="24">
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