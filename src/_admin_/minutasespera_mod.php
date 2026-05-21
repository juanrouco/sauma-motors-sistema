<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinutaEspera			= intval($_REQUEST['IdMinutaEspera']);
$IdModelo				= intval($_REQUEST['IdModelo']);
$VehiculoModelo			= strval($_REQUEST['VehiculoModelo']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Cliente				= strval($_REQUEST['Cliente']);
$IdUsuario				= intval($_REQUEST['IdUsuario']);
$Usuario				= strval($_REQUEST['Usuario']);
$FechaMinuta			= strval($_REQUEST['FechaMinuta']);
$NumeroPedido			= strval($_REQUEST['NumeroPedido']);
$NumeroVin				= strval($_REQUEST['NumeroVin']);
$Anticipo				= floatval($_REQUEST['Anticipo']);
$Color					= strval($_REQUEST['Color']);
$Color2					= strval($_REQUEST['Color2']);
$Color3					= strval($_REQUEST['Color3']);
$ColorCodigo			= strval($_REQUEST['ColorCodigo']);
$ColorCodigo2			= strval($_REQUEST['ColorCodigo2']);
$ColorCodigo3			= strval($_REQUEST['ColorCodigo3']);
$IdColor				= intval($_REQUEST['IdColor']);
$IdColor2				= intval($_REQUEST['IdColor2']);
$IdColor3				= intval($_REQUEST['IdColor3']);
$Financiacion			= intval($_REQUEST['Financiacion']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);
$FinanciacionCuotas		= intval($_REQUEST['FinanciacionCuotas']);
$FinanciacionAcreedor	= strval($_REQUEST['FinanciacionAcreedor']);
$FinanciacionValorCuota	= floatval($_REQUEST['FinanciacionValorCuota']);
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
$UsadoArreglos			= floatval($_REQUEST['UsadoArreglos']);
$UsadoObservaciones		= strval($_REQUEST['UsadoObservaciones']);
$UsadoInfo				= floatval($_REQUEST['UsadoInfo']);

$UsadoMarca2			= strval($_REQUEST['UsadoMarca2']);
$UsadoMarcaCodigo2		= strval($_REQUEST['UsadoMarcaCodigo2']);
$UsadoIdMarca2			= intval($_REQUEST['UsadoIdMarca2']);
$UsadoModelo2			= strval($_REQUEST['UsadoModelo2']);
$UsadoDominio2			= strval($_REQUEST['UsadoDominio2']);
$UsadoColor2			= strval($_REQUEST['UsadoColor2']);
$UsadoColorCodigo2		= strval($_REQUEST['UsadoColorCodigo2']);
$UsadoIdColor2			= intval($_REQUEST['UsadoIdColor2']);
$UsadoModeloAnio2		= intval($_REQUEST['UsadoModeloAnio2']);
$UsadoKilometraje2		= floatval($_REQUEST['UsadoKilometraje2']);
$UsadoValuacion2		= floatval($_REQUEST['UsadoValuacion2']);
$UsadoArreglos2			= floatval($_REQUEST['UsadoArreglos2']);
$UsadoObservaciones2	= strval($_REQUEST['UsadoObservaciones2']);
$UsadoInfo2				= floatval($_REQUEST['UsadoInfo2']);

$GastosFlete			= floatval($_REQUEST['GastosFlete']);
$GastosPatentamiento	= floatval($_REQUEST['GastosPatentamiento']);
$GastosOtorgamiento		= floatval($_REQUEST['GastosOtorgamiento']);
$GastosPrenda			= floatval($_REQUEST['GastosPrenda']);
$Circular				= floatval($_REQUEST['Circular']);
$Precio					= floatval($_REQUEST['Precio']);
$DepositoGarantia		= floatval($_REQUEST['DepositoGarantia']);
$Rentas					= floatval($_REQUEST['Rentas']);
$IdAcreedor				= intval($_REQUEST['IdAcreedor']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$EliminarUsado1			= intval($_REQUEST['EliminarUsado1']);
$EliminarUsado2			= intval($_REQUEST['EliminarUsado2']);
$FechaVencimiento		= strval($_REQUEST['FechaVencimiento']);
$FechaRetiro			= strval($_REQUEST['FechaRetiro']);
$Submit					= (isset($_REQUEST['Submitted']));

$MostrarEliminarUsado = true;

/* declaracion de variables */
$err			= 0;
$oMinutaEspera 	= new MinutaEspera();
$oMinutasEspera	= new MinutasEspera();
$oUsado 		= new Usado();
$oUsados		= new Usados();
$oModelos		= new Modelos();
$oColores		= new Colores();
$oAcreedores	= new Acreedores();
$oMarcas		= new Marcas();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinutaEspera = $oMinutasEspera->GetById($IdMinutaEspera))
{	
	header("Location: minutasespera.php" . $strParams);
	exit();
}

if ($oMinutaEspera->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinutaEspera($oMinutaEspera->IdMinutaEspera);
	$oUsado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$arrAcreedores = $oAcreedores->GetAll();

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdModelo == '')
		$err |= 1;
	if ($IdCliente == '')
		$err |= 2;
	if ($IdUsuario == '')
		$err |= 4;
	if ($FechaMinuta == '')
		$err |= 8;
	if ($IdColor == '')
		$err |= 16;
	if (($Financiacion) && ($FinanciacionCapital == ''))
		$err |= 32;
	if (($Financiacion) && ($IdAcreedor == ''))
		$err |= 16384;
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
			
		if ($UsadoIdMarca2 != '')
		{
			if ($UsadoModelo2 == '')
				$err |= 2048;
			if ($UsadoModeloAnio2 == '')
				$err |= 4096;
			if ($UsadoValuacion2 == '')
				$err |= 8192;
		}
	}
	if ($Precio == '')
		$err |= 1024;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$Precio					= str_replace(",", ".", $Precio);
		$FinanciacionCapital	= str_replace(",", ".", $FinanciacionCapital);
		$UsadoValuacion			= str_replace(",", ".", $UsadoValuacion);
		$UsadoValuacion2		= str_replace(",", ".", $UsadoValuacion2);
		$GastosFlete			= str_replace(",", ".", $GastosFlete);
		$GastosPatentamiento	= str_replace(",", ".", $GastosPatentamiento);
		$GastosOtorgamiento		= str_replace(",", ".", $GastosOtorgamiento);
		$GastosPrenda			= str_replace(",", ".", $GastosPrenda);
		$Circular				= str_replace(",", ".", $Circular);
		$Anticipo				= str_replace(",", ".", $Anticipo);
		$DepositoGarantia		= str_replace(",", ".", $DepositoGarantia);
		$Rentas					= str_replace(",", ".", $Rentas);
		
		/* si no requiere financiacion */
		if (!($Financiacion)) $FinanciacionCapital = 0;
		if (!($Financiacion)) $IdAcreedor = '';
		
		if ($Financiacion)
		{
			$oMinutaEspera->Financia				= $Financiacion;
			$oMinutaEspera->FinanciacionCapital		= $FinanciacionCapital;
			$oMinutaEspera->FinanciacionCuotas		= $FinanciacionCuotas;
			$oMinutaEspera->IdAcreedor				= $IdAcreedor;
		}
		else
		{
			$oMinutaEspera->Financia				= $Financiacion;
			$oMinutaEspera->FinanciacionCapital		= '';
			$oMinutaEspera->FinanciacionCuotas		= '';
			$oMinutaEspera->IdAcreedor				= '';
		}
		
		$oMinutaEspera->IdModelo			= $IdModelo;
		$oMinutaEspera->IdUsuario			= $IdUsuario;
		$oMinutaEspera->IdCliente			= $IdCliente;
		$oMinutaEspera->IdColor				= $IdColor;
		$oMinutaEspera->IdColor2			= $IdColor2;
		$oMinutaEspera->IdColor3			= $IdColor3;
		$oMinutaEspera->FechaMinuta			= $FechaMinuta;
		$oMinutaEspera->IdEstado			= 1;
		$oMinutaEspera->Anticipo			= $Anticipo;
		$oMinutaEspera->GastosFlete			= $GastosFlete;
		$oMinutaEspera->GastosPatentamiento	= $GastosPatentamiento;
		$oMinutaEspera->GastosOtorgamiento	= $GastosOtorgamiento;
		$oMinutaEspera->GastosPrenda		= $GastosPrenda;
		$oMinutaEspera->Circular			= $Circular;
		$oMinutaEspera->Precio				= $Precio;
		$oMinutaEspera->DepositoGarantia	= $DepositoGarantia;
		$oMinutaEspera->Rentas				= $Rentas;
		$oMinutaEspera->IdAcreedor			= $IdAcreedor;
		$oMinutaEspera->Observaciones		= $Observaciones;
		$oMinutaEspera->EntregaUsado		= $EntregaUsado;
		$oMinutaEspera->FechaVencimiento	= $FechaVencimiento;
		$oMinutaEspera->FechaRetiro			= $FechaRetiro;
				
		$oMinutaEspera = $oMinutasEspera->Update($oMinutaEspera);
		
		if ($oMinutaEspera && $EntregaUsado)
		{
			/* si existe lo modificamos, por el contrario, lo creamos */
			if ($oUsado)
			{
				if (!$EliminarUsado1)
				{
					$oUsado->IdMarca		= $UsadoIdMarca;
					$oUsado->IdColor		= $UsadoIdColor;
					$oUsado->Modelo			= $UsadoModelo;
					$oUsado->ModeloAnio		= $UsadoModeloAnio;
					$oUsado->Kilometraje	= $UsadoKilometraje;
					$oUsado->Valuacion		= $UsadoValuacion;
					$oUsado->Dominio		= $UsadoDominio;
					$oUsado->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
					$oUsado->Arreglos		= $UsadoArreglos;
					$oUsado->Observaciones	= $UsadoObservaciones;
					$oUsado->Info			= $UsadoInfo;
					
					/* actualizamos el registro */
					$oUsado = $oUsados->Update($oUsado);
				}
				else
					$oUsados->Delete($oUsado->IdUsado);
					
				if ($oUsado2)
				{
					if (!$EliminarUsado2)
					{
						$oUsado2->IdMarca		= $UsadoIdMarca2;
						$oUsado2->IdColor		= $UsadoIdColor2;
						$oUsado2->Modelo			= $UsadoModelo2;
						$oUsado2->ModeloAnio		= $UsadoModeloAnio2;
						$oUsado2->Kilometraje	= $UsadoKilometraje2;
						$oUsado2->Valuacion		= $UsadoValuacion2;
						$oUsado2->Dominio		= $UsadoDominio2;
						$oUsado2->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
						$oUsado2->Info			= $UsadoInfo2;
				
						/* actualizamos el registro */
						$oUsado = $oUsados->Update($oUsado2);
					}
					else
						$oUsados->Delete($oUsado2->IdUsado);
				}
				else
				{
					if ($UsadoIdMarca2)
					{
						$oUsado2 = new Usado();
						$oUsado2->IdMarca		= $UsadoIdMarca2;
						$oUsado2->IdColor		= $UsadoIdColor2;
						$oUsado2->Modelo			= $UsadoModelo2;
						$oUsado2->ModeloAnio		= $UsadoModeloAnio2;
						$oUsado2->Kilometraje	= $UsadoKilometraje2;
						$oUsado2->Valuacion		= $UsadoValuacion2;
						$oUsado2->Dominio		= $UsadoDominio2;
						$oUsado2->IdUbicacion	= Ubicacion::Transito;
						$oUsado2->IdEstado		= EstadoUnidad::Stock;
						$oUsado2->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
						$oUsado2->Info			= $UsadoInfo2;

						/* creamos el registro */		
						$oUsado = $oUsados->Create($oUsado2);
					}
				}
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
				$oUsado->IdUbicacion	= Ubicacion::Transito;
				$oUsado->IdEstado		= EstadoUnidad::Stock;
				$oUsado->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
				$oUsado->Arreglos		= $UsadoArreglos;
				$oUsado->Observaciones	= $UsadoObservaciones;
				$oUsado->Info			= $UsadoInfo;

				/* creamos el registro */		
				$oUsado = $oUsados->Create($oUsado);
				
				if ($oUsado2)
				{
					if (!$EliminarUsado2)
					{
						$oUsado2->IdMarca		= $UsadoIdMarca2;
						$oUsado2->IdColor		= $UsadoIdColor2;
						$oUsado2->Modelo			= $UsadoModelo2;
						$oUsado2->ModeloAnio		= $UsadoModeloAnio2;
						$oUsado2->Kilometraje	= $UsadoKilometraje2;
						$oUsado2->Valuacion		= $UsadoValuacion2;
						$oUsado2->Dominio		= $UsadoDominio2;
						$oUsado2->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
						$oUsado2->Info			= $UsadoInfo2;
				
						/* actualizamos el registro */
						$oUsado = $oUsados->Update($oUsado2);
					}
					else
						$oUsados->Delete($oUsado2->IdUsado);
				}
				else
				{
					if ($UsadoIdMarca2)
					{
						$oUsado2 = new Usado();
						$oUsado2->IdMarca		= $UsadoIdMarca2;
						$oUsado2->IdColor		= $UsadoIdColor2;
						$oUsado2->Modelo			= $UsadoModelo2;
						$oUsado2->ModeloAnio		= $UsadoModeloAnio2;
						$oUsado2->Kilometraje	= $UsadoKilometraje2;
						$oUsado2->Valuacion		= $UsadoValuacion2;
						$oUsado2->Dominio		= $UsadoDominio2;
						$oUsado2->IdUbicacion	= Ubicacion::Transito;
						$oUsado2->IdEstado		= EstadoUnidad::Stock;
						$oUsado2->IdMinutaEspera		= $oMinutaEspera->IdMinutaEspera;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
						$oUsado2->Info			= $UsadoInfo2;

						/* creamos el registro */		
						$oUsado = $oUsados->Create($oUsado2);
					}
				}
			}
		}
		else
		{
			/* si no entrega usado, verificamos que no queda basura en la base de datos */
			if ($oUsado && !$EntregaUsado) 
			{
				$oUsados->Delete($oUsado->IdUsado);
				if ($oUsado2)
					$oUsados->Delete($oUsado2->IdUsado);
			}
		}

		header("Location: minutasespera.php" . $strParams);
		exit();
	}
}
else
{
	/* determinamos como fecha de compra a la fecha de ayer */
	$Financiacion			= $oMinutaEspera->Financia;
	$FinanciacionCapital	= $oMinutaEspera->FinanciacionCapital;
	$FinanciacionCuotas		= $oMinutaEspera->FinanciacionCuotas;
	$IdAcreedor				= $oMinutaEspera->IdAcreedor;
	$FinanciacionValorCuota	= $oMinutaEspera->FinanciacionValorCuota;
	$EntregaUsado			= $oMinutaEspera->EntregaUsado;
	$GastosFlete			= $oMinutaEspera->GastosFlete;
	$GastosPatentamiento	= $oMinutaEspera->GastosPatentamiento;
	$GastosOtorgamiento		= $oMinutaEspera->GastosOtorgamiento;
	$GastosPrenda			= $oMinutaEspera->GastosPrenda;
	$Circular				= $oMinutaEspera->Circular;
	$Precio					= $oMinutaEspera->Precio;
	$DepositoGarantia		= $oMinutaEspera->DepositoGarantia;
	$Rentas					= $oMinutaEspera->Rentas;
	
	$oUsadoMarca 	= $oMarcas->GetById($oUsado->IdMarca);
	$oUsadoColor 	= $oColores->GetById($oUsado->IdColor);
	$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
	$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);
	
	$FechaMinuta 			= CambiarFecha($oMinutaEspera->FechaMinuta);
	$IdModelo 				= $oMinutaEspera->IdModelo;
	$IdUsuario 				= $oMinutaEspera->IdUsuario;
	$IdCliente 				= $oMinutaEspera->IdCliente;
	$Anticipo 				= $oMinutaEspera->Anticipo;
	$IdColor 				= $oMinutaEspera->IdColor;
	$IdColor2 				= $oMinutaEspera->IdColor2;
	$IdColor3				= $oMinutaEspera->IdColor3;
	$Observaciones 			= $oMinutaEspera->Observaciones;
	$FechaVencimiento		= CambiarFecha($oMinutaEspera->FechaVencimiento);
	$FechaRetiro			= CambiarFecha($oMinutaEspera->FechaRetiro);
	
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
	$UsadoArreglos		= $oUsado->Arreglos;
	$UsadoObservaciones	= $oUsado->Observaciones;
	
	$UsadoIdMarca2		= $oUsadoMarca2->IdMarca;
	$UsadoMarca2		= $oUsadoMarca2->Nombre;
	$UsadoMarcaCodigo2	= $oUsadoMarca2->Codigo;
	$UsadoIdColor2		= $oUsadoColor2->IdColor;
	$UsadoColor2		= $oUsadoColor2->Nombre;
	$UsadoColorCodigo2	= $oUsadoColor2->Codigo;
	$UsadoModelo2		= $oUsado2->Modelo;
	$UsadoModeloAnio2	= $oUsado2->ModeloAnio;
	$UsadoKilometraje2	= $oUsado2->Kilometraje;
	$UsadoValuacion2	= $oUsado2->Valuacion;
	$UsadoDominio2		= $oUsado2->Dominio;
	$UsadoArreglos2		= $oUsado2->Arreglos;
	$UsadoObservaciones2= $oUsado2->Observaciones;
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
		//FilterUsuario(oCliente.IdVendedor, '');
	}
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

