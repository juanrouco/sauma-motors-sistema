<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CONT_UPDATE))
	Session::NoPerm();

/* obtenemos los datos del usuario logeado */
$oCurrentUser = Session::GetCurrentUser();

/* obtiene datos del formulario */
$ContraseniaActual		= trim(strval($_REQUEST['ContraseniaActual']));
$Contrasenia			= trim(strval($_REQUEST['Contrasenia']));
$ConfirmarContrasenia	= trim(strval($_REQUEST['ConfirmarContrasenia']));
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oUsuarios	= new Usuarios();

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($ContraseniaActual == '')
		$err |= 1;
	elseif (!$oUsuarios->GetByCredentials($oCurrentUser->Login, $ContraseniaActual))
		$err |= 2;
	if ($Contrasenia == '')
		$err |= 4;
	if ($ConfirmarContrasenia == '')
		$err |= 8;
	elseif ($Contrasenia != $ConfirmarContrasenia)
		$err |= 16;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oCurrentUser->Password = $Contrasenia;

		/* modificamos el usuario */
		if ($oUsuarios->ChangePassword($oCurrentUser))
		{
			header("Location: cuenta_constrasenia_do.php");
			exit();
		}
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuenta - Cambio de contrase&ntilde;a</span></td>
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
                <form name="frmData" id="frmData" method="post">
                    <input type="hidden" name="Submitted" id="Submitted" value="1">
    
                    <table width="50%"  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Contrase&ntilde;a actual:</div></td>
                                        <td>
                                            <div align="left">
                                            	<input type="password" name="ContraseniaActual" id="ContraseniaActual" class="camporFormularioSimple" value="<?=$ContraseniaActual;?>" />
                                            	<span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>     
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la contrase&ntilde;a actual</li><?php } ?><?php if ($err & 2) { ?><li style="color:#FF0000;">La contrase&ntilde;a ingresada no pertenece al usuario logueado</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Contrase&ntilde;a nueva:</div></td>
                                        <td>
                                            <div align="left">
                                            	<input type="password" name="Contrasenia" id="Contrasenia" class="camporFormularioSimple" value="<?=$Contrasenia;?>" />
                                            	<span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>     
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la contrase&ntilde;a</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Confirmar contrase&ntilde;a:</div></td>
                                        <td>
                                            <div align="left">
                                            	<input type="password" name="ConfirmarContrasenia" id="ConfirmarContrasenia" class="camporFormularioSimple" value="<?=$ConfirmarContrasenia;?>" />
                                            	<span style="color:#FF0000;">&nbsp;(*)</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Confirme la contrase&ntilde;a</li><?php } ?><?php if ($err & 16) { ?><li style="color:#FF0000;">Las contrase&ntilde;as no coinciden</li><?php } ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="1"><div align="center"></div></td>
                        </tr>
                    </table>
                    <table width="50%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                        <tr>
                            <td height="30">
                                <div align="center">
                                    <input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
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