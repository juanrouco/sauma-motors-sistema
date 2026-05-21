<?php

require_once('../inc_library.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_USUA_UPDATE))
	Session::NoPerm();

$IdUsuario	= strval($_REQUEST['IdUsuario']);
$Submit		= (isset($_REQUEST['Submitted']));

$err				= 0;
$oUsuarios			= new Usuarios();
$oUsuarioJornadas 	= new UsuarioJornadas();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oUsuario = $oUsuarios->GetById($IdUsuario))
{
	header('Location: usuarios.php' . $strParams);
	exit;
}

if ($Submit)
{
	$oUsuarioJornadas->DeleteByIdUsuario($oUsuario->IdUsuario);
	DBAccess::$db->Begin();
	for ($Count = 1; $Count <= 7; $Count++)
	{
		$checked = $_REQUEST['cb_' . $Count];
		
		if ($checked)
		{
			$HoraInicio = $_REQUEST['HoraInicio_' . $Count] . ':' . $_REQUEST['MinutoInicio_' . $Count];
			$HoraFin = $_REQUEST['HoraFin_' . $Count] . ':' . $_REQUEST['MinutoFin_' . $Count];
			$HoraAlmuerzoInicio = $_REQUEST['HoraAlmuerzoInicio_' . $Count] . ':' . $_REQUEST['MinutoAlmuerzoInicio_' . $Count];
			$HoraAlmuerzoFin = $_REQUEST['HoraAlmuerzoFin_' . $Count] . ':' . $_REQUEST['MinutoAlmuerzoFin_' . $Count];
			
			$oUsuarioJornada = new UsuarioJornada();
			$oUsuarioJornada->IdUsuario 			= $oUsuario->IdUsuario;
			$oUsuarioJornada->DiaSemana 			= $Count;
			$oUsuarioJornada->HoraInicio 			= $HoraInicio;
			$oUsuarioJornada->HoraFin 				= $HoraFin;
			$oUsuarioJornada->HoraAlmuerzoInicio 	= $HoraAlmuerzoInicio;
			$oUsuarioJornada->HoraAlmuerzoFin 		= $HoraAlmuerzoFin;
			
			$oUsuarioJornadas->Create($oUsuarioJornada);
		}
	}
	DBAccess::$db->Commit();
	
	header("Location: usuarios.php" . $strParams);
	exit();
}
else
{
	$Nombre 	= $oUsuario->Nombre;
	$Apellido 	= $oUsuario->Apellido;
	$Email 		= $oUsuario->Email;
	$IdSector 	= $oUsuario->IdSector;
	$IdPerfil 	= $oUsuario->IdPerfil;
	$Usuario 	= $oUsuario->Usuario;
	$Password 	= '**********';
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Horarios del Usuario <?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></span></td>
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
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="IdUsuario" id="IdUsuario" value="<?=$IdUsuario?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  		<tr>
							<td class="bordeGris">
								<table width="100%"  border="0" align="center" cellpadding="1" cellspacing="0">
									<tr>
										<?php
										for ($Count = 1; $Count <= 7; $Count++)
										{
											$class = '';
											if ($Count % 2 == 1)
												$class = 'class="bordeGrisFondo"';
												
											switch($Count)
											{
												case 1:	$dia = 'Lunes';break;
												case 2:	$dia = 'Martes';break;
												case 3:	$dia = 'Miercoles';break;
												case 4:	$dia = 'Jueves';break;
												case 5:	$dia = 'Viernes';break;
												case 6:	$dia = 'S&aacute;bado';break;
												case 7:	$dia = 'Domingo';break;
											}
											
											$oUsuarioJornada = $oUsuarioJornadas->GetByIdUsuarioAndDiaSemana($oUsuario->IdUsuario, $Count);
											$checked = '';
											$HoraInicio = '';
											$MinutoInicio = '';
											$HoraAlmuerzoInicio = '';
											$MinutoAlmuerzoInicio = '';
											$HoraAlmuerzoFin = '';
											$MinutoAlmuerzoFin = '';
											$HoraFin = '';
											$MinutoFin = '';
											if ($oUsuarioJornada)
											{
												$checked = 'checked="checked"';
												
												$HI = explode(':', $oUsuarioJornada->HoraInicio);
												$HoraInicio = $HI[0];
												$MinutoInicio = $HI[1];
												
												$HAI = explode(':', $oUsuarioJornada->HoraAlmuerzoInicio);
												$HoraAlmuerzoInicio = $HAI[0];
												$MinutoAlmuerzoInicio = $HAI[1];
												
												$HAF = explode(':', $oUsuarioJornada->HoraAlmuerzoFin);
												$HoraAlmuerzoFin = $HAF[0];
												$MinutoAlmuerzoFin = $HAF[1];
												
												$HF = explode(':', $oUsuarioJornada->HoraFin);
												$HoraFin = $HF[0];
												$MinutoFin = $HF[1];
											}
										?>
										<td width="14%" <?= $class ?>>
											<table width="100%" border="0" cellpadding="0">
												<tr>
													<td><div align="center"><?= $dia ?></div></td>
												</tr>
												<tr>
													<td><div align="center"><input type="checkbox" id="cb_<?= $Count ?>" name="cb_<?= $Count ?>" <?= $checked ?> /></div></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><div align="center">Horario Inicio</div></td>
												</tr>
												<tr>
													<td>
														<div align="center">
															<select id="HoraInicio_<?= $Count ?>" name="HoraInicio_<?= $Count ?>">
															<?php
																for ($Hora = 0; $Hora < 24; $Hora++)
																{
																	$selected = '';
																	if ($Hora == intval($HoraInicio))
																		$selected = 'selected="selected"';
																	
															?>
																<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select> : <select id="MinutoInicio_<?= $Count ?>" name="MinutoInicio_<?= $Count ?>">
															<?php
																for ($Minuto = 0; $Minuto < 60; $Minuto++)
																{
																	$selected = '';
																	if ($Minuto == intval($MinutoInicio))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select>
														</div>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><div align="center">Horario Almuerzo Inicio</div></td>
												</tr>
												<tr>
													<td>
														<div align="center">
															<select id="HoraAlmuerzoInicio_<?= $Count ?>" name="HoraAlmuerzoInicio_<?= $Count ?>">
															<?php
																for ($Hora = 0; $Hora < 24; $Hora++)
																{
																	$selected = '';
																	if ($Hora == intval($HoraAlmuerzoInicio))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select> : <select id="MinutoAlmuerzoInicio_<?= $Count ?>" name="MinutoAlmuerzoInicio_<?= $Count ?>">
															<?php
																for ($Minuto = 0; $Minuto < 60; $Minuto++)
																{
																	$selected = '';
																	if ($Minuto == intval($MinutoAlmuerzoInicio))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select>
														</div>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><div align="center">Horario Almuerzo Fin</div></td>
												</tr>
												<tr>
													<td>
														<div align="center">
															<select id="HoraAlmuerzoFin_<?= $Count ?>" name="HoraAlmuerzoFin_<?= $Count ?>">
															<?php
																for ($Hora = 0; $Hora < 24; $Hora++)
																{
																	$selected = '';
																	if ($Hora == intval($HoraAlmuerzoFin))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select> : <select id="MinutoAlmuerzoFin_<?= $Count ?>" name="MinutoAlmuerzoFin_<?= $Count ?>">
															<?php
																for ($Minuto = 0; $Minuto < 60; $Minuto++)
																{
																	$selected = '';
																	if ($Minuto == intval($MinutoAlmuerzoFin))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select>
														</div>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td><div align="center">Horario Fin</div></td>
												</tr>
												<tr>
													<td>
														<div align="center">
															<select id="HoraFin_<?= $Count ?>" name="HoraFin_<?= $Count ?>">
															<?php
																for ($Hora = 0; $Hora < 24; $Hora++)
																{
																	$selected = '';
																	if ($Hora == intval($HoraFin))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select> : <select id="MinutoFin_<?= $Count ?>" name="MinutoFin_<?= $Count ?>">
															<?php
																for ($Minuto = 0; $Minuto < 60; $Minuto++)
																{
																	$selected = '';
																	if ($Minuto == intval($MinutoFin))
																		$selected = 'selected="selected"';
															?>
																<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
															<?php
																}
															?>
															</select>
														</div>
													</td>
												</tr>
											</table>
										</td>
										<?php
										}
										?>																				
									</tr>                                	
								</table>							
                           	</td>
						</tr>
					</table>
	            	<table width="90%" border="0" cellspacing="0" cellpadding="0">
                      	<tr>
                        	<td height="20"><div align="center"></div></td>
                      	</tr>
                    </table>
      				<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'usuarios.php<?=$strParams?>';" value="Cancelar" />
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