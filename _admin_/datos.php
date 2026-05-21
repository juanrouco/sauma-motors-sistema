<?php

require_once('../inc_library.php');
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
//if (!Session::CheckPerm(PERM_DEMP_MOD))
	//Session::NoPerm();

/* obtiene datos enviados por formulario */
$IdDatoEmpresa				= intval($_REQUEST['IdDatoEmpresa']);
$RazonSocial				= strval($_REQUEST['RazonSocial']);
$TelefonoCodigoArea			= strval($_REQUEST['TelefonoCodigoArea']);
$Telefono					= strval($_REQUEST['Telefono']);
$TelefonoCodigoArea2		= strval($_REQUEST['TelefonoCodigoArea2']);
$Telefono2					= strval($_REQUEST['Telefono2']);
$CodigoAreaFax				= strval($_REQUEST['CodigoAreaFax']);
$Fax						= strval($_REQUEST['Fax']);
$Email						= strval($_REQUEST['Email']);
$IdPais						= intval($_REQUEST['IdPais']);
$IdPartido					= intval($_REQUEST['IdPartido']);
$IdProvincia				= intval($_REQUEST['IdProvincia']);
$IdLocalidad				= intval($_REQUEST['IdLocalidad']);
$Localidad					= strval($_REQUEST['Localidad']);
$CodigoPostal				= strval($_REQUEST['CodigoPostal']);
$DomicilioCalle				= strval($_REQUEST['DomicilioCalle']);
$DomicilioNumero			= strval($_REQUEST['DomicilioNumero']);
$DomicilioPiso				= strval($_REQUEST['DomicilioPiso']);
$DomicilioDepartamento		= strval($_REQUEST['DomicilioDepartamento']);
$PaginaWeb					= (strval($_REQUEST['PaginaWeb']) != '') ? strval($_REQUEST['PaginaWeb']) : 'http://';
$ComercianteHabitualista	= strval($_REQUEST['ComercianteHabitualista']);
$Submit						= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oLocalidades	= new Localidades();
$oDatosEmpresa	= new DatosEmpresa();

$oDatoEmpresa = $oDatosEmpresa->GetAll();

