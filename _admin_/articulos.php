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
$filter['Industria']	= $_REQUEST['FilterIndustria'];
$filter['Catalogo']		= $_REQUEST['FilterCatalogo'];
$filter['ConStock']		= $_REQUEST['FilterConStock'];

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
$arr 			= $Articulos->GetAll($filter, $oPage);
$CountRows		= $Articulos->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $Articulos->GetPagesCount($oPage, $filter));


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterCodigo=' 		. $filter['Codigo'];
$strParams.= '&FilterDescripcion=' 	. $filter['Descripcion'];
$strParams.= '&FilterIdProveedor='	. $filter['IdProveedor'];
$strParams.= '&FilterClasePieza='	. $filter['ClasePieza'];
$strParams.= '&FilterIndustria='	. $filter['Industria'];
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

function ClearFilter()
{
	window.location.href='articulos.php';
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
        			<td height="40"><span class="tituloPagina">Repuestos </span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td width="40%" height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <?php if (Session::CheckPerm(PERM_ARTI_CREATE)){ ?>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                  <td><a title="Agregar" href="articulos_add.php<?=$strParams?>">Agregar</a></td>
                </tr>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Reporte Articulos Vendidos" border="0"></div></td>
                  <td><a title="Agregar" href="ventasinternas.php<?=$strParams?>">Reporte Articulos Vendidos</a></td>
                </tr>
              <?php } ?>
              </table></td>
			  <?php if (Session::CheckPerm(PERM_ARTI_UPDATE)){ ?>
			  <td width="40%" height="40">
				<table border="0" align="left" cellpadding="0" cellspacing="0">
					<tr>
						<td width="30"><div align="center"><img src="images/iconos/relacion.png" alt="Importar" border="0"></div></td>
						<td><a href="articulos_import.php<?=$strParams?>">Importar</a></td>
						<?php
						if ($currentUser->IdPerfil == 1) { ?>
						<td>&nbsp;</td>
						<td width="30"><div align="center"><img src="images/iconos/relacion.png" alt="Importar" border="0"></div></td>
						<td><a href="articulos_ajustar.php<?=$strParams?>">Actualizar stock</a></td>
						<?php } ?>
					</tr>
				</table>
			</td>
			<?php } ?>
             <td width="20%" height="40">
				<table border="0" align="left" cellpadding="0" cellspacing="0">
					<tr>
						<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
						<td><a href="articulos_exportar.php<?=$strParams?>">Exportar XLS</a></td>
					</tr>
				</table>
			</td>
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
				
				<div class="bordeGrisFondo" id="ShownFilter" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; ">
			   		<table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
				</div>
				<div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-left: 10px; padding-bottom: 10px; padding-right: 10px; padding-top: 10px; " >
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
						  <td class="tituloMenu"><table border="0" align="left" cellpadding="0" cellspacing="0">
							<tr>
                              <td class="tituloMenu">C&oacute;digo:</td>
                              <td width="270"><input type="text" name="FilterCodigo" id="FilterCodigo" class="camporFormularioSimple" value="<?=$filter['Codigo']?>" /></td>
                              <td class="tituloMenu">Descripci&oacute;n:</td>
                              <td width="270"><input name="FilterDescripcion" id="FilterDescripcion" type="text" class="camporFormularioSimple" value="<?=$filter['Descripcion']?>" maxlength="255" /></td>
							  <td>&nbsp;</td>
							  <td class="tituloMenu" valign="top" width="85">
										<input type="checkbox" name="FilterConStock" id="FilterConStock" class="botonBasico" <?= $filter['ConStock'] ? 'checked="true"' : '' ?> />&nbsp; <span style="padding-top: 5px; float:right;">Con Stock</span>
									
								</td>
                            </tr>
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
                              <td class="tituloMenu">&nbsp;</td>
                              <td>&nbsp;</td>
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
  
<?php if ($arr != NULL) { ?>
  	
	<tr>
    	<td>
			<div align="right"><?php print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Reemplazo</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Proveedor</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sugerido<br/>(s/IVA)</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sugerido<br/>(c/IVA)</strong></div></td>
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Stock</strong></div></td>  
					<td width="140" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oArticulo) { 
				$oProveedor = $Proveedores->GetById($oArticulo->IdProveedor);
				$oIva			= $Ivas->GetById($oArticulo->IdIva);
				$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
				$oArticuloStock = $ArticuloStocks->GetAllByArticulo($oArticulo);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oArticulo->Codigo?></div></td>
	                <td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
					<td height="25"><div id="margen"><?=$oArticulo->Reemplazo?></div></td>
	                <td height="25"><div id="margen"><?=$oProveedor->Empresa?></div></td>
					<td height="25"><div id="margen"><?=$oArticuloStock[0]->Ubicacion?></div></td>
	                <td height="25"><div id="margen">$<?=$oArticulo->PrecioLista?></div></td>
					<td height="25"><div id="margen">$<?=number_format($precioConIva, 2)?></div></td>
					<td height="25"><div id="margen"><a href="#" class="tooltip" id="tooltip_<?= $oArticulo->IdArticulo ?>"><?=$oArticulo->StockTotal()?></a></div></td>
	                <td width="140" height="25">
                   <div align="center">                         
						<?php if (Session::CheckPerm(PERM_ARTI_UPDATE)){ ?>
			                <a href="articulos_mod.php<?=$strParams?>&IdArticulo=<?=$oArticulo->IdArticulo?>">
				                <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a> - 
                        <?php } ?>
						<?php if (Session::CheckPerm(PERM_STOCK_LIST)){ ?>
			                <a href="articulostocks.php<?=$strParams?>&FilterIdArticulo=<?=$oArticulo->IdArticulo?>">
				                <img src="images/iconos/referencias.png" alt="Stock Actual" title="Stock Actual" border="0" /></a> - 
                        <?php } ?>
						<?php if (Session::CheckPerm(PERM_STOCK_LIST)){ ?>
			                <a href="stockmovimientos.php<?=$strParams?>&IdArticulo=<?=$oArticulo->IdArticulo?>">
				                <img src="images/iconos/clock.png" alt="Movimientos Stock" title="Movimientos Stock" border="0" /></a> - 
                        <?php } ?>                     
						<?php if (Session::CheckPerm(PERM_ARTI_UPDATE)){ ?>
			                <a href="articulos_del.php<?=$strParams?>&IdArticulo=<?=$oArticulo->IdArticulo?>">
				                <img src="images/iconos/del.gif" alt="Modificar" title="Eliminar" border="0" /></a>
                        <?php } ?>                     
		                </div>
                    </td>
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
        	<div align="right"><?php print ($Paginado) ?></div>
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