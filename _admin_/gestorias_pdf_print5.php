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
$oPrendas			= new Prendas();
$oPrendaConyuges	= new PrendaConyuges();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();
	
/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
	exit();
	
if (!$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
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
$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);
$Nacionalidad = $oNacionalidad->Nombre;
	
/* formamos cadena con paramentros */
$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

$FileActual = 'gestorias_pdf_print5.php';
$File = 'gestorias_pdf_10.php' . $strParams;

$arrGestoriaSocios	= $oGestoriaSocios->GetAllByGestoria($oGestoria);

$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge) && !$oGestoria->CondominioConyuge)
{
	$oNacionalidadConyuge 	= $oPaises->GetById($oPrendaConyuge->IdNacionalidad);
	$NacionalidadConyuge = $oNacionalidadConyuge->Nombre;
}


$arrFiadores = $oPrenda->GetAllFiadores();

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
	<form id="frmData" name="frmData" method="post" action="<?= $File ?>" target="_blank">
		<input type="hidden" id="ImprimeLeyenda" name="ImprimeLeyenda" value="" />
		<input type="hidden" id="IdSocio" name="IdSocio" value="" />
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			<tr>
				<td>
					
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Contrato de Prenda GPAT - Informaci&oacute;n</span></td>
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
										<td valign="top"><strong>Salvar Informaci&oacute;n Frente:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="SalvarInformacion" name="SalvarInformacion" class="camporFormularioSimple" value="<?= $SalvarInformacion ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top">&nbsp;</td>
									</tr>
									<tr>
										<td valign="top"><strong>Salvar Informaci&oacute;n Final:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<textarea id="Observaciones" name="Observaciones" style="width:550px;height:200px" onkeyup="javascript: StrToUpper(this.id);"></textarea>
										</td>
									</tr>
									<tr>
										<td valign="top"><strong>Nacionalidad:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Nacionalidad" name="Nacionalidad" class="camporFormularioSimple" value="<?= $Nacionalidad ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<?php
									if ($oNacionalidadConyuge)
									{
									?>
									<tr>
										<td valign="top"><strong>Nacionalidad Conyuge:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="NacionalidadConyuge" name="NacionalidadConyuge" class="camporFormularioSimple" value="<?= $NacionalidadConyuge ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<?php
									}
									?>
									<?php
									if ($arrFiadores)
									{
										for ($j=0; $j<count($arrFiadores); $j++)
										{
											$oFiador = $arrFiadores[$j];
											$oNacionalidadFiador 	= $oPaises->GetById($oFiador->IdNacionalidad);
									?>
									<tr>
										<td valign="top"><strong>Nacionalidad Fiador N&deg;<?= $oFiador->Posicion ?>:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="NacionalidadFiador<?= $j ?>" name="NacionalidadFiador<?= $j ?>" class="camporFormularioSimple" value="<?= $oNacionalidadFiador->Nombre ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<?php
										}
									}
									?>
									<tr>
										<td valign="top"><strong>Intereses Punitorios:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="InteresesPunitorios" name="InteresesPunitorios" class="camporFormularioSimple" value="<?= $InteresesPunitorios ?>" onkeyup="javascript: StrToUpper(this.id);" />
										</td>
									</tr>
									<tr>
										<td valign="top"><strong>Valor 9.4:</strong></td>
									</tr>
									<tr>
										<td valign="top" align="left">
											<input type="text" id="Valor94" name="Valor94" class="camporFormularioSimple" value="<?= $Valor94 ?>" onkeyup="javascript: StrToUpper(this.id);" />
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
								<input type="button" class="botonBasico" id="btnAceptar" value="Generar Contrato" onclick="javascript: Generar();" />	
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