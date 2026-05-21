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
$DNRPA					= strval($_REQUEST['DNRPA']);
$LugarPatentamiento		= strval($_REQUEST['LugarPatentamiento']);
$Anio					= intval($_REQUEST['Anio']);
$Patente				= strval($_REQUEST['Patente']);
$FechaFacturaCompra		= strval($_REQUEST['FechaFacturaCompra']);
$NumeroFacturaCompra	= strval($_REQUEST['NumeroFacturaCompra']);
$ImporteCompraNeto		= floatval($_REQUEST['ImporteCompraNeto']);
$Iva10					= floatval($_REQUEST['Iva10']);
$Iva21					= floatval($_REQUEST['Iva21']);
$PercepcionIVA			= floatval($_REQUEST['PercepcionIVA']);
$PercepcionIB			= floatval($_REQUEST['PercepcionIB']);
$PercepcionGanancias	= floatval($_REQUEST['PercepcionGanancias']);
$NoGrabado				= floatval($_REQUEST['NoGrabado']);
$ImpuestoInterno		= floatval($_REQUEST['ImpuestoInterno']);
$ImpuestoInternoD		= floatval($_REQUEST['ImpuestoInternoD']);
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
$IdProveedor			= intval($_REQUEST['IdProveedor']);
$FechaPedidoFactura		= strval($_REQUEST['FechaPedidoFactura']);
$FechaRecepcionFactura	= strval($_REQUEST['FechaRecepcionFactura']);
$FechaPatentamiento		= strval($_REQUEST['FechaPatentamiento']);
$Consignacion			= intval($_REQUEST['Consignacion']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$IdFacturaCompra		= intval($_REQUEST['IdFacturaCompra']);
$NumeroCertificado		= strval($_REQUEST['NumeroCertificado']);
$Marcha					= intval($_REQUEST['Marcha']);
$Conforme				= intval($_REQUEST['Conforme']);
$FechaMarchaVencimiento	= strval($_REQUEST['FechaMarchaVencimiento']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= 0;
$oUnidades					= new Unidades();
$oModelos					= new Modelos();
$oUbicaciones				= new Ubicaciones();
$oColores					= new Colores();
$oModelos					= new Modelos();
$oEstadosUnidad				= new EstadosUnidad();
$oFacturasCompras			= new FacturasCompras();
$oUnidadesFacturasCompras	= new UnidadesFacturasCompras();
$oPeriodos					= new Periodos();
$oProveedores 				= new Proveedores();
$oUnidadesArreglos			= new UnidadesArreglos();
$oTallerUnidades			= new TallerUnidades();

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
	if ($NumeroVinPrefijo == '' && $IdEstado != EstadoUnidad::PreVenta)
		$err |= 1;
	if ($IdProveedor == '')
		$err |= 2;
	if ($NumeroVin == '' && $IdUbicacion != Ubicacion::Transito)
		$err |= 4;
	elseif ($oUnidades->GetByNumeroVin($NumeroVin) && $oUnidad->NumeroVin != $NumeroVin)
		$err |= 8;
	if (($NumeroVinPrefijo != '') && ($NumeroVin != '') && ($oUnidades->GetByNumeroChasis($NumeroVinPrefijo . $NumeroVin) && $oUnidad->NumeroChasis != $NumeroVinPrefijo . $NumeroVin) && $IdUbicacion != Ubicacion::Transito)
		$err |= 16;
	/*if ($NumeroMotor == '' && $IdEstado != EstadoUnidad::PreVenta)
		$err |= 32;
	else*/
	if ($NumeroMotor != '' && $oUnidades->GetByNumeroMotor($NumeroMotor) && $oUnidad->NumeroMotor != $NumeroMotor)
		$err |= 64;
	if ($IdUbicacion == '')
		$err |= 128;
	if ($IdEstado == '')
		$err |= 256;
	if ($IdEstado != $oUnidad->IdEstado && $IdEstado == EstadoUnidad::ListoFacturar && $currentUser->IdUsuario != 27 && $currentUser->IdUsuario != 7 && $currentUser->IdUsuario != 16 && $currentUser->IdUsuario != 1)
		$err |= 256;
		
	if ($IdColor == '')
		$err |= 512;
	/*if ($IdEstado != EstadoUnidad::PreVenta && !$oModelo = $oModelos->GetByPrefijoVinAndCodigoComercial($NumeroVinPrefijo, $CodigoComercial))
		$err |= 1024;
	elseif ($IdEstado == EstadoUnidad::PreVenta)*/
	$oModelo = $oModelos->GetById($IdModelo);
	if ($NumeroPedido == '' && $IdEstado == EstadoUnidad::PreVenta)
		$err |= 2048;
	//$brutoCalculado = $ImporteCompraNeto + $Iva10 + $Iva21 + $PercepcionIVA + $PercepcionIB + $PercepcionGanancias + $NoGrabado + $ImpuestoInterno + $ImpuestoInternoD;
	//print_R(abs($ImporteCompraBruto - $ImporteCompraBruto));exit;
	//if (abs($ImporteCompraBruto - $brutoCalculado) > 0.001 || abs($brutoCalculado - 0) > 0.001)
	//	$err |= 4096;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$ImporteCompraNeto	= str_replace(",", ".", $ImporteCompraNeto);
		$Iva21				= str_replace(",", ".", $Iva21);
		$ImpuestoInterno	= str_replace(",", ".", $ImpuestoInterno);
		$ImporteCompraBruto	= str_replace(",", ".", $ImporteCompraBruto);

		$oUnidad->IdModelo				= $oModelo->IdModelo;
		$oUnidad->IdUbicacion			= $IdUbicacion;
		$oUnidad->IdColor				= $IdColor;
		$oUnidad->CodigoComercial		= $CodigoComercial;
		$oUnidad->NumeroVinPrefijo		= $NumeroVinPrefijo;
		$oUnidad->NumeroVin				= $NumeroVin;
		$oUnidad->NumeroMotor			= $NumeroMotor;
		$oUnidad->NumeroChasis			= $NumeroVinPrefijo . $NumeroVin;
		$oUnidad->Anio					= $Anio;
		$oUnidad->Patente				= $Patente;
		$oUnidad->FechaFacturaCompra	= $FechaFacturaCompra;
		$oUnidad->NumeroFacturaCompra	= $NumeroFacturaCompra;
		$oUnidad->ImporteCompraNeto		= $ImporteCompraNeto;
		$oUnidad->Iva10					= $Iva10;
		$oUnidad->Iva21					= $Iva21;
		$oUnidad->PercepcionIVA			= $PercepcionIVA;
		$oUnidad->PercepcionIB			= $PercepcionIB;
		$oUnidad->PercepcionGanancias	= $PercepcionGanancias;
		$oUnidad->ImpuestoInterno		= $ImpuestoInterno;
		$oUnidad->ImpuestoInternoD		= $ImpuestoInternoD;
		$oUnidad->NoGrabado				= $NoGrabado;
		$oUnidad->ImporteCompraBruto	= $ImporteCompraBruto;
		$oUnidad->ImporteNotaCredito	= $ImporteNotaCredito;
		$oUnidad->CodigoRadio			= $CodigoRadio;
		$oUnidad->Cancelada				= $Cancelada;
		$oUnidad->DNRPA					= $DNRPA;
		$oUnidad->Verificado			= $Verificado;
		$oUnidad->Certificado			= $Certificado;
		$oUnidad->IdEstado				= $IdEstado;
		$oUnidad->NumeroPedido			= $NumeroPedido;
		$oUnidad->FechaArriboEstimada	= $FechaArriboEstimada;
		$oUnidad->IdProveedor			= $IdProveedor;
		$oUnidad->FechaPedidoFactura	= $FechaPedidoFactura;
		$oUnidad->FechaRecepcionFactura	= $FechaRecepcionFactura;
		$oUnidad->FechaPatentamiento	= $FechaPatentamiento;
		$oUnidad->Consignacion			= $Consignacion;
		$oUnidad->Observaciones			= $Observaciones;
		$oUnidad->NumeroCertificado		= $NumeroCertificado;
		$oUnidad->Marcha				= $Marcha;
		$oUnidad->Conforme				= $Conforme;
		$oUnidad->FechaMarchaVencimiento= $FechaMarchaVencimiento;
		
		
		if ($IdFacturaCompra)
		{	
			if ($oFacturaCompra = $oFacturasCompras->GetById($IdFacturaCompra))
			{
				$oUnidadesFacturasCompras->DeleteByUnidad($oUnidad);
				
				$oUnidadFacturaCompra					= new UnidadFacturaCompra();
				$oUnidadFacturaCompra->IdUnidad 		= $oUnidad->IdUnidad;
				$oUnidadFacturaCompra->IdFacturaCompra	= $oFacturaCompra->IdFacturaCompra;
				$oUnidadesFacturasCompras->Create($oUnidadFacturaCompra);
			}
		}
		
		$oUnidad = $oUnidades->Update($oUnidad);
		
		if ($oTallerUnidad = $oTallerUnidades->GetByIdUnidad($oUnidad->IdUnidad))
		{
			$oTallerUnidad->Dominio = $oUnidad->Patente;
			$oTallerUnidades->Update($oTallerUnidad);
		}

		header("Location: unidades.php" . $strParams);
		exit();
	}
}
else
{
	$oModelo 					= $oModelos->GetById($oUnidad->IdModelo);
	$oUbicacion 					= $oUbicaciones->GetById($oUnidad->IdUbicacion);
	$oColor 						= $oColores->GetById($oUnidad->IdColor);
	$oEstadoUnidad 					= $oEstadosUnidad->GetById($oUnidad->IdEstado);
	$arrUnidadesFacturasCompras 	= $oUnidadesFacturasCompras->GetAllByUnidad($oUnidad);
	
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
	$Verificado				= $oUnidad->Verificado;
	$Certificado			= $oUnidad->Certificado;
	$IdEstado				= $oEstadoUnidad->IdEstado;
	$Estado					= $oEstadoUnidad->Nombre;
	$EstadoCodigo			= $oEstadoUnidad->Codigo;
	$NumeroPedido			= $oUnidad->NumeroPedido;
	$IdProveedor			= $oUnidad->IdProveedor;
	$FechaPedidoFactura		= CambiarFecha($oUnidad->FechaPedidoFactura);
	$FechaRecepcionFactura	= CambiarFecha($oUnidad->FechaRecepcionFactura);
	$FechaArriboEstimada	= CambiarFecha($oUnidad->FechaArriboEstimada);
	$FechaPatentamiento		= CambiarFecha($oUnidad->FechaPatentamiento);
	$Consignacion			= $oUnidad->Consignacion;
	$Observaciones			= $oUnidad->Observaciones;
	$DNRPA					= $oUnidad->DNRPA;
	$NumeroCertificado		= $oUnidad->NumeroCertificado;
	$Marcha					= $oUnidad->Marcha;
	$Conforme				= $oUnidad->Conforme;
	$FechaMarchaVencimiento	= CambiarFecha($oUnidad->FechaMarchaVencimiento);
}


