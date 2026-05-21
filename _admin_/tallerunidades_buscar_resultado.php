<?php 
require_once('../library/class.tallerunidades.php');
require_once('../library/class.session.php'); 
require_once('../library/class.clientes.php'); 
require_once('../library/class.modelos.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$Dominio				= $_REQUEST['FilterDominio'];
$NumeroVin				= $_REQUEST['FilterNumeroVin'];
$Cliente				= $_REQUEST['FilterCliente'];
$Modelo					= $_REQUEST['FilterModelo'];
$filter['Dominio'] 		= $Dominio;
$filter['NumeroVin']	= $NumeroVin;
$filter['Cliente']		= $Cliente;
$filter['Modelo']		= $Modelo;

/* declaracion de variables */
$arr 			= array();
$TallerUnidades = new TallerUnidades();
$oClientes		= new Clientes();
$oModelos		= new Modelos();
$oPage 			= new Page($Page);
$oPage->Size 	= 10;	

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

$TallerUnidades->ActualizarUnidades();

if ($Page > $TallerUnidades->GetPagesCount($oPage, $filter))
	$Page = $TallerUnidades->GetPagesCount($oPage, $filter);

$arr 			= $TallerUnidades->GetAll($filter, $oPage);
$CountRows		= $TallerUnidades->GetCountRows($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $TallerUnidades->GetPagesCount($oPage, $filter), 'Busqueda');

?>
<?php if ($arr != NULL) { ?>
  	
    	<td>
			<div align="left"><input type="button" id="btnAgregar2" name="btnAgregar2" value="Agregar Unidad" class="botonBasico" onclick="AddTallerUnidad()" /></div>
        	
			<div align="right"><?= $Paginado ?></div>
			
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="35%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
					<td width="10%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>
					<td width="35%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
					<td width="15%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vin</strong></div></td>
					<td width="5%" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>  
				</tr>
      
	  		<?php foreach ($arr as $oTallerUnidad) { 
				$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
			?>          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                	<td height="25"><div id="margen"><?=$oTallerUnidad->Dominio?></div></td>
	                <td height="25"><div id="margen"><?=$oTallerUnidad->Modelo?></div></td>
	                <td height="25"><div id="margen"><?=$oTallerUnidad->NumeroVin?></div></td>
	                <td width="70" height="25" >
						<div align="center" align="center">
							<img src="images/iconos/add.gif" class="agregar" id="agregar_<?= $oTallerUnidad->IdTallerUnidad ?>" alt="Seleccionar" title="Seleccionar" onclick="FilterTallerUnidad('<?= $oTallerUnidad->IdTallerUnidad ?>', '<?= $oTallerUnidad->Dominio ?>');" />
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