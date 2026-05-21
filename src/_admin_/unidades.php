<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_STOCK) && !Session::CheckPerm(PERM_UNID_LIST))
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
	$filter['NumeroVinPrefijo'] = trim($_REQUEST['FilterNumeroVinPrefijo']);
	$filter['NumeroVin'] 		= trim($_REQUEST['FilterNumeroVin']);
	$filter['IdUnidad'] 		= trim($_REQUEST['FilterIdUnidad']);
	$filter['Modelo'] 			= trim($_REQUEST['FilterModelo']);
	$filter['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);
	$filter['IdEstado'] 		= trim($_REQUEST['FilterEstado']);
	$filter['NumeroPedido'] 	= trim($_REQUEST['FilterNumeroPedido']);
	$filter['ClienteReventa'] 	= trim($_REQUEST['FilterClienteReventa']);
	$filter['Certificado'] 		= trim($_REQUEST['FilterCertificado']);
	$filter['Cancelado'] 		= trim($_REQUEST['FilterCancelado']);
	$filter['Dominio'] 			= trim($_REQUEST['FilterDominio']);
	$filter['IdProveedor']		= trim($_REQUEST['FilterIdProveedor']);
	$filter['IdMarca']			= trim($_REQUEST['FilterIdMarca']);
	$filter['Consignacion']		= trim($_REQUEST['FilterConsignacion']);
	$filter['NumeroMotor']		= trim($_REQUEST['FilterNumeroMotor']);
	$filter['FechaArriboEstimadaDesde']	= trim($_REQUEST['FilterFechaArriboEstimadaDesde']);
	$filter['FechaArriboEstimadaHasta']	= trim($_REQUEST['FilterFechaArriboEstimadaHasta']);
	$filter['FechaMarchaVencimientoDesde']	= trim($_REQUEST['FilterFechaMarchaVencimientoDesde']);
	$filter['FechaMarchaVencimientoHasta']	= trim($_REQUEST['FilterFechaMarchaVencimientoHasta']);
	$filter['Marcha'] 			= trim($_REQUEST['FilterMarcha']);
	$filter['Conforme'] 		= trim($_REQUEST['FilterConforme']);
	
	/* En caso de querer agregar una venta, solo levanta los autos en stock */
	if ($Action == 'Select')
	{
		$filter['IdEstado'] = array();
		$filter['IdEstado'][0] = EstadoUnidad::Stock;
		$filter['IdEstado'][1] = EstadoUnidad::PreVenta;
		$filter['IdEstado'][2] = EstadoUnidad::Plan;
		$filter['IdEstado'][3] = EstadoUnidad::VentasEspeciales;
		
		if (!Session::CheckPerm(PERM_UNID_REPARAR))
			$filter['Pisado'] = '0';
		$PageSize = 1000;
	}
}
if ($currentUser->IdPerfil == 2)
	$filter['IdUbicacion'] = $currentUser->IdUbicacion;

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
$oProveedores			= new Proveedores();
$oMarcas				= new Marcas();
$oMinutasEspera			= new MinutasEspera();

$oMinutaEspera = $oMinutasEspera->GetById($_REQUEST['IdMinutaEspera']);
if ($oMinutaEspera)
{
	$filter['IdModelo'] = $oMinutaEspera->IdModelo;
}

$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);
$arrData 	= $oUnidades->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);
if (Session::CheckPerm(PERM_UNID_CREATE))
	$strParams.= '&fullpermisos=1';

$arrModelos 		= $oModelos->GetAllOrdered();
$arrUbicaciones 	= $oUbicaciones->GetAll();
$arrEstadosUnidad 	= $oEstadosUnidad->GetAll();
$arrProveedores 	= $oProveedores->GetAll(array('IdRubro' => Rubro::IdVehiculo));
$arrMarcas			= $oMarcas->GetAll();

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

function SetDenominacionComercial(IdUnidad, DenominacionComercial)
{
	Get('FilterModelo').value = DenominacionComercial;
}

</script>

<?php include('include/head.inc.php'); ?>

