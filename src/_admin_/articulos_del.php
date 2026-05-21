<?php

require_once('../inc_library.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$IdArticulo	= $_REQUEST['IdArticulo'];
$Submit			= $_REQUEST['Submitted'];

/* declaracion de variables */
$err		= 0;
$Articulos	= new Articulos();

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
$oArticulo = $Articulos->GetById($IdArticulo);
if (!$oArticulo)
	$err += 1;

if ($Submit)
{
	$Articulos->Delete($oArticulo->IdArticulo);
	header("Location: articulos.php" . $strParams);
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
					<td height="40"><span class="tituloPagina">Eliminar Repuesto</span></td>
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
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar el siguiente repuesto?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$oArticulo->Descripcion?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
						  </table>						</td>
					</tr>
				</table>
		        <table width="60%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="1"><div align="center"></div></td>
                  </tr>
                </table>
  <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
<form method="post" action="<?=$strParams?>">
						<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?=$IdArticulo?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1">
						
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'articulos.php<?=$strParams?>';">
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