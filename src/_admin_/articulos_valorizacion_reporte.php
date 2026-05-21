<?php 

require_once('../inc_library.php'); 


/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['Codigo'] 		= $_REQUEST['FilterCodigo'];
$filter['Descripcion']	= $_REQUEST['FilterDescripcion'];
$filter['IdProveedor']	= $_REQUEST['FilterIdProveedor'];
$filter['ClasePieza']	= $_REQUEST['FilterClasePieza'];
$filter['IdUbicacion']	= $_REQUEST['FilterIdUbicacion'];
$filter['Catalogo']		= $_REQUEST['FilterCatalogo'];
$filter['ConStock']		= '1';

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}



/* declaracion de variables */
$arr = array();

$Proveedores 	= new Proveedores();
$Articulos 		= new Articulos();
$Ubicaciones 	= new Ubicaciones();
$ArticuloStocks	= new ArticuloStocks();
$Ivas			= new Ivas();
$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arrProveedores = $Proveedores->GetAll();

$filter['IdProveedor'] = '33';
$oReporteTotalFord 	= $Articulos->GetTotalReporte($filter);
$oReporteConMovimientoFord = $Articulos->GetTotalReporteRotacion(1, $filter);
$oReporteLentoFord = $Articulos->GetTotalReporteRotacion(2, $filter);

$filter['IdProveedor'] = '';
$filter['NotIdProveedor'] = '33';
$oReporteTotalTerceros 	= $Articulos->GetTotalReporte($filter);
$oReporteConMovimientoTerceros = $Articulos->GetTotalReporteRotacion(1, $filter);
$oReporteLentoTerceros = $Articulos->GetTotalReporteRotacion(2, $filter);

