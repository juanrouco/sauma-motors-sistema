<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);
$Comentarios	= $_REQUEST['Comentarios'];
$IdOrdenTrabajo	= $_REQUEST['IdOrdenTrabajo'];
$Submit			= $_REQUEST['Submitted'];

/* declaracion de variables */
$err	= 0;
$oOrdenTrabajoComentario 	= new OrdenTrabajoComentario();
$oOrdenTrabajoComentarios	= new OrdenTrabajoComentarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();

$strParams = '';
$strParams.= '?Page=' 			. $Page;
$strParams.= '&FilterNombre='	. $_REQUEST['FilterNombre'];
$errFinalizar			= intval($_REQUEST['errFinalizar']);
$errMONegativa			= intval($_REQUEST['errMONegativa']);

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	exit;
}
			
if ($oOrdenTrabajo->GetListoFinalizar() && $oOrdenTrabajo->GetTareaNegativa())
{
	$oOrdenTrabajo->IdEstadoOrden = EstadoOrden::Finalizado;
	$oOrdenTrabajo->FechaFin = date('d-m-Y H:i:s');
	$oOrdenesTrabajo->Update($oOrdenTrabajo);
				
	header("Location: ordenestrabajo_detail.php" . $strParams . '&IdOrdenTrabajo=' . $oOrdenTrabajo->IdOrdenTrabajo);
}
else
{
	if (!$oOrdenTrabajo->GetListoFinalizar())
		header("Location: ordenestrabajo_detail.php" . $strParams . '&IdOrdenTrabajo=' . $oOrdenTrabajo->IdOrdenTrabajo . '&errFinalizar=1');
	if (!$oOrdenTrabajo->GetTareaNegativa())
		header("Location: ordenestrabajo_detail.php" . $strParams . '&IdOrdenTrabajo=' . $oOrdenTrabajo->IdOrdenTrabajo . '&errMONegativa=1');
}
exit();
		

?>
