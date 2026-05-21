<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CLIE_CONTACTS))
	Session::NoPerm();

/* obtiene datos enviados */
$IdContacto				= strval($_REQUEST['IdContacto']);
$Nombre					= strval($_REQUEST['Nombre']);
$Apellido				= strval($_REQUEST['Apellido']);
$TelefonoCodigoArea		= strval($_REQUEST['TelefonoCodigoArea']);
$Telefono				= strval($_REQUEST['Telefono']);
$DocumentoTipo			= intval($_REQUEST['DocumentoTipo']);
$DocumentoTipoNombre	= strval($_REQUEST['DocumentoTipoNombre']);
$DocumentoTipoCodigo	= strval($_REQUEST['DocumentoTipoCodigo']);
$DocumentoNumero		= strval($_REQUEST['DocumentoNumero']);
$FechaNacimiento		= strval($_REQUEST['FechaNacimiento']);
$Email					= strval($_REQUEST['Email']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oClienteContactos	= new ClienteContactos();
$oTiposDocumento	= new TiposDocumento();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oClienteContacto = $oClienteContactos->GetById($IdContacto))
{
	header('Location: clientes_contactos.php' . $strParams);
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err |= 1;
	if ($Apellido == '')
		$err |= 2;
	if (($TelefonoCodigoArea == '') && ($Telefono == ''))
		$err |= 4;

	/* si no hay ningun error... */	
	if ($err == 0)
	{			
		$oClienteContacto->Nombre 				= $Nombre;
		$oClienteContacto->Apellido 			= $Apellido;
		$oClienteContacto->TelefonoCodigoArea 	= $TelefonoCodigoArea;
		$oClienteContacto->Telefono 			= $Telefono;
		$oClienteContacto->DocumentoTipo 		= $DocumentoTipo;
		$oClienteContacto->DocumentoNumero 		= $DocumentoNumero;
		$oClienteContacto->FechaNacimiento 		= $FechaNacimiento;
		$oClienteContacto->Email 				= $Email;
	
		$oClienteContacto = $oClienteContactos->Update($oClienteContacto);
		
		header("Location: clientes_contactos.php" . $strParams);
		exit();
	}
}
else
{
	$oDocumentoTipo = $oTiposDocumento->GetById($oCliente->DocumentoTipo);
	
	$DocumentoTipo			= $oDocumentoTipo->IdTipoDocumento;
	$DocumentoTipoNombre	= $oDocumentoTipo->Nombre;
	$DocumentoTipoCodigo	= $oDocumentoTipo->Codigo;
	
	$Nombre 			= $oClienteContacto->Nombre;
	$Apellido 			= $oClienteContacto->Apellido;
	$TelefonoCodigoArea = $oClienteContacto->TelefonoCodigoArea;
	$Telefono 			= $oClienteContacto->Telefono;
	$DocumentoTipo 		= $oClienteContacto->DocumentoTipo;
	$DocumentoNumero 	= $oClienteContacto->DocumentoNumero;
	$FechaNacimiento 	= CambiarFecha($oClienteContacto->FechaNacimiento);
	$Email 				= $oClienteContacto->Email;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterTipoDocumento(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('DocumentoTipoCodigo').value 	= '';
		Get('DocumentoTipoNombre').value 	= '';
		Get('DocumentoTipo').value 			= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('DocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('DocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('DocumentoTipo').value 			= oTipoDocumento.IdTipoDocumento;
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
        			<td height="40"><span class="tituloPagina">Adminitraci&oacute;n de  Contactos - Modificar</span></td>
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
					<input type="hidden" name="IdCliente" id="IdCliente" value="<?=$IdCliente?>" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
					  	<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Nombre:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" value="<?=$Nombre?>">
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Apellido:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Apellido" id="Apellido" class="camporFormularioSimple" maxlength="128" value="<?=$Apellido?>">
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el apellido</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Tel&eacute;fono:</div></td>
										<td>
                                        	<div align="left">
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="TelefonoCodigoArea" id="TelefonoCodigoArea" class="camporFormularioChico" maxlength="128" value="<?=$TelefonoCodigoArea?>" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="Telefono" id="Telefono" class="camporFormularioMedianoI" maxlength="128" value="<?=$Telefono?>" />
                                                              
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un tel&eacute;fono de contacto.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Email:</div></td>
										<td>
                                        	<div align="left">
                                                <input type="text" name="Email" id="Email" class="camporFormularioSimple" maxlength="128" value="<?=$Email?>">
                                          	</div>
                                       	</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Tipo Documento:</div></td>
										<td>
                                        	<div align="left">
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                                <input type="text" name="DocumentoTipoNombre" id="DocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$DocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                <script language="javascript">
                                                                SUGGESTRequest('TiposDocumento', 'GetAll', 'DocumentoTipoNombre', 'FilterTipoDocumento', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div align="left">
                                                                <input type="text" name="DocumentoTipoCodigo" id="DocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$DocumentoTipoCodigo?>" readonly="readonly" />
                                                                
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Nro. Documento:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoNumero?>" />
                                            </div>
                                        </td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Fecha Nacimiento:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="FechaNacimiento" type="text" class="camporFormularioMediano" id="FechaNacimiento" value="<?=$FechaNacimiento?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaNacimiento'});
                                                </script>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'clientes_contactos.php<?=$strParams?>';" value="Cancelar" />
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