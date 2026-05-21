<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_USADOS_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= isset($_REQUEST['PageSize']) ? intval($_REQUEST['PageSize']) : 100;
$Action 	= strval($_REQUEST['MainAction']);
$Option 	= strval($_REQUEST['Option']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['IdUsado'] 		= trim($_REQUEST['FilterIdUsado']);
	$filter['IdMarca'] 		= trim($_REQUEST['FilterMarca']);
	$filter['Modelo'] 		= trim($_REQUEST['FilterModelo']);
	$filter['Dominio'] 		= trim($_REQUEST['FilterDominio']);
	$filter['IdEstado'] 	= trim($_REQUEST['FilterIdEstado']);
	$filter['IdUbicacion'] 	= trim($_REQUEST['FilterIdUbicacion']);
	$filter['Consignacion']	= trim($_REQUEST['FilterConsignacion']);
	$filter['AnioDesde']	= trim($_REQUEST['FilterAnioDesde']);
	$filter['AnioHasta']	= trim($_REQUEST['FilterAnioHasta']);
	
	if ($Action == 'Select')
	{
		$filter['IdEstado'] = EstadoUnidad::Stock;
		
		if ($Option == 'Recepcion')
		{
			$filter['IdUbicacion'] = Ubicacion::Transito;
			$filter['IdEstado'] = '';
		}
		
		$PageSize = 1000;
	}
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 		= array();
$oUsados 		= new Usados();
$oMarcas 		= new Marcas();
$oColores 		= new Colores();
$oUbicaciones 	= new Ubicaciones();
$oEstadosUnidad = new EstadosUnidad();
$oMinutas		= new Minutas();
$oMinutasUsados	= new MinutasUsados();
$oUsadosArreglos= new UsadosArreglos();
$oRecepcionesUsados = new RecepcionesUsados();

$oPage 		= new Page($Page, $PageSize);

$Paginado	= Pageable::PrintPaginator($oPage, $oUsados->GetCountRows($filter), true);
$arrData 	= $oUsados->GetAll($filter, $oPage);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);
if (Session::CheckPerm(PERM_UNID_CREATE))
	$strParams.= '&fullpermisos=1';

