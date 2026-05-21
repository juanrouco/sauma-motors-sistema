<?php

require_once('../inc_library.php');

Session::ForceLogin();

$oGeneradorCierreZ 	= new GeneradorCierreZ();
$oCierresZ			= new CierresZ();
$oCierreZ			= new CierreZ();

$oGeneradorCierreZ->RealizarCierreZ();

$oCierreZ->Fecha = date('d/m/Y H:i:s');
$oCierreZ->IdUsuario = Session::GetCurrentUser()->IdUsuario;

$oCierresZ->Create($oCierreZ);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
</head>
<body>
<table width="100%" height="68"  border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><div align="center"></div></td>
	</tr>
</table>
<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordeGris">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><p align="center">&nbsp;</p></td>
	</tr>
	<tr>
		<td><p align="center" class="tituloCategoriaMenu"><b>El cierre Z fue efecturado exitosamente</b></p></td>
	</tr>
	<tr>
		<td><p align="center">&nbsp;</p></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"> - <?=date('d/m/Y');?> - </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
</body>
</html>