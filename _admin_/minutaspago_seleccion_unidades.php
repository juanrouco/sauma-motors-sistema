<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MINP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$filter			= ReceiveArray($_REQUEST['filter']);
$Page 			= intval($_REQUEST['Page']);
$PageSize 		= intval($_REQUEST['PageSize']);
$IdMinutaPago 	= intval($_REQUEST['IdMinutaPago']);
$Action 		= strval($_REQUEST['MainAction']);
$Submit			= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVinPrefijo'] = trim($_REQUEST['FilterNumeroVinPrefijo']);
	$filter['NumeroVin'] 		= trim($_REQUEST['FilterNumeroVin']);
	$filter['IdUnidad'] 		= trim($_REQUEST['FilterIdUnidad']);
	$filter['IdModelo'] 		= trim($_REQUEST['FilterModelo']);
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
	$filter['IdEstado'] 		= trim($_REQUEST['FilterEstado']);
	$filter['NumeroPedido'] 	= trim($_REQUEST['FilterNumeroPedido']);
	$filter['Certificado'] 		= '0';
	$filter['Cancelado'] 		= '0';
	$filter['NotIdClientePlan'] = '1';
	
	/* En caso de querer agregar una venta, solo levanta los autos en stock */
	$PageSize = 1000;
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 				= array();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oColores				= new Colores();
$oUbicaciones 			= new Ubicaciones();
$oPlanillasRecepcion 	= new PlanillasRecepcion();
$oEstadosUnidad 		= new EstadosUnidad();
$oPage 					= new Page($Page, $PageSize);
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oMinutasPago			= new MinutasPago();
$oMinutasPagoItems		= new MinutasPagoItems();

