<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RUBR_UPDATE))
	Session::NoPerm();

/* declaracion de variables */
$err			= 0;
$Rubros			= new Rubros();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);
$IdRubro		= $_REQUEST['IdRubro'];
$FilterNombre 	= $_REQUEST['FilterNombre'];

/* obtiene datos del formulario */
$Nombre			= $_REQUEST['Nombre'];
$Submit			= $_REQUEST['Submitted'];

$strParams = '';
$strParams.= '?Page=' 			. $Page;
$strParams.= '&FilterNombre=' 	. $FilterNombre;

/* verifica si existe el registro */
$oRubro = $Rubros->GetById($IdRubro);
if (!$oRubro)
	$err += 1;

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err += 2;
	else
	{
		if ($oRubro->Nombre != $Nombre)
		{
			$oRubrosAux = $Rubros->GetByNombre($Nombre);
			
			if ($oRubrosAux)
			{		
				foreach ($oRubrosAux as $oRubroAux)
				{
					if ($oRubroAux->Nombre == $Nombre)
						$err += 4;
				}
			}
		}
	}
		
	/* si no hay ningun error... */	
	if ($err == 0)
	{
		$oRubro->Nombre 	= $Nombre;
		
		if ($Rubros->Update($oRubro))
		{	
			header("Location: rubros.php" . $strParams);
			exit();
		}
			
	}
}
else
{
	$Nombre = $oRubro->Nombre;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function ShowImagenUpdate()
{
	ShowSection('trImagenModificar');
	ShowSection('trImagenModificarMensaje');
	ShowSection('trImagenModificarMensajeError');
	HideSection('trImagen');	
}


function HideImagenUpdate()
{
	HideSection('trImagenModificar');
	HideSection('trImagenModificarMensaje');
	HideSection('trImagenModificarMensajeError');
	ShowSection('trImagen');	
}

</script>

</head>

<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Modificar Rubro</span></td>
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
      		<form name="frmData" id="frmData" method="post" action="rubros_mod.php<?=$strParams?>" enctype="multipart/form-data" >
				<input type="hidden" name="Submitted" id="Submitted" value="1">
				<input type="hidden" name="IdRubro" id="IdRubro" value="<?=$IdRubro?>">
				
				<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td><div align="right">Rubro:</div></td>
									<td>
										<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" value="<?=$Nombre?>" />
										<span style="color:#FF0000;">&nbsp;(*)</span>									</td>
								</tr>								
                            <?php if ($err & 2) { ?>
                                <tr>
									<td>&nbsp;</td>
									<td align="left"><li style="color:#FF0000;">Ingrese el nombre del rubro</li></td>
                                </tr>
							<?php } ?>
                            <?php if ($err & 4) { ?>
                            	<tr>
                                	<td>&nbsp;</td>
                                    <td><li class="error">Ya existe registrado el nombre del rubro</li></td>
                                </tr>
							<?php } ?>                                        
															
							</table>						</td>
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
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'rubros.php<?=$strParams?>';" value="Cancelar" />
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