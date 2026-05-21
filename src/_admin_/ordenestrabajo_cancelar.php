<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);
$Comentarios	= $_REQUEST['Comentarios'];
$IdTipoRechazo	= $_REQUEST['IdTipoRechazo'];
$IdOrdenTrabajo	= $_REQUEST['IdOrdenTrabajo'];
$Submit			= $_REQUEST['Submitted'];

/* declaracion de variables */
$err	= 0;
$oOrdenTrabajoComentario 	= new OrdenTrabajoComentario();
$oOrdenTrabajoComentarios	= new OrdenTrabajoComentarios();
$oOrdenesTrabajo			= new OrdenesTrabajo();

$arrTiposRechazos			= TiposRechazos::GetAll();

$strParams = '';
$strParams.= '?Page=' 			. $Page;
$strParams.= '&FilterNombre='	. $_REQUEST['FilterNombre'];

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($IdTipoRechazo == '')
		$err |= 1;
	if ($IdTipoRechazo == 4 && trim($Comentarios) == '')
		$err |= 2;

	/* si no hay errores... */
	if ($err == 0)
	{		
			$oOrdenTrabajoComentario->IdOrdenTrabajo 	= $IdOrdenTrabajo;
			$oOrdenTrabajoComentario->Comentarios 		= $Comentarios;
			$oOrdenTrabajoComentario->IdTipoRechazo		= $IdTipoRechazo;
			$oOrdenTrabajoComentario->IdUsuario 		= Session::GetCurrentUser()->IdUsuario;

			$oOrdenTrabajoComentarios->Create($oOrdenTrabajoComentario);
			
			$oOrdenTrabajo->IdEstadoOrden = EstadoOrden::Rechazado;
			$oOrdenTrabajo->FechaInicio = date('d/m/Y H:i:s');
			$oOrdenesTrabajo->Update($oOrdenTrabajo);
			
			header("Location: ordenestrabajo_detail.php" . $strParams . '&IdOrdenTrabajo=' . $oOrdenTrabajo->IdOrdenTrabajo);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Ordenes de Trabajo - Ingreso Unidad a OT N&deg; <?= $IdOrdenTrabajo ?></span></td>
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
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data">
					<input type="hidden" name="Submitted" id="Submitted" value="1">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
					<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>">
					<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>Motivo:</td>
										<td>
											<select name="IdTipoRechazo" id="IdTipoRechazo" class="camporFormularioSimple" style="width: 300px">
											<option value="">Seleccione un motivo</option>
											<?php
											foreach ($arrTiposRechazos as $oTipoRechazo)
											{
												$select = '';
												if ($IdTipoRechazo == $oTipoRechazo['IdTipoRechazo'])
													$select = 'selected="selected"';
											?>
												<option value="<?= $oTipoRechazo['IdTipoRechazo'] ?>" <?= $select ?>><?= $oTipoRechazo['Nombre'] ?></option>
											<?php
											}
											?>
											</select>
										</td>
									</tr>
								<?php if ($err & 1) { ?>
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><li style="color:#FF0000;">Seleccione el motivo</li></td>
                                    </tr>
								<?php } ?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>Comentarios:</td>
										<td>
											<textarea name="Comentarios" id="Comentarios" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" style="height: 125px; width: 300px"><?=$Comentarios?></textarea>
										</td>
									</tr>
								<?php if ($err & 2) { ?>
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><li style="color:#FF0000;">Debe ingresar un comentario</li></td>
                                    </tr>
								<?php } ?>
															
									<tr>
										<td>&nbsp;</td>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_detail.php<?=$strParams?>&IdOrdenTrabajo=<?= $oOrdenTrabajo->IdOrdenTrabajo ?>';" value="Cancelar" />
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