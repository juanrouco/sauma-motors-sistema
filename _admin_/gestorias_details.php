<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdGestoria	= intval($_REQUEST['IdGestoria']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oGestorias			= new Gestorias();
$oMinutas 			= new Minutas();
$oFormularios		= new Formularios();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oTiposFormulario 	= new TiposFormulario();
$oGestoriaSocios	= new GestoriaSocios();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($IdGestoria))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la localidad */
if (!$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la localidad fiscal */
if (!$oLocalidadFiscal = $oLocalidades->GetById($oGestoria->DomicilioFiscalIdLocalidad))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la marca */
if (!$oMarca = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);
$oLocalidadCondominio = $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);

/* obtenemos los formularios asociados a la getoris */
$arrFormularios = $oGestoria->GetAllFormularios();

$arrGestoriaSocios	= $oGestoriaSocios->GetAllByGestoria($oGestoria);

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Detalle</span></td>
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
					<input type="hidden" name="IdGestoria" id="IdGestoria" value="<?=$IdGestoria?>" />
					<input type="hidden" name="IdMinuta" id="IdMinuta" value="" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de Operaci&oacute;n</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DATOS GENERALES</strong></div></td>
                                                    </tr>                                  
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="30%"><div id="margen" align="left">Porcentaje Titularidad:</div></td>
                                                                    <td width="70%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=number_format($oGestoria->PorcentajeTitularidad, 2)?> %</label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="30%"><div id="margen" align="left">N&uacute;mero Certificado <?=Origen::GetDescripcionById($oModelo->Origen)?>:</div></td>
                                                                    <td width="70%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oGestoria->NumeroCertificado?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="30%"><div id="margen" align="left">Tipo de Uso:</div></td>
                                                                    <td width="70%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=UsoTipos::getById($oGestoria->IdTipoUso)?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DATOS DEL CLIENTE</strong></div></td>
                                                    </tr>                                  
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oCliente->RazonSocial?></label>
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
                                                                                        <label id="ClienteDomicilio"><?=$oCliente->GetDomicilio()?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidad->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidad->CodigoPostal?></label>
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
                                                                        	<label id="ClienteTelefono"><?=$oCliente->TelefonoCodigoArea . ' - ' . $oCliente->Telefono?></label>
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
                                                                                        <label id="ClienteCondicionIva"><?=$oTipoIva->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT/CUIL:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    
                                               	<?php if (($oGestoria->PorcentajeTitularidad < 100) && ($oGestoria->IdClienteCondominio != '')) { ?>
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DATOS DEL CONDOMINIO</strong></div></td>
                                                    </tr>                                  
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oClienteCondominio->RazonSocial?></label>
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
                                                                                        <label id="ClienteDomicilio"><?=($oClienteCondominio) ? $oClienteCondominio->GetDomicilio() : ''?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidadCondominio->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidadCondominio->CodigoPostal?></label>
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
                                                                        	<label id="ClienteTelefono"><?=$oClienteCondominio->TelefonoCodigoArea . ' - ' . $oClienteCondominio->Telefono?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">CUIT:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                              	<?php } ?>
                                                   <?php if ($oGestoria->SociedadHecho && $arrGestoriaSocios) { ?>
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DATOS DE LOS SOCIOS</strong></div></td>
                                                    </tr>   
													<?php
													foreach ($arrGestoriaSocios as $oGestoriaSocio)
													{
														$oClienteSocio = $oClientes->GetById($oGestoriaSocio->IdCliente);
														$oLocalidadSocio = $oLocalidades->GetById($oClienteSocio->DomicilioIdLocalidad);
													?>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oClienteSocio->RazonSocial?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td width="20%"><div id="margen" align="left">Porcentaje:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"><?=$oGestoriaSocio->Porcentaje?>%</label>
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
                                                                                        <label id="ClienteDomicilio"><?=($oClienteSocio) ? $oClienteSocio->GetDomicilio() : ''?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidadSocio->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidadSocio->CodigoPostal?></label>
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
                                                                        	<label id="ClienteTelefono"><?=$oClienteSocio->TelefonoCodigoArea . ' - ' . $oClienteSocio->Telefono?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">CUIT:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <label id="ClienteCuit"><?=ClaveFiscalTipos::GetById($oClienteSocio->ClaveFiscalTipo) . ': ' . $oClienteSocio->ClaveFiscalNumero?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                              	<?php 
													}
												} ?>
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DOMICILIO FISCAL</strong></div></td>
                                                    </tr>                                  
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="19%"><div id="margen" align="left">Domicilio:</div></td>
                                                                    <td width="81%">
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="36%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilio"><?=$oGestoria->DomicilioFiscalCalle . ' ' . $oCliente->DomicilioFiscalNumero?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"><?=$oLocalidadFiscal->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"><?=$oLocalidadFiscal->CodigoPostal?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td height="30" class="bordeGrisFondo"><div align="center"><strong>DATOS DE LA UNIDAD</strong></div></td>
                                                    </tr>                                  
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Marca:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="VehiculoMarca"><?=$oMarca->Nombre?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tipo:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="53%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoTipo"><?=$oTipoModelo->Nombre?></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="7%"><div align="left">A&ntilde;o:</div></td>
                                                                            	<td width="40%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoAnio"><?=$oModelo->Anio?></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Modelo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoModelo"><?=$oModelo->DenominacionModelo?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Motor:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroMotor"><?=$oUnidad->NumeroMotor?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Chasis:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroChasis"><?=$oUnidad->NumeroChasis?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Color:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoColor"><?=$oColor->Nombre?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Equipo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoDenominacionComercial"><?=$oModelo->DenominacionComercial?></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Iva:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoIva"><?=$oModelo->Iva?> %</label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Formularios Generados</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                <?php if ($arrFormularios) { ?>
                                                
                                                	<tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table width="80%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                                <tr class="bordeGrisFondo">
                                                                    <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Formulario</strong></div></td>
                                                                    <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero</strong></div></td>
                                                                    <td width="92" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                                                                </tr>
                                                      
                                                            <?php foreach ($arrFormularios as $oFormulario) { ?>
                                                            	<?php $oTipoFormulario = $oTiposFormulario->GetById($oFormulario->IdTipoFormulario); ?>
                                                      
                                                                <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                                    <td width="181" height="25"><div id="margen"><?=$oTipoFormulario->Nombre?></div></td>
                                                                    <td width="203" height="25"><div id="margen"><?=$oFormulario->Numero?></div></td>
                                                                    <td width="92" height="25" valign="middle">
                                                                        <div align="center">
                                                                        	<!--
                                                                            <a href="gestorias_pdf_.php<?=$strParams?>&IdFormulario=<?=$oFormulario->IdFormulario?>" target="_blank"><img src="images/iconos/jpg.png" alt="Imprimir" border="0" /></a> - 
                                                                            -->
                                                                            <a href="gestorias_pdf_print1.php<?=$strParams?>&IdFormulario=<?=$oFormulario->IdFormulario?>" target="_blank"><img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="10">
                                                                        <div align="center">
                                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                                <tr>
                                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                
                                                            <?php } ?>
                                                            
                                                            </table>		
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                
                                                <?php } else { ?>  
                                                
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table width="80%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div align="center"><strong>No hay formularios generados.</strong></div></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            </table>		
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                      
                                                <?php } ?>
                                            
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'gestorias.php<?=$strParams?>';" value="Volver" />
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