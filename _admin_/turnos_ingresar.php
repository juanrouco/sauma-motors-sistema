<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);
$Comentarios	= $_REQUEST['Comentarios'];
$IdTurno		= $_REQUEST['IdTurno'];
$Submit			= $_REQUEST['Submitted'];

/* declaracion de variables */
$err	= 0;
$oTurnoComentario 	= new TurnoComentario();
$oTurnosComentarios	= new TurnosComentarios();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oTurnos			= new Turnos();

$strParams = '';
$strParams.= '?Page=' 			. $Page;
$strParams.= '&FilterNombre='	. $_REQUEST['FilterNombre'];

if (!$oTurno = $oTurnos->GetById($IdTurno))
{
	exit;
}

if ($Submit)
{
	/* validaciones... 
	if (trim($Comentarios) == '')
		$err += 1;	
*/
	/* si no hay errores... */
	if ($err == 0)
	{		
		if ($Comentarios != '')
		{
			$oTurnoComentario->IdTurno 			= $IdTurno;
			$oTurnoComentario->Comentarios 		= $Comentarios;
			$oTurnoComentario->IdUsuario 		= Session::GetCurrentUser()->IdUsuario;

			$oTurnosComentarios->Create($oTurnoComentario);
		}
			$oTurno->IdEstadoOrden = EstadoOrden::Aceptada;
			$oTurno->IdUsuarioAsignado = Session::GetCurrentUser()->IdUsuario;
			if ($oOrdenTrabajo = $oTurno->GenerarOrdenTrabajo())
			{
				$oTurno->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
				$oOrdenTrabajo->IdUsuarioAsignado = Session::GetCurrentUser()->IdUsuario;
				$oOrdenesTrabajo->Update($oOrdenTrabajo);
			}
			
			$oTurnos->Update($oTurno);
			
			header("Location: ordenestrabajo_imagenes.php" . $strParams . '&IdTurno=' . $oOrdenTrabajo->IdTurno);
			exit();
		
	}
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Turnos - Ingreso de la Unidad</span></td>
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
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data" onsubmit="document.getElementById('btnAceptar').disabled=true;">
					<input type="hidden" name="Submitted" id="Submitted" value="1">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
					<input type="hidden" name="IdTurno" id="IdTurno" value="<?=$IdTurno?>">
					<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>Comentarios:</td>
										<td>
											<textarea name="Comentarios" id="Comentarios" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" style="height: 125px; width: 300px"><?=$Comentarios?></textarea>
										</td>
									</tr>
								<?php if ($err & 1) { ?>
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><li style="color:#FF0000;">Ingrese el comentario</li></td>
                                    </tr>
								<?php } ?>
															
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="50%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'turnos_detail.php<?=$strParams?>&IdTurno=<?= $oOrdenTrabajo->IdTurno ?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

</body>
</html>