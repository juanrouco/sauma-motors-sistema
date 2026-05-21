<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUnidad				= intval($_REQUEST['IdUnidad']);

/* declaracion de variables */
$err			= 0;
$oUnidades		= new Unidades();
$oModelos		= new Modelos();
$oUbicaciones	= new Ubicaciones();
$oColores		= new Colores();
$oModelos		= new Modelos();
$oEstadosUnidad	= new EstadosUnidad();
$oFacturasCompras = new FacturasCompras();
$oMinutas = new Minutas();
$oProveedores = new Proveedores();
$oFacturaUnidades = new FacturaUnidades();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oUnidad = $oUnidades->GetById($IdUnidad))
{	
	header("Location: unidades.php" . $strParams);
	exit();
}

$oProveedor = $oProveedores->GetById($oUnidad->IdProveedor);
$oMinuta = $oMinutas->GetById($oUnidad->IdUnidad);


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
	$Iva10					= $oUnidad->Iva10;
	$Iva21					= $oUnidad->Iva21;
	$PercepcionIVA			= $oUnidad->PercepcionIVA;
	$PercepcionIB			= $oUnidad->PercepcionIB;
	$PercepcionGanancias	= $oUnidad->PercepcionGanancias;
	$NoGrabado				= $oUnidad->NoGrabado;
	$ImpuestoInterno		= $oUnidad->ImpuestoInterno;
	$ImpuestoInternoD		= $oUnidad->ImpuestoInternoD;
	$ImporteCompraBruto		= $oUnidad->ImporteCompraBruto;
	$ImporteNotaCredito		= $oUnidad->ImporteNotaCredito;
	$CodigoRadio			= $oUnidad->CodigoRadio;
	$Cancelada				= $oUnidad->Cancelada;
	$Marcha					= $oUnidad->Marcha;
	$Conforme				= $oUnidad->Conforme;
	$FechaMarchaVencimiento	= CambiarFecha($oUnidad->FechaMarchaVencimiento);
	$Observaciones			= $oUnidad->Observaciones;
	$Verificado				= $oUnidad->Verificado;
	$Certificado			= $oUnidad->Certificado;
	$IdEstado				= $oEstadoUnidad->IdEstado;
	$Estado					= $oEstadoUnidad->Nombre;
	$EstadoCodigo			= $oEstadoUnidad->Codigo;
	$NumeroPedido			= $oUnidad->NumeroPedido;
	$FechaPedidoFactura		= CambiarFecha($oUnidad->FechaPedidoFactura);
	$FechaRecepcionFactura		= CambiarFecha($oUnidad->FechaRecepcionFactura);
	$FechaArriboEstimada	= CambiarFecha($oUnidad->FechaArriboEstimada);
	$Observaciones			= $oUnidad->Observaciones;
	
	if ($oMinuta)
	{
		if ($oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oMinuta->IdMinuta))
		{
			$NumeroFacturaVenta = $oFacturaUnidad->NumeroComprobante;
			$FechaFacturaVenta = CambiarFecha($oFacturaUnidad->Fecha);
			$PrecioVenta = number_format($oFacturaUnidad->Total, 2, ',', '.');
		}
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
		
		//Get('Anio').value 					= '';
		Get('CodigoComercial').value 		= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;
	//Get('NumeroVinPrefijo').value 		= '';
	Get('IdModelo').value 				= oModelo.IdModelo;
	//Get('NumeroChasisPrefijo').value 	= '';
	Get('VehiculoModelo').value 		= oModelo.DenominacionModelo;
	Get('CodigoComercial').value 		= oModelo.CodigoComercial;
	//Get('Anio').value 					= oModelo.Anio;
	filterCodigo['FilterCodigoComercial'] = oModelo.CodigoComercial;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Unidades - Detalle Interno <?= $oUnidad->IdUnidad ?></span></td>
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
                                                                            <td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSimple" value="<?=$VehiculoModelo?>" readonly="readonly" />
																					<script language="">
																					SUGGESTRequest('Modelos', 'GetAll', 'VehiculoModelo', 'FilterCodigoComercial', 'IdModelo', 'DenominacionComercial', 'FilterDenominacionComercial', null);
																				</script>
																				</div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
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
                                                                                    <input type="text" name="NumeroVinPrefijo" id="NumeroVinPrefijo" class="camporFormularioSimple" maxlength="10" value="<?=$NumeroVinPrefijo?>" onkeyup="javascript: StrToUpper(this.id); SetNumeroChasisPrefijo(this.value);" readonly="readonly" />
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
                                                                        <td><div id="margen" align="left">N&uacute;mero Vin:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input  readonly="readonly" type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" maxlength="7" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id); SetNumeroChasis(this.value);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                <input readonly="readonly" type="text" name="NumeroChasisPrefijo" id="NumeroChasisPrefijo" class="camporFormularioMedianoSuggestDisabled" maxlength="10" readonly="readonly" value="<?=$NumeroVinPrefijo?>" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="NumeroChasis" id="NumeroChasis" class="camporFormularioMedianoSuggestDisabled" maxlength="7" value="<?=$NumeroChasis?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                <input readonly="readonly" type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroMotor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
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
                                                                                    <input readonly="readonly" type="text" name="NumeroPedido" id="NumeroPedido" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroPedido?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                    <input readonly="readonly" name="FechaArriboEstimada" type="text" class="camporFormularioSimple" id="FechaArriboEstimada" value="<?=$FechaArriboEstimada?>" size="12" maxlength="12" />
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
                                                                            <td><div id="margen" align="left">Proveedor:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input readonly="readonly" name="FechaArriboEstimada" type="text" class="camporFormularioSimple" id="FechaArriboEstimada" value="<?=$oProveedor->Empresa?>" size="12" maxlength="12" />
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
                                                                                <input readonly="readonly" type="text" name="Anio" id="Anio" class="camporFormularioSimple" value="<?=$Anio?>" />
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
                                                                                <input readonly="readonly" type="text" name="Patente" id="Patente" class="camporFormularioSimple" maxlength="10" value="<?=$Patente?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                <input readonly="readonly" type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td></td>
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
                                                                                <input readonly="readonly" type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" maxlength="128" value="<?=$Ubicacion?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="UbicacionCodigo" id="UbicacionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UbicacionCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td></td>
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
                                                                                <input readonly="readonly" type="text" name="Estado" id="Estado" class="camporFormularioSuggest" maxlength="128" value="<?=$Estado?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="EstadoCodigo" id="EstadoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$EstadoCodigo?>" readonly="readonly" />
                                                                                
                                                                            </div>
                                                                        </td>
                                                                        <td>&nbsp;</td>
                                                                        <td></td>
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
                                                                                <input readonly="readonly" type="text" name="CodigoRadio" id="CodigoRadio" class="camporFormularioSimple" maxlength="10" value="<?=$CodigoRadio?>" />
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
                                                                        <td>Certificado:</td>
                                                                        <td><input type="checkbox" name="Certificado" id="Certificado" value="1" <?=($Certificado) ? 'checked="checked"' : '';?>  /></td>
                                                                    </tr>
																	<tr>
                                                                        <td>Marcha:</td>
                                                                        <td><input type="checkbox" name="Marcha" id="Marcha" value="1" <?=($Marcha) ? 'checked="checked"' : '';?> /></td>
                                                                        <td>Conforme:</td>
                                                                        <td><input type="checkbox" name="Conforme" id="Conforme" value="1" <?=($Conforme) ? 'checked="checked"' : '';?>  /></td>
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
                                                                            <td><div id="margen" align="left">Fecha Marcha Vencimiento:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input name="FechaMarchaVencimiento" readonly="true" type="text" class="camporFormularioMediano" id="FechaMarchaVencimiento" value="<?=$FechaMarchaVencimiento?>" size="12" maxlength="12" />
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
                                                                            <td><div id="margen" align="left">Observaciones:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <textarea name="Observaciones" readonly="true" id="Observaciones" class="camporFormularioSimple" style="height: 75px"><?=$Observaciones?></textarea>
                                                                                 
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
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
								<?php
								if (Session::CheckPerm(PERM_COMPRA_LIST) && true)
								{
								?>
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
                                                                    <td><div align="right">Pedido Factura:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input type="text" name="FechaPedidoFactura" id="FechaPedidoFactura" class="camporFormularioChico" maxlength="13" value="<?=$FechaPedidoFactura?>" />
																			<script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaPedidoFactura'});
                                                                            </script>
																		</div>
                                                                    </td>
                                                                    <td width="20">&nbsp;</td>
                                                                    <td><div align="right">Recepcion Factura:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaRecepcionFactura" type="text" class="camporFormularioChico" id="FechaRecepcionFactura" value="<?=$FechaRecepcionFactura?>" size="12" maxlength="12" />
                                                                            <script language="javascript">
                                                                            new tcal({'formname': 'frmData', 'controlname': 'FechaRecepcionFactura'});
                                                                            </script>
                                                                        </div>
                                                                    </td>
                                                                    <td width="20">&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            <tr>
                                                                <td><div align="right">Nro. Factura:</div></td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input readonly="readonly" type="text" name="NumeroFacturaCompra" id="NumeroFacturaCompra" class="camporFormularioMediano" maxlength="10" value="<?=$NumeroFacturaCompra?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td><div align="right">Fecha Factura:</div></td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input readonly="readonly" name="FechaFacturaCompra" type="text" class="camporFormularioMediano" id="FechaFacturaCompra" value="<?=$FechaFacturaCompra?>" size="12" maxlength="12" />
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
                                                                        <input readonly="readonly" type="text" name="ImporteCompraNeto" id="ImporteCompraNeto" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraNeto?>" />
                                                                    </td>
                                                                    <td><div align="right">IVA 10,5%:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="Iva10" id="Iva10" class="camporFormularioChico" maxlength="128" value="<?=$Iva10?>" />
                                                                    </td>
                                                                    <td><div align="right">IVA 21%:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="Iva21" id="Iva21" class="camporFormularioChico" maxlength="128" value="<?=$Iva21?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Percepci&oacute;n IVA:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="PercepcionIVA" id="PercepcionIVA" class="camporFormularioChico" maxlength="128" value="<?=$PercepcionIVA?>" />
                                                                    </td>
                                                                    <td><div align="right">Percepci&oacute;n IIBB:</div></td>
                                                                    <td>
                                                                        <input  readonly="readonly" type="text" name="PercepcionIB" id="PercepcionIB" class="camporFormularioChico" maxlength="128" value="<?=$PercepcionIB?>" />
                                                                    </td>
                                                                    <td><div align="right">Percepci&oacute;n Ganancias:</div></td>
                                                                    <td>
                                                                        <input type="text" name="PercepcionGanancias" id="PercepcionGanancias" class="camporFormularioChico" maxlength="128" value="<?=$PercepcionGanancias?>" />
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">Imp. Interno:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="ImpuestoInterno" id="ImpuestoInterno" class="camporFormularioChico" maxlength="128" value="<?=$ImpuestoInterno?>" />
                                                                    </td>
                                                                    <td><div align="right">Imp. Interno Diesel:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="ImpuestoInternoD" id="ImpuestoInternoD" class="camporFormularioChico" maxlength="128" value="<?=$ImpuestoInternoD?>" />
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
																<tr>
                                                                    <td><div align="right">No Grabado:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="NoGrabado" id="NoGrabado" class="camporFormularioChico" maxlength="128" value="<?=$NoGrabado?>" />
                                                                    </td>
                                                                    <td><div align="right">Importe Bruto:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="ImporteCompraBruto" id="ImporteCompraBruto" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraBruto?>" />
                                                                    </td>
                                                                    <td><div align="right">mporte Nota Cto.:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="ImporteNotaCredito" id="ImporteNotaCredito" class="camporFormularioChico" maxlength="128" value="<?=$ImporteNotaCredito?>" />
                                                                    </td>
                                                                </tr>
																<tr>
																	<td colspan="6" height="20"><?php if ($err & 4096) { ?><li style="color:#FF0000;">Los importes ingresados son inconsistentes.</li><?php } ?></td>
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
								<?php
								}
								?>
								<tr>
                                    <td>
                                        <div align="center">
                                            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                <tr>
                                                    <td height="40" align="center"><span class="tituloPagina">Datos de Venta</span></td>
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
                                                                        <input readonly="readonly" type="text" name="NumeroFacturaVenta" id="NumeroFacturaVenta" class="camporFormularioMediano" maxlength="10" value="<?=$NumeroFacturaVenta?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td><div align="right">Fecha Factura:</div></td>
                                                                <td>
                                                                    <div align="left">
                                                                        <input readonly="readonly" name="FechaFacturaVenta" type="text" class="FechaFacturaVenta" id="FechaFacturaCompra" value="<?=$FechaFacturaVenta?>" size="12" maxlength="12" />
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
                                                                    
                                                                    <td><div align="right">Precio de Venta.:</div></td>
                                                                    <td>
                                                                        <input readonly="readonly" type="text" name="PrecioVenta" id="PrecioVenta" class="camporFormularioChico" maxlength="128" value="<?=$PrecioVenta?>" />
                                                                    </td>
                                                                </tr>
																<tr>
																	<td colspan="6" height="20"><?php if ($err & 4096) { ?><li style="color:#FF0000;">Los importes ingresados son inconsistentes.</li><?php } ?></td>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'unidades.php<?=$strParams?>';" value="Volver" />
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