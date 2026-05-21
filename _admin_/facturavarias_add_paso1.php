<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACV_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCliente			= intval($_REQUEST['IdCliente']);
$Cliente			= strval($_REQUEST['Cliente']);
$IdComprobante		= intval($_REQUEST['IdComprobante']);
$NumeroComprobante	= strval($_REQUEST['NumeroComprobante']);
$Fecha				= strval($_REQUEST['Fecha']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oFacturaVaria 	= new FacturaVaria();
$oFacturaVarias	= new FacturaVarias();
$oComprobantes 	= new Comprobantes();
$oClientes 		= new Clientes();
$oTiposIva 		= new TiposIva();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdCliente == '')
		$err |= 1;
	if ($NumeroComprobante == '')
		$err |= 2;
	if ($Fecha == '')
		$err |= 4;
		
	if ($oComprobante = $oComprobantes->GetById($IdComprobante))
	{
		if ($oComprobante->IdEstado != ComprobanteEstados::Libre)
			$err |= 8;
		if ($oComprobante->Numero != $NumeroComprobante | strlen($oComprobante->Numero) != strlen($NumeroComprobante))
			$err |= 16;
	}
	else
	{
		$err |= 32;
	}
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oFacturaVaria->IdCliente			= $IdCliente;
		$oFacturaVaria->IdComprobante		= $IdComprobante;
		$oFacturaVaria->NumeroComprobante	= $NumeroComprobante;
		$oFacturaVaria->Fecha				= $Fecha;

		if ($oFacturaVaria = $oFacturaVarias->Create($oFacturaVaria))
		{
			/* actualizamos el estado del comprobante */
			if ($oComprobante = $oComprobantes->GetById($IdComprobante))
			{
				$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
				$oComprobante->Fecha = $oFacturaVaria->Fecha;
				$oComprobante->IdCliente = $oFacturaVaria->IdCliente;
				
				$oComprobantes->Update($oComprobante);
			}
		}

		header("Location: facturavarias_add_paso2.php" . $strParams . '&IdFactura=' . $oFacturaVaria->IdFactura);
		exit();
	}
}
else
{
	/* determinamos como fecha a la fecha de ayer */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

var IdTipoComprobante = '';
var arrParams = new Array();

function GetNextFactura(IdTipoComprobante)
{
	var arr = new Array();
	var obj;
	var oComprobante;

	if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
		return;
				
	arr['IdTipoComprobante'] = IdTipoComprobante;
	obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oComprobante = obj.Response;

	return oComprobante;	
}

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente;
	if (!(oCliente = GetCliente(IdCliente)))
		return;

	var oTipoiva;
	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;

	if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaA?>')
	{		
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaA?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

		/* obtenemos la proxima factura */
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaA?>');
		
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/
	}
	else if (oTipoIva.FacturaTipo == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::FacturaB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;

		/* obtenemos la proxima factura */
		/*oComprobante = GetNextFactura('<?=ComprobanteTipos::FacturaB?>');
		
		Get('IdComprobante').value = oComprobante.IdComprobante;
		Get('NumeroComprobante').value = oComprobante.Numero;*/
	}
}

function VerificarCliente()
{
	var IdCliente = Get('IdCliente').value;

	HideSection('trModificarCliente');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarCliente');
	}
}

function ModCliente()
{
	var IdCliente = Get('IdCliente').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	Get('IdComprobante').value 		= IdComprobante;
	Get('NumeroComprobante').value 	= NumeroComprobante;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Agregar</span></td>
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
					<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?=$oComprobante->IdComprobante?>" />
                    
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
                                                    <td><div align="right">Cliente:</div></td>
                                                    <td>
                                                        <table>
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
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr id="trModificarCliente" style="display:none;">
                                                	<td>&nbsp;</td>
                                                    <td height="20" colspan="2"><a href="#" class="linkMenu" onclick="javascript:ModCliente();">Modificar datos del Cliente</a></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Nro. Factura:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioSimple" maxlength="8" value="<?=$oComprobante->Numero?>" />
															<script language="javascript">
                                                            arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
                                                            SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
                                                            </script>
                                                            <span style="color:#FF0000;">&nbsp;(*)</span>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nro. de factura</li><?php } ?>
													<?php if ($err & 8) { ?><li style="color:#FF0000;">La factura seleccionada ya fue utilizada.</li><?php } ?>
													<?php if ($err & 16) { ?><li style="color:#FF0000;">Por favor, vuelva a seleccionar la factura.</li><?php } ?>
													<?php if ($err & 32) { ?><li style="color:#FF0000;">La factura seleccionada no existe.</li><?php } ?>
													</td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                            <script language="javascript">
                                                            new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                            </script>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                	<td>&nbsp;</td>
                                                	<td colspan="2"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese fecha</li><?php } ?></td>
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
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Siguiente" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturavarias.php<?=$strParams?>';" value="Cancelar" />
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
FilterCliente('<?=$IdCliente?>', '');
</script>

</body>
</html>