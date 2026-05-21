<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENTUS_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta				= intval($_REQUEST['IdMinuta']);
$IdUsado				= intval($_REQUEST['IdUsado']);
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
$Gastos					= floatval($_REQUEST['Gastos']);
$GastosOtorgamiento		= floatval($_REQUEST['GastosOtorgamiento']);
$DepositoGarantia		= floatval($_REQUEST['DepositoGarantia']);
$GastosPrenda			= floatval($_REQUEST['GastosPrenda']);
$Anticipo				= floatval($_REQUEST['Anticipo']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);
$Financiacion			= intval($_REQUEST['Financiacion']);
$Condominio				= intval($_REQUEST['Condominio']);
$PlazoPrenda			= intval($_REQUEST['PlazoPrenda']);
$EntregaUsado			= intval($_REQUEST['EntregaUsado']);
$EliminarUsado1			= intval($_REQUEST['EliminarUsado1']);
$EliminarUsado2			= intval($_REQUEST['EliminarUsado2']);
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
$IdAcreedor				= intval($_REQUEST['IdAcreedor']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$FechaVencimiento		= strval($_REQUEST['FechaVencimiento']);
$FechaRetiro			= strval($_REQUEST['FechaRetiro']);
$CedulaAzul				= intval($_REQUEST['CedulaAzul']);

$arrIdAcreedor 			= $_REQUEST['FinanciacionIdAcreedor'];
$arrFinanciacionImportes= $_REQUEST['FinanciacionImporte'];
$arrFinanciacionCuotas	= $_REQUEST['FinanciacionCuota'];

$PedidoAccesorios		= intval($_REQUEST['PedidoAccesorios']);
$Accesorios				= strval($_REQUEST['Accesorios']);
$arrDetalles 			= $_REQUEST['Detalle'];
$arrImportes 			= $_REQUEST['Importe'];
$arrIdArticulo 			= $_REQUEST['IdArticulo'];

$Submit					= (isset($_REQUEST['Submitted']));

$MostrarEliminarUsado = true;

/* declaracion de variables */
$err		= 0;
$oMinutasUsados				= new MinutasUsados();
$oUsados					= new Usados();
$oClientes					= new Clientes();
$oUsuarios					= new Usuarios();
$oMarcas					= new Marcas();
$oColores					= new Colores();
$oAcreedores				= new Acreedores();
$oMinutasFinanciacion 		= new MinutasUsadosFinanciacion();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro a modificar */
if (!$oMinutaUsado = $oMinutasUsados->GetById($IdMinuta))
{	
	header("Location: minutasusados.php" . $strParams);
	exit();
}

$oPedidoAccesorios = $oPedidosAccesorios->GetByMinutaUsado($oMinutaUsado);
$arrMinutasFinanciacion = $oMinutasFinanciacion->GetByMinuta($oMinutaUsado);


/* obtenemos los datos del usado entregado en caso de que exista */
if ($oMinutaUsado->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinutaUsado->IdMinuta);
	$oUsadoTomado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$arrAcreedores = $oAcreedores->GetAll();

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdUsado == '')
		$err |= 1;
	if ($IdCliente == '')
		$err |= 2;
	else
	{
		$oCliente = $oClientes->GetById($IdCliente);
		if ($oCliente->Telefono == '' || $oCliente->Email == '')
				$err |= 65536;
	}
	if ($IdUsuario == '')
		$err |= 4;
	if ($FechaMinuta == '')
		$err |= 8;
	if ($PrecioVenta == '')
		$err |= 16;
	/*if (($Financiacion) && ($FinanciacionCapital == ''))
		$err |= 32;
	if (($Financiacion) && ($IdAcreedor == ''))
		$err |= 16384;*/
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
		$Gastos					= str_replace(",", ".", $Gastos);
		$GastosOtorgamiento		= str_replace(",", ".", $GastosOtorgamiento);
		$GastosPrenda			= str_replace(",", ".", $GastosPrenda);
		$Anticipo				= str_replace(",", ".", $Anticipo);
		$FinanciacionCapital	= str_replace(",", ".", $FinanciacionCapital);
		$UsadoValuacion			= str_replace(",", ".", $UsadoValuacion);
		$UsadoValuacion2		= str_replace(",", ".", $UsadoValuacion2);
		$DepositoGarantia		= str_replace(",", ".", $DepositoGarantia);

		/* si no requiere financiacion */
		if (!($Financiacion)) $FinanciacionCapital = 0;
		
		$oMinutaUsado->IdUsado			= $IdUsado;
		$oMinutaUsado->IdUsuario				= $IdUsuario;
		$oMinutaUsado->IdCliente				= $IdCliente;
		if ($Condominio)
			$oMinutaUsado->IdClienteCondominio	= $IdClienteCondominio;
		$oMinutaUsado->FechaMinuta			= $FechaMinuta;
		$oMinutaUsado->PrecioVenta			= $PrecioVenta;
		$oMinutaUsado->Gastos				= $Gastos;
		$oMinutaUsado->GastosOtorgamiento	= $GastosOtorgamiento;
		$oMinutaUsado->DepositoGarantia		= $DepositoGarantia;
		$oMinutaUsado->GastosPrenda			= $GastosPrenda;
		$oMinutaUsado->Anticipo				= $Anticipo;
		$oMinutaUsado->FinanciacionCapital	= $FinanciacionCapital;
		$oMinutaUsado->Condominio			= $Condominio;
		$oMinutaUsado->PlazoPrenda			= $PlazoPrenda;
		$oMinutaUsado->IdClienteReventa		= $IdClienteReventa;
		$oMinutaUsado->EntregaUsado			= $EntregaUsado;
		$oMinutaUsado->IdUsadoTomado		= ($oUsadoTomado) ? $oUsadoTomado->IdUsado : '';
		$oMinutaUsado->IdAcreedor			= $IdAcreedor;
		$oMinutaUsado->Observaciones		= $Observaciones;
		$oMinutaUsado->FechaVencimiento		= $FechaVencimiento;
		$oMinutaUsado->FechaRetiro			= $FechaRetiro;
		$oMinutaUsado->CedulaAzul			= $CedulaAzul;

		$oMinutaUsado = $oMinutasUsados->Update($oMinutaUsado);
		
		/* si entrega un auto usado como parte de pago... */
		if ($oMinutaUsado && $EntregaUsado)
		{
			/* si existe lo modificamos, por el contrario, lo creamos */
			if ($oUsadoTomado)
			{
				if (!$EliminarUsado1)
				{
					$oUsadoTomado->IdMarca		= $UsadoIdMarca;
					$oUsadoTomado->IdColor		= $UsadoIdColor;
					$oUsadoTomado->Modelo			= $UsadoModelo;
					$oUsadoTomado->ModeloAnio		= $UsadoModeloAnio;
					$oUsadoTomado->Kilometraje	= $UsadoKilometraje;
					$oUsadoTomado->Valuacion		= $UsadoValuacion;
					$oUsadoTomado->Dominio		= $UsadoDominio;
					$oUsadoTomado->Arreglos		= $UsadoArreglos;
					$oUsadoTomado->Observaciones		= $UsadoObservaciones;
					$oUsadoTomado->Info			= $UsadoInfo;
			
					/* actualizamos el registro */
					$oUsadoTomado = $oUsados->Update($oUsadoTomado);
				}
				else
					$oUsados->Delete($oUsadoTomado->IdUsado);
					
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
						$oUsado2->IdMinutaUsado		= $oMinutaUsado->IdMinuta;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones		= $UsadoObservaciones2;
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
						$oUsado2->IdMinutaUsado		= $oMinutaUsado->IdMinuta;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones		= $UsadoObservaciones2;
						$oUsado2->Info		= $UsadoInfo2;

						/* creamos el registro */		
						$oUsado = $oUsados->Create($oUsado2);
					}
				}
			}
			else
			{
				$oUsadoTomado = new Usado();
				$oUsadoTomado->IdMarca		= $UsadoIdMarca;
				$oUsadoTomado->IdColor		= $UsadoIdColor;
				$oUsadoTomado->Modelo			= $UsadoModelo;
				$oUsadoTomado->ModeloAnio		= $UsadoModeloAnio;
				$oUsadoTomado->Kilometraje	= $UsadoKilometraje;
				$oUsadoTomado->Valuacion		= $UsadoValuacion;
				$oUsadoTomado->Dominio		= $UsadoDominio;
				$oUsadoTomado->IdUbicacion	= Ubicacion::Transito;
				$oUsadoTomado->IdEstado		= EstadoUnidad::Stock;
				$oUsadoTomado->Arreglos		= $UsadoArreglos;
				$oUsadoTomado->Observaciones		= $UsadoObservaciones;
				$oUsadoTomado->IdMinutaUsado		= $oMinutaUsado->IdMinuta;
				$oUsadoTomado->Info			= $UsadoInfo;

				/* creamos el registro */		
				$oUsadoTomado = $oUsados->Create($oUsadoTomado);
				
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
						$oUsado2->IdMinutaUsado	= $oMinutaUsado->IdMinuta;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones		= $UsadoObservaciones2;
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
						$oUsado2->IdMinutaUsado	= $oMinutaUsado->IdMinuta;
						$oUsado2->Arreglos		= $UsadoArreglos2;
						$oUsado2->Observaciones		= $UsadoObservaciones2;
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
			if ($oUsadoTomado && !$EntregaUsado) 
			{
				$oUsados->Delete($oUsadoTomado->IdUsado);
				if ($oUsado2)
					$oUsados->Delete($oUsado2->IdUsado);
			}
		}
		
		$oMinutasFinanciacion->DeleteByIdMinuta($oMinutaUsado->IdMinuta);
		if ($oMinutaUsado && $Financiacion)
		{
			for ($i = 0; $i < count($arrIdAcreedor); $i++)
			{
				$IdAcreedor = $arrIdAcreedor[$i];
				$Importe 	= $arrFinanciacionImportes[$i];
				$Cuotas	 	= $arrFinanciacionCuotas[$i];
				$Importe 	= str_replace(',', '.', $Importe);
				
				if ($IdAcreedor && $Importe != '' && $Cuotas != '')
				{
					$oMinutaFinanciacion = new MinutaUsadoFinanciacion();
					$oMinutaFinanciacion->IdAcreedor 	= $IdAcreedor;
					$oMinutaFinanciacion->Importe 		= $Importe;
					$oMinutaFinanciacion->Cuotas 		= $Cuotas;
					$oMinutaFinanciacion->IdMinuta 		= $IdMinuta;
					
					$oMinutasFinanciacion->Create($oMinutaFinanciacion);
				}
			}
		}
		
		if ($oMinutaUsado && $PedidoAccesorios)
		{
			$create = true;
			if (!$oPedidoAccesorios)
				$oPedidoAccesorios = new PedidoAccesorios();
			else
				$create = false;

			$oPedidoAccesorios->IdMinuta = $oMinutaUsado->IdMinuta;
			$oPedidoAccesorios->Fecha = $oMinutaUsado->FechaMinuta;
			$oPedidoAccesorios->Accesorios 	= $Accesorios;
			
			if ($create)
				$oPedidoAccesorios = $oPedidosAccesorios->Create($oPedidoAccesorios);
			else
			{
				$oPedidosAccesorios->Update($oPedidoAccesorios);
				$oPedidosAccesoriosItems->DeleteByPedidoAccesorio($oPedidoAccesorios->IdPedido);
			}
			
			if ($oPedidoAccesorios)
			{
				for ($i = 0; $i < count($arrDetalles); $i++)
				{
					$Detalle = $arrDetalles[$i];
					$Importe = $arrImportes[$i];
					$IdArticulo = $arrIdArticulo[$i];
					$Importe = str_replace(',', '.', $Importe);
					
					if ($Detalle && $Importe != '')
					{
						$oPedidoAccesorioItem = new PedidoAccesorioItem();
						$oPedidoAccesorioItem->Detalle = $Detalle;
						$oPedidoAccesorioItem->Importe = $Importe;
						$oPedidoAccesorioItem->IdArticulo = $IdArticulo;
						$oPedidoAccesorioItem->IdPedidoAccesorio = $oPedidoAccesorios->IdPedido;
						
						$oPedidosAccesoriosItems->Create($oPedidoAccesorioItem);
					}
				}
			}
		}
		elseif ($oPedidoAccesorios && !$PedidoAccesorios)
		{
			$oPedidosAccesorios->Delete($oPedidoAccesorios->IdPedido);
		}

		header("Location: minutasusados_detail.php?IdMinuta=" . $oMinutaUsado->IdMinuta);
		exit();
	}
}
else
{
	$oUsado 		= $oUsados->GetById($oMinutaUsado->IdUsado);
	$oCliente 		= $oClientes->GetById($oMinutaUsado->IdCliente);
	$oUsuario 		= $oUsuarios->GetById($oMinutaUsado->IdUsuario);
	$oUsadoTomadoMarca 	= $oMarcas->GetById($oUsadoTomado->IdMarca);
	$oUsadoTomadoColor 	= $oColores->GetById($oUsadoTomado->IdColor);
	$oUsadoMarca2 	= $oMarcas->GetById($oUsado2->IdMarca);
	$oUsadoColor2 	= $oColores->GetById($oUsado2->IdColor);
	
	if ($arrMinutasFinanciacion)
	{
		$Financiacion = true;
		$arrIdAcreedor = array();
		$arrFinanciacionImportes = array();
		$arrFinanciacionCuotas = array();
		
		foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
		{
			$arrIdAcreedor[] = $oMinutaFinanciacion->IdAcreedor;
			$arrFinanciacionImportes[] = number_format($oMinutaFinanciacion->Importe, 2);
			$arrFinanciacionCuotas[] = $oMinutaFinanciacion->Cuotas;
		}
	}
	
	if ($oPedidoAccesorios)
	{
		$PedidoAccesorios = true;
		$Accesorios = $oPedidoAccesorios->Accesorios;
		$arrPedidosAccesoriosItems = $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($oPedidoAccesorios);
		$arrDetalles = array();
		$arrImportes = array();
		$arrIdArticulo = array();
		
		foreach ($arrPedidosAccesoriosItems as $oPedidoAccesorioItem)
		{
			$arrDetalles[] = $oPedidoAccesorioItem->Detalle;
			$arrIdArticulo[] = $oPedidoAccesorioItem->IdArticulo;
			$arrImportes[] = number_format($oPedidoAccesorioItem->Importe, 2, ',', '');
		}
	}
	
	
	if ($oMinutaUsado->Condominio)
	{
		$oClienteCondominio = $oClientes->GetById($oMinutaUsado->IdClienteCondominio);
	}
	
	if ($oMinutaUsado->IdClienteReventa)
	{
		$oClienteReventa = $oClientes->GetById($oMinutaUsado->IdClienteReventa);
	}

	$VehiculoModelo = $oUsado->Modelo;

	$IdUsado				= $oMinutaUsado->IdUsado;
	$IdUsuario				= $oUsuario->IdUsuario;
	$Usuario				= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);
	$IdCliente				= $oCliente->IdCliente;
	$IdClienteCondominio	= $oMinutaUsado->IdClienteCondominio;
	$IdClienteReventa		= $oMinutaUsado->IdClienteReventa;
	$IdAcreedor				= $oMinutaUsado->IdAcreedor;
	if ($oClienteReventa)
		$Reventa				= $oClienteReventa->RazonSocial;
		
	$Cliente				= $oCliente->RazonSocial;
	$ClienteCondominio		= '';
	if ($oMinutaUsado->Condominio)
	{
		$ClienteCondominio = $oClienteCondominio->RazonSocial;
	}
	$FechaMinuta			= CambiarFecha($oMinutaUsado->FechaMinuta);
	$PrecioVenta			= $oMinutaUsado->PrecioVenta;
	$Gastos					= $oMinutaUsado->Gastos;
	$GastosOtorgamiento		= $oMinutaUsado->GastosOtorgamiento;
	$DepositoGarantia		= $oMinutaUsado->DepositoGarantia;
	$GastosPrenda			= $oMinutaUsado->GastosPrenda;
	$Anticipo				= $oMinutaUsado->Anticipo;
	$FinanciacionCapital	= $oMinutaUsado->FinanciacionCapital;
	//$Financiacion			= (($oMinutaUsado->FinanciacionCapital != '') && ($oMinutaUsado->FinanciacionCapital != '0')) ? '1' : '0';
	$Condominio				= $oMinutaUsado->Condominio;
	$PlazoPrenda			= $oMinutaUsado->PlazoPrenda;
	$EntregaUsado			= $oMinutaUsado->EntregaUsado;
	$Observaciones			= $oMinutaUsado->Observaciones;
	$FechaVencimiento		= CambiarFecha($oMinutaUsado->FechaVencimiento);
	$FechaRetiro			= CambiarFecha($oMinutaUsado->FechaRetiro);
	$CedulaAzul				= $oMinutaUsado->CedulaAzul;
	
	/* datos del usado */
	$UsadoIdMarca		= $oUsadoTomadoMarca->IdMarca;
	$UsadoMarca			= $oUsadoTomadoMarca->Nombre;
	$UsadoMarcaCodigo	= $oUsadoTomadoMarca->Codigo;
	$UsadoIdColor		= $oUsadoTomadoColor->IdColor;
	$UsadoColor			= $oUsadoTomadoColor->Nombre;
	$UsadoColorCodigo	= $oUsadoTomadoColor->Codigo;
	$UsadoModelo		= $oUsadoTomado->Modelo;
	$UsadoModeloAnio	= $oUsadoTomado->ModeloAnio;
	$UsadoKilometraje	= $oUsadoTomado->Kilometraje;
	$UsadoValuacion		= $oUsadoTomado->Valuacion;
	$UsadoDominio		= $oUsadoTomado->Dominio;
	$UsadoArreglos		= $oUsadoTomado->Arreglos;
	$UsadoObservaciones	= $oUsadoTomado->Observaciones;
	$UsadoInfo			= $oUsadoTomado->Info;
	
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
	$UsadoInfo2			= $oUsado2->Info;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript" src="../js/minutasusados.js"></script>

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
	
	function FilterArticulo(IdArticulo, Codigo)
	{
		var oArticulo = GetArticulo(IdArticulo);
		AgregarItem(oArticulo);
	}

	function AgregarItem(oArticulo) {
		var html = '<tr class="bordeGrisFondo">';
			html+= '	<td height="30"><div id="margen"><input type="text" id="Detalle[]" name="Detalle[]" class="camporFormularioSimple" value="' + oArticulo.Descripcion + '" /></div></td>';
			html+= '	<td width="10">&nbsp;</td>';
			html+= '	<td width="200"><input type="hidden" id="IdArticulo[]" name="IdArticulo[]" value="' + oArticulo.IdArticulo + '" /><input type="hidden" id="Importe[]" name="Importe[]" value="' + (oArticulo.PrecioTerceros*1.21) + '" /></td>';
			html+= '	<td width="75"><div id="margen" align="center"><a href="#" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>';
			html+= '</tr>';
		
			var element = $j(html);
			element.find('#quitar-item').click(function(e) {
				e.preventDefault();
				element.remove();
			});
			$j('#contenedor-items').append(element);
			
			$j('#modal-popup').dialog('close');
	}
	
	function RealizarBusquedaRepuestos(page) {
		var codigo = $j('#FilterCodigo').val();
		var Descripcion = $j('#FilterDescripcion').val();
		if (codigo == undefined)
				Codigo = '';
		if (Descripcion == undefined)
				Descripcion = '';
		var urlAjax = 'articulos_buscar_popup2.php?FilterIdUbicacion=&FilterCodigo=' + Codigo + '&FilterDescripcion=' + Descripcion + '&Page=' + page;
			$j('body').addClass("loading"); 
			$j.ajax(urlAjax,{
				success: function(data) {
					$j('#modal-popup').html(data);	
					$j('body').removeClass("loading"); 
					$j('.agregar').click(function() {
						var idArticulo = $j(this).attr('id').split('_')[1];							
						//FilterArticulo(idArticulo);
					});						
					
					$j('#modal-popup').dialog({
						closeOnEscape: true,
						title: 'Repuestos encontrados',
						width: 700,
						height: 550,
						modal: true
					});
				}
			});
	}


	function AgregarItemFinanciacion() {
		var html = '<tr class="bordeGrisFondo">';
			html+= '	<td height="30"><div id="margen">';
			html+= '		<select id="FinanciacionIdAcreedor[]" name="FinanciacionIdAcreedor[]" class="camporFormularioSimple">';
			<?php
			foreach ($arrAcreedores as $oAcreedor)
			{
				if ($oAcreedor->IdAcreedor == 10 || $oAcreedor->IdAcreedor == 17 || $oAcreedor->IdAcreedor == 18)
					continue;
			?>
			html+= '			<option value="<?= $oAcreedor->IdAcreedor ?>"><?= $oAcreedor->RazonSocial ?></option>';
			<?php
			}
			?>
			html+= '		</select>';
			html+= '	</div></td>';
			html+= '	<td width="10">&nbsp;</td>';
			html+= '	<td width="200"><div id="margen" align="center"><input type="text" id="FinanciacionCuota[]" name="FinanciacionCuota[]" class="camporFormularioChico" value="0" /></div></td>';
			html+= '	<td width="200"><div id="margen" align="center">$<input type="text" id="FinanciacionImporte[]" name="FinanciacionImporte[]" class="camporFormularioChico" value="<?= $arrFinanciacionImportes[$i] ?>" /></div></td>';
			html+= '	<td width="75"><div id="margen" align="center"><a href="#" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>';
			html+= '</tr>';
		
			var element = $j(html);
			element.find('#quitar-item').click(function(e) {
				e.preventDefault();
				element.remove();
			});
			$j('#financiacion-items').append(element);
	}
	
	function QuitarItemFinanciacion(id) {
		$j('#rowfinanciacion_' + id).remove();
	}
	
	
	$j(document).ready(function() {
		<?php
		if (!$arrIdAcreedor || count($arrIdAcreedor) == 0)
		{
		?>
		AgregarItemFinanciacion();
		<?php
		}
		?>
		$j('#agregar-item').click(function(e) {
			e.preventDefault();
			RealizarBusquedaRepuestos(1);
			
			//AgregarItem();
		});
		$j('#agregar-item-financiacion').click(function(e) {
			e.preventDefault();
			
			AgregarItemFinanciacion();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Usados - Modificar</span></td>
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
				<?php include('ssi_minutausados_cuerpo.php'); ?>
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
VerificarPedidoAccesorios('<?=$PedidoAccesorios?>');
</script>

<div id="modal-popup" style="display:none">
</div>
</body>
</html>