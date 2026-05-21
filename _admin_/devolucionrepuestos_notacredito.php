<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCompra = intval($_REQUEST['IdCompra']);

$oCompras 			= new Compras();
$oComprobantes 		= new Comprobantes();
$oNotaCredito		= new NotaCredito();
$oNotasCredito		= new NotasCredito();
$oGeneradorNotasCreditoVentas = new GeneradorNotasCreditoVentas();

/* obtenemos los datos del comprobante */
if (!$oCompra = $oCompras->GetById($IdCompra))
	exit();

$oCompra->LoadAllDetalles();
$oNotaCredito->IdCliente = $oCompra->IdCliente;	
$oNotaCredito->Comentarios			= 'NOTA DE CREDITO GENERADA POR DEVOLUCION';
$oNotaCredito->Importe				= $oCompra->Total();
$oNotaCredito->Fecha				= date('d-m-Y');
$oNotaCredito = $oNotasCredito->Create($oNotaCredito);

	
$oGeneradorNotasCreditoVentas->Imprimir($oCompra, $oNotaCredito);

$oCompra->IdNotaCredito = $oNotaCredito->IdNotaCredito;
$oCompras->Update($oCompra);

?>
<script>window.close();</script>