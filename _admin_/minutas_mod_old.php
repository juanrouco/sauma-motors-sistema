<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta				= intval($_REQUEST['IdMinuta']);
$IdUnidad				= intval($_REQUEST['IdUnidad']);
$VehiculoModelo			= strval($_REQUEST['VehiculoModelo']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$IdClienteCondominio	= intval($_REQUEST['IdClienteCondominio']);
$IdClienteReventa		= intval($_REQUEST['IdClienteReventa']);
$Cliente				= strval($_REQUEST['Cliente']);
$ClienteCondominio		= strval($_REQUEST['ClienteCondominio']);
$Reventa				= strval($_REQUEST['Reventa']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);
$Usuario				= strval($_REQUEST['Usuario']);
$FechaMinuta			= strval($_REQUEST['FechaMinuta']);
$PrecioVenta			= floatval($_REQUEST['PrecioVenta']);
$GastosFlete			= floatval($_REQUEST['GastosFlete']);
$GastosPatentamiento	= floatval($_REQUEST['GastosPatentamiento']);
$GastosOtorgamiento		= floatval($_REQUEST['GastosOtorgamiento']);
$DepositoGarantia		= floatval($_REQUEST['DepositoGarantia']);
$GastosPrenda			= floatval($_REQUEST['GastosPrenda']);
$Circular				= floatval($_REQUEST['Circular']);
$Anticipo				= floatval($_REQUEST['Anticipo']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);
$Rentas					= floatval($_REQUEST['Rentas']);
$Financiacion			= intval($_REQUEST['Financiacion']);
$Condominio				= intval($_REQUEST['Condominio']);
$EntregaUsado			= intval($_REQUEST['EntregaUsado']);
$UsadoMarca				= strval($_REQUEST['UsadoMarca']);
$UsadoMarcaCodigo		= strval($_REQUEST['UsadoMarcaCodigo']);
$UsadoIdMarca			= intval($_REQUEST['UsadoIdMarca']);
$UsadoModelo			= strval($_REQUEST['UsadoModelo']);
$UsadoDominio			= strval($_REQUEST['UsadoDominio']);
$UsadoColor				= strval($_REQUEST['UsadoColor']);
$UsadoColorCodigo		= strval($_REQUEST['UsadoColorCodigo']);
$UsadoIdColor			= intval($_REQUEST['UsadoIdColor']);
$UsadoModeloAnio		= intval($_REQUEST['UsadoModeloAnio']);
$UsadoKilometraje		= floatval($_REQUEST['UsadoKilometraje']);
$UsadoValuacion			= floatval($_REQUEST['UsadoValuacion']);
$PlazoPrenda			= intval($_REQUEST['PlazoPrenda']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oMinutas	= new Minutas();
$oUnidades	= new Unidades();
$oModelos	= new Modelos();
$oClientes	= new Clientes();
$oUsuarios	= new Usuarios();
$oUsados	= new Usados();
$oMarcas	= new Marcas();
$oColores	= new Colores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: minutas.php" . $strParams);
	exit();
}

/* obtenemos los datos del usado entregado en caso de que exista */
if ($oMinuta->EntregaUsado) $oUsado = $oUsados->GetById($oMinuta->IdUsado);

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdUnidad == '')
		$err |= 1;
	if ($IdCliente == '')
		$err |= 2;
	if ($IdUsuario == '')
		$err |= 4;
	if ($FechaMinuta == '')
		$err |= 8;
	if ($PrecioVenta == '')
		$err |= 16;
	if (($Financiacion) && ($FinanciacionCapital == ''))
		$err |= 32;
	if ($EntregaUsado)
	{
		if ($UsadoIdMarca == '')
			$err |= 64;
		if ($UsadoModelo == '')
			$err |= 128;
		if ($UsadoModeloAnio == '')
			$err |= 256;
		if ($UsadoValuacion == '')
			$err |= 512;
	}
	if ($Condominio && $IdClienteCondominio == '')
		$err |= 1024;

	/* si no hay errores... */
	if ($err == 0)
	{
		$PrecioVenta			= str_replace(",", ".", $PrecioVenta);
		$GastosFlete			= str_replace(",", ".", $GastosFlete);
		$GastosPatentamiento	= str_replace(",", ".", $GastosPatentamiento);
		$GastosOtorgamiento		= str_replace(",", ".", $GastosOtorgamiento);
		$GastosPrenda			= str_replace(",", ".", $GastosPrenda);
		$Circular				= str_replace(",", ".", $Circular);
		$Anticipo				= str_replace(",", ".", $Anticipo);
		$FinanciacionCapital	= str_replace(",", ".", $FinanciacionCapital);
		$UsadoValuacion			= str_replace(",", ".", $UsadoValuacion);
		$DepositoGarantia		= str_replace(",", ".", $DepositoGarantia);
		$Rentas					= str_replace(",", ".", $Rentas);

		/* si no requiere financiacion */
		if (!($Financiacion)) $FinanciacionCapital = 0;

		/* si entrega un auto usado como parte de pago... */
		if ($EntregaUsado)
		{
			/* si existe lo modificamos, por el contrario, lo creamos */
			if ($oUsado)
			{
				$oUsado->IdMarca		= $UsadoIdMarca;
				$oUsado->IdColor		= $UsadoIdColor;
				$oUsado->Modelo			= $UsadoModelo;
				$oUsado->ModeloAnio		= $UsadoModeloAnio;
				$oUsado->Kilometraje	= $UsadoKilometraje;
				$oUsado->Valuacion		= $UsadoValuacion;
				$oUsado->Dominio		= $UsadoDominio;
		
				/* actualizamos el registro */
				$oUsado = $oUsados->Update($oUsado);
			}
			else
			{
				$oUsado = new Usado();
				$oUsado->IdMarca		= $UsadoIdMarca;
				$oUsado->IdColor		= $UsadoIdColor;
				$oUsado->Modelo			= $UsadoModelo;
				$oUsado->ModeloAnio		= $UsadoModeloAnio;
				$oUsado->Kilometraje	= $UsadoKilometraje;
				$oUsado->Valuacion		= $UsadoValuacion;
				$oUsado->Dominio		= $UsadoDominio;

				/* creamos el registro */		
				$oUsado = $oUsados->Create($oUsado);
			}
		}
		else
		{
			/* si no entrega usado, verificamos que no queda basura en la base de datos */
			if ($oUsado) $oUsados->Delete($oUsado->IdUsado);
		}

		$oMinuta->IdUnidad				= $IdUnidad;
		$oMinuta->IdUsuario				= $IdUsuario;
		$oMinuta->IdCliente				= $IdCliente;
		if ($Condominio)
			$oMinuta->IdClienteCondominio	= $IdClienteCondominio;
		$oMinuta->FechaMinuta			= $FechaMinuta;
		$oMinuta->PrecioVenta			= $PrecioVenta;
		$oMinuta->GastosFlete			= $GastosFlete;
		$oMinuta->GastosPatentamiento	= $GastosPatentamiento;
		$oMinuta->GastosOtorgamiento	= $GastosOtorgamiento;
		$oMinuta->DepositoGarantia		= $DepositoGarantia;
		$oMinuta->GastosPrenda			= $GastosPrenda;
		$oMinuta->Circular				= $Circular;
		$oMinuta->Anticipo				= $Anticipo;
		$oMinuta->FinanciacionCapital	= $FinanciacionCapital;
		$oMinuta->Condominio			= $Condominio;
		$oMinuta->EntregaUsado			= $EntregaUsado;
		$oMinuta->IdUsado				= ($oUsado) ? $oUsado->IdUsado : '';
		$oMinuta->PlazoPrenda			= $PlazoPrenda;
		$oMinuta->Rentas				= $Rentas;
		$oMinuta->IdClienteReventa		= $IdClienteReventa;

		$oMinuta = $oMinutas->Update($oMinuta);

		header("Location: minutas_detail.php?IdMinuta=" . $oMinuta->IdMinuta);
		exit();
	}
}
else
{
	$oUnidad 		= $oUnidades->GetById($oMinuta->IdUnidad);
	$oModelo 		= $oModelos->GetById($oUnidad->IdModelo);
	$oCliente 		= $oClientes->GetById($oMinuta->IdCliente);
	$oUsuario 		= $oUsuarios->GetById($oMinuta->IdUsuario);
	$oUsadoMarca 	= $oMarcas->GetById($oUsado->IdMarca);
	$oUsadoColor 	= $oColores->GetById($oUsado->IdColor);
	
	if ($oMinuta->Condominio)
	{
		$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
	}
	
	if ($oMinuta->IdClienteReventa)
	{
		$oClienteReventa = $oClientes->GetById($oMinuta->IdClienteReventa);
	}

	$VehiculoModelo = $oModelo->DenominacionModelo;

	$IdUnidad				= $oMinuta->IdUnidad;
	$IdUsuario				= $oUsuario->IdUsuario;
	$Usuario				= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);
	$IdCliente				= $oCliente->IdCliente;
	$IdClienteCondominio	= $oMinuta->IdClienteCondominio;
	$IdClienteReventa		= $oMinuta->IdClienteReventa;
	if ($oClienteReventa)
		$Reventa				= $oClienteReventa->RazonSocial;
		
	$Cliente				= $oCliente->RazonSocial;
	$ClienteCondominio		= '';
	if ($oMinuta->Condominio)
	{
		$ClienteCondominio = $oClienteCondominio->RazonSocial;
	}
	$FechaMinuta			= CambiarFecha($oMinuta->FechaMinuta);
	$PrecioVenta			= $oMinuta->PrecioVenta;
	$GastosFlete			= $oMinuta->GastosFlete;
	$GastosPatentamiento	= $oMinuta->GastosPatentamiento;
	$GastosOtorgamiento		= $oMinuta->GastosOtorgamiento;
	$DepositoGarantia		= $oMinuta->DepositoGarantia;
	$GastosPrenda			= $oMinuta->GastosPrenda;
	$Circular				= $oMinuta->Circular;
	$Anticipo				= $oMinuta->Anticipo;
	$FinanciacionCapital	= $oMinuta->FinanciacionCapital;
	$Rentas					= $oMinuta->Rentas;
	$Financiacion			= (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) ? '1' : '0';
	$Condominio				= $oMinuta->Condominio;
	$EntregaUsado			= $oMinuta->EntregaUsado;
	$PlazoPrenda			= $oMinuta->PlazoPrenda;
	
	/* datos del usado */
	$UsadoIdMarca		= $oUsadoMarca->IdMarca;
	$UsadoMarca			= $oUsadoMarca->Nombre;
	$UsadoMarcaCodigo	= $oUsadoMarca->Codigo;
	$UsadoIdColor		= $oUsadoColor->IdColor;
	$UsadoColor			= $oUsadoColor->Nombre;
	$UsadoColorCodigo	= $oUsadoColor->Codigo;
	$UsadoModelo		= $oUsado->Modelo;
	$UsadoModeloAnio	= $oUsado->ModeloAnio;
	$UsadoKilometraje	= $oUsado->Kilometraje;
	$UsadoValuacion		= $oUsado->Valuacion;
	$UsadoDominio		= $oUsado->Dominio;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}
}
function FilterClienteReventa(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdClienteReventa').value 	= '';
		Get('Reventa').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdClienteReventa').value 	= oCliente.IdCliente;
	Get('Reventa').value 	= oCliente.RazonSocial;
}

