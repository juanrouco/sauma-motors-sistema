<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtenemos datos enviados */
$IdFormulario 		= intval($_REQUEST['IdFormulario']);
$IdTipoFormulario 	= intval($_REQUEST['IdTipoFormulario']);
$OffsetX 			= floatval($_REQUEST['OffsetX']);
$OffsetY 			= floatval($_REQUEST['OffsetY']);

/* declaramos variables necesarias */
$oFormularios 		= new Formularios();
$oGestorias			= new Gestorias();
$oGestoriaSocios	= new GestoriaSocios();
$oClientes			= new Clientes();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();
	
$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria);

$NumeroFormulario01 = '';
if (!$oFormulario01 = $oFormularios->GetByIdGestoriaIdTipoFormulario($oFormulario->IdGestoria, TipoFormulario::Formulario01Nacional))
{
	$oFormulario01 = $oFormularios->GetByIdGestoriaIdTipoFormulario($oFormulario->IdGestoria, TipoFormulario::Formulario01Importado);
}

$NumeroFormulario01 = $oFormulario01->Numero;

/* formamos cadena con paramentros */
$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

switch ($oFormulario->IdTipoFormulario)
{
	case TipoFormulario::Formulario01Importado:
		$File = 'gestorias_pdf_1.php' . $strParams;
		break;

	case TipoFormulario::Formulario01Nacional:
		$File = 'gestorias_pdf_1.php' . $strParams;
		break;
		
	case TipoFormulario::TituloAutomotor:
		$File = 'gestorias_pdf_3.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario12:
		$File = 'gestorias_pdf_4.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario13ACapital:
		$File = 'gestorias_pdf_6.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario13AProvincia:
		$File = 'gestorias_pdf_5.php' . $strParams;
		break;

	case TipoFormulario::Formulario03:
		$File = 'gestorias_pdf_7.php' . $strParams;
		break;

	case TipoFormulario::ContratoPrenda:
		$File = 'gestorias_pdf_8.php' . $strParams;
		break;

	case TipoFormulario::ContratoPrendaStandardBank:
		$File = 'gestorias_pdf_9.php' . $strParams;
		break;
		
	case TipoFormulario::Formulario02:
		$File = 'gestorias_pdf_11.php' . $strParams;
		break;
}

$arrGestoriaSocios	= $oGestoriaSocios->GetAllByGestoria($oGestoria);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript" type="text/javascript">

function Generar(value)
{
	var File = '<?=$File?>';
	File +="&Declaraciones=" + document.getElementById('Declaraciones').value;
	if (document.getElementById('Embargos').checked)
		File +="&Embargos=1";
	if (document.getElementById('Levantamiento').checked)
		File +="&Levantamiento=1";
	if (document.getElementById('Inhibiciones').checked)
		File +="&Inhibiciones=1";
	if (document.getElementById('LevantamientoInhibiciones').checked)
		File +="&LevantamientoInhibiciones=1";
	if (document.getElementById('CertificadoDominio').checked)
		File +="&CertificadoDominio=1";
	if (document.getElementById('InformeDominio').checked)
		File +="&InformeDominio=1";
	if (document.getElementById('AnotacionComunicaciones').checked)
		File +="&AnotacionComunicaciones=1";
	if (document.getElementById('AnotacionComunicaciones2').checked)
		File +="&AnotacionComunicaciones2=1";
	if (document.getElementById('CertificadoTransferencia').checked)
		File +="&CertificadoTransferencia=1";
	if (document.getElementById('DuplicadoBajaVehiculo').checked)
		File +="&DuplicadoBajaVehiculo=1";
	if (document.getElementById('DuplicadoBajaMotor').checked)
		File +="&DuplicadoBajaMotor=1";
	if (document.getElementById('DuplicadoBajaChasis').checked)
		File +="&DuplicadoBajaChasis=1";
	if (document.getElementById('DuplicadoDenunciaRobo').checked)
		File +="&DuplicadoDenunciaRobo=1";
	if (document.getElementById('DuplicadoComunicacionRecupero').checked)
		File +="&DuplicadoComunicacionRecupero=1";
	if (document.getElementById('AsignacionCodificacion').checked)
		File +="&AsignacionCodificacion=1";
	if (document.getElementById('DuplicadoTitulo').checked)
		File +="&DuplicadoTitulo=1";
	if (document.getElementById('DuplicadoCedula').checked)
		File +="&DuplicadoCedula=1";
	if (document.getElementById('CambioUso').checked)
		File +="&CambioUso=1";
	if (document.getElementById('CertificadoConstanciasRegistrables').checked)
		File +="&CertificadoConstanciasRegistrables=1";
	if (document.getElementById('OtrosTramites').checked)
		File +="&OtrosTramites=1";
	
	window.open(File,'_newtab');	
}

function GenerarSocio(IdSocio)
{
	var File = '<?=$File?>';
	File = File + "&IdSocio=" + IdSocio + "&Declaraciones=" + document.getElementById('Declaraciones').value;
	
	window.open(File,'_newtab');	
}

