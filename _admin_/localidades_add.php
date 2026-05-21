<?php
$TipoAux = $Tipo;
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_LOCA_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Nombre			= strval($_REQUEST['Nombre']);
$CodigoPostal	= strval($_REQUEST['CodigoPostal']);
$IdPais 		= intval($_REQUEST['IdPais']);
$IdProvincia 	= intval($_REQUEST['IdProvincia']);
$IdPartido 		= intval($_REQUEST['IdPartido']);
$Jurisdiccion	= intval($_REQUEST['Jurisdiccion']);

$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oLocalidad 	= new Localidad;
$oLocalidades	= new Localidades();
$oPartidos		= new Partidos();
$oPaises 		= new Paises();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	/*elseif ($oPartidos->GetByNombre($Nombre))
		$err |= 2;*/
	if ($IdPais == '')
		$err |= 4;
	if ($IdProvincia == '')
		$err |= 8;
	if ($IdPartido == '')
		$err |= 16;
			
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oLocalidad->Nombre 		= $Nombre;
		$oLocalidad->CodigoPostal 	= $CodigoPostal;
		$oLocalidad->IdPais 		= $IdPais;
		$oLocalidad->IdProvincia 	= $IdProvincia;
		$oLocalidad->IdPartido 		= $IdPartido;
		$oLocalidad->Jurisdiccion 	= $Jurisdiccion;
		
		$oLocalidad = $oLocalidades->Create($oLocalidad);

		if (!$popup)
		{
			header("Location: localidades.php" . $strParams);
			exit();
		}
		else
		{
			$Create = true;
		}
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
<script language="javascript">

var arrProvincia = new Array();
arrProvincia['FilterIdPais'] = 0;
arrProvincia['IdProvincia'] = 0;
arrProvincia['FilterIdProvincia'] = 0;

function ValidatePais() {
	var pais = Get('Pais');
	if (pais.value == '') {
		Get('Provincia').disabled = true;
		Get('IdProvincia').value = '';
		ValidateProvincia();
	}
}

