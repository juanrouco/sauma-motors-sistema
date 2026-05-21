<?php 
require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_LIST))
	Session::NoPerm();

$oUsuario = Session::GetCurrentUser();

/* obtiene datos enviados */
$Page 			= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;
$PageDerivada 	= (isset($_REQUEST['PageDerivada'])) ? intval($_REQUEST['PageDerivada']) : 0;
$IdTarea 		= $_REQUEST['IdTarea'];

/* armamos el filtro */
$filter = array();
$filter['IdTipo']				= $_REQUEST['FilterIdTipo'];
$filter['FechaInicio']			= $_REQUEST['FilterFechaInicio'];
$filter['FechaFin'] 			= $_REQUEST['FilterFechaFin'];
$filter['Nombre']				= $_REQUEST['FilterNombre'];
$filter['IdUsuarioFrom']		= $_REQUEST['FilterIdUsuarioFrom'];
$filter['IdUsuarioTo']			= $_REQUEST['FilterIdUsuarioTo'];
$filter['IdEstado']				= $_REQUEST['FilterIdEstado'];


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
$oTiposTareas			= new TiposTareas();
$oUsuarios				= new Usuarios();
$oClientes				= new Clientes();
$oPage->Size 			= 30;
$oPageDerivada->Size 	= 10;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */
if (!$filter['IdUsuarioTo'])
	$filter['IdUsuarioTo'] = $oUsuario->IdUsuario;
if ($Page > $oTareas->GetPagesCount($oPage, $filter))
	$Page = $oTareas->GetPagesCount($oPage, $filter);

$oPage 				= new Page($Page);
$oPage->Size 		= 20;
$arr 				= $oTareas->GetAll($filter, $oPage);
$arrUsuarios 		= $oUsuarios->GetAll();
$arrTiposTareas 	= $oTiposTareas->GetAll();


$CountRows			= $oTareas->GetCountRows($filter);
$Paginado			= Pageable::PrintPaginator($oPage, $oTareas->GetPagesCount($oPage, $filter), $CountRows);



