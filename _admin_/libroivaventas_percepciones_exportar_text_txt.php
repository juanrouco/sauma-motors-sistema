<?php
set_time_limit(10000);
require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();
/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_LIBRO_IVA))
	Session::NoPerm();

/* obtiene datos enviados */
$FechaDesde				= strval($_REQUEST['FilterFechaDesde']);
$FechaHasta				= strval($_REQUEST['FilterFechaHasta']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oMinutasPago			= new MinutasPago();
$oMinutasPagoItems		= new MinutasPagoItems();
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oNumber				= new Number(); 

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

$filter = ReceiveArray($_REQUEST['filter']);
$oComprobantes 	= new Comprobantes();
$oPage 			= new Page($Page, $PageSize);

$arrUnidades = $oComprobantes->GenerarArchivoPercepciones($filter);

?>
