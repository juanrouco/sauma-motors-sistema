<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCompra	= intval($_REQUEST['IdCompra']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oCompras			= new Compras();
$oComprobantes		= new Comprobantes();
$StockMovimientos	= new StockMovimientos();
$ArticuloStocks		= new ArticuloStocks();
$oNotaCredito		= new NotaCredito();
$oNotasCredito		= new NotasCredito();
$oGeneradorNotasCreditoVentas = new GeneradorNotasCreditoVentas();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oCompra = $oCompras->GetById($IdCompra))
{
	header("Location: ventarepuestos.php" . $strParams);
	exit;
}

$oFactura = false;
if ($oCompra->IdFactura && !$oFactura = $oComprobantes->GetById($oCompra->IdFactura))
{
	header("Location: ventarepuestos.php" . $strParams);
	exit;
}

if (!$oFactura = $oComprobantes->GetById($oCompra->IdFactura))
{
	header("Location: ventarepuestos.php" . $strParams);
	exit;
}

/* si el fomulario fue enviado */
if ($Submit)
{
	if ($oFactura)
	{
		$oCompra->LoadAllDetalles();
		$oNotaCredito->IdCliente = $oCompra->IdCliente;	
		$oNotaCredito->IdFactura			= $oFactura->IdComprobante;
		$oNotaCredito->Comentarios			= 'ANULACION FACTURA ' . $oFactura->Numero;
		$oNotaCredito->Importe				= $oCompra->Total();
		$oNotaCredito->Fecha				= date('d-m-Y');
		$oNotaCredito = $oNotasCredito->Create($oNotaCredito);
		$oGeneradorNotasCreditoVentas->Imprimir($oCompra, $oNotaCredito);
	}
	$oFactura->IdEstado = ComprobanteEstados::Anulado;
	$oFactura->FechaAnulada = date('d-m-Y');
	
	$oFactura = $oComprobantes->Update($oFactura);
	
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

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
		  	<div align="center">
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
					<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdCompra" id="IdCompra" value="<?=$IdCompra?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'ventarepuestos.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>