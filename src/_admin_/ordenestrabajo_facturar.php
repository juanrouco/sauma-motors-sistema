<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);

$oOrdenesTrabajo			= new OrdenesTrabajo();
$oGeneradorFacturaOrdenes	= new GeneradorFacturaOrdenes();

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oGeneradorFacturaOrdenes->Imprimir($oOrdenTrabajo);

?>
<script>window.close();</script>