$arrUnidadesArreglos = $oUnidadesArreglos->GetAllByUnidad($oUnidad);

$TotalArreglos = 0;
foreach ($arrUnidadesArreglos as $oUnidadArreglo)
{
	$TotalArreglos+= $oUnidadArreglo->Importe;
}

$arrProveedores = $oProveedores->GetAll(array('IdRubro' => Rubro::IdVehiculo ));

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
	Get('VehiculoModelo').value 		= oModelo.DenominacionComercial;
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

function SetNumeroChasisPrefijo(value)
{
	Get('NumeroChasisPrefijo').value = value;
}

function SetNumeroChasis(value)
{
	Get('NumeroChasis').value = value;
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
	Get('VehiculoModelo').value 	= oModelo.DenominacionComercial;
}

function FilterNumeroFactura(IdFacturaCompra, Numero)
{
	if (IdFacturaCompra == '')
	{
		Get('NumeroFacturaCompra').value 	= '';
		Get('IdFacturaCompra').value 		= '';
		Get('FechaFacturaCompra').value 	= '';		
	}

	var oFacturaCompra = GetFacturaCompra(IdFacturaCompra);
	if (!(oFacturaCompra))
		return;
	Get('NumeroFacturaCompra').value 	= oFacturaCompra.Numero;
	Get('IdFacturaCompra').value 		= oFacturaCompra.IdFacturaCompra;
	Get('FechaFacturaCompra').value 	= oFacturaCompra.Fecha;
}

