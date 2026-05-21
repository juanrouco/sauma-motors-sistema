<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);

$oOrdenesTrabajo				= new OrdenesTrabajo();
$oTallerUnidades				= new TallerUnidades();
$oGeneradorNotasCreditoOrdenes	= new GeneradorNotasCreditoOrdenes();
$oComprobantes					= new Comprobantes();
$oNotasCredito					= new NotasCredito();

$oOrdenTrabajo 	= $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$oComprobante	= $oComprobantes->GetById($oOrdenTrabajo->IdComprobante);

$oNotaCredito = new NotaCredito();
$oNotaCredito->IdCliente 			= $oTallerUnidad->IdCliente;	
$oNotaCredito->IdFactura			= $oComprobante->IdComprobante;
$oNotaCredito->Comentarios			= 'ANULACION FACTURA ' . $oComprobante->Numero;
$oNotaCredito->Importe				= str_replace(',', '', $oOrdenTrabajo->ImporteTotal());
$oNotaCredito->Fecha				= date('d-m-Y');

$oNotaCredito = $oNotasCredito->Create($oNotaCredito);

$oGeneradorNotasCreditoOrdenes->Imprimir($oOrdenTrabajo, $oNotaCredito);

?>
<script>window.close();</script>
