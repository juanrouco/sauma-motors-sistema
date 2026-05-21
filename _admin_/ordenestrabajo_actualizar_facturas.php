<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Action 	= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaDesde']			= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta']			= trim($_REQUEST['FilterFechaHasta']);
	$filter['IdEstadoOrden'] 		= trim($_REQUEST['FilterIdEstadoOrden']);	
	$filter['Dominio'] 				= trim($_REQUEST['FilterDominio']);	
	$filter['IdUsuarioAsignado'] 	= trim($_REQUEST['FilterIdUsuarioAsignado']);
	$filter['IdTipoVenta']			= trim($_REQUEST['FilterIdTipoVenta']);
	$filter['Cliente']				= trim($_REQUEST['FilterCliente']);
	$filter['NumeroVin']			= trim($_REQUEST['FilterNumeroVin']);
	$filter['IdOrdenTrabajo']			= trim($_REQUEST['FilterIdOrdenTrabajo']);
}

$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$arrData 				= array();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oModelos 				= new Modelos();
$oTallerUnidades		= new TallerUnidades();
$oUsuarios				= new Usuarios();
$oEstadosOrden			= new EstadosOrden();
$oClientes				= new Clientes();
$oComprobantes			= new Comprobantes();

$arrData 	= $oOrdenesTrabajo->GetAllFacturados();

foreach ($arrData as $oOrdenTrabajo)
{
	$oComprobante = $oComprobantes->GetById($oOrdenTrabajo->IdComprobante);
	if (!$oComprobante->Importe)
	{
		$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
		
		$oComprobante->IdCliente = $oTallerUnidad->IdCliente;
		$oComprobante->Importe = str_replace(',', '', $oOrdenTrabajo->ImporteTotal());
		$oComprobante->Fecha = CambiarFecha($oOrdenTrabajo->FechaFin);
		$oComprobante->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
		print_r($oOrdenTrabajo->IdOrdenTrabajo);
		$oComprobantes->Update($oComprobante);
		
	}
}

?>