function FilterUsadoMarca2(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('UsadoMarcaCodigo2').value 	= '';
		Get('UsadoMarca2').value 		= '';
		Get('UsadoIdMarca2').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('UsadoMarcaCodigo2').value 	= oMarca.Codigo;
	Get('UsadoMarca2').value 		= oMarca.Nombre;
	Get('UsadoIdMarca2').value 		= oMarca.IdMarca;
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

function FilterUsadoColor2(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('UsadoColorCodigo2').value 	= '';
		Get('UsadoColor2').value 		= '';
		Get('UsadoIdColor2').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('UsadoColorCodigo2').value 	= oColor.Codigo;
	Get('UsadoColor2').value 		= oColor.Nombre;
	Get('UsadoIdColor2').value 		= oColor.IdColor;
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
	HideSection('trPlazoPrenda');
	HideSection('trPlazoPrendaError');
	HideSection('trQuebranto');
	HideSection('trQuebrantoError');
	HideSection('trAcreedor');
	HideSection('trAcreedorError');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trFinanciacionCapital');
		ShowSection('trFinanciacionCapitalError');
		ShowSection('trPlazoPrenda');
		ShowSection('trPlazoPrendaError');
		ShowSection('trQuebranto');
		ShowSection('trQuebrantoError');
		ShowSection('trAcreedor');
		ShowSection('trAcreedorError');
	}
}