$filterEstado = array();
$arrMarcas = $oMarcas->GetAll();
$arrUbicaciones	= $oUbicaciones->GetAll();
$arrEstadosUnidad = $oEstadosUnidad->GetAll($filterEstado);

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
	window.location.href = 'usados.php';
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

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Usados</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
						<td width="25%" height="40">
                            <table border="0" align="cener" cellpadding="0" cellspacing="0">
                                <tr>
									<?php
									if (Session::CheckPerm(PERM_USADOS_UPDATE)){
									?>
                                    <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar PDF" border="0"></div></td>
                                    <td><a href="usados_add.php<?=$strParams?>">Agregar</a></td>
                                    <td width="10">&nbsp;</td>
									
									<?php
									}
									
									if (Session::CheckPerm(PERM_USADOS_REPORT)){
									?>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar Hist&oacute;rico PDF" border="0"></div></td>
                                    <td><a href="usados_exportar_historico.php<?=$strParams?>">Exportar Hist&oacute;rico</a></td>
									
									<?php
									}
									?>
                                </tr>
                            </table>
                        </td>
						<td width="25%" height="40">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
							<?php
									if (Session::CheckPerm(PERM_USADOS_LIST)){
									?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar Stock PDF" border="0"></div></td>
                                    <td><a href="usados_exportar_stock_pdf.php<?=$strParams?>">Exportar Stock PDF</a></td>
                                </tr>
								<?php
									}
									?>
                            </table>
                        </td>
						<td width="25%" height="40">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
							<?php
									if (Session::CheckPerm(PERM_USADOS_REPORT)){
									?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td><a href="usados_exportar_pdf.php<?=$strParams?>">Exportar PDF</a></td>
                                </tr>
								<?php
									}
									?>
                            </table>
                        </td>
                        <td width="25%" height="40">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
							<?php
									if (Session::CheckPerm(PERM_USADOS_REPORT)){
									?>
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
                                    <td><a href="usados_exportar.php<?=$strParams?>">Exportar XLS</a></td>
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
                                        <td class="tituloMenu"><div align="right">N&uacute;mero interno:</div></td>
                                        <td><input name="FilterIdUsado" id="FilterIdUsado" type="text" class="camporFormularioSimple" value="<?=$filter['IdUsado']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Marca:</div></td>
                                        <td><select name="FilterMarca" id="FilterMarca"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">A&ntilde;o Desde:</div></td>
										<td><input name="FilterAnioDesde" id="FilterAnioDesde" type="text" class="camporFormularioSimple" value="<?=$filter['AnioDesde']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
									</tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Modelo:</div></td>
                                        <td><input name="FilterModelo" id="FilterModelo" type="text" class="camporFormularioSimple" value="<?=$filter['Modelo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Dominio:</div></td>
                                        <td><input name="FilterDominio" id="FilterDominio" type="text" class="camporFormularioSimple" value="<?=$filter['Dominio']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">A&ntilde;o Desde:</div></td>
										<td><input name="FilterAnioHasta" id="FilterAnioHasta" type="text" class="camporFormularioSimple" value="<?=$filter['AnioHasta']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                    </tr>
									<tr>                              
										<td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td><select name="FilterIdEstado" id="FilterIdEstado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrEstadosUnidad as $oEstadoUnidad) { ?>
                                        <option value="<?=$oEstadoUnidad->IdEstado?>" <?php if ($oEstadoUnidad->IdEstado == $filter['IdEstado']) echo "selected='selected'"; ?> ><?=$oEstadoUnidad->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Ubicaci&oacute;n:</div></td>
                                        <td><select name="FilterIdUbicacion" id="FilterIdUbicacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                        <option value="<?=$oUbicacion->IdUbicacion?>" <?php if ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) echo "selected='selected'"; ?> ><?=$oUbicacion->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Consignaci&oacute;n:</div></td>
                                        <td><select name="FilterConsignacion" id="FilterConsignacion"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Consignacion']) echo "selected='selected'"; ?> >NO</option>
                                        <option value="1" <?php if ('1' == $filter['Consignacion']) echo "selected='selected'"; ?> >SI</option>
                                        </select></td>
                                    </tr>
									<tr>                              
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><div align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></div></td>
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
			<?php if ($Option == 'Recepcion') { ?>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;">
                    <tr>
                        <td>&nbsp;</td>
                        <td><span><strong>Seleccione el usado que desea vender. Para ello haga clic sobre el s&iacute;mbolo </strong></span> <img src="images/iconos/check.gif" width="16" height="16" border="0" /> <span><strong>del usado correspondiente.</strong></span>
                    </td>
                    </tr>
                </table>
            </td>
        </tr>
			<?php } else { ?>
		<tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;">
                    <tr>
                        <td>&nbsp;</td>
                        <td><span><strong>Seleccione el usado que desea Recepcionar. Para ello haga clic sobre el s&iacute;mbolo </strong></span> <img src="images/iconos/facturacion.png" width="16" height="16" border="0" /> <span><strong>del usado correspondiente.</strong></span>
                    </td>
                    </tr>
                </table>
            </td>
        </tr>
			<?php } ?>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Marca</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>A&ntilde;o</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Kilometraje</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Ubicaci&oacute;n</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Recepci&oacute;n</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Estado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Consig.</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Costo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Info</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Precio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Precio 2</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oUsado) 
					{
						$oMarca = $oMarcas->GetById($oUsado->IdMarca);
						$oColor = $oColores->GetById($oUsado->IdColor);
						$oUbicacion = $oUbicaciones->GetById($oUsado->IdUbicacion);
						$oEstado = $oEstadosUnidad->GetById($oUsado->IdEstado);
						$oRecepcionUsado = $oRecepcionesUsados->GetByIdUsado($oUsado->IdUsado);
						$CarpetaOrigen = '';
						$oMinuta = $oMinutas->GetByIdUsado($oUsado->IdUsado);
						if ($oMinuta)
							$CarpetaOrigen = $oMinuta->IdMinuta;
						else
						{
							$oMinutaUsado = $oMinutasUsados->GetByIdUsadoTomado($oUsado->IdUsado);
							$CarpetaOrigen = 'U-' . $oMinutaUsado->IdUsado;
						}
						
						$arrUsadosArreglos = $oUsadosArreglos->GetAllByUsado($oUsado);

						$TotalArreglos = 0;
						foreach ($arrUsadosArreglos as $oUsadoArreglo)
						{
							$TotalArreglos+= $oUsadoArreglo->Importe;
						}
						
						$PrecioVentaMinimo = $oUsado->Valuacion + $TotalArreglos + Config::SumaUsados;
				?>
          
                    <tr onMouseOver="bgColor='<?= $oUsado->Pisado ? '#ADECDF': ($oUsado->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' : '#f3f3f3') ?>'" onMouseOut="bgColor='<?= $oUsado->Pisado ? '#ADECDF':($oUsado->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' : '') ?>'" bgColor='<?= $oUsado->Pisado ? '#ADECDF':($oUsado->IdEstado == EstadoUnidad::Reservado ? '#F4DA80' : '') ?>'>
                        <td width="100" height="25"><div id="margen" align="center">U-<?=$oUsado->IdUsado?></div></td>
                        <td width="163" height="25"><div id="margen"><?=$oMarca->Nombre?></div></td>
                        <td width="157" height="25"><div id="margen"><?=$oUsado->Modelo?></div></td>
                        <td width="157" height="25"><div id="margen"><?=$oUsado->Dominio?></div></td>
                        <td width="159" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="120" height="25"><div id="margen" align="center"><?=$oUsado->ModeloAnio?></div></td>
                        <td width="120" height="25"><div id="margen" align="center"><?=number_format($oUsado->Kilometraje, 0, ',', '.')?> Km.</div></td>
                        <td width="120" height="25"><div id="margen"><?= $oUbicacion->Nombre ?></div></td>
                        <td width="120" height="25"><div id="margen"><?= CambiarFecha($oRecepcionUsado->Fecha) ?></div></td>
                        <td width="120" height="25"><div id="margen"><?= $oEstado->Nombre ?></div></td>
                        <td width="120" height="25"><div id="margen"><?= $oUsado->Consignacion ? 'SI' : 'NO' ?></div></td>
                        <td width="120" height="25"><div id="margen" align="center">$<?=number_format($PrecioVentaMinimo, 2, ',', '.')?></div></td>
                        <td width="120" height="25"><div id="margen" align="center">$<?=number_format($oUsado->Info, 2, ',', '.')?></div></td>
                        <td width="120" height="25"><div id="margen" align="center"><strong>$<?=number_format($oUsado->PrecioVenta, 2, ',', '.')?></strong></div></td>
                        <td width="120" height="25"><div id="margen" align="center"><strong>$<?=number_format($oUsado->PrecioVenta2, 2, ',', '.')?></strong></div></td>
						<td width="120">
							<div id="margen" align="center">
							<?php
							if ($Action == 'Select' && $Option == 'Recepcion') {
							?>
								<?php if (Session::CheckPerm(PERM_RECEPUS_CREATE)){ ?>
							<a href="recepcionesusados_add.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/check.gif" alt="Recepcion Usado" border="0" /></a> 
								<?php } ?> 
							<?php
							} else {
							?>
							<?php if ($oUsado->IdEstado == EstadoUnidad::Stock || $oUsado->IdEstado == EstadoUnidad::PreVenta) { ?>
								<?php if (Session::CheckPerm(PERM_VENTUS_CREATE)){ ?>
									<?php if ($oUsado->Pisado == '0' || Session::CheckPerm(PERM_UNID_PISAR)){ ?>
                                <a href="minutasusados_add.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/facturacion.png" alt="Registrar Venta" border="0" /></a> - 
                                <?php 
									}
								} ?>
								<?php if (Session::CheckPerm(PERM_USADOS_PISAR)){ ?>
								<a href="usados_pisar.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/permisos.gif" alt="Pisar Usado" border="0" /></a> - 
								 <?php } ?>
							<?php } ?>
								<?php if (Session::CheckPerm(PERM_USADOS_UPDATE)){ ?>
								<a href="usados_mod.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a> - <a href="usadosarreglos.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/adm_general.png" alt="Arreglos" title="Arreglos" border="0" /></a><?php 
if (Session::CheckPerm(PERM_UNID_DELETE)) { ?> - <a href="usados_del.php<?=$strParams?>&IdUsado=<?=$oUsado->IdUsado?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" border="0" /></a><?php } ?>
								<?php } ?>
							<?php } ?>
							</div>
						</td>
					</tr>
                    <tr>
                        <td colspan="12">
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