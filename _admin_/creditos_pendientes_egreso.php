<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);
$PageSize 			= intval($_REQUEST['PageSize']);
$Importe			= intval($_REQUEST['Importe']);
$Observaciones		= strval($_REQUEST['Observaciones']);
$Cliente			= strval($_REQUEST['Cliente']);
$BancoDesde			= strval($_REQUEST['BancoDesde']);
$NumeroCheque		= strval($_REQUEST['NumeroCheque']);
$FechaEmision		= strval($_REQUEST['FechaEmision']);
$FechaDeposito		= strval($_REQUEST['FechaDeposito']);
$Observaciones		= strval($_REQUEST['Observaciones']);
$arrPagos			= $_REQUEST['Pago'];
$Submit				= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filterPago)) || (IsEmptyArray($filterPago)) || ($Submit))
{
	$filter = array();
	$filter['FechaDesde'] 	= trim($_REQUEST['FilterFechaDesde']);
	$filter['FechaHasta'] 	= trim($_REQUEST['FilterFechaHasta']);
	$filter['Interno'] 		= trim($_REQUEST['FilterInterno']);
	$filter['Pago'] 		= '0';
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";
$IdTipoPago = TipoPago::CreditoPersonal;
$filter['IdTipoPago'] 	= TipoPago::CreditoPersonal;

/* declaracion de variables */
$arrData 				= array();
$oPagos			 		= new Pagos();
$oCajasDetalles			= new CajasDetalles();
$oAcreedores	 		= new Acreedores();
$oCajaMovimiento 		= new CajaMovimiento();
$oCajasMovimientos		= new CajasMovimientos();
$oCajasMovimientosPagos	= new CajasMovimientosPagos();

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
		$str = '';
		
		$oPago = new Pago();
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
		
		$oCajaMovimiento = $oCajasMovimientos->GetByIdEntidad(TiposMovimientosCaja::Pago, $oPago->IdPago);
		
		foreach ($arrPagos as $IdPago)
		{
			$oPago = $oPagos->GetById($IdPago);
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->IdCajaMovimiento = $oCajaMovimiento->IdCajaMovimiento;
			$oCajaMovimientoPago->IdPago = $IdPago;
			$oCajasMovimientosPagos->Create($oCajaMovimientoPago);
			
			$oPago->Pago = 1;
			$oPagos->UpdatePago($oPago);
		}

		header("Location: creditos_pendientes.php");
		exit();
	}
}

/* definimos cadena a mandar por get */
$strParams = '?';
$strParams.= '&IdCajaDetalle=' 		. $IdCajaDetalle;
$strParams.= '&Page='				. $Page;
$strParams.= '&PageSize='			. $PageSize;


/* obtenemos el listado de datos a mostrar */
$arrData 	= $oPagos->GetAllOrdered($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">
function ActualizarImporte() {
		var Importe = 0;
		$j('input[type=checkbox]').each(function() {
			if (this.checked)
			{
				var ValorCheque = parseFloat($j(this).attr('Importe'));
				Importe+= ValorCheque;
			}
		});
		
		$j('#Importe').val(Importe.toFixed(2));
}
</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
	<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="PageContacto" id="PageContacto" value="<?=$PageContacto?>" />
	<input type="hidden" name="PageContactoSize" id="PageContactoSize" value="<?=$PageContactoSize?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?= $IdCajaDetalle ?>" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cr&eacute;ditos Pendientes - Realizar Liquidaci&oacute;n</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
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
                                                              <input type="text" readonly="readonly" name="Importe" id="Importe" class="camporFormularioChico" maxlength="128" value="<?=$Importe?>" />
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
					</table>
			</td>
        </tr>
          
    <?php if ($arrData != NULL) { ?>
            
        <tr>
            <td>
                <table width="70%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha</strong></div></td>
                        <td width="80" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Interno</strong></div></td>
                        <td width="80" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Contrato</strong></div></td>
                        <td width="200" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Cliente</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Acreedor</strong></div></td>
                        <td width="200" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Observaciones</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Importe</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					$Total = 0;
					foreach ($arrData as $oPago) 
					{
						$Interno = $oPago->IdMinuta;
						if (!$Interno)
							$Interno = 'U-' . $oPago->IdMinutaUsado;
						
						$Total+= $oPago->Importe;
						$oAcreedor = $oAcreedores->GetById($oPago->IdAcreedor);
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->Fecha)?></div></td>
                        <td height="25"><div id="margen" align="center"><?=$Interno?></div></td>
                        <td height="25"><div id="margen" align="center"><?=$oPago->NumeroRecibo ?></div></td>
                        <td height="25"><div id="margen" align="left"><?= $oPago->Cliente ?></div></td>
                        <td height="25"><div id="margen" align="left"><?= $oAcreedor->RazonSocial ?></div></td>
                        <td height="25"><div id="margen" align="left"><?= $oPago->Observaciones ?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= number_format($oPago->Importe, 2, ',', '.') ?></div></td>
                        <td height="25">
							<div id="margen" align="center">
								<input type="checkbox" Importe="<?= $oPago->Importe ?>" id="Pago[]" name="Pago[]" value="<?= $oPago->IdPago ?>" onclick="ActualizarImporte();" />
							</div>
						</td>
                     </tr>
                    <tr>
                        <td colspan="8">
                            <div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
          
                <?php } ?> 				
                    
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>No hay registros disponibles.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
          
    <?php } ?>
    
    </table>
			  		<table width="63%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'creditos_pendientes.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
</form>

</body>
</html>