<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUnidad				= intval($_REQUEST['IdUnidad']);
$IdModelo				= intval($_REQUEST['IdModelo']);
$VehiculoModelo			= strval($_REQUEST['VehiculoModelo']);
$IdUbicacion			= intval($_REQUEST['IdUbicacion']);
$Ubicacion				= strval($_REQUEST['Ubicacion']);
$UbicacionCodigo		= strval($_REQUEST['UbicacionCodigo']);
$IdColor				= intval($_REQUEST['IdColor']);
$Color					= strval($_REQUEST['Color']);
$ColorCodigo			= strval($_REQUEST['ColorCodigo']);
$NumeroVinPrefijo		= strval($_REQUEST['NumeroVinPrefijo']);
$NumeroVin				= strval($_REQUEST['NumeroVin']);
$NumeroMotor			= strval($_REQUEST['NumeroMotor']);
$NumeroChasisPrefijo	= strval($_REQUEST['NumeroChasisPrefijo']);
$NumeroChasis			= strval($_REQUEST['NumeroChasis']);
$CodigoComercial		= strval($_REQUEST['CodigoComercial']);
$Anio					= intval($_REQUEST['Anio']);
$Patente				= strval($_REQUEST['Patente']);
$FechaFacturaCompra		= strval($_REQUEST['FechaFacturaCompra']);
$NumeroFacturaCompra	= strval($_REQUEST['NumeroFacturaCompra']);
$ImporteCompraNeto		= floatval($_REQUEST['ImporteCompraNeto']);
$ImporteCompraBruto		= floatval($_REQUEST['ImporteCompraBruto']);
$ImporteNotaCredito		= floatval($_REQUEST['ImporteNotaCredito']);
$CodigoRadio			= strval($_REQUEST['CodigoRadio']);
$Cancelada				= intval($_REQUEST['Cancelada']);
$Verificado				= intval($_REQUEST['Verificado']);
$Certificado			= intval($_REQUEST['Certificado']);
$IdEstado				= intval($_REQUEST['IdEstado']);
$Estado					= strval($_REQUEST['Estado']);
$EstadoCodigo			= strval($_REQUEST['EstadoCodigo']);
$NumeroPedido			= strval($_REQUEST['NumeroPedido']);
$FechaArriboEstimada	= strval($_REQUEST['FechaArriboEstimada']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oUnidades		= new Unidades();
$oModelos		= new Modelos();
$oUbicaciones	= new Ubicaciones();
$oColores		= new Colores();
$oModelos		= new Modelos();
$oEstadosUnidad	= new EstadosUnidad();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oUnidad = $oUnidades->GetById($IdUnidad))
{	
	header("Location: unidades.php" . $strParams);
	exit();
}