if ($Submit)
{
	/* validaciones */
	if (empty($RazonSocial))
		$err |= 1;
	
	/* si no hay errores... */
	if ($err == 0)
	{		
		$oDatoEmpresa->RazonSocial				= $RazonSocial;
		$oDatoEmpresa->TelefonoCodigoArea		= $TelefonoCodigoArea;
		$oDatoEmpresa->Telefono					= $Telefono;
		$oDatoEmpresa->TelefonoCodigoArea2		= $TelefonoCodigoArea2;
		$oDatoEmpresa->Telefono2				= $Telefono2;
		$oDatoEmpresa->CodigoAreaFax			= $CodigoAreaFax;
		$oDatoEmpresa->Fax						= $Fax;
		$oDatoEmpresa->Email					= $Email;
		$oDatoEmpresa->IdPais					= $IdPais;
		$oDatoEmpresa->IdPartido				= $IdPartido;
		$oDatoEmpresa->IdProvincia				= $IdProvincia;
		$oDatoEmpresa->Localidad				= $Localidad;
		$oDatoEmpresa->CodigoPostal				= $CodigoPostal;
		$oDatoEmpresa->DomicilioCalle			= $DomicilioCalle;
		$oDatoEmpresa->DomicilioNumero			= $DomicilioNumero;
		$oDatoEmpresa->DomicilioPiso			= $DomicilioPiso;
		$oDatoEmpresa->DomicilioDepartamento	= $DomicilioDepartamento;
		$oDatoEmpresa->PaginaWeb				= $PaginaWeb;
		$oDatoEmpresa->ComercianteHabitualista	= $ComercianteHabitualista;
			
		$oDatoEmpresa = $oDatosEmpresa->Update($oDatoEmpresa);	

		/* determinamos el resultado de la operacion para informar al usuario */
		$Operation = Operaciones::Update;
		$Status = (($oDatoEmpresa) ? true : false);
	}
}
else
{
	$oLocalidad = $oLocalidades->GetById($oDatoEmpresa->IdLocalidad);
	
	$IdDatoEmpresa				= $oDatoEmpresa->IdDatoEmpresa;
	$RazonSocial				= $oDatoEmpresa->RazonSocial;
	$TelefonoCodigoArea			= $oDatoEmpresa->TelefonoCodigoArea;
	$Telefono					= $oDatoEmpresa->Telefono;
	$TelefonoCodigoArea2		= $oDatoEmpresa->TelefonoCodigoArea2;
	$Telefono2					= $oDatoEmpresa->Telefono2;
	$CodigoAreaFax				= $oDatoEmpresa->CodigoAreaFax;
	$Fax						= $oDatoEmpresa->Fax;
	$Email						= $oDatoEmpresa->Email;
	$IdPais						= $oLocalidad->IdPais;
	$IdProvincia				= $oLocalidad->IdProvincia;
	$IdPartido					= $oLocalidad->IdPartido;
	$IdLocalidad				= $oLocalidad->IdLocalidad;
	$Localidad					= $oLocalidad->Nombre;
	$CodigoPostal				= $oLocalidad->CodigoPostal;
	$DomicilioCalle				= $oDatoEmpresa->DomicilioCalle;
	$DomicilioNumero			= $oDatoEmpresa->DomicilioNumero;
	$DomicilioPiso				= $oDatoEmpresa->DomicilioPiso;
	$DomicilioDepartamento		= $oDatoEmpresa->DomicilioDepartamento;
	$PaginaWeb					= $oDatoEmpresa->PaginaWeb;
	$ComercianteHabitualista	= $oDatoEmpresa->ComercianteHabitualista;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterLocalidad(IdLocalidad, Nombre)
{
	var oLocalidad = GetLocalidad(IdLocalidad);

	if (!(oLocalidad))
		return;

	Get('IdPais').value 		= oLocalidad.IdPais;
	Get('IdProvincia').value 	= oLocalidad.IdProvincia;
	Get('IdPartido').value 		= oLocalidad.IdPartido;
	Get('IdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('CodigoPostal').value 	= oLocalidad.CodigoPostal;
	Get('Localidad').value 		= oLocalidad.Nombre;
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post">
    <input type="hidden" name="IdPais" id="IdPais" value="<?=$IdPais?>" />
    <input type="hidden" name="IdProvincia" id="IdProvincia" value="<?=$IdProvincia?>" />
    <input type="hidden" name="IdPartido" id="IdPartido" value="<?=$IdPartido?>" />
    <input type="hidden" name="IdLocalidad" id="IdLocalidad" value="<?=$IdLocalidad?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1">

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Datos de la Empresa</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
        </tr>
    
        <?php echo Operaciones::PrintResult($Operation, $Status); ?>
    
        <tr>
            <td valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <div align="center">
                    <table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Raz&oacute;n Social:</div></td>
                                        <td>
                                            <input type="text" name="RazonSocial" id="RazonSocial" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$RazonSocial;?>" />
                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la Raz&oacute;n Social</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Telefono 1:</div></td>
                                        <td>
                                            <input type="text" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="16" value="<?=$TelefonoCodigoArea;?>" />
                                            <input type="text" name="Telefono" id="Telefono" class="camporFormularioMediano" maxlength="32" value="<?=$Telefono;?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Telefono 2:</div></td>
                                        <td>
                                            <input type="text" name="TelefonoCodigoArea2" id="TelefonoCodigoArea2" class="camporFormularioChico" maxlength="16" value="<?=$TelefonoCodigoArea2;?>"> 
                                            <input type="text" name="Telefono2" id="Telefono2" class="camporFormularioMediano" maxlength="32" value="<?=$Telefono2;?>" />
                                            <span style="color:#FF0000;">&nbsp;</span>
                                        </td>
                                    </tr>              					
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Fax:</div></td>
                                        <td>
                                            <input type="text" name="CodigoAreaFax" id="CodigoAreaFax" class="camporFormularioChico" maxlength="16" value="<?=$CodigoAreaFax;?>" />
                                            <input type="text" name="Fax" id="Fax" class="camporFormularioMediano" maxlength="32" value="<?=$Fax;?>" />
                                            <span style="color:#FF0000;">&nbsp;</span>
                                        </td>
                                    </tr>              					
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">E-mail:</div></td>
                                        <td>
                                            <input type="text" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email;?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Calle:</div></td>
                                        <td>
                                            <input type="text" name="DomicilioCalle" id="DomicilioCalle" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$DomicilioCalle;?>" />
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">N&uacute;mero:</div></td>
                                        <td>
                                            <input type="text" name="DomicilioNumero" id="DomicilioNumero" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$DomicilioNumero;?>" />
                                        </td>
                                    </tr>              					
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Piso:</div></td>
                                        <td>
                                            <input type="text" name="DomicilioPiso" id="DomicilioPiso" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioPiso;?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Dpto:</div></td>
                                        <td>
                                            <input type="text" name="DomicilioDepartamento" id="DomicilioDepartamento" class="camporFormularioSimple" maxlength="128" value="<?=$DomicilioDepartamento;?>" />
                                        </td>
                                    </tr>              					
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Localidad:</div></td>
                                        <td>
                                        	<div align="left">
                                            	<table>
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                                <input type="text" name="Localidad" id="Localidad" class="camporFormularioSuggest" maxlength="128" value="<?=$Localidad?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                <script language="javascript">
                                                                SUGGESTRequest('Localidades', 'GetAllSuggest', 'Localidad', 'FilterLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="left">
                                                                <input type="text" name="CodigoPostal" id="CodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=$CodigoPostal?>" readonly="readonly" />
                                                                
                                                            </div>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                              	</table>
                                          	</div>
                                      	</td>
                                    </tr>
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">P&aacute;gina Web:</div></td>
                                        <td>
                                            <input type="text" name="PaginaWeb" id="PaginaWeb" class="camporFormularioSimple" maxlength="128" value="<?=$PaginaWeb;?>" />(Ej.: http://www.ejemplo.com)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10">&nbsp;</td>
                                        <td height="10">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Comerciante Habitualista:</div></td>
                                        <td>
                                            <input type="text" name="ComercianteHabitualista" id="ComercianteHabitualista" class="camporFormularioSimple" maxlength="128" value="<?=$ComercianteHabitualista;?>" />
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
                                <input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Guardar" />                			
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>

<script language="javascript">
LoadProvincias('IdProvincia', '<?=$IdPais?>', '<?=$IdProvincia?>');
</script>

</body>
</html>