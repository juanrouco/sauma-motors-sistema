<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ORDS_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUsado						= intval($_REQUEST['IdUsado']);
$IdCliente						= intval($_REQUEST['IdCliente']);
$IdTipoDestinatario				= intval($_REQUEST['IdTipoDestinatario']);
$Transporte						= strval($_REQUEST['Transporte']);
$TransporteClaveFiscalTipo		= intval($_REQUEST['TransporteClaveFiscalTipo']);
$TransporteClaveFiscalNumero	= strval($_REQUEST['TransporteClaveFiscalNumero']);
$AdquirienteRazonSocial			= strval($_REQUEST['AdquirienteRazonSocial']);
$AdquirienteDocumentoTipo		= intval($_REQUEST['AdquirienteDocumentoTipo']);
$AdquirienteDocumentoTipoNombre	= strval($_REQUEST['AdquirienteDocumentoTipoNombre']);
$AdquirienteDocumentoTipoCodigo	= strval($_REQUEST['AdquirienteDocumentoTipoCodigo']);
$AdquirienteDocumentoNumero		= strval($_REQUEST['AdquirienteDocumentoNumero']);
$Fecha							= strval($_REQUEST['Fecha']);
$EntregaManuales				= intval($_REQUEST['EntregaManuales']);
$EntregaLlaves					= intval($_REQUEST['EntregaLlaves']);
$EntregaTarjetaCode				= intval($_REQUEST['EntregaTarjetaCode']);
$EntregaDocumentacion			= intval($_REQUEST['EntregaDocumentacion']);
$IdUbicacion					= intval($_REQUEST['IdUbicacion']);
$Submit							= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oMinutas 		= new MinutasUsados();
$oOrdenSalida 	= new OrdenSalidaUsado();
$oOrdenesSalida	= new OrdenesSalidaUsados();
$oClientes		= new Clientes();
$oUbicaciones	= new Ubicaciones();
$oUsados		= new Usados();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

