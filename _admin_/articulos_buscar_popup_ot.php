<?php 

require_once('../library/class.articulos.php'); 
require_once('../library/class.articulostocks.php'); 
require_once('../library/class.ubicaciones.php'); 
require_once('../library/class.ordenestrabajo.php'); 
require_once('../library/class.ordenestrabajotareas.php'); 
require_once('../library/class.session.php'); 
require_once('../library/class.ivas.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$Codigo							= $_REQUEST['FilterCodigo'];
$Descripcion					= $_REQUEST['FilterDescripcion'];
$IdUbicacion					= $_REQUEST['FilterIdUbicacion'];
$IdOrdenTrabajo					= $_REQUEST['IdOrdenTrabajo'];
$IdOrdenTrabajoTarea			= $_REQUEST['IdOrdenTrabajoTarea'];
$filter['Codigo'] 				= $Codigo;
$filter['Descripcion']			= $Descripcion;
$filter['IdOrdenTrabajo']		= $IdOrdenTrabajo;
$filter['IdOrdenTrabajoTarea']	= $IdOrdenTrabajoTarea;
//$filter['IdUbicacion']	= $IdUbicacion;

/* declaracion de variables */
$arr = array();
$Articulos 			= new Articulos();
$ArticuloStocks		= new ArticuloStocks();
$Ubicaciones 		= new Ubicaciones();
$oCompraDetalles	= new CompraDetalles();
$Ivas				= new Ivas();
$oPage 				= new Page($Page);
$oPage->Size 		= 10;	

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $Articulos->GetPagesCount($oPage, $filter))
	$Page = $Articulos->GetPagesCount($oPage, $filter);

$arr 			= $Articulos->GetAll($filter, $oPage);
$CountRows		= $Articulos->GetCountRows($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $Articulos->GetPagesCount($oPage, $filter));
$oUbicacion		= $Ubicaciones->GetById($IdUbicacion);

?>


<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">  	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
  
<?php if ($arr != NULL) { ?>
  	
	<tr>
    	<td>
			<div align="right"><strong>Sucursal:</strong> <?= $oUbicacion->Nombre ?></div>
        	<br>
			<div align="right"><strong>C&oacute;digo:</strong> <?= $Codigo ?>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Descripci&oacute;n:</strong> <?= $Descripcion ?></div>
        	<br>
			<div align="right"><?= $Paginado ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
				<tr class="bordeGrisFondo">
					<td colspan="3" height="25" class="bordeGrisTitulo" style="border: 0px">&nbsp;</div></td>
					<td colspan="2" height="25" class="bordeGrisTitulo" style="border: 0px"><div align="center"><strong>Precio</strong></div></td>
					<td colspan="2" height="25" class="bordeGrisTitulo" style="border: 0px">&nbsp;</td>		
				</tr>
      			<tr class="bordeGrisFondo">
					<td width="126" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div align="center"><strong>Unidad<br />Venta</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>(s/IVA)</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>(c/IVA)</strong></div></td>
					<?php
					if ($oUbicacion)
					{
					?>
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Stock</strong></div></td>  
					<?php
					}
					?>
					<td width="140" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>  
				</tr>
      
	  		<?php foreach ($arr as $oArticulo) { 
				$oArticuloStock = $ArticuloStocks->GetByArticuloAndUbicacion($oArticulo->IdArticulo, $oUbicacion->IdUbicacion);
				$oIva			= $Ivas->GetById($oArticulo->IdIva);
				$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
				$Cantidad 		= $oCompraDetalles->GetCountByTareaAndArticulo($IdOrdenTrabajoTarea, $oArticulo->IdArticulo);
			?>          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oArticulo->Codigo?></div></td>
	                <td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
					<td height="25"><div align="center"><?=$oArticulo->UnidadVenta?></div></td>
	                <td height="25"><div id="margen">$<?=$oArticulo->PrecioLista?></div></td>
					<td height="25"><div id="margen">$<?=number_format($precioConIva, 2)?></div></td>
					<?php
					if ($oUbicacion)
					{
					?>
					<td height="25"><div id="margen" align="center"><?=$oArticuloStock->StockActual?></div></td>
					<?php
					}
					?>
	                <td width="120" height="25" >
						<div align="center" align="center">
						<?php 
							if (!$oUbicacion || $oArticuloStock->StockActual > 0)
							{
						?>
								<input type="hidden" id="Cantidad_<?= $oArticulo->IdArticulo ?>" name="Cantidad_<?= $oArticulo->IdArticulo ?>" value="<?= $Cantidad ?>" />
								<img src="images/iconos/add.gif" class="agregar" id="agregar_<?= $oArticulo->IdArticulo ?>" alt="Agregar" title="Agregar" />
						<?php 
							}
						?>
						</div>
                    </td>
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
      
	  		<?php } ?>      
			</table>
	  </td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
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