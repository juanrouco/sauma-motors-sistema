<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura			= intval($_REQUEST['IdFactura']);
$NumeroOperacion	= strval($_REQUEST['NumeroOperacion']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oGestoria 	= new Gestoria();
$oGestorias	= new Gestorias();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdFactura == '')
		$err |= 1;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oGestoria->IdFactura = $IdFactura;

		$oGestoria = $oGestorias->Create($oGestoria);

		header("Location: gestorias.php" . $strParams);
		exit();
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function SelectFactura()
{
	var Url = 'facturaunidades.php?MainAction=Select';
	
	window.open(Url, this.target, 'width=1000,height=600'); 
}

function SetFactura(IdFactura)
{
	var oFacturaUnidad;
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

	if (!(oFacturaUnidad = GetFacturaUnidad(IdFactura)))
		return;

	if (!(oMinuta = GetMinuta(oFacturaUnidad.IdMinuta)))
		return;

	if (!(oUnidad = GetUnidad(oMinuta.IdUnidad)))
		return;

	if (!(oModelo = GetModelo(oUnidad.IdModelo)))
		return;

	if (!(oTipoModelo = GetTipoModelo(oModelo.IdTipoModelo)))
		return;

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

	if (!(oLocalidad = GetLocalidad(oCliente.DomicilioIdLocalidad)))
		return;

	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
		return;

	Get('FacturaNumero').innerHTML 					= oFacturaUnidad.NumeroComprobante;
	Get('FacturaFecha').innerHTML 					= oFacturaUnidad.Fecha;
	Get('ClienteRazonSocial').innerHTML 			= oCliente.RazonSocial;
	Get('ClienteDomicilio').innerHTML 				= oCliente.DomicilioCalle + ' ' + oCliente.DomicilioNumero;
	Get('ClienteLocalidad').innerHTML 				= oLocalidad.Nombre;
	Get('ClienteCodigoPostal').innerHTML 			= oLocalidad.CodigoPostal;
	Get('ClienteTelefono').innerHTML 				= oCliente.TelefonoCodigoArea + ' ' + oCliente.Telefono;
	Get('ClienteCondicionIva').innerHTML 			= oTipoIva.Nombre;
	Get('ClienteCuit').innerHTML 					= oCliente.CuitCuil;
	Get('VehiculoMarca').innerHTML 					= oMarca.Nombre;
	Get('VehiculoTipo').innerHTML 					= oTipoModelo.Nombre;
	Get('VehiculoAnio').innerHTML 					= oModelo.Anio;
	Get('VehiculoModelo').innerHTML 				= oModelo.DenominacionModelo;
	Get('VehiculoNumeroMotor').innerHTML 			= oUnidad.NumeroMotor + ' - ' + oMarcaMotor.Nombre;
	Get('VehiculoNumeroChasis').innerHTML 			= oUnidad.NumeroChasisPrefijo + oUnidad.NumeroChasis + ' - ' + oMarcaChasis.Nombre;
	Get('VehiculoColor').innerHTML					= oColor.Nombre;
	Get('VehiculoDenominacionComercial').innerHTML 	= oModelo.DenominacionComercial;
	Get('VehiculoCodigoLlaves').innerHTML 			= oUnidad.CodigoLlaves;
	
	/* guardamos el numero de operacion */
	Get('IdFactura').value = IdFactura;
	Get('NumeroOperacion').value = oMinuta.IdUnidad;
	
	ShowSection('trDatosGenerales');
	ShowSection('trDatosGestoriaTitulo');
	ShowSection('trDatosGestoria');
}

