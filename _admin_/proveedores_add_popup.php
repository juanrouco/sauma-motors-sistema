<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PROVE_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$Empresa 				= $_REQUEST['Empresa'];
$IdRubro 				= $_REQUEST['IdRubro'];
$Rubro					= $_REQUEST['Rubro'];
$TelefonoCodigoArea		= $_REQUEST['TelefonoCodigoArea'];
$Telefono 				= $_REQUEST['Telefono'];
$TelefonoCodigoArea2	= $_REQUEST['TelefonoCodigoArea2'];
$Telefono2 				= $_REQUEST['Telefono2'];
$FaxCodigoArea			= $_REQUEST['FaxCodigoArea'];
$Fax 					= $_REQUEST['Fax'];
$Email					= $_REQUEST['Email'];
$Web					= $_REQUEST['Web'];
$DomicilioCalle			= $_REQUEST['DomicilioCalle'];
$DomicilioNumero		= $_REQUEST['DomicilioNumero'];
$DomicilioPiso			= $_REQUEST['DomicilioPiso'];
$DomicilioDpto			= $_REQUEST['DomicilioDpto'];
$IdPais					= $_REQUEST['IdPais'];
$Pais					= $_REQUEST['Pais'];
$IdProvincia			= $_REQUEST['IdProvincia'];
$Provincia				= $_REQUEST['Provincia'];
$IdPartido				= $_REQUEST['IdPartido'];
$Partido				= $_REQUEST['Partido'];
$IdLocalidad			= $_REQUEST['IdLocalidad'];
$Localidad				= $_REQUEST['Localidad'];
$CodigoPostal			= $_REQUEST['CodigoPostal'];
$Observaciones			= $_REQUEST['Observaciones'];
$Cuit					= $_REQUEST['Cuit'];

$Submit					= $_REQUEST['Submitted'];

/* declaracion de variables */
$err			= 0;
$oProveedor 	= new Proveedor();
$Proveedores	= new Proveedores();
$Paises			= new Paises();
$Provincias		= new Provincias();
$Rubros			= new Rubros();
$Proveedores	= new Proveedores();
$Create 		= false;

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterEmpresa=' 		. $_REQUEST['FilterEmpresa'];
$strParams.= '&FilterEmail=' 		. $_REQUEST['FilterEmail'];
$strParams.= '&FilterIdRubro='		. $_REQUEST['FilterIdRubro'];
$strParams.= '&FilterPais='			. $_REQUEST['FilterPais'];
$strParams.= '&FilterProvincia='	. $_REQUEST['FilterProvincia'];

