<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$IdOrdenTrabajoFranquicia			= intval($_REQUEST['IdOrdenTrabajoFranquicia']);

$oOrdenesTrabajo			= new OrdenesTrabajo();
$oGeneradorFacturaFranquicias	= new GeneradorFacturaFranquicias();

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oGeneradorFacturaFranquicias->Imprimir($oOrdenTrabajo, $IdOrdenTrabajoFranquicia);

?>
<script>window.close();</script>
