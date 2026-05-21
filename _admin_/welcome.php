<?php

require_once('../inc_library.php');

Session::ForceLogin();

$oUsuario = Session::GetCurrentUser();
if ($oUsuario->IdUsuario == 3)
{
	header("Location: unidades_reporte.php");
	exit;
}

if ( $currentUser->IdUsuario == 21) {
	header('Location: dashboard_postventa.php');
	exit;
}

if ((Session::CheckPerm(PERM_TAREAS_LIST)) && (Session::CheckPerm(PERM_PRESUP_LIST))) {
	header('Location: dashboard_vendedores.php');
	exit;
	
}


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
		<td><p align="center" class="tituloCategoriaMenu">&iexcl;&iexcl;<b> Bienvenido</b> !!</p></td>
	</tr>
	<tr>
		<td><p align="center">&nbsp;</p></td>
	</tr>
	<tr>
		<td><p align="center" class="pNegroBold10">Ya puedes comenzar a utilizar el panel de administraci&oacute;n del sitio.</p></td>
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