<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MODU_PERMS))
	Session::NoPerm();

/* obtiene datos enviados */
$IdModulo 	= intval($_REQUEST['IdModulo']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaramos variables necesarias */
$err				= 0;
$oModulos			= new Modulos();
$oModuloPermisos 	= new ModuloPermisos();
$oPermisos			= new Permisos();

/* armamos la cadena a mandar con parametros */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos del registro */
if (!$oModulo = $oModulos->GetById($IdModulo))
{
	header('Location: modulos.php' . $strParams);
	exit;
}

/* obtenemos todos los permisos existentes */
$arrPermisos = $oPermisos->GetAll();

/* obtenenmos el ultimo id de permiso */
$LastPermId = $oPermisos->GetLastInsertId();

/* si el formulario fue enviado... */
if ($Submit)
{
	/* eliminamos todos los permisos asignados anteriormente */
	$oModulo->DeleteAllPermisos();

	/* guardamos los permisos asignados */
	for ($IdPermiso=1; $IdPermiso<=$LastPermId; $IdPermiso++)
	{
		$Permiso = (isset($_REQUEST['Perm_' . $IdPermiso])) ? $_REQUEST['Perm_' . $IdPermiso] : '';

		if ($Permiso)
		{
			$oModuloPermiso = new ModuloPermiso();
			$oModuloPermiso->IdModulo	= $IdModulo;
			$oModuloPermiso->IdPermiso	= $IdPermiso;
			
			$oModuloPermiso = $oModuloPermisos->Create($oModuloPermiso);
		}
	}
	
	header('Location: modulos.php' . $strParams);
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function CheckPerms(Checked)
{
	var AllPermsSelect 	= Get('AllPermsSelect');
	var LastPermId		= '<?=$LastPermId?>';
	
	for (var i=1; i<=LastPermId; i++)
	{
		var Perm = Get('Perm_' + i);

		if (Perm) Perm.checked = Checked;
	}
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
	<input type="hidden" name="Submitted" id="Submitted" value="1" />
	<input type="hidden" name="IdModulo" id="IdModulo" value="<?=$IdModulo?>" />

	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Administraci&oacute;n de M&oacute;dulos - Permisos del Módulo '<?=$oModulo->Nombre?>'</span></td>
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
					<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td>
								<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
									<tr class="bordeGrisFondo">
										<td height="25"><div id="margen"><strong>Permiso</strong></div></td>
										<td height="25"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
										<td>
											<input type="checkbox" name="AllPerms" id="AllPerms" onclick="javascript: CheckPerms(this.checked)" />
										</td>
									</tr>
									
								<?php foreach ($arrPermisos as $oPermiso) { ?>
									
									<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
										<td height="25"><div id="margen"><?=$oPermiso->Nombre?></div></td>
										<td height="25"><div id="margen"><?=$oPermiso->Descripcion?></div></td>
										<td>
											<input type="checkbox" name="Perm_<?=$oPermiso->IdPermiso?>" id="Perm_<?=$oPermiso->IdPermiso?>" <?=($oModuloPermisos->GetById($IdModulo, $oPermiso->IdPermiso)) ? 'checked' : ''?> value="1" />
										</td>
									</tr>
									<tr>
										<td colspan="6">
                                        	<div align="center">
                                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                    </tr>
                                                </table>
											</div>
                                      	</td>
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
					<table width="75%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'modulos.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>

</body>
</html>