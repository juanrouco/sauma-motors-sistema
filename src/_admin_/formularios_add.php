<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FORM_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdTipoFormulario	= intval($_REQUEST['IdTipoFormulario']);
$NumeroDesde		= strval($_REQUEST['NumeroDesde']);
$NumeroHasta		= strval($_REQUEST['NumeroHasta']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oFormulario 		= new Formulario();
$oFormularios		= new Formularios();
$oTiposFormulario 	= new TiposFormulario();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos correspondientes al tipo de formulario */
$oTipoFormulario = $oTiposFormulario->GetById($IdTipoFormulario);

if ($Submit)
{
	/* validaciones... */
	if ($NumeroDesde == '')
		$err |= 1;
	if ($NumeroHasta == '')
		$err |= 2;
	if ($NumeroDesde > $NumeroHasta)
		$err |= 4;

	/* verificamos que no se solapen numeros */
	if ($err == 0)
	{
		$arrLote = $oFormularios->CheckLoteLibre($IdTipoFormulario, $NumeroDesde, $NumeroHasta);
		
		if ((is_numeric($arrLote['MinimoUtilizado'])) && (is_numeric($arrLote['MaximoUtilizado'])))
			$err |= 8;
	}

	/* si no hay errores... */
	if ($err == 0)
	{
		if ($oFormularios->CreateLote($IdTipoFormulario, $NumeroDesde, $NumeroHasta))
		{
			header("Location: formularios.php" . $strParams);
			exit();
		}
	}
}
else
{
	$oProximoFormulario = $oFormularios->GetNextCargaLote($IdTipoFormulario);
	
	$NumeroDesde = $oProximoFormulario->Numero;
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
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Formularios - Agregar lote de <?=$oTipoFormulario->Descripcion?></span></td>
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
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero Desde:</div></td>
										<td>
											<input type="text" name="NumeroDesde" id="NumeroDesde" class="camporFormularioMediano" maxlength="8" onkeyup="javascript: StrToUpper(this.id);" value="<?=$NumeroDesde?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nro. inicial</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">N&uacute;mero Hasta:</div></td>
										<td>
											<input type="text" name="NumeroHasta" id="NumeroHasta" class="camporFormularioMediano" maxlength="8" onkeyup="javascript: StrToUpper(this.id);" value="<?=$NumeroHasta?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
                                    <tr>
                                        <td height="20"><div align="right"></div></td>
                                        <td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nro. final</li><?php } ?></td>
                                    </tr>
                                    <?php if ($err & 4) { ?>
                                    <tr>
                                        <td height="20"><div align="right"></div></td>
                                        <td height="20" align="left"><li style="color:#FF0000;">Nro. inicial debe ser inferior a nro. final</li></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if ($err & 8) { ?>
                                    <tr>
                                        <td height="20"><div align="right"></div></td>
                                        <td height="20" align="left"><li style="color:#FF0000;">Ya existe un lote entre <?=$arrLote['MinimoUtilizado']?> y <?=$arrLote['MaximoUtilizado']?></li></td>
                                    </tr>
                                    <?php } ?>

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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'formularios.php<?=$strParams?>';" value="Cancelar" />
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