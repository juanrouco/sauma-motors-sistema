<?php

require_once('../inc_library.php'); 
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdFacturaPostVenta			= intval($_REQUEST['IdFacturaPostVenta']);

$oFacturasPostVentas				= new FacturasPostVentas();
$oGeneradorFacturaFacturaPostVenta	= new GeneradorFacturaFacturaPostVenta();

$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta);
$oGeneradorFacturaFacturaPostVenta->Imprimir($oFacturaPostVenta);

?>
<script type="text/javascript">window.opener.location.reload();window.opener.Refreshwindow.close();</script>

