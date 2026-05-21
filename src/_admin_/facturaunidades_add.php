<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta			= intval($_REQUEST['IdMinuta']);
$IdComprobante		= intval($_REQUEST['IdComprobante']);
$NumeroComprobante	= strval($_REQUEST['NumeroComprobante']);
$Fecha				= strval($_REQUEST['Fecha']);
$Subtotal			= floatval($_REQUEST['Subtotal']);
$Iva10				= floatval($_REQUEST['Iva10']);
$Iva21				= floatval($_REQUEST['Iva21']);
$ImpuestoInterno	= floatval($_REQUEST['ImpuestoInterno']);
$Total				= floatval($_REQUEST['Total']);
$OtrosTitulares		= strval($_REQUEST['OtrosTitulares']);
$Observaciones		= strval($_REQUEST['Observaciones']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oFacturaUnidad 	= new FacturaUnidad();
$oFacturaUnidades	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if (($IdMinuta == '') || (!$oMinuta = $oMinutas->GetById($IdMinuta)))
		$err |= 1;
	if ($NumeroComprobante == '')
		$err |= 2;
	if ($Fecha == '')
		$err |= 4;
	
	/* validamos los importes de la compra */
	if ($oMinutas->GetById($IdMinuta))
	{
		/* obtenemos los datos de la venta */
		if (!$oMinuta = $oMinutas->GetById($IdMinuta))
		{	
			header("Location: facturaunidades.php" . $strParams);
			exit();
		}

		/* obtenemos los datos del cliente */
		if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
		{	
			header("Location: facturaunidades.php" . $strParams);
			exit();
		}

		/* obtenemos los datos de condicion de iva del cliente */
		if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
		{	
			header("Location: facturaunidades.php" . $strParams);
			exit();
		}

		/* obtenemos los datos de la unidad */
		if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
		{	
			header("Location: facturaunidades.php" . $strParams);
			exit();
		}

		/* obtenemos los datos del modelo */
		if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
		{	
			header("Location: facturaunidades.php" . $strParams);
			exit();
		}

		if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA)
		{
			if ($Subtotal == '')
				$err |= 8;
			if ($oModelo->Iva == '10.5')
			{
				if ($Iva10 == '')
					$err |= 16;
			}
			elseif ($oModelo->Iva == '21')
			{
				if ($Iva21 == '')
					$err |= 32;
			}
		}
		else
		{
			$Subtotal 	= $Total;
			$Iva10 		= 0;
			$Iva21 		= 0;
		}
		
		if ($Total == '')
			$err |= 64;
		if ($oMinuta->GetTotalPendiente() > 0.1)
			$err |= 512;
		if ($oComprobante = $oComprobantes->GetById($IdComprobante))
		{
		
			if ($oComprobante->IdEstado != ComprobanteEstados::Libre)
				$err |= 128;
			if ($oComprobante->Numero != $NumeroComprobante || strlen($oComprobante->Numero) != strlen($NumeroComprobante))
				$err |= 256;
		}
		else
		{
			$err |= 512;
		}
	}
	/* si no hay errores... */
	if ($err == 0)
	{
		$oFacturaUnidad->IdMinuta			= $IdMinuta;
		$oFacturaUnidad->IdComprobante		= $IdComprobante;
		$oFacturaUnidad->NumeroComprobante	= $NumeroComprobante;
		$oFacturaUnidad->Fecha				= $Fecha;
		$oFacturaUnidad->Subtotal			= $Subtotal;
		$oFacturaUnidad->Iva10				= $Iva10;
		$oFacturaUnidad->Iva21				= $Iva21;
		$oFacturaUnidad->Total				= $Total;
		$oFacturaUnidad->ImpuestoInterno	= $ImpuestoInterno;
		$oFacturaUnidad->OtrosTitulares		= $OtrosTitulares;
		$oFacturaUnidad->Observaciones		= $Observaciones;

		if ($oFacturaUnidad = $oFacturaUnidades->Create($oFacturaUnidad))
		{
			/* actualizamos el estado del comprobante */
			if ($oComprobante = $oComprobantes->GetById($IdComprobante))
			{
				$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
				$oComprobante->IdCliente = $oCliente->IdCliente;
				$oComprobante->Fecha = $oFacturaUnidad->Fecha;
				$oComprobante->Importe = $Total;
				$oComprobante->ImpuestoInterno = $oUnidad->ImpuestoInterno;
				if ($oUnidad->ImpuestoInternoD)
					$oComprobante->ImpuestoInterno += $oUnidad->ImpuestoInternoD;
				if ($oModelo->Iva == '10.5')
				{
					$oComprobante->ImporteIva10 = ($Total - $oComprobante->ImpuestoInterno) / 1.105 * 0.105;
				}
				elseif ($oModelo->Iva == '21')
				{
					$oComprobante->ImporteIva21 = ($Total - $oComprobante->ImpuestoInterno) / 1.21 * 0.21;
				}
				
				$oComprobantes->Update($oComprobante);
			}

			/* Actualizamos el estado de la unidad */
			if ($oUnidad->IdEstado == EstadoUnidad::Reservado)
				$oUnidad->IdEstado = EstadoUnidad::Facturado;
			
			$oUnidades->Update($oUnidad);
		}

		header("Location: facturaunidades.php" . $strParams);
		exit();
	}
}
else
{
	$Fecha = date("d-m-Y");
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

var IdTipoComprobante = '';
var arrParams = new Array();

function GetNextFactura(IdTipoComprobante)
{
	var arr = new Array();
	var obj;
	var oComprobante;

	if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
		return;
				
	arr['IdTipoComprobante'] = IdTipoComprobante;
	obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oComprobante = obj.Response;

	return oComprobante;	
}

function SelectMinuta()
{
	var Url = 'minutas.php?MainAction=SelectFacturacion';
	
	window.open(Url, this.target, 'width=1000,height=600'); 
}

function SetMinuta(IdMinuta)
{
	var oMinuta;
	var oUnidad;
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

	HideSection('trSubtotal');
	HideSection('trSubtotalError');
	HideSection('trTotal');
	HideSection('trTotalError');
	HideSection('trIva10');
	HideSection('trIva10Error');
	HideSection('trIva21');
	HideSection('trIva21Error');
	HideSection('trDatosFacturaUnidadTitulo');
	HideSection('trDatosFacturaUnidad');

	if (!(oMinuta = GetMinuta(IdMinuta)))
	{
		ShowSection('IdMinutaError');
		return;
	}

	if (!(oUnidad = GetUnidad(oMinuta.IdUnidad)))
		return;
		
	if (oUnidad.IdEstado != '<?= EstadoUnidad::Reservado ?>' && oUnidad.IdEstado != '<?= EstadoUnidad::Entregado ?>')
	{
		alert('El interno ya ha sido facturado.');
		return;
	}

	/*if (oUnidad.Certificado == 'false')
	{
		alert('La unidad no puede ser facturada, ya que no posee certificado.');
		return;
	}*/
		
	if (!(oModelo = GetModelo(oUnidad.IdModelo)))
		return;

	if (!(oTipoModelo = GetTipoModelo(oModelo.IdTipoModelo)))
	{
		alert('Debe ingresar el tipo de modelo a la unidad.');
		return;
	}

	if (!(oColor = GetColor(oUnidad.IdColor)))
		return;

	if (!(oMarca = GetMarca(oModelo.IdMarcaVehiculo)))
		return;

	if (!(oMarcaMotor = GetMarca(oModelo.IdMarcaMotor)))
		return;

	if (!(oMarcaChasis = GetMarca(oModelo.IdMarcaChasis)))
		return;

	if (!(oCliente = GetCliente(oMinuta.IdCliente)))
		return;
		
	oClienteCondominio = null;
	if (oMinuta.Condominio == 'true')
	{
		if (!(oClienteCondominio = GetCliente(oMinuta.IdClienteCondominio)))
			return;
			
		if (!(oLocalidadCondominio = GetLocalidad(oClienteCondominio.DomicilioIdLocalidad)))
		{
			alert('El cliente en condominio no posee cargada la localidad.');
			return;
		}

		if (!(oTipoIvaCondominio = GetTipoIva(oClienteCondominio.IdTipoIva)))
		{
			alert('El cliente en condominio no posee cargada la condicion de IVA.');
			return;
		}
	}

	if (!(oLocalidad = GetLocalidad(oCliente.DomicilioIdLocalidad)))
	{
		alert('El cliente no posee cargada la localidad.');
		return;
	}

	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
	{
		alert('El cliente no posee cargada la condicion de IVA.');
		return;
	}

	HideSection('IdMinutaError');
	HideSection('trCondominio');

	Get('ClienteRazonSocial').innerHTML 			= oCliente.RazonSocial;
	Get('ClienteDomicilio').innerHTML 				= oCliente.DomicilioCalle + ' ' + oCliente.DomicilioNumero;
	Get('ClienteLocalidad').innerHTML 				= oLocalidad.Nombre;
	Get('ClienteCodigoPostal').innerHTML 			= oLocalidad.CodigoPostal;
	Get('ClienteTelefono').innerHTML 				= oCliente.TelefonoCodigoArea + ' ' + oCliente.Telefono;
	Get('ClienteCondicionIva').innerHTML 			= oTipoIva.Nombre;
	Get('ClienteCuit').innerHTML 					= oCliente.ClaveFiscalNumero;
	if (oClienteCondominio != null)
	{
		ShowSection('trCondominio');
		Get('ClienteRazonSocialCondominio').innerHTML 			= oClienteCondominio.RazonSocial;
		Get('ClienteDomicilioCondominio').innerHTML 			= oClienteCondominio.DomicilioCalle + ' ' + oCliente.DomicilioNumero;
		Get('ClienteLocalidadCondominio').innerHTML 			= oLocalidadCondominio.Nombre;
		Get('ClienteCodigoPostalCondominio').innerHTML 			= oLocalidadCondominio.CodigoPostal;
		Get('ClienteTelefonoCondominio').innerHTML 				= oClienteCondominio.TelefonoCodigoArea + ' ' + oCliente.Telefono;
		Get('ClienteCondicionIvaCondominio').innerHTML 			= oTipoIvaCondominio.Nombre;
		Get('ClienteCuitCondominio').innerHTML 					= oClienteCondominio.ClaveFiscalNumero;
	}
	Get('VehiculoMarca').innerHTML 					= oMarca.Nombre;
	Get('VehiculoTipo').innerHTML 					= oTipoModelo.Nombre;
	Get('VehiculoAnio').innerHTML 					= oModelo.Anio;
	Get('VehiculoModelo').innerHTML 				= oModelo.DenominacionModelo;
	Get('VehiculoNumeroMotor').innerHTML 			= oUnidad.NumeroMotor + ' - ' + oMarcaMotor.Nombre;
	if (oUnidad.NumeroChasisPrefijo == undefined)
		Get('VehiculoNumeroChasis').innerHTML 			= oUnidad.NumeroChasis + ' - ' + oMarcaChasis.Nombre;
	else
		Get('VehiculoNumeroChasis').innerHTML 			= oUnidad.NumeroChasisPrefijo + oUnidad.NumeroChasis + ' - ' + oMarcaChasis.Nombre;
	Get('VehiculoColor').innerHTML					= oColor.Nombre;
	Get('VehiculoDenominacionComercial').innerHTML 	= oModelo.DenominacionComercial;
	Get('VehiculoIva').innerHTML 					= oModelo.Iva + ' %';

	if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaA?>')
	{		
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaA?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
	
		/* obtenemos la proxima factura */
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaA?>');
		
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/
	
		ShowSection('trSubtotal');
		ShowSection('trSubtotalError');
		ShowSection('trTotal');
		ShowSection('trTotalError');
		
		var ImpuestoInterno = 0;
		var Subtotal 		= 0;
		var Iva10 			= 0;
		var Iva21 			= 0;
		var Total 			= 0;

		Total = parseFloat(oMinuta.PrecioVenta);
		ImpuestoInterno = parseFloat(oUnidad.ImpuestoInterno);
		if (oUnidad.ImpuestoInternoD)
			ImpuestoInterno += parseFloat(oUnidad.ImpuestoInternoD);
		
		if (oModelo.Iva == '10.5')
		{			
			ShowSection('trIva10');
			ShowSection('trIva10Error');
			
			Subtotal 	= (Total - ImpuestoInterno) / 1.105;
			Iva10 		= (Subtotal) * 0.105;
		}
		else if (oModelo.Iva == '21')
		{
			ShowSection('trIva21');
			ShowSection('trIva21Error');

			Subtotal 	= (Total - ImpuestoInterno) / 1.21;
			Iva21 		= (Subtotal) * 0.21;
		}

		/* asignamos totales */
		Get('Subtotal').value 			= format_number(Subtotal, 2);
		Get('Iva10').value 				= format_number(Iva10, 2);
		Get('Iva21').value 				= format_number(Iva21, 2);
		Get('ImpuestoInterno').value 	= format_number(ImpuestoInterno, 2);
		Get('Total').value 				= format_number(Total, 2);
	}
	else if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

		ShowSection('trTotal');
		ShowSection('trTotalError');
		/* obtenemos la proxima factura */
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaB?>');
		
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/

		ShowSection('trTotal');
		ShowSection('trTotalError');

		/* asignamos total */
		Get('Total').value = format_number(oMinuta.PrecioVenta, 2);
	}

	/* guardamos el numero de venta */
	Get('IdMinuta').value = IdMinuta;
	
	ShowSection('trDatosFacturaUnidadTitulo');
	ShowSection('trDatosFacturaUnidad');
}

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	if (IdComprobante == '' && NumeroComprobante == '')
	{
		Get('IdComprobante').value 		= '';
		Get('NumeroComprobante').value 	= '';
	}
	
	var oComprobante = GetComprobante(IdComprobante);
	if (!(oComprobante))
		return;
	Get('IdComprobante').value 		= oComprobante.IdComprobante;
	Get('NumeroComprobante').value 	= oComprobante.Numero;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Minutas de Unidades - Agregar</span></td>
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
					<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?=$oComprobante->IdComprobante?>" />
                    
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
                                                            <input type="text" name="IdMinuta" id="IdMinuta" class="camporFormularioChico" maxlength="10" onblur="javascript: SetMinuta(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$IdMinuta?>" />
	                                                        &nbsp;<input type="button" id="btnSelectMinuta" class="botonBasico"  onClick="javascript:SelectMinuta();" value=" ? " />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25">
														<?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione o ingrese un nro. de carpeta v&aacute;lido</li><?php } else { ?><li style="color:#FF0000; display:none;" id="IdMinutaError">El nro. ingresado no existe</li><?php } ?>
														<?php if ($err & 512) { ?><li style="color:#FF0000;">La minuta ingresada tiene saldo a pagar</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Nro. Factura:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioMediano" maxlength="13" value="<?=$oComprobante->Numero?>" />
															<script language="javascript">
                                                            arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
                                                            SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
                                                            </script>
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25">
														<?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nro. de factura</li><?php } ?>
														<?php if ($err & 128) { ?><li style="color:#FF0000;">La factura seleccionada ya fue utilizada.</li><?php } ?>
														<?php if ($err & 256) { ?><li style="color:#FF0000;">Por favor, vuelva a seleccionar la factura.</li><?php } ?>
														<?php if ($err & 512) { ?><li style="color:#FF0000;">La factura seleccionada no existe.</li><?php } ?>
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
                                                	<td height="25"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese fecha</li><?php } ?></td>
                                                </tr>
                                                <tr id="trSubtotal" style="display:none;">
                                                    <td><div align="right">Subtotal:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="Subtotal" id="Subtotal" class="camporFormularioMediano" maxlength="8" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Subtotal?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trSubtotalError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el subtotal de la factura</li><?php } ?></td>
                                                </tr>
                                                <tr id="trIva10" style="display:none;">
                                                    <td><div align="right">IVA 10.5%:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="Iva10" id="Iva10" class="camporFormularioMediano" maxlength="8" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Iva10?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trIva10Error" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese el importe de 10.5% de iva</li><?php } ?></td>
                                                </tr>
                                                <tr id="trIva21" style="display:none;">
                                                    <td><div align="right">IVA 21%:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="Iva21" id="Iva21" class="camporFormularioMediano" maxlength="8" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Iva21?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trIva21Error" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el importe de 21% de iva</li><?php } ?></td>
                                                </tr>
                                                <tr id="trTotal" style="display:none;">
                                                    <td><div align="right">Total:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="Total" id="Total" class="camporFormularioMediano" maxlength="8" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Total?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr id="trTotalError" style="display:none;">
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese el total de la factura</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Otros Titulares:</div></td>
                                                    <td>
                                                        <div align="left">
															<textarea name="OtrosTitulares" id="OtrosTitulares" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$OtrosTitulares?></textarea>                                                
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Observaciones:</div></td>
                                                    <td>
                                                        <div align="left">
															<textarea name="Observaciones" id="Observaciones" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$Observaciones?></textarea>                                                
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosFacturaUnidadTitulo" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Minuta</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosFacturaUnidad" style="display:none;">
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
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr id="trCondominio" style="display:none">
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente Condominio:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocialCondominio"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Domicilio Condominio:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="36%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilioCondominio"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidadCondominio"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostalCondominio"></label>
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
                                                                        	<label id="ClienteTelefonoCondominio"></label>
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
                                                                                        <label id="ClienteCondicionIvaCondominio"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuitCondominio"></label>
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
                                                                    <td><div id="margen" align="left">Iva:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoIva"></label>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturaunidades.php<?=$strParams?>';" value="Cancelar" />
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
<?php if ($IdMinuta != '') { ?>
SetMinuta('<?=$IdMinuta?>');
<?php } ?>
</script>

</body>
</html>