<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TALL_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdClienteInterno 		= Cliente::IdClienteInterno;
$IdTallerUnidad			= intval($_REQUEST['IdTallerUnidad']);
$IdMarca				= intval($_REQUEST['IdMarca']);
$Marca					= strval($_REQUEST['Marca']);
$MarcaCodigo			= strval($_REQUEST['MarcaCodigo']);
$Modelo					= strval($_REQUEST['Modelo']);
$IdColor				= intval($_REQUEST['IdColor']);
$Color					= strval($_REQUEST['Color']);
$ColorCodigo			= strval($_REQUEST['ColorCodigo']);
$Dominio				= strval($_REQUEST['Dominio']);
$ModeloAnio				= strval($_REQUEST['ModeloAnio']);
$Cliente				= strval($_REQUEST['Cliente']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$PrefijoVin				= strval($_REQUEST['PrefijoVin']);
$NumeroVin				= strval($_REQUEST['NumeroVin']);
$NumeroMotor			= strval($_REQUEST['NumeroMotor']);
$FechaInicioGarantia	= strval($_REQUEST['FechaInicioGarantia']);
$Concesionario			= strval($_REQUEST['Concesionario']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oTallerUnidades	= new TallerUnidades();
$oClientes			= new Clientes();
$oMarcas			= new Marcas();
$oColores			= new Colores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oTallerUnidad = $oTallerUnidades->GetById($IdTallerUnidad))
{
	header('Location: tallerunidades.php');
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($IdMarca == '' || $IdMarca == '0')
		$err |= 1;
	if ($Modelo == '')
		$err |= 2;
	if ($Dominio == '' && $IdCliente != $IdClienteInterno)
		$err |= 8;
	if ($ModeloAnio == '')
		$err |= 16;
	if ($IdCliente == '' || $IdCliente == '0')
		$err |= 32;
	/*if ($NumeroVin == '' || strlen($NumeroVin) != 17)
		$err |= 128;
	/*if ($NumeroMotor == '')
		$err |= 256;*/
	if ($FechaInicioGarantia == '' && $IdCliente != $IdClienteInterno)
		$err |= 512;

	if ($err == 0)
	{
		$oTallerUnidad->IdMarca			= $IdMarca;
		$oTallerUnidad->Modelo			= $Modelo;
		$oTallerUnidad->IdColor			= $IdColor;
		$oTallerUnidad->Dominio			= $Dominio;
		$oTallerUnidad->ModeloAnio		= $ModeloAnio;
		$oTallerUnidad->IdCliente		= $IdCliente;
		$oTallerUnidad->PrefijoVin			= $PrefijoVin;
		$oTallerUnidad->NumeroVin			= $NumeroVin;
		$oTallerUnidad->NumeroMotor			= $NumeroMotor;
		$oTallerUnidad->FechaInicioGarantia	= $FechaInicioGarantia;
		$oTallerUnidad->Concesionario		= $Concesionario;

		$oTallerUnidad = $oTallerUnidades->Update($oTallerUnidad);

		header("Location: tallerunidades.php" . $strParams);
		exit();
	}
}
else
{
	$oMarca 		= $oMarcas->GetById($oTallerUnidad->IdMarca);
	$IdMarca		= $oMarca->IdMarca;
	$Marca			= $oMarca->Nombre;
	$MarcaCodigo	= $oMarca->Codigo;
	$Modelo			= $oTallerUnidad->Modelo;
	$oColor 		= $oColores->GetById($oTallerUnidad->IdColor);
	$IdColor		= $oColor->IdColor;
	$Color			= $oColor->Nombre;
	$ColorCodigo	= $oColor->Codigo;
	$Dominio		= $oTallerUnidad->Dominio;
	$ModeloAnio		= $oTallerUnidad->ModeloAnio;
	$oCliente 		= $oClientes->GetById($oTallerUnidad->IdCliente);
	$Cliente		= $oCliente->RazonSocial;
	$IdCliente		= $oCliente->IdCliente;
	$PrefijoVin		= $oTallerUnidad->PrefijoVin;
	$NumeroVin		= $oTallerUnidad->NumeroVin;
	$NumeroMotor	= $oTallerUnidad->NumeroMotor;
	$FechaInicioGarantia	= CambiarFecha($oTallerUnidad->FechaInicioGarantia);
	$Concesionario			= $oTallerUnidad->Concesionario;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}
}

function FilterMarca(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('MarcaCodigo').value 	= '';
		Get('Marca').value 		= '';
		Get('IdMarca').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('MarcaCodigo').value 	= oMarca.Codigo;
	Get('Marca').value 		= oMarca.Nombre;
	Get('IdMarca').value 		= oMarca.IdMarca;
}

function FilterColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('ColorCodigo').value 	= '';
		Get('Color').value 			= '';
		Get('IdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('ColorCodigo').value 	= oColor.Codigo;
	Get('Color').value 			= oColor.Nombre;
	Get('IdColor').value 		= oColor.IdColor;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de unidades de taller - Agregar</span></td>
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
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                    <input type="hidden" name="IdMarca" id="IdMarca" value="<?=$IdMarca?>" />
                 	<input type="hidden" name="IdColor" id="IdColor" value="<?=$IdColor?>" />
					<input type="hidden" name="IdTallerUnidad" id="IdTallerUnidad" value="<?=$IdTallerUnidad?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2">
														<table border="0" align="left" cellpadding="0" cellspacing="0">
															<tr>
																<td><div id="margen" align="left">Cliente:</div></td>
																<td><div id="margen" align="left">Id.</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarCliente();" autocomplete="Off" />
																		<script language="javascript">
																		SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
																		</script>
																	</div>
																</td>
																<td>
																	<div align="left">
																		<input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
																		
																	</div>
																</td>
																<td>&nbsp;</td>
																<td><input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
																<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2">
														<table border="0" align="left" cellpadding="0" cellspacing="0">
															<tr>
																<td><div id="margen" align="left">Marca:</div></td>
																<td><div id="margen" align="left">Cod.</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Marca" id="Marca" class="camporFormularioSuggest" maxlength="128" value="<?=$Marca?>" onkeyup="javascript: StrToUpper(this.id);" />
																		<script language="javascript">
																		SUGGESTRequest('Marcas', 'GetAll', 'Marca', 'FilterMarca', 'IdMarca', 'Nombre', 'FilterNombre', null);
																		</script>
																	</div>
																</td>
																<td>
																	<div align="left">
																		<input type="text" name="MarcaCodigo" id="MarcaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$MarcaCodigo?>" readonly="readonly" />
																	</div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la marca</li><?php } ?></td>
												</tr>
                                                <tr>
													<td colspan="2"><div id="margen" align="left">Modelo:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="Modelo" id="Modelo" class="camporFormularioSimple" value="<?=$Modelo?>" onkeyup="javascript: StrToUpper(this.id);" />
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
												</tr>												
												<tr>
													<td colspan="2"><div id="margen" align="left">N&uacute;mero de Chasis:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioSimple" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id);" />
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Debe ingresar un n&uacute;mero de chasis de 17 caracteres.</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2"><div id="margen" align="left">N&uacute;mero de Motor:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="NumeroMotor" id="NumeroMotor" class="camporFormularioSimple" value="<?=$NumeroMotor?>" onkeyup="javascript: StrToUpper(this.id);" />
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Ingrese el n&uacute;mero de motor</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2"><div id="margen" align="left">Fecha de Inicio de Garantia:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="FechaInicioGarantia" id="FechaInicioGarantia" class="camporFormularioMediano" size="12" maxlength="12" value="<?=$FechaInicioGarantia?>" readonly="readonly" />
															<script language="javascript">
																new tcal({'formname': 'frmData', 'controlname': 'FechaInicioGarantia'});
															</script>
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese la fecha de inicio de garantia</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2"><div id="margen" align="left">Concesionario Vendedor:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="Concesionario" id="Concesionario" class="camporFormularioSimple" size="12" maxlength="12" value="<?=$Concesionario?>" onkeyup="javascript: StrToUpper(this.id);" />
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20">&nbsp;</td>
												</tr>
												<tr>
													<td><div id="margen" align="left">A&ntilde;o:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<select name="ModeloAnio" id="ModeloAnio" class="camporFormularioSimple">
																<option value="">[SELECCIONE]</option>
																<?php $year = date('Y'); ?>
																<?php for ($i=$year-15; $i<=$year; $i++) { ?>
																<option value="<?=$i?>" <?=($ModeloAnio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
																<?php } ?>
															</select>
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
												</tr>
												<tr>
													<td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
												</tr>
												<tr>
													<td>
														<table border="0" align="left" cellpadding="0" cellspacing="0">
															<tr>
																<td><div id="margen" align="left">Color:</div></td>
																<td><div id="margen" align="left">Cod.</div></td>
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" />
																		<script language="javascript">
																		SUGGESTRequest('Colores', 'GetAll', 'Color', 'FilterColor', 'IdColor', 'Nombre', 'FilterNombre', null);
																		</script>
																	</div>
																</td>
																<td>
																	<div align="left">
																		<input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
																		
																	</div>
																</td>
															</tr>
														</table>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2">&nbsp;</td>
												</tr>
												 <tr>
													<td colspan="2"><div id="margen" align="left">Dominio:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="Dominio" id="Dominio" class="camporFormularioSimple" maxlength="12" value="<?=$Dominio?>" onkeyup="javascript: StrToUpper(this.id);" />
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el dominio</li><?php } ?></td>
												</tr>
                                            </table>
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
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'tallerunidades.php<?=$strParams?>';" value="Cancelar" />
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
LoadListas('CodigoComercial', '<?=$NumeroVinPrefijo?>', '<?=$CodigoComercial?>');
</script>

</body>
</html>