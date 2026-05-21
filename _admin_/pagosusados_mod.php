
<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes_contactos autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPago					= intval($_REQUEST['IdPago']);
$IdMinuta				= intval($_REQUEST['IdMinuta']);
$IdTipoPago				= intval($_REQUEST['IdTipoPago']);
$IdAcreedor				= intval($_REQUEST['IdAcreedor']);
$Cuotas					= intval($_REQUEST['Cuotas']);
$Fecha					= strval($_REQUEST['Fecha']);
$Importe				= strval($_REQUEST['Importe']);
$BancoDestino			= strval($_REQUEST['BancoDestino']);
$BancoDesde				= strval($_REQUEST['BancoDesde']);
$Cliente				= strval($_REQUEST['Cliente']);
$NumeroCheque			= strval($_REQUEST['NumeroCheque']);
$FechaEmision			= strval($_REQUEST['FechaEmision']);
$FechaDeposito			= strval($_REQUEST['FechaDeposito']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$NumeroRecibo			= strval($_REQUEST['NumeroRecibo']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oPago			 	= new Pago();
$oPagos				= new Pagos();
$oAcreedores		= new Acreedores();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oPago = $oPagos->GetById($IdPago))
{
	header("Location: pagosusados.php" . $strParams);
	exit;
}

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
	if ($IdTipoPago == TipoPago::DepositoEfectivo || $IdTipoPago == TipoPago::DepositoCheque || $IdTipoPago == TipoPago::Transferencia || $IdTipoPago == TipoPago::DepositoCheque)
	{
		if ($BancoDestino == '')
			$err |= 8;
	}
	/*if ($NumeroRecibo == '' && $IdTipoPago == TipoPago::CreditoPersonal)
		$err |= 16;*/
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oPago->IdMinuta 			= $IdMinuta;
		$oPago->IdTipoPago			= $IdTipoPago;
		$oPago->Fecha	 			= $Fecha;
		$oPago->Importe			 	= $Importe;
		$oPago->BancoDestino		= $BancoDestino;
		$oPago->BancoDesde			= $BancoDesde;
		$oPago->Cliente				= $Cliente;
		$oPago->NumeroCheque		= $NumeroCheque;
		$oPago->FechaEmision		= $FechaEmision;
		$oPago->FechaDeposito		= $FechaDeposito;
		$oPago->Observaciones		= $Observaciones;
		$oPago->NumeroRecibo		= $NumeroRecibo;
		$oPago->IdAcreedor			= $IdAcreedor;
		$oPago->Cuotas				= $Cuotas;
		
		$oPago = $oPagos->Update($oPago);

		header("Location: pagosusados.php" . $strParams);
		exit();
	}
}
else
{
	$IdMinuta 			= $oPago->IdMinuta;
	$IdTipoPago			= $oPago->IdTipoPago;
	$Fecha	 			= CambiarFecha($oPago->Fecha);
	$Importe			= $oPago->Importe;
	$BancoDestino		= $oPago->BancoDestino;
	$BancoDesde			= $oPago->BancoDesde;
	$Cliente			= $oPago->Cliente;
	$NumeroCheque		= $oPago->NumeroCheque;
	$Observaciones		= $oPago->Observaciones;
	$FechaEmision		= CambiarFecha($oPago->FechaEmision);
	$FechaDeposito		= CambiarFecha($oPago->FechaDeposito);
	$NumeroRecibo		= $oPago->NumeroRecibo;
	$IdAcreedor			= $oPago->IdAcreedor;
	$Cuotas				= $oPago->Cuotas;
}

$arrAcreedores = $oAcreedores->GetAll();
/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">
$j(document).ready(function() {
	$j('#calcular-cuotas').click(function(e) {
		e.preventDefault();
		var IdAcreedor = $j('#IdAcreedor').val();
		if (!IdAcreedor)
		{
			alert('Seleccione el acreedor');
			return;
		}
		var FinanciacionCapital = parseFloat($j('#Importe').val());
		if (!FinanciacionCapital)
		{
			alert('Ingrese el importe');
			return;
		}
		var PlazoPrenda = parseInt($j('#Cuotas').val());
		if (!PlazoPrenda)
		{
			alert('Ingrese las cuotas');
			return;
		}
		$j.ajax('ssi_cuotas.php?IdAcreedor=' + IdAcreedor + '&FinanciacionCapital=' + FinanciacionCapital + '&Cuotas=' + PlazoPrenda, {
			success: function (data, textStatus, jqXHR) {
				$j('#cuotas-container').html(data);
			}
		});
	});
	$j('#IdTipoPago').change(function() {
		ValidarTipoPago($j('#IdTipoPago').val());
	});
	
	ValidarTipoPago('<?= $IdTipoPago ?>');
});

