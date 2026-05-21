<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ORDE_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdOrdenTrabajo	= intval($_REQUEST['IdOrdenTrabajo']);
$Submit			= (isset($_REQUEST['Submitted']));

$err		= 0;
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oOrdenTrabajoHitos	= new OrdenTrabajoHitos();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajo	= $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header('Location: ordenestrabajo_taller.php' . $strParams);
	exit;
}

if ($Submit)
{
	$oOrdenTrabajoHito = new OrdenTrabajoHito();
	$oOrdenTrabajoHito->IdOrdenTrabajo 	= $oOrdenTrabajo->IdOrdenTrabajo;
	$oOrdenTrabajoHito->IdUsuario		= Session::GetCurrentUser()->IdUsuario;
	$oOrdenTrabajoHito->FechaHora		= date('d/m/Y H:i:s');
	$oOrdenTrabajoHito->TipoHito		= OrdenTrabajoHito::Iniciar;
	
	$oOrdenTrabajoHitos->Create($oOrdenTrabajoHito);

	header("Location: ordenestrabajo_taller.php" . $strParams);
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
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Ordenes de Trabajo - Iniciar</span></td>
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
									<td><div align="center"><strong>&iquest;Esta seguro que desea iniciar la orden de trabajo n&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?> ?</strong></div></td>
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
						<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'ordenestrabajo_taller.php<?=$strParams?>';">
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