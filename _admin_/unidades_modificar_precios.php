<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ACTUALIZAR_PRECIO))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$Action 		= strval($_REQUEST['MainAction']);
$PageSize 	= 1000;
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVinPrefijo'] = trim($_REQUEST['FilterNumeroVinPrefijo']);
	$filter['DenominacionComercial'] 	= trim($_REQUEST['FilterDenominacion']);
	$filter['IdTipoModelo'] 	= trim($_REQUEST['FilterTipoModelo']);
	$filter['IdMarcaVehiculo'] 	= trim($_REQUEST['FilterMarcaVehiculo']);
	$filter['ConStock'] 		= trim($_REQUEST['FilterConStock']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* declaracion de variables */
$arrData 			= array();
$oModelos 			= new Modelos();
$oTiposModelo 		= new TiposModelo();
$oCategoriasModelo 	= new CategoriasModelo();
$oMarcas 			= new Marcas();
$oPage 				= new Page($Page, $PageSize);

$filter['Obsoleto'] = '0';

$Paginado	= Pageable::PrintPaginator($oPage, $oModelos->GetCountRows($filter), true);
$arrData 	= $oModelos->GetAll($filter, $oPage);

if ($Submit)
{
	if ($Action == 'Actualizar')
	{
		if ($arrData)
		{
			$PorcentajeActualizacion = floatval($_REQUEST['PorcentajeActualizacion']);
			foreach ($arrData as $oModelo)
			{
				if (isset($_REQUEST['PrecioPublicoNeto_' . $oModelo->IdModelo]))
				{
					$PrecioPublicoNeto 		= floatval($_REQUEST['PrecioPublicoNeto_' . $oModelo->IdModelo]);
					$ImpuestoInterno		= floatval($_REQUEST['ImpuestoInterno_' . $oModelo->IdModelo]);
					$PrecioPublicoTotalIva	= floatval($_REQUEST['PrecioPublicoTotalIva_' . $oModelo->IdModelo]);
					$Flete					= floatval($_REQUEST['Flete_' . $oModelo->IdModelo]);
					$PrecioCompra			= floatval($_REQUEST['PrecioCompra_' . $oModelo->IdModelo]);
					$ReventaPrecio			= floatval($_REQUEST['ReventaPrecio_' . $oModelo->IdModelo]);
					$Precio1				= floatval($_REQUEST['Precio1_' . $oModelo->IdModelo]);
					$Precio2				= floatval($_REQUEST['Precio2_' . $oModelo->IdModelo]);
					$Patentamiento			= floatval($_REQUEST['Patentamiento_' . $oModelo->IdModelo]);
					$ReventaPrecio			= floatval($_REQUEST['ReventaPrecio_' . $oModelo->IdModelo]);
					$Prenda					= floatval($_REQUEST['Prenda_' . $oModelo->IdModelo]);
					$BonificacionExtra		= floatval($_REQUEST['BonificacionExtra_' . $oModelo->IdModelo]);
					$DescuentoReventa		= floatval($_REQUEST['DescuentoReventa_' . $oModelo->IdModelo]);
					$Otorgamiento			= floatval($_REQUEST['Otorgamiento_' . $oModelo->IdModelo]);
					$FleteFormularios		= floatval($_REQUEST['FleteFormularios_' . $oModelo->IdModelo]);
					
					$oModelo->PrecioPublicoNeto		= $PrecioPublicoNeto;
					$oModelo->ImpuestoInterno		= $ImpuestoInterno;
					$oModelo->PrecioPublicoTotalIva = $PrecioPublicoTotalIva;
					$oModelo->Flete					= $Flete;
					$oModelo->PrecioCompra			= $PrecioCompra;
					$oModelo->Precio1				= $Precio1;
					$oModelo->Precio2				= $Precio2;
					$oModelo->Patentamiento			= $Patentamiento;
					$oModelo->ReventaPrecio			= $ReventaPrecio;
					$oModelo->Prenda				= $Prenda;
					$oModelo->BonificacionExtra		= $BonificacionExtra;
					$oModelo->DescuentoReventa		= $DescuentoReventa;
					$oModelo->Otorgamiento			= $Otorgamiento;
					$oModelo->FleteFormularios		= $FleteFormularios;
					
					$oModelos->Update($oModelo);
				}
			}
		}
		
		header('Location: unidades.php');
		exit;
	}
}

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrTiposModelo = $oTiposModelo->GetAll();
$arrMarcas = $oMarcas->GetAll();

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

function Actualizar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;
		
	frmData.MainAction.value = 'Actualizar';

	frmData.Page.value = 0;
	frmData.submit();
}

