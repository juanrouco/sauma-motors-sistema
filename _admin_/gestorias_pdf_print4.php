<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtenemos datos enviados */
$IdFormulario 		= intval($_REQUEST['IdFormulario']);
$IdTipoFormulario 	= intval($_REQUEST['IdTipoFormulario']);
$IdSocio		 	= intval($_REQUEST['IdSocio']);
$OffsetX 			= floatval($_REQUEST['OffsetX']);
$OffsetY 			= floatval($_REQUEST['OffsetY']);

$oClienteCondominio = null;

/* declaramos variables necesarias */
$oFormularios 		= new Formularios();
$oGestorias			= new Gestorias();
$oGestoriaSocios	= new GestoriaSocios();
$oMinutas			= new Minutas();
$oClientes			= new Clientes();
$oLocalidades		= new Localidades();
$oProvincias		= new Provincias();
$oPaises			= new Paises();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();
	
/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
	exit();

if ($oGestoria->SociedadHecho && $IdSocio)
{
	$oGestoriaSocio = $oGestoriaSocios->GetById($IdSocio);
	$porcentajeTitularidad = $oGestoriaSocio->Porcentaje;
	if (!$oCliente = $oClientes->GetById($oGestoriaSocio->IdCliente))
		exit();
	$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);
}
else
{
	if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
		exit();
	$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);
}

$oLocalidadNacimiento = $oLocalidades->GetById($oCliente->NacimientoIdLocalidad);
$oProvinciaNacimiento = $oProvincias->GetById($oLocalidadNacimiento->IdProvincia);
	
/* formamos cadena con paramentros */
$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

$FileActual = 'gestorias_pdf_print4.php';
switch ($oFormulario->IdTipoFormulario)
{
	case TipoFormulario::Formulario01Importado:
		$File = 'gestorias_pdf_2.php' . $strParams;
		break;

	case TipoFormulario::Formulario01Nacional:
		$File = 'gestorias_pdf_1.php' . $strParams;
		break;
}

$FileReverso = 'gestorias_pdf_1_reverso.php' . $strParams;

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
	var form = Get('frmData');
	form.action = File;
	
	form.submit();	
}

function GenerarSocio(IdSocio)
{
	Get('IdSocio').value = IdSocio;
	var form = Get('frmData');
	form.submit();	
}

function GenerarReverso()
{
	var FileReverso = '<?=$FileReverso?>';
	
	if (confirm('Desea imprimir con leyenda?'))
	{
		Get('ImprimeLeyenda').value = '1';
	}
	else
	{
		Get('ImprimeLeyenda').value = '0';
	}
	
	var form = Get('frmData');
	form.action = FileReverso;
	
	form.submit();	
}

function CedulaAzul() {
	debugger;
	Get('Observaciones').value = Get('Observaciones').value + 'SOLICITA CEDULA PARA AUTORIZADO A CONDUCIR A FAVOR DE:';
	return false;
}

</script>
</head>
<body>
	<form target="_blank" id="frmData" name="frmData" method="post" action="<?= $File ?>">
		<input type="hidden" id="ImprimeLeyenda" name="ImprimeLeyenda" value="" />
		<input type="hidden" id="IdSocio" name="IdSocio" value="" />
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>
					
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Formulario 01 - Informaci&oacute;n</span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td>&nbsp;</td>
							<td width="550">
								<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td valign="top"><strong>Nacionalidad y Sexo:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Nacionalidad" name="Nacionalidad" class="camporFormularioSimple" value="NACIONALIDAD: <?= $oNacionalidad->Nacionalidad ?> / SEXO: " onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<tr>
										<td valign="top"><strong>Email Cliente:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Email" name="Email" class="camporFormularioSimple" value="<?= $oCliente->Email ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<?php
									if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
									{
										$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);
										$oNacionalidadCondiminio = $oPaises->GetById($oClienteCondominio->IdNacionalidad);	
									?>
									<tr>
										<td valign="top"><strong>Nacionalidad y Sexo Condominio:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="NacionalidadCondominio" name="NacionalidadCondominio" class="camporFormularioSimple" value="NACIONALIDAD: <?= $oNacionalidadCondominio->Nacionalidad ?> / SEXO: " onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top"><strong>Email Condominio:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="EmailCondominio" name="EmailCondominio" class="camporFormularioSimple" value="<?= $oClienteCondominio->Email ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td valign="top"><strong>Localidad de Nacimiento del Cliente:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="LocalidadNacimiento" name="LocalidadNacimiento" class="camporFormularioSimple" value="<?= $oLocalidadNacimiento->Nombre . ', ' . $oProvinciaNacimiento->Nombre ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<?php
									if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
									{
										$oLocalidadCondominioNacimiento = $oLocalidades->GetById($oClienteCondominio->NacimientoIdLocalidad);
										$oProvinciaCondominioNacimiento = $oProvincias->GetById($oLocalidadCondominioNacimiento->IdProvincia);
									?>
									<tr>
										<td valign="top"><strong>Localidad de Nacimiento del Condominio:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="LocalidadNacimientoCondominio" name="LocalidadNacimientoCondominio" class="camporFormularioSimple" value="<?= $oLocalidadCondominioNacimiento->Nombre . ', ' . $oProvinciaCondominioNacimiento->Nombre ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td valign="top"><strong>Observaciones:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<textarea id="Observaciones" name="Observaciones" style="width:550px;height:200px" onkeyup="javascript: StrToUpper(this.id);"></textarea>
										</td>
									</tr>
									<tr>
										<td valign="top"><a href="#" class="linkMenu" onclick="javascript:CedulaAzul();">Utiliza C&eacute;dula Azul</a></td>
									</tr>
								</table>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
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
								<input type="button" class="botonBasico" id="btnAceptar" value="Generar Frente" onclick="javascript: Generar();" />	
								<input type="button" class="botonBasico" id="btnAceptar" value="Generar Reverso" onclick="javascript: GenerarReverso();" />						
							</td>
						</tr>
						<?php
						if ($oGestoria->SociedadHecho && $arrGestoriaSocios && !$IdSocio)
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
	</form>
</body>