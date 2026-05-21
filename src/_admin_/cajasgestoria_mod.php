<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJGES_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCajaGestoria			= intval($_REQUEST['IdCajaGestoria']);
$IdTipoMovimiento		= intval($_REQUEST['IdTipoMovimiento']);
$Fecha					= strval($_REQUEST['Fecha']);
$Monto					= floatval($_REQUEST['Monto']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oCajasGestoria		= new CajasGestoria();

if (!$oCajaGestoria = $oCajasGestoria->GetById($IdCajaGestoria))
{	
	header('Location: cajasgestoria.php');
	exit;
}

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	$Monto = str_replace(',', '.', $Monto);
	/* validaciones... */
	if ($IdTipoMovimiento == '')
		$err |= 1;
	if ($Fecha == '')
		$err |= 2;
	if (($Monto == '') || (floatval($Monto) == 0))
		$err |= 4;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oUsuario = Session::GetCurrentUser();
		
		$oCajaGestoria->IdUsuario 			= $oUsuario->IdUsuario;
		$oCajaGestoria->IdTipoMovimiento 	=  $IdTipoMovimiento;
		if ($oCajaGestoria->IdTipoMovimiento == TiposMovimientosCaja::Egreso && $Monto > 0)
			$Monto *= -1;
		elseif ($oCajaGestoria->IdTipoMovimiento == TiposMovimientosCaja::Ingreso && $Monto < 0)
			$Monto *= -1;
		$oCajaGestoria->Fecha	 			= $Fecha;
		$oCajaGestoria->Monto			 	= $Monto;
		$oCajaGestoria->Observaciones		= $Observaciones;
		$oCajaGestoria->Disponible			= 0;
		
		$oCajaGestoria = $oCajasGestoria->Update($oCajaGestoria);

		header("Location: cajasgestoria.php" . $strParams);
		exit();
	}
}
else
{
	$Fecha = CambiarFecha($oCajaGestoria->Fecha);
	$Monto = $oCajaGestoria->Monto;
	$Observaciones = $oCajaGestoria->Observaciones;
	$IdTipoMovimiento = $oCajaGestoria->IdTipoMovimiento;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Caja Gestor&iacute;a - Modificar</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="IdCajaGestoria" id="IdCajaGestoria" value="<?= $IdCajaGestoria ?>" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
					  	<tr>
							<td class="bordeGris">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									
									<tr>
										<td><div align="right">Fecha:</div></td>
										<td>
                                        	<div align="left">
                                                 <input name="Fecha" type="text" class="camporFormularioMediano" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                </script>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione una fecha</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Tipo de Movimiento:</div></td>
										<td>
                                        	<div align="left">
                                                <select name="IdTipoMovimiento" class="camporFormularioMediano" id="IdTipoMovimiento">
													<option value="">[Seleccione tipo de movimiento]</option>
													<?php
													foreach (TiposMovimientosCaja::GetAll() as $oTipoMovimiento)
													{
														if ($oTipoMovimiento['IdTipo'] == TiposMovimientosCaja::Ingreso || $oTipoMovimiento['IdTipo'] == TiposMovimientosCaja::Egreso)
														{
															$selected = '';
															if ($IdTipoMovimiento == $oTipoMovimiento['IdTipo'])	
																$selected = 'selected="selected"';
													?>
													<option value="<?= $oTipoMovimiento['IdTipo'] ?>" <?= $selected ?>><?= $oTipoMovimiento['Descripcion'] ?></option>
													<?php
														}
													}
													?>
												</select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione el tipo de movimiento</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Monto:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="Monto" id="Monto" class="camporFormularioChico" maxlength="128" value="<?=$Monto?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span></div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un Monto mayor distinto a 0.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Observaciones:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <textarea name="Observaciones" id="Observaciones" class="camporFormularioSimple" style="height: 50px"><?=$Observaciones?></textarea>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									
								</table>
						  	</td>
						</tr>
						<tr>
							<td height="1"><div align="center"></div></td>
					  </tr>
					</table>
			  		<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cajasgestoria.php<?=$strParams?>';" value="Cancelar" />
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