$arrUbicaciones = $Ubicaciones->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterCodigo=' 		. $filter['Codigo'];
$strParams.= '&FilterDescripcion=' 	. $filter['Descripcion'];
$strParams.= '&FilterIdProveedor='	. $filter['IdProveedor'];
$strParams.= '&FilterClasePieza='	. $filter['ClasePieza'];
$strParams.= '&FilterIdUbicacion='	. $filter['IdUbicacion'];
$strParams.= '&FilterCatalogo='		. $filter['Catalogo'];
$strParams.= '&FilterConStock='		. $filter['ConStock'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript" src="../js/jquery.tooltip.js"></script>
<link rel="stylesheet" href="../css/jquery.tooltip.css" />
<script language="javascript">

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
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

function ClearCampos()
{
	var frmData = Get('frmData');

	frmData.FilterCodigo.value 		= '';
	frmData.FilterDescripcion.value = '';
	frmData.FilterIdProveedor.value = '';
	frmData.FilterClasePieza.value 	= '';
	frmData.FilterIndustria.value 	= '';
	frmData.FilterCatalogo.value 	= '';
	frmData.FilterConStock.checked 	= false;
	
	return true;
}

$j(document).ready(function() {
	$j(".tooltip").tooltip({
		bodyHandler: function() {
			return $j('.' + $j(this).attr("id")).html();
		},
		showURL: false
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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Reporte Valorizaci&oacute;n Repuestos en Stock</span></td>
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
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
				<input type="hidden" name="MainAction" id="MainAction">
				<input type="hidden" name="Id" id="Id">
				<input type="hidden" name="filtroActivo" id="filtroActivo" value="1">
                <div align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1"><div align="center"></div></td>
                    </tr>
                  </table>
                </div>
				<div id="FilterMain"class="">
				<div id="Filter" >
					<table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td><span class="tituloPagina">Filtro</span></td>
						</tr>
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						  <td class="tituloMenu"><table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tr>
                              
                              <td class="tituloMenu">Ubicaci&oacute;n:</td>
								<td width="270">
									<select name="FilterIdUbicacion" id="FilterIdUbicacion" class="camporFormularioSuggest">
										<option value="" >Indistinto</option>
										<?php if ($arrUbicaciones){ ?>
											<?php foreach ($arrUbicaciones as $oUbicacion) { ?>
												<option value="<?=$oUbicacion->IdUbicacion?>" <?php echo ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) ? "selected='selected'" : "" ?> >
													<?=$oUbicacion->Nombre;?>
												</option>
											<?php } ?>
										<?php } ?>
									</select>
								</td>
                              <td>&nbsp;</td>
							  <td valign="middle">
									<div align="left">
										<input type="submit" name="button" id="button" class="botonBasico" value="Buscar" />
									</div>
								</td>
                            </tr>
                          </table></td>
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
	<tr>
    	<td>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Valorizaci&oacute;n</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Sin IVA</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Unidades</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Activo Honda (S/I)</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Un. Activo Honda</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Lento Honda (S/I)</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Un. Lento Honda</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Sin Movimiento Honda (S/I)</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Un. Sin Movimiento Honda</strong></div></td>					
				</tr>
      
          
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><strong>HONDA</strong></div></td>
	                <td height="25"><div id="margen">$<?= number_format($oReporteTotalFord->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteTotalFord->StockTotal ?></div></td>	
					<td height="25"><div id="margen">$<?= number_format($oReporteConMovimientoFord->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteConMovimientoFord->StockTotal ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteLentoFord->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteLentoFord->StockTotal ?></div></td>
					<td height="25"><div id="margen">$0,00</div></td>
	                <td height="25"><div id="margen">0</div></td>
				</tr>
	  			<tr>
        			<td colspan="9"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><strong>TERCEROS</strong></div></td>
	                <td height="25"><div id="margen">$<?= number_format($oReporteTotalTerceros->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteTotalTerceros->StockTotal ?></div></td>	
					<td height="25"><div id="margen">$<?= number_format($oReporteConMovimientoTerceros->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteConMovimientoTerceros->StockTotal ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($oReporteLentoTerceros->CostoTotal, 2, ',', '.') ?></div></td>
	                <td height="25"><div id="margen"><?= $oReporteLentoTerceros->StockTotal ?></div></td>	
<td height="25"><div id="margen">$0,00</div></td>
	                <td height="25"><div id="margen">0</div></td>					
				</tr>				
	  			<tr>
        			<td colspan="9"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<tr bgColor='#f3f3f3'>
                	<td height="25"><div id="margen"><strong>TOTAL</strong></div></td>
	                <td height="25"><div id="margen"><strong>$<?= number_format($oReporteTotalTerceros->CostoTotal + $oReporteTotalFord->CostoTotal, 2, ',', '.') ?></strong></div></td>
	                <td height="25"><div id="margen"><strong><?= $oReporteTotalTerceros->StockTotal + $oReporteTotalFord->StockTotal ?></strong></div></td>
					<td height="25"><div id="margen"><strong>$<?= number_format($oReporteConMovimientoTerceros->CostoTotal + $oReporteConMovimientoFord->CostoTotal, 2, ',', '.') ?></strong></div></td>
	                <td height="25"><div id="margen"><strong><?= $oReporteConMovimientoTerceros->StockTotal + $oReporteConMovimientoFord->StockTotal ?></strong></div></td>			
					<td height="25"><div id="margen"><strong>$<?= number_format($oReporteLentoTerceros->CostoTotal + $oReporteLentoFord->CostoTotal, 2, ',', '.') ?></strong></div></td>
	                <td height="25"><div id="margen"><strong><?= $oReporteLentoTerceros->StockTotal + $oReporteLentoFord->StockTotal ?></strong></div></td>	
<td height="25"><div id="margen">$0,00</div></td>
	                <td height="25"><div id="margen">0</div></td>					
				</tr>				
	  			<tr>
        			<td colspan="3"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
			</table>
	  </td>
  	</tr>
  	<tr>
    	<td>&nbsp;
		</td>
  	</tr>

</table>
</body>
</html>