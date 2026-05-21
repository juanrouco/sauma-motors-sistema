<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MODE_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdTipoCombustible			= intval($_REQUEST['IdTipoCombustible']);
$TipoModelo					= strval($_REQUEST['TipoModelo']);
$TipoModeloCodigo			= strval($_REQUEST['TipoModeloCodigo']);
$IdTipoModelo				= intval($_REQUEST['IdTipoModelo']);

$TipoVehiculo				= strval($_REQUEST['TipoVehiculo']);
$TipoVehiculoCodigo			= strval($_REQUEST['TipoVehiculoCodigo']);
$IdTipoVehiculo				= intval($_REQUEST['IdTipoVehiculo']);
$TipoCarroceria				= strval($_REQUEST['TipoCarroceria']);
$TipoCarroceriaCodigo		= strval($_REQUEST['TipoCarroceriaCodigo']);
$IdTipoCarroceria			= intval($_REQUEST['IdTipoCarroceria']);
$TipoUso					= strval($_REQUEST['TipoUso']);
$TipoUsoCodigo				= strval($_REQUEST['TipoUsoCodigo']);
$IdTipoUso					= intval($_REQUEST['IdTipoUso']);
$DestinoVehiculo			= strval($_REQUEST['DestinoVehiculo']);
$DestinoVehiculoCodigo		= strval($_REQUEST['DestinoVehiculoCodigo']);
$IdDestinoVehiculo			= intval($_REQUEST['IdDestinoVehiculo']);

$CategoriaModelo			= strval($_REQUEST['CategoriaModelo']);
$CategoriaModeloCodigo		= strval($_REQUEST['CategoriaModeloCodigo']);
$IdCategoriaModelo			= intval($_REQUEST['IdCategoriaModelo']);
$MarcaVehiculo				= strval($_REQUEST['MarcaVehiculo']);
$MarcaVehiculoCodigo		= strval($_REQUEST['MarcaVehiculoCodigo']);
$IdMarcaVehiculo			= intval($_REQUEST['IdMarcaVehiculo']);
$MarcaMotor					= strval($_REQUEST['MarcaMotor']);
$MarcaMotorCodigo			= strval($_REQUEST['MarcaMotorCodigo']);
$IdMarcaMotor				= intval($_REQUEST['IdMarcaMotor']);
$MarcaChasis				= strval($_REQUEST['MarcaChasis']);
$MarcaChasisCodigo			= strval($_REQUEST['MarcaChasisCodigo']);
$IdMarcaChasis				= intval($_REQUEST['IdMarcaChasis']);
$NumeroVin					= strval($_REQUEST['NumeroVin']);
$CodigoComercial			= strval($_REQUEST['CodigoComercial']);
$DenominacionComercial		= strval($_REQUEST['DenominacionComercial']);
$DenominacionModelo			= strval($_REQUEST['DenominacionModelo']);
$Anio						= intval($_REQUEST['Anio']);
$Peso						= floatval($_REQUEST['Peso']);
$Flete			= floatval($_REQUEST['Flete']);
$MesPrecioTotal				= floatval($_REQUEST['MesPrecioTotal']);
$Patentamiento				= floatval($_REQUEST['Patentamiento']);
$MesPrecioTotalGeneral		= floatval($_REQUEST['MesPrecioTotalGeneral']);
$ReventaPrecio				= floatval($_REQUEST['ReventaPrecio']);
$Iva						= floatval($_REQUEST['Iva']);
$Prenda						= floatval($_REQUEST['Prenda']);
$Otorgamiento				= floatval($_REQUEST['Otorgamiento']);
$ImpuestoInterno			= floatval($_REQUEST['ImpuestoInterno']);
$BonificacionCompra			= floatval($_REQUEST['BonificacionCompra']);
$BonificacionVenta			= floatval($_REQUEST['BonificacionVenta']);
$BonificacionExtra			= floatval($_REQUEST['BonificacionExtra']);
$DescuentoReventa			= floatval($_REQUEST['DescuentoReventa']);
$PrecioPublicoNeto			= floatval($_REQUEST['PrecioPublicoNeto']);
$PrecioPublicoTotalIva		= floatval($_REQUEST['PrecioPublicoTotalIva']);
$PrecioCompra				= floatval($_REQUEST['PrecioCompra']);
$Precio1					= floatval($_REQUEST['Precio1']);
$Precio2					= floatval($_REQUEST['Precio2']);
$RecuperoBonificacion		= floatval($_REQUEST['RecuperoBonificacion']);
$Cilindrada					= strval($_REQUEST['Cilindrada']);
$Electrolito				= floatval($_REQUEST['Electrolito']);
$GTIN						= strval($_REQUEST['GTIN']);
$Cufe						= strval($_REQUEST['Cufe']);

