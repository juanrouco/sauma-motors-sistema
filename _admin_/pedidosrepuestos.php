<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PEDREP_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['FechaDesde'] 			= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']			= $_REQUEST['FilterFechaHasta'];
$filter['IdUsuario']			= $_REQUEST['FilterIdUsuario'];
$filter['IdOrdenTrabajo']		= $_REQUEST['FilterIdOrdenTrabajo'];
$filter['Vencido']				= $_REQUEST['FilterVencido'];
$filter['Aprobado']				= $_REQUEST['FilterAprobado'];
$filter['Recibido']				= $_REQUEST['FilterRecibido'];
$filter['Pedido']				= $_REQUEST['FilterPedido'];
$filter['IdPedidoRepuesto']		= $_REQUEST['FilterIdPedidoRepuesto'];


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

$oPedidosRepuestos		= new PedidosRepuestos();
$oTallerUnidades		= new TallerUnidades();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oUsuarios	 			= new Usuarios();
$oPage 					= new Page($Page);
$oPage->Size 			= 20;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $oPedidosRepuestos->GetPagesCount($oPage, $filter))
	$Page = $oPedidosRepuestos->GetPagesCount($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arr 			= $oPedidosRepuestos->GetAll($filter, $oPage);
$CountRows		= $oPedidosRepuestos->GetCountRows($filter);
$CostoTotal		= $oPedidosRepuestos->GetCostoTotal($filter); 

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, true);

$arrUsuarios = $oUsuarios->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 					. $Page;
$strParams.= '&FilterFechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&FilterIdUsuario='		. $filter['IdUsuario'];
$strParams.= '&FilterIdOrdenTrabajo='	. $filter['IdOrdenTrabajo'];
$strParams.= '&FilterVencido='			. $filter['Vencido'];
$strParams.= '&FilterAprobado='			. $filter['Aprobado'];
/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
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