function FilterDomicilioFiscalLocalidad(IdLocalidad, Nombre)
{
	var oLocalidad = GetLocalidad(IdLocalidad);

	if (!(oLocalidad))
		return;

	Get('DomicilioFiscalIdPais').value 			= oLocalidad.IdPais;
	Get('DomicilioFiscalIdProvincia').value 	= oLocalidad.IdProvincia;
	Get('DomicilioFiscalIdPartido').value 		= oLocalidad.IdPartido;
	Get('DomicilioFiscalIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('DomicilioFiscalCodigoPostal').value 	= oLocalidad.CodigoPostal;
	Get('DomicilioFiscalLocalidad').value 		= oLocalidad.Nombre;
}

function FilterCliente(IdCliente, RazonSocial)
{
	var oCliente = GetCliente(IdCliente);

	if (!(oCliente))
		return;

	Get('IdClienteCondominio').value 	= oClienteCondominio.IdCliente;
	Get('ClienteCondominio').value 		= oClienteCondominio.RazonSocial;
}

function VerificarClienteCondominio()
{
	var IdClienteCondominio = Get('IdClienteCondominio').value;

	HideSection('trModificarClienteCondominio');
	
	if (IdClienteCondominio != '')
	{
		ShowSection('trModificarClienteCondominio');
	}
}

function ModCliente(Elemento)
{
	var IdCliente = Get(Element).value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Agregar</span></td>
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
					<input type="hidden" name="IdFactura" id="IdFactura" value="" />
					<input type="hidden" name="IdCliente" id="IdCliente" value="" />
                    <input type="hidden" name="DomicilioFiscalIdPais" id="DomicilioFiscalIdPais" value="<?=$DomicilioFiscalIdPais?>" />
                    <input type="hidden" name="DomicilioFiscalIdProvincia" id="DomicilioFiscalIdProvincia" value="<?=$DomicilioFiscalIdProvincia?>" />
                    <input type="hidden" name="DomicilioFiscalIdPartido" id="DomicilioFiscalIdPartido" value="<?=$DomicilioFiscalIdPartido?>" />
                    <input type="hidden" name="DomicilioFiscalIdLocalidad" id="DomicilioFiscalIdLocalidad" value="<?=$DomicilioFiscalIdLocalidad?>" />
                    
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
                                                    <td><div align="right">Operaci&oacute;n:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="NumeroOperacion" id="NumeroOperacion" class="camporFormularioSuggestDisabled" maxlength="10" readonly="readonly" value="<?=$NumeroOperacion?>" />
	                                                        &nbsp;<input type="button" id="btnSelectFactura" class="botonBasico"  onClick="javascript:SelectFactura();" value=" ? " />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione la operaci&oacute;n</li><?php } ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr id="trDatosGenerales">
                                        <td>
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td><div align="right">Porcentaje Titularidad:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="PorcentajeTitularidad" id="PorcentajeTitularidad" class="camporFormularioSimple" maxlength="10" value="<?=$PorcentajeTitularidad?>" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el porcentaje de titularidad</li><?php } ?></td>
                                                </tr>
                                                <tr id="trClienteCondominio">
                                                    <td><div align="right">Condominio:</div></td>
                                                    <td>
                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="ClienteCondominio" id="ClienteCondominio" class="camporFormularioSuggest" maxlength="128" value="<?=$ClienteCondominio?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarClienteCondominio();" />
                                                                        <script language="javascript">
                                                                        SUGGESTRequest('Clientes', 'GetAll', 'ClienteCondominio', 'FilterCliente', 'IdCliente', 'RazonSocial', 'RazonSocial', null);
                                                                        </script>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="IdClienteCondominio" id="IdClienteCondominio" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdClienteCondominio?>" readonly="readonly" />
                                                                        
                                                                    </div>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td><input type="button" id="btnAddClienteCondominio" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
                                                            </tr>
                                                            <tr id="trModificarClienteCondominio" style="display:none;">
                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModCliente('IdClienteCondominio');">Modificar datos del Condominio</a></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr id="trClienteCondominioError">
                                                	<td>&nbsp;</td>
                                                	<td><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el la informaci&oacute; de condominio</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right"><label id="lblCertificado">N&uacute;mero Certificado:</label></div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="NumeroCertificado" id="NumeroCertificado" class="camporFormularioSimple" maxlength="10" value="<?=$NumeroCertificado?>" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el nro. de certificado</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Uso</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <select name="IdTipoUso" id="IdTipoUso" class="camporFormularioSimple">
                                                            	<option value="">[SELECCIONE]</option>
                                                                <?php foreach (UsoTipos::GetAll() as $oUsoTipo) { ?>
                                                                <option value="<?=$oUsoTipo['IdTipo']?>" <?=($IdTipoUso == $oUsoTipo['IdTipo'])?> ><?=$oUsoTipo['Descripcion']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td><?php if ($err & 16) { ?><li style="color:#FF0000;">Seleccione el uso</li><?php } ?></td>
                                                </tr>
                                                
                                                
                                                
                                                
                                                
                                                <tr>
                                                    <td colspan="2">
                                                        <div align="center">
                                                            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">Domicilio Fiscal</span></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>                                    
                                                <tr>
                                                    <td colspan="2">
                                                        <div align="center">
                                                            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>                                          
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">Localidad:</div></td>
                                                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="DomicilioFiscalLocalidad" id="DomicilioFiscalLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=$DomicilioFiscalLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                                <script language="javascript">
                                                                                                                SUGGESTRequest('Localidades', 'GetAllSuggest', 'DomicilioFiscalLocalidad', 'FilterDomicilioFiscalLocalidad', 'IdLocalidad', 'Nombre', 'Nombre', null);
                                                                                                                </script>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="DomicilioFiscalCodigoPostal" id="DomicilioFiscalCodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=$DomicilioFiscalCodigoPostal?>" readonly="readonly" />
                                                                                                                
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('DomicilioFiscal');" value=" + " /></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Calle:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DomicilioFiscalCalle" id="DomicilioFiscalCalle" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioFiscalCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                            <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td height="20">&nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                                    <tr>
                                                                                                        <td><div id="margen" align="left">N&uacute;mero:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Piso:</div></td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td><div id="margen" align="left">Dpto.:</div></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="DomicilioFiscalNumero" id="DomicilioFiscalNumero" class="camporFormularioChico" maxlength="12" value="<?=$DomicilioFiscalNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="DomicilioFiscalPiso" id="DomicilioFiscalPiso" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioFiscalPiso?>" />
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>&nbsp;</td>
                                                                                                        <td>
                                                                                                            <div align="left">
                                                                                                                <input type="text" name="DomicilioFiscalDpto" id="DomicilioFiscalDpto" class="camporFormularioChico" maxlength="4" value="<?=$DomicilioFiscalDpto?>" />
                                                                                                            </div>
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
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                
                                                
                                                
                                                
                                                
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosGestoriaTitulo" style="display:none;">
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
                                    <tr id="trDatosGestoria" style="display:none;">
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
                                                                    <td width="20%"><div id="margen" align="left">Nro. Factura:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="FacturaNumero"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Fecha Factura:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="FacturaFecha"></label>
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
                                                        <td height="20"><div id="margen" align="left"><a href="#" class="linkMenu" onclick="javascript:ModCliente('IdCliente');">Modificar datos del Cliente</a></div></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'gestorias.php<?=$strParams?>';" value="Cancelar" />
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
SetFactura('<?=$IdMinuta?>');
</script>

</body>
</html>