function FilterClienteCondominio(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdClienteCondominio').value 	= '';
		Get('ClienteCondominio').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdClienteCondominio').value 	= oCliente.IdCliente;
	Get('ClienteCondominio').value 	= oCliente.RazonSocial;	
}

function FilterUsadoMarca(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('UsadoMarcaCodigo').value 	= '';
		Get('UsadoMarca').value 		= '';
		Get('UsadoIdMarca').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('UsadoMarcaCodigo').value 	= oMarca.Codigo;
	Get('UsadoMarca').value 		= oMarca.Nombre;
	Get('UsadoIdMarca').value 		= oMarca.IdMarca;
}

function FilterUsadoColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('UsadoColorCodigo').value 	= '';
		Get('UsadoColor').value 		= '';
		Get('UsadoIdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('UsadoColorCodigo').value 	= oColor.Codigo;
	Get('UsadoColor').value 		= oColor.Nombre;
	Get('UsadoIdColor').value 		= oColor.IdColor;
}

function VerificarEntregaUsado(value)
{
	HideSection('trDatosUsadoTitulo');
	HideSection('trDatosUsado');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trDatosUsadoTitulo');
		ShowSection('trDatosUsado');
	}
}

function VerificarFinanciacion(value)
{
	HideSection('trFinanciacionCapital');
	HideSection('trFinanciacionCapitalError');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trFinanciacionCapital');
		ShowSection('trFinanciacionCapitalError');
	}
}

