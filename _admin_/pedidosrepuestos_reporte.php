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
$filter['TurnoCreado']			= $_REQUEST['FilterTurnoCreado'];
$filter['OTCreado']				= $_REQUEST['FilterOTCreado'];
$filter['ArticuloAsignado']		= $_REQUEST['FilterArticuloAsignado'];
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
$oArticulos	 			= new Articulos();
$oPage 					= new Page($Page);
$oPage->Size 			= 50;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $oPedidosRepuestos->GetPagesCountReporte($oPage, $filter))
	$Page = $oPedidosRepuestos->GetPagesCountReporte($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 50;
$arr 			= $oPedidosRepuestos->GetReporte($filter, $oPage);
$CountRows		= $oPedidosRepuestos->GetCountRowsReporte($filter);
$oReporteTotal	= $oPedidosRepuestos->GetReporteTotal($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, true);

$arrUsuarios = $oUsuarios->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 					. $Page;
$strParams.= '&FilterFechaDesde=' 		. $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta=' 		. $filter['FechaHasta'];
$strParams.= '&FilterIdUsuario='		. $filter['IdUsuario'];
$strParams.= '&FilterIdOrdenTrabajo='	. $filter['IdOrdenTrabajo'];
$strParams.= '&FilterTurnoCreado='		. $filter['Vencido'];
$strParams.= '&FilterOTCreado='			. $filter['OTCreado'];
$strParams.= '&FilterArticuloAsignado='	. $filter['ArticuloAsignado'];
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
	window.location.href='pedidosrepuestos_reporte.php';
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
        			<td height="40"><span class="tituloPagina">Reporte de Pedidos de Repuestos</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
                <tr><?php /*
                  <td width="30"><div align="center"><img src="images/iconos/detalles.png" alt="Detalles Repuestos Pedidos" title="Detalles Repuestos Pedidos" border="0"></div></td>
                  <td><a title="Agregar" href="pedidosrepuestosdetalles.php<?=$strParams?>">Detalles Repuestos Pedidos</a></td>
                */ ?></tr>
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
                              <td class="tituloMenu">Turno Creado:</td>
                              <td><select name="FilterTurnoCreado" id="FilterTurnoCreado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['TurnoCreado']) echo "selected='selected'"; ?> >NO</option>
                                        <option value="1" <?php if ('1' == $filter['TurnoCreado']) echo "selected='selected'"; ?> >SI</option>
                                        </select></td>
							  <td>&nbsp;</td>
                              <td class="tituloMenu">OT Creada:</td>
                              <td><select name="FilterOTCreado" id="FilterOTCreado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['OTCreado']) echo "selected='selected'"; ?> >NO</option>
                                        <option value="1" <?php if ('1' == $filter['OTCreado']) echo "selected='selected'"; ?> >SI</option>
                                        </select></td>
                              </tr>
                            <tr>
							  
                              <td class="tituloMenu">Repuesto Asignado:</td>
                              <td><select name="FilterArticuloAsignado" id="FilterArticuloAsignado"  class="camporFormularioSimple">
                                        <option value="" >INDISTINTO</option>
                                        <option value="0" <?php if ('0' == $filter['ArticuloAsignado']) echo "selected='selected'"; ?> >NO</option>
                                        <option value="1" <?php if ('1' == $filter['ArticuloAsignado']) echo "selected='selected'"; ?> >SI</option>
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
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
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
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tbody><tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Costo Total: $<?= number_format($oReporteTotal->Total, 2, ',', '.') ?></span></td>
   			  </tr>
    		</tbody></table>
		</td>
  	</tr>	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
    	<td>
			<div align="right"><?php print_r ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Pedido</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Repuesto</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Pedido</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Recepci&oacute;n</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Solicitante</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sector</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&deg; OT</strong></div></td>					
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Dominio</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modalidad</strong></div></td>		
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Turno Asignado</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>OT Creada</strong></div></td>	
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Repuesto Asignado</strong></div></td>	
					<td width="70" height="25" class="bordeGrisTitulo"><div id="margen" align="right"><strong>Costo</strong></div></td>	
					<td width="10">&nbsp;</td>
				</tr>
      
	  		<?php foreach ($arr as $oReporte) 
			{ 
				$oPedidoRepuestoDetalle = $oReporte->PedidoRepuestoDetalle;
				$oPedidoRepuesto = $oPedidosRepuestos->GetById($oPedidoRepuestoDetalle->IdPedidoRepuesto);
				$oArticulo = $oArticulos->GetById($oPedidoRepuestoDetalle->IdArticulo);
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen" align="center"><?= $oPedidoRepuesto->IdPedidoRepuesto ?></div></td>
                	<td height="25"><div id="margen"><?= $oArticulo->Codigo ?> - <?= $oArticulo->Descripcion ?></div></td>
                	<td height="25"><div id="margen"><?=CambiarFecha($oPedidoRepuestoDetalle->FechaPedido)?></div></td>
                	<td height="25"><div id="margen"><?=CambiarFecha($oReporte->FechaRecepcion)?></div></td>
	                <td height="25"><div id="margen"><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></div></td>
	                <td height="25"><div id="margen"><?= $oSector['Nombre'] ?></div></td>
	                <td height="25">
						<div id="margen">
							<a href="ordenestrabajo_detail.php?IdOrdenTrabajo=<?= $oPedidoRepuesto->IdOrdenTrabajo ?>" target="_blank"><?= $oPedidoRepuesto->IdOrdenTrabajo ?></a></div></td>
					<td height="25"><div id="margen"><?= $oPedidoRepuesto->Dominio ?></div></td>					
					<td height="25"><div id="margen"><?= $oModalidad['Nombre'] ?></div></td>					
					<td height="25"><div id="margen" align="center"><?= $oReporte->TurnoCreado ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen" align="center"><?= $oReporte->OTCreado ? '<a href="ordenestrabajo_detail.php?IdOrdenTrabajo=' . $oReporte->OTCreado . '" target="_blank"><span style="color:green">' . $oReporte->OTCreado . '</span></a>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen" align="center"><?= $oReporte->ArticuloAsignado ? '<span style="color:green">SI</span>' : '<span style="color:red">NO</span>' ?></div></td>					
	                <td height="25"><div id="margen" align="right">$<?= $oPedidoRepuestoDetalle->Cantidad * $oPedidoRepuestoDetalle->Precio ?></div></td>	
					<td width="10">&nbsp;</td>
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