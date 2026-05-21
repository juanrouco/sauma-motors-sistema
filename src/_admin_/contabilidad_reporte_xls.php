<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_UNIDADES))
	Session::NoPerm();

/* obtiene datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);
$Submit		= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaDesde'] 	= trim($_REQUEST['FechaDesde']);
	$filter['FechaHasta'] = trim($_REQUEST['FechaHasta']);
	$filter['Numero'] = trim($_REQUEST['FilterNumero']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* agregamos filtro oculto para tipo de comprobante */
$filter['IdTipoComprobante'] 	= trim($_REQUEST['IdTipoComprobante']);
$filter['Prefijo'] 				= '0002';

/* declaracion de variables */
$arrData 		= array();
$oComprobantes 	= new Comprobantes();
$oClientes 		= new Clientes();
$oPage 			= new Page($Page, $PageSize);

$arrUnidades = $oComprobantes->ExportReporteCsv($filter);
?>