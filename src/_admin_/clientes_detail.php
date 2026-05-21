<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos 
if (!Session::CheckPerm(PERM_CLIE_UPDATE))
	Session::NoPerm();*/

/* obtiene datos enviados */
$IdCliente						= intval($_REQUEST['IdCliente']);

/* declaracion de variables */
$err				= 0;
$oClientes			= new Clientes();
$oUsuarios			= new Usuarios();
$oTiposIva 			= new TiposIva();
$oProfesiones 		= new Profesiones();
$oLocalidades 		= new Localidades();
$oTiposDocumento 	= new TiposDocumento();
$oEstadosCiviles 	= new EstadosCiviles();
$oPaises			= new Paises();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oCliente = $oClientes->GetById($IdCliente))
{
	header('Location: clientes.php' . $strParams);
	exit;
}


$oDocumentoTipo 		= $oTiposDocumento->GetById($oCliente->DocumentoTipo);
$oEstadoCivil 			= $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);
$oProfesion 			= $oProfesiones->GetById($oCliente->IdProfesion);
$oTipoIva 				= $oTiposIva->GetById($oCliente->IdTipoIva);
$oDocumentoTipoConyuge 	= $oTiposDocumento->GetById($oCliente->ConyugeDocumentoTipo);
$oUsuario 				= $oUsuarios->GetById($oCliente->IdVendedor);
$oDomicilioLocalidad 	= $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
$oDomicilioLocalidadPostal 	= $oLocalidades->GetById($oCliente->DomicilioIdLocalidadPostal);
$oNacimientoLocalidad 	= $oLocalidades->GetById($oCliente->NacimientoIdLocalidad);

$DocumentoTipo					= $oDocumentoTipo->IdTipoDocumento;
$DocumentoTipoNombre			= $oDocumentoTipo->Nombre;
$DocumentoTipoCodigo			= $oDocumentoTipo->Codigo;
$IdEstadoCivil					= $oEstadoCivil->IdEstadoCivil;
$EstadoCivil					= $oEstadoCivil->Nombre;
$EstadoCivilCodigo				= $oEstadoCivil->Codigo;
$IdProfesion					= $oProfesion->IdProfesion;
$Profesion						= $oProfesion->Nombre;
$ProfesionCodigo				= $oProfesion->Codigo;
$IdTipoIva						= $oTipoIva->IdTipoIva;
$TipoIva						= $oTipoIva->Nombre;
$TipoIvaCodigo					= $oTipoIva->Codigo;
$IdEstadoCivil					= $oEstadoCivil->IdEstadoCivil;
$EstadoCivil					= $oEstadoCivil->Nombre;
$EstadoCivilCodigo				= $oEstadoCivil->Codigo;
$ConyugeDocumentoTipo 			= $oDocumentoTipoConyuge->IdTipoDocumento;
$ConyugeDocumentoTipoNombre 	= $oDocumentoTipoConyuge->Nombre;
$ConyugeDocumentoTipoCodigo 	= $oDocumentoTipoConyuge->Codigo;

$IdUsuario	= $oUsuario->IdUsuario;
$Usuario	= ($oUsuario->Nombre . ' ' . $oUsuario->Apellido);

