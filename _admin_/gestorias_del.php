<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdGestoria	= intval($_REQUEST['IdGestoria']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oGestorias		= new Gestorias();
$oMinutas 		= new Minutas();
$oFormularios 	= new Formularios();
$oPrendas		= new Prendas();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oGestoria = $oGestorias->GetById($IdGestoria))
{
	header("Location: gestorias.php" . $strParams);
	exit;
}

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

if ($Submit)
{
	/* liberamos los formularios asociados */
	$oFormularios->LiberarByGestoria($oGestoria);
	
	$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria);
	$oPrendas->Delete($oPrenda->IdPrenda);

	/* eliminamo el archivo */
	$oGestoria = $oGestorias->Delete($oGestoria->IdGestoria);

	header("Location: gestorias.php" . $strParams);
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
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Eliminar</span></td>
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
									<td><div align="center" class="campoEliminar">Gesti&oacute;n de Operaci&oacute;n Nro. <?=$oMinuta->IdUnidad?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>Los Formularios asociados ser&aacute;n liberados.</strong></div></td>
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
          		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdGestoria" id="IdGestoria" value="<?=$IdGestoria?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'gestorias.php<?=$strParams?>';">
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