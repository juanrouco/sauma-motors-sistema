<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtiene datos enviados */
$Page						= intval($_REQUEST['Page']);
$IdOrdenTrabajoComentario	= $_REQUEST['IdOrdenTrabajoComentario'];
$IdOrdenTrabajo				= $_REQUEST['IdOrdenTrabajo'];
$Submit						= $_REQUEST['Submitted'];

/* declaracion de variables */
$err	= 0;
$oOrdenTrabajoComentarios	= new OrdenTrabajoComentarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();
if (!$IdOrdenTrabajoComentario)
{
	$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
	$oOrdenTrabajo->Comentarios = '';
	$oOrdenesTrabajo->Update($oOrdenTrabajo);
}
else
{
	$oOrdenTrabajoComentarios->Delete($IdOrdenTrabajoComentario);
}

header("Location: ordenestrabajo_detail.php?IdOrdenTrabajo=" . $IdOrdenTrabajo);
exit();

?>
