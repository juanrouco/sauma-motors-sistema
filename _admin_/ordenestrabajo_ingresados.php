<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= 300;///intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaInicioDesde']		= trim($_REQUEST['FilterFechaInicioDesde']);
	$filter['FechaInicioHasta']		= trim($_REQUEST['FilterFechaInicioHasta']);
	$filter['IdEstadoOrden'] 		= trim($_REQUEST['FilterIdEstadoOrden']);	
	$filter['Dominio'] 				= trim($_REQUEST['FilterDominio']);	
	$filter['IdUsuarioAsignado'] 	= trim($_REQUEST['FilterIdUsuarioAsignado']);
	$filter['IdTipoVenta']			= trim($_REQUEST['FilterIdTipoVenta']);
	$filter['Cliente']				= trim($_REQUEST['FilterCliente']);
	$filter['NumeroVin']			= trim($_REQUEST['FilterNumeroVin']);
	$filter['Factura']				= trim($_REQUEST['FilterFactura']);
	$filter['IdOrdenTrabajo']		= trim($_REQUEST['FilterIdOrdenTrabajo']);
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$filter['EnTaller']				= '1';

$arrData 				= array();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oModelos 				= new Modelos();
$oTallerUnidades		= new TallerUnidades();
$oUsuarios				= new Usuarios();
$oEstadosOrden			= new EstadosOrden();
$oClientes				= new Clientes();
$oComprobantes			= new Comprobantes();
$oFacturasPostVentas	= new FacturasPostVentas();

$oPage 					= new Page($Page, 1000);


$arrEstadosOrden = $oEstadosOrden->GetAll();
$filterUsuarios = array();
$filterUsuarios['IdPerfil'] = 10;
$arrUsuarios = $oUsuarios->GetAll($filterUsuarios);
$arrTipoVenta = TipoVenta::GetAllOrdenTrabajo();

$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

$arrModelos 		= $oModelos->GetAll();

IncludeSUGGEST();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
	frmData.submit();
}

function SetPageSize(PageSize)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	if (frmData.PageSize == undefined)
		return false;

	frmData.PageSize.value = PageSize;
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
	window.location.href = 'ordenestrabajo.php_ingresados?MainAction=<?=$Action?>';
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

function SetNumeroVinPrefijo(IdModelo, NumeroVinPrefijo)
{
	Get('FilterNumeroVinPrefijo').value = NumeroVinPrefijo;
}

