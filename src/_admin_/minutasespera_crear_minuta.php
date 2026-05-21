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
$UsadoArreglos			= floatval($_REQUEST['UsadoArreglos']);
$UsadoObservaciones		= strval($_REQUEST['UsadoObservaciones']);

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

$PlazoPrenda			= intval($_REQUEST['PlazoPrenda']);
$PedidoAccesorios		= intval($_REQUEST['PedidoAccesorios']);
$Accesorios				= strval($_REQUEST['Accesorios']);
$arrDetalles 			= $_REQUEST['Detalle'];
$arrImportes 			= $_REQUEST['Importe'];
$IdAcreedor				= intval($_REQUEST['IdAcreedor']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$FechaVencimiento		= strval($_REQUEST['FechaVencimiento']);
$FechaRetiro			= strval($_REQUEST['FechaRetiro']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oMinuta 		= new Minuta();
$oMinutas		= new Minutas();
$oMinutasEspera	= new MinutasEspera();
$oUnidades		= new Unidades();
$oUsado 		= new Usado();
$oUsados		= new Usados();
$oModelos		= new Modelos();
$oClientes		= new Clientes();
$oUsuarios		= new Usuarios();
$oMarcas		= new Marcas();
$oAcreedores	= new Acreedores();
$oMarcas		= new Marcas();
$oColores		= new Colores();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro de la minuta espera */
if (!$oMinutaEspera = $oMinutasEspera->GetById($IdMinutaEspera))
{	
	header("Location: minutasespera.php" . $strParams);
	exit();
}

$arrAcreedores = $oAcreedores->GetAll();

/* obtenemos los datos del usado entregado en caso de que exista */
if ($oMinutaEspera->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinutaEspera($oMinutaEspera->IdMinutaEspera);
	$oUsado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

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
		$UsadoValuacion2		= str_replace(",", ".", $UsadoValuacion2);
		$DepositoGarantia  		= str_replace(",", ".", $DepositoGarantia);
		$Rentas			  		= str_replace(",", ".", $Rentas);

		/* si no requiere financiacion */
		if (!($Financiacion)) $FinanciacionCapital = 0;
		if (!($Financiacion)) $IdAcreedor = '';

		/* si entrega un auto usado como parte de pago... */
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
		$oMinuta->Rentas				= $Rentas;
		$oMinuta->Condominio			= $Condominio;
		$oMinuta->EntregaUsado			= $EntregaUsado;
		//$oMinuta->IdUsado				= ($oUsado) ? $oUsado->IdUsado : '';
		$oMinuta->PlazoPrenda			= $PlazoPrenda;
		$oMinuta->IdClienteReventa		= $IdClienteReventa;
		$oMinuta->IdAcreedor			= $IdAcreedor;
		$oMinuta->Observaciones			= $Observaciones;
		$oMinuta->FechaVencimiento		= $FechaVencimiento;
		$oMinuta->FechaRetiro			= $FechaRetiro;

		if ($oMinutaExistente = $oMinutas->GetById($IdUnidad))
		{
			$oMinuta->IdMinuta = $IdUnidad;
			if ($oMinuta = $oMinutas->Update($oMinuta))
			{
				/* obtenemos los datos de la unidad */
				$oUnidad = $oUnidades->GetById($IdUnidad);
				
				/* actualizamos el estado del vehiculo */
				if ($oUnidad->IdEstado == EstadoUnidad::Stock)
					$oUnidad->IdEstado = EstadoUnidad::Reservado;
				elseif ($oUnidad->IdEstado == EstadoUnidad::PreVenta)
					$oUnidad->IdEstado = EstadoUnidad::PreVentaReservado;
				
				$oUnidades->Update($oUnidad);
			}
		}
		else
		{
			if ($oMinuta = $oMinutas->Create($oMinuta))
			{
				/* obtenemos los datos de la unidad */
				$oUnidad = $oUnidades->GetById($IdUnidad);
				
				/* actualizamos el estado del vehiculo */
				if ($oUnidad->IdEstado == EstadoUnidad::Stock)
					$oUnidad->IdEstado = EstadoUnidad::Reservado;
				elseif ($oUnidad->IdEstado == EstadoUnidad::PreVenta)
					$oUnidad->IdEstado = EstadoUnidad::PreVentaReservado;
				
				$oUnidades->Update($oUnidad);
			}
		}
		
		if ($oMinuta && $EntregaUsado)
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
					$oUsado->IdMinuta		= $oMinuta->IdMinuta;
					$oUsado->IdEstado		= EstadoUnidad::Stock;
					$oUsado->IdMinutaEspera	= '';
					$oUsado->Arreglos		= $UsadoArreglos;
					$oUsado->Observaciones	= $UsadoObservaciones;
					
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
						$oUsado2->IdMinuta		= $oMinuta->IdMinuta;
						$oUsado2->IdEstado		= EstadoUnidad::Stock;
						$oUsado2->IdMinutaEspera= '';
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
				
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
						$oUsado2->IdMinuta		= $oMinuta->IdMinuta;
						$oUsado2->IdMinutaEspera= '';
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;

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
				$oUsado->IdMinuta		= $oMinuta->IdMinuta;
				$oUsado->IdMinutaEspera	= '';
				$oUsado->Arreglos		= $UsadoArreglos;
				$oUsado->Observaciones	= $UsadoObservaciones;

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
						$oUsado2->IdMinuta		= $oMinuta->IdMinuta;
						$oUsado2->IdMinutaEspera= '';
						$oUsado2->IdEstado		= EstadoUnidad::Stock;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;
				
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
						$oUsado2->IdMinuta		= $oMinuta->IdMinuta;
						$oUsado2->IdMinutaEspera= '';
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones	= $UsadoObservaciones2;

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

		$oMinutasEspera->Delete($oMinutaEspera->IdMinutaEspera);
		
		header("Location: minutas.php" . $strParams);
		exit();
	}
}
else
{	
	$oUnidad 				= $oUnidades->GetById($IdUnidad);
	$oModelo 				= $oModelos->GetById($oUnidad->IdModelo);
	$oCliente				= $oClientes->GetById($oMinutaEspera->IdCliente);
	$oUsuario				= $oUsuarios->GetById($oMinutaEspera->IdUsuario);
	
	$IdUnidad				= $oUnidad->IdUnidad;
	$IdCliente				= $oCliente->IdCliente;
	$Cliente				= $oCliente->RazonSocial;
	$IdUsuario				= $oUsuario->IdUsuario;
	$Usuario				= $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
	$Condominio 			= 0;
	
	$Financiacion 			= $oMinutaEspera->Financia;
	$EntregaUsado 			= $oMinutaEspera->EntregaUsado;
	$VehiculoModelo 		= $oModelo->DenominacionModelo;
	$PrecioVenta			= $oModelo->VentaPrecio;
	$GastosFlete			= $oModelo->VentaGastosFlete;
	$GastosPatentamiento	= $oModelo->VentaGastosPatentamiento;
	$GastosOtorgamiento		= (($oModelo->VentaPrecio * $oModelo->Otorgamiento) / 100);
	$GastosPrenda			= (($oModelo->VentaPrecio * $oModelo->Prenda) / 100);
	$Circular				= $oModelo->ReventaBonificacion;
	
	$PrecioVenta			= $oMinutaEspera->Precio;
	$FinanciacionCapital	= $oMinutaEspera->FinanciacionCapital;
	
	$oUsadoMarca 	= $oMarcas->GetById($oUsado->IdMarca);
	$oUsadoColor 	= $oColores->GetById($oUsado->IdColor);
	$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
	$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);
	
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
	
	$PlazoPrenda			= $oMinutaEspera->FinanciacionCuotas;
	
	$GastosFlete			= $oMinutaEspera->GastosFlete;
	$GastosPatentamiento	= $oMinutaEspera->GastosPatentamiento;
	$GastosOtorgamiento		= $oMinutaEspera->GastosOtorgamiento;
	$GastosPrenda			= $oMinutaEspera->GastosPrenda;
	$Circular				= $oMinutaEspera->Circular;
	$Anticipo				= $oMinutaEspera->Anticipo;
	$DepositoGarantia		= $oMinutaEspera->DepositoGarantia;
	$Rentas					= $oMinutaEspera->Rentas;
	$IdAcreedor				= $oMinutaEspera->IdAcreedor;
	$Observaciones			= $oMinutaEspera->Observaciones;
	$FechaVencimiento		= CambiarFecha($oMinutaEspera->FechaVencimiento);
	$FechaRetiro			= CambiarFecha($oMinutaEspera->FechaRetiro);
	
		
	/* determinamos como fecha de compra a la fecha de ayer */
	//$FechaMinuta = date("Y-m-d", strtotime(date("Y-m-d") . " - 1 days"));
	$FechaMinuta = CambiarFecha($oMinutaEspera->FechaMinuta);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript" src="../js/minutas.js"></script>

<script type="text/javascript">
	function VerificarPedidoAccesorios(value)
	{
		HideSection('trPedidoAccesorioTitulo');
		HideSection('trPedidoAccesorioComentarios');
		HideSection('trPedidoAccesorio');
		HideSection('trPedidoAccesorioLink');
		
		if ((value == '1') || (value == true))
		{
			ShowSection('trPedidoAccesorioTitulo');
			ShowSection('trPedidoAccesorioComentarios');
			ShowSection('trPedidoAccesorio');
			ShowSection('trPedidoAccesorioLink');
		}
	}

	function AgregarItem() {
		var html = '<tr class="bordeGrisFondo">';
			html+= '	<td height="30"><div id="margen"><input type="text" id="Detalle[]" name="Detalle[]" class="camporFormularioSimple" /></div></td>';
			html+= '	<td width="10">&nbsp;</td>';
			html+= '	<td width="200"><div id="margen" align="center">$<input type="text" id="Importe[]" name="Importe[]" class="camporFormularioChico" /></div></td>';
			html+= '	<td width="75"><div id="margen" align="center"><a href="#" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>';
			html+= '</tr>';
		
			var element = $j(html);
			element.find('#quitar-item').click(function(e) {
				e.preventDefault();
				element.remove();
			});
			$j('#contenedor-items').append(element);
	}
	
	function QuitarItem(id) {
		$j('#row_' + id).remove();
	}
	
	$j(document).ready(function() {
		<?php
		if (!$arrDetalles || count($arrDetalles) == 0)
		{
		?>
		AgregarItem();
		<?php
		}
		?>
		$j('#agregar-item').click(function(e) {
			e.preventDefault();
			
			AgregarItem();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas - Agregar</span></td>
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
				<?php include('ssi_minuta_cuerpo.php'); ?>
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
VerificarPedidoAccesorios('<?=$PedidoAccesorios?>');
</script>

</body>
</html>