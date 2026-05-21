<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST) && $currentUser->IdUsuario != 25 && $currentUser->IdUsuario !=29)
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);
$PageSize 			= intval($_REQUEST['PageSize']);
$IdCajaDetalle		= intval($_REQUEST['IdCajaDetalle']);
$Importe			= intval($_REQUEST['Importe']);
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
$IdTipoPago = TipoPago::Cheque;
$filter['IdTipoPago'] 	= TipoPago::Cheque;

/* declaracion de variables */
$arrData 				= array();
$oPagos			 		= new Pagos();
$oCajasDetalles			= new CajasDetalles();
$oCajaMovimiento 		= new CajaMovimiento();
$oCajasMovimientos		= new CajasMovimientos();
$oCajasMovimientosPagos	= new CajasMovimientosPagos();

if (!$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle))
{
	header("Location: cajas_detalle_cheque.php" . $strParams);
	exit();
}

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
		
		$oCajaMovimiento->IdTipoMovimiento		= TiposMovimientosCaja::Egreso;
		$Importe = $Importe * -1;
		$oCajaMovimiento->Total 				= $Importe;
		$oCajaMovimiento->Comentarios 			= $Observaciones;
		$oCajaMovimiento->Fecha					= date('Y-m-d H:i:s');
		$oCajaMovimiento->IdCajaDetalle 		= $IdCajaDetalle;
		$oCajaMovimiento = $oCajasMovimientos->Create($oCajaMovimiento);
		
		$oCajasDetalles = new CajasDetalles();
		$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
		$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
		$oCajaDetalle->Total += $oCajaMovimiento->Total;
		$oCajasDetalles->Update($oCajaDetalle);
				
		$oCajas = new Cajas();
		$oCaja = $oCajas->GetById(1);
		$oCaja->TotalRendir += $oCajaMovimiento->Total;
		$oCaja->TotalDetalles += $oCajaMovimiento->Total;
		$oCajas->Update($oCaja);
		
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

		header("Location: cajas_detalle_cheque.php?IdCajaDetalle=" . $IdCajaDetalle);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de <?= $oCajaDetalle->Nombre ?> - Realizar Egreso</span></td>
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
                        <td width="100" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Banco</strong></div></td>
                        <td width="80" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Cheque</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha Emisi&oacute;n</strong></div></td>
                        <td width="150" height="25" class="bordeGrisTitulo"><div id="margen" align="left"><strong>Fecha Deposito</strong></div></td>
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
				?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen" align="left"><?=$oPago->BancoDesde?></div></td>
                        <td height="25"><div id="margen" align="center"><?=$oPago->NumeroCheque?></div></td>
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->FechaEmision) ?></div></td>
                        <td height="25"><div id="margen" align="left"><?=CambiarFecha($oPago->FechaDeposito) ?></div></td>
                        <td height="25"><div id="margen" align="cemter">$<?= number_format($oPago->Importe, 2, ',', '.') ?></div></td>
                        <td height="25">
							<div id="margen" align="center">
								<input type="checkbox" Importe="<?= $oPago->Importe ?>" id="Pago[]" name="Pago[]" value="<?= $oPago->IdPago ?>" onclick="ActualizarImporte();" />
							</div>
						</td>
                     </tr>
                    <tr>
                        <td colspan="6">
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cajas_detalle_cheque.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
</form>

</body>
</html>