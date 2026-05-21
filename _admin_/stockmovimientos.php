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
$filter['FechaDesde'] 	= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']	= $_REQUEST['FilterFechaHasta'];
$filter['IdUbicacion']	= $_REQUEST['FilterIdUbicacion'];
$filter['IdArticulo']	= $_REQUEST['IdArticulo'];

$IdArticulo 	= $_REQUEST['IdArticulo'] ;
$IdUbicacion 	= $_REQUEST['FilterIdUbicacion'] ;

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if ($filter['FechaDesde'] != '' || $filter['FechaHasta'] != '' || $filter['IdUbicacion'] != '')
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

/* declaracion de variables */
$arr = array();

$Ubicaciones 		= new Ubicaciones();
$Articulos	 		= new Articulos();
$ArticuloStocks 	= new ArticuloStocks();
$StockMovimientos 	= new StockMovimientos();
$Comprobantes	 	= new Comprobantes();
$Compras			= new Compras();
$Clientes			= new Clientes();
$TallerUnidades		= new TallerUnidades();
$oPage 				= new Page($Page);
$oPage->Size 		= 20;
$oArticulo	 		= $Articulos->GetById($IdArticulo);
$arrUbicaciones 	= $Ubicaciones->GetAll();

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $StockMovimientos->GetPagesCount($oPage, $filter))
	$Page = $StockMovimientos->GetPagesCount($oPage, $filter);

$oPage 		= new Page($Page);
$oPage->Size = 20;
$arr 			= $StockMovimientos->GetAll($filter, $oPage);
$CountRows		= $StockMovimientos->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $StockMovimientos->GetPagesCount($oPage, $filter), $CountRows);

if (!$filter['FechaDesde'])
{
	$arrStocksIniciales = $ArticuloStocks->GetAll($filter);
}


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterFechaDesde=' 	. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 	. $filter['FechaHasta'];
$strParams.= '&FilterIdUbicacion=' 	. $filter['IdUbicacion'];
$strParams.= '&IdArticulo=' 		. $filter['IdUbicacion'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
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

	frmData.FilterIdUbicacion.value = '';	
	
	return true;
}

function ClearFilter()
{
	window.location.href='stockmovimientos.php?IdArticulo=<?= $IdArticulo ?>';
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

<?php include('include/head.inc.php'); ?></head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina"><?= $oArticulo->Descripcion ?> - Movimientos de Stock </span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="40">
						<table border="0" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<td><a title="Agregar" href="articulos.php<?=$strParams?>" class="linkMenu">[Volver a repuestos]</a></td>
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
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
				<input type="hidden" name="MainAction" id="MainAction" />
				<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?= $IdArticulo ?>" />
				<input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
				
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
								<td class="tituloMenu">Fecha Desde:</td>
								<td width="270">
									<input type="text" name="FilterFechaDesde" id="FilterFechaDesde" class="camporFormularioSuggest" value="<?= $filter['FechaDesde'] ?>" />										
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
									</script>
								</td>
								<td class="tituloMenu">Fecha Hasta:</td>
								<td width="270">
									<input type="text" name="FilterFechaHasta" id="FilterFechaHasta" class="camporFormularioSuggest" value="<?= $filter['FechaHasta'] ?>" />
									<script type="text/javascript">
										new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
									</script>
								</td>								
                            </tr>
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
								<td class="tituloMenu">&nbsp;</td>
								<td width="270">&nbsp;</td>
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
			<div align="right"><? print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
					<td width="170" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Art&iacute;culo</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>
					<td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estanteria</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Remito</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Venta</strong></div></td>
					<td width="170" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente/Unidad</strong></div></td>
					<td width="103" height="25" class="bordeGrisTitulo"><strong>Cantidad</strong></td>				
				</tr>
				<?php
				foreach ($arr as $oStockMovimiento) { 				
				$oUbicacion	= $Ubicaciones->GetById($oStockMovimiento->IdUbicacion);
				$oRemito = $Comprobantes->GetByNumero(ComprobanteTipos::Remito, $oStockMovimiento->Remito);
				$oCompra = $Compras->GetById($oStockMovimiento->IdCompra);
				$oTipoVenta = TipoVenta::GetById($oCompra->TipoOperacion);
				$oArticuloStock = $ArticuloStocks->GetByArticuloAndUbicacion($IdArticulo, $oUbicacion->IdUbicacion);
				$UnidadCliente = 'N/C';
				if ($oStockMovimiento->Cantidad > 0 && !$oStockMovimiento->IdCompra)
				{
				}
				else
				{
					
					if ($oCompra->TipoOperacion == TipoVenta::Mostrador)
					{
						$oCliente = $Clientes->GetById($oCompra->IdCliente);
						if ($oCliente)
							$UnidadCliente		= $oCliente->GetUsuario();
					}
					else
					{
						$oTallerUnidad = $TallerUnidades->GetById($oCompra->IdTallerUnidad);
						$UnidadCliente		= $oTallerUnidad->Dominio;
					}
				}
			?>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen"><?=CambiarFecha($oStockMovimiento->Fecha)?></div></td>
					<td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
                	<td height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>
					<td height="25"><div id="margen"><?=$oArticuloStock->Ubicacion?></div></td>
	                <td height="25" <?= (!$oStockMovimiento->Remito && !$oStockMovimiento->IdCompra) ? 'style="color:red;"' : ''?>><div id="margen"><?= $oStockMovimiento->Remito ? $oStockMovimiento->Remito : ($oStockMovimiento->IdCompra ? 'Devoluci&oacute;n' :'Ajuste Stock')?></div></td>					
					<td height="25"><div id="margen"><?=$oStockMovimiento->Cantidad > 0 && !$oStockMovimiento->IdCompra ? 'N/C' : $oTipoVenta['Nombre']?></div></td>
					<td height="25"><div id="margen"><?=$UnidadCliente?></div></td>
   	                <td height="25" <?= ($oStockMovimiento->Cantidad < 0 || (!$oStockMovimiento->Remito && !$oStockMovimiento->IdCompra)) ? 'style="color:red;"' : ''?>><?=$oStockMovimiento->Cantidad?></td>
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
      
	  		<?php }
				if ($arrStocksIniciales)
				{
					foreach ($arrStocksIniciales as $oArticuloStock)
					{
						$oUbicacion	= $Ubicaciones->GetById($oArticuloStock->IdUbicacion);
				?>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen">Inicial</div></td>
					<td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
                	<td height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>
					<td height="25"><div id="margen"><?=$oArticuloStock->Ubicacion?></div></td>
	                <td height="25"><div id="margen">N/C</div></td>					
					<td height="25"><div id="margen">N/C</div></td>
					<td height="25"><div id="margen">N/C</div></td>
   	                <td height="25"><?=$oArticuloStock->StockInicial?></td>
				</tr>
	  			<tr>
        			<td colspan="7"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
				<?php
					}
				}	
				?>
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
</body>
</html>