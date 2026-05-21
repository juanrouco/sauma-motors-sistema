<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCompra = intval($_REQUEST['IdCompra']);

$oCompras 			= new Compras();
$oComprobantes 		= new Comprobantes();
$oGeneradorRemitos	= new GeneradorRemitos();

/* obtenemos los datos del comprobante */
if (!$oCompra = $oCompras->GetById($IdCompra))
	exit();

$oGeneradorRemitos->Imprimir($oCompra);

?>

<script>window.close();</script>