<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$IdOrdenTrabajoFranquicia			= intval($_REQUEST['IdOrdenTrabajoFranquicia']);

$oOrdenesTrabajo				= new OrdenesTrabajo();
$oTallerUnidades				= new TallerUnidades();
$oGeneradorNotasCreditoFranquicias	= new GeneradorNotasCreditoFranquicias();
$oComprobantes					= new Comprobantes();
$oNotasCredito					= new NotasCredito();
$oOrdenesTrabajoFranquicias		= new OrdenesTrabajoFranquicias();

$oOrdenTrabajo 	= $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oOrdenTrabajoFranquicia 	= $oOrdenesTrabajoFranquicias->GetById($IdOrdenTrabajoFranquicia);
$oComprobante	= $oComprobantes->GetById($oOrdenTrabajoFranquicia->IdComprobante);

$oNotaCredito = new NotaCredito();
$oNotaCredito->IdCliente 			= $oOrdenTrabajoFranquicia->IdCliente;	
$oNotaCredito->IdFactura			= $oComprobante->IdComprobante;
$oNotaCredito->Comentarios			= 'ANULACION FACTURA ' . $oComprobante->Numero;
$oNotaCredito->Importe				= str_replace(',', '', $oOrdenTrabajoFranquicia->Importe);
$oNotaCredito->Fecha				= date('d-m-Y');

$oNotaCredito = $oNotasCredito->Create($oNotaCredito);

$oGeneradorNotasCreditoFranquicias->Imprimir($oOrdenTrabajo, $IdOrdenTrabajoFranquicia, $oNotaCredito);

//
?>