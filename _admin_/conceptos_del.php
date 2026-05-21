<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_COMPRA_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);
$IdConcepto		= $_REQUEST['IdConcepto'];
$Submit			= $_REQUEST['Submitted'];
$FilterNombre 	= $_REQUEST['FilterNombre'];

/* declaracion de variables */
$err	= 0;
$oConceptos	= new Conceptos();

$strParams = '';
$strParams.= '?Page=' . $Page;
$strParams.= '&FilterNombre=' . $FilterNombre;

/* verifica si existe el registro */
$oConcepto = $oConceptos->GetById($IdConcepto);
if (!$oConcepto)
	$err += 1;

if ($Submit)
{
	$oConceptos->Delete($oConcepto->IdConcepto);
	header("Location: conceptos.php" . $strParams);
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
					<td height="40"><span class="tituloPagina">Eliminar Concepto </span></td>
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
				<?php
					if ($oConcepto->CanDelete())
					{
				?>
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
										<td><div align="center" class="campoEliminar"><?=$oConcepto->Nombre?></div></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table width="60%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="1"><div align="center"></div></td>
					</tr>
					</table>
					<form method="post">
						<input type="hidden" name="Submitted" id="Submitted" value="1">
						<input type="hidden" name="IdConcepto" id="IdConcepto" value="<?=$IdConcepto?>">
						<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
						<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">						
							<tr>
								<td height="30">
									<div align="center">
										<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
										<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'conceptos.php<?=$strParams?>';">
									</div>
								</td>
							</tr>
						</table>
					</form>
				<?php
					} else {						
				?>
					<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="center"><strong>El concepto <?= $oConcepto->Nombre ?> no puede ser eliminado, ya que posee registros relacionados.</strong></div></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="center" class="campoEliminar"><input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Aceptar" onClick="javascript: window.location.href = 'conceptos.php<?=$strParams?>';"></div></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>						
							</td>
						</tr>
					</table>
				<?php
					}
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>