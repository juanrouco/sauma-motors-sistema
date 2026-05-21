<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCompra			= intval($_REQUEST['IdCompra']);
$IdComprobante		= intval($_REQUEST['IdComprobante']);
$NumeroComprobante	= strval($_REQUEST['NumeroComprobante']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;

$oFacturasPostVentas	= new FacturasPostVentas();
$oComprobantes			= new Comprobantes();
$oNotaCredito 			= new NotaCredito();
$oNotasCredito			= new NotasCredito();
$oCompras				= new Compras();
$oComprobantes			= new Comprobantes();
$StockMovimientos		= new StockMovimientos();
$ArticuloStocks			= new ArticuloStocks();
$oGeneradorFacturaNotaCreditoPostVenta	= new GeneradorNotasCreditoFacturasPostVentas();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oCompra = $oCompras->GetById($IdCompra))
{
	header("Location: ventarepuestos.php" . $strParams);
	exit;
}

$oFactura = false;
$oFacturaPostVenta = $oFacturasPostVentas->GetByCompra($oCompra);
if ($oFacturaPostVenta)
{
	$oFacturaPostVenta = $oFacturaPostVenta[0];
	$oFactura = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
	$oComprobante = $oFactura;
}

/* si el fomulario fue enviado */
if ($Submit)
{
	if ($oFacturaPostVenta)
	{
		if ($NumeroComprobante == '' || $IdComprobante == '' || $IdComprobante == '0')
			$err |= 2;
		
		if ($err == 0)
		{
			$oCompra->LoadAllDetalles();
			$oNotaCredito->IdCliente 			= $oFacturaPostVenta->IdCliente;
			$oNotaCredito->IdComprobante		= $IdComprobante;	
			$oNotaCredito->Fecha				= date('d-m-Y');
			$oNotaCredito->Comentarios			= 'ANULACION FACTURA ' . $oFactura->Numero;
			$oNotaCredito->Subtotal 			= $oFacturaPostVenta->ImporteNeto;
			$oNotaCredito->Iva10 				= $oFacturaPostVenta->Iva10;
			$oNotaCredito->Iva21 				= $oFacturaPostVenta->Iva21;
			$oNotaCredito->Importe 				= $oFacturaPostVenta->ImporteBruto;
			$oNotaCredito->PercepcionIIBB 		= $oFacturaPostVenta->PercepcionIIBB;
			$oNotaCredito->IdFactura			= $oFactura->IdComprobante;
			$oNotaCredito = $oNotasCredito->Create($oNotaCredito);
			
			if ($oComprobanteNC = $oComprobantes->GetById($IdComprobante))
			{
				$oComprobanteNC->IdEstado = ComprobanteEstados::Utilizado;
				if ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaA)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
				elseif ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaB)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
				
				$oComprobanteNC->Importe = $oFacturaPostVenta->ImporteBruto;
				$oComprobanteNC->Fecha = date('d-m-Y');
				$oComprobanteNC->ImporteIva10 = $oComprobante->ImporteIva10;
				$oComprobanteNC->ImporteIva21 = $oComprobante->ImporteIva21;
				$oComprobanteNC->PercepcionIIBB = $oComprobante->PercepcionIIBB;
				$oComprobanteNC->IdCliente = $oComprobante->IdCliente;
						
				$oComprobantes->Update($oComprobanteNC);
			}
			
			//$oGeneradorFacturaNotaCreditoPostVenta->Imprimir($oFacturaPostVenta);
			$oFactura->IdEstado = ComprobanteEstados::Anulado;
			$oFactura->FechaAnulada = date('d-m-Y');
			$oFactura = $oComprobantes->Update($oFactura);
			
		}
	}
	
	
	$oCompra->LoadAllDetalles();
	foreach ($oCompra->CompraDetalles as $oCompraDetalle)
	{
		$oStockMovimiento = $StockMovimientos->GetByArticuloAndUbicacionAndRemito($oCompraDetalle->IdArticulo, $oCompra->IdUbicacion, $oRemito->Numero);
		if ($oStockMovimiento)
		{
			$StockMovimientos->Delete($oStockMovimiento->IdStockMovimiento);
		}
	
		$oArticuloStock = $ArticuloStocks->GetByArticuloAndUbicacion($oCompraDetalle->IdArticulo, $oCompra->IdUbicacion);
		$oArticuloStock->AumentarStock($oCompraDetalle->Cantidad);
		$ArticuloStocks->Update($oArticuloStock);
	}

	header("Location: ventarepuestos.php" . $strParams);
	exit;
}


/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

var IdTipoComprobante = '';
var arrParams = new Array();

function GetNextFactura(IdTipoComprobante)
{
	var arr = new Array();
	var obj;
	var oComprobante;

	if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
		return;
				
	arr['IdTipoComprobante'] = IdTipoComprobante;
	obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oComprobante = obj.Response;

	return oComprobante;	
}

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	Get('IdComprobante').value 		= IdComprobante;
	Get('NumeroComprobante').value 	= NumeroComprobante;
}

$j(document).ready(function() {
	if ('<?= $oComprobante->IdTipoComprobante ?>'  == '<?=ComprobanteTipos::FacturaA?>')
	{		
		IdTipoComprobante = '<?=ComprobanteTipos::NotaCreditoA?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
	}
	else if ('<?= $oComprobante->IdTipoComprobante ?>' == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::NotaCreditoB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
	}
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
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Repuestos - Anular</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
	</tr>
	<tr>
		<td>
		  	<div align="center"><form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea anular la siguiente venta?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<?php 
								if ($oFactura)
								{
								?>
								<tr>
									<td><div align="center" class="campoEliminar">Nro. Factura:&nbsp;<?=$oFactura->Prefijo . ' - ' . $oFactura->Numero?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="left"><strong>Nro. Nota de Cr&eacute;dito:</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<div align="left">
											<input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioSimple" maxlength="8" value="<?=$oComprobanteNC->Numero?>" autocomplete="off" />
											<script language="javascript">
												arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
												arrParams['Prefijo'] = '0002';
												SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
											</script>
										</div>
									</td>
								</tr>
								<tr>
									<td><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nro. de nota de cr&eacute;dito</li><?php } ?>&nbsp;</td>
								</tr>
								<?php
								}								
								?>
						  	</table>						
                      	</td>
					</tr>
				</table>
                <table width="60%" border="0" cellspacing="0" cellpadding="0">
                  	<tr>
                    	<td height="1"><div align="center"></div></td>
                  	</tr>
                </table>
          		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdCompra" id="IdCompra" value="<?=$IdCompra?>" />
						<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?=$IdComprobante?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'ventarepuestos.php<?=$strParams?>';">
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