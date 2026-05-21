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
}
else
{
	if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
		exit();
}

$oLocalidadNacimiento = $oLocalidades->GetById($oCliente->NacimientoIdLocalidad);
$oProvinciaNacimiento = $oProvincias->GetById($oLocalidadNacimiento->IdProvincia);
	
/* formamos cadena con paramentros */
$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

$FileActual = 'gestorias_pdf_print5.php';
$File = 'gestorias_pdf_7.php' . $strParams;

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

</script>
</head>
<body>
	<form id="frmData" name="frmData" method="post" action="<?= $File ?>">
		<input type="hidden" id="ImprimeLeyenda" name="ImprimeLeyenda" value="" />
		<input type="hidden" id="IdSocio" name="IdSocio" value="" />
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>
					
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Formulario 03 - Informaci&oacute;n</span></td>
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
										<td valign="top"><strong>Localidad:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Localidad" name="Localidad" class="camporFormularioSimple" value="<?= $SalvarInformacion ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<tr>
										<td valign="top"><strong>Partido:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Partido" name="Partido" class="camporFormularioSimple" value="<?= $SalvarInformacion ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
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
								<input type="button" class="botonBasico" id="btnAceptar" value="Generar Formulario" onclick="javascript: Generar();" />	
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
</body>