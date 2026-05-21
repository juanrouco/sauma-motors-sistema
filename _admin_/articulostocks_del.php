<?php

require_once('../inc_library.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$IdArticuloStock	= $_REQUEST['IdArticuloStock'];
$Submit				= $_REQUEST['Submitted'];

/* declaracion de variables */
$err		= 0;
$ArticuloStocks	= new ArticuloStocks();
$Articulos		= new Articulos();
$Ubicaciones	= new Ubicaciones();

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
$oArticuloStock = $ArticuloStocks->GetById($IdArticuloStock);
if (!$oArticuloStock)
	$err += 1;

$oArticulo 	= $Articulos->GetById($oArticuloStock->IdArticulo);
$oUbicacion = $Ubicaciones->GetById($oArticuloStock->IdUbicacion);
	
if ($Submit)
{
	$ArticuloStocks->Delete($oArticuloStock->IdArticuloStock);
	header("Location: articulostocks.php" . $strParams);
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
					<td height="40"><span class="tituloPagina">Eliminar Stock</span></td>
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
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar el siguiente registro?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$oArticulo->Descripcion?> - <?=$oUbicacion->Nombre?></div></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'articulostocks.php<?=$strParams?>';">
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