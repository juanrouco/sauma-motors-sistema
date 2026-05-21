<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

$IdOrdenTrabajoFranquicia	= intval($_REQUEST['IdOrdenTrabajoFranquicia']);
$IdOrdenTrabajo				= intval($_REQUEST['IdOrdenTrabajo']);
$Importe					= floatval($_REQUEST['Importe']);
$Cliente					= strval($_REQUEST['Cliente']);
$IdCliente					= intval($_REQUEST['IdCliente']);
$Descripcion				= strval($_REQUEST['Descripcion']);
$Submit						= (isset($_REQUEST['Submitted']));

$err						= 0;
$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
$oOrdenesTrabajo			= new OrdenesTrabajo();
$oTallerUnidades			= new TallerUnidades();
$oClientes					= new Clientes();

$strParams = '?' . $_SERVER['QUERY_STRING'];
$oOrdenTrabajoFranquicia = $oOrdenesTrabajoFranquicias->GetById($IdOrdenTrabajoFranquicia);
$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);

if ($Submit)
{
	if ($Importe == '')
		$err |= 1;
	if ($Descripcion == '')
		$err |= 2;
	if ($IdCliente == '' || $IdCliente == 0)
		$err |= 4;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$Importe	= str_replace(",", ".", $Importe);
		
		$oOrdenTrabajoFranquicia->IdCliente			= $IdCliente;
		$oOrdenTrabajoFranquicia->Importe			= $Importe;
		$oOrdenTrabajoFranquicia->IdOrdenTrabajo	= $IdOrdenTrabajo;
		$oOrdenTrabajoFranquicia->Descripcion		= $Descripcion;
		
		$oOrdenTrabajoFranquicia = $oOrdenesTrabajoFranquicias->Update($oOrdenTrabajoFranquicia);

		header('Location: ordenestrabajo_facturacion.php' . $strParams);
		exit();
	}
}
else
{
	$oCliente = $oClientes->GetById($oOrdenTrabajoFranquicia->IdCliente);
	$IdCliente = $oCliente->IdCliente;
	$Cliente = $oCliente->RazonSocial;
	$Importe			= $oOrdenTrabajoFranquicia->Importe;
	$IdOrdenTrabajo	= $oOrdenTrabajoFranquicia->IdOrdenTrabajo;
	$Descripcion		= $oOrdenTrabajoFranquicia->Descripcion;
}

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de la orden de trabajo N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?> - Modificar Franquicia</span></td>
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
                    <input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
                    <input type="hidden" name="IdOrdenTrabajoFranquicia" id="IdOrdenTrabajoFranquicia" value="<?=$IdOrdenTrabajoFranquicia?>" />
                 	
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
                                                    <td valign="top">
                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">                                                           
                                                            <tr>
																<td colspan="2"><div id="margen" align="left">Descripci&oacute;n:</div></td>
															</tr>
															<tr>
																<td colspan="2">
																	<div align="left">
																		<input type="text" name="Descripcion" id="TitDescripcionulo" class="camporFormularioSimple"  value="<?=$Descripcion?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese una descripci&oacute;n</li><?php } ?></td>
                                                            </tr>
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
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un cliente</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td colspan="2"><div id="margen" align="left">Importe:</div></td>
															</tr>
															<tr>
																<td colspan="2">
																	<div align="left">
																		<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?=$Importe?>" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
                                                            <tr class="tr_costoFijo">
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese un importe</li><?php } ?></td>
                                                            </tr>
																														
                                                        </table>
                                                    </td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo_facturacion.php<?=$strParams?>';" value="Cancelar" />
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

<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>

</body>
</html>