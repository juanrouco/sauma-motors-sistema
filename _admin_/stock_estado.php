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
$PageSize 	= intval($_REQUEST['PageSize']);
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
	//$filter['IdEstado'] 		= trim($_REQUEST['FilterIdEstado']);//EstadoUnidad::Stock;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oUnidades 				= new Unidades();
$oUsados 				= new Usados();
$oMarcas 				= new Marcas();
$oModelos 				= new Modelos();
$oColores				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oEstadosUnidad 		= new EstadosUnidad();
$oMinutas				= new Minutas();
$oMinutasUsados			= new MinutasUsados();
$oPagos					= new Pagos();
$oCajasDetalles			= new CajasDetalles();
$oPage 					= new Page($Page, $PageSize);

$oReporteTotalUnidades 				= $oUnidades->GetTotalReporteStock($filter);
$oReporteTotalUsados 				= $oUsados->GetTotalReporteStock($filter);
$oReporteTotalDeuda 				= $oUnidades->GetTotalReporteDeuda($filter);
$oReporteTotalSaldo 				= $oMinutas->GetSaldoTotal($filter);
$oReporteTotalSaldoAIngresar 		= $oMinutas->GetUsadosAIngresar($filter);
$oReporteTotalSaldoUsados 			= $oMinutasUsados->GetSaldoTotal($filter);
$oReporteTotalSaldoUsadosAIngresar	= $oMinutasUsados->GetUsadosAIngresar($filter);
$oReporteTotalPagoCredito			= $oPagos->GetTotales(array('Pago' => '0', 'IdTipoPago' => TipoPago::CreditoPersonal));

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&FilterModelo=' 		. $filter['IdModelo'];
$strParams.= '&FilterUbicacion=' 	. $filter['IdUbicacion'];
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
	window.location.href = 'stock_estado.php?MainAction=<?=$Action?>';
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
									<tr>
										<td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Marca:</div></td>
                                        <td><select name="FilterIdMarca" id="FilterIdMarca"  class="camporFormularioSimple">
                                        <option value="">INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
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
						<td align="left" class="tituloPagina" width="120">Tipo</td>
						<td align="center" class="tituloPagina" width="120">Cantidad</td>
						<td align="center" class="tituloPagina" width="120">Galp&oacute;n</td>
						<td align="center" class="tituloPagina" width="120">Efectivo</td>
						<td align="center" class="tituloPagina" width="120">Cr&eacute;dito</td>
						<td height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>0 Km</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalUnidades->CantidadTotal ?></strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalUnidades->CostoTotal, 2, ',', '.') ?></strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalUnidades->VentaTotal, 2, ',', '.') ?></strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalUnidades->VentaCreditoTotal, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" class="tituloPagina" width="120">Tipo</td>
						<td align="center" class="tituloPagina" width="120">Cantidad</td>
						<td colspan="3" align="center" class="tituloPagina" width="120">Valuaci&oacute;n</td>
						<td height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Usados</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalUsados->CantidadTotal ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px; text-align:center"><strong>$<?= number_format($oReporteTotalUsados->CostoTotal, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>A Cobrar 0 Km (Efectivo)</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalSaldo->Cantidad ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldo->Saldo - $oReporteTotalSaldoAIngresar->Valuacion, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>A Cobrar 0 Km (Usados)</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalSaldoAIngresar->Cantidad ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldoAIngresar->Valuacion, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php /*
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Adelantos Financieros 0Km</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>-</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldo->ImportePagos, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>*/ ?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>A Cobrar Usados (Efectivo)</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalSaldoUsados->Cantidad ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldoUsados->Saldo - $oReporteTotalSaldoUsadosAIngresar->Valuacion, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>A Cobrar Usados (Usados)</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalSaldoUsadosAIngresar->Cantidad ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldoUsadosAIngresar->Valuacion, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Cr&eacute;ditos Pendientes</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong><?= $oReporteTotalPagoCredito->Cantidad ?></strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalPagoCredito->Valuacion, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php /*
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Adelantos Financieros Usados</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>-</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oReporteTotalSaldoUsados->ImportePagos, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>*/ 
					?>
					<?php
					$oCaja = $oCajasDetalles->GetById(1);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Caja Liberador</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php
					$oCaja = $oCajasDetalles->GetById(6);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Caja Ramirez</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php
					$oCaja = $oCajasDetalles->GetById(2);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Valores a Depositar</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php
					$oCaja = $oCajasDetalles->GetById(3);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Banco Galicia</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php
					$oCaja = $oCajasDetalles->GetById(4);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Banco Patagonia</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<?php
					$oCaja = $oCajasDetalles->GetById(5);
					?>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Banco Honda</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>N/A</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>$<?= number_format($oCaja->Total, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td align="left" width="120" style="font-size: 15px;"><strong>Deuda</strong></td>
						<td align="center" width="120" style="font-size: 15px;"><strong>0</strong></td>
						<td colspan="3" align="center" width="120" style="font-size: 15px;"><strong>-$<?= number_format($oReporteTotalDeuda->CostoTotal, 2, ',', '.') ?></strong></td>
						
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
    
    </table>
</form>

</body>
</html>