$j(document).ready(function() {
	<?php
	if ($IdModelo)
	{
	?>
	FilterCodigoComercial(<?= $IdModelo ?>, '');
	<?php
	}
	if ($IdFacturaCompra)
	{
	?>
	FilterNumeroFactura(<?= $IdFacturaCompra ?>, '');
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Unidades - Modificar</span></td>
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
				<input type="hidden" name="CodigoComercial" id="CodigoComercial" value="<?= $CodigoComercial ?>" />
					
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
                                                                            <td><div id="margen" align="left">Prefijo Vin:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="NumeroVinPrefijo" id="NumeroVinPrefijo" class="camporFormularioSimple" maxlength="10" value="<?=$NumeroVinPrefijo?>" onkeyup="javascript: StrToUpper(this.id); SetNumeroChasisPrefijo(this.value);" autocomplete="Off" />
																					<script language="">
																						SUGGESTRequest('Modelos', 'GetAll', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', 'IdModelo', 'NumeroVinPrefijo', 'FilterNumeroVinPrefijo', filterCodigo);
                                                                                    </script>
																			   </div>
                                                                            </td>
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
                                                                                    <input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSimpleDisabled" readonly="readonly" value="<?=$VehiculoModelo?>" autocomplete="off" />
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
                                                                                <input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" maxlength="9" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id); SetNumeroChasis(this.value);" />
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
                                                                                    <input type="text" name="NumeroPedido" id="NumeroPedido" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroPedido?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                            <td><div id="margen" align="left">DNRPA:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="DNRPA" id="DNRPA" class="camporFormularioSimple" maxlength="128" value="<?=$DNRPA?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                            <td><div id="margen" align="left">N&uacute;mero Certificado:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="NumeroCertificado" id="NumeroCertificado" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroCertificado?>" onkeyup="javascript: StrToUpper(this.id);" />
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
															<tr>
																<td>&nbsp;</td>
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
                                                                                    <select name="IdProveedor" id="IdProveedor" type="text" class="camporFormularioSimple">
																						<option value="">[Seleccione el proveedor]</option>
																						<?php
																						foreach ($arrProveedores as $oProveedor)
																						{
																							$selected = '';
																							if ($oProveedor->IdProveedor == $IdProveedor)
																								$selected = 'selected="selected"';
																						?>
																						<option value="<?= $oProveedor->IdProveedor ?>" <?= $selected ?>><?= $oProveedor->Empresa ?></option>
																						<?php
																						}
																						?>
																						
																					</select>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
																</td>
															</tr>
															<tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione el proveedor</li><?php } ?></td>
                                                            </tr>
															<tr>
                                                                <td>
																	<table width="100%" border="0" cellpadding="0" cellspacing="0">
																		<tr>
																			<td width="25"><input type="checkbox" id="Consignacion" name="Consignacion" value="1" <?= $Consignacion ? 'checked="checked"' : '' ?> />
																			</td>
																			<td>En Consignaci&oacute;n</td>
																		</tr>
																	</table>
																</td>
                                                            </tr>
															<tr>
                                                                <td height="20">&nbsp;</td>
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
                                                                                <select name="Anio" id="Anio" class="camporFormularioSimple">
                                                                                    <option value="">[SELECCIONE]</option>
                                                                                    <?php $year = date('Y'); ?>
                                                                                    <?php for ($i=$year-5; $i<=$year+5; $i++) { ?>
                                                                                    <option value="<?=$i?>" <?=($Anio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                    <?php } ?>
                                                                                </select>                                                                                </div>
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
                                                                        <td><div id="margen" align="left">Fecha Patentamiento:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="FechaPatentamiento" id="FechaPatentamiento" class="camporFormularioSuggest" maxlength="13" value="<?=$FechaPatentamiento?>" />
																				<script language="javascript">
																				new tcal({'formname': 'frmData', 'controlname': 'FechaPatentamiento'});
																				</script>
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
                                                                            <td><div id="margen" align="left">Lugar Patentamiento:</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="LugarPatentamiento" id="LugarPatentamiento" class="camporFormularioSimple" maxlength="128" value="<?=$LugarPatentamiento?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                        <td><div id="margen" align="left">Color:</div></td>
                                                                        <td><div id="margen" align="left">Cod.</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                <input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" maxlength="128" value="<?=$Ubicacion?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                <script language="javascript">
                                                                                SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
                                                                                </script>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="UbicacionCodigo" id="UbicacionCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UbicacionCodigo?>" readonly="readonly" />
                                                                                
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
                                                                                <input type="text" name="Estado" id="Estado" class="camporFormularioSuggest" maxlength="128" value="<?=$Estado?>" onkeyup="javascript: StrToUpper(this.id);" />
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
                                                                                    <input name="FechaMarchaVencimiento" type="text" class="camporFormularioMediano" id="FechaMarchaVencimiento" value="<?=$FechaMarchaVencimiento?>" size="12" maxlength="12" />
																					<script language="javascript">
																					new tcal({'formname': 'frmData', 'controlname': 'FechaMarchaVencimiento'});
																					</script>
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
                                                                                    <textarea name="Observaciones" id="Observaciones" class="camporFormularioSimple" style="height: 75px"><?=$Observaciones?></textarea>
                                                                                 
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
                                                                        <td><div id="margen" align="left">Costos Arreglos:</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div align="left">
                                                                                <input type="text" name="TotalArreglos" id="TotalArreglos" class="camporFormularioSimple" readonly="readonly" value="<?= $TotalArreglos ?>" />
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
                                                                            <input type="text" name="NumeroFacturaCompra" readonly="readonly" id="NumeroFacturaCompra" class="camporFormularioMediano" maxlength="13" value="<?=$NumeroFacturaCompra?>" onkeyup="javascript: StrToUpper(this.id);" />
																			
																		</div>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td><div align="right">Fecha Factura:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <input name="FechaFacturaCompra" type="text" readonly="readonly" class="camporFormularioMediano" id="FechaFacturaCompra" value="<?=$FechaFacturaCompra?>" size="12" maxlength="12" />
                                                                           
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
                                                                    <td><div align="right">IVA 21%:</div></td>
                                                                    <td>
                                                                        <input type="text" name="Iva21" id="Iva21" class="camporFormularioChico" maxlength="128" value="<?=$Iva21?>" />
                                                                    </td>
                                                                    <td><div align="right">Imp. Interno:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImpuestoInterno" id="ImpuestoInterno" class="camporFormularioChico" maxlength="128" value="<?=$ImpuestoInterno?>" />
                                                                    </td>
                                                                    <td><div align="right">Importe Bruto:</div></td>
                                                                    <td>
                                                                        <input type="text" name="ImporteCompraBruto" id="ImporteCompraBruto" class="camporFormularioChico" maxlength="128" value="<?=$ImporteCompraBruto?>" />
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