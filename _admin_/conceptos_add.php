<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_COMPRA_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page	= intval($_REQUEST['Page']);
$Nombre	= $_REQUEST['Nombre'];
$Submit	= $_REQUEST['Submitted'];

/* declaracion de variables */
$err	= 0;
$oConcepto = new Concepto();
$oConceptos	= new Conceptos();
$NombreImagen = '';

$strParams = '';
$strParams.= '?Page=' 			. $Page;
$strParams.= '&FilterNombre='	. $_REQUEST['FilterNombre'];

if ($Submit)
{
	/* validaciones... */
	if (trim($Nombre) == '')
		$err += 1;
	else
	{
		$oConceptosAux = $oConceptos->GetByNombre($Nombre);
		
		if ($oConceptosAux)
		{		
			foreach ($oConceptosAux as $oConceptoAux)
			{
				if ($oConceptoAux->Nombre == $Nombre)
					$err += 2;
			}
		}
	}

	/* si no hay errores... */
	if ($err == 0)
	{		
			$oConcepto->Nombre 	= $Nombre;

			$oConceptos->Create($oConcepto);
			
			header("Location: conceptos.php" . $strParams);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Concepto - Agregar</span></td>
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
										<td>Concepto:</td>
										<td>
											<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" value="<?=$Nombre?>">
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
								<?php if ($err & 1) { ?>
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><li style="color:#FF0000;">Ingrese el nombre del concepto</li></td>
                                    </tr>
								<?php } ?>
								<?php if ($err & 2) { ?>
                                	<tr>
                                    	<td>&nbsp;</td>
                                        <td><li class="error">Ya existe registrado el nombre del concepto</li></td>
                                    </tr>
								<?php } ?>								
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="50%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'conceptos.php<?=$strParams?>';" value="Cancelar" />
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