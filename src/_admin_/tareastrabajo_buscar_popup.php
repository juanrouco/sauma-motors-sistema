<?php 

require_once('../library/class.tareastrabajo.php'); 
require_once('../library/class.modelospv.php'); 
require_once('../library/class.session.php'); 

/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['IdTipoCosto'] 		= trim($_REQUEST['FilterIdTipoCosto']);
$filter['IdModeloPV'] 		= trim($_REQUEST['IdModeloPV']);
$filter['Anio'] 			= trim($_REQUEST['FilterAnio']);
$filter['PalabraClave'] 	= trim($_REQUEST['FilterPalabraClave']);	
$filter['NotCero'] 			= 1;	

/* declaracion de variables */
$arr = array();
$oTareasTrabajo	= new TareasTrabajo();
$oModelosPV 	= new ModelosPV();
$oPage 			= new Page($Page);
$oPage->Size 	= 10;	


$Paginado	= Pageable::PrintPaginator($oPage, $oTareasTrabajo->GetCountRows($filter), true);
$arr 	= $oTareasTrabajo->GetAll($filter, $oPage);

?>


<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">  	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
  
<?php if ($arr != NULL) { ?>
  	
	<tr>
    	<td>
			<div align="right"><?= $Paginado ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
				<tr class="bordeGrisFondo">
					<td colspan="2" height="25" class="bordeGrisTitulo" style="border: 0px">&nbsp;</div></td>
					<td colspan="2" height="25" class="bordeGrisTitulo" style="border: 0px"><div align="center"><strong>Precio</strong></div></td>
					<td colspan="2" height="25" class="bordeGrisTitulo" style="border: 0px">&nbsp;</td>		
				</tr>
      			<tr class="bordeGrisFondo">
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nombre</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Hs. Estimadas</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Mano Obra</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuestos</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>
					<td width="140" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>  
				</tr>
      
	  		<?php foreach ($arr as $oTareaTrabajo) { 
				$oModelo = $oModelosPV->GetById($oTareaTrabajo->IdModeloPV); 
			?>          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td width="250" height="25"><div id="margen"><?=$oTareaTrabajo->Titulo?></div></td>                        
					<td width="250" height="25"><div id="margen"><?=$oModelo->Modelo?></div></td>
					<td width="100" height="25"><div id="margen">&nbsp;&nbsp;<?=$oTareaTrabajo->HorasEstimadas?></div></td>						
					<td width="100" height="25"><div id="margen"><?=$oTareaTrabajo->IdTipoCosto == TipoCosto::CostoFijo ? 'N/C' : '$' . $oTareaTrabajo->ImporteManoObra()?></div></td>
					<td width="100" height="25"><div id="margen"><?=$oTareaTrabajo->IdTipoCosto == TipoCosto::CostoFijo ? 'N/C' : '$' . $oTareaTrabajo->ImporteRepuestos()?></div></td>
					<td width="100" height="25"><div id="margen">$<?=$oTareaTrabajo->ImporteTotal()?></div></td>
	                <td width="120" height="25" >
						<div align="center" align="center">						
							<img src="images/iconos/add.gif" class="agregar" id="agregar_<?= $oTareaTrabajo->IdTareaTrabajo ?>" alt="Agregar" title="Agregar" />
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