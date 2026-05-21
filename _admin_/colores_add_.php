<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_COLO_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Codigo	= strval($_REQUEST['Codigo']);
$Nombre	= strval($_REQUEST['Nombre']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$NombreImagen 	= '';
$oColor 		= new Color();
$oColores		= new Colores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Codigo == '')
		$err |= 4;
	if ($Nombre == '')
		$err |= 1;
	elseif ($oColores->GetByNombre($Nombre))
		$err |= 2;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$oColor->Codigo = $Codigo;
		$oColor->Nombre = $Nombre;
		
		$oColor = $oColores->Create($oColor);

		header("Location: colores.php" . $strParams);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Colores - Agregar</span></td>
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
				<form name="frmData" id="frmData" action="colores_add.php<?=$strParams?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="Submitted" id="Submitted" value="1">
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
					
					<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">C&oacute;digo:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Codigo" id="Codigo" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Codigo?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>
                                          	</div>
                                        </td>
									</tr>
								<?php if ($err & 4) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                        	<div class="errorCampoSimple">
	                                            <span>Ingrese el c&oacute;digo del color</span>
                                        	</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>&nbsp;</td>
                                    	<td>&nbsp;</td>
                                    </tr>
                                <?php } ?>
									<tr>
										<td>Color:</td>
										<td>
											<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Nombre?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
                                        </td>
									</tr>
								<?php if ($err & 1) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                        	<div class="errorCampoSimple">
	                                            <span>Ingrese el nombre del color</span>
                                        	</div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($err & 2) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                        	<div class="errorCampoSimple">
	                                            <span>Ya existe registrado el nombre del color</span>
                                        	</div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>							
                        	</td>
						</tr>
					</table>
			        <table width="50%" border="0" cellspacing="0" cellpadding="0">
                      	<tr>
                        	<td height="1"><div align="center"></div></td>
                      	</tr>
                    </table>
      				<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'colores.php<?=$strParams?>';" value="Cancelar" />
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