function FilterModelo(IdModelo, Nombre)
{
	if ((IdModelo == ''))
	{
		Get('IdModelo').value 	= '';
		Get('Modelo').value 	= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;

	Get('IdModelo').value 	= oModelo.IdModelo;
	Get('VehiculoModelo').value 	= oModelo.DenominacionComercial;	
}

function FilterColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo').value 	= '';
		Get('Color').value 		= '';
		Get('IdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo').value 	= oColor.Codigo;
	Get('Color').value 			= oColor.Nombre;
	Get('IdColor').value 		= oColor.IdColor;
}
function FilterColor2(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo2').value 	= '';
		Get('Color2').value 		= '';
		Get('IdColor2').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo2').value 	= oColor.Codigo;
	Get('Color2').value 			= oColor.Nombre;
	Get('IdColor2').value 		= oColor.IdColor;
}
function FilterColor3(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo3').value 	= '';
		Get('Color3').value 		= '';
		Get('IdColor3').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo3').value 	= oColor.Codigo;
	Get('Color3').value 			= oColor.Nombre;
	Get('IdColor3').value 		= oColor.IdColor;
}


function VerificarCliente()
{
	var IdCliente = Get('IdCliente').value;

	HideSection('trModificarCliente');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarCliente');
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


$j(document).ready(function() {
	$j('#calcular-cuotas').click(function(e) {
		e.preventDefault();
		var IdAcreedor = $j('#IdAcreedor').val();
		if (!IdAcreedor)
		{
			alert('Seleccione el acreedor');
			return;
		}
		var FinanciacionCapital = parseFloat($j('#FinanciacionCapital').val());
		if (!FinanciacionCapital)
		{
			alert('Ingrese el capital a financiar');
			return;
		}
		var PlazoPrenda = parseInt($j('#FinanciacionCuotas').val());
		if (!PlazoPrenda)
		{
			alert('Ingrese el plazo');
			return;
		}
		$j.ajax('ssi_cuotas.php?IdAcreedor=' + IdAcreedor + '&FinanciacionCapital=' + FinanciacionCapital + '&Cuotas=' + PlazoPrenda, {
			success: function (data, textStatus, jqXHR) {
				$j('#cuotas-container').html(data);
			}
		});
	});
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas en Espera - Modificar</span></td>
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
				<?php include('ssi_minutaespera_cuerpo.php'); ?>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

<script language="javascript">
FilterModelo('<?=$IdModelo?>', '');
FilterColor('<?=$IdColor?>');
FilterColor2('<?=$IdColor2?>');
FilterColor3('<?=$IdColor3?>');
FilterCliente('<?=$IdCliente?>');
FilterUsuario('<?=$IdUsuario?>');
VerificarEntregaUsado('<?=$EntregaUsado?>');
VerificarFinanciacion('<?=$Financiacion?>');
<?php
if ($UsadoIdMarca)
{
?>
FilterUsadoMarca('<?=$UsadoIdMarca?>', '');
<?php
}
?>
</script>

</body>
</html>