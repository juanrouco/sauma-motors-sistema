<?php 

Header('Location: tareas_agenda.php');
exit;
require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_LIST))
	Session::NoPerm();

$oUsuario = Session::GetCurrentUser();

/* obtiene datos enviados */
$Page 				= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;
$PageDerivada 		= (isset($_REQUEST['PageDerivada'])) ? intval($_REQUEST['PageDerivada']) : 0;
$PagePresupuestos 	= (isset($_REQUEST['PagePresupuestos'])) ? intval($_REQUEST['PagePresupuestos']) : 0;
$IdTarea 			= $_REQUEST['IdTarea'];

/* armamos el filtro */
$filter = array();
$filter['FechaInicio']			= date('d-m-Y');
$filter['IdUsuarioTo']			= $oUsuario->IdUsuario;

$filterPendiente = array();
$filterPendiente['FechaHasta']			= date('d-m-Y');
$filterPendiente['IdUsuarioTo']			= $oUsuario->IdUsuario;
$filterPendiente['IdEstado']			= TareaEstados::Pendiente;

$filterPresupuesto = array();
$filterPresupuesto['IdEstado']			= PresupuestoEstados::Pendiente;
$filterPresupuesto['IdUsuario']			= $oUsuario->IdUsuario;

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";



if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}
if ($filter['IdEstado'] == '')
{	
	$filter['IdEstado'] 	= 1;
}	
/* declaracion de variables */
$arr 					= array();

$oTareas 				= new Tareas();
$oPage 					= new Page($Page);
$oPageDerivada 			= new Page($PageDerivada);
$PagePresupuestos		= new Page($PagePresupuestos);
$oTiposTareas			= new TiposTareas();
$oUsuarios				= new Usuarios();
$oClientes				= new Clientes();
$oPresupuestos			= new Presupuestos();
$oModelos				= new Modelos();
$oPage->Size 			= 5;
$oPageDerivada->Size 	= 5;
$oPagePresupuestos->Size 	= 5;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */
if ($Page > $oTareas->GetPagesCount($oPage, $filter))
	$Page = $oTareas->GetPagesCount($oPage, $filter);

$oPage 				= new Page($Page);
$oPage->Size 		= 20;
$arr 				= $oTareas->GetAll($filter, $oPage);
$arrUsuarios 		= $oUsuarios->GetAll();
$arrTiposTareas 	= $oTiposTareas->GetAll();


$CountRows			= $oTareas->GetCountRows($filter);
$Paginado			= Pageable::PrintPaginator($oPage, $oTareas->GetPagesCount($oPage, $filter), $CountRows);



$arrTareasDerivadas				= $oTareas->GetAll($filterPendiente, $oPageDerivada);

$arrPresupuestos				= $oPresupuestos->GetAll($filterPresupuesto, $PagePresupuestos);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 							. $Page;

$strParams.= '&FilterIdTipo='					. $filter['FilterIdTipo'];
$strParams.= '&FilterFechaInicio='				. $filter['FilterFechaInicio'];
$strParams.= '&FilterFechaFin='					. $filter['FilterFechaFin'];
$strParams.= '&FilterNombre='					. $filter['FilterNombre'];
$strParams.= '&FilterIdUsuarioFrom='			. $filter['FilterIdUsuarioFrom'];
$strParams.= '&FilterIdUsuarioTo='				. $filter['FilterIdUsuarioTo'];
$strParams.= '&FilterIdEstado='					. $filter['FilterIdEstado'];
$strParams.= '&FilterDescripcion='				. $filter['FilterDescripcion'];


if ($filter['FechaInicio'] == '')
	$filter['FechaInicio'] 	= date('d-m-Y', time()-30*24*3600);;

if ($filter['FechaFin'] == '')
	$filter['FechaFin']	= date('d-m-Y');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
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

	frmData.FilterEmpresa.value 		= '';
	frmData.FilterEmail.value 			= '';
																													
	return true;
}