$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);
$arrData 	= $oUnidades->GetAllOrderByEstado($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrModelos 		= $oModelos->GetAllOrdered();
$arrUbicaciones 	= $oUbicaciones->GetAll();
$arrEstadosUnidad 	= $oEstadosUnidad->GetAll();

$oMinutaPago = $oMinutasPago->GetById($IdMinutaPago);

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
	window.location.href = 'unidades.php?MainAction=<?=$Action?>';
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

<script type="text/javascript">
$j(document).ready(function() {
	$j(".check-change").change(function() {
		var total = 0;
		$j(".check-change:checked").each(function() {
			total += parseFloat($j(this).attr('importe'));
		});
		$j('#total-seleccionado').html(total.toFixed(2));
	});
});


</script>

</head>
<body>


    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Pago - Selecci&oacute;n de Unidades</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <tr>
            <td height="30" valign="top">
				<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
					<input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />
					<input type="hidden" name="IdPresupuesto" id="IdPresupuesto" value="<?=$_REQUEST['IdPresupuesto'] ?>" />
					<input type="hidden" name="IdMinutaEspera" id="IdMinutaEspera" value="<?=$_REQUEST['IdMinutaEspera'] ?>" />

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
                                        <td class="tituloMenu"><div align="right">Prefijo Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVinPrefijo" id="FilterNumeroVinPrefijo" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVinPrefijo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Modelos', 'GetAll', 'FilterNumeroVinPrefijo', 'SetNumeroVinPrefijo', 'IdModelo', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', null);
                                            </script>
                                       	</td>
                                        <td width="10">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVin" id="FilterNumeroVin" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVin']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAll', 'FilterNumeroVin', 'SetNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
                                            </script>
                                     	</td>
                                        <td width="10">&nbsp;</td>
										<td class="tituloMenu"><div align="right">N&uacute;mero Pedido:</div></td>
                                        <td>
                                        	<input name="FilterNumeroPedido" id="FilterNumeroPedido" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroPedido']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                     	</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero interno:</div></td>
                                        <td><input name="FilterIdUnidad" id="FilterIdUnidad" type="text" class="camporFormularioSimple" value="<?=$filter['IdUnidad']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td width="10">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
                                        <td><select name="FilterModelo" id="FilterModelo"  class="camporFormularioSimple">
                                        <option value="">INDISTINTO</option>
                                        <?php foreach ($arrModelos as $oModelo) { ?>
                                        <option value="<?=$oModelo->IdModelo?>" <?php if ($oModelo->IdModelo == $filter['IdModelo']) echo "selected='selected'"; ?> ><?=$oModelo->DenominacionComercial?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td width="10">&nbsp;</td>
										<td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td><select name="FilterEstado" id="FilterEstado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrEstadosUnidad as $oEstadoUnidad) { ?>
                                        <option value="<?=$oEstadoUnidad->IdEstado?>" <?php if ($oEstadoUnidad->IdEstado == $filter['IdEstado']) echo "selected='selected'"; ?> ><?=$oEstadoUnidad->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                                    </tr>
									<tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                                    </tr>
									<tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                                        <td align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                    </tr>
									<tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                </div>	
				</form>				
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
                        <td><span><strong>Seleccione las undidades que desea pagar.</strong></span>
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
				<form name="frmData1" id="frmData1" method="post" action="minutaspago_add_paso2.php">
					<input type="hidden" id="MainAction" name="MainAction" value="Select" />
					<input type="hidden" id="Submitted" name="Submitted" value="1" />
					<input type="hidden" id="IdMinutaPago" name="IdMinutaPago" value="<?= $IdMinutaPago ?>" />
				<table width="100%" align="center" cellpadding="0" cellspacing="0" class="borderGris">
					<tr>
						<td width="33%" align="center">Disponible: $ <?= number_format($oMinutaPago->MontoDisponible, 2, '.', '') ?></td>
						<td width="34%" align="center">Acumulado Seleccionado: $ <label id="total-seleccionado">0.00</label></td>
						<td width="33%" align="right"><input type="submit" name="button1" id="button1" class="botonBasico" style="float: right" value="Confirmar"></td>
					</tr>
				</table>
						
						<br />
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td>&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Interno</strong></div></td>                        
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>						
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>A&ntilde;o</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>						
                        <td width="84" height="25"  class="bordeGrisTitulo">&nbsp;</td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) 
				{
                    $oModelo = $oModelos->GetById($oUnidad->IdModelo);
                    $oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
                    $oEstadoUnidad = $oEstadosUnidad->GetById($oUnidad->IdEstado);
					$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
					$oColor	= $oColores->GetById($oUnidad->IdColor);
					$cliente = '';
					if ($oMinuta = $oMinutas->GetById($oUnidad->IdUnidad))
					{
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$cliente = $oCliente->RazonSocial;
					} elseif ($oUnidad->IdClientePlan)
					{
						$oCliente = $oClientes->GetById($oUnidad->IdClientePlan);
						$cliente = $oCliente->RazonSocial;
					}
					
					$TotalPagado = $oMinutasPagoItems->GetPagadoByIdUnidad($oUnidad->IdUnidad);
					$ImporteAAbonar = $oUnidad->ImporteNotaCredito - $TotalPagado;
				?>          
                    <tr onMouseOver="bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80': ($oUnidad->Pisado ? '#ADECDF' : '#f3f3f3') ?>'" onMouseOut="bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80':($oUnidad->Pisado ? '#ADECDF' : '') ?>'" bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80':($oUnidad->Pisado ? '#ADECDF' : '') ?>'>
                        <td bgcolor="<?=$oEstadoUnidad->Color?>" width="7">&nbsp;</td>
                        <td width="70" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>                        
                        <td width="70" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="261" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						<td width="150" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="100" height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>						
                        <td width="50" height="25"><div id="margen"><?=$oUnidad->Anio?></div></td>
						<td width="91" height="25"><div id="margen"><?=$cliente?></div></td>
						<td width="91" height="25"><div id="margen"><?=$oEstadoUnidad->Nombre?></div></td>
                        <td width="84" height="25" valign="middle">
                            <div align="center">
								<input class="check-change" type="checkbox" id="IdUnidad[]" name="IdUnidad[]" importe="<?= $ImporteAAbonar ?>" value="<?= $oUnidad->IdUnidad ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="13">
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