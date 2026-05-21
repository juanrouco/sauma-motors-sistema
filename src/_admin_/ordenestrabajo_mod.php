<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_CREATE))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$IdEstadoOrden			= intval($_REQUEST['IdEstadoOrden']);
$IdTallerUnidad			= intval($_REQUEST['IdTallerUnidad']);
$FechaInicio			= strval($_REQUEST['FechaInicio']);
$FechaFin				= strval($_REQUEST['FechaFin']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);
$Usuario				= strval($_REQUEST['Usuario']);
$Kilometros				= floatval($_REQUEST['Kilometros']);
$Comentarios			= strval($_REQUEST['Comentarios']);
$HoraInicio				= intval($_REQUEST['HoraInicio']);
$MinutoInicio			= intval($_REQUEST['MinutoInicio']);
$HoraSalida				= intval($_REQUEST['HoraSalida']);
$MinutoSalida			= intval($_REQUEST['MinutoSalida']);
$TelefonoCodigoArea		= strval($_REQUEST['TelefonoCodigoArea']);
$Telefono				= strval($_REQUEST['Telefono']);
$FaxCodigoArea			= strval($_REQUEST['FaxCodigoArea']);
$Fax					= strval($_REQUEST['Fax']);
$Email					= strval($_REQUEST['Email']);
$Dominio				= strval($_REQUEST['Dominio']);
$Bahia					= 0;//intval($_REQUEST['Bahia']);
$NumeroVin				= strval($_REQUEST['NumeroVin']);
$NumeroMotor			= strval($_REQUEST['NumeroMotor']);
$FechaInicioGarantia	= strval($_REQUEST['FechaInicioGarantia']);
$Concesionario			= strval($_REQUEST['Concesionario']);
$DomicilioCalle			= strval($_REQUEST['DomicilioCalle']);
$DomicilioNumero		= strval($_REQUEST['DomicilioNumero']);
$DomicilioPiso			= strval($_REQUEST['DomicilioPiso']);
$DomicilioDpto			= strval($_REQUEST['DomicilioDpto']);
$DomicilioLocalidad		= strval($_REQUEST['DomicilioLocalidad']);
$DomicilioIdLocalidad	= intval($_REQUEST['DomicilioIdLocalidad']);
$DocumentoNumero		= strval($_REQUEST['DocumentoNumero']);
$Submit					= (isset($_REQUEST['Submitted']));

$err				= 0;
$oOrdenTrabajo		= new OrdenTrabajo();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oEstadosOrden		= new EstadosOrden();
$oClientes			= new Clientes();
$oTallerUnidades	= new TallerUnidades();
$oOrdenTrabajoHitos = new OrdenTrabajoHitos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header("Location: ordenestrabajo.php" . $strParams);
	exit();
}

$arrEstadosOrden = $oEstadosOrden->GetAll();
$arrTipoVenta		= TipoVenta::GetAllOrdenTrabajo();