<script language="javascript" src="../js/jquery.tooltip.js"></script>
<link rel="stylesheet" href="../css/jquery.tooltip.css" />

<script type="text/javascript">

$j(document).ready(function() {
	$j(".tooltip").tooltip({
		bodyHandler: function() {
			return $j('.tooltip_' + $j(this).attr("id-unidad")).html();
		},
		showURL: false
	});
});

</script>
</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />
    <input type="hidden" name="IdPresupuesto" id="IdPresupuesto" value="<?=$_REQUEST['IdPresupuesto'] ?>" />
    <input type="hidden" name="IdMinutaEspera" id="IdMinutaEspera" value="<?=$_REQUEST['IdMinutaEspera'] ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Unidades</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
       
	  	<?php if ($Action != 'Select') { ?>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="20%" height="40">
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <?php if (Session::CheckPerm(PERM_UNID_CREATE)){ ?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="unidades_add.php<?=$strParams?>">Agregar</a></td>
									<td width="10" height="30" >&nbsp;</td>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Agregar" border="0"></div></td>
                                    <td><a href="unidades_importar.php<?=$strParams?>">Importar</a></td>
									<td width="10" height="30" >&nbsp;</td>
                                    <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Facturas" border="0"></div></td>
                                    <td><a href="unidades_factura_carga.php<?=$strParams?>">Cargar Factura</a></td>
									<td width="10" height="30" >&nbsp;</td>
								</tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="15%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
									<?php
									if (Session::CheckPerm(PERM_UNID_CREATE)){
									?>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar Hist&oacute;rico PDF" border="0"></div></td>
                                    <td><a href="unidades_exportar_historico.php<?=$strParams?>">Exportar Hist&oacute;rico</a></td>
									
									<?php
									}
									?>
                                </tr>
                            </table>
                        </td>
						 <td width="15%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar Stock PDF" border="0"></div></td>
                                    <td><a href="modelos_listaprecios_exportar_pdf.php<?=$strParams?>">Lista de Precios</a></td>
                                </tr>
                            </table>
                        </td>
						 <td width="15%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar Stock PDF" border="0"></div></td>
                                    <td><a href="unidades_exportar_stock_pdf.php<?=$strParams?>">Exportar Stock PDF</a></td>
                                </tr>
                            </table>
                        </td>
						<td width="15%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td><a href="unidades_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="16%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="unidades_exportar.php<?=$strParams?>">Exportar XLS</a></td>
                                </tr>
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
										<td class="tituloMenu"><div align="right">Consignaci&oacute;n:</div></td>
                                        <td><select name="FilterConsignacion" id="FilterConsignacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Consignacion']) echo "selected='selected'"; ?> >NO</option>
                                        <option value="1" <?php if ('1' == $filter['Consignacion']) echo "selected='selected'"; ?> >SI</option>
                                        </select></td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero interno:</div></td>
                                        <td><input name="FilterIdUnidad" id="FilterIdUnidad" type="text" class="camporFormularioSimple" value="<?=$filter['IdUnidad']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td width="10">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Marcas:</div></td>
                                        <td>
											<select name="FilterIdMarca" id="FilterIdMarca" type="text" class="camporFormularioSimple">
												<option value="">INDISTINTO</option>
												<?php
												foreach ($arrMarcas as $oMarca)
												{
													$selected = '';
													if ($oMarca->IdMarca == $filter['IdMarca'])
														$selected = 'selected="selected"';
												?>
												<option value="<?= $oMarca->IdMarca ?>" <?= $selected ?>><?= $oMarca->Nombre ?></option>
												<?php
												}
												?>
												
											</select>
                                            </td>
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
                                        <td class="tituloMenu"><div align="right">N&deg; Motor:</div></td>
                                        <td><input name="FilterNumeroMotor" id="FilterNumeroMotor" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroMotor']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            </td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Modelo:</div></td>
                                        <td><input type="text" name="FilterModelo" id="FilterModelo"  class="camporFormularioSimple" value="<?= $filter['Modelo'] ?>" />
                                        <script language="">
                                            SUGGESTRequest('Modelos', 'GetAllModelos', 'FilterModelo', 'SetDenominacionComercial', 'IdModelo', 'DenominacionComercial', 'FilterDenominacionComercial', null);
                                            </script>
										</td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Facturado:</div></td>
                                        <td><select name="FilterCertificado" id="FilterCertificado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Certificado']) echo "selected='selected'"; ?> >Sin Factura</option>
                                        <option value="1" <?php if ('1' == $filter['Certificado']) echo "selected='selected'"; ?> >Con Factura</option>
                                        </select></td>
                                    </tr>
									<tr>
                                        <td class="tituloMenu"><div align="right">Patente:</div></td>
                                        <td><input name="FilterDominio" id="FilterDominio" type="text" class="camporFormularioSimple" value="<?=$filter['Dominio']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            </td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Nro. Pedido:</div></td>
                                        <td>
											<input type="text" name="FilterNumeroPedido" id="FilterNumeroPedido" type="text" class="camporFormularioSimple" value="<?= $filter['NumeroPedido'] ?>" />
												
                                            </td>
										<td>&nbsp;</td>
										
										<td class="tituloMenu"><div align="right">Cancelado:</div></td>
                                        <td><select name="FilterCancelado" id="FilterCancelado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Cancelado']) echo "selected='selected'"; ?> >Sin Cancelar</option>
                                        <option value="1" <?php if ('1' == $filter['Cancelado']) echo "selected='selected'"; ?> >Cancelado</option>
                                        </select></td>
                                    </tr>
									<tr>
                                        <td class="tituloMenu"><div align="right">Fecha Arribo Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaArriboEstimadaDesde" type="text" class="camporFormularioMediano" id="FilterFechaArriboEstimadaDesde" value="<?=$filter['FechaArriboEstimadaDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaArriboEstimadaDesde'});
                                            </script>
                                      	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Arribo Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaArriboEstimadaHasta" type="text" class="camporFormularioMediano" id="FilterFechaArriboEstimadaHasta" value="<?=$filter['FechaArriboEstimadaHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaArriboEstimadaHasta'});
                                            </script>
                                      	</td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td><select name="FilterEstado" id="FilterEstado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrEstadosUnidad as $oEstadoUnidad) { ?>
                                        <option value="<?=$oEstadoUnidad->IdEstado?>" <?php if ($oEstadoUnidad->IdEstado == $filter['IdEstado']) echo "selected='selected'"; ?> ><?=$oEstadoUnidad->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                    </tr>
									<tr>
                                        <td class="tituloMenu"><div align="right">Marcha Vto Desde:</div></td>
                                        <td>
                                            <input name="FilterFechaMarchaVencimientoDesde" type="text" class="camporFormularioMediano" id="FilterFechaMarchaVencimientoDesde" value="<?=$filter['FilterFechaMarchaVencimientoDesde']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaMarchaVencimientoDesde'});
                                            </script>
                                      	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Marcha Vto Hasta:</div></td>
                                        <td>
                                            <input name="FilterFechaMarchaVencimientoHasta" type="text" class="camporFormularioMediano" id="FilterFechaMarchaVencimientoHasta" value="<?=$filter['FechaMarchaVencimientoHasta']?>" size="12" maxlength="12" />
                                            <script language="javascript">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaMarchaVencimientoHasta'});
                                            </script>
                                      	</td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Marcha:</div></td>
                                        <td><select name="FilterMarcha" id="FilterMarcha"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="1" <?php if ($filter['Marcha'] == '1') echo "selected='selected'"; ?> >SI</option>
                                        <option value="0" <?php if ($filter['Marcha'] == '0') echo "selected='selected'"; ?> >NO</option>
                                        </select></td>
                                    </tr>
									<tr>
										<td class="tituloMenu"><div align="right">Conforme:</div></td>
                                        <td><select name="FilterConforme" id="FilterConforme"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="1" <?php if ($filter['Conforme'] == '1') echo "selected='selected'"; ?> >SI</option>
                                        <option value="0" <?php if ($filter['Conforme'] == '0') echo "selected='selected'"; ?> >NO</option>
                                        </select></td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Certificado:</div></td>
                                        <td><select name="FilterCertificado" id="FilterCertificado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="1" <?php if ($filter['Certificado'] == '1') echo "selected='selected'"; ?> >SI</option>
                                        <option value="0" <?php if ($filter['Certificado'] == '0') echo "selected='selected'"; ?> >NO</option>
                                        </select></td>
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
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php if ($arrData != NULL) { ?>
            
        <?php if ($Action == 'Select') { ?>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;">
                    <tr>
                        <td>&nbsp;</td>
                        <td><span><strong>Seleccione la undidad que desea vender. Para ello haga clic sobre el s&iacute;mbolo </strong></span> <img src="images/iconos/facturacion.png" width="16" height="16" border="0" /> <span><strong>de la unidad correspondiente.</strong></span>
                    </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php } ?>

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
                        <td>&nbsp;</td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Interno</strong></div></td>                        
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>						
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>A&ntilde;o</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Pedido</strong></div></td>
                        <td height="25" class="bordeGrisTitulo">&nbsp;</td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<?php
						if ($currentUser->IdPerfil == 18)
						{
						?>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;d Llaves</strong></div></td>
						<?php
						}
						?>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>						
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio</strong></div></td>							
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Acreditado</strong></div></td>							
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" ><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) 
				{
                    $oModelo = $oModelos->GetById($oUnidad->IdModelo);
                    $oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
                    $oEstadoUnidad = $oEstadosUnidad->GetById($oUnidad->IdEstado);
					$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);
					$oColor	= $oColores->GetById($oUnidad->IdColor);
					$cliente = '';
					$oMinuta = $oMinutas->GetById($oUnidad->IdUnidad);
					if ($oMinuta && $oUnidad->IdEstado != EstadoUnidad::Stock)
					{
						
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$cliente = $oCliente->RazonSocial;
					} elseif ($oUnidad->IdClientePlan)
					{
						$oCliente = $oClientes->GetById($oUnidad->IdClientePlan);
						$cliente = $oCliente->RazonSocial;
					}
				?>          
                    <tr onMouseOver="bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' :($oUnidad->Pisado ? '#ADECDF': '#f3f3f3') ?>'" onMouseOut="bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' : ($oUnidad->Pisado ? '#ADECDF': '') ?>'" bgColor='<?= $oUnidad->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' : ($oUnidad->Pisado ? '#ADECDF':'') ?>'>
                        <td bgcolor="<?=$oEstadoUnidad->Color?>" width="7">&nbsp;</td>
                        <td width="70" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>                        
                        <td width="70" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="261" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						<td width="150" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="100" height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>						
                        <td width="50" height="25"><div id="margen"><?=$oUnidad->Anio?></div></td>
                        <td width="50" height="25"><div id="margen"><?=$oUnidad->NumeroPedido?></div></td>
                        <td width="60" height="25">
							<div id="margen" style="color: green; font-weight: bold">
								<?= $oUnidad->Cancelada ? 'P' : '' ?><?= $oUnidad->Cancelada && $oUnidad->Certificado ? ' - ' : '' ?><?= $oUnidad->Certificado ? 'F' : '' ?>
							</div>
						</td>
						<td width="91" height="25"><div id="margen"><?=$cliente?></div></td>
						<?php
						if ($currentUser->IdPerfil == 18)
						{
						?>
						<td width="91" height="25"><div id="margen"><?= $oUnidad->CodigoLlaves ?></div></td>
						<?php
						}
						?>
                        <td width="91" height="25"><div id="margen"><?=$oEstadoUnidad->Nombre?></div></td>
                        <td width="91" height="25"><div id="margen"><strong>$<?=number_format($oModelo->Precio1, 2)?></strong></div></td>
                        <td width="91" height="25"><div id="margen"><strong>$<?=number_format($oMinuta ? $oMinuta->GetTotalAcreditado() : 0, 2)?></strong></div></td>
                        <td width="84" height="25" valign="middle">
                            <div align="center">
                            <?php if ($oUnidad->IdEstado == EstadoUnidad::Stock || $oUnidad->IdEstado == EstadoUnidad::PreVenta) { ?>
                                <?php if (Session::CheckPerm(PERM_VENT_CREATE)){ ?>
									<?php if ($oUnidad->Pisado == '0' || Session::CheckPerm(PERM_UNID_PISAR)){ ?>
										<?php if ((!isset($_REQUEST['IdPresupuesto']) || $_REQUEST['IdPresupuesto'] == '') && (!isset($_REQUEST['IdMinutaEspera']) || $_REQUEST['IdMinutaEspera'] == '')){ ?>
                                <a href="minutas_add.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" title="Registrar Venta" border="0" /></a> - 
                                <?php 	} elseif (isset($_REQUEST['IdPresupuesto']) && $_REQUEST['IdPresupuesto'] != ''){ ?>
								<a href="minutas_presupuestos_add.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>&IdPresupuesto=<?= $_REQUEST['IdPresupuesto'] ?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" border="0" /></a> - 
                                <?php 
										} elseif ($_REQUEST['IdMinutaEspera'] != '') {
								?>
								<a href="minutasespera_crear_minuta.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>&IdMinutaEspera=<?= $_REQUEST['IdMinutaEspera'] ?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" border="0" /></a> - 
								<?php
										}
									}
								} ?>
								<?php if (Session::CheckPerm(PERM_UNID_PISAR)){ ?>
								<a class="tooltip" id-unidad="<?= $oUnidad->IdUnidad ?>" href="unidades_pisar.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/permisos.gif" alt="Pisar Unidad" title="Pisar Unidad" border="0" /></a> - 
								 <?php } ?>
                            <?php } elseif ($oUnidad->IdEstado == EstadoUnidad::Plan) { ?>
								<?php if (Session::CheckPerm(PERM_VENT_CREATE)){ ?>
								<a href="minutas_plan_add.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" border="0" /></a> - 
								<?php } ?>
                           <?php } elseif ($oUnidad->IdEstado == EstadoUnidad::VentasEspeciales) { ?>
								<?php if (Session::CheckPerm(PERM_VENT_CREATE)){ ?>
								<a href="minutas_ventaespecial_add.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" border="0" /></a> - 
								<?php } ?>
                            <?php } ?>
						<?php if ($Action != 'Select') { ?>
								<a href="unidades_detail.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/preview.png" alt="Detalle" title="Detalle" border="0" /></a> - 
                            <?php if (Session::CheckPerm(PERM_UNID_UPDATE)){ ?>
								<a href="unidades_mod.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/mod.gif" title="Modificar" alt="Modificar" border="0" /></a> - 
								<a href="unidadesarreglos.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/adm_general.png" alt="Arreglos" border="0" /></a> - 
							<?php if ($oUnidad->IdEstado == EstadoUnidad::PreVenta || $oUnidad->IdEstado == EstadoUnidad::PreVentaReservado) { ?>
								<a href="unidades_asignar.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/auto_modelo.png" alt="Asignar" border="0" /></a> - 
							<?php } ?>
                            <?php } ?>
                            <?php if (Session::CheckPerm(PERM_UNID_DELETE)){ ?>
                                <a href="unidades_del.php<?=$strParams?>&IdUnidad=<?=$oUnidad->IdUnidad?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                            <?php } ?>
						<?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="14">
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
<?php foreach ($arrData as $oUnidad) 
{
?>
	<div class="tooltip_<?= $oUnidad->IdUnidad ?>" style="display:none; width: 150px">
	<?php
		if ($oUnidad->Pisado)
		{
	?>
		<table align="center" width="200" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<tr>
				<td><div align="center"><strong>La unidad se encuentra pisada.</strong></div></td>
			</tr>
			<tr>
				<td><div align="center"><strong><?= $oUnidad->Comentarios ?></strong></div></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	<?php
		}
		else
		{
	?>
		<table align="center" width="200" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<tr>
				<td><div align="center"><strong>La unidad no se encuentra pisada.</strong></div></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	<?php
		}
	?>
	</div>
<?php
}
?>

</body>
</html>