<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPresupuesto			= intval($_REQUEST['IdPresupuesto']);
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

$PlazoPrenda			= intval($_REQUEST['PlazoPrenda']);
$PedidoAccesorios		= intval($_REQUEST['PedidoAccesorios']);
$Accesorios				= strval($_REQUEST['Accesorios']);
$arrDetalles 			= $_REQUEST['Detalle'];
$arrImportes 			= $_REQUEST['Importe'];
$arrIdAcreedor 			= $_REQUEST['FinanciacionIdAcreedor'];
$arrFinanciacionImportes= $_REQUEST['FinanciacionImporte'];
$arrFinanciacionCuotas	= $_REQUEST['FinanciacionCuota'];
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= 0;
$oMinuta 					= new Minuta();
$oMinutas					= new Minutas();
$oUnidades					= new Unidades();
$oUsado 					= new Usado();
$oUsados					= new Usados();
$oModelos			 		= new Modelos();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oMinutasFinanciacion 		= new MinutasFinanciacion();
$oPresupuestos				= new Presupuestos();
$oClientes					= new Clientes();
$oUsuarios					= new Usuarios();
$oMarcas					= new Marcas();
$oAcreedores				= new Acreedores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro de la unidad */
if (!$oUnidad = $oUnidades->GetById($IdUnidad))
{	
	header("Location: unidades.php" . $strParams);
	exit();
}

if (!$oPresupuesto = $oPresupuestos->GetById($IdPresupuesto))
{	
	header("Location: presupuestos.php" . $strParams);
	exit();
}

