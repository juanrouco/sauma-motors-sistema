<?php 
require_once('../library/class.articulos.php');
require_once('../library/class.session.php'); 
require_once('../library/class.ivas.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$Codigo					= $_REQUEST['FilterCodigo'];
$Descripcion			= $_REQUEST['FilterDescripcion'];
$filter['Codigo'] 		= $Codigo;
$filter['Descripcion']	= $Descripcion;

/* declaracion de variables */
$arr 			= array();
$oArticulos		= new Articulos();
$Ivas			= new Ivas();
$oPage 			= new Page($Page);
$oPage->Size 	= 10;	

/* SOLUCION TEMPORAL PARA EL PAGINADOR*/

if ($Page > $oArticulos->GetPagesCount($oPage, $filter))
	$Page = $oArticulos->GetPagesCount($oPage, $filter);

$arr 			= $oArticulos->GetAll($filter, $oPage);
$CountRows		= $oArticulos->GetCountRows($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $oArticulos->GetPagesCount($oPage, $filter), 'Busqueda');

?>
<?php if ($arr != NULL) { ?>
  	
    	<td>
			
			<div align="right"><?= $Paginado ?></div>
			
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="126" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="150" height="25" class="bordeGrisTitulo"><div align="center"><strong>Unidad<br />Venta</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>(s/IVA)</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>(c/IVA)</strong></div></td>
					<td width="140" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>  
				</tr>
      
	  		<?php foreach ($arr as $oArticulo) { 
				$oIva			= $Ivas->GetById($oArticulo->IdIva);
				$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
			?>          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oArticulo->Codigo?></div></td>
	                <td height="25"><div id="margen"><?=$oArticulo->Descripcion?></div></td>
					<td height="25"><div align="center"><?=$oArticulo->UnidadVenta?></div></td>
	                <td height="25"><div id="margen">$<?=$oArticulo->PrecioLista?></div></td>
					<td height="25"><div id="margen">$<?=number_format($precioConIva, 2)?></div></td>
	                <td width="120" height="25" >
						<div align="center" align="center">
							<img src="images/iconos/add.gif" class="agregar" id="agregar_<?= $oArticulo->IdArticulo ?>" alt="Seleccionar" title="Seleccionar" onclick="FilterArticulo('<?= $oArticulo->IdArticulo ?>', '<?= $oArticulo->Codigo ?>');" />
						</div>
                    </td>
              </tr>
	  			<tr>
        			<td colspan="5"><div align="center">
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
  	

<?php } else { ?>  

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
      
<?php } ?>