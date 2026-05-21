<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PART_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPartido		= intval($_REQUEST['IdPartido']);
$IdPais 		= intval($_REQUEST['IdPais']);
$IdProvincia 	= intval($_REQUEST['IdProvincia']);
$Nombre			= strval($_REQUEST['Nombre']);
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oPartidos	= new Partidos();
$oPaises 	= new Paises();

/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oPartidos = $oPartidos->GetById($IdPartido))
{
	header('Location: partidos.php' . $strParams);
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	if (($Nombre != $oPartidos->Nombre) && ($oPartidos->GetByNombre($Nombre)))
		$err |= 2;
	if ($IdPais == '')
		$err |= 4;
	if ($IdProvincia == '')
		$err |= 8;
		
	/* si no hay ningun error... */	
	if ($err == 0)
	{
		$oPartidos->Nombre 		= $Nombre;
		$oPartidos->IdPais 		= $IdPais;
		$oPartidos->IdProvincia = $IdProvincia;

		$oPartidos = $oPartidos->Update($oPartidos);

		header("Location: partidos.php" . $strParams);
		exit();
	}
}
else
{
	$Nombre 		= $oPartidos->Nombre;
	$IdPais 		= $oPartidos->IdPais;
	$IdProvincia 	= $oPartidos->IdProvincia;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script language="javascript">
var arrPais = new Array();
arrPais['IdPais'] = 0;

function ValidatePais() {
	var pais = Get('Pais');
	if (pais.value == '') {
		Get('Provincia').disabled = true;
		Get('IdProvincia').value = '';		
	}
}

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
	
	if (Get('IdPais').value != oPais.IdPais) {
		Get('Provincia').value = '';
		Get('IdProvincia').value = '';
	}
	
	Get('Pais').value 				= oPais.Nombre;
	Get('IdPais').value 			= oPais.IdPais;
	arrPais['IdPais']				= oPais.IdPais;
	Get('Provincia').disabled 	= false;
}

function FilterProvincia(IdProvincia, Nombre)
{
	if ((IdProvincia == '') && (Nombre == ''))
	{		
		Get('Provincia').value 			= '';
		Get('IdProvincia').value 		= '';
	}

	var oProvincia = GetProvincia(IdProvincia);
	if (!(oProvincia))
		return;
	
	Get('Provincia').value 				= oProvincia.Nombre;
	Get('IdProvincia').value 			= oProvincia.IdProvincia;
}

$j(document).ready(function() { 	
	<?php
	if ($IdPais) {
	?>
		FilterPais(<?= $IdPais ?>, '');
	<?php
	}
	?>
	<?php
	if ($IdProvincia) {
	?>
		FilterProvincia(<?= $IdProvincia ?>, '');
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Partidos - Modificar</span></td>
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
				<input type="hidden" name="IdPartido" id="IdPartido" value="<?=$IdPartido?>" />
				<input type="hidden" name="IdPais" id="IdPais" value="<?= $IdPais ?>" />
				<input type="hidden" name="IdProvincia" id="IdProvincia" value="<?= $IdProvincia ?>" />
				
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
                                    	<div align="left">
                                           <input type="text" name="Pais" id="Pais" onkeyup="javascript: StrToUpper(this.id); ValidatePais();" class="camporFormularioSuggest" maxlength="128" value="<?=$Pais?>" autocomplete="off">
												<input type="button" id="btnAddPais" class="botonBasico"  onClick="javascript:AddPais();" value=" + " />
												<span style="color:#FF0000;">&nbsp;(*)</span>
												<script language="">												
													SUGGESTRequest('Paises', 'GetAll', 'Pais', 'FilterPais', 'IdPais', 'Nombre', 'Filter_Nombre', null);
												</script>
                                       	</div>
                              		</td>
           					  	</tr>
                                <tr>
                                    <td height="20"><div align="right"></div></td>
                                    <td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Debe seleccionar un pa&iacute;s</li><?php } ?></td>
                                </tr>
                                <tr>
                                    <td><div align="right">Provincia:</div></td>
                                    <td>
                                        <div align="left">
                                            	<input type="text" name="Provincia" id="Provincia" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Provincia?>" autocomplete="off" disabled="true" />											
												<input type="button" id="btnAddProvincia" class="botonBasico"  onClick="javascript:AddProvincia(arrPais['IdPais']);" value=" + " />
												<script language="">												
													SUGGESTRequest('Provincias', 'GetAll', 'Provincia', 'FilterProvincia', 'IdProvincia', 'Nombre', 'Nombre', arrPais);
												</script>
                                        </div>									
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20"><div align="right"></div></td>
                                    <td height="20" align="left"><?php if ($err & 8) { ?><li style="color:#FF0000;">Debe seleccionar una provincia</li><?php } ?></td>
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
		  		<table width="70%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td height="30">
							<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'partidos.php<?=$strParams?>';" value="Cancelar" />
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