<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PROV_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Nombre	= strval($_REQUEST['Nombre']);
$IdPais = intval($_REQUEST['IdPais']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oProvincias	= new Provincias();
$oPaises 		= new Paises();


/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	elseif ($oProvincias->GetByNombre($Nombre))
		$err |= 2;
	if ($IdPais == '')
		$err |= 4;
			
	/* si no hay errores... */
	if ($err == 0)
	{
		$oProvincia = new Provincia;
		
		$oProvincia->Nombre = $Nombre;
		$oProvincia->IdPais = $IdPais;
		
		$oProvincia = $oProvincias->Create($oProvincia);

		header("Location: provincias.php" . $strParams);
		exit();
	}
}
else
{
	$IdPais = 13;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
function FilterPais(IdPais, Nombre)
{
	if ((IdPais == '') && (Nombre == ''))
	{		
		Get('Pais').value 			= '';
		Get('IdPais').value 		= '';
	}

	var oPais = GetPais(IdPais);
	if (!(oPais))
		return;
		
	Get('Pais').value 				= oPais.Nombre;
	Get('IdPais').value 			= oPais.IdPais;
}
$j(document).ready(function() { 
	<?php
	if ($IdPais) {
	?>
		FilterPais(<?= $IdPais ?>, '');
	<?php
	}
	?>
});
</script>
</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Provincias - Agregar</span></td>
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
					<input type="hidden" name="IdPais" id="IdPais" value="<?= $IdPais ?>" />
    
                    <table width="70%" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Pa&iacute;s:</div></td>
                                        <td>
                                            <input type="text" name="Pais" id="Pais" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Pais?>" autocomplete="off">
											<input type="button" id="btnAddPais" class="botonBasico"  onClick="javascript:AddPais();" value=" + " />
											<span style="color:#FF0000;">&nbsp;(*)</span>
											<script language="">												
												SUGGESTRequest('Paises', 'GetAll', 'Pais', 'FilterPais', 'IdPais', 'Nombre', 'Filter_Nombre', null);
											</script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Debe seleccionar un pa&iacute;s</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Provincia:</div></td>
                                        <td>
                                            <input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?= $Nombre; ?>">									
                                            <span style="color:#FF0000;">&nbsp;(*)</span>									
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre de la provincia</li><?php } ?><?php if ($err & 2) { ?><li class="error">Ya existe registrado el nombre de la provincia</li><?php } ?></td>
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
                                    <input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'provincias.php<?=$strParams?>';" value="Cancelar" />
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