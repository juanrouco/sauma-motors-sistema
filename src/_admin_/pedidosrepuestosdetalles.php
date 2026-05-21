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
$filter['Codigo']				= $_REQUEST['FilterCodigo'];
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

$oPedidosRepuestosDetalles	= new PedidosRepuestosDetalles();
$oPedidosRepuestos			= new PedidosRepuestos();
$oTallerUnidades			= new TallerUnidades();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oUsuarios	 				= new Usuarios();
$oArticulos					= new Articulos();
$oPage 						= new Page($Page);
$oPage->Size 				= 20;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $oPedidosRepuestosDetalles->GetPagesCount($oPage, $filter))
	$Page = $oPedidosRepuestosDetalles->GetPagesCount($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arr 			= $oPedidosRepuestosDetalles->GetAll($filter, $oPage);
$CountRows		= $oPedidosRepuestosDetalles->GetCountRows($filter);

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
	window.location.href='pedidosrepuestosdetalles.php';
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
        			<td height="40"><span class="tituloPagina">Detalle Repuestos Pedidos</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Detalles Repuestos Pedidos" title="Detalles Repuestos Pedidos" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestos.php<?=$strParams?>">Ver Formato Listado</a></td>
                </tr>
              </table></td>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Detalles Repuestos Pedidos" title="Detalles Repuestos Pedidos" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestos_vista.php<?=$strParams?>">Ver Formato Grafico</a></td>
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
                              <td class="tituloMenu">Recibido:</td>
                              <td><select name="FilterRecibido" id="FilterRecibido"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Recibido']) echo "selected='selected'"; ?> >Sin Recibir</option>
                                        <option value="1" <?php if ('1' == $filter['Recibido']) echo "selected='selected'"; ?> >Recibido</option>
                                        </select></td>
							  <td>&nbsp;</td>
                              <td class="tituloMenu">Pedido:</td>
                              <td><select name="FilterPedido" id="FilterPedido"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['Pedido']) echo "selected='selected'"; ?> >Sin Pedir</option>
                                        <option value="1" <?php if ('1' == $filter['Pedido']) echo "selected='selected'"; ?> >Pedido</option>
                                        </select></td>
							<td>&nbsp;</td>
                              <td class="tituloMenu">C&oacute;digo Repuesto:</td>
                              <td>
							  <input type="text" name="FilterCodigo" id="FilterCodigo" class="camporFormularioSimple" value="<?= $filter['Codigo'] ?>" />
							  </td>
                              </tr>
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
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuesto</strong></div></td>
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sector</strong></div></td>
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&deg; OT</strong></div></td>					
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>	
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modalidad</strong></div></td>	
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Aprobado</strong></div></td>	
					<td width="50" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Recibido</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Pedido</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Numero SAP</strong></div></td>	
					<td width="80" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oPedidoRepuestoDetalle) 
			{ 
				$oPedidoRepuesto = $oPedidosRepuestos->GetById($oPedidoRepuestoDetalle->IdPedidoRepuesto);
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
				$oArticulo = $oArticulos->GetById($oPedidoRepuestoDetalle->IdArticulo);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->IdPedidoRepuesto ?></div></td>
                	<td height="25"><div id="margen"><?=CambiarFecha($oPedidoRepuesto->Fecha)?></div></td>
					<td height="25"><div id="margen"><?= $oArticulo->Codigo ?></div></td>
					<td height="25"><div id="margen"><?= $oArticulo->Descripcion ?></div></td>
	                <td height="25"><div id="margen"><?= $oSector['Nombre'] ?></div></td>
	                <td height="25"><div id="margen"><?= $oPedidoRepuesto->IdOrdenTrabajo ?></div></td>
					<td height="25"><div id="margen"><?= $oPedidoRepuesto->Dominio ?></div></td>					
					<td height="25"><div id="margen"><?= $oModalidad['Nombre'] ?></div></td>					
					<td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->Aprobado ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
					<td height="25"><div id="margen" align="center"><?= $oPedidoRepuestoDetalle->Recibido ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen"><?= CambiarFechaHora($oPedidoRepuestoDetalle->FechaPedido) ?></div></td>						
	                <td height="25"><div id="margen"><?= $oPedidoRepuestoDetalle->NumeroSap ?></div></td>						
					<td width="80" height="25">
						<div align="center">                         
							<?php
							if (Session::CheckPerm(PERM_PEDREP_PEDIDO) && $oPedidoRepuestoDetalle->FechaPedido && !$oPedidoRepuestoDetalle->Recibido && false)
							{
							?>
							<a href="pedidosrepuestosdetalle_recibir.php<?= $strParams ?>&IdPedidoRepuestoDetalle=<?= $oPedidoRepuestoDetalle->IdPedidoRepuestoDetalle ?>">
								<img src="images/iconos/check.gif" alt="Recibir Repuesto" title="Recibir Repuesto" border="0" /></a>
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