function VerificarClienteReventa()
{
	var IdCliente = Get('IdClienteReventa').value;

	HideSection('trModificarClienteReventa');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarClienteReventa');
	}
}

function VerificarClienteCondominio()
{
	var IdCliente = Get('IdClienteCondominio').value;

	HideSection('trModificarClienteCondominio');
	
	if (IdClienteCondominio != '')
	{
		ShowSection('trModificarClienteCondominio');
	}
}

function VerificarCondominio(value)
{
	HideSection('trClienteCondominio');
	HideSection('trClienteCondominio_white');
		
	if ((value == '1') || (value == true))
	{
		ShowSection('trClienteCondominio');
		ShowSection('trClienteCondominio_white');
		VerificarClienteCondominio();
	}
}

function ModCliente()
{
	var IdCliente = Get('IdCliente').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function ModClienteReventa()
{
	var IdCliente = Get('IdClienteReventa').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function ModClienteCondominio()
{
	var IdCliente = Get('IdClienteCondominio').value;

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas - Modificar</span></td>
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
					<input type="hidden" name="UsadoIdMarca" id="UsadoIdMarca" value="<?=$UsadoIdMarca?>" />
					<input type="hidden" name="UsadoIdColor" id="UsadoIdColor" value="<?=$UsadoIdColor?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Venta</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Nro. Carpeta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" class="camporFormularioMedianoDisabled" maxlength="10" readonly="readonly" value="<?=$IdMinuta?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Unidad:</div></td>
                                                                                <td><div id="margen" align="left">Interno:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSuggestDisabled" maxlength="128" value="<?=$VehiculoModelo?>" readonly="readonly" />
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdUnidad" id="NumeroVin" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUnidad?>" onkeyup="javascript: StrToUpper(this.id);" readonly="readonly" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
																			<tr id="trModificarUnidad">
                                                                                <td height="20"><a href="unidades_mod.php?IdUnidad=<?= $oUnidad->IdUnidad ?>" class="linkMenu">Modificar datos de la Unidad</a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la unidad</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Cliente:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                        <script language="javascript">
                                                                                        SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
                                                                            </tr>
																			<tr id="trModificarCliente">
                                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModCliente();">Modificar datos del Cliente</a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
                                                                </tr>
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Reventa:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Reventa" id="Reventa" class="camporFormularioSuggest" maxlength="128" value="<?=$Reventa?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarClienteReventa();" autocomplete="Off" />
                                                                                        <script language="javascript">
                                                                                        SUGGESTRequest('Clientes', 'GetAll', 'Reventa', 'FilterClienteReventa', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdClienteReventa" id="IdClienteReventa" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdClienteReventa?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddClienteReventa" class="botonBasico"  onClick="javascript:AddClienteReventa();" value=" + " /></td>
                                                                            </tr>
                                                                            <tr id="trModificarClienteReventa" style="display:none;">
                                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModClienteReventa();">Modificar datos del Reventa</a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Vendedor:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                        <script language="javascript">
                                                                                        var arrParams = new Array();
                                                                                        arrParams['FilterIdPerfil'] = '<?=Usuario::Vendedor?>';
                                                                                        SUGGESTRequest('Usuarios', 'GetAllSuggest', 'Usuario', 'FilterUsuario', 'IdUsuario', 'Nombre', 'FilterUsuario', arrParams);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdUsuario" id="IdUsuario" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUsuario?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddUsuario" class="botonBasico"  onClick="javascript:AddUsuario();" value=" + " /></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el vendedor</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de Minuta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input name="FechaMinuta" type="text" class="camporFormularioMediano" id="FechaMinuta" value="<?=$FechaMinuta?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'FechaMinuta'});
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese la fecha de venta</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input type="checkbox" name="Condominio" id="Condominio" value="1" <?=($Condominio) ? 'checked="checked"' : ''?> onclick="javascript: VerificarCondominio(this.checked);" />&nbsp;En Condominio
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<tr id="trClienteCondominio" style="display:none;">
																	<td>
																		<table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Cliente Condominio:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="ClienteCondominio" id="ClienteCondominio" class="camporFormularioSuggest" maxlength="128" value="<?=$ClienteCondominio?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarClienteCondominio();" autocomplete="Off" />
                                                                                        <script language="javascript">
                                                                                        SUGGESTRequest('Clientes', 'GetAll', 'ClienteCondominio', 'FilterClienteCondominio', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdClienteCondominio" id="IdClienteCondominio" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdClienteCondominio?>" readonly="readonly" />                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddClienteCondominio" class="botonBasico"  onClick="javascript:AddClienteCondominio();" value=" + " /></td>
                                                                            </tr>
                                                                            <tr id="trModificarClienteCondominio" style="display:none;">
                                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModClienteCondominio();">Modificar datos del Cliente Condominio</a></td>
                                                                            </tr>
                                                                        </table>
																	</td>
																</tr>
																 <tr id="trClienteCondominio_white" style="display:none;">
                                                                    <td height="20"><?php if ($err & 1024) { ?><li style="color:#FF0000;">Ingrese el cliente de condominio</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>																						
                                                                                            <input type="checkbox" name="EntregaUsado" id="EntregaUsado" value="1" onchange="javascript: VerificarEntregaUsado(this.checked);" <?=($EntregaUsado) ? 'checked="checked"' : ''?> />&nbsp;Entrega Usado
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input type="checkbox" name="Financiacion" id="Financiacion" value="1" onchange="javascript: VerificarFinanciacion(this.checked);" <?=($Financiacion) ? 'checked="checked"' : ''?> />&nbsp;Requiere Financiaci&oacute;n
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Precio de Venta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="PrecioVenta" id="PrecioVenta" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$PrecioVenta?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese precio de venta</li><?php } ?></td>
                                                                </tr>
    
    
    
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Flete:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosFlete" id="GastosFlete" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosFlete?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Patentamiento:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosPatentamiento" id="GastosPatentamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPatentamiento?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Gastos Prendarios:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosOtorgamiento" id="GastosOtorgamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosOtorgamiento?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																 <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Deposito En Garant&iacute;a:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="DepositoGarantia" id="DepositoGarantia" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$DepositoGarantia?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Rentas:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Rentas" id="Rentas" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Rentas?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Gastos Prenda:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosPrenda" id="GastosPrenda" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPrenda?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>     
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Plazo Prenda:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="PlazoPrenda" id="PlazoPrenda" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$PlazoPrenda?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>    																																
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Circular:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Circular" id="Circular" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Circular?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Anticipo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Anticipo" id="Anticipo" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Anticipo?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr id="trFinanciacionCapital" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Capital a Financiar:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCapital?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionCapitalError">
                                                                    <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el capital a financiar</li><?php } ?></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                            	</table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosUsadoTitulo">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos del Usado</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosUsado">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Marca:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarca" id="UsadoMarca" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'UsadoMarca', 'FilterUsadoMarca', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadocolorCodigo" id="UsadoMarcaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoMarcaCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddColor" class="botonBasico" onClick="javascript:AddMarca('Usado');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese la marca</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoModelo" id="UsadoModelo" class="camporFormularioSimple" maxlength="255" value="<?=$UsadoModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Color:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoColor" id="UsadoColor" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoColor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Colores', 'GetAll', 'UsadoColor', 'FilterUsadoColor', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadocolorCodigo" id="UsadoColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoColorCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddColor" class="botonBasico" onClick="javascript:AddColor('Usado');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
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
                                                                                            <td><div id="margen" align="left">Dominio:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoDominio" id="UsadoDominio" class="camporFormularioSimple" maxlength="128" value="<?=$UsadoDominio?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="UsadoModeloAnio" id="UsadoModeloAnio" class="camporFormularioSimple">
                                                                                            <option value="">[SELECCIONE]</option>
                                                                                            <?php $year = date('Y'); ?>
                                                                                            <?php for ($i=$year-15; $i<=$year; $i++) { ?>
                                                                                            <option value="<?=$i?>" <?=($UsadoModeloAnio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                 	</div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoKilometraje" id="UsadoKilometraje" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoKilometraje?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Importe:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoValuacion" id="UsadoValuacion" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoValuacion?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el importe del usado</li><?php } ?></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'minutas.php<?=$strParams?>';" value="Cancelar" />
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
VerificarEntregaUsado('<?=$EntregaUsado?>');
VerificarFinanciacion('<?=$Financiacion?>');
VerificarCondominio('<?= $Condominio ?>');
</script>

</body>
</html>