function ClearFilter()
{
	window.location.href='pedidosrepuestos.php';
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

function FilterCliente(IdCliente, Nombre)
{
	if ((IdCliente == '') && (Nombre == ''))
	{		
		$j('#Cliente').val('');
		$j('#IdCliente').val('');
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;
	
	$j('#Cliente').val(oCliente.RazonSocial);
	$j('#FilterIdCliente').val(oCliente.IdCliente);
}

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Pedidos de Repuestos</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <?php if (Session::CheckPerm(PERM_PEDREP_CREATE)){ ?>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestos_add.php<?=$strParams?>">Agregar</a></td>
                </tr>
              <?php } ?>
              </table></td>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Detalles Repuestos Pedidos" title="Detalles Repuestos Pedidos" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestos_vista.php<?=$strParams?>">Ver Formato Grafico</a></td>
                </tr>
              </table></td>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Detalles Repuestos Pedidos" title="Detalles Repuestos Pedidos" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestosdetalles.php<?=$strParams?>">Detalles Repuestos Pedidos</a></td>
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
                               <td class="tituloMenu">N&deg; Pedido:</td>
                              <td>
								<input type="text" name="FilterIdPedidoRepuesto" id="FilterIdPedidoRepuesto" class="camporFormularioSimple" value="<?=$filter['IdPedidoRepuesto']?>" />
							</td>
							<td>&nbsp;</td>
                              <td class="tituloMenu">Fecha Desde:</td>
                              <td>
								<input type="text" name="FilterFechaDesde" id="FilterFechaDesde" class="camporFormularioMediano" value="<?=$filter['FechaDesde']?>" />
								<script language="javascript">
								new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
								</script>
							</td>
							<td>&nbsp;</td>
                              <td class="tituloMenu">Fecha Hasta:</td>
                              <td>
							  <input type="text" name="FilterFechaHasta" id="FilterFechaHasta" class="camporFormularioMediano" value="<?=$filter['FechaHasta']?>" />
							<script language="javascript">
							new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
							</script>
							  </td>
                            </tr>
                            <tr>
                              <td class="tituloMenu">N&deg; OT:</td>
                              <td>
							  <input type="text" class="camporFormularioSimple" name="FilterIdOrdenTrabajo" id="FilterIdOrdenTrabajo" value="<?=$filter['IdOrdenTrabajo']?>" />
							  </td>
							  <td>&nbsp;</td>
                              <td class="tituloMenu">Vencido:</td>
                              <td><select name="FilterVencido" id="FilterVencido"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Vencido']) echo "selected='selected'"; ?> >Sin Vencer</option>
                                        <option value="1" <?php if ('1' == $filter['Vencido']) echo "selected='selected'"; ?> >Vencido</option>
                                        </select></td>
							  <td>&nbsp;</td>
                              <td class="tituloMenu">Aprobado:</td>
                              <td><select name="FilterAprobado" id="FilterAprobado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Aprobado']) echo "selected='selected'"; ?> >Sin Aprobar</option>
                                        <option value="1" <?php if ('1' == $filter['Aprobado']) echo "selected='selected'"; ?> >Aprobado</option>
                                        </select></td>
                              </tr>
                            <tr>
                              <td class="tituloMenu">Pedido:</td>
                              <td><select name="FilterPedido" id="FilterPedido"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Pedido']) echo "selected='selected'"; ?> >Sin Pedir</option>
                                        <option value="1" <?php if ('1' == $filter['Pedido']) echo "selected='selected'"; ?> >Pedido</option>
                                        </select></td>
							  <td>&nbsp;</td>
                              <td class="tituloMenu">Recibido:</td>
                              <td><select name="FilterRecibido" id="FilterRecibido"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Recibido']) echo "selected='selected'"; ?> >Sin Recibir</option>
                                        <option value="1" <?php if ('1' == $filter['Recibido']) echo "selected='selected'"; ?> >Recibido</option>
                                        </select></td>
							<td>&nbsp;</td>
                              <td class="tituloMenu">Usuario Solicitante:</td>
                              <td>
							  <select name="FilterIdUsuario" id="FilterIdUsuario" class="camporFormularioSimple">
								<option value="">INDISTINTO</option>
								<?php
									foreach ($arrUsuarios as $oUsuario)
									{
										$selected = '';
										if ($oUsuario->IdUsuario == $filter['IdUsuario'])	
											$selected = "selected='selected'";
								?>
								<option value="<?= $oUsuario->IdUsuario ?>" <?= $selected ?>><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></option>
								<?php
									}
								?>
							  </select>
							  </td>
                              </tr>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <tr>
							  <td colspan="7">&nbsp;</td>
							  <td valign="middle">
									<div align="right">
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
			<div align="right"><?php print_r ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Pedido</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sector</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&deg; OT</strong></div></td>					
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modalidad</strong></div></td>		
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Aprobado</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Pedido</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Recibido</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Vencido</strong></div></td>	
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen" align="right"><strong>Costo</strong></div></td>	
					<td width="10">&nbsp;</td>
					<td width="100" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oPedidoRepuesto) 
			{ 
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->IdPedidoRepuesto ?></div></td>
                	<td height="25"><div id="margen"><?=CambiarFecha($oPedidoRepuesto->Fecha)?></div></td>
	                <td height="25"><div id="margen"><?= $oSector['Nombre'] ?></div></td>
	                <td height="25"><div id="margen"><?= $oPedidoRepuesto->IdOrdenTrabajo ?></div></td>
					<td height="25"><div id="margen"><?= $oPedidoRepuesto->Dominio ?></div></td>					
					<td height="25"><div id="margen"><?= $oModalidad['Nombre'] ?></div></td>					
					<td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->Aprobado ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->Pedido() ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->RecibidoTexto() ?></div></td>					
	                <td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->Vencido() ? '<span style="color:red">SI</span>' : '<span style="color:green">NO</span>' ?></div></td>					
					<td height="25"><div id="margen" align="right">$<?= $oPedidoRepuesto->Costo() ?></div></td>	
					<td width="10">&nbsp;</td>					
					<td width="100" height="25">
						<div align="center">                         
							<a href="pedidosrepuestos_detail.php<?= $strParams ?>&IdPedidoRepuesto=<?= $oPedidoRepuesto->IdPedidoRepuesto ?>">
								<img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a>
							<?php
							if (Session::CheckPerm(PERM_PEDREP_APROBAR) && !$oPedidoRepuesto->Aprobado)
							{
							?>
							- <a href="pedidosrepuestos_aprobar.php<?= $strParams ?>&IdPedidoRepuesto=<?= $oPedidoRepuesto->IdPedidoRepuesto ?>">
								<img src="images/iconos/check.gif" alt="Aprobar Pedido" title="Aprobar Pedido" border="0" /></a>
							<?php
							}
							if (Session::CheckPerm(PERM_PEDREP_PEDIDO) && $oPedidoRepuesto->Aprobado)
							{
							?>
							- <a href="pedidosrepuestos_realizarpedido.php<?= $strParams ?>&IdPedidoRepuesto=<?= $oPedidoRepuesto->IdPedidoRepuesto ?>">
								<img src="images/iconos/test.png" alt="Realizar Pedido" title="Realizar Pedido" border="0" /></a>
							<?php
							}
							if (Session::CheckPerm(PERM_PEDREP_UPDATE) && !$oPedidoRepuesto->Aprobado)
							{
							?>
							- <a href="pedidosrepuestos_mod.php<?= $strParams ?>&IdPedidoRepuesto=<?= $oPedidoRepuesto->IdPedidoRepuesto ?>">
								<img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a>
							<?php
							}
							?>
							- <a target="_blank" href="pedidosrepuestos_pdf.php<?= $strParams ?>&IdPedidoRepuesto=<?= $oPedidoRepuesto->IdPedidoRepuesto ?>">
								<img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a>
							<?php
							if (Session::CheckPerm(PERM_PEDREP_DELETE) && false)
							{
							?>
							- <a href="pedidosrepuestos_del.php<?=$strParams?>&IdPedidoRepuesto=<?=$oPedidoRepuesto->IdPedidoRepuesto?>">
								<img src="images/iconos/del.gif" alt="Anular" border="0" /></a>
							<?php
							}
							?>
		                </div>
                    </td>
              </tr>
	  			<tr>
        			<td colspan="13"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
      
	  		<?php } ?>   
				<tr class="bordeGrisFondo">					
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>
					<td width="200" height="25" class="bordeGrisTitulo">&nbsp;</td>
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>					
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>	
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>	
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>	
					<td width="85" height="25" class="bordeGrisTitulo">&nbsp;</td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total:</strong></div></td>	
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen" align="right"><strong>$<?= $CostoTotal; ?></strong></div></td>	
					<td width="10">&nbsp;</td>
					<td width="100" class="bordeGrisTitulo">&nbsp;</td>
				</tr>			
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
</body>
</html>