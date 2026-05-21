<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();


/* obtiene datos enviados */
$Cliente				= strval($_REQUEST['Cliente']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Comentarios			= strval($_REQUEST['Comentarios']);
$Importe				= floatval($_REQUEST['Importe']);
$IdFactura				= intval($_REQUEST['IdFactura']);
$Factura				= strval($_REQUEST['Factura']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oNotaCredito		= new NotaCredito();
$oNotasCredito		= new NotasCredito();
$oClientes			= new Clientes();
$oComprobantes		= new Comprobantes();
$oGeneradorNotasCredito = new GeneradorNotasCredito();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];
if ($Submit)
{
	/* validaciones... */
	if ($IdCliente == '' || $IdCliente == '0')
		$err |= 1;
	if ($Factura == '')
		$err |= 2;
	if ($Comentarios == '')
		$err |= 4;
	if ($Importe == '')
		$err |= 8;
	
	if ($err == 0)
	{
		$oCliente = $oClientes->GetById($IdCliente);
		if ($oCliente->IdTipoIva == TipoIva::RI)
			$oFactura = $oComprobantes->GetByNumeroPrefijo(ComprobanteTipos::FacturaA, '0002', str_pad($Factura, 8, 0, STR_PAD_LEFT));
		else
			$oFactura = $oComprobantes->GetByNumeroPrefijo(ComprobanteTipos::FacturaB, '0002', str_pad($Factura, 8, 0, STR_PAD_LEFT));
	
		$oNotaCredito->IdCliente				= $IdCliente;
		if ($oFactura)
			$oNotaCredito->IdFactura			= $oFactura->IdComprobante;
		$oNotaCredito->Comentarios				= $Comentarios;
		$oNotaCredito->Importe					= $Importe;
		$oNotaCredito->Fecha					= date('d-m-Y');
		//print_r($oNotaCredito);exit;
		$oNotaCredito = $oNotasCredito->Create($oNotaCredito);
		
		$oGeneradorNotasCredito->Imprimir($oNotaCredito);
//exit;
		header("Location: notascredito.php" . $strParams);
		exit();
	}
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
	/*if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}*/
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Notas de Cr&eacute;dito - Agregar</span></td>
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
																<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2">
														<table border="0" align="left" cellpadding="0" cellspacing="0">
															<tr>
																<td><div id="margen" align="left">Factura:</div></td>
																
															</tr>
															<tr>
																<td>
																	<div align="left">
																		<input type="text" name="Factura" id="Factura" class="camporFormularioSuggest" maxlength="128" value="<?=$Factura?>" onkeyup="javascript: StrToUpper(this.id);" />																		
																	</div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese la factura</li><?php } ?>&nbsp;</td>
												</tr>
                                                <tr>
													<td colspan="2"><div id="margen" align="left">Comentarios:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="Comentarios" id="Comentarios" class="camporFormularioSimple" value="<?=$Comentarios?>" onkeyup="javascript: StrToUpper(this.id);" />
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
													<td></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese los comentarios</li><?php } ?></td>
												</tr>
												<tr>
													<td colspan="2"><div id="margen" align="left">Importe:</div></td>
												</tr>
												<tr>
													<td>
														<div align="left">
															<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?=$Importe?>" onkeyup="javascript: StrToUpper(this.id);" />
															<span style="color:#FF0000;">&nbsp;(*)</span>
														</div>
													</td>
													<td><span style="color:#FF0000;">&nbsp;</span></td>
												</tr>
												<tr>
													<td colspan="2" height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Debe ingresar el importe.</li><?php } ?></td>
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
									<?php
									if  (!$popup)
									{
									?>
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'notascredito.php<?=$strParams?>';" value="Cancelar" />
									<?php
									}
									else
									{
									?>
									<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.close();" value="Cancelar" />
									<?php
									}
									?>
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