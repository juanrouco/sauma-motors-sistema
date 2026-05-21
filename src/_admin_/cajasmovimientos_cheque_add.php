<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST) && $currentUser->IdUsuario != 25 && $currentUser->IdUsuario !=29)
	Session::NoPerm();

/* obtiene datos del formulario */
$IdCajaDetalle		= intval($_REQUEST['IdCajaDetalle']);
$IdTipoMovimiento	= intval($_REQUEST['IdTipoMovimiento']);
$Importe			= strval($_REQUEST['Importe']);
$Cliente			= strval($_REQUEST['Cliente']);
$BancoDesde			= strval($_REQUEST['BancoDesde']);
$NumeroCheque		= strval($_REQUEST['NumeroCheque']);
$FechaEmision		= strval($_REQUEST['FechaEmision']);
$FechaDeposito		= strval($_REQUEST['FechaDeposito']);
$Observaciones		= strval($_REQUEST['Observaciones']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$oCajaMovimiento 	= new CajaMovimiento();
$oCajasMovimientos	= new CajasMovimientos();
$oCajasDetalles		= new CajasDetalles();
$oPago			 	= new Pago();
$oPagos				= new Pagos();

if (!$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle))
{
	header("Location: cajas_detalle_cheque.php" . $strParams);
	exit();
}


/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* si el formulario fue enviado */
if ($Submit)
{
	$Importe = floatval(str_replace(',', '.', $Importe));
	/* validaciones... */
	if (!$Importe)
		$err |= 2;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oPago->IdTipoPago			= TipoPago::Cheque;
		$oPago->Fecha	 			= date('d-m-Y');
		$oPago->Importe			 	= $Importe;
		$oPago->BancoDesde			= $BancoDesde;
		$oPago->Cliente				= $Cliente;
		$oPago->NumeroCheque		= $NumeroCheque;
		$oPago->FechaEmision		= $FechaEmision;
		$oPago->FechaDeposito		= $FechaDeposito;
		$oPago->Observaciones		= $Observaciones;
		$oPago->NumeroRecibo		= 'SN';
		
		$oPago = $oPagos->Create($oPago);

		header("Location: cajas_detalle_cheque.php" . $strParams);
		exit();
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>


<script language="javascript">

function ValidarCajaDestino(value)
{
	if (value == '<?= TiposMovimientosCaja::TransferenciaCaja ?>')
		ShowSection('tr_CajaDestino');
	else
		HideSection('tr_CajaDestino');
}

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de <?= $oCajaDetalle->Nombre ?> - Realizar Ingreso</span></td>
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
					<input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?=$IdCajaDetalle?>" />
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
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese un importe mayor a 0.</li><?php } ?></td>
									</tr>
									<tr id="trBancoCliente">
										<td><div align="right">Cliente:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple" maxlength="128" value="<?=$Cliente?>" />
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trBancoCliente2">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trBancoDesde">
										<td><div align="right">Banco Origen:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="BancoDesde" id="BancoDesde" class="camporFormularioSimple" maxlength="128" value="<?=$BancoDesde?>" />
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trBancoDesde2">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trNumeroCheque">
										<td><div align="right">N&uacute;mero de Cheque:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="NumeroCheque" id="NumeroCheque" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroCheque?>" />
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trNumeroCheque2">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trFechaEmision">
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
															</script>
															</div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trFechaEmision2">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trFechaDeposito">
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
															</script>
															</div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trFechaDeposito2">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trBancoDestino" style="display: none">
										<td><div align="right">Banco Destino:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <select name="BancoDestino" id="BancoDestino" class="camporFormularioSimple">
																<option value="">Seleccione el Banco</option>
																<option value="Banco Galicia" <?= $BancoDestino == 'Banco Galicia' ? 'selected="selected"' : '' ?>>Banco Galicia</option>
																<option value="Banco Patagonia" <?= $BancoDestino == 'Banco Patagonia' ? 'selected="selected"' : '' ?>>Banco Patagonia</option>
																<option value="Bancos Honda" <?= $BancoDestino == 'Bancos Honda' ? 'selected="selected"' : '' ?>>Bancos Honda</option>
															  </select>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trBancoDestino2" style="display: none">
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cajas_detalle_cheque.php<?=$strParams?>';" value="Cancelar" />
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