if ($Submit)
{
	/* validaciones... */
	if ($Empresa == '')
		$err += 1;
	if ($IdRubro == '')
		$err += 2;
	if ($IdPais == '')
		$err += 128;
	if ($Cuit == '')
		$err += 256;
	if ($oProveedorAux = $Proveedores->GetByCUIT($Cuit))
		$err |= 4;

	/* si no hay errores... */
	if ($err == 0)
	{	
		$oProveedor->Empresa 				= $Empresa;
		$oProveedor->IdRubro 				= $IdRubro;
		$oProveedor->TelefonoCodigoArea		= $TelefonoCodigoArea;
		$oProveedor->Telefono 				= $Telefono;
		$oProveedor->TelefonoCodigoArea2	= $TelefonoCodigoArea2;
		$oProveedor->Telefono2 				= $Telefono2;		
		$oProveedor->FaxCodigoArea			= $FaxCodigoArea;
		$oProveedor->Fax 					= $Fax;
		$oProveedor->Email 					= $Email;
		$oProveedor->Web 					= $Web;
		$oProveedor->DomicilioCalle 		= $DomicilioCalle;
		$oProveedor->DomicilioNumero 		= $DomicilioNumero;
		$oProveedor->DomicilioPiso 			= $DomicilioPiso;
		$oProveedor->DomicilioDpto 			= $DomicilioDpto;
		$oProveedor->IdPais	 				= $IdPais;
		$oProveedor->IdProvincia 			= $IdProvincia;
		$oProveedor->IdPartido	 			= $IdPartido;
		$oProveedor->IdLocalidad 			= $IdLocalidad;
		$oProveedor->CodigoPostal 			= $CodigoPostal;
		$oProveedor->Observaciones 			= $Observaciones;
		$oProveedor->Cuit					= $Cuit;

		/* crea el proveedor */
		$oProveedor = $Proveedores->Create($oProveedor);
		$Create = true;
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

var arrPais = new Array();
arrPais['IdPais'] = 0;
var arrProvincia = new Array();
arrProvincia['FilterIdPais'] = 0;
arrProvincia['IdProvincia'] = 0;
arrProvincia['FilterIdProvincia'] = 0;

function FilterRubro(IdRubro, Nombre)
{
	if ((IdRubro == '') && (Nombre == ''))
	{		
		Get('Rubro').value 			= '';
		Get('IdRubro').value 		= '';
	}

	var oRubro = GetRubro(IdRubro);
	if (!(oRubro))
		return;
	
	Get('Rubro').value 			= oRubro.Nombre;
	Get('IdRubro').value 		= oRubro.IdRubro;
}

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
	arrPais['IdPais']				= oPais.IdPais;
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
	Get('Localidad').disabled 			= false;
}

function FilterLocalidad(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{		
		Get('Localidad').value 			= '';
		Get('IdLocalidad').value 		= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;
	
	Get('Localidad').value 		= oLocalidad.Nombre;
	Get('IdLocalidad').value 	= oLocalidad.IdLocalidad;	
}

function CheckNewsletter(value)
{
	var IdGrupos = document.frmData['IdGrupo[]'];
	var i;
	
	if (!IdGrupos)
		return false;
	
	if (value != 1)
	{
		for (i=0; i<IdGrupos.length; i++)
			IdGrupos[i].checked = false;
			
		HideSection('Grupos');
	}
	else
	{
		ShowSection('Grupos');
	}
}
$j(document).ready(function() { 
	<?php
	if ($IdRubro) {
	?>
		FilterRubro(<?= $IdRubro ?>, '');
	<?php
	}
	?>
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
	?>
	<?php
	if ($IdLocalidad) {
	?>
		FilterLocalidad(<?= $IdLocalidad ?>, '');
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
        			<td height="40"><span class="tituloPagina">Agregar Proveedor </span></td>
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
      		<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
	  			<input type="hidden" name="Submitted" id="Submitted" value="1">				
				<input type="hidden" name="IdRubro" id="IdRubro" value="<?= $IdRubro ?>">	
				<input type="hidden" name="IdPais" id="IdPais" value="<?= $IdPais ?>">
				<input type="hidden" name="IdProvincia" id="IdProvincia" value="<?= $IdProvincia ?>">				
				<input type="hidden" name="IdPartido" id="IdPartido" value="<?= $IdPartido ?>">	
				<input type="hidden" name="IdLocalidad" id="IdLocalidad" value="<?= $IdLocalidad ?>">	

				<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
          			<tr>
            			<td class="bordeGris">
							<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
              					<tr>
                					<td>&nbsp;</td>
                                    <td>&nbsp;</td>
              					</tr>
								<tr>
									<td><div align="right">Empresa:</div></td>
									<td>
										<div align="left">
										<input type="text" name="Empresa" id="Empresa" class="camporFormularioSimple" value="<?=$Empresa?>" >
								  		<span style="color:#FF0000;">&nbsp;(*)</span>										</div>
																			</td>
								</tr>
                           <?php if ($err & 1) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese nombre de la empresa</li></td>
                                </tr>
                           <?php } ?>
								<tr>
									<td><div align="right">Cuit:</div></td>
									<td>
										<div align="left">
										<input type="text" name="Cuit" id="Cuit" class="camporFormularioSimple" value="<?=$Cuit?>" >
								  		<span style="color:#FF0000;">&nbsp;(*)</span>										</div>
																			</td>
								</tr>
                           <?php if ($err & 256) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese el Cuit</li></td>
                                </tr>
                           <?php } ?>	
						<?php if ($err & 4) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">El CUIT ya fue ingresado para otro proveedor.</li></td>
                                </tr>
                           <?php } ?>			
                                <tr>
									<td><div align="right">Rubro:</div></td>
									<td>
										<div align="left">
											<input type="text" id="Rubro" name="Rubro" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Rubro?>" autocomplete="off" />
											<input type="button" id="btnAddRubro" class="botonBasico"  onClick="javascript:AddRubro();" value=" + " />
											<span style="color:#FF0000;">&nbsp;(*)</span>
											<script language="">
												SUGGESTRequest('Rubros', 'GetAll', 'Rubro', 'FilterRubro', 'IdRubro', 'Nombre', 'Filter_Nombre', null);
											</script>
										</div>
									</td>
								</tr>
                           <?php if ($err & 2) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese un rubro.</li></td>
                                </tr>
                           <?php } ?>
                                <tr>
									<td><div align="right">Cod. &Aacute;rea:</div></td>
									<td>
										<div align="left">
										<input type="text" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="16" value="<?=$TelefonoCodigoArea;?>"> 
                                        Tel:&nbsp;&nbsp;
                                        <input type="text" name="Telefono" id="Telefono" class="camporFormularioMediano2" maxlength="32" value="<?=$Telefono;?>"> 
										</div>									</td>
								</tr>
                                <tr>
									<td><div align="right">Cod. &Aacute;rea 2:</div></td>
									<td>
										<div align="left">
										<input type="text" name="TelefonoCodigoArea2" id="TelefonoCodigoArea2" class="camporFormularioChico" maxlength="16" value="<?=$TelefonoCodigoArea2;?>"> 
                                        Tel2:
                                        <input type="text" name="Telefono2" id="Telefono2" class="camporFormularioMediano2" maxlength="32" value="<?=$Telefono2;?>"> 
										</div>									</td>
								</tr>
								<tr>
									<td><div align="right">Cod. &Aacute;rea Fax:</div></td>
									<td>	
										<div align="left">
										<input type="text" name="FaxCodigoArea" id="FaxCodigoArea" class="camporFormularioChico" maxlength="16" value="<?=$FaxCodigoArea;?>"> 
										Fax:
                                        <input type="text" name="Fax" id="Fax" class="camporFormularioMediano2" maxlength="32" value="<?=$Fax;?>"> 
										</div>									</td>
								</tr>
								<tr>
									<td><div align="right">E-Mail:</div></td>
									<td>
										<div align="left">
										<input type="text" name="Email" id="Email" class="camporFormularioSimple" value="<?=$Email?>" >
								  												</div>					</td>
								</tr>
								<tr>
									<td><div align="right">Web:</div></td>
									<td>
										<div align="left">
										<input type="text" name="Web" id="Web" class="camporFormularioSimple" value="<?=$Web?>" >
								  		</div></td>
								</tr>
                                <tr>
									<td><div align="right">Calle:</div></td>
									<td>
										<div align="left">
										<input type="text" name="DomicilioCalle" id="DomicilioCalle" class="camporFormularioSimple" value="<?=$DomicilioCalle?>" >
										</div></td>
								</tr>
								<tr>
									<td><div align="right">Nro.:</div></td>
									<td>
										<div align="left">
										<input type="text" name="DomicilioNumero" id="DomicilioNumero" class="camporFormularioSimple" value="<?=$DomicilioNumero?>" >
										</div></td>
								</tr>
								<tr>
									<td><div align="right">Piso:</div></td>
									<td>
										<div align="left">
										<input type="text" name="DomicilioPiso" id="DomicilioPiso" class="camporFormularioSimple" value="<?=$DomicilioPiso?>" >
										</div></td>
								</tr>
								<tr>
									<td><div align="right">Dpto:</div></td>
									<td>
										<div align="left">
										<input type="text" name="DomicilioDpto" id="DomicilioDpto" class="camporFormularioSimple" value="<?=$DomicilioDpto?>" >
										</div></td>
								</tr>
              					<tr>
                					<td><div align="right">Pais:</div></td>
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
              				<?php if ($err & 128) { ?>
                            	<tr>
                					<td><div align="right"></div></td>
                					<td><li style="color:#FF0000;">Ingrese un pa&iacute;s</li></td>
								</tr>
                            <?php } ?>
								<tr>
									<td><div align="right">Provincia:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Provincia" id="Provincia" onkeyup="javascript: StrToUpper(this.id); ValidateProvincia();" class="camporFormularioSuggest" maxlength="128" value="<?=$Provincia?>" autocomplete="off" disabled="true" />											
											<input type="button" id="btnAddProvincia" class="botonBasico"  onClick="javascript:AddProvincia(arrPais['IdPais']);" value=" + " />
											<script language="">												
												SUGGESTRequest('Provincias', 'GetAll', 'Provincia', 'FilterProvincia', 'IdProvincia', 'Nombre', 'Nombre', arrPais);
											</script>
										</div>
									</td>
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
									<td><div align="right">Localidad:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Localidad" id="Localidad" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Localidad?>" autocomplete="off" disabled="true" >
											<input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad(Get('IdPais').value, Get('IdProvincia').value, Get('IdPartido').value);" value=" + " />
											<script language="">												
												SUGGESTRequest('Localidades', 'GetAll', 'Localidad', 'FilterLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', arrProvincia);
											</script>
										</div>
									</td>
								</tr>
								<tr>
									<td><div align="right">Codigo Postal:</div></td>
									<td>
										<div align="left">
										<input type="text" name="CodigoPostal" id="CodigoPostal" class="camporFormularioSimple" value="<?=$CodigoPostal?>" >
										</div>									</td>
								</tr>
                                <tr>
									<td><div align="right">Observaciones:</div></td>
									<td>
										<div align="left">
										<textarea name="Observaciones" id="Observaciones" class="camporFormularioMultiline"><?=$Observaciones?></textarea>
										</div>									</td>
								</tr>
                        		<tr>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
								</tr>

            				</table>						</td>
          			</tr>
        		</table>
				
   		        <table width="75%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="1"><div align="center"></div></td>
                  </tr>
                </table>
  <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          			<tr>
            			<td height="30">
              				<div align="center">
                				<input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
                				<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'proveedores.php<?=$strParams?>';" value="Cancelar" />
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


<script language="javascript">
	CheckNewsletter('<?=$Newsletter?>');
	<?php if ($Create) { ?>
		window.opener.FilterProveedor('<?=$oProveedor->IdProveedor?>', '');
		window.close();
	<?php } ?>
</script>
</body>
</html>