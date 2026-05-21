<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GESTOR_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdGestor		= intval($_REQUEST['IdGestor']);
$RazonSocial	= strval($_REQUEST['RazonSocial']);
$Email			= strval($_REQUEST['Email']);
$Telefono		= strval($_REQUEST['Telefono']);
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oGestores	= new Gestores();


/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oGestor = $oGestores->GetById($IdGestor))
{
	header('Location: gestores.php' . $strParams);
	exit;
}


if ($Submit)
{
	/* validaciones... */
	if ($RazonSocial == '')
		$err |= 1;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oGestor->RazonSocial 	= $RazonSocial;
		$oGestor->Email 		= $Email;
		$oGestor->Telefono 		= $Telefono;
		
		$oGestor = $oGestores->Update($oGestor);

		header("Location: gestores.php" . $strParams);
		exit();
	}
}
else
{
	$RazonSocial 	= $oGestor->RazonSocial;
	$Email 		= $oGestor->Email;
	$Telefono 		= $oGestor->Telefono;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestores - Modificar</span></td>
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
					<input type="hidden" name="IdGestor" id="IdGestor" value="<?= $IdGestor ?>" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
					  	<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Nombre:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="RazonSocial" id="RazonSocial" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$RazonSocial?>" />
												<span style="color:#FF0000;">&nbsp;(*)</span>										
                                           	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Email:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email?>" />
                                           	</div>
                                       	</td>
									</tr>
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Telefono:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="Telefono" id="Telefono" class="camporFormularioSimple" maxlength="128" value="<?=$Telefono?>" />
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'gestores.php';" value="Cancelar" />
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