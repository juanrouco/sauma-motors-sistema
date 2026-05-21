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
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
	$filter['FechaDesde'] 		= trim($_REQUEST['FilterFechaDesde']);	
	$filter['FechaHasta'] 		= trim($_REQUEST['FilterFechaHasta']);	
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

$arrModelos	= $oModelos->GetAllModelos();


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&PageSize=' 			. $PageSize;
$strParams.= '&FilterUbicacion=' 	. $filter['IdUbicacion'];
$strParams.= '&FilterFechaDesde=' 	. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 	. $filter['FechaHasta'];

$arrUbicaciones 	= $oUbicaciones->GetAll();

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
	window.location.href = 'unidades_reporte_quincenal.php?MainAction=<?=$Action?>';
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
						<td>&nbsp;</td>
						<td width="30"><div align="center"><img src="images/iconos/imprimir.png" alt="Imprimir" border="0"></div></td>
						<td width="30"><div align="center"><a href="#" onclick="printFrame(); return false;">Imprimir</a></div></td>
						<td>&nbsp;</td>
						<td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Imprimir" border="0"></div></td>
						<td width="30"><div align="center"><a href="unidades_reporte_quincenal_exportar.php<?= $strParams ?>">Exportar</a></div></td>
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
                                        <td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterUbicacion" id="FilterUbicacion"  class="camporFormularioMediano">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
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
    <?php if ($arrModelos != NULL) { ?>        
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
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad Vendidas</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad en Stock</strong></div></td>				
						<td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cantidad Recibidas</strong></div></td>				
                    </tr>
          
                <?php foreach ($arrModelos as $oModelo) 
				{
					$filterVendido = array();
					$filterVendido['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
					$filterVendido['FechaDesde'] 		= $filter['FechaDesde'];	
					$filterVendido['FechaHasta'] 		= $filter['FechaHasta'];
					$filterVendido['IdModelo'] 			= $oModelo->IdModelo;
					$TotalVendido 	= $oUnidades->GetTotalReporteVendidos($filterVendido);	
					$filterVendido['FechaDesde'] 		= null;	
					$filterVendido['FechaHasta'] 		= null;
					$TotalStock 	= $oUnidades->GetTotalReporteStock($filterVendido);
					$filterVendido['FechaArriboEstimadaDesde'] 		= $filter['FechaDesde'];	
					$filterVendido['FechaArriboEstimadaHasta'] 		= $filter['FechaHasta'];
					$TotalRecibido 	= $oUnidades->GetTotalReporteRecibido($filterVendido);	
                ?>
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="7">&nbsp;</td>
						<td width="40%" height="25"><div id="margen" align="left"><?= $oModelo->DenominacionComercial ?></div></td>  
						<td width="20%" height="25"><div id="margen" align="center"><?= $TotalVendido->CantidadTotal ?></div></td>  
						<td width="20%" height="25"><div id="margen" align="center"><?= $TotalStock->CantidadTotal ?></div></td>  
						<td width="20%" height="25"><div id="margen" align="center"><?= $TotalRecibido->CantidadTotal ?></div></td>  
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