$IdTipoPersona 					= $oCliente->IdTipoPersona;
$RazonSocial 					= $oCliente->RazonSocial;
$DomicilioCalle 				= $oCliente->DomicilioCalle;
$DomicilioNumero 				= $oCliente->DomicilioNumero;
$DomicilioPiso 					= $oCliente->DomicilioPiso;
$DomicilioDpto 					= $oCliente->DomicilioDpto;
$DomicilioIdLocalidad	 		= $oCliente->DomicilioIdLocalidad;
$DomicilioLocalidad	 			= $oDomicilioLocalidad->Nombre;
$DomicilioCodigoPostal 			= $oDomicilioLocalidad->CodigoPostal;
$DomicilioCallePostal			= $oCliente->DomicilioCallePostal;
$DomicilioNumeroPostal			= $oCliente->DomicilioNumeroPostal;
$DomicilioPisoPostal			= $oCliente->DomicilioPisoPostal;
$DomicilioDptoPostal			= $oCliente->DomicilioDptoPostal;
$DomicilioIdLocalidadPostal		= $oCliente->DomicilioIdLocalidadPostal;
$DomicilioLocalidadPostal		= $oDomicilioLocalidadPostal->Nombre;
$DomicilioCodigoPostalPostal	= $oDomicilioLocalidadPostal->CodigoPostal;
$NacimientoIdLocalidad 			= $oCliente->NacimientoIdLocalidad;
$NacimientoLocalidad 			= $oNacimientoLocalidad->Nombre;
$NacimientoCodigoPostal 		= $oNacimientoLocalidad->CodigoPostal;
$TelefonoCodigoArea 			= $oCliente->TelefonoCodigoArea;
$Telefono 						= $oCliente->Telefono;
$FaxCodigoArea 					= $oCliente->FaxCodigoArea;
$Fax 							= $oCliente->Fax;
$DocumentoNumero 				= $oCliente->DocumentoNumero;
$DocumentoExpedido 				= $oCliente->DocumentoExpedido;
$FechaNacimiento 				= CambiarFecha($oCliente->FechaNacimiento);
$Empresa 						= $oCliente->Empresa;
$ClaveFiscalTipo 				= $oCliente->ClaveFiscalTipo;
$ClaveFiscalNumero 				= $oCliente->ClaveFiscalNumero;
$Email 							= $oCliente->Email;
$IdNacionalidad 				= $oCliente->IdNacionalidad;
$Nupcia 						= $oCliente->Nupcia;
$ConyugeNombre 					= $oCliente->ConyugeNombre;
$ConyugeApellido 				= $oCliente->ConyugeApellido;
$ConyugeDocumentoNumero 		= $oCliente->ConyugeDocumentoNumero;
$EnteJuridicoOtorgacion 		= $oCliente->EnteJuridicoOtorgacion;
$EnteJuridicoDatosInscripcion 	= $oCliente->EnteJuridicoDatosInscripcion;
$EnteJuridicoFechaInscripcion 	= CambiarFecha($oCliente->EnteJuridicoFechaInscripcion);


/* obtenemos listado de paises */
$arrPaises = $oPaises->GetAll();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

</head>
<body>
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <div align="center">
                    <table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td valign="top">
											<table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
												<tr>
													<td width="20%"><div id="margen" align="left">Cliente:</div></td>
													<td width="80%">
														<div align="left">
															<strong><label id="ClienteRazonSocial"><?=$oCliente->RazonSocial?></label></strong>
														</div>
													</td>
												</tr>
												<tr>
													<td><div id="margen" align="left">Domicilio:</div></td>
													<td>
														<table width="100%" border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td width="36%">
																	<div align="left">
																		<strong><label id="ClienteDomicilio"><?=$oCliente->GetDomicilio()?></label></strong>
																	</div>
																</td>
																<td width="11%"><div align="left">Localidad:</div></td>
																<td width="31%">
																	<div align="left">
																		<strong><label id="ClienteLocalidad"><?=$oDomicilioLocalidad->Nombre?></label></strong>
																	</div>
																</td>
																<td width="5%"><div align="left">CP:</div></td>
																<td width="17%">
																	<div align="left">
																		<strong><label id="ClienteCodigoPostal"><?=$oDomicilioLocalidad->CodigoPostal?></label></strong>
																	</div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td><div id="margen" align="left">Tel&eacute;fono:</div></td>
													<td>
														<div align="left">
															<strong><label id="ClienteTelefono"><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></label></strong>
														</div>
													</td>
												</tr>
												<tr>
													<td><div id="margen" align="left">Condici&oacute;n IVA:</div></td>
													<td>
														<table width="100%" border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td width="43%">
																	<div align="left">
																		<strong><label id="ClienteCondicionIva"><?=$oTipoIva->Nombre?></label></strong>
																	</div>
																</td>
																<td width="8%"><div align="left">CUIT/CUIL:</div></td>
																<td width="49%">
																	<div align="left">
																		<strong><label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></label></strong>
																	</div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
                                </table>						
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
</body>
</html>