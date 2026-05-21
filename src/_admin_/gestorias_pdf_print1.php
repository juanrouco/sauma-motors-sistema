<?php

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 		= intval($_REQUEST['IdFormulario']);
$IdTipoFormulario 	= intval($_REQUEST['IdTipoFormulario']);

$oFormularios = new Formularios();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();

$OffsetX = '0.00';
$OffsetY = '0.00';

header('Location: gestorias_pdf_print2.php?IdFormulario=' . $IdFormulario . '&IdTipoFormulario=' . $IdTipoFormulario . '&OffsetX=0&OffsetY=0');
exit;

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Imprimir Formulario</span></td>
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
				<form name="frmData" id="frmData" method="post" action="gestorias_pdf_print2.php">
					<input type="hidden" name="IdFormulario" id="IdFormulario" value="<?=$IdFormulario?>" />
					<input type="hidden" name="IdTipoFormulario" id="IdTipoFormulario" value="<?=$IdTipoFormulario?>" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  		<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
                                    <tr>
										<td><div align="right">Mover formulario en X:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="OffsetX" id="OffsetX" class="camporFormularioChico" maxlength="16" onkeyup="javascript: StrToUpper(this.id);" value="<?=$OffsetX?>">
												<span style="color:#FF0000;">&nbsp;(En cm. Por ej: 1.5)</span>
                                           	</div>
										</td>
									</tr>
                                    <tr>
                                    	<td>&nbsp;</td>
                                    	<td>&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Mover Formulario en Y:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="OffsetY" id="OffsetY" class="camporFormularioChico" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$OffsetY?>">
												<span style="color:#FF0000;">&nbsp;(En cm. Por ej: 1.5)</span>
                                        	</div>
										</td>
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
									<input type="submit" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Siguiente" />
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