<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_USUA_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUsuario		= strval($_REQUEST['IdUsuario']);
$IdUbicacion	= strval($_REQUEST['IdUbicacion']);
$Nombre			= strval($_REQUEST['Nombre']);
$Apellido		= strval($_REQUEST['Apellido']);
$Email			= strval($_REQUEST['Email']);
$IdSector		= intval($_REQUEST['IdSector']);
$IdPerfil		= intval($_REQUEST['IdPerfil']);
$Login			= strval($_REQUEST['Login']);
$Password		= strval($_REQUEST['Password']);
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oSectores		= new Sectores();
$oUsuarios		= new Usuarios();
$oPerfiles		= new Perfiles();
$oUbicaciones	= new Ubicaciones();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oUsuario = $oUsuarios->GetById($IdUsuario))
{
	header('Location: usuarios.php' . $strParams);
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	if ($Apellido == '')
		$err |= 2;
	if ($IdSector == '')
		$err |= 4;
	if ($IdPerfil == '')
		$err |= 8;
	if ($Password == '')
		$err |= 16;

	/* si no hay ningun error... */	
	if ($err == 0)
	{			
		$oUsuario->Nombre 		= $Nombre;
		$oUsuario->Apellido 	= $Apellido;
		$oUsuario->Email 		= $Email;
		$oUsuario->IdUbicacion = $IdUbicacion;
		$oUsuario->IdSector 	= $IdSector;
		$oUsuario->IdPerfil 	= $IdPerfil;
	
		$oUsuario = $oUsuarios->Update($oUsuario);
		
		if ($Password != '**********' && md5($Password) != $Usuario->Password)
		{
			$oUsuario->Password = $Password;

			$oUsuario = $oUsuarios->ChangePassword($oUsuario);
		}		

		header("Location: usuarios.php" . $strParams);
		exit();
	}
}
else
{
	$Nombre 		= $oUsuario->Nombre;
	$Apellido 		= $oUsuario->Apellido;
	$Email 			= $oUsuario->Email;
	$IdUbicacion 	= $oUsuario->IdUbicacion;
	$IdSector 		= $oUsuario->IdSector;
	$IdPerfil 		= $oUsuario->IdPerfil;
	$Usuario 		= $oUsuario->Usuario;
	$Password 		= '**********';
}

/* obtenemos listado de perfiles */
$arrPerfiles = $oPerfiles->GetAll();

/* obtenemos listado de perfiles */
$arrSectores = $oSectores->GetAll();

/* obtenemos listado de ubicaciones */
$arrUbicaciones = $oUbicaciones->GetAll();

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Usuarios - Modificar</span></td>
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
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  		<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="1" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Nombre:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Nombre?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Apellido:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Apellido" id="Apellido" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Apellido?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                         	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el apellido</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Email:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" autocomplete="off" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email?>" />
                                           	</div>
                                       	</td>
									</tr>
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Sucursal:</div></td>
										<td>
                                        	<div align="left">
                                                <select name="IdUbicacion" id="IdUbicacion" class="camporFormularioSimple">
                                                    <option value="">[Seleecione]</option>
                                                    <?php foreach ($arrUbicaciones as $oUbicacion) { ?>
                                                    <option value="<?=$oUbicacion->IdUbicacion?>" <?=($oUbicacion->IdUbicacion == $IdUbicacion) ? 'selected="selected"' : ''?> ><?=$oUbicacion->Nombre?></option>
                                                    <?php } ?>
                                                </select>
                                          	</div>
                                       	</td>
									</tr>
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Sector:</div></td>
										<td>
                                        	<div align="left">
                                                <select name="IdSector" id="IdSector" class="camporFormularioSimple">
                                                    <option value="">[Seleecione]</option>
                                                    <?php foreach ($arrSectores as $oSector) { ?>
                                                    <option value="<?=$oSector->IdSector?>" <?=($oSector->IdSector == $IdSector) ? 'selected="selected"' : ''?> ><?=$oSector->Nombre?></option>
                                                    <?php } ?>
                                                </select>
                                                &nbsp;<input type="button" id="btnAddSector" class="botonBasico"  onClick="javascript:AddSector();" value=" + " />
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">seleccione el sector</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Perfil:</div></td>
										<td>
                                        	<div align="left">
                                                <select name="IdPerfil" id="IdPerfil" class="camporFormularioSimple">
                                                    <option value="">[Seleecione]</option>
                                                    <?php foreach ($arrPerfiles as $oPerfil) { ?>
                                                    <option value="<?=$oPerfil->IdPerfil?>" <?=($oPerfil->IdPerfil == $IdPerfil) ? 'selected="selected"' : ''?> ><?=$oPerfil->Nombre?></option>
                                                    <?php } ?>
                                                </select>
                                                &nbsp;<input type="button" id="btnAddPerfil" class="botonBasico"  onClick="javascript:AddPerfil();" value=" + " />
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 8) { ?><li style="color:#FF0000;">seleccione el perfil</li><?php } ?></td>
									</tr>
									<tr>
										<td height="25"><div align="right">Login:</div></td>
										<td height="25"><div align="left"><b><?=$oUsuario->Login?></b></div></td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
                                    <tr>
                                    	<td><div align="right">Nueva contrase&ntilde;a:</div></td>
                                        <td>
                                        	<div align="left">
		                                        <input type="password" name="Password" id="Password" class="camporFormularioSimple" value="<?=$Password?>" />
                                          	</div>
                                       	</td>
                                    </tr>                                    	                                
                                	<tr>
                                    	<td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 16) { ?><li class="error">Ingrese una contrase&ntilde;a</li><?php } ?></td>
                                    </tr>
								</table>							
                           	</td>
						</tr>
					</table>
	            	<table width="70%" border="0" cellspacing="0" cellpadding="0">
                      	<tr>
                        	<td height="1"><div align="center"></div></td>
                      	</tr>
                    </table>
      				<table width="70%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
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