if ($Submit)
{
	/* validaciones... */
	if ($NumeroVinPrefijo == '')
		$err |= 1;
	if ($NumeroVin == '')
		$err |= 4;
	elseif ($oUnidades->GetByNumeroVin($NumeroVin) && $oUnidad->NumeroVin != $NumeroVin)
		$err |= 8;
	if (($NumeroVinPrefijo != '') && ($NumeroVin != '') && ($oUnidades->GetByNumeroChasis($NumeroVinPrefijo . $NumeroVin) && $oUnidad->NumeroChasis != $NumeroVinPrefijo . $NumeroVin))
		$err |= 16;
	if ($NumeroMotor == '')
		$err |= 32;
	elseif ($oUnidades->GetByNumeroMotor($NumeroMotor) && $oUnidad->NumeroMotor != $NumeroMotor)
		$err |= 64;
	if ($IdUbicacion == '')
		$err |= 128;
	if (!$oModelo = $oModelos->GetByPrefijoVinAndCodigoComercial($NumeroVinPrefijo, $CodigoComercial))
		$err |= 1024;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$ImporteCompraNeto	= str_replace(",", ".", $ImporteCompraNeto);
		$ImporteCompraBruto	= str_replace(",", ".", $ImporteCompraBruto);
		$ImporteNotaCredito	= str_replace(",", ".", $ImporteNotaCredito);

		$oUnidad->IdModelo				= $oModelo->IdModelo;
		$oUnidad->IdUbicacion			= $IdUbicacion;
		$oUnidad->NumeroVinPrefijo		= $NumeroVinPrefijo;
		$oUnidad->NumeroVin				= $NumeroVin;
		$oUnidad->NumeroMotor			= $NumeroMotor;
		$oUnidad->NumeroChasis			= $NumeroVinPrefijo . $NumeroVin;
		$oUnidad->Patente				= $Patente;
		$oUnidad->FechaFacturaCompra	= $FechaFacturaCompra;
		$oUnidad->NumeroFacturaCompra	= $NumeroFacturaCompra;
		$oUnidad->ImporteCompraNeto		= $ImporteCompraNeto;
		$oUnidad->ImporteCompraBruto	= $ImporteCompraBruto;
		$oUnidad->ImporteNotaCredito	= $ImporteNotaCredito;
		$oUnidad->CodigoRadio			= $CodigoRadio;
		$oUnidad->Cancelada				= $Cancelada;
		$oUnidad->Verificado			= $Verificado;
		$oUnidad->Certificado			= $Certificado;
		
		if ($oUnidad->IdEstado == EstadoUnidad::PreVenta)
			$oUnidad->IdEstado = EstadoUnidad::Stock;
		elseif ($oUnidad->IdEstado == EstadoUnidad::PreVentaReservado)
			$oUnidad->IdEstado = EstadoUnidad::Reservado;
		
		$oUnidad = $oUnidades->Update($oUnidad);

		header("Location: unidades.php" . $strParams);
		exit();
	}
}
else
{
	$oModelo 		= $oModelos->GetById($oUnidad->IdModelo);
	$oUbicacion 	= $oUbicaciones->GetById($oUnidad->IdUbicacion);
	$oColor 		= $oColores->GetById($oUnidad->IdColor);
	$oEstadoUnidad 	= $oEstadosUnidad->GetById($oUnidad->IdEstado);
	
	$IdModelo				= $oModelo->IdModelo;
	$VehiculoModelo			= $oModelo->DenominacionModelo;
	$IdUbicacion			= $oUbicacion->IdUbicacion;
	$Ubicacion				= $oUbicacion->Nombre;
	$UbicacionCodigo		= $oUbicacion->Codigo;
	$IdColor				= $oColor->IdColor;
	$Color					= $oColor->Nombre;
	$ColorCodigo			= $oColor->Codigo;
	$CodigoComercial		= $oUnidad->CodigoComercial;
	$NumeroVinPrefijo		= $oUnidad->NumeroVinPrefijo;
	$NumeroVin				= $oUnidad->NumeroVin;
	$NumeroMotor			= $oUnidad->NumeroMotor;
	$NumeroChasisPrefijo	= $oModelo->NumeroVinPrefijo;
	$NumeroChasis			= $oUnidad->NumeroVin;
	$Anio					= $oUnidad->Anio;
	$Patente				= $oUnidad->Patente;
	$FechaFacturaCompra		= CambiarFecha($oUnidad->FechaFacturaCompra);
	$NumeroFacturaCompra	= $oUnidad->NumeroFacturaCompra;
	$ImporteCompraNeto		= $oUnidad->ImporteCompraNeto;
	$ImporteCompraBruto		= $oUnidad->ImporteCompraBruto;
	$ImporteNotaCredito		= $oUnidad->ImporteNotaCredito;
	$CodigoRadio			= $oUnidad->CodigoRadio;
	$Cancelada				= $oUnidad->Cancelada;
	$Verificado				= $oUnidad->Verificado;
	$Certificado			= $oUnidad->Certificado;
	$IdEstado				= $oEstadoUnidad->IdEstado;
	$Estado					= $oEstadoUnidad->Nombre;
	$EstadoCodigo			= $oEstadoUnidad->Codigo;
	$NumeroPedido			= $oUnidad->NumeroPedido;
	$FechaArriboEstimada	= CambiarFecha($oUnidad->FechaArriboEstimada);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

var filterCodigo = new Array();

function FilterCodigoComercial(IdModelo, NumeroVinPrefijo)
{
	if (IdModelo == '')
	{
		Get('VehiculoModelo').value 		= '';
		
		Get('Anio').value 					= '';
		Get('CodigoComercial').value 		= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;
	Get('NumeroVinPrefijo').value 		= '';
	Get('IdModelo').value 				= oModelo.IdModelo;
	Get('NumeroChasisPrefijo').value 	= '';
	Get('VehiculoModelo').value 		= oModelo.DenominacionModelo;
	Get('CodigoComercial').value 		= oModelo.CodigoComercial;
	Get('Anio').value 					= oModelo.Anio;
	filterCodigo['FilterCodigoComercial'] = oModelo.CodigoComercial;
}

function FilterNumeroVinPrefijo(IdModelo, NumeroVinPrefijo)
{
	if (IdModelo == '')
	{
		Get('NumeroVinPrefijo').value 		= '';
		Get('IdModelo').value 				= '';
		Get('NumeroChasisPrefijo').value 	= '';		
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;
	Get('NumeroVinPrefijo').value 		= oModelo.NumeroVinPrefijo;
	Get('IdModelo').value 				= oModelo.IdModelo;
	Get('NumeroChasisPrefijo').value 	= oModelo.NumeroVinPrefijo;
}

function FilterUbicacion(IdUbicacion, Nombre)
{
	if ((IdUbicacion == '') && (Nombre == ''))
	{
		Get('UbicacionCodigo').value 	= '';
		Get('Ubicacion').value 			= '';
		Get('IdUbicacion').value 		= '';
	}

	var oUbicacion = GetUbicacion(IdUbicacion);
	if (!(oUbicacion))
		return;

	Get('UbicacionCodigo').value 	= oUbicacion.Codigo;
	Get('Ubicacion').value 			= oUbicacion.Nombre;
	Get('IdUbicacion').value 		= oUbicacion.IdUbicacion;
}

function FilterColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo').value 	= '';
		Get('Color').value 			= '';
		Get('IdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo').value 	= oColor.Codigo;
	Get('Color').value 			= oColor.Nombre;
	Get('IdColor').value 		= oColor.IdColor;
}

function FilterEstado(IdEstado, Nombre)
{
	if ((IdEstado == '') && (Nombre == ''))
	{
		Get('EstadoCodigo').value 	= '';
		Get('Estado').value 		= '';
		Get('IdEstado').value 		= '';
	}

	var oEstadoUnidad = GetEstadoUnidad(IdEstado);
	if (!(oEstadoUnidad))
		return;

	Get('EstadoCodigo').value 	= oEstadoUnidad.Codigo;
	Get('Estado').value 		= oEstadoUnidad.Nombre;
	Get('IdEstado').value 		= oEstadoUnidad.IdEstado;
}

function SetNumeroChasis(value)
{
	Get('NumeroChasis').value = value;
}

$j(document).ready(function() {
	<?php
	if ($IdModelo)
	{
	?>
	FilterCodigoComercial(<?= $IdModelo ?>, '');
	FilterNumeroVinPrefijo(<?= $IdModelo ?>, '');
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Unidades - Asignar</span></td>
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
                <input type="hidden" name="IdUnidad" id="IdUnidad" value="<?=$IdUnidad?>" />
                <input type="hidden" name="IdModelo" id="IdModelo" value="<?=$IdModelo?>" />
                <input type="hidden" name="IdColor" id="IdColor" value="<?=$IdColor?>" />
                <input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?=$IdUbicacion?>" />
                <input type="hidden" name="IdEstado" id="IdEstado" value="<?=$IdEstado?>" />

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
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">C&oacute;digo Lista:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="CodigoComercial" id="CodigoComercial" class="camporFormularioSimpleDisabled" value="<?= $CodigoComercial ?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" readonly="true" />
																				<script language="">
																					SUGGESTRequest('Modelos', 'GetAllNumeroLista', 'CodigoComercial', 'FilterCodigoComercial', 'IdModelo', 'CodigoComercial', 'FilterCodigoComercial', null);
																				</script>
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione el c&oacute;digo de lista</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Prefijo Vin:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroVinPrefijo" id="NumeroVinPrefijo" class="camporFormularioSimple" maxlength="10" value="<?=$NumeroVinPrefijo?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                <script language="">
                                                                                SUGGESTRequest('Modelos', 'GetAll', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', 'IdModelo', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', filterCodigo);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el prefijo vin</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSimpleDisabled" maxlength="128" value="<?=$VehiculoModelo?>" readonly="readonly" />
                                                                            </div>
                                                                        </td>
                                                                    </tr>
																	<?php
																	if (Session::CheckPerm(PERM_MODE_UPDATE))
																	{
																	?>
																	<tr>
																		<td>
																			<div align="left">
																				<a href="modelos_mod.php?IdModelo=<?= $IdModelo ?>" class="linkMenu">Modificar Modelo</a>
																			</div>
																		</td>
																	</tr>
																	<?php
																	}
																	?>
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
                                                                        <td><div id="margen" align="left">N&uacute;mero Vin:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" maxlength="7" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id); SetNumeroChasis(this.value);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el n&uacute;mero de vin</li><?php } ?><?php if ($err & 8) { ?><li style="color:#FF0000;">Ya existe registrado el n&uacute;mero de vin</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">N&uacute;mero de Chasis:</div></td>
                                                                        <td><div id="margen" align="left"></div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroChasisPrefijo" id="NumeroChasisPrefijo" class="camporFormularioMedianoSuggestDisabled" maxlength="10" readonly="readonly" value="<?=$NumeroVinPrefijo?>" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroChasis" id="NumeroChasis" class="camporFormularioMedianoSuggestDisabled" maxlength="7" value="<?=$NumeroChasis?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ya existe registrado el n&uacute;mero de chasis</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">N&uacute;mero de Motor:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroMotor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>                                                            
                                                        <tr>
                                                            <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el n&uacute;mero de motor</li><?php } ?><?php if ($err & 64) { ?><li style="color:#FF0000;">ya existe registrado el n&uacute;mero de motor</li><?php } ?></td>
                                                        </tr>
														<tr>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">N&uacute;mero de Pedido:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="NumeroPedido" id="NumeroPedido" class="camporFormularioSimpleDisabled" maxlength="128" value="<?=$NumeroPedido?>" onkeyup="javascript: StrToUpper(this.id);" readonly="true" />
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
																</td>
															</tr>
															<tr>
                                                                <td height="20"><?php if ($err & 2048) { ?><li style="color:#FF0000;">Ingrese el n&uacute;mero de pedido</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td>
																	<table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">Fecha Estimada de Arribo:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input name="FechaArriboEstimada" type="text" class="camporFormularioMediano" id="FechaArriboEstimada" value="<?=$FechaArriboEstimada?>" size="12" maxlength="12" />
																					<script language="javascript">
																					new tcal({'formname': 'frmData', 'controlname': 'FechaArriboEstimada'});
																					</script>
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
                                                                        <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Anio" id="Anio" class="camporFormularioSimpleDisabled" readonly="disabled" value="<?= $Anio ?>"  />
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
                                                                        <td><div id="margen" align="left">Patente:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Patente" id="Patente" class="camporFormularioSimple" maxlength="10" value="<?=$Patente?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                        <td><div id="margen" align="left">Color:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" readonly="true" style="background: #DFDFDF" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('Colores', 'GetAll', 'Color', 'FilterColor', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td><input type="button" id="btnAddColor" class="botonBasico"  onClick="javascript:AddColor('Unidad');" value=" + " /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el color de la unidad</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Ubicaci&oacute;n:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" maxlength="128" value="<?=$Ubicacion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="UbicacionCodigo" id="UbicacionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UbicacionCodigo?>" readonly="readonly"  />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td><input type="button" id="btnAddUbicacion" class="botonBasico"  onClick="javascript:AddUbicacion();" value=" + " /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese la ubicaci&oacute;n</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">Estado:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Estado" id="Estado" class="camporFormularioSuggest" maxlength="128" value="<?=$Estado?>" onkeyup="javascript: StrToUpper(this.id);" readonly="true" style="background: #DFDFDF" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('EstadosUnidad', 'GetAll', 'Estado', 'FilterEstado', 'IdEstado', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="EstadoCodigo" id="EstadoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$EstadoCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td><input type="button" id="btnAddEstadoUnidad" class="botonBasico"  onClick="javascript:AddEstadoUnidad();" value=" + " /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Ingrese el estado de la unidad</li><?php } ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td><div id="margen" align="left">C&oacute;digo Radio:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="CodigoRadio" id="CodigoRadio" class="camporFormularioSimple" maxlength="10" value="<?=$CodigoRadio?>" />
                                                                                <span style="color:#FF0000;">&nbsp;</span>
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
																		<td colspan="6"><div id="margen" align="left">&nbsp;</div></td>
																	</tr>
                                                                    <tr>
                                                                        <td>Cancelado:</td>
                                                                        <td><input type="checkbox" name="Cancelada" id="Cancelada" value="1" <?=($Cancelada) ? 'checked="checked"' : '';?> /></td>
                                                                        <td>Verificado:</td>
                                                                        <td><input type="checkbox" name="Verificado" id="Verificado" value="1" <?=($Verificado) ? 'checked="checked"' : '';?> /></td>
                                                                        <td>Certificado:</td>
                                                                        <td><input type="checkbox" name="Certificado" id="Certificado" value="1" <?=($Certificado) ? 'checked="checked"' : '';?> /></td>
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
                                <tr>
                                    <td>
                                        <div align="center">
                                            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                <tr>
                                                    <td height="40" align="center"><span class="tituloPagina">Datos de Compra</span></td>
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
                                                    <td>
                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td><div align="right">Nro. Factura:</div></td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="NumeroFacturaCompra" id="NumeroFacturaCompra" class="camporFormularioMediano" maxlength="10" value="<?=$NumeroFacturaCompra?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td><div align="right">Fecha Factura:</div></td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input name="FechaFacturaCompra" type="text" class="camporFormularioMediano" id="FechaFacturaCompra" value="<?=$FechaFacturaCompra?>" size="12" maxlength="12" />
                                                                        <script language="javascript">
                                                                        new tcal({'formname': 'frmData', 'controlname': 'FechaFacturaCompra'});
                                                                        </script>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>                                          
                                                <tr>
                                                    <td>
                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td><div align="right">Importe Neto:</div></td>
                                                                <td>
                                                                    <input type="text" name="ImporteCompraNeto" id="ImporteCompraNeto" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraNeto?>" />
                                                                </td>
                                                                <td><div align="right">Importe Bruto:</div></td>
                                                                <td>
                                                                    <input type="text" name="ImporteCompraBruto" id="ImporteCompraBruto" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraBruto?>" />
                                                                </td>
                                                                <td><div align="right">Importe Nota Cto.:</div></td>
                                                                <td>
                                                                    <input type="text" name="ImporteNotaCredito" id="ImporteNotaCredito" class="camporFormularioChico" maxlength="128" value="<?=$ImporteNotaCredito?>" />
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
  				<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td height="30">
							<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'unidades.php<?=$strParams?>';" value="Cancelar" />
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
</body>
</html>