function ValidarTipoPago(IdTipoPago)
{
		if (IdTipoPago == '<?= TipoPago::Redondeo ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::Efectivo ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::DepositoEfectivo ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::DepositoCheque ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::Transferencia ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::Cheque ?>') {
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
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::Credito ?>') {
			$j('#trBancoCliente').show();
			$j('#trBancoCliente2').show();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
			$j('#trBancoAcreedor').show();
			$j('#trBancoAcreedor2').show();
			$j('#trCuotas').show();
			$j('#trCuotas2').show();
		}
		else if (IdTipoPago == '<?= TipoPago::Debito ?>') {
			$j('#trBancoCliente').show();
			$j('#trBancoCliente2').show();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
			$j('#trBancoAcreedor').hide();
			$j('#trBancoAcreedor2').hide();
			$j('#trCuotas').hide();
			$j('#trCuotas2').hide();
		}
		else if (IdTipoPago == '<?= TipoPago::CreditoPersonal ?>') {
			$j('#trBancoCliente').show();
			$j('#trBancoCliente2').show();
			$j('#trBancoDesde').hide();
			$j('#trBancoDesde2').hide();
			$j('#trBancoDestino').hide();
			$j('#trBancoDestino2').hide();
			$j('#trNumeroCheque').hide();
			$j('#trNumeroCheque2').hide();
			$j('#trFechaEmision').hide();
			$j('#trFechaEmision2').hide();
			$j('#trFechaDeposito').hide();
			$j('#trFechaDeposito2').hide();
			$j('#trBancoAcreedor').show();
			$j('#trBancoAcreedor2').show();
			$j('#trCuotas').show();
			$j('#trCuotas2').show();
			$j('#trCuotas2').show();
		}
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Pagos - Modificar</span></td>
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
					<input type="hidden" name="IdPago" id="IdPago" value="<?=$IdPago?>" />
					<input type="hidden" name="IdMinuta" id="IdMinuta" value="<?=$IdMinuta?>" />
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
														$selected = '';
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
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaNacimiento'});
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
										<td><div align="right">Nro. Recibo:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
                                                              <input type="text" name="NumeroRecibo" id="NumeroRecibo" class="camporFormularioChico" maxlength="128" value="<?=$NumeroRecibo?>" />
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese un nro de recibo.</li><?php } ?></td>
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
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese un importe mayor a 0.</li><?php } ?></td>
									</tr>
									<tr id="trCuotas" style="display: none">
										<td><div align="right">Cuotas:</div></td>
										<td>
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
																  <input type="text" name="Cuotas" id="Cuotas" class="camporFormularioChico" maxlength="128" value="<?=$Cuotas?>" />
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trCuotas2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr id="trBancoAcreedor" style="display: none">
										<td valign="top"><div align="right" style="margin-top: 7px">Acreedor:</div></td>
										<td valign="top">
                                        	<div align="left">
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <div align="left">
																<select name="IdAcreedor" id="IdAcreedor" class="camporFormularioSimple">
																	<option value="">Seleccione el acreedor</option>
																<?php
																foreach ($arrAcreedores as $oAcreedor)
																{
																	$selected = '';
																	if ($oAcreedor->IdAcreedor == $IdAcreedor)
																		$selected = 'selected="selected"';
																?>
																	<option value="<?= $oAcreedor->IdAcreedor ?>" <?= $selected ?>><?= $oAcreedor->RazonSocial ?></option>
																<?php
																}
																?>
																</select>
                                                            </div>
                                                        </td>
                                                        <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                    </tr>
													<tr>
														<td colspan="2" align="left"><a id="calcular-cuotas" href="#">[Calcular cuotas] </a></td>
													</tr>
                                                    <tr>
														<td colspan="2" id="cuotas-container"></td>
													</tr>                    
                                                </table>
                                          	</div>
                                       	</td>
									</tr>
                                	<tr id="trBancoAcreedor2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
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
									<tr  id="trBancoDestino" style="display: none">
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
                                	<tr  id="trBancoDestino2" style="display: none">
										<td height="20">&nbsp;</td>
										<td height="20"><?php if ($err & 8) { ?><li style="color:#FF0000;">Seleccione el banco destino.</li><?php } ?></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pagosusados.php<?=$strParams?>';" value="Cancelar" />
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