$arrTareasDerivadas				= $oTareas->GetTareasDerivadas($oUsuario);

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
        			<td height="40"><span class="tituloPagina">Tareas</span></td>
   			  	</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <?php if (Session::CheckPerm(PERM_TAREAS_CREATE)){ ?>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                  <td><a title="Agregar" href="tareas_add.php<?=$strParams?>">Agregar</a></td>
                </tr>
              <?php } ?>
              </table></td>
              <td height="40"><table border="0" align="right" cellpadding="0" cellspacing="0">
                <tr>


                </tr>
              </table></td>
          </tr>
        </table>
	  </td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
                <input type="hidden" name="PageDerivada" id="PageDerivada" value="<?=$PageDerivada?>">
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
                              <td width="87" class="tituloMenu">Nombre:</td>
                              <td width="263"><input name="FilterNombre" id="FilterNombre" type="text" class="camporFormularioSimple" value="<?=$filter['Nombre']?>" maxlength="128"></td>
                              <td class="tituloMenu">Generada por:</td>
                              <td width="263"><select name="FilterIdUsuarioFrom" id="FilterIdUsuarioFrom" class="camporFormularioSimple">
                                <option value="" >Indistinto</option>
                                <?php foreach ($arrUsuarios as $oUsuario) { ?>
                                <option value="<?=$oUsuario->IdUsuario?>" <?php echo ($oUsuario->IdUsuario == $filter['IdUsuarioFrom']) ? "selected='selected'" : "" ?> >
                                <?=$oUsuario->Apellido;?>, <?=$oUsuario->Nombre;?>
                                </option>
                                <?php } ?>
                              </select></td>
                              <td class="tituloMenu">Asignada a:</td>
                              <td width="264"><select name="FilterIdUsuarioTo" id="FilterIdUsuarioTo" class="camporFormularioSimple">
                                <option value="" >Indistinto</option>
                                <?php foreach ($arrUsuarios as $oUsuario) { ?>
                                <option value="<?=$oUsuario->IdUsuario?>" <?php echo ($oUsuario->IdUsuario == $filter['IdUsuarioTo']) ? "selected='selected'" : "" ?> >
                                <?=$oUsuario->Apellido;?>, <?=$oUsuario->Nombre;?>
                                </option>
                                <?php } ?>
                              </select></td>
                            </tr>
							<tr>
                              <td class="tituloMenu">Fecha Inicio:</td>
                              <td>
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td>
										<input name="FilterFechaInicio" type="text" class="camporFormularioMediano" id="FilterFechaInicio" value="<?=$filter['FechaInicio']?>" size="12" maxlength="12" />
										<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FilterFechaInicio'});
										</script></td>
                                    </tr>
                                  </table>                              </td>
                              <td class="tituloMenu">Fecha Fin:</td>
                              <td>
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td>
									  <input name="FilterFechaFin" type="text" class="camporFormularioMediano" id="FilterFechaFin" value="<?=$filter['FechaFin']?>" size="12" maxlength="12" />
										<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FilterFechaFin'});
										</script></td>
                                    </tr>
                                  </table>                              </td>
                              <td class="tituloMenu">Tipo:</td>
                              <td width="263"><select name="FilterFilterIdTipo" id="FilterIdTipo" class="camporFormularioSimple">
                                    <option value="" >Indistinto</option>
                                    <?php foreach ($arrTiposTareas as $oTareaTipo) { ?>
                                    <option value="<?=$oTareaTipo->IdTipoTarea?>" <?php echo ($oTareaTipo->IdTipoTarea == $filter['IdTipo']) ? "selected='selected'" : "" ?> >
                                    <?=$oTareaTipo->Nombre;?>
                                    </option>
                                    <?php } ?>
                                    </select></td>
								</tr>
								<tr>
                              <td class="tituloMenu">Estado:</td>
                              <td width="264"><select name="FilterIdEstado" id="FilterIdEstado" class="camporFormularioSimple">
											<option value="" >[Seleccione]</option>
											<?php foreach (TareaEstados::GetAll() as $oTareaEstado) { ?>
											<option value="<?=$oTareaEstado['IdEstado']?>" <?php echo ($oTareaEstado['IdEstado'] == $filter['IdEstado']) ? "selected='selected'" : "" ?> > <?=$oTareaEstado['Descripcion']?></option>
											<?php } ?>
                                          </select></td>
                              <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
                                                          <td valign="middle"><div align="right">
                                <input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
                              </div></td>
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
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center">
                    	<table width="100%" cellpadding="0" cellspacing="0">
                        	<tr>
								<?php foreach (TareaEstados::GetAll() as $oTareaEstado) { ?>
                                <td bgcolor="<?=TareaEstados::GetOnlyColorById($oTareaEstado['IdEstado'])?>" width="40" height="20"><div align="left"></div></td>
                                <td width="40">&nbsp;</td>
                                <td width="270"><div align="left"><?=$oTareaEstado['Descripcion']?></div></td>
                                <?php } ?>
							</tr>
                        </table>
                    </td>        					
				</tr>
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="">
                    	<table cellpadding="0" cellspacing="0" width="100%">
                        	<tr>
                    			<td><span class="tituloPagina">Tareas</span></td>
                    			<td><div align="right"><? print ($Paginado) ?></div></td>
                            </tr>
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </td>            
                </tr>
                
			</table>
            <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris" border="0">
            	
     			<?php if ($arr != NULL) { ?> 
   	    <br>
			
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
      
	  		<?php foreach ($arr as $oTarea) { 
							
				$oTareaTipo 		= $oTiposTareas->GetById($oTarea->IdTipo);
				$oUsuarioTo 	= $oUsuarios->GetById($oTarea->IdUsuarioTo);
				$oUsuario 		= $oUsuarios->GetById($oTarea->IdUsuarioFrom);
				$oCliente		= $oClientes->GetById($oTarea->IdCliente);
			
			?>
          
         		<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''" >
                <td bgcolor="<?=TareaEstados::GetOnlyColorById($oTarea->IdEstado)?>"></td>
				<td height="25"><div id="margen"><?=$oTareaTipo->Nombre?></div></td>
				<td height="25"><?=$oCliente ? $oCliente->RazonSocial : 'NO POSEE'?></td>
            	<td height="25"><?=CambiarFecha($oTarea->FechaInicio)?></td>
            	<td height="25"><?=$oTarea->Hora?></td>
                <td height="25"><?=CambiarFecha($oTarea->FechaFin)?></td>           	
                <td height="25"><span title="Haga clic para ver m&aacute;s informaci&oacute;n"><a href="tareas_descripcion.php?IdTarea=<?=$oTarea->IdTarea?>" target="_blank" onClick="window.open(this.href, this.target, 'width=1000,height=1000'); return false;" class="linkMenu"><?=$oTarea->Nombre?></a></span></td>
                
                <td height="25"><?=TareaEstados::GetColorById($oTarea->IdEstado)?></td>
            	<td width="88" height="25">
                	<div align="center">
						<?php if (Session::CheckPerm(PERM_TAREAS_UPDATE)){ ?>
                    	<a href="tareas_descripcion.php?IdTarea=<?=$oTarea->IdTarea?>" target="_blank" onClick="window.open(this.href, this.target, 'width=1000,height=1000'); return false;" class="linkMenu"><img alt="Detalles" title="Detalles" src="images/iconos/preview.png" width="16" height="16" border="0"></a> -
					
						
                    	    <a href="tareas_mod.php<?=$strParams?>&IdTarea=<?=$oTarea->IdTarea?>">
                    	        <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a>
                    	<?php } ?>
                   
                  	</div>
                </td>
           	</tr>
          	<tr>
                <td colspan="9"><div align="center">
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
                    <td colspan="8"><div align="right"><? print ($Paginado) ?></div></td>
                </tr>	           
            </table>
            <br>
			<?php
			/*
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
           <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="10"><span class="tituloPagina">Tareas derivadas</span></td>
                     <td width="17%" colspan="4"><div align="right"><? print ($PaginadoDerivada) ?></div></td>
                </tr>
                <tr>
                	<td colspan="14">&nbsp;</td>
                </tr>
			</table>
            <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" class="bordeGris">
				<?php if ($arrTareasDerivadas != NULL) {?>	
                    
                        <tr class="bordeGrisFondo">
                          <td width="25" height="10" ></td>
                          <td width="93" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Inicio</strong></div></td>
                          <td width="108" height="25" class="bordeGrisTitulo"><strong>Fecha Fin</strong></td>
                          <td width="147" height="25" class="bordeGrisTitulo"><strong>Nombre</strong></td>
                          <td width="90" height="25" class="bordeGrisTitulo"><strong>Tipo</strong></td>
                          <td width="114" height="25" class="bordeGrisTitulo"><strong>Estado</strong></td>
                          <td width="203" height="25" class="bordeGrisTitulo"><strong>Asignado a</strong></td>
                          <td width="87" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                      </tr>
              
                    <?php foreach ($arrTareasDerivadas as $oTareaX) { 
                                    
                    $oTareaTipo = $oTiposTareas->GetById($oTareaX->IdTipo);
                    $oUsuarioAux  = $oUsuarios->GetById($oTareaX->IdUsuarioTo);
					
                    ?>
                  
                        <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''" >
                        <td bgcolor="<?=TareaEstados::GetOnlyColorById($oTareaX->IdEstado)?>"></td>
                        <td height="25"><div id="margen"><?=CambiarFecha($oTareaX->FechaInicio)?></div></td>
                        <td height="25"><?=CambiarFecha($oTareaX->FechaFin)?></td>           	
                        <td height="25"><span title="Haga clic para ver m&aacute;s informaci&oacute;n"><a href="tarea_descripcion.php?IdTarea=<?=$oTareaX->IdTarea?>" target="_blank" onClick="window.open(this.href, this.target, 'width=550,height=325'); return false;" class="linkMenu"><?=$oTareaX->Nombre?></a></span></td>
                        <td height="25"><?=$oTareaTipo->Nombre?></td>
                        <td height="25"><?=TareaEstados::GetColorById($oTareaX->IdEstado)?></td>
                        <td height="25"><?=$oUsuarioAux->Apellido?>, <?=$oUsuarioAux->Nombre?></td>
                        <td width="87" height="25">
                          <div align="center">
                            <?php if (Session::CheckPerm(PERM_TAREAS_UPDATE)){ ?>
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
                    <td colspan="8"><div align="right"><? print ($PaginadoDerivada) ?></div></td>
                </tr>	           
            </table>
			<?php
			*/ 
			?>
	  </td>
  	</tr>
  	

</table>

</body>

<script language="javascript">
	//HideFilter();
</script>

</html>