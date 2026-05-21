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
$oPage->Size 			= 2000;

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

$j(document).ready(function() {
	$j('.auto-ingresado').click(function() {
		var IdPedidoRepuesto = $j(this).attr('id').replace('pr-', '');
		window.location.href = 'pedidosrepuestos_detail.php?IdPedidoRepuesto=' + IdPedidoRepuesto;
	});
	
	$j('.auto-ingresado').hover(function() {
		
		var IdPedidoRepuesto = $j(this).attr('id').replace('pr-', '');
		var tooltip = '<div class="tooltipevetn"><strong>PR N&deg; ' + IdPedidoRepuesto + '</strong><div class="informacion-ot"><img src="images/preloader_transparent.gif" width="50" /></div></div>';
		$j("body").append(tooltip);
		$j('.tooltipevetn').fadeIn('500');
		$j('.tooltipevetn').css('top', $j(this).offset().top - $j('.tooltipevetn').height() - 25);
		$j('.tooltipevetn').css('left', $j(this).offset().left - 90);
		$j.ajax('json-pedidosrepuestos.php?IdPedidoRepuesto=' + IdPedidoRepuesto, {
					dataTyoe: 'json',
					success: function (data, textStatus, jqXHR) {
						var json = data[0];
						var html = '<div class="row"><label>Fecha:&nbsp;</label>' + json.Fecha + '</div>';
						html+= '<div class="row"><label>Sector:&nbsp;</label>' + json.Sector + '</div>';
						html+= '<div class="row"><label>Nro. OT:&nbsp;</label>' + json.IdOrdenTrabajo + '</div>';
						html+= '<div class="row"><label>Dominio:&nbsp;</label>' + json.Dominio + '</div>';
						html+= '<div class="row"><label>Modalidad:&nbsp;</label>' + json.Modalidad + '</div>';
						html+= '<div class="row"><label>Costo:&nbsp;</label>$' + json.Costo + '</div>';
						html+= '<div class="row"><label>Repuestos:&nbsp;</label>' + json.Repuestos + '</div>';
						
						$j('.informacion-ot').html(html);
					}
				});
				$j(this).mouseover(function(e) {
					$j(this).css('z-index', 10000);
					$j('.tooltipevetn').fadeIn('500');
					$j('.tooltipevetn').fadeTo('10', 1.9);
				});
		}, function() {
			$j(this).css('z-index', 8);
				$j('.tooltipevetn').remove();
		});	
});

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
                  <td><a title="Agregar" href="pedidosrepuestos.php<?=$strParams?>">Ver Formato Listado</a></td>
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
  
		<?php
		$filter['IdModalidad'] = Modalidades::Rush;
		$arr = $oPedidosRepuestos->GetAllOrderedByEstado($filter);		
		if ($arr != NULL)
		{
		?>
		<tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">RUSH</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      
	  		<?php 
			$Count = 0;
			foreach ($arr as $oPedidoRepuesto) 
			{ 
				$CantidadDias = CantidadDiasPasados($oPedidoRepuesto->FechaVencido());
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
				
				if ($oPedidoRepuesto->Vencido())
				{
					if ($oPedidoRepuesto->Parcial())
						$clase = "reg";
					else
						$clase = "mal";
				}
				else
				{
					$clase = 'ok';
				}
							
				if ($Count % 10 == 0)
				{
					if ($Count != 0)
					{
			?>
			</tr>
			<?php
					}
			?>
			<tr>
			<?php
				}
			?>
				<td width="10%">
					<div id="pr-<?= $oPedidoRepuesto->IdPedidoRepuesto ?>" class="auto-ingresado <?=  $clase ?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" style="color: #ffffff"><strong>PR N&deg; <?= $oPedidoRepuesto->IdPedidoRepuesto ?></strong></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">$<?= $oPedidoRepuesto->Costo() ?></td>
							</tr>
						</table>
					</td>
				</td>
			<?php
				$Count++;
			}
			?>
          
      			
			</table>
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
  
		<?php
		$filter['IdModalidad'] = Modalidades::Urgente;
		$arr = $oPedidosRepuestos->GetAllOrderedByEstado($filter);		
		if ($arr != NULL)
		{
		?>
		<tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">URGENTE</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      
	  		<?php 
			$Count = 0;
			foreach ($arr as $oPedidoRepuesto) 
			{ 
				$CantidadDias = CantidadDiasPasados($oPedidoRepuesto->FechaVencido());
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
				
				if ($oPedidoRepuesto->Vencido())
				{
					if ($oPedidoRepuesto->Parcial())
						$clase = "reg";
					else
						$clase = "mal";
				}
				else
				{
					$clase = 'ok';
				}
							
				if ($Count % 10 == 0)
				{
					if ($Count != 0)
					{
			?>
			</tr>
			<?php
					}
			?>
			<tr>
			<?php
				}
			?>
				<td width="10%">
					<div id="pr-<?= $oPedidoRepuesto->IdPedidoRepuesto ?>" class="auto-ingresado <?=  $clase ?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" style="color: #ffffff"><strong>PR N&deg; <?= $oPedidoRepuesto->IdPedidoRepuesto ?></strong></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">$<?= $oPedidoRepuesto->Costo() ?></td>
							</tr>
						</table>
					</td>
				</td>
			<?php
				$Count++;
			}
			?>
          
      			
			</table>
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
  
		<?php
		$filter['IdModalidad'] = Modalidades::Normal;
		$arr = $oPedidosRepuestos->GetAllOrderedByEstado($filter);		
		if ($arr != NULL)
		{
		?>
		<tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">NORMAL</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      
	  		<?php 
			$Count = 0;
			foreach ($arr as $oPedidoRepuesto) 
			{ 
				$CantidadDias = CantidadDiasPasados($oPedidoRepuesto->FechaVencido());
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oPedidoRepuesto->IdOrdenTrabajo);
				$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
				$oUsuario = $oUsuarios->GetById($oPedidoRepuesto->IdUsuario);
				$oUsuarioAprobado = $oUsuarios->GetById($oPedidoRepuesto->IdUsuarioAprobado);
				$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
				$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);
				
				if ($oPedidoRepuesto->Vencido())
				{
					if ($oPedidoRepuesto->Parcial())
						$clase = "reg";
					else
						$clase = "mal";
				}
				else
				{
					$clase = 'ok';
				}
							
				if ($Count % 10 == 0)
				{
					if ($Count != 0)
					{
			?>
			</tr>
			<?php
					}
			?>
			<tr>
			<?php
				}
			?>
				<td width="10%">
					<div id="pr-<?= $oPedidoRepuesto->IdPedidoRepuesto ?>" class="auto-ingresado <?=  $clase ?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" style="color: #ffffff"><strong>PR N&deg; <?= $oPedidoRepuesto->IdPedidoRepuesto ?></strong></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
							</tr>
							<tr>
								<td align="center" style="color: #ffffff">$<?= $oPedidoRepuesto->Costo() ?></td>
							</tr>
						</table>
					</td>
				</td>
			<?php
				$Count++;
			}
			?>
          
      			
			</table>
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