<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PERF_PERMS))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPerfil 	= intval($_REQUEST['IdPerfil']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaramos variables necesarias */
$err				= 0;
$oPerfiles			= new Perfiles();
$oPerfilPermisos 	= new PerfilPermisos();
$oPerfilModulos 	= new PerfilModulos();
$oPermisos			= new Permisos();
$oModulos			= new Modulos();

/* armamos la cadena a mandar con parametros */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos del registro */
if (!$oPerfil = $oPerfiles->GetById($IdPerfil))
{
	header('Location: perfiles.php' . $strParams);
	exit;
}

/* obtenemos todos los permisos existentes */
$arrPermisos = $oPermisos->GetAll();

/* obtenemos todos los grupos existentes */
$arrModulos = $oModulos->GetAll();

/* obtenenmos el ultimo id de permiso */
$LastPermId = $oPermisos->GetLastInsertId();

/* obtenenmos el ultimo id de grupo */
$LastModuloId = $oModulos->GetLastInsertId();

/* si el formulario fue enviado... */
if ($Submit)
{
	/* eliminamos todos los permisos asignados anteriormente */
	$oPerfil->DeleteAllPermisos();

	/* eliminamos todos los grupos asignados anteriormente */
	$oPerfil->DeleteAllModulos();

	/* guardamos los permisos asignados */
	for ($IdPermiso=1; $IdPermiso<=$LastPermId; $IdPermiso++)
	{
		$Permiso = (isset($_REQUEST['Perm_' . $IdPermiso])) ? $_REQUEST['Perm_' . $IdPermiso] : '';

		if ($Permiso)
		{
			$oPerfilPermiso = new PerfilPermiso();
			$oPerfilPermiso->IdPerfil	= $IdPerfil;
			$oPerfilPermiso->IdPermiso	= $IdPermiso;
			
			$oPerfilPermiso = $oPerfilPermisos->Create($oPerfilPermiso);
		}
	}

	/* guardamos los grupos asignados */
	for ($IdModulo=1; $IdModulo<=$LastModuloId; $IdModulo++)
	{
		$Modulo = (isset($_REQUEST['Modulo_' . $IdModulo])) ? $_REQUEST['Modulo_' . $IdModulo] : '';

		if ($Modulo)
		{
			$oPerfilModulo = new PerfilModulo();
			$oPerfilModulo->IdPerfil	= $IdPerfil;
			$oPerfilModulo->IdModulo	= $IdModulo;
			
			$oPerfilModulo = $oPerfilModulos->Create($oPerfilModulo);
		}
	}
	
	header('Location: perfiles.php' . $strParams);
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function CheckModulos(Checked)
{
	var AllModulosSelect 	= Get('AllModulosSelect');
	var AllPerms			= Get('AllPerms');
	var LastModuloId		= '<?=$LastModuloId?>';
		
	for (var i=1; i<=LastModuloId; i++)
	{
		var Modulo = Get('Modulo_' + i);

		if (Modulo)			
			Modulo.checked = Checked;
	}
	
	CheckPerms(Checked);
	AllPerms.checked = Checked;
}

function CheckPerms(Checked)
{
	var AllPermsSelect 	= Get('AllPermsSelect');
	var LastPermId		= '<?=$LastPermId?>';
	
	for (var i=1; i<=LastPermId; i++)
	{
		var Perm = Get('Perm_' + i);

		if (Perm)			
			Perm.checked = Checked;
	}
}

function ClickModulo(IdModulo, Checked)
{
	var arr = new Array();
	var obj;
	var oModuloPermisos;
				
	arr['IdModulo'] = IdModulo;
	obj = SendXMLRequest('ModuloPermisos', 'GetAll', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oModuloPermisos = obj.Response.Rows;
	
	for (var i=0; oModuloPermisos && i<oModuloPermisos.length; i++)
	{
		var oModuloPermiso = oModuloPermisos[i];
	
		var Perm = Get('Perm_' + oModuloPermiso.IdPermiso);

		if (Perm) Perm.checked = Checked;
	}	
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
	<input type="hidden" name="Submitted" id="Submitted" value="1" />
	<input type="hidden" name="IdPerfil" id="IdPerfil" value="<?=$IdPerfil?>" />

	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Administraci&oacute;n de Perfiles - Permisos de accesos para '<?=$oPerfil->Nombre?>'</span></td>
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
										<td height="25"><div id="margen"><strong>M&oacute;dulo</strong></div></td>
										<td>
                                        	<div align="right">
												<input type="checkbox" name="AllModulos" id="AllModulos" onclick="javascript: CheckModulos(this.checked)" />
                                           	</div>
										</td>
									</tr>
									
								<?php foreach ($arrModulos as $oModulo) { ?>
									
									<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
										<td height="25"><div id="margen"><?=$oModulo->Nombre?></div></td>
										<td>
                                        	<div align="right">
												<input type="checkbox" name="Modulo_<?=$oModulo->IdModulo?>" id="Modulo_<?=$oModulo->IdModulo?>" <?=($oPerfilModulos->GetById($IdPerfil, $oModulo->IdModulo)) ? 'checked' : ''?> value="1" onclick="javascript: ClickModulo('<?=$oModulo->IdModulo?>', this.checked);" />
                                          	</div>
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
					<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td>
								<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
									<tr class="bordeGrisFondo">
										<td height="25"><div id="margen"><strong>Permiso</strong></div></td>
										<td height="25"><div id="margen"><strong>Descripci&oacute;n</strong></div></td>
										<td>
                                        	<div align="right">
												<input type="checkbox" name="AllPerms" id="AllPerms" onclick="javascript: CheckPerms(this.checked)" />
                                          	</div>
										</td>
									</tr>
									
								<?php foreach ($arrPermisos as $oPermiso) { ?>
									
									<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
										<td height="25"><div id="margen"><?=$oPermiso->Nombre?></div></td>
										<td height="25"><div id="margen"><?=$oPermiso->Descripcion?></div></td>
										<td>
                                        	<div align="right">
												<input type="checkbox" name="Perm_<?=$oPermiso->IdPermiso?>" id="Perm_<?=$oPermiso->IdPermiso?>" <?=($oPerfilPermisos->GetById($IdPerfil, $oPermiso->IdPermiso)) ? 'checked' : ''?> value="1" />
                                           	</div>
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
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'perfiles.php<?=$strParams?>';" value="Cancelar" />
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