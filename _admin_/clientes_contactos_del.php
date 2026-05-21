<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CLIE_CONTACTS))
	Session::NoPerm();

/* obtiene datos enviados */
$IdContacto = intval($_REQUEST['IdContacto']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oClienteContactos	= new ClienteContactos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oClienteContacto = $oClienteContactos->GetById($IdContacto))
{
	header('Location: clientes_contactos.php' . $strParams);
	exit;
}

if ($Submit)
{
	$oClienteContacto = $oClienteContactos->Delete($oClienteContacto->IdContacto);

	header("Location: clientes_contactos.php" . $strParams);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Contactos - Eliminar</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
	</tr>
	<tr>
		<td>
		  	<div align="center">

				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar el siguiente registro?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$oClienteContacto->Nombre . ', ' . $oClienteContacto->Apellido?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
						  </table>
						</td>
					</tr>
					<tr>
						<td height="1"><div align="center"></div></td>
				  	</tr>
				</table>
				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
			  		<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdContacto" id="IdContacto" value="<?=$IdContacto?>" />
						
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'clientes_contactos.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					</form>
				</table>				
		  	</div>
       	</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>