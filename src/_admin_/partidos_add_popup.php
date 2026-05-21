<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PART_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Nombre			= strval($_REQUEST['Nombre']);
$IdPais 		= intval($_REQUEST['IdPais']);
$IdProvincia 	= intval($_REQUEST['IdProvincia']);
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oPartidos	= new Partidos();
$oPaises 	= new Paises();

$arr = $oPaises->GetAll();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	elseif ($oPartidos->GetByNombre($Nombre))
		$err |= 2;
	if ($IdPais == '')
		$err |= 4;
	if ($IdProvincia == '')
		$err |= 8;
			
	/* si no hay errores... */
	if ($err == 0)
	{
		$oPartido = new Partido;
		
		$oPartido->Nombre 		= $Nombre;
		$oPartido->IdPais 		= $IdPais;
		$oPartido->IdProvincia 	= $IdProvincia;
		
		$oPartido = $oPartidos->Create($oPartido);

		$Create = true;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de  Partidos - Agregar</span></td>
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
	  			<input type="hidden" name="Submitted" id="Submitted" value="1" />
				<input type="hidden" name="IdPais" id="IdPais" value="<?= $IdPais ?>" />
				<input type="hidden" name="IdProvincia" id="IdProvincia" value="<?= $IdProvincia ?>" />

        		<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
	   			  	<tr>
            			<td class="bordeGris">
							<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td height="20">&nbsp;</td>
                                    <td height="20">&nbsp;</td>
                                </tr>              					                               
              					<tr>
                					<td><div align="right">Partido:</div></td>
                					<td>
                                    	<div align="left">
											<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Nombre; ?>">									
											<span style="color:#FF0000;">&nbsp;(*)</span>									
                                       	</div>
                                  	</td>
								</tr>
                                <tr>
                                    <td height="20"><div align="right"></div></td>
                                    <td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?><?php if ($err & 2) { ?><li class="error">Ya existe registrado el nombre</li><?php } ?></td>
                                </tr>
            				</table>
					  	</td>
          			</tr>
          			<tr>
            			<td height="1"><div align="center"></div></td>
       			  	</tr>
        		</table>
   		  		<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
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
window.opener.FilterPartido('<?=$oPartido->IdPartido?>', '');
window.close();
</script>
<?php } ?>

</body>
</html>