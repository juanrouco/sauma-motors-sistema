<?php 

require_once('../library/class.codigostrabajo.php');
require_once('../library/class.modelospv.php'); 
require_once('../library/class.session.php'); 
require_once('../library/class.ivas.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$IdModeloPV					= $_REQUEST['FilterIdModeloPV'];
$CodigoHistorico			= $_REQUEST['FilterCodigoHistorico'];
$Codigo						= $_REQUEST['FilterCodigo'];
$Descripcion				= $_REQUEST['FilterDescripcion'];
$filter['IdModeloPV'] 		= $IdModeloPV;
$filter['CodigoHistorico'] 	= $CodigoHistorico;
$filter['Codigo']			= $Codigo;
$filter['Descripcion']		= $Descripcion;

/* declaracion de variables */
$arr 			= array();
$CodigosTrabajo = new CodigosTrabajo();
$oModelosPV = new ModelosPV();
$oPage 			= new Page($Page);
$oPage->Size 	= 10;	

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $CodigosTrabajo->GetPagesCount($oPage, $filter))
	$Page = $CodigosTrabajo->GetPagesCount($oPage, $filter);

$arr 			= $CodigosTrabajo->GetAll($filter, $oPage);
$CountRows		= $CodigosTrabajo->GetCountRows($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $CodigosTrabajo->GetPagesCount($oPage, $filter), 'Busqueda');

?>
<?php if ($arr != NULL) { ?>
  	
    	<td>
			<div align="right"><?= $Paginado ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
					<td width="80" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
					<td width="90" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="90" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cod Hist</strong></div></td>
					<td width="300" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
					<td width="70" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>  
				</tr>
      
	  		<?php foreach ($arr as $oCodigoTrabajo) { 
				$oModeloPV = $oModelosPV->GetById($oCodigoTrabajo->IdModeloPV);
			?>          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oModeloPV->Modelo?></div></td>
	                <td height="25"><div id="margen"><?=$oCodigoTrabajo->Codigo?></div></td>
					<td height="25"><div align="center"><?=$oCodigoTrabajo->CodigoHistorico?></div></td>
	                <td height="25"><div id="margen"><?= utf8_encode($oCodigoTrabajo->Descripcion)?></div></td>
	                <td width="70" height="25" >
						<div align="center" align="center">
							<img src="images/iconos/add.gif" class="agregar" id="agregar_<?= $oCodigoTrabajo->IdCodigoTrabajo ?>" alt="Seleccionar" title="Seleccionar" onclick="FilterCodigoTrabajo('<?= $oCodigoTrabajo->IdCodigoTrabajo ?>', '<?= $oCodigoTrabajo->Descripcion ?>');" />
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