if ($Submit)
{
	if ($IdEstadoOrden == '')
		$err |= 1;
	if ($IdTallerUnidad == '')
		$err |= 2;
	if ($IdEstadoOrden == EstadoOrden::Finalizado)
	{
		$filter = array();
		$filter['IdOrdenTrabajo'] 	= $IdOrdenTrabajo;
		$filter['TipoHito'] 		= OrdenTrabajoHito::Iniciar;
		$countIniciadas = $oOrdenTrabajoHitos->GetCountRows($filter);
		
		$filter['TipoHito'] 		= OrdenTrabajoHito::Detener;
		$countDetenidas = $oOrdenTrabajoHitos->GetCountRows($filter);
		
		$filter['TipoHito'] 		= OrdenTrabajoHito::Finalizar;
		$countFinalizadas = $oOrdenTrabajoHitos->GetCountRows($filter);
		
		if ($countIniciadas > $countDetenidas + $countFinalizadas)
			$err |= 4;
	}
		
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oOrdenTrabajo->IdEstadoOrden		= $IdEstadoOrden;
		$oOrdenTrabajo->IdTallerUnidad		= $IdTallerUnidad;
		$oOrdenTrabajo->FechaInicio			= $FechaInicio . ' ' . str_pad($HoraInicio, 2, 0, STR_PAD_LEFT) . ':' . str_pad($MinutoInicio, 2, 0, STR_PAD_LEFT);
		$oOrdenTrabajo->FechaFin			= $FechaFin . ' ' . str_pad($HoraSalida, 2, 0, STR_PAD_LEFT) . ':' .str_pad( $MinutoSalida, 2, 0, STR_PAD_LEFT);
		
		$oOrdenTrabajo->IdUsuarioAsignado	= $IdUsuario;
		$oOrdenTrabajo->Kilometros			= $Kilometros;
		$oOrdenTrabajo->Comentarios			= $Comentarios;
		$oOrdenTrabajo->Bahia				= $Bahia;
		
		$oOrdenTrabajo = $oOrdenesTrabajo->Update($oOrdenTrabajo);
		
		$oTallerUnidad = $oTallerUnidades->GetById($IdTallerUnidad);
		$oTallerUnidad->Dominio = $Dominio;
		$oTallerUnidad->NumeroVin = $NumeroVin;
		$oTallerUnidad->NumeroMotor = $NumeroMotor;
		$oTallerUnidad->Concesionario = $Concesionario;
		$oTallerUnidad->FechaInicioGarantia = $FechaInicioGarantia;
		$oTallerUnidades->Update($oTallerUnidad);
		
		
		$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
		$oCliente->Email = $Email;
		$oCliente->TelefonoCodigoArea = $TelefonoCodigoArea;
		$oCliente->Telefono = $Telefono;
		$oCliente->FaxCodigoArea = $FaxCodigoArea;
		$oCliente->Fax = $Fax;
		$oCliente->DomicilioCalle = $DomicilioCalle;
		$oCliente->DomicilioNumero = $DomicilioNumero;
		$oCliente->DomicilioPiso = $DomicilioPiso;
		$oCliente->DomicilioDpto = $DomicilioDpto;
		$oCliente->DomicilioIdLocalidad = $DomicilioIdLocalidad;
		$oCliente->DocumentoNumero = $DocumentoNumero;
			
		$oClientes->Update($oCliente);

		header("Location: ordenestrabajo.php" . $strParams);
		exit();
	}
}
else
{
	$IdEstadoOrden 	= $oOrdenTrabajo->IdEstadoOrden;
	$IdTallerUnidad	= $oOrdenTrabajo->IdTallerUnidad;
	$FechaInicio	= date_format(new DateTime($oOrdenTrabajo->FechaInicio), 'd-m-Y');
	$FechaFin		= date_format(new DateTime($oOrdenTrabajo->FechaFin), 'd-m-Y');
	$HoraInicio		= date_format(new DateTime($oOrdenTrabajo->FechaInicio), 'H');
	$HoraSalida		= date_format(new DateTime($oOrdenTrabajo->FechaFin), 'H');
	$MinutoInicio	= date_format(new DateTime($oOrdenTrabajo->FechaInicio), 'i');
	$MinutoSalida	= date_format(new DateTime($oOrdenTrabajo->FechaFin), 'i');
	$IdUsuario		= $oOrdenTrabajo->IdUsuarioAsignado;
	$Kilometros		= $oOrdenTrabajo->Kilometros;
	$Comentarios	= $oOrdenTrabajo->Comentarios;
	$Bahia			= $oOrdenTrabajo->Bahia;
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

function FilterTallerUnidad(IdTallerUnidad, Dominio)
{
	if ((IdTallerUnidad == '') && (Dominio == ''))
	{		
		$j('#Dominio').val('');
		$j('#IdTallerUnidad').val('');
	}

	var oTallerUnidad = GetTallerUnidad(IdTallerUnidad);
	if (!(oTallerUnidad))
		return;
		
	var oCliente = GetCliente(oTallerUnidad.IdCliente);
	if (!(oCliente))
		return;
	
	$j('#Dominio').val(oTallerUnidad.Dominio);
	if (oTallerUnidad.Dominio)
	{
		$j('#Dominio').attr('readonly', 'readonly');
		$j('#Dominio').addClass('camporFormularioSimpleDisabled');
	}
	
	$j('#IdTallerUnidad').val(oTallerUnidad.IdTallerUnidad);
	$j('#lblTallerUnidad').val(oTallerUnidad.Modelo);
	$j('#lblCliente').val(oCliente.RazonSocial);
	$j('#NumeroVin').val(oTallerUnidad.NumeroVin);
	$j('#NumeroMotor').val(oTallerUnidad.NumeroMotor);
	$j('#Concesionario').val(oTallerUnidad.Concesionario);
	if (oTallerUnidad.FechaInicioGarantia)
	{
		var fecha = new Date(oTallerUnidad.FechaInicioGarantia);
		$j('#FechaInicioGarantia').val(fecha.getDate() + '-' + (fecha.getMonth() + 1) + '-' + fecha.getFullYear());
	}
	if (oTallerUnidad.NumeroVin)
	{
		$j('#NumeroVin').attr('readonly', 'readonly');
		$j('#NumeroVin').addClass('camporFormularioSimpleDisabled');
	}
	
	$j('#TelefonoCodigoArea').val(oCliente.TelefonoCodigoArea);
	$j('#Telefono').val(oCliente.Telefono);
	$j('#FaxCodigoArea').val(oCliente.FaxCodigoArea);
	$j('#Fax').val(oCliente.Fax);
	$j('#Email').val(oCliente.Email);
	$j('#DomicilioCalle').val(oCliente.DomicilioCalle);
	$j('#DomicilioNumero').val(oCliente.DomicilioNumero);
	$j('#DomicilioPiso').val(oCliente.DomicilioPiso);
	$j('#DomicilioCalle').val(oCliente.DomicilioCalle);
	$j('#DomicilioDpto').val(oCliente.DomicilioDpto);
	
	if (oCliente.IdTipoPersona == '<?= PersonaTipos::PersonaFisica ?>')
	{
		$j('#DocumentoNumero').val(oCliente.DocumentoNumero);
		$j('#trDocumentoNumero').show();
	}
	else
	{
		$j('#trDocumentoNumero').hide();
	}
	
	if (oCliente.DomicilioIdLocalidad)
	{
		var oLocalidad = GetLocalidad(oCliente.DomicilioIdLocalidad);
		if (oLocalidad)
			FilterLocalidad(oLocalidad.IdLocalidad, oLocalidad.Nombre);
	}
	
	$j('#modal-popup').dialog('close');
}

function FilterUsuario(IdUsuario, Nombre)
{
	if ((IdUsuario == '') && (Nombre == ''))
	{
		Get('IdUsuario').value 	= '';
		Get('Usuario').value 	= '';
	}

	var oUsuario = GetUsuario(IdUsuario);
	if (!(oUsuario))
		return;

	Get('IdUsuario').value 	= oUsuario.IdUsuario;
	Get('Usuario').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function FilterLocalidad(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('DomicilioIdLocalidad').value 	= '';
		Get('DomicilioLocalidad').value 	= '';
	}

	Get('DomicilioIdLocalidad').value 	= IdLocalidad;
	Get('DomicilioLocalidad').value 	= Nombre;
}

$j(document).ready(function() {
	$j('#buscar-codigos').click(function(e) {
		e.preventDefault();
		
		RealizarBusquedaPopup('tallerunidades_buscar_popup.php', {}, 'Unidades');
	});
	<?php
	if ($IdTallerUnidad)
	{
	?>
	FilterTallerUnidad(<?= $IdTallerUnidad ?>, '');
	<?php
	}
	if ($IdUsuario)
	{
	?>
	FilterUsuario(<?= $IdUsuario ?>, '');
	<?php
	}
	?>
});

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de ordenes de trabajo - Modificar</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                    <input type="hidden" name="IdTallerUnidad" id="IdTallerUnidad" value="<?=$IdTallerUnidad?>" />
					<input type="hidden" name="IdUsuario" id="IdUsuario" value="<?= $IdUsuario ?>" />
                 	
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td valign="top">
                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">                                                           
                                                            <tr>
																<td colspan="2">		
																	<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" class="bordeGris">
																		<tr class="bordeGrisFondo">
																			<td height="25"><div id="margen"><strong>Datos de la Unidad</strong></div></td>
																		</tr>
																		<tr>
																			<td>
																				<table width="100%" cellpadding="0" cellspacing="0" border="0">
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Patente: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="Dominio" id="Dominio" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Dominio ?>" autocomplete="off" style="width: 225px" />
																							<a id="buscar-codigos" href="#"><img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" /></a>
																							<span style="color:#FF0000;">&nbsp;(*)</span>
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Modelo: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="lblTallerUnidad" id="lblTallerUnidad" class="camporFormularioSimpleDisabled" onkeyup="javascript: StrToUpper(this.id);" value="<?= $lblTallerUnidad ?>" autocomplete="off" style="width: 225px" readonly="readonly" />
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">N&deg; Chasis: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?= $lblTallerUnidad ?>" autocomplete="off" style="width: 225px" />
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">N&deg; Motor: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?= $NumeroMotor ?>" autocomplete="off" style="width: 225px" />
																						</td>
																					</tr>
																					<tr>
																						<td><div align="right">Kil&oacute;metros:</div></td>
																						<td>
																							<div align="left">
																								<input type="text" name="Kilometros" id="Kilometros" class="camporFormularioMediano" value="<?=$Kilometros?>"/>																		
																							</div>
																						</td>
																					</tr>
																					<tr>
																						<td><div align="right">Fecha Venta:</div></td>
																						<td>
																							<div align="left">
																								<input type="text" name="FechaInicioGarantia" id="FechaInicioGarantia" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$FechaInicioGarantia?>" readonly="readonly" />
																								<script language="javascript">
																									new tcal({'formname': 'frmData', 'controlname': 'FechaInicioGarantia'});
																								</script>																	
																							</div>
																						</td>
																					</tr>
																					<tr>
																						<td><div align="right">Conc Vend:</div></td>
																						<td>
																							<div align="left">
																								<input type="text" name="Concesionario" id="Concesionario" class="camporFormularioSimple" size="12" maxlength="255" value="<?=$Concesionario?>" onkeyup="javascript: StrToUpper(this.id);" />																			
																							</div>
																						</td>
																					</tr>
																					
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>																	
															<tr>											
																<td height="20" colspan="2"><?php if ($err & 2) { ?><li style="color:#FF0000;">Debe ingresar una unidad de taller</li><?php } ?></td>
															</tr>
															<tr>
																<td colspan="2" height="25">&nbsp;</td>
															</tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" class="bordeGris">
																		<tr class="bordeGrisFondo">
																			<td height="25"><div id="margen"><strong>Datos del Cliente</strong></div></td>
																		</tr>
																		<tr>
																			<td>
																				<table width="100%" cellpadding="0" cellspacing="0" border="0">
																					
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Cliente: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="lblCliente" id="lblCliente" class="camporFormularioSimpleDisabled" onkeyup="javascript: StrToUpper(this.id);" value="<?= $lblCliente ?>" autocomplete="off" style="width: 225px" readonly="readonly" />
																						</td>
																					</tr>
																					<tr id="trDocumentoNumero">
																						<td valign="niddle">
																							<div align="right">N&deg; Documento: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="">
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Tel&eacute;fono: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="128" value="">
																							<input type="text" name="Telefono" id="Telefono" class="camporFormularioMedianoI" maxlength="128" value="">
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Tel&eacute;fono 2: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="FaxCodigoArea" id="FaxCodigoArea" class="camporFormularioChico" maxlength="128" value="">
																							<input type="text" name="Fax" id="Fax" class="camporFormularioMedianoI" maxlength="128" value="">
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Email: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="">
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Domicilio: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="DomicilioCalle" id="DomicilioCalle" class="camporFormularioSimple" maxlength="128" value="">
																						</td>
																					</tr>
																					<tr>
																						<td valign="middle">
																							<div align="right">N&uacute;mero: &nbsp;</div>
																						</td>
																						<td valign="middle">
																							<table width="100%" border="0" cellpadding="0" cellspacing="0">
																								<td valign="top">
																									<input type="text" name="DomicilioNumero" id="DomicilioNumero" class="camporFormularioChico" maxlength="128" value="">
																								</td>
																								<td valign="niddle">
																									<div align="right">&nbsp;P:</div>
																								</td>
																								<td valign="top">
																									<input type="text" name="DomicilioPiso" id="DomicilioPiso" class="camporFormularioChico" maxlength="128" value="">
																								</td>
																								<td valign="niddle">
																									<div align="right">&nbsp;D:</div>
																								</td>
																								<td valign="top">
																									<input type="text" name="DomicilioDpto" id="DomicilioDpto" class="camporFormularioChico" maxlength="128" value="">
																								</td>
																							</table>
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Localidad: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="DomicilioLocalidad" id="DomicilioLocalidad" class="camporFormularioSimple" maxlength="128" value="" autocomplete="off" />
																							<input type="hidden" name="DomicilioIdLocalidad" id="DomicilioIdLocalidad" value="">
																							<script language="javascript">
																								SUGGESTRequest('Localidades', 'GetAllSuggest', 'DomicilioLocalidad', 'FilterLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
																							</script>
																						</td>
																					</tr>
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
													        
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" class="bordeGris">
																		<tr class="bordeGrisFondo">
																			<td height="25"><div id="margen"><strong>Datos del Turno</strong></div></td>
																		</tr>
																		<tr>
																			<td>
																				<table width="100%" cellpadding="0" cellspacing="0" border="0">
																					
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Fecha Ingreso: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="FechaInicio" id="FechaInicio" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$FechaInicio?>" readonly="readonly" />
																							<script language="javascript">
																								new tcal({'formname': 'frmData', 'controlname': 'FechaInicio'});
																							</script>
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Horario Ingreso: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<select class="camporFormularioMediano" style="width: 50px" id="HoraInicio" name="HoraInicio">
																							<?php
																								for ($Hora = 0; $Hora < 24; $Hora++)
																								{
																									$selected = '';
																									if ($Hora == intval($HoraInicio))
																										$selected = 'selected="selected"';
																									
																							?>
																								<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
																							<?php
																								}
																							?>
																							</select> : <select class="camporFormularioMediano" style="width: 50px" id="MinutoInicio" name="MinutoInicio">
																							<?php
																								for ($Minuto = 0; $Minuto < 60; $Minuto++)
																								{
																									$selected = '';
																									if ($Minuto == intval($MinutoInicio))
																										$selected = 'selected="selected"';
																							?>
																								<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
																							<?php
																								}
																							?>
																							</select>
																						</td>
																					</tr>
																					
																					<tr>
																						<td valign="niddle">
																							<div align="right">Fecha Salida: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="FechaFin" id="FechaFin" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$FechaFin?>" readonly="readonly" />
																							<script language="javascript">
																								new tcal({'formname': 'frmData', 'controlname': 'FechaFin'});
																							</script>
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Horario Salida: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<select class="camporFormularioMediano" style="width: 50px" id="HoraSalida" name="HoraSalida">
																							<?php
																								for ($Hora = 0; $Hora < 24; $Hora++)
																								{
																									$selected = '';
																									if ($Hora == intval($HoraSalida))
																										$selected = 'selected="selected"';
																									
																							?>
																								<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
																							<?php
																								}
																							?>
																							</select> : <select class="camporFormularioMediano" style="width: 50px" id="MinutoSalida" name="MinutoSalida">
																							<?php
																								for ($Minuto = 0; $Minuto < 60; $Minuto++)
																								{
																									$selected = '';
																									if ($Minuto == intval($MinutoSalida))
																										$selected = 'selected="selected"';
																							?>
																								<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
																							<?php
																								}
																							?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td valign="niddle">
																							<div align="right">Usuario Asignado: &nbsp;</div>
																						</td>
																						<td valign="top">
																							<input type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="Off" />
																							<script language="javascript">
																								var arrParams = new Array();
																								arrParams['FilterIdPerfil'] = '<?=Usuario::Taller?>';
																								SUGGESTRequest('Usuarios', 'GetAllSuggest', 'Usuario', 'FilterUsuario', 'IdUsuario', 'Nombre', 'FilterUsuario', arrParams);
																							</script>
																						</td>
																					</tr>
																					<tr>
																						<td><div align="right">Estado:</div></td>
																						<td>
																							<div align="left">
																								<select id="IdEstadoOrden" name="IdEstadoOrden" class="camporFormularioSimple">
																									<option value>Seleccione un Estado</option>
																									<?php
																									foreach ($arrEstadosOrden as $oEstadoOrden)
																									{
																										$selected = '';
																										if ($IdEstadoOrden == $oEstadoOrden->IdEstado)
																											$selected = 'selected="true"';
																									?>
																										<option value="<?= $oEstadoOrden->IdEstado ?>" <?= $selected ?>><?= $oEstadoOrden->Nombre ?></option>
																									<?php
																									}
																									?>
																								</select>
																								<span style="color:#FF0000;">&nbsp;(*)</span>                                                                        
																							</div>
																						</td>
																					</tr>
																					<tr>
																						<td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione un estado</li><?php } ?></td>
																					</tr>
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															
															<?php /*<tr>
                                                                <td><div align="right">Fecha Salida:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="FechaFin" id="FechaFin" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$FechaFin?>" readonly="readonly" />
																		<script language="javascript">
																			new tcal({'formname': 'frmData', 'controlname': 'FechaFin'});
																		</script>
																	</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
                                                                <td><div align="right">Horario Salida:</div></td>
																<td>
																	<div align="left">
																		<select class="camporFormularioMediano" style="width: 50px" id="HoraSalida" name="HoraSalida">
																		<?php
																			for ($Hora = 0; $Hora < 24; $Hora++)
																			{
																				$selected = '';
																				if ($Hora == intval($HoraSalida))
																					$selected = 'selected="selected"';
																				
																		?>
																			<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
																		<?php
																			}
																		?>
																		</select> : <select class="camporFormularioMediano" style="width: 50px" id="MinutoSalida" name="MinutoSalida">
																		<?php
																			for ($Minuto = 0; $Minuto < 60; $Minuto++)
																			{
																				$selected = '';
																				if ($Minuto == intval($MinutoSalida))
																					$selected = 'selected="selected"';
																		?>
																			<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
																		<?php
																			}
																		?>
																		</select>
																	</div>
                                                                </td>
                                                            </tr> */ ?>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr> 
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
																<td colspan="2">
																	<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" class="bordeGris">
																		<tr class="bordeGrisFondo">
																			<td height="25"><div id="margen"><strong>Comentarios</strong></div></td>
																		</tr>
																		<tr>
																			<td>
																				<table width="100%" cellpadding="0" cellspacing="0" border="0">
																					
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																					<tr>
																						<td colspan="2" align="center" valign="top">
																							<textarea name="Comentarios" id="Comentarios" class="camporFormularioSuggest" onkeyup="javascript: StrToUpper(this.id);" style="width: 300px;height: 150px"><?=$Comentarios?></textarea>
																						</td>
																					</tr>
																					<tr>
																						<td colspan="2" height="25">&nbsp;</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>  
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>									
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

<script language="javascript">
LoadListas('CodigoComercial', '<?=$NumeroVinPrefijo?>', '<?=$CodigoComercial?>');
</script>

<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>