function ValidateProvincia() {
	var provincia = Get('Provincia');
	if (provincia.value == '') {
		Get('Localidad').disabled = true;
		Get('IdLocalidad').value = '';
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
		
	Get('Pais').value 				= oPais.Nombre;
	Get('IdPais').value 			= oPais.IdPais;	
	arrProvincia['FilterIdPais']	= oPais.IdPais;
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
	arrProvincia['FilterIdProvincia']	= oProvincia.IdProvincia;
	arrProvincia['IdProvincia']			= oProvincia.IdProvincia;
	Get('Partido').disabled 			= false;
}

function FilterPartido(IdPartido, Nombre)
{
	if ((IdPartido == '') && (Nombre == ''))
	{		
		Get('Partido').value 			= '';
		Get('IdPartido').value 		= '';
	}

	var oPartido = GetPartido(IdPartido);
	if (!(oPartido))
		return;
	
	Get('Partido').value 				= oPartido.Nombre;
	Get('IdPartido').value 				= oPartido.IdPartido;	
	arrProvincia['FilterIdPartido']		= oPartido.IdPartido;	
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
	<?php
	if ($IdPartido) {
	?>
		FilterPartido(<?= $IdPartido ?>, '');
	<?php
	}
	if ($Create) 
	{ 
		if ($TipoAux == 'Domicilio') 
		{ 
	?>
		window.opener.FilterDomicilioLocalidad('<?=$oLocalidad->IdLocalidad?>', '');
	<?php 
		} elseif ($TipoAux == 'DomicilioPostal') 
		{ 
	?>
		window.opener.FilterDomicilioLocalidadPostal('<?=$oLocalidad->IdLocalidad?>', '');
		<?php 
		} elseif ($TipoAux == 'DomicilioNacimiento') 
		{ 
	?>
		window.opener.FilterNacimientoLocalidad('<?=$oLocalidad->IdLocalidad?>', '');
	<?php 
		} elseif ($TipoAux == 'DomicilioFiscal') 
		{
	?>
		window.opener.FilterDomicilioFiscalLocalidad('<?=$oLocalidad->IdLocalidad?>', '');
	<?php 
		} elseif ($TipoAux == 'DomicilioConyugeTitular') 
		{
	?>
		window.opener.FilterDomicilioLocalidadConyugeTitular('<?=$oLocalidad->IdLocalidad?>', '');
	<?php
		} elseif ($TipoAux == 'DomicilioConyugeCondominio')
		{
	?>
		window.opener.FilterDomicilioLocalidadConyugeCondominio('<?=$oLocalidad->IdLocalidad?>', '');
	<?php 
		} 
	?>
		window.close();
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Localidades - Agregar</span></td>
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
					<input type="hidden" name="IdProvincia" id="IdProvincia" value="<?= $IdProvincia ?>" />				
					<input type="hidden" name="IdPartido" id="IdPartido" value="<?= $IdPartido ?>" />
					<?php
					
						if ($TipoAux)
						{
					?>
					<input type="hidden" name="Tipo" id="Tipo" value="<?= $TipoAux ?>" />	
					<?php
						}
					?>
                    <table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
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
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Debe seleccionar un pa&iacute;s</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Provincia:</div></td>
                                        <td>
                                            <div align="left">
                                               <input type="text" name="Provincia" id="Provincia" onkeyup="javascript: StrToUpper(this.id); ValidateProvincia();" class="camporFormularioSuggest" maxlength="128" value="<?=$Provincia?>" autocomplete="off" disabled="true" />											
											<input type="button" id="btnAddProvincia" class="botonBasico"  onClick="javascript:AddProvincia(arrProvincia['IdPais']);" value=" + " />
											<script language="">												
												SUGGESTRequest('Provincias', 'GetAll', 'Provincia', 'FilterProvincia', 'IdProvincia', 'Nombre', 'Nombre', arrProvincia);
											</script>
                                            </div>									
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 8) { ?><li style="color:#FF0000;">Debe seleccionar una provincia</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Partido:</div></td>
                                        <td>
                                            <div align="left">
                                               <input type="text" name="Partido" id="Partido" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Partido?>" autocomplete="off" disabled="true" >
											<input type="button" id="btnAddPartido" class="botonBasico"  onClick="javascript:AddPartido(Get('IdPais').value, Get('IdProvincia').value);" value=" + " />
											<script language="">												
												SUGGESTRequest('Partidos', 'GetAll', 'Partido', 'FilterPartido', 'IdPartido', 'Nombre', 'Nombre', arrProvincia);
											</script>
                                            </div>									
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 16) { ?><li style="color:#FF0000;">Debe seleccionar un partido</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Localidad:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Nombre?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>
                                            </div>
                                        </td>						
                                    </tr>                        		
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre de la localidad</li><?php } ?><?php if ($err & 2) { ?><li style="color:#FF0000;">Ya existe registrada la localidad</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">C&oacute;digo Postal:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="CodigoPostal" id="CodigoPostal" class="camporFormularioSimple" onkeyup="javascript: StrToUpper(this.id);" value="<?=$CodigoPostal?>" />                                                
                                            </div>
                                        </td>						
                                    </tr>                        		
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el c&oacute;digo postal</li><?php } ?></td>
                                    </tr>
									<tr>
                                        <td><div align="right">Jurisdiccion:</div></td>
                                        <td>
                                            <div align="left">
                                                <select type="text" name="Jurisdiccion" id="Jurisdiccion" class="camporFormularioSimple">
													<option value="<?= Jurisdicciones::Indistinto ?>" <?= Jurisdicciones::Indistinto == $Jurisdiccion ? 'selected="selected"' : '' ?>><?= Jurisdicciones::GetDescripcionById(Jurisdicciones::Indistinto) ?></option>
													<option value="<?= Jurisdicciones::ProvinciaBuenosAires ?>" <?= Jurisdicciones::ProvinciaBuenosAires == $Jurisdiccion ? 'selected="selected"' : '' ?>><?= Jurisdicciones::GetDescripcionById(Jurisdicciones::ProvinciaBuenosAires) ?></option>
													<option value="<?= Jurisdicciones::CapitalFederal ?>" <?= Jurisdicciones::CapitalFederal == $Jurisdiccion ? 'selected="selected"' : '' ?>><?= Jurisdicciones::GetDescripcionById(Jurisdicciones::CapitalFederal) ?></option>
												</select>
                                            </div>
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
                            <td height="1"><div align="center"></div></td>
                        </tr>
                    </table>
                    <table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                        <tr>
                            <td height="30">
                                <div align="center">
                                    <input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<?php
									if  (!$popup)
									{
									?>
                                    <input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'localidades.php<?=$strParams?>';" value="Cancelar" />
									<?php
									}
									else
									{
									?>
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.close();" value="Cancelar" />
									<?php
									}
									?>
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