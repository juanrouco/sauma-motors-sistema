<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPedidoMayorista		= intval($_REQUEST['IdPedidoMayorista']);
$IdTipoPago				= intval($_REQUEST['IdTipoPago']);
$Fecha					= strval($_REQUEST['Fecha']);
$Importe				= strval($_REQUEST['Importe']);
$BancoDestino			= strval($_REQUEST['BancoDestino']);
$BancoDesde				= strval($_REQUEST['BancoDesde']);
$Cliente				= strval($_REQUEST['Cliente']);
$NumeroCheque			= strval($_REQUEST['NumeroCheque']);
$FechaEmision			= strval($_REQUEST['FechaEmision']);
$FechaDeposito			= strval($_REQUEST['FechaDeposito']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oPagoMayorista			 	= new PagoMayorista();
$oPagosMayorista	= new PagosMayorista();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	$Importe = str_replace(',', '.', $Importe);
	/* validaciones... */
	if ($IdTipoPago == '')
		$err |= 1;
	if ($Fecha == '')
		$err |= 2;
	if (($Importe == '') || (floatval($Importe) == 0))
		$err |= 4;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oPagoMayorista->IdPedidoMayorista 			= $IdPedidoMayorista;
		$oPagoMayorista->IdTipoPago			= $IdTipoPago;
		$oPagoMayorista->Fecha	 			= $Fecha;
		$oPagoMayorista->Importe			 	= $Importe;
		$oPagoMayorista->BancoDestino		= $BancoDestino;
		$oPagoMayorista->BancoDesde			= $BancoDesde;
		$oPagoMayorista->Cliente				= $Cliente;
		$oPagoMayorista->NumeroCheque		= $NumeroCheque;
		$oPagoMayorista->FechaEmision		= $FechaEmision;
		$oPagoMayorista->FechaDeposito		= $FechaDeposito;
		
		$oPagoMayorista = $oPagosMayorista->Create($oPagoMayorista);

		header("Location: pagos_pedidosmayorista.php" . $strParams);
		exit();
	}
}
else
{
	$Fecha = date('d-m-Y');
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">
$j(document).ready(function() {
	$j('#IdTipoPago').change(function() {
		if ($j('#IdTipoPago').val() == '<?= TipoPago::Redondeo ?>') {
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trBancoCliente').hide();
			$j('#trBancoCliente2').hide();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
		}
		else if ($j('#IdTipoPago').val() == '<?= TipoPago::Efectivo ?>') {
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trBancoCliente').hide();
			$j('#trBancoCliente2').hide();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
		}
		else if ($j('#IdTipoPago').val() == '<?= TipoPago::DepositoEfectivo ?>') {
			$j('#trBancoDestino').show();
			$j('#trBancoDestino2').show();
			$j('#trBancoCliente').hide();
			$j('#trBancoCliente2').hide();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
		}
		else if ($j('#IdTipoPago').val() == '<?= TipoPago::DepositoCheque ?>') {
			$j('#trBancoDestino').show();
			$j('#trBancoDestino2').show();
			$j('#trBancoCliente').hide();
			$j('#trBancoCliente2').hide();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trNumeroCheque').show();
			$j('#trNumeroCheque2').show();
			$j('#trFechaEmision').show();
			$j('#trFechaEmision2').show();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
		}
		else if ($j('#IdTipoPago').val() == '<?= TipoPago::Transferencia ?>') {
			$j('#trBancoCliente').show();
			$j('#trBancoCliente2').show();
			$j('#trBancoDesde').show();
			$j('#trBancoDesde2').show();
			$j('#trBancoDestino').show();
			$j('#trBancoDestino2').show();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
		}
		else if ($j('#IdTipoPago').val() == '<?= TipoPago::Cheque ?>') {
			$j('#trBancoCliente').show();
			$j('#trBancoCliente2').show();
			$j('#trBancoDesde').show();
			$j('#trBancoDesde2').show();
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trNumeroCheque').show();
			$j('#trNumeroCheque2').show();
			$j('#trFechaEmision').show();
			$j('#trFechaEmision2').show();
			$j('#trFechaDeposito').show();
			$j('#trFechaDeposito2').show();
		}
	});
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Pagos - Agregar</span></td>
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
					<input type="hidden" name="IdPedidoMayorista" id="IdPedidoMayorista" value="<?=$IdPedidoMayorista?>" />
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
										<td><div align="right">Tipo de Pago:</div></td>
										<td>
                                        	<div align="left">
                                                <select name="IdTipoPago" id="IdTipoPago" class="camporFormularioSimple">
													<option value="">Seleccione un tipo de pago</option>
													<?php
													foreach (TipoPago::GetAll() as $oTipoPago)
													{
														$selected == '';
														if ($oTipoPago['IdTipoPago'] == $IdTipoPago)
															$selected = "selected='selected'";
													?>
													<option value="<?= $oTipoPago['IdTipoPago'] ?>" <?= $selected ?>><?= $oTipoPago['Descripcion'] ?></option>
													<?php
													}
													?>
												</select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione el tipo de pago</li><?php } ?></td>
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
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione una fecha</li><?php } ?></td>
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
									<tr id="trBancoCliente" style="display: none">
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
                                	<tr id="trBancoCliente2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trBancoDesde" style="display: none">
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
                                	<tr id="trBancoDesde2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trNumeroCheque" style="display: none">
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
                                	<tr id="trNumeroCheque2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trFechaEmision" style="display: none">
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
                                	<tr id="trFechaEmision2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trFechaDeposito" style="display: none">
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
                                	<tr id="trFechaDeposito2" style="display: none">
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
                                                              <input type="text" name="BancoDestino" id="BancoDestino" class="camporFormularioSimple" maxlength="128" value="<?=$BancoDestino?>" />
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pagos_pedidosmayorista.php<?=$strParams?>';" value="Cancelar" />
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