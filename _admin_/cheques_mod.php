<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CHEQUE_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCheque				= intval($_REQUEST['IdCheque']);
$Fecha					= strval($_REQUEST['Fecha']);
$Importe				= strval($_REQUEST['Importe']);
$Banco					= intval($_REQUEST['Banco']);
$NumeroCheque			= strval($_REQUEST['NumeroCheque']);
$FechaEmision			= strval($_REQUEST['FechaEmision']);
$FechaDeposito			= strval($_REQUEST['FechaDeposito']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$IdProveedor			= intval($_REQUEST['IdProveedor']);
$Proveedor				= strval($_REQUEST['Proveedor']);
$IdFacturaCompra		= intval($_REQUEST['IdFacturaCompra']);
$NumeroFactura			= strval($_REQUEST['NumeroFactura']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oCheques			= new Cheques();
$oCajasDetalles		= new CajasDetalles();
$arrCajasDeposito = $oCajasDetalles->GetAll(array('RecibeDeposito' => 1));

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oCheque = $oCheques->GetById($IdCheque))
{
	header('Location: cheques.php' . $strParams);
	exit;
}

if ($Submit)
{
	$Importe = str_replace(',', '.', $Importe);
	/* validaciones... */
	if ($Fecha == '')
		$err |= 1;
	if ($Banco == '')
		$err |= 2;
	if (($Importe == '') || (floatval($Importe) == 0))
		$err |= 4;
	if ($NumeroCheque == '')
		$err |= 8;
	if ($FechaEmision == '')
		$err |= 16;
	if ($FechaDeposito == '')
		$err |= 32;
	if ($IdProveedor == '')
		$err |= 64;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oCajaDestino	= $oCajasDetalles->GetById($Banco);
		$oCheque->Fecha	 			= $Fecha;
		$oCheque->Importe			= $Importe;
		$oCheque->Banco				= $oCajaDestino->Nombre;
		$oCheque->NumeroCheque		= $NumeroCheque;
		$oCheque->FechaEmision		= $FechaEmision;
		$oCheque->FechaDeposito		= $FechaDeposito;
		$oCheque->Observaciones		= $Observaciones;
		$oCheque->NumeroFactura		= $NumeroFactura;
		$oCheque->IdProveedor		= $IdProveedor;
		$oCheque->IdFacturaCompra	= $IdFacturaCompra;
		
		$oCheques->Update($oCheque);

		header("Location: cheques.php" . $strParams);
		exit();
	}
}
else
{
	$Fecha	 			= CambiarFecha($oCheque->Fecha);
	$Importe			= $oCheque->Importe;
	$BancoOrigen		= $oCheque->Banco;
	$NumeroCheque		= $oCheque->NumeroCheque;
	$FechaEmision		= CambiarFecha($oCheque->FechaEmision);
	$FechaDeposito		= CambiarFecha($oCheque->FechaDeposito);
	$Observaciones		= $oCheque->Observaciones;
	$NumeroFactura		= $oCheque->NumeroFactura;
	$IdProveedor		= $oCheque->IdProveedor;
	$IdFacturaCompra	= $oCheque->IdFacturaCompra;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">
var arrProveedor = new Array();
arrProveedor['FilterIdProveedor'] = 0;

function FilterProveedor(IdProveedor, Nombre)
{
	if ((IdProveedor == '') && (Nombre == ''))
	{		
		Get('Proveedor').value 			= '';
		Get('IdProveedor').value 		= '';
	}

	var oProveedor = GetProveedor(IdProveedor);
	if (!(oProveedor))
		return;
	
	Get('Proveedor').value 			= oProveedor.Empresa;
	Get('IdProveedor').value 		= oProveedor.IdProveedor;
	arrProveedor['FilterIdProveedor'] = oProveedor.IdProveedor;
}

function FilterFactura(IdFacturaCompra, Numero)
{
	if ((IdFacturaCompra == '') && (Numero == ''))
	{		
		Get('IdFacturaCompra').value 			= '';
		Get('NumeroFactura').value 		= '';
	}

	var oFactura = GetFacturaCompra(IdFacturaCompra);
	if (!(oFactura))
		return;
	
	Get('NumeroFactura').value 			= oFactura.Numero;
	Get('IdFacturaCompra').value 		= oFactura.IdFacturaCompra;
}
$j(document).ready(function() { 
	<?php
	if ($IdProveedor) {
	?>
		FilterProveedor(<?= $IdProveedor ?>, '');
	<?php
	}
	?>
});
</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cheques - Modificar</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>"  onsubmit="btnAceptar.disabled = true; return true;">
					<input type="hidden" name="IdCheque" id="IdCheque" value="<?=$IdCheque?>" />
					<input type="hidden" name="IdProveedor" id="IdProveedor" value="<?=$IdProveedor?>" />
					<input type="hidden" name="IdFacturaCompra" id="IdFacturaCompra" value="<?=$IdFacturaCompra?>" />
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
					  	<tr>
							<td class="bordeGris">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
									<tr>
										<td><div align="right">Fecha:</div></td>
										<td>
                                        	<div align="left">
                                                 <input name="Fecha" type="text" class="camporFormularioMediano" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                </script>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione una fecha</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Importe:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="Importe" id="Importe" class="camporFormularioChico" maxlength="128" value="<?=$Importe?>" />
                                                            <span style="color:#FF0000;">&nbsp;(*)</span></div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un importe mayor a 0.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Banco Origen:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
																<select name="Banco" id="Banco" class="camporFormularioSimple">
																	<option value="">Seleccione el Banco</option>
																	<?php foreach ($arrCajasDeposito as $oCajaDetalle) { ?> 
																		<option value="<?=$oCajaDetalle->IdCajaDetalle?>" <?= $BancoOrigen == $oCajaDetalle->Nombre || $Banco == $oCajaDetalle->IdCajaDetalle ? 'selected="selected"' : '' ?>><?=$oCajaDetalle->Nombre?></option>
																	<?php } ?>
																</select><span style="color:#FF0000;">&nbsp;(*)</span>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione un banco.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero de Cheque:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="NumeroCheque" id="NumeroCheque" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroCheque?>" />
																<span style="color:#FF0000;">&nbsp;(*)</span>
															</div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese el nro de cheque.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Fecha de Emisi&oacute;n:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="FechaEmision" id="FechaEmision" class="camporFormularioMediano" maxlength="128" value="<?=$FechaEmision?>" />
                                                            <script language="javascript">
															new tcal({'formname': 'frmData', 'controlname': 'FechaEmision'});
															</script><span style="color:#FF0000;">&nbsp;(*)</span>
															</div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese la fecha de emisi&oacute;n.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Fecha de Deposito:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="FechaDeposito" id="FechaDeposito" class="camporFormularioMediano" maxlength="128" value="<?=$FechaDeposito?>" />
                                                            <script language="javascript">
															new tcal({'formname': 'frmData', 'controlname': 'FechaDeposito'});
															</script><span style="color:#FF0000;">&nbsp;(*)</span>
															</div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la fecha de deposito.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">Proveedor:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
																<input type="text" name="Proveedor" id="Proveedor" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Proveedor?>" autocomplete="off">
																<span style="color:#FF0000;">&nbsp;(*)</span>
																<script language="">												
																	SUGGESTRequest('Proveedores', 'GetAll', 'Proveedor', 'FilterProveedor', 'IdProveedor', 'Empresa', 'Filter_Empresa', null);
																</script>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese el proveedor.</li><?php } ?></td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero Factura:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
																<input type="text" name="NumeroFactura" id="NumeroFactura" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$NumeroFactura?>" autocomplete="off">
																<script language="">												
																	SUGGESTRequest('FacturasCompras', 'GetAll', 'NumeroFactura', 'FilterFactura', 'IdFacturaCompra', 'Numero', 'FilterNumero', arrProveedor);
																</script>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Comentarios:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <textarea name="Observaciones" id="Observaciones" style="height: 45px" class="camporFormularioSimple"><?=$Observaciones?></textarea>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
								</table>
						  	</td>
						</tr>
						<tr>
							<td height="1"><div align="center"></div></td>
					  </tr>
					</table>
			  		<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cheques.php<?=$strParams?>';" value="Cancelar" />
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