$Submit						= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oModelo 	= new Modelo();
$oModelos	= new Modelos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($IdTipoCombustible == '')
		$err |= 2048;
	if ($IdTipoModelo == '')
		$err |= 1;
	if ($IdCategoriaModelo == '')
		$err |= 1024;
	if ($IdMarcaMotor == '')
		$err |= 2;
	if ($IdMarcaChasis == '')
		$err |= 4;
	if ($IdMarcaVehiculo == '')
		$err |= 8;
	/*if ($CodigoComercial == '')
		$err |= 16;*/
	if ($DenominacionComercial == '')
		$err |= 32;
	/*if ($NumeroVin == '')
		$err |= 64;
	else*/if ($NumeroVin != '' && strlen($NumeroVin) != 10 && strlen($NumeroVin) != 8)
		$err |= 128;
	if ($DenominacionModelo == '')
		$err |= 256;
	if ($Anio == '')
		$err |= 512;

	/* si no hay errores... */
	if ($err == 0)
	{
		$Peso						= str_replace(",", ".", $Peso);
		$PrecioPublicoNeto			= str_replace(",", ".", $PrecioPublicoNeto);
		$PrecioPublicoTotalIva		= str_replace(",", ".", $PrecioPublicoTotalIva);		
		$PrecioCompra				= str_replace(",", ".", $PrecioCompra);		
		$Precio1					= str_replace(",", ".", $Precio1);		
		$Precio2					= str_replace(",", ".", $Precio2);		
		$MesPrecioTotal				= str_replace(",", ".", $MesPrecioTotal);
		$Patentamiento				= str_replace(",", ".", $Patentamiento);
		$MesPrecioTotalGeneral		= str_replace(",", ".", $MesPrecioTotalGeneral);
		$Flete						= str_replace(",", ".", $Flete);
		$ReventaPrecio				= str_replace(",", ".", $ReventaPrecio);		
		$Iva						= str_replace(",", ".", $Iva);
		$Prenda						= str_replace(",", ".", $Prenda);
		$Otorgamiento				= str_replace(",", ".", $Otorgamiento);
		$ImpuestoInterno			= str_replace(",", ".", $ImpuestoInterno);
		$BonificacionCompra			= str_replace(",", ".", $BonificacionCompra);
		$BonificacionVenta			= str_replace(",", ".", $BonificacionVenta);
		$BonificacionExtra			= str_replace(",", ".", $BonificacionExtra);
		$DescuentoReventa			= str_replace(",", ".", $DescuentoReventa);
		$RecuperoBonificacion		= str_replace(",", ".", $RecuperoBonificacion);

		$oModelo->IdTipoCombustible			= $IdTipoCombustible;
		$oModelo->IdTipoModelo				= $IdTipoModelo;
		$oModelo->IdCategoriaModelo			= $IdCategoriaModelo;
		$oModelo->IdMarcaVehiculo			= $IdMarcaVehiculo;
		$oModelo->IdMarcaMotor				= $IdMarcaMotor;
		$oModelo->IdMarcaChasis				= $IdMarcaChasis;
		
		$oModelo->IdTipoVehiculo			= $IdTipoVehiculo;
		$oModelo->IdTipoCarroceria			= $IdTipoCarroceria;
		$oModelo->IdTipoUso					= $IdTipoUso;
		$oModelo->IdDestinoVehiculo			= $IdDestinoVehiculo;
		
		$oModelo->NumeroVinPrefijo			= $NumeroVin;
		$oModelo->CodigoComercial			= $CodigoComercial;
		$oModelo->DenominacionComercial		= $DenominacionComercial;
		$oModelo->DenominacionModelo		= $DenominacionModelo;
		$oModelo->Anio						= $Anio;
		$oModelo->Peso						= $Peso;
		$oModelo->PrecioPublicoNeto			= $PrecioPublicoNeto;
		$oModelo->PrecioPublicoTotalIva		= $PrecioPublicoTotalIva;		
		$oModelo->PrecioCompra				= $PrecioCompra;		
		$oModelo->Precio1					= $Precio1;
		$oModelo->Precio2					= $Precio2;
		$oModelo->MesPrecioTotal			= $MesPrecioTotal;
		$oModelo->Patentamiento				= $Patentamiento;
		$oModelo->MesPrecioTotalGeneral		= $MesPrecioTotalGeneral;
		$oModelo->Flete			= $Flete;
		$oModelo->ReventaPrecio				= $ReventaPrecio;		
		$oModelo->Iva						= $Iva;
		$oModelo->Prenda					= $Prenda;
		$oModelo->Otorgamiento				= $Otorgamiento;
		$oModelo->ImpuestoInterno			= $ImpuestoInterno;		
		$oModelo->BonificacionCompra		= $BonificacionCompra;
		$oModelo->BonificacionVenta			= $BonificacionVenta;
		$oModelo->BonificacionExtra			= $BonificacionExtra;
		$oModelo->DescuentoReventa			= $DescuentoReventa;
		$oModelo->RecuperoBonificacion		= $RecuperoBonificacion;
		$oModelo->Cilindrada				= $Cilindrada;
		$oModelo->Electrolito				= $Electrolito;
		$oModelo->GTIN						= $GTIN;
		$oModelo->Cufe						= $Cufe;
	
		$oModelo = $oModelos->Create($oModelo);

		header("Location: modelos.php" . $strParams);
		exit();
	}
}
else
{
	$Anio = date('Y');
	$Iva = '21';
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterMarcaVehiculo(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('MarcaVehiculoCodigo').value 	= '';
		Get('MarcaVehiculo').value 			= '';
		Get('IdMarcaVehiculo').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('MarcaVehiculoCodigo').value 	= oMarca.Codigo;
	Get('MarcaVehiculo').value 			= oMarca.Nombre;
	Get('IdMarcaVehiculo').value 		= oMarca.IdMarca;
}

function FilterMarcaMotor(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('MarcaMotorCodigo').value 	= '';
		Get('MarcaMotor').value 		= '';
		Get('IdMarcaMotor').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('MarcaMotorCodigo').value 	= oMarca.Codigo;
	Get('MarcaMotor').value 		= oMarca.Nombre;
	Get('IdMarcaMotor').value 		= oMarca.IdMarca;
}

function FilterMarcaChasis(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('MarcaChasisCodigo').value 	= '';
		Get('MarcaChasis').value 		= '';
		Get('IdMarcaChasis').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('MarcaChasisCodigo').value 	= oMarca.Codigo;
	Get('MarcaChasis').value 		= oMarca.Nombre;
	Get('IdMarcaChasis').value 		= oMarca.IdMarca;
}

function FilterTipoModelo(IdTipoModelo, Nombre)
{
	if ((IdTipoModelo == '') && (Nombre == ''))
	{
		Get('TipoModeloCodigo').value 	= '';
		Get('TipoModelo').value 		= '';
		Get('IdTipoModelo').value 		= '';
	}

	var oTipoModelo = GetTipoModelo(IdTipoModelo);
	if (!(oTipoModelo))
		return;
	
	Get('TipoModeloCodigo').value 	= oTipoModelo.Codigo;
	Get('TipoModelo').value 		= oTipoModelo.Nombre;
	Get('IdTipoModelo').value 		= oTipoModelo.IdTipoModelo;
}

function FilterCategoriaModelo(IdCategoriaModelo, Nombre)
{
	if ((IdCategoriaModelo == '') && (Nombre == ''))
	{
		Get('CategoriaModeloCodigo').value 	= '';
		Get('CategoriaModelo').value 		= '';
		Get('IdCategoriaModelo').value 		= '';
	}

	var oCategoriaModelo = GetCategoriaModelo(IdCategoriaModelo);
	if (!(oCategoriaModelo))
		return;
	
	Get('CategoriaModeloCodigo').value 	= oCategoriaModelo.Codigo;
	Get('CategoriaModelo').value 		= oCategoriaModelo.Nombre;
	Get('IdCategoriaModelo').value 		= oCategoriaModelo.IdCategoriaModelo;
}


function FilterTipoVehiculo(IdTipoVehiculo, Nombre)
{
	if ((IdTipoVehiculo == '') && (Nombre == ''))
	{
		Get('TipoVehiculoCodigo').value 	= '';
		Get('TipoVehiculo').value 			= '';
		Get('IdTipoVehiculo').value 		= '';
	}

	var oTipoVehiculo = GetTipoVehiculo(IdTipoVehiculo);
	if (!(oTipoVehiculo))
		return;
	
	Get('TipoVehiculoCodigo').value 	= oTipoVehiculo.Codigo;
	Get('TipoVehiculo').value 		= oTipoVehiculo.Nombre;
	Get('IdTipoVehiculo').value 		= oTipoVehiculo.IdTipoVehiculo;
}

function FilterTipoUso(IdTipoUso, Nombre)
{
	if ((IdTipoUso == '') && (Nombre == ''))
	{
		Get('TipoUsoCodigo').value 	= '';
		Get('TipoUso').value 			= '';
		Get('IdTipoUso').value 		= '';
	}

	var oTipoUso = GetTipoUso(IdTipoUso);
	if (!(oTipoUso))
		return;
	
	Get('TipoUsoCodigo').value 	= oTipoUso.Codigo;
	Get('TipoUso').value 		= oTipoUso.Nombre;
	Get('IdTipoUso').value 		= oTipoUso.IdTipoUso;
}

function FilterTipoCarroceria(IdTipoCarroceria, Nombre)
{
	if ((IdTipoCarroceria == '') && (Nombre == ''))
	{
		Get('TipoCarroceriaCodigo').value 	= '';
		Get('TipoCarroceria').value 			= '';
		Get('IdTipoCarroceria').value 		= '';
	}

	var oTipoCarroceria = GetTipoCarroceria(IdTipoCarroceria);
	if (!(oTipoCarroceria))
		return;
	
	Get('TipoCarroceriaCodigo').value 	= oTipoCarroceria.Codigo;
	Get('TipoCarroceria').value 		= oTipoCarroceria.Nombre;
	Get('IdTipoCarroceria').value 		= oTipoCarroceria.IdTipoCarroceria;
}

function FilterDestinoVehiculo(IdDestinoVehiculo, Nombre)
{
	if ((IdDestinoVehiculo == '') && (Nombre == ''))
	{
		Get('DestinoVehiculoCodigo').value 	= '';
		Get('DestinoVehiculo').value 			= '';
		Get('IdDestinoVehiculo').value 		= '';
	}

	var oDestinoVehiculo = GetDestinoVehiculo(IdDestinoVehiculo);
	if (!(oDestinoVehiculo))
		return;
	
	Get('DestinoVehiculoCodigo').value 	= oDestinoVehiculo.Codigo;
	Get('DestinoVehiculo').value 		= oDestinoVehiculo.Nombre;
	Get('IdDestinoVehiculo').value 		= oDestinoVehiculo.IdDestinoVehiculo;
}

function validateNumber(value) {
	return value != '' && value != NaN;
}

function CalcularPrecioPublicoTotalIva() {
	var Neto = parseFloat($j('#PrecioPublicoNeto').val());
	var Iva = parseFloat($j('#Iva').val());
	if (Neto && Iva) {
		$j('#PrecioPublicoTotalIva').val((Neto * (Iva / 100)).toFixed(2));
	} 
	else {
		$j('#PrecioPublicoTotalIva').val(0);
	}
}

function CalcularPrecioCompra() {
	var Neto = parseFloat($j('#PrecioPublicoNeto').val());
	var PrecioPublicoTotalIva = parseFloat($j('#PrecioPublicoTotalIva').val());
	var ImpuestoInterno = parseFloat($j('#ImpuestoInterno').val());
	if (!Neto) {
		Neto = 0;
	} 
	$j('#Flete').val((<?= Config::Flete ?> + Neto * <?= Config::Seguro ?>).toFixed(2));
	var Flete = parseFloat($j('#Flete').val());
	if (!PrecioPublicoTotalIva) {
		PrecioPublicoTotalIva = 0;
	} 
	if (!ImpuestoInterno) {
		ImpuestoInterno = 0;
	}
	if (!Flete) {
		Flete = 0;
	} 
	
	$j('#PrecioCompra').val(((Neto + Flete) * 1.275 + ImpuestoInterno +  100).toFixed(2));
}

$j(document).ready(function() {	
	$j('#Iva').change(function() {
		CalcularPrecioPublicoTotalIva();
		CalcularPrecioCompra();
	});
	
	$j('#PrecioPublicoNeto').keyup(function(e) {
		CalcularPrecioPublicoTotalIva();
		CalcularPrecioCompra();
	});
	
	$j('#Flete,#ImpuestoInterno').keyup(function(e) {
		CalcularPrecioCompra();
	});
	
	$j('#DescuentoReventa,#RecuperoBonificacion').keyup(function(e) {		
		CalcularPrecioReventa();
	});
	
	$j('#Precio1').keyup(function(e) {		
		var Precio1 = parseFloat($j('#Precio1').val());
		if (Precio1)
			$j('#Precio2').val((Precio1 * 1.1).toFixed(2));
		else
			$j('#Precio2').val(0);
	});
});

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
    <input type="hidden" name="IdTipoModelo" id="IdTipoModelo" value="<?=$IdTipoModelo?>" />
    <input type="hidden" name="IdCategoriaModelo" id="IdCategoriaModelo" value="<?=$IdCategoriaModelo?>" />
    <input type="hidden" name="IdMarcaVehiculo" id="IdMarcaVehiculo" value="<?=$IdMarcaVehiculo?>" />
    <input type="hidden" name="IdMarcaMotor" id="IdMarcaMotor" value="<?=$IdMarcaMotor?>" />
    <input type="hidden" name="IdMarcaChasis" id="IdMarcaChasis" value="<?=$IdMarcaChasis?>" />
	<input type="hidden" name="IdTipoVehiculo" id="IdTipoVehiculo" value="<?=$IdTipoVehiculo?>" />
	<input type="hidden" name="IdTipoUso" id="IdTipoUso" value="<?=$IdTipoUso?>" />
	<input type="hidden" name="IdTipoCarroceria" id="IdTipoCarroceria" value="<?=$IdTipoCarroceria?>" />	
	<input type="hidden" name="IdDestinoVehiculo" id="IdDestinoVehiculo" value="<?=$IdDestinoVehiculo?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Modelos - Agregar</span></td>
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
                    <table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="75%" border="0" align="center" cellpadding="0" cellspacing="0">
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
                                                                            <td><div id="margen" align="left">Veh&iacute;culo Marca:</div></td>
                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaVehiculo" id="MarcaVehiculo" class="camporFormularioSuggest" maxlength="128" value="<?=$MarcaVehiculo?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'MarcaVehiculo', 'FilterMarcaVehiculo', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                    </script>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaVehiculoCodigo" id="MarcaVehiculoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$MarcaVehiculoCodigo?>" readonly="readonly" />
                                                                                    
                                                                                </div>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td><input type="button" id="btnAddMarcaVehiculo" class="botonBasico"  onClick="javascript:AddMarca('Vehiculo');" value=" + " /></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese la marca del vehiculo</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Veh&iacute;culo Modelo (Para gestor&iacute;a):</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="DenominacionModelo" id="DenominacionModelo" class="camporFormularioSimple" maxlength="128" value="<?=$DenominacionModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                        <span style="color:#FF0000;">&nbsp;(*)</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Ingrese el modelo de veh&iacute;culo</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Denominaci&oacute;n Comercial:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="DenominacionComercial" id="DenominacionComercial" class="camporFormularioSimple" maxlength="128" value="<?=$DenominacionComercial?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                        <span style="color:#FF0000;">&nbsp;(*)</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la denominaci&oacute;n comercial</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">Motor Marca:</div></td>
                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaMotor" id="MarcaMotor" class="camporFormularioSuggest" maxlength="128" value="<?=$MarcaMotor?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'MarcaMotor', 'FilterMarcaMotor', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                    </script>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaMotorCodigo" id="MarcaMotorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$MarcaMotorCodigo?>" readonly="readonly" />
                                                                                    
                                                                                </div>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td><input type="button" id="btnAddMarcaMotor" class="botonBasico"  onClick="javascript:AddMarca('Motor');" value=" + " /></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese la marca del motor</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">Chasis Marca:</div></td>
                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaChasis" id="MarcaChasis" class="camporFormularioSuggest" maxlength="128" value="<?=$MarcaChasis?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'MarcaChasis', 'FilterMarcaChasis', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                    </script>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="MarcaChasisCodigo" id="MarcaChasisCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$MarcaChasisCodigo?>" readonly="readonly" />
                                                                                    
                                                                                </div>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td><input type="button" id="btnAddMarcaChasis" class="botonBasico"  onClick="javascript:AddMarca('Chasis');" value=" + " /></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>                                                            
                                                            <tr>
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la marca del chasis</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Prefijo VIN:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" maxlength="10" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="25"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese el nro de vin</li><?php } ?><?php if ($err & 128) { ?><li style="color:#FF0000;">La extensi&oacute;n debe ser de 8 o 10 carcteres.</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Iva:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <select name="Iva" id="Iva" class="camporFormularioSimple">
                                                                            <option value="">[Iva]</option>
                                                                            <option value="10.5" <?=($Iva == '10.5') ? 'selected="selected"' : ''?>>10.5%</option>
                                                                            <option value="21" <?=($Iva == '21') ? 'selected="selected"' : ''?>>21%</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
															<tr>
                                                                <td height="20"></td>
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
                                                                            <td><div id="margen" align="left">Veh&iacute;culo Tipo:</div></td>
                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="TipoModelo" id="TipoModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$TipoModelo?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('TiposModelo', 'GetAll', 'TipoModelo', 'FilterTipoModelo', 'IdTipoModelo', 'Nombre', 'FilterNombre', null);																		
                                                                                    </script>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="TipoModeloCodigo" id="TipoModeloCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$TipoModeloCodigo?>" readonly="readonly" />
                                                                                    
                                                                                </div>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td><input type="button" id="btnAddTipoModelo" class="botonBasico"  onClick="javascript:AddTipoModelo();" value=" + " /></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>                                                               
                                                            <tr>
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el tipo de veh&iacute;culo</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td><div id="margen" align="left">Veh&iacute;culo Categor&iacute;a:</div></td>
                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="CategoriaModelo" id="CategoriaModelo" class="camporFormularioSuggest" maxlength="128" value="<?=$CategoriaModelo?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
                                                                                    <script language="">
                                                                                    SUGGESTRequest('CategoriasModelo', 'GetAll', 'CategoriaModelo', 'FilterCategoriaModelo', 'IdCategoriaModelo', 'Nombre', 'Nombre', null);																		
                                                                                    </script>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div align="left">
                                                                                    <input type="text" name="CategoriaModeloCodigo" id="CategoriaModeloCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$CategoriaModeloCodigo?>" readonly="readonly" />
                                                                                    
                                                                                </div>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                            <td><input type="button" id="btnAddCategoriaModelo" class="botonBasico"  onClick="javascript:AddCategoriaModelo();" value=" + " /></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>                                                               
                                                            <tr>
                                                                <td height="20"><?php if ($err & 1024) { ?><li style="color:#FF0000;">Ingrese la categor&iacute;a</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Combustible:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <select name="IdTipoCombustible" id="IdTipoCombustible" class="camporFormularioSimple">
                                                                            <option value="">[SELECCIONE]</option>
                                                                            <?php foreach (CombustibleTipos::GetAll() as $oCombustibleTipo) { ?>
                                                                            <option value="<?=$oCombustibleTipo['IdTipo']?>" <?=($IdTipoCombustible == $oCombustibleTipo['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oCombustibleTipo['Descripcion']?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2048) { ?><li style="color:#FF0000;">Seleccione el tipo de combustible</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Modelo A&ntilde;o:</div></td>
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
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o del modelo</li><?php } ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Peso Imponible (en Kg.):</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="Peso" id="Peso" class="camporFormularioSimple" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Peso?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Cilindrada:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="Cilindrada" id="Cilindrada" class="camporFormularioSimple" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Cilindrada?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Volumen de Electrolito (Lts):</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="Electrolito" id="Electrolito" class="camporFormularioSimple" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Electrolito?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">GTIN:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="GTIN" id="GTIN" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$GTIN?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><div id="margen" align="left">Cufe Origen:</div></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div align="left">
                                                                        <input type="text" name="Cufe" id="Cufe" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Cufe?>" />
                                                                    </div>
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
                                                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Importes</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td class="bordeGris">
                                            <div align="center">
                                                <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td colspan="2" height="20" align="center"><span class="tituloMenu">Costos</span></td>
                                                    </tr>
                                                    <tr>
														<td colspan="2">
															<table width="100%" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td><div align="right">Neto:</div></td>
																	<td>
																		<input type="text" name="PrecioPublicoNeto" id="PrecioPublicoNeto" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$PrecioPublicoNeto?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Impuesto:</div></td>
																	<td>
																		<input type="text" name="ImpuestoInterno" id="ImpuestoInterno" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$ImpuestoInterno?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Iva:</div></td>
																	<td>
																		<input type="text" name="PrecioPublicoTotalIva" id="PrecioPublicoTotalIva" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" readonly="true" value="<?=$PrecioPublicoTotalIva?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Flete y Seguro:</div></td>
																	<td>
																		<input type="text" name="Flete" id="Flete" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;"  value="<?=$Flete?>" />

																	</td>
																</tr>
															</table>
														</td>
                                                    </tr>   
													<tr>
														<td colspan="2">&nbsp;</td>
													</tr>
													<tr>
                                                        <td colspan="2" height="20" align="center"><span class="tituloMenu">Precios al P&uacute;blico</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
															<table width="100%" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td><div align="right">Galp&oacute;n:</div></td>
																	<td>
																		<input type="text" name="PrecioCompra" id="PrecioCompra" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" readonly="true" value="<?=$PrecioCompra?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Sugerido Fact.:</div></td>
																	<td>
																		<input type="text" name="ReventaPrecio" id="ReventaPrecio" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$ReventaPrecio?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Contado:</div></td>
																	<td>
																		<input type="text" name="Precio1" id="Precio1" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Precio1?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Cr&eacute;dito:</div></td>
																	<td>
																		<input type="text" name="Precio2" id="Precio2" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Precio2?>" />

																	</td>
																	<td width="10">&nbsp;</td>
																	<td><div align="right">Patente:</div></td>
																	<td>
																		<input type="text" name="Patentamiento" id="Patentamiento" class="camporFormularioChico" maxlength="128" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;"  value="<?=$Patentamiento?>" />

																	</td>
																</tr>
															</table>
														</td>   
                                                    </tr>   
													<tr>
														<td colspan="2">&nbsp;</td>
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
    	                            <input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'modelos.php<?=$strParams?>';" value="Cancelar" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>