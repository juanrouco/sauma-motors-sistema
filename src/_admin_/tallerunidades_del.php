<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdTallerUnidad	= intval($_REQUEST['IdTallerUnidad']);
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oTallerUnidades	= new TallerUnidades();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oTallerUnidad = $oTallerUnidades->GetById($IdTallerUnidad))
{
	header("Location: tallerunidades.php" . $strParams);
	exit;
}

if ($Submit)
{
	$oTallerUnidad = $oTallerUnidades->Delete($oTallerUnidad->IdTallerUnidad);

	header("Location: tallerunidades.php" . $strParams);
	exit;
}

/* armamos la descripcion de la unidad */
$Unidad = 'Interno Nro. ' . $oTallerUnidad->IdTallerUnidad;

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
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Unidades de Taller - Eliminar</span></td>
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
            
			<?php if ($oTallerUnidad->CanDelete()) { ?>
            
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar el siguiente registro?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$Unidad?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
						  	</table>						
                      	</td>
					</tr>
				</table>
                <table width="60%" border="0" cellspacing="0" cellpadding="0">
                  	<tr>
                    	<td height="1"><div align="center"></div></td>
                  	</tr>
                </table>
          		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdTallerUnidad" id="IdTallerUnidad" value="<?=$IdTallerUnidad?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'tallerunidades.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					</form>
				</table>
                
	    <?php } else { ?>
        
				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
	  				<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
    	                            <td>&nbsp;</td>
	                            </tr>
                                <tr>
        	                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0" /> </div></td>
                                </tr>
                                <tr>
            	                    <td class="error"><div align="center"><strong>El registro no puede eliminarse debido a que tiene otros registros asociados.</strong></div></td>
                                </tr>
                                <tr>
                	                <td>&nbsp;</td>
                                </tr>
						  </table>
						</td>
					</tr>
					<tr>
						<td height="1"><div align="center"></div></td>
				  	</tr>
				</table>
  				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="30">
                            <div align="center">
                              	<input type="button" class="botonBasico" value="Volver" onclick="javascript: window.location.href = 'unidades.php<?=$strParams?>'" />
                            </div>
                        </td>
                    </tr>
				</table>

	    <?php } ?>

			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>