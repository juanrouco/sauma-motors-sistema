<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
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
$GastosOtorgamiento		= floatval($_REQUEST['GastosOtorgamiento']);
$DepositoGarantia		= floatval($_REQUEST['DepositoGarantia']);
$GastosPrenda			= floatval($_REQUEST['GastosPrenda']);
$Gastos					= floatval($_REQUEST['Gastos']);
$Anticipo				= floatval($_REQUEST['Anticipo']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);
$Financiacion			= intval($_REQUEST['Financiacion']);
$Condominio				= intval($_REQUEST['Condominio']);
$PlazoPrenda			= intval($_REQUEST['PlazoPrenda']);
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
$arrIdAcreedor 			= $_REQUEST['FinanciacionIdAcreedor'];
$arrFinanciacionImportes= $_REQUEST['FinanciacionImporte'];
$arrFinanciacionCuotas	= $_REQUEST['FinanciacionCuota'];

$IdAcreedor				= intval($_REQUEST['IdAcreedor']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$FechaVencimiento		= strval($_REQUEST['FechaVencimiento']);
$FechaRetiro			= strval($_REQUEST['FechaRetiro']);
$CedulaAzul				= intval($_REQUEST['CedulaAzul']);
$PedidoAccesorios		= intval($_REQUEST['PedidoAccesorios']);
$Accesorios				= strval($_REQUEST['Accesorios']);
$arrDetalles 			= $_REQUEST['Detalle'];
$arrImportes 			= $_REQUEST['Importe'];
$arrIdArticulo 			= $_REQUEST['IdArticulo'];
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oMinutaUsado 				= new MinutaUsado();
$oMinutasUsados				= new MinutasUsados();
$oUsados					= new Usados();
$oAcreedores				= new Acreedores();
$oMinutasFinanciacion 		= new MinutasUsadosFinanciacion();
$oPedidosAccesorios	 		= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();
$oClientes					= new Clientes();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro de la unidad */
if (!$oUsado = $oUsados->GetById($IdUsado))
{	
	header("Location: usados.php" . $strParams);
	exit();
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
		$GastosOtorgamiento		= str_replace(",", ".", $GastosOtorgamiento);
		$GastosPrenda			= str_replace(",", ".", $GastosPrenda);
		$Anticipo				= str_replace(",", ".", $Anticipo);
		$FinanciacionCapital	= str_replace(",", ".", $FinanciacionCapital);
		$DepositoGarantia  		= str_replace(",", ".", $DepositoGarantia);
		$UsadoValuacion			= str_replace(",", ".", $UsadoValuacion);
		$UsadoValuacion2		= str_replace(",", ".", $UsadoValuacion2);
		
		/* si no requiere financiacion */
		if (!($Financiacion)) $FinanciacionCapital = 0;
		if (!($Financiacion)) $IdAcreedor = '';

		$oMinutaUsado->IdUsado				= $IdUsado;
		$oMinutaUsado->IdUsuario				= $IdUsuario;
		$oMinutaUsado->IdCliente				= $IdCliente;
		if ($Condominio)
			$oMinutaUsado->IdClienteCondominio	= $IdClienteCondominio;
		$oMinutaUsado->FechaMinuta			= $FechaMinuta;
		$oMinutaUsado->PrecioVenta			= $PrecioVenta;
		$oMinutaUsado->GastosOtorgamiento	= $GastosOtorgamiento;
		$oMinutaUsado->DepositoGarantia		= $DepositoGarantia;
		$oMinutaUsado->GastosPrenda			= $GastosPrenda;
		$oMinutaUsado->Gastos				= $Gastos;
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

		if ($oMinutaExistente = $oMinutasUsados->GetById($IdUsado))
		{
			$oMinutaUsado->IdMinuta = $IdUsado;
			if ($oMinutaUsado = $oMinutasUsados->Update($oMinutaUsado))
			{
				/* obtenemos los datos de la unidad */
				$oUsado = $oUsados->GetById($IdUsado);
				
				/* actualizamos el estado del vehiculo */
				if ($oUsado->IdEstado == EstadoUnidad::Stock)
					$oUsado->IdEstado = EstadoUnidad::Reservado;
				//print_r($oUsado);
				$oUsados->Update($oUsado);
				
			}
		}
		else
		{
			if ($oMinutaUsado = $oMinutasUsados->Create($oMinutaUsado))
			{
				/* obtenemos los datos de la unidad */
				$oUsado = $oUsados->GetById($IdUsado);
				
				/* actualizamos el estado del vehiculo */
				if ($oUsado->IdEstado == EstadoUnidad::Stock)
					$oUsado->IdEstado = EstadoUnidad::Reservado;
				
				$oUsados->Update($oUsado);
			}
		}
		
		if ($oMinutaUsado && $EntregaUsado)
		{
			$oUsadoTomado = new Usado();
			$oUsadoTomado->IdMarca			= $UsadoIdMarca;
			$oUsadoTomado->IdColor			= $UsadoIdColor;
			$oUsadoTomado->Modelo			= $UsadoModelo;
			$oUsadoTomado->ModeloAnio		= $UsadoModeloAnio;
			$oUsadoTomado->Kilometraje		= $UsadoKilometraje;
			$oUsadoTomado->Valuacion		= $UsadoValuacion;
			$oUsadoTomado->Dominio			= $UsadoDominio;
			$oUsadoTomado->IdUbicacion		= Ubicacion::Transito;
			$oUsadoTomado->IdEstado			= EstadoUnidad::Stock;
			$oUsadoTomado->IdMinutaUsado	= $oMinutaUsado->IdMinuta;
			$oUsadoTomado->IdClente			= $oMinutaUsado->IdCliente;
			$oUsadoTomado->Arreglos			= $UsadoArreglos;
			$oUsadoTomado->Observaciones	= $UsadoObservaciones;
			$oUsadoTomado->Info				= $UsadoInfo;
	
			$oUsadoTomado = $oUsados->Create($oUsadoTomado);
			
			if ($UsadoIdMarca2 != '')
			{
				$oUsadoTomado = new Usado();
				$oUsadoTomado->IdMarca		= $UsadoIdMarca2;
				$oUsadoTomado->IdColor		= $UsadoIdColor2;
				$oUsadoTomado->Modelo			= $UsadoModelo2;
				$oUsadoTomado->ModeloAnio		= $UsadoModeloAnio2;
				$oUsadoTomado->Kilometraje	= $UsadoKilometraje2;
				$oUsadoTomado->Valuacion		= $UsadoValuacion2;
				$oUsadoTomado->Dominio		= $UsadoDominio2;
				$oUsadoTomado->IdUbicacion	= Ubicacion::Transito;
				$oUsadoTomado->IdEstado		= EstadoUnidad::Stock;
				$oUsadoTomado->IdMinutaUsado	= $oMinutaUsado->IdMinuta;
				$oUsadoTomado->IdClente		= $oMinutaUsado->IdCliente;
				$oUsadoTomado->Arreglos			= $UsadoArreglos2;
				$oUsadoTomado->Observaciones	= $UsadoObservaciones2;
				$oUsadoTomado->Info			= $UsadoInfo2;
		
				$oUsadoTomado = $oUsados->Create($oUsadoTomado);
			}
		}
		
		if ($oMinutaUsado && $PedidoAccesorios)
		{
			$oPedidoAccesorios = new PedidoAccesorios();
			$oPedidoAccesorios->IdMinutaUsado = $oMinutaUsado->IdMinuta;
			$oPedidoAccesorios->Fecha = $oMinutaUsado->FechaMinuta;
			$oPedidoAccesorios->Accesorios 	= $Accesorios;
			
			if ($oPedidoAccesorios = $oPedidosAccesorios->Create($oPedidoAccesorios))
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
					$oMinutaFinanciacion->IdMinuta 		= $oMinutaUsado->IdMinuta;
					
					$oMinutasFinanciacion->Create($oMinutaFinanciacion);
				}
			}
		}
		
		header("Location: minutasusados_detail.php?IdMinuta=" . $oMinutaUsado->IdMinuta);
		exit();
	}
}
else
{
	$Condominio 			= 0;
	$Financiacion 			= 0;
	$VehiculoModelo 		= $oUsado->Modelo;
		
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Usados - Agregar</span></td>
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
VerificarPedidoAccesorios('<?=$PedidoAccesorios?>');
<?php 
if ($Condominio)
{
?>
VerificarCondominio('1');
<?php
}
?>
</script>

<div id="modal-popup" style="display:none">
</div>
</body>
</html>