function ClearFilter()
{
	window.location.href='tareas.php';
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
        			<td width="20" height="40" class="TituloDepartamento">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Inicio</span></td>
   			  	</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
  	
	<tr>
    	<td>
			
            
            <br>
            <table width="100%" cellpadding="0" cellspacing="0">
            	<tr>
                    <td colspan="8"><div align="center">
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td background="images/linea_punteada.gif"><div align="center"></div></td>
                            </tr>
                        </table></div>
                    </td>
                </tr>
            </table>
           <?php /*<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="10"><span class="tituloPagina">Tareas Pendientes</span></td>
                     <td width="17%" colspan="4"><div align="right"></div></td>
                </tr>
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
			</table>
            <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" class="bordeGris">
				<?php if ($arrTareasDerivadas != NULL) {?>	
                    
                        <tr class="bordeGrisFondo">
                          <td width="25" height="10" ></td>
				  <td width="89" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo</strong></div></td>
				  <td width="198" height="25" class="bordeGrisTitulo"><strong>Cliente</strong></td>
                  <td width="102" height="25" class="bordeGrisTitulo"><strong>Fecha Inicio</strong></td>
				  <td width="89" height="25" class="bordeGrisTitulo"><strong>Hora</strong></td>
        		  <td width="107" height="25" class="bordeGrisTitulo"><strong>Fecha Fin</strong></td>
                  <td width="146" height="25" class="bordeGrisTitulo"><strong>Nombre</strong></td>
				  
                  <td width="112" height="25" class="bordeGrisTitulo"><strong>Estado</strong></td>
                  <td width="88" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                      </tr>
              
                    <?php foreach ($arrTareasDerivadas as $oTareaX) { 
                                    
                    $oTareaTipo = $oTiposTareas->GetById($oTareaX->IdTipo);
                    $oUsuarioAux  = $oUsuarios->GetById($oTareaX->IdUsuarioTo);
                    $oClienteAux  = $oClientes->GetById($oTareaX->IdCliente);
					
                    ?>
                  
                        <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''" >
                        <td bgcolor="<?=TareaEstados::GetOnlyColorById($oTareaX->IdEstado)?>"></td>
						<td height="25"><div id="margen"><?=$oTareaTipo->Nombre?></div></td>
						<td height="25"><?=$oClienteAux ? $oClienteAux->RazonSocial : 'NO POSEE'?></td>
                        <td height="25"><?=CambiarFecha($oTareaX->FechaInicio)?></td>
                        <td height="25"><?=$oTareaX->Hora?></td>
                        <td height="25"><?=CambiarFecha($oTareaX->FechaFin)?></td>           	
                        <td height="25"><span title="Haga clic para ver m&aacute;s informaci&oacute;n"><a href="tareas_descripcion.php?IdTarea=<?=$oTareaX->IdTarea?>" target="_blank" onClick="window.open(this.href, this.target, 'width=1000,height=1000'); return false;" class="linkMenu"><?=$oTareaX->Nombre?></a></span></td>
                       <td height="25"><?=TareaEstados::GetColorById($oTareaX->IdEstado)?></td>
                        <td width="87" height="25">
                          <div align="center">
                            <?php if (Session::CheckPerm(PERM_TAREAS_UPDATE)){ ?>
								<a href="tareas_descripcion.php?IdTarea=<?=$oTareaX->IdTarea?>" target="_blank" onClick="window.open(this.href, this.target, 'width=1000,height=1000'); return false;" class="linkMenu"><img alt="Detalles" title="Detalles" src="images/iconos/preview.png" width="16" height="16" border="0"></a> -
					
                                <a href="tareas_mod.php<?=$strParams?>&IdTarea=<?=$oTareaX->IdTarea?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a>
                            <?php } ?>
                           
                          </div>
                        </td>
                    </tr>
                        <tr>
                        <td colspan="14"><div align="center">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                </tr>
                            </table></div>
                        </td>
                    </tr>
              
                    <?php } ?>      
                    
                <?php } else { ?>   
                 
                    <tr>
                        <td colspan="14">
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
            <table cellpadding="0" cellspacing="0" width="100%">
                <br>
                <tr>
                    <td colspan="8"><div align="right"><a href="tareas.php">[Ver todas las tareas]</a></div></td>
                </tr>	           
            </table>
			<br>
            <table width="100%" cellpadding="0" cellspacing="0">
            	<tr>
                    <td colspan="8"><div align="center">
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td background="images/linea_punteada.gif"><div align="center"></div></td>
                            </tr>
                        </table></div>
                    </td>
                </tr>
            </table>
			*/ ?>
           <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="10"><span class="tituloPagina">Facturas Proforma Pendientes</span></td>
                     <td width="17%" colspan="4"><div align="right"></div></td>
                </tr>
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
					<td align="center">
                    	<table width="100%" cellpadding="0" cellspacing="0">
                        	<tr>
								<?php foreach (PresupuestoEstados::GetAll() as $oPresupuestoEstado) { ?>
                                <td bgcolor="<?=PresupuestoEstados::GetOnlyColorById($oPresupuestoEstado['IdEstado'])?>" width="40" height="20"><div align="left"></div></td>
                                <td width="40">&nbsp;</td>
                                <td width="270"><div align="left"><?=$oPresupuestoEstado['Descripcion']?></div></td>
                                <?php } ?>
							</tr>
                        </table>
                    </td>        					
				</tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
		</table>
		<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<?php if ($arrPresupuestos != NULL) {?>	
                    <tr class="bordeGrisFondo">
						<td width="25" height="10" ></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Presupuesto</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Precio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Vencimiento</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Vendedor</strong></div></td>
                        <td width="103" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrPresupuestos as $oPresupuesto) 
					{ 
						$oModelo = $oModelos->GetById($oPresupuesto->IdModelo);
						$oCliente = $oClientes->GetById($oPresupuesto->IdCliente);
						$oUsuario = $oUsuarios->GetById($oPresupuesto->IdUsuario);
						$cliente = $oCliente->RazonSocial;
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
						<td bgcolor="<?=PresupuestoEstados::GetOnlyColorById($oPresupuesto->IdEstado)?>"></td>
                        <td width="75" height="25"><div id="margen" align="center"><?=$oPresupuesto->IdPresupuesto?></div></td>
						 <td width="134" height="25"><div id="margen"><?=$cliente?></div></td>
						 <td width="168" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
						 <td width="79" height="25"><div id="margen" align="center"><?=number_format($oPresupuesto->Precio, 2)?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oPresupuesto->Fecha)?></div></td>
                        <td width="80" height="25"><div id="margen" align="center"><?=CambiarFecha($oPresupuesto->FechaVencimiento)?></div></td>
                        <td width="79" height="25"><div id="margen"><?=PresupuestoEstados::GetColorById($oPresupuesto->IdEstado)?></div></td>
                        <td width="153" height="25"><div id="margen"><?=$oUsuario->Nombre . ', ' . $oUsuario->Apellido?></div></td>
                        <td width="103" height="25" valign="middle">
                            <div align="center">
								<a href="presupuestos_detail.php<?=$strParams?>&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/preview.png" alt="Detalle" border="0" /></a> - 
                                <?php
									if (Session::CheckPerm(PERM_VENT_CREATE)) {
								?>
									<a href="unidades.php<?=$strParamsSelect?>&MainAction=Select&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/facturacion.png" alt="Generar Minuta" border="0" /></a> - 
                                <?php 
									}
									if (Session::CheckPerm(PERM_PRESUP_UPDATE)){ ?>
                                <a href="presupuestos_mod.php<?=$strParams?>&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/mod.gif" alt="Modificar" border="0" /></a> - 
            	                <?php } ?>
                	            <?php if (Session::CheckPerm(PERM_PRESUP_DELETE)){ ?>
                                <a href="presupuestos_del.php<?=$strParams?>&IdPresupuesto=<?=$oPresupuesto->IdPresupuesto?>">
                                    <img src="images/iconos/del.gif" alt="Eliminar" border="0" /></a>
                    	        <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
    
                <?php } ?>      
                <?php } else { ?>   
                 
                    <tr>
                        <td colspan="14">
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
				<table cellpadding="0" cellspacing="0" width="100%">
                <br>
                <tr>
                    <td colspan="8"><div align="right"><a href="presupuestos.php">[Ver todas los presupuestos]</a></div></td>
                </tr>	           
            </table>
	  </td>
  	</tr>
  	

</table>

</body>

<script language="javascript">
	//HideFilter();
</script>

</html>