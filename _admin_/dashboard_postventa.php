<?php 

require_once('../inc_library.php'); 


/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();


/* armamos el filtro */
$filter = array();
$filter['AlertaStockMinimo'] = 1;
$filter['IdUbicacion'] = Ubicacion::Libertador;

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

$arr 			= $Articulos->GetAll($filter);


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
        			<td height="40"><span class="tituloPagina">Repuestos con stock debajo del minimo</span></td>
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
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Proveedor</strong></div></td>
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Stock Min</strong></div></td>  
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Stock Max</strong></div></td>  
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Stock</strong></div></td>  
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
					<td height="25"><div id="margen"><?=$oProveedor->Empresa?></div></td>
	                <td height="25"><div id="margen" align="center"><?=$oArticulo->StockMinimo?></div></td>
	                <td height="25"><div id="margen" align="center"><?=$oArticulo->StockMaximo?></div></td>
	                <td height="25"><div id="margen" align="center"><a href="#" class="tooltip" id="tooltip_<?= $oArticulo->IdArticulo ?>"><?=$oArticulo->StockTotal()?></a></div></td>
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
				                <img src="images/iconos/clock.png" alt="Movimientos Stock" title="Movimientos Stock" border="0" /></a>
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