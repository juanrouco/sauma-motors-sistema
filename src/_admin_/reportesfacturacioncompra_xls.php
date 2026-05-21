<?php

require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_REPF_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdReporteFacturacion = intval($_REQUEST['IdReporteFacturacion']);

/* declaramos variables necesarias */
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oColores				= new Colores();
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oReportesFacturacion	= new ReportesFacturacion();
$oFacturaUnidades		= new FacturaUnidades();
$oComprobantes 			= new Comprobantes();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['FechaFacturaDesde'] 	= trim($_REQUEST['FechaFacturaDesde']);
	$filter['FechaFacturaHasta'] 	= trim($_REQUEST['FechaFacturaHasta']);
	
}


//$Paginado	= Pageable::PrintPaginator($oPage, $oUnidades->GetCountRows($filter), true);

/* obtenemos listado de undiades */
$arrUnidades = $oUnidades->ExportReporteFacturasCompraCsv($filter);

?>
