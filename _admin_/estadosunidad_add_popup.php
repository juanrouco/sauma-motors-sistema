<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ESTU_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Codigo	= strval($_REQUEST['Codigo']);
$Nombre	= strval($_REQUEST['Nombre']);
$Color	= strval($_REQUEST['Color']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oEstadoUnidad 	= new EstadoUnidad();
$oEstadosUnidad	= new EstadosUnidad();

if ($Submit)
{
	/* validaciones... */
	if ($Codigo == '')
		$err |= 1;
	if ($Nombre == '')
		$err |= 2;
	elseif ($oEstadosUnidad->GetByNombre($Nombre))
		$err |= 4;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oEstadoUnidad->Codigo 	= $Codigo;
		$oEstadoUnidad->Nombre 	= $Nombre;
		$oEstadoUnidad->color 	= $Color;
		
		$oEstadoUnidad = $oEstadosUnidad->Create($oEstadoUnidad);
		
		$Create = true;
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
<script type="text/javascript" language="javascript" src="../js/colorPicker.js"></script>
</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Estados de Unidades - Agregar</span></td>
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
					<input type="hidden" name="Submitted" id="Submitted" value="1" />

					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">C&oacute;digo:</div></td>
										<td>
											<input type="text" name="Codigo" id="Codigo" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Codigo?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el c&oacute;digo</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">Nombre:</div></td>
										<td>
											<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Nombre?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?><?php if ($err & 4) { ?><li class="error">Ya existe registrado el nombre</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="226"><div align="right">Color:</div></td>
                                        <td>
                                            <input type="text" id="Color" name="Color" onclick="startColorPicker(this, 'ColorSelector')" onkeyup="maskedHex(this)" class="camporFormularioSimple" value="<?=$Color?>" <?php if (!empty($Color)) { ?> style="background:<?=$Color?>; color:#FFF;" <?php } ?> readonly="readonly" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="226">&nbsp;</td>
                                        <td>
                                            <span><strong>(Haga clic en el campo para seleccionar el color)</strong></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                            <div id="ColorSelector"></div>
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
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="80%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.close();" value="Cancelar" />
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

<?php if ($Create) { ?>
<script language="javascript">
window.opener.FilterEstado('<?=$oEstadosUnidad->IdEstado?>', '');
window.close();
</script>
<?php } ?>

</body>
</html>