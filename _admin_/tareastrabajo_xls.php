<?php 
require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_CREATE))
	Session::NoPerm();


/* agregamos filtro oculto para tipo de comprobante */

/* declaracion de variables */
$arrData 		= array();
$oTareasTrabajo	= new TareasTrabajo();

$arrUnidades = $oTareasTrabajo->ExportCsv();
?>