/* verifica si existe el registro del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
{	
	header("Location: unidades.php" . $strParams);
	exit();
}

$arrAcreedores = $oAcreedores->GetAll();

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
	if (($Financiacion) && (count($arrFinanciacionImportes) == 0))
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
			$oUsado->IdClente		= $oMinuta->IdCliente;
	
			$oUsado = $oUsados->Create($oUsado);
			
			if ($UsadoIdMarca2 != '')
			{
				$oUsado = new Usado();
				$oUsado->IdMarca		= $UsadoIdMarca2;
				$oUsado->IdColor		= $UsadoIdColor2;
				$oUsado->Modelo			= $UsadoModelo2;
				$oUsado->ModeloAnio		= $UsadoModeloAnio2;
				$oUsado->Kilometraje	= $UsadoKilometraje2;
				$oUsado->Valuacion		= $UsadoValuacion2;
				$oUsado->Dominio		= $UsadoDominio2;
				$oUsado->IdUbicacion	= Ubicacion::Transito;
				$oUsado->IdEstado		= EstadoUnidad::Stock;
				$oUsado->IdMinuta		= $oMinuta->IdMinuta;
				$oUsado->IdClente		= $oMinuta->IdCliente;
		
				$oUsado = $oUsados->Create($oUsado);
			}
		}
		
		if ($oMinuta && $PedidoAccesorios)
		{
			$oPedidoAccesorios = new PedidoAccesorios();
			$oPedidoAccesorios->IdMinuta = $oMinuta->IdMinuta;
			$oPedidoAccesorios->Fecha = $oMinuta->FechaMinuta;
			$oPedidoAccesorios->Accesorios 	= $Accesorios;
			
			if ($oPedidoAccesorios = $oPedidosAccesorios->Create($oPedidoAccesorios))
			{
				for ($i = 0; $i < count($arrDetalles); $i++)
				{
					$Detalle = $arrDetalles[$i];
					$Importe = $arrImportes[$i];
					$Importe = str_replace(',', '.', $Importe);
					
					if ($Detalle && $Importe != '')
					{
						$oPedidoAccesorioItem = new PedidoAccesorioItem();
						$oPedidoAccesorioItem->Detalle = $Detalle;
						$oPedidoAccesorioItem->Importe = $Importe;
						$oPedidoAccesorioItem->IdPedidoAccesorio = $oPedidoAccesorios->IdPedido;
						
						$oPedidosAccesoriosItems->Create($oPedidoAccesorioItem);
					}
				}
			}
		}
		
		if ($oMinuta && $Financiacion)
		{
			for ($i = 0; $i < count($arrIdAcreedor); $i++)
			{
				$IdAcreedor = $arrIdAcreedor[$i];
				$Importe 	= $arrFinanciacionImportes[$i];
				$Cuotas	 	= $arrFinanciacionCuotas[$i];
				$Importe 	= str_replace(',', '.', $Importe);
				
				if ($IdAcreedor && $Importe != '' && $Cuotas != '')
				{
					$oMinutaFinanciacion = new MinutaFinanciacion();
					$oMinutaFinanciacion->IdAcreedor 	= $IdAcreedor;
					$oMinutaFinanciacion->Importe 		= $Importe;
					$oMinutaFinanciacion->Cuotas 		= $Cuotas;
					$oMinutaFinanciacion->IdMinuta 		= $oMinuta->IdMinuta;
					
					$oMinutasFinanciacion->Create($oMinutaFinanciacion);
				}
			}
		}
		
		if ($oMinuta)
		{
			$oPresupuesto->IdEstado	= PresupuestoEstados::Ganado;
			$oPresupuesto->IdMinuta	= $oMinuta->IdMinuta;
			$oPresupuestos->Update($oPresupuesto);
		}

		header("Location: minutas_detail.php?IdMinuta=" . $oMinuta->IdMinuta);
		exit();
	}
}
else
{
	$Condominio 			= 0;
	$Financiacion 			= $oPresupuesto->Financia;
	$EntregaUsado 			= $oPresupuesto->EntregaUsado;
	$VehiculoModelo 		= $oModelo->DenominacionModelo;
	$PrecioVenta			= $oModelo->ReventaPrecio;
	$GastosFlete			= $oModelo->VentaGastosFlete;
	$GastosPatentamiento	= $oModelo->VentaGastosPatentamiento;
	$GastosOtorgamiento		= (($oModelo->VentaPrecio * $oModelo->Otorgamiento) / 100);
	$GastosPrenda			= (($oModelo->VentaPrecio * $oModelo->Prenda) / 100);
	$Circular				= $oModelo->ReventaBonificacion;
	
	$oCliente				= $oClientes->GetById($oPresupuesto->IdCliente);
	$IdCliente				= $oCliente->IdCliente;
	$Cliente				= $oCliente->RazonSocial;
	
	$oUsuario				= $oUsuarios->GetById($oPresupuesto->IdUsuario);
	$IdUsuario				= $oUsuario->IdUsuario;
	$Usuario				= $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
	

	$PrecioVentaTotal		= $oPresupuesto->Precio;
	$PrecioVenta			= $oModelo->Precio;
	$FinanciacionCapital	= $oPresupuesto->FinanciacionCapital;
	
	$oMarca					= $oMarcas->GetById($oPresupuesto->UsadoIdMarca);
	$UsadoIdMarca			= $oPresupuesto->UsadoIdMarca;
	$UsadoMarca				= $oMarca->Nombre;
	$UsadoMarcaCodigo		= $oMarca->Codigo;
	$UsadoModelo			= $oPresupuesto->UsadoModelo;
	$UsadoModeloAnio		= $oPresupuesto->UsadoAnio;
	$UsadoKilometraje		= $oPresupuesto->UsadoKm;
	$UsadoValuacion			= $oPresupuesto->UsadoPrecioTomado;
	$PlazoPrenda			= $oPresupuesto->FinanciacionCuotas;
	
	$GastosFlete			= $oPresupuesto->GastosFlete;
	$GastosPatentamiento	= $oPresupuesto->GastosPatentamiento;
	$GastosOtorgamiento		= $PrecioVentaTotal - $PrecioVenta - $GastosPatentamiento;
	$GastosPrenda			= $oPresupuesto->GastosPrenda;
	$Circular				= $oPresupuesto->Circular;
	$Anticipo				= $oPresupuesto->Anticipo;
	$DepositoGarantia		= $oPresupuesto->DepositoGarantia;
	$Rentas					= $oPresupuesto->Rentas;
		
	/* determinamos como fecha de compra a la fecha de ayer */
	$FechaMinuta = date("Y-m-d");
	$FechaMinuta = CambiarFecha($FechaMinuta);
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
		if (!$arrDetalles || count($arrDetalles) == 0)
		{
		?>
		AgregarItem();
		<?php
		}
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
VerificarCondominio('<?= $Condominio ?>');
</script>

</body>
</html>