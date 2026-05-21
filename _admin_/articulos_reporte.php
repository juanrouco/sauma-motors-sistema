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

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $Articulos->GetPagesCount($oPage, $filter))
	$Page = $Articulos->GetPagesCount($oPage, $filter);

$oPage 		= new Page($Page);
$oPage->Size = 20;
$arr 			= $Articulos->GetAllReporte($filter, $oPage);
$CountRows		= $Articulos->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $Articulos->GetPagesCount($oPage, $filter));

$oReporteTotal 	= $Articulos->GetTotalReporte($filter);

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
        			<td height="40"><span class="tituloPagina">Reporte Repuestos en Stock</span></td>
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
					<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
					<td><a href="articulos_reporte_exportar.php<?=$strParams?>">Exportar XLS</a></td>
				</tr>
			</table>
		</td>
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
                              <td class="tituloMenu">Proveedor:</td>
                              <td width="270"><select name="FilterIdProveedor" id="FilterIdProveedor" class="camporFormularioSimple">
                                <option value="" >Indistinto</option>
                                <?php if ($arrProveedores){ ?>
                                <?php foreach ($arrProveedores as $oProveedor) { ?>
                                <option value="<?=$oProveedor->IdProveedor?>" <?php echo ($oProveedor->IdProveedor == $filter['IdProveedor']) ? "selected='selected'" : "" ?> >
                                <?=$oProveedor->Empresa;?>
                                </option>
                                <?php } ?>
                                <?php } ?>
                              </select></td>
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
								<td class="tituloPagina" width="210">Stock Total de Repuestos:</td>
								<td class="tituloPagina" align="left"><?= $oReporteTotal->StockTotal ?></td>
								<td width="100">&nbsp;</td>
								<td class="tituloPagina" width="175">Valorizaci&oacute;n de Stock:</td>
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
<?php if ($arr != NULL) { ?>
  	
	<tr>
    	<td>
			<div align="right"><? print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Proveedor</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio<br />Compra</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Precio<br />Dealer</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sugerido<br/>(s/IVA)</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sugerido<br/>(c/IVA)</strong></div></td>
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Stock</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oArticulo) { 
				$oProveedor = $Proveedores->GetById($oArticulo->IdProveedor);
				$oIva			= $Ivas->GetById($oArticulo->IdIva);
				$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
				$oArticuloStock = $ArticuloStocks->GetAllByArticulo($oArticulo);
				$stock = $oArticulo->StockTotal();
				if ($filter['IdUbicacion'])
				{
					foreach ($oArticulo->Stocks as $oStock)
					{
						if ($oStock->IdUbicacion == $filter['IdUbicacion'])
							$stock = $oStock->StockActual;
					}
				}
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oArticulo->Codigo?></div></td>
	                <td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
	                <td height="25"><div id="margen"><?=$oProveedor->Empresa?></div></td>
					<td height="25"><div id="margen">$<?=$oArticulo->PrecioCompra?></div></td>
					<td height="25"><div id="margen">$<?=$oArticulo->PrecioTerceros?></div></td>
	                <td height="25"><div id="margen">$<?=$oArticulo->PrecioLista?></div></td>
					<td height="25"><div id="margen">$<?=number_format($precioConIva, 2)?></div></td>
					<td height="25"><div id="margen"><a href="#" class="tooltip" id="tooltip_<?= $oArticulo->IdArticulo ?>"><?=$stock?></a></div></td>
              </tr>
	  			<tr>
        			<td colspan="8"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
      
	  		<?php } ?>      
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
<?php foreach ($arr as $oArticulo) { 
?>
<div class="tooltip_<?= $oArticulo->IdArticulo ?>" style="display:none">
	<?php
		if ($oArticulo->Stocks)
		{
	?>
		<table align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<tbody>
				<tr class="bordeGrisFondo">
					<td width="103" align="center" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sucursal</strong></div></td>
					<td width="103" align="center" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Stock Actual</strong></div></td>
				</tr>
				<tr>
						<td colspan="2">
							<div align="center">
								<table width="100%"  border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				<?php
					foreach ($oArticulo->Stocks as $oStock)
					{
						$oUbicaciones = $Ubicaciones->GetById($oStock->IdUbicacion);
				?>
					<tr>
						<td width="103" align="center" height="25"><div id="margen"><?= $oUbicaciones->Nombre ?></div></td>
						<td width="103" align="center" height="25"><div id="margen"><?= $oStock->StockActual ?></div></td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<table width="100%"  border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	<?php
		}
		else
		{
	?>
		<table align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<tr>
				<td><div align="center"><strong>No hay stocks cargados.</strong></div></td>
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