function ClearFilter()
{	
	window.location.href = 'unidades_actualizar_precios.php';
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
</script>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">
$j(document).ready(function() {
	$j('.numerico').keydown(function(event) {
	    // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
			 (event.keyCode == 190 || event.keyCode == 188) || 
             // Allow: home, end, left, right
			 (event.keyCode == 189) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
	$j(".numerico").on('input',function() {
		if ($j(this).hasClass('final'))
			return;
		var IdModelo = $j(this).attr('data');
		var Neto = parseFloat($j('#PrecioPublicoNeto_' + IdModelo).val());
		var ImpuestoInterno = parseFloat($j('#ImpuestoInterno_' + IdModelo).val());
		if (!ImpuestoInterno)
			ImpuestoInterno = 0;
		var Iva = Neto * 0.21;
		$j('#PrecioPublicoTotalIva_' + IdModelo).val(Iva.toFixed(2));
		//$j('#Flete_' + IdModelo).val((<?= Config::Flete ?>).toFixed(2));
		// $j('#Prenda_' + IdModelo).val((Neto * <?= Config::Seguro ?>).toFixed(2));
		// $j('#Otorgamiento_' + IdModelo).val(<?= Config::Formularios ?>);
		var Flete = parseFloat($j('#Flete_' + IdModelo).val());
		var Seguro = parseFloat($j('#Prenda_' + IdModelo).val());
		var Formularios = parseFloat($j('#Otorgamiento_' + IdModelo).val());
		
		// $j('#BonificacionExtra_' + IdModelo).val(((Neto + Flete + Seguro) * <?= Config::PercepcionIIBB ?>).toFixed(2));
		$j('#DescuentoReventa_' + IdModelo).val(((Neto + Flete + Seguro) * <?= Config::RetencionIVA ?>).toFixed(2));
		var PercepcionIIBB = parseFloat($j('#BonificacionExtra_' + IdModelo).val());
		var RetencionIVA = parseFloat($j('#DescuentoReventa_' + IdModelo).val());
		
		var Galpon = ((Neto + Flete + Seguro + PercepcionIIBB + RetencionIVA + Iva + Formularios) + ImpuestoInterno);
	
		$j('#PrecioCompra_' + IdModelo).val(Galpon.toFixed(2));
		
	});
});

</script>


</head>
<body>

<form name="frmData" id="frmData" method="post"  enctype="multipart/form-data">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="MainAction" id="MainAction" value="<?= $Action ?>" />
    <input type="hidden" name="Id" id="Id" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Modelos - Actualizar Precios</span></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td height="30" valign="middel">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Importar XLS" border="0"></div></td>
                                    <td><a href="modelos_precios_importar.php<?=$strParams?>">Importar Precios</a></td>
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
                                        <td class="tituloMenu"><div align="right">Tipo:</div></td>
                                        <td><select name="FilterTipoModelo" id="FilterTipoModelo"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrTiposModelo as $oTipoModelo) { ?>
                                        <option value="<?=$oTipoModelo->IdTipoModelo?>" <?php if ($oTipoModelo->IdTipoModelo == $filter['IdTipoModelo']) echo "selected='selected'"; ?> ><?=$oTipoModelo->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Marca Vehic.:</div></td>
                                        <td><select name="FilterMarcaVehiculo" id="FilterMarcaVehiculo"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <?php foreach ($arrMarcas as $oMarca) { ?>
                                        <option value="<?=$oMarca->IdMarca?>" <?php if ($oMarca->IdMarca == $filter['IdMarca']) echo "selected='selected'"; ?> ><?=$oMarca->Nombre?></option>
                                        <?php } ?>
                                        </select></td>
                                        <td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Stock:</div></td>
                                        <td><select name="FilterConStock" id="FilterConStock"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['ConStock']) echo "selected='selected'"; ?> >Sin Stock</option>
                                        <option value="1" <?php if ('1' == $filter['ConStock']) echo "selected='selected'"; ?> >Con Stock</option>
                                        </select></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">Prefijo Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVinPrefijo" id="FilterNumeroVinPrefijo" type="text" class="camporFormularioSimple" value="<?=$filter['NumeroVinPrefijo']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Modelos', 'GetAll', 'FilterNumeroVinPrefijo', 'SetNumeroVinPrefijo', 'IdModelo', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', null);
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Denominaci&oacute;n:</div></td>
                                        <td><input name="FilterDenominacion" id="FilterDenominacion" type="text" class="camporFormularioSimple" value="<?=$filter['DenominacionComercial']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);"></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
										<td align="right"><input type="button" name="button" id="button" class="botonBasico" value="Buscar" onclick="Filtrar();"></td>
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
          
    <?php if ($arrData != NULL) { /* ?>
            
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><div align="center"><strong>Actualizar la lista filtrada un </strong><input type="text" class="numerico final" id="PorcentajeActualizacion" name="PorcentajeActualizacion" value="<?= ($PorcentajeActualizacion) ?>" />%</div></td>
                    </tr>
                </table>
            </td>
        </tr> */ ?>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Comercial</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Neto</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Impuesto</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Flete</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Seguro</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Formularios</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Galp&oacute;n</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fact</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cont</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Cr&eacute;dito</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>FyF</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Arancel Reg.</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Gest. Olivos</strong></div></td>
                        <td width="84" height="25"  class="bordeGrisTitulo"><div id="margen" align="center"><strong>Gest. Otro</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oModelo) { ?>
                    <?php $oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo); ?>
                    <?php $oCategoriaModelo = $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo); ?>
                    <?php $oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="150" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?><br>(<?= $oModelo->NumeroVinPrefijo ?>)</div></td>
                        <td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="PrecioPublicoNeto_<?= $oModelo->IdModelo ?>" name="PrecioPublicoNeto_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->PrecioPublicoNeto ?>" />
							</div>
						</td>
						<td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="ImpuestoInterno_<?= $oModelo->IdModelo ?>" name="ImpuestoInterno_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->ImpuestoInterno ?>" />
							<input type="hidden" id="PrecioPublicoTotalIva_<?= $oModelo->IdModelo ?>" name="PrecioPublicoTotalIva_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->PrecioPublicoTotalIva ?>" readonly="readonly" />
							
							</div>
						</td>
						<td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="Flete_<?= $oModelo->IdModelo ?>" name="Flete_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Flete ?>" />
							</div>
						</td>
						<td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="BonificacionExtra_<?= $oModelo->IdModelo ?>" name="BonificacionExtra_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->BonificacionExtra ?>" />
							<input type="hidden" id="Prenda_<?= $oModelo->IdModelo ?>" name="Prenda_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Prenda ?>" />
							<input type="hidden" id="DescuentoReventa_<?= $oModelo->IdModelo ?>" name="DescuentoReventa_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->DescuentoReventa ?>" />
							</div>
						</td>
						<td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="Otorgamiento_<?= $oModelo->IdModelo ?>" name="Otorgamiento_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Otorgamiento ?>" />
							</div>
						</td>
						<td width="80" height="25">
							<div id="margen" align="center">
							$<input type="text" id="PrecioCompra_<?= $oModelo->IdModelo ?>" name="PrecioCompra_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico final" value="<?= $oModelo->PrecioCompra ?>" readonly="readonly" />
							</div>
						</td>
						<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="ReventaPrecio_<?= $oModelo->IdModelo ?>" name="ReventaPrecio_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->ReventaPrecio ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="Precio1_<?= $oModelo->IdModelo ?>" name="Precio1_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Precio1 ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="Precio2_<?= $oModelo->IdModelo ?>" name="Precio2_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Precio2 ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="FleteFormularios_<?= $oModelo->IdModelo ?>" name="FleteFormularios_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->FleteFormularios ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="Patentamiento_<?= $oModelo->IdModelo ?>" name="Patentamiento_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->Patentamiento ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="Patentamiento3_<?= $oModelo->IdModelo ?>" name="Patentamiento3_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->IdTipoModelo == 39 || $oModelo->IdTipoModelo == 40 ? 0 : Config::GestoriaLibertador ?>" />
										</div>
									</td>
									<td width="80" height="25">
										<div id="margen" align="center">
										$<input type="text" id="Patentamiento4_<?= $oModelo->IdModelo ?>" name="Patentamiento4_<?= $oModelo->IdModelo ?>" data="<?= $oModelo->IdModelo ?>" style="width: 60px" class="camporFormularioMuyChico numerico" value="<?= $oModelo->IdTipoModelo == 39 || $oModelo->IdTipoModelo == 40 ? 0 : Config::GestoriaOtro ?>" />
										</div>
									</td>
                    </tr>
                    <tr>
                        <td colspan="8">
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
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><div align="right"><input type="button" name="btnActualizar" id="btnActualizar" class="botonBasico" value="Actualizar" onclick="Actualizar();"></div></td>
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