$j(document).ready(function() {
	$j('.auto-ingresado').click(function() {
		var IdOrdenTrabajo = $j(this).attr('id').replace('ot-', '');
		window.location.href = 'ordenestrabajo_detail.php?IdOrdenTrabajo=' + IdOrdenTrabajo;
	});
	
	$j('.auto-ingresado').hover(function() {
		
		var IdOrdenTrabajo = $j(this).attr('id').replace('ot-', '');
		var tooltip = '<div class="tooltipevetn"><strong>OT N&deg; ' + IdOrdenTrabajo + '</strong><div class="informacion-ot"><img src="images/preloader_transparent.gif" width="50" /></div></div>';
		$j("body").append(tooltip);
		$j('.tooltipevetn').fadeIn('500');
		$j('.tooltipevetn').css('top', $j(this).offset().top - $j('.tooltipevetn').height() - 25);
		$j('.tooltipevetn').css('left', $j(this).offset().left - 90);
		$j.ajax('json-ordenestrabajo.php?IdOrdenTrabajo=' + IdOrdenTrabajo, {
					dataTyoe: 'json',
					success: function (data, textStatus, jqXHR) {
						var json = data[0];
						var html = '<div class="row"><label>Estado:&nbsp;</label>' + json.Estado + '</div>';
						html+= '<div class="row"><label>Fecha de Ingreso:&nbsp;</label>' + json.FechaInicio + '</div>';
						html+= '<div class="row"><label>Modelo:&nbsp;</label>' + json.Modelo + '</div>';
						html+= '<div class="row"><label>Cliente:&nbsp;</label>' + json.Cliente.RazonSocial + '</div>';
						html+= '<div class="row"><label>Tel:&nbsp;</label>' + json.Cliente.Telefono + '</div>';
						html+= '<div class="row"><label>Email:&nbsp;</label>' + json.Cliente.Email + '</div>';
						html+= '<div class="row"><label>Tipo:&nbsp;</label>' + json.Tipo + '</div>';
						html+= '<div class="row"><label>Tareas:&nbsp;</label>' + json.Tareas + '</div>';
						
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

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="<?=$Action?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Ordenes de Trabajo</span></td>
                    </tr>
                </table>		
            </td>
        </tr>       
	  	<tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="30"><div align="center"><img src="images/iconos/csv.png" alt="Agregar" border="0"></div></td>
						<td><a href="ordenestrabajo_ingresados_exportar.php<?=$strParams?>">Exportar Abiertas</a></td>
						<td width="10" height="30" >&nbsp;</td>                      
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <!-- Aca van los filtros -->				
                <div id="ShownFilter" class="bordeGrisFondo" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div id="HiddenFilter" style="<?=$filterStyle;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;" class="bordeGrisFondo" >
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
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>
										<td class="tituloMenu"><div align="right">Nro. OT:</div></td>
                                        <td>
                                        	<input name="FilterIdOrdenTrabajo" id="FilterIdOrdenTrabajo" type="text" class="camporFormularioSimple" value="<?=$filter['IdOrdenTrabajo']?>" />
                                       	</td>
										<td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Desde:</div></td>
                                        <td>
                                        	<input name="FilterFechaInicioDesde" id="FilterFechaInicioDesde" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaInicioDesde']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaInicioDesde'});
                                            </script>
                                       	</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Fecha Hasta:</div></td>
										<td>
											<input name="FilterFechaInicioHasta" id="FilterFechaInicioHasta" type="text" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$filter['FechaInicioHasta']?>" />
                                            <script language="">
                                            new tcal({'formname': 'frmData', 'controlname': 'FilterFechaInicioHasta'});
                                            </script>
										</td>
                                        
                                    </tr>
                                    <tr>
                                        <td class="tituloMenu"><div align="right">Cliente:</div></td>
                                        <td>
											<input type="text" name="FilterCliente" id="FilterCliente"  class="camporFormularioSimple" value="<?= $filter['Cliente'] ?>" />												
										</td>
                                        <td>&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Dominio:</div></td>
                                        <td>
											<input type="text" name="FilterDominio" id="FilterDominio"  class="camporFormularioSimple" value="<?= $filter['Dominio'] ?>">
										</td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Asesor:</div></td>
                                        <td>
											<select id="FilterIdUsuarioAsignado" name="FilterIdUsuarioAsignado" class="camporFormularioSimple">
												<option value>INDISTINTO</option>
												<?php
												foreach ($arrUsuarios as $oUsuario)
												{
													$selected = '';
													if ($filter['IdUsuarioAsignado'] == $oUsuario->IdUsuario)
														$selected = 'selected="true"';
												?>
													<option value="<?= $oUsuario->IdUsuario ?>" <?= $selected ?>><?= $oUsuario->Nombre . ' '. $oUsuario->Apellido ?></option>
												<?php
												}
												?>
											</select>
										</td>
                                    </tr>
									<tr>
										<td class="tituloMenu"><div align="right">Vin:</div></td>
                                        <td>
											<input type="text" name="FilterNumeroVin" id="FilterNumeroVin"  class="camporFormularioSimple" value="<?= $filter['NumeroVin'] ?>" />
										</td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Estado:</div></td>
                                        <td>
											<select id="FilterIdEstadoOrden" name="FilterIdEstadoOrden" class="camporFormularioSimple">
												<option value>INDISTINTO</option>
												<?php
												foreach ($arrEstadosOrden as $oEstadoOrden)
												{
													$selected = '';
													if ($filter['IdEstadoOrden'] == $oEstadoOrden->IdEstado)
														$selected = 'selected="true"';
												?>
													<option value="<?= $oEstadoOrden->IdEstado ?>" <?= $selected ?>><?= $oEstadoOrden->Nombre ?></option>
												<?php
												}
												?>
											</select>
										</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="9" align="right"><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
									</tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                </div>				
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php 
	$filter['IdCategoria'] = Categorias::Taller;
	$Paginado	= Pageable::PrintPaginator($oPage, $oOrdenesTrabajo->GetCountRows($filter), true);
	$arrData 	= $oOrdenesTrabajo->GetAllOrderByIngreso($filter, $oPage);
	if ($arrData != NULL) { ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">Taller</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
					<?php
					$Count = 0;
					foreach ($arrData as $oOrdenTrabajo)
					{
						$CantidadDias = CantidadDiasPasados($oOrdenTrabajo->FechaInicio);
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						
						if ($CantidadDias <= 3)
							$clase = 'ok';
						elseif ($CantidadDias <= 7)
							$clase = 'reg';
						else
							$clase = 'mal';
							
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
							<div id="ot-<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="auto-ingresado <?=  $clase ?>">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="center" style="color: #ffffff"><strong>OT N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff"><strong>Dominio: <br><?= $oTallerUnidad->Dominio ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
									</tr>
								</table>
							</td>
						</td>
					<?php
						$Count++;
					}
					?>
					</tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php } ?>
          
    <?php 
	$filter['IdCategoria'] = Categorias::ChapaYPintura;
	$Paginado	= Pageable::PrintPaginator($oPage, $oOrdenesTrabajo->GetCountRows($filter), true);
	$arrData 	= $oOrdenesTrabajo->GetAllOrderByIngreso($filter, $oPage);
	
	if ($arrData != NULL) { ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">Chapa y Pintura</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
					<tr>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
					</tr>
					<?php
					$Count = 0;
					foreach ($arrData as $oOrdenTrabajo)
					{
						$CantidadDias = CantidadDiasPasados($oOrdenTrabajo->FechaInicio);
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						
						if ($CantidadDias <= 3)
							$clase = 'ok';
						elseif ($CantidadDias <= 7)
							$clase = 'reg';
						else
							$clase = 'mal';
							
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
						<td width="10%" style="width: 10%">
							<div id="ot-<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="auto-ingresado <?=  $clase ?>">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="center" style="color: #ffffff"><strong>OT N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff"><strong>Dominio: <br><?= $oTallerUnidad->Dominio ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
									</tr>
								</table>
							</td>
						</td>
					<?php
						$Count++;
					}
					?>
					</tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php } ?>
          
    <?php 
	$filter['IdCategoria'] = Categorias::Accesorios;
	$Paginado	= Pageable::PrintPaginator($oPage, $oOrdenesTrabajo->GetCountRows($filter), true);
	$arrData 	= $oOrdenesTrabajo->GetAllOrderByIngreso($filter, $oPage);
	if ($arrData != NULL) { ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">Accesorios</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
					<tr>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
					</tr>
					<?php
					$Count = 0;
					foreach ($arrData as $oOrdenTrabajo)
					{
						$CantidadDias = CantidadDiasPasados($oOrdenTrabajo->FechaInicio);
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						
						if ($CantidadDias <= 3)
							$clase = 'ok';
						elseif ($CantidadDias <= 7)
							$clase = 'reg';
						else
							$clase = 'mal';
							
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
							<div id="ot-<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="auto-ingresado <?=  $clase ?>">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="center" style="color: #ffffff"><strong>OT N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff"><strong>Dominio: <br><?= $oTallerUnidad->Dominio ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
									</tr>
								</table>
							</td>
						</td>
					<?php
						$Count++;
					}
					?>
					</tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php } ?>
          
    <?php 
	$filter['IdCategoria'] = Categorias::PreEntrega;
	$Paginado	= Pageable::PrintPaginator($oPage, $oOrdenesTrabajo->GetCountRows($filter), true);
	$arrData 	= $oOrdenesTrabajo->GetAllOrderByIngreso($filter, $oPage);
	if ($arrData != NULL) { ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><span class="tituloPagina">Preentrega</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
					<tr>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
						<td width="10%"></td>
					</tr>
					<?php
					$Count = 0;
					foreach ($arrData as $oOrdenTrabajo)
					{
						$CantidadDias = CantidadDiasPasados($oOrdenTrabajo->FechaInicio);
						$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
						
						if ($CantidadDias <= 3)
							$clase = 'ok';
						elseif ($CantidadDias <= 7)
							$clase = 'reg';
						else
							$clase = 'mal';
							
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
							<div id="ot-<?= $oOrdenTrabajo->IdOrdenTrabajo ?>" class="auto-ingresado <?=  $clase ?>">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="center" style="color: #ffffff"><strong>OT N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff"><strong>Dominio: <br><?= $oTallerUnidad->Dominio ?></strong></td>
									</tr>
									<tr>
										<td align="center" style="color: #ffffff">D&iacute;as: <?= $CantidadDias ?></td>
									</tr>
								</table>
							</td>
						</td>
					<?php
						$Count++;
					}
					?>
					</tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
          
    <?php } ?>
    
    </table>
</form>
<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>