$arrUbicaciones = $oUbicaciones->GetAll();

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if (($IdUsado == '') || (!$oUsados->GetById($IdUsado)))
		$err |= 1;
	if ($Fecha == '')
		$err |= 2;
	if ($IdTipoDestinatario == '')
		$err |= 4;
	if ($IdTipoDestinatario == OrdenSalidaDestinatarios::Transporte)
	{
		if ($Transporte == '')
			$err |= 8;
		if ($TransporteClaveFiscalNumero == '')
			$err |= 16;
		if ($IdUbicacion == '')
			$err |= 256;
	}
	elseif ($IdTipoDestinatario == OrdenSalidaDestinatarios::Tercero)
	{
		if ($AdquirienteRazonSocial == '')
			$err |= 32;
		if ($AdquirienteDocumentoTipo == '')
			$err |= 64;
		if ($AdquirienteDocumentoNumero == '')
			$err |= 128;
	}
	else
	{
		if ($IdCliente == '')
			$err |= 512;
		if ($IdUbicacion == '')
			$err |= 256;
		
		$oMinuta = $oMinutas->GetById($IdUsado);
		if ($oMinuta->GetTotalPendiente() != 0)
			$err |= 1024;
	}

	/* si no hay errores... */
	if ($err == 0)
	{
		/* borramos algunos campos segun el tipo de destinatario */
		if ($IdTipoDestinatario != OrdenSalidaDestinatarios::Transporte)
		{
			$Transporte 					= '';
			$TransporteClaveFiscalTipo 		= '';
			$TransporteClaveFiscalNumero 	= '';
		}
		if ($IdTipoDestinatario != OrdenSalidaDestinatarios::Tercero)
		{
			$AdquirienteRazonSocial 	= '';
			$AdquirienteDocumentoTipo 	= '';
			$AdquirienteDocumentoNumero = '';
		}
		else
		{
			$oCliente = $oClientes->GetById($IdCliente);
			if ($IdTipoDestinatario == OrdenSalidaDestinatarios::Cliente)
			{
				$AdquirienteRazonSocial 	= '';
				$AdquirienteDocumentoTipo 	= $oCliente->DocumentoTipo;
				$AdquirienteDocumentoNumero = '';
			}
		}

		$oOrdenSalida->IdUsado 						= $IdUsado;
		$oOrdenSalida->IdCliente 						= $IdCliente;
		$oOrdenSalida->IdTipoDestinatario				= $IdTipoDestinatario;
		$oOrdenSalida->Transporte						= $Transporte;
		$oOrdenSalida->TransporteClaveFiscalTipo		= $TransporteClaveFiscalTipo;
		$oOrdenSalida->TransporteClaveFiscalNumero		= $TransporteClaveFiscalNumero;
		$oOrdenSalida->AdquirienteRazonSocial			= $AdquirienteRazonSocial;
		$oOrdenSalida->AdquirienteDocumentoTipo			= $AdquirienteDocumentoTipo;
		$oOrdenSalida->AdquirienteDocumentoTipoCodigo	= $AdquirienteDocumentoTipoCodigo;
		$oOrdenSalida->AdquirienteDocumentoNumero		= $AdquirienteDocumentoNumero;
		$oOrdenSalida->Fecha 							= $Fecha;
		$oOrdenSalida->EntregaManuales 					= $EntregaManuales;
		$oOrdenSalida->EntregaLlaves 					= $EntregaLlaves;
		$oOrdenSalida->EntregaTarjetaCode				= $EntregaTarjetaCode;
		$oOrdenSalida->EntregaDocumentacion				= $EntregaDocumentacion;
		$oOrdenSalida->IdUbicacion						= $IdUbicacion;

		if ($oOrdenSalida = $oOrdenesSalida->Create($oOrdenSalida))
		{
			$oUsado = $oUsados->GetById($IdUsado);
			if ($IdTipoDestinatario == OrdenSalidaDestinatarios::Cliente)
				$oUsado->IdEstado = EstadoUnidad::Entregado;
			$oUsado->IdUbicacion = $IdUbicacion;
			$oUsado->FechaRetiro = $Fecha;
			$oUsados->Update($oUsado);
		}

		header("Location: ordenessalidausados.php" . $strParams);
		exit();
	}
}
else
{
	$IdTipoDestinatario = OrdenSalidaDestinatarios::Cliente;
	$EntregaManuales = 0;
	$EntregaLlaves = 0;
	$EntregaTarjetaCode = 0;
	$EntregaDocumentacion = 0;
	
	/* determinamos como fecha a la fecha de mañana */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterTipoDocumento(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('AdquirienteDocumentoTipoCodigo').value = '';
		Get('AdquirienteDocumentoTipoNombre').value = '';
		Get('AdquirienteDocumentoTipo').value 		= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('AdquirienteDocumentoTipoCodigo').value = oTipoDocumento.Codigo;
	Get('AdquirienteDocumentoTipoNombre').value = oTipoDocumento.Nombre;
	Get('AdquirienteDocumentoTipo').value 		= oTipoDocumento.IdTipoDocumento;
}


function SetUnidad(IdUsado)
{
	var oMinuta;
	var oUsado;
	var oModelo;
	var oTipoModelo;
	var oColor;
	var oMarca;
	var oMarcaMotor;
	var oMarcaChasis;
	var oCliente;
	var oTipoiva;
	var oLocalidad;
	var oProvincia;

	if ((oMinuta = GetMinutaUsado(IdUsado)))
	{
		FilterCliente(oMinuta.IdCliente, '');
	}

	if (!(oUsado = GetUsado(IdUsado)))
		return;

	if (!(oColor = GetColor(oUsado.IdColor)))
		return;

	

	HideSection('IdMinutaError');

	
	
	Get('VehiculoMarca').innerHTML 					= oMarca.Nombre;
	Get('VehiculoTipo').innerHTML 					= oTipoModelo.Nombre;
	Get('VehiculoAnio').innerHTML 					= oModelo.Anio;
	Get('VehiculoModelo').innerHTML 				= oModelo.DenominacionModelo;
	Get('VehiculoNumeroMotor').innerHTML 			= oUsado.NumeroMotor + ' - ' + oMarcaMotor.Nombre;
	Get('VehiculoNumeroChasis').innerHTML 			= oUsado.NumeroChasis + ' - ' + oMarcaChasis.Nombre;
	Get('VehiculoColor').innerHTML					= oColor.Nombre;
	Get('VehiculoDenominacionComercial').innerHTML 	= oModelo.DenominacionComercial;
	Get('VehiculoCodigoLlaves').innerHTML 			= oUsado.CodigoLlaves;
	
	
	/* guardamos el numero de operacion */
	Get('IdUsado').value = IdUsado;
	
	ShowSection('trDatosOrdenSalidaTitulo');
	ShowSection('trDatosOrdenSalida');
}

function VerificarTipoDestinatario(IdTipoDestinatario)
{
	HideSection('trTransporte');
	HideSection('trTransporteError');
	HideSection('trTransporteClaveFiscal');
	HideSection('trTransporteClaveFiscalError');
	HideSection('trAdquirienteRazonSocial');
	HideSection('trAdquirienteRazonSocialError');
	HideSection('trAdquirienteDocumentoTipo');
	HideSection('trAdquirienteDocumentoTipoError');
	HideSection('trAdquirienteDocumentoNumero');
	HideSection('trAdquirienteDocumentoNumeroError');
	HideSection('trUbicacion');
	HideSection('trUbicacionError');
	HideSection('trCliente');
	HideSection('trClienteError');
	
	if (IdTipoDestinatario == '<?=OrdenSalidaDestinatarios::Transporte?>')
	{
		ShowSection('trTransporte');
		ShowSection('trTransporteError');
		ShowSection('trTransporteClaveFiscal');
		ShowSection('trTransporteClaveFiscalError');
		ShowSection('trUbicacion');
		ShowSection('trUbicacionError');
	}
	if (IdTipoDestinatario == '<?=OrdenSalidaDestinatarios::Cliente?>')
	{
		ShowSection('trCliente');
		ShowSection('trClienteError');
		ShowSection('trUbicacion');
		ShowSection('trUbicacionError');
		$j('#IdUbicacion').val('9');
	}
}

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	
	oLocalidad = GetLocalidad(oCliente.DomicilioIdLocalidad);
	oTipoIva = GetTipoIva(oCliente.IdTipoIva);
			
	Get('ClienteRazonSocial').innerHTML 			= oCliente.RazonSocial;
	Get('ClienteDomicilio').innerHTML 				= oCliente.DomicilioCalle + ' ' + oCliente.DomicilioNumero;
	Get('ClienteLocalidad').innerHTML 				= oLocalidad.Nombre;
	Get('ClienteCodigoPostal').innerHTML 			= oLocalidad.CodigoPostal;
	Get('ClienteTelefono').innerHTML 				= oCliente.TelefonoCodigoArea + ' ' + oCliente.Telefono;
	Get('ClienteCondicionIva').innerHTML 			= oTipoIva.Nombre;
	Get('ClienteCuit').innerHTML 					= oCliente.ClaveFiscalNumero;
	
}

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de &Oacute;rdenes de Salida de Usados - Agregar</span></td>
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
					<input type="hidden" name="IdCliente" id="IdCliente" value="<?= $IdCliente ?>" />
				    <input type="hidden" name="AdquirienteDocumentoTipo" id="AdquirienteDocumentoTipo" value="<?=$AdquirienteDocumentoTipo?>" />
                    
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
                                                    <td><div align="right">Nro. Interno:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="IdUsado" id="IdUsado" class="camporFormularioChico" maxlength="10" onblur="javascript: SetUnidad(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$IdUsado?>" />
	                                                    </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25">
														<?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione o ingrese un nro. de carpeta v&aacute;lido</li><?php } else { ?><li style="color:#FF0000; display:none;" id="IdMinutaError">El nro. ingresado no existe</li><?php } ?>
														<?php if ($err & 1024) { ?><li style="color:#FF0000;">La unidad seleccionada posee deuda</li><?php } ?>
													</td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                            <script language="javascript">
                                                            new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                            </script>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese la fecha</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Adquiriente:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <select name="IdTipoDestinatario" id="IdTipoDestinatario" class="camporFormularioSimple" onchange="javascript: VerificarTipoDestinatario(this.value);">
                                                                <option value="">[SELECCIONE]</option>
                                                                <?php foreach (OrdenSalidaDestinatarios::GetAll() as $oDestinatario) { ?>
                                                                <option value="<?=$oDestinatario['IdTipoDestinatario']?>" <?=($IdTipoDestinatario == $oDestinatario['IdTipoDestinatario']) ? 'selected="selected"' : ''?> ><?=$oDestinatario['Descripcion']?></option>
                                                                <?php } ?>
                                                            </select>                                                     	
                                                    	</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 4) { ?><li style="color:#FF0000;">Seleccione quien es el adquiriente</li><?php } ?><?php if ($err & 512) { ?><li style="color:#FF0000;">La unidad no puede ser entregada al cliente ya que no posee prenda.</li><?php } ?></td>
                                                </tr>
												<tr id="trCliente" style="display:none;">
                                                    <td><div align="right">Cliente:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Cliente?>" autocomplete="off" />
															<script language="javascript">
																SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
															</script>
															<input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " />
                                                        </div>
														
                                                    </td>
                                                </tr>
                                                <tr id="trClienteError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
                                                </tr>
                                                <tr id="trTransporte" style="display:none;">
                                                    <td><div align="right">Trasporte:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <textarea name="Transporte" id="Transporte" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$Transporte?></textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trTransporteError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese la empresa de transporte</li><?php } ?></td>
                                                </tr>
                                                <tr id="trTransporteClaveFiscal" style="display:none;">
                                                    <td><div align="right">Transporte CUIT/CUIL:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<table width="100%" border="0">
                                                            	<tr>
                                                                	<td width="28%">
                                                                    	<select name="TransporteClaveFiscalTipo" id="TransporteClaveFiscalTipo" class="camporFormularioChico">
                                                                        	<?php foreach (ClaveFiscalTipos::GetAll() as $oClaveFiscal) { ?>
                                                                            <option value="<?=$oClaveFiscal['IdTipo']?>" <?=($TransporteClaveFiscalTipo == $oClaveFiscal['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oClaveFiscal['Descripcion']?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                 	</td>
                                                                	<td width="72%">
                                                            			<input type="text" name="TransporteClaveFiscalNumero" id="TransporteClaveFiscalNumero" class="camporFormularioMedianoI" maxlength="16" value="<?=$TransporteClaveFiscalNumero?>" />
                                                                 	</td>
                                                             	</tr>
                                                         	</table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trTransporteClaveFiscalError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese el nro. de cuit del transporte</li><?php } ?></td>
                                                </tr>
												<tr id="trUbicacion" style="display:none;">
                                                    <td><div align="right">Destino:</div></td>
                                                    <td>
                                                        <div align="left">
															<select name="IdUbicacion" id="IdUbicacion" class="camporFormularioSimple">
																<option value="">Seleccione Destino</option>
																<?php
																foreach ($arrUbicaciones as $oUbicacion)
																{
																	$selected = '';
																	if ($oUbicacion->IdUbicacion == $IdUbicacion)
																		$selected = 'selected="selected"';
																?>
																<option value="<?= $oUbicacion->IdUbicacion ?>" <?= $selected ?>><?= $oUbicacion->Nombre ?></option>
																<?php
																}
																?>
															</select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trUbicacionError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 256) { ?><li style="color:#FF0000;">Ingrese la empresa de transporte</li><?php } ?></td>
                                                </tr>
                                                <tr id="trAdquirienteRazonSocial" style="display:none;">
                                                    <td><div align="right">Razon Social / Apellido y Nombres:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="AdquirienteRazonSocial" id="AdquirienteRazonSocial" class="camporFormularioSimple" maxlength="128" value="<?=$AdquirienteRazonSocial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trAdquirienteRazonSocialError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese raz&oacute;n social del adquiriente</li><?php } ?></td>
                                                </tr>
                                                <tr id="trAdquirienteDocumentoTipo" style="display:none;">
                                                    <td><div align="right">Tipo Documento:</div></td>
                                                    <td>
                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="AdquirienteDocumentoTipoNombre" id="AdquirienteDocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$AdquirienteDocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                        <script language="javascript">
                                                                        SUGGESTRequest('TiposDocumento', 'GetAll', 'AdquirienteDocumentoTipoNombre', 'FilterTipoDocumento', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                        </script>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="AdquirienteDocumentoTipoCodigo" id="AdquirienteDocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$AdquirienteDocumentoTipoCodigo?>" readonly="readonly" />
                                                                        
                                                                    </div>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('Documento');" value=" + " /></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr id="trAdquirienteDocumentoTipoError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese tipo documento del adquiriente</li><?php } ?></td>
                                                </tr>
                                                <tr id="trAdquirienteDocumentoNumero" style="display:none;">
                                                    <td><div align="right">Nro. Documento:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="AdquirienteDocumentoNumero" id="AdquirienteDocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$AdquirienteDocumentoNumero?>" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trAdquirienteDocumentoNumeroError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese nro. documento del adquiriente</li><?php } ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosOrdenSalidaTitulo" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Operaci&oacute;n</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosOrdenSalida" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Fecha Venta:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="MinutaFecha"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Domicilio:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="36%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilio"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tel&eacute;fono:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="ClienteTelefono"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Condici&oacute;n IVA:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="43%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCondicionIva"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuit"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Marca:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="VehiculoMarca"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tipo:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="53%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoTipo"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="7%"><div align="left">A&ntilde;o:</div></td>
                                                                            	<td width="40%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoAnio"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Modelo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoModelo"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Motor:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroMotor"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Chasis:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroChasis"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Color:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoColor"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Equipo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoDenominacionComercial"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">C&oacute;digo de llaves:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoCodigoLlaves"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<label>
												<input type="checkbox" name="EntregaManuales" id="EntregaManuales" value="1" <?=($EntregaManuales) ? 'checked="checked"' : ''?> />&nbsp;Se entregan los manuales
                                            </label>
										</td>
									</tr>
									<tr>
										<td>
											<label>
												<input type="checkbox" name="EntregaLlaves" id="EntregaLlaves" value="1" <?=($EntregaLlaves) ? 'checked="checked"' : ''?> />&nbsp;Se entregan las llaves
                                            </label>
										</td>
									</tr>
									<tr>
										<td>
											<label>
												<input type="checkbox" name="EntregaTarjetaCode" id="EntregaTarjetaCode" value="1" <?=($EntregaTarjetaCode) ? 'checked="checked"' : ''?> />&nbsp;Se entrega la tarjeta code
                                            </label>
										</td>
									</tr>
									<tr>
										<td>
											<label>
												<input type="checkbox" name="EntregaDocumentacion" id="EntregaDocumentacion" value="1" <?=($EntregaDocumentacion) ? 'checked="checked"' : ''?> />&nbsp;Se entrega la documentaci&oacute;n
                                            </label>
										</td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenessalidausados.php<?=$strParams?>';" value="Cancelar" />
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
VerificarTipoDestinatario('<?=$IdTipoDestinatario?>');
<?php if ($IdUsado != '') { ?>
SetUnidad('<?=$IdUsado?>');
<?php } ?>
</script>

</body>
</html>