</script>
</head>
<body>
	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Formulario 02 - Declaraciones</span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top">
				<table width="60%" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="center" width="20"><input type="checkbox" name="Embargos" id="Embargos" value="1" /></td>
						<td>ANOTACION DE EMBARGOS, LITIS, MEDIDAS DE NO INNOVAR Y OTRAS MEDIDAS PRECAUTORIAS RELACIONADAS CON AUTOMOTORES</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="Levantamiento" id="Levantamiento" value="1" /></td>
						<td>LEVANTAMIENTO DE EMBARGOS, LITIS, MEDIDAS DE NO INNOVAR Y OTRAS MEDIDAS PRECAUTORIAS RELACIONADAS CON AUTOMOTORES</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="Inhibiciones" id="Inhibiciones" value="1" /></td>
						<td>ANOTACION DE INHIBICIONES, AFECTACIONES Y OTRAS MEDIDAS PRECAUTORIAS DE TIPO PERSONAL</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="LevantamientoInhibiciones" id="LevantamientoInhibiciones" value="1" /></td>
						<td>LEVANTAMIENTO DE INHIBICIONES, AFECTACIONES Y OTRAS MEDIDAS PRECAUTORIAS DE TIPO PERSONAL</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="CertificadoDominio" id="CertificadoDominio" value="1" /></td>
						<td>CERTIFICADO DE ESTADO DE DOMINIO, BLOQUEA EL DOMINIO POR QUINCE (15) DIAS HABILES</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="InformeDominio" id="InformeDominio" value="1" /></td>
						<td>INFORME DE ESTADO DE DOMINIO. NO BLOQUEA EL DOMINIO.</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="AnotacionComunicaciones" id="AnotacionComunicaciones" value="1" /></td>
						<td>ANOTACION DE COMUNICACIONES DE SINIESTROS QUE FORMULEN LAS COMPA&Ntilde;IAS ASEGURADORAS</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="AnotacionComunicaciones2" id="AnotacionComunicaciones2" value="1" /></td>
						<td>ANOTACION DE COMUNICACIONES QUE FORMULEN LAS AUTORIDADES POLICIALES</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="CertificadoTransferencia" id="CertificadoTransferencia" value="1" /></td>
						<td>CERTIFICADO DE TRANSFERENCIA</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoBajaVehiculo" id="DuplicadoBajaVehiculo" value="1" /></td>
						<td>DUPLICADO DE CERTIFICADO DE BAJA DE VEHICULO</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoBajaMotor" id="DuplicadoBajaMotor" value="1" /></td>
						<td>DUPLICADO DE CERTIFICADO DE BAJA DE MOTOR</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoBajaChasis" id="DuplicadoBajaChasis" value="1" /></td>
						<td>DUPLICADO DE CERTIFICADO DE BAJA DE CARROCERIA Y/O CHASIS</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoDenunciaRobo" id="DuplicadoDenunciaRobo" value="1" /></td>
						<td>DUPLICADO DE CERTIFICADO DE DENUNCIA DE ROBO O HURTO</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoComunicacionRecupero" id="DuplicadoComunicacionRecupero" value="1" /></td>
						<td>DUPLICADO DE CERTIFICADO DE COMUNICACION DE RECUPERO</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="AsignacionCodificacion" id="AsignacionCodificacion" value="1" /></td>
						<td>ASIGNACION CODIFICACIONES DE IDENTIFICACION MOTOR Y/O CHASIS</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoTitulo" id="DuplicadoTitulo" value="1" /></td>
						<td>DUPLICADO DE TITULO</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="DuplicadoCedula" id="DuplicadoCedula" value="1" /></td>
						<td>DUPLICADO DE CEDULA, RENOVACION O CEDULA ADICIONAL</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="CambioUso" id="CambioUso" value="1" /></td>
						<td>CAMBIO DE USO</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="CertificadoConstanciasRegistrables" id="CertificadoConstanciasRegistrables" value="1" /></td>
						<td>CERTIFICADO DE OTRAS CONSTANCIAS REGISTRABLES</td>
					</tr>
					<tr>
						<td align="center" width="20"><input type="checkbox" name="OtrosTramites" id="OtrosTramites" value="1" /></td>
						<td>OTROS TRAMITES</td>
					</tr>
				</table>
			</td>
		</tr>	
		<tr>
			<td valign="top">&nbsp;</td>
		</tr>	
		<tr>
			<td valign="top" align="center">
				<textarea id="Declaraciones" name="Declaraciones" style="width:550px;height:200px">SOLICITO QUE LA INSCRIPCION INICIAL QUEDE CONDICIONADA A LA INSCRIPCION DE LA PRENDA - F. 01 N <?= $NumeroFormulario01 ?> - F. 03 N</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="center">
				<table align="center" border="0" width="550" cellpadding="0" cellspacing="0">
					<tr align="right">
						<td>
							<input type="button" class="botonBasico" id="btnAceptar" value="Generar" onclick="javascript: Generar();" />
						</td>
					</tr>
					<?php
					if ($oGestoria->SociedadHecho && $arrGestoriaSocios)
					{
						foreach ($arrGestoriaSocios as $oGestoriaSocio)
						{
							$oCliente = $oClientes->GetById($oGestoriaSocio->IdCliente);
					?>
					<tr align="right">
						<td>
							&nbsp;
						</td>
					</tr>
					<tr align="right">
						<td>
							<input type="button" class="botonBasico" id="btnAceptar_<?= $oGestoriaSocio->IdCliente ?>" value="Generar Frente - <?= $oCliente->RazonSocial ?>" onclick="javascript: GenerarSocio(<?= $oGestoriaSocio->IdGestoriaSocio ?>);" />								
						</td>
					</tr>
					<?php
						}
					}
					?>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</body>