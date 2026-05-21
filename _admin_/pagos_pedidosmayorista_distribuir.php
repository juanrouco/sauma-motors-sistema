<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PAGO_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPagoMayorista		= intval($_REQUEST['IdPagoMayorista']);
$Action					= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$errTotal				= false;
$err					= array();
$oUnidades				= new Unidades();
$oMinutas				= new Minutas();
$oPagosMayorista		= new PagosMayorista();
$oPagos					= new Pagos();
$oModelos				= new Modelos();
$oPedidosMayorista		= new PedidosMayorista();
$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oPagoMayorista = $oPagosMayorista->GetById($IdPagoMayorista))
{
	header('Location: pagos_pedidosmayorista.php' . $strParams);
	exit;
}

$oPedidoMayorista = $oPedidosMayorista->GetById($oPagoMayorista->IdPedidoMayorista);

$arrPedidosMayoristaDetalles = $oPedidoMayorista->GetAllDetalles();

/* si el formulario fue enviado... */
if ($Submit)
{
	$PagoTotalCalculado = 0;
	
	foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
	{
		$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
		$PagoAsignado = floatval($_REQUEST['PagoAsignado_' . $oMinuta->IdMinuta]);
		$PagoTotalCalculado += $PagoAsignado;
	}
	
	$oPagoMayorista->ImporteAsignado = $PagoTotalCalculado;
	if ($PagoTotalCalculado > $oPagoMayorista->Importe && abs($PagoTotalCalculado - $oPagoMayorista->Importe) > 0.001)
		$errTotal = true;
	
	if (!$errTotal)
	{
		foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
		{
			$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
			$oPago = $oPagos->GetByIdPagoMayoristaAndIdMinuta($IdPagoMayorista, $oMinuta->IdMinuta);
							
			$PagoAsignado = floatval($_REQUEST['PagoAsignado_' . $oMinuta->IdMinuta]);
			
			if ($PagoAsignado > $oMinuta->GetTotalPendiente() && abs($PagoAsignado - $oMinuta->GetTotalPendiente()) > 0.001)
				$err[$oMinuta->IdMinuta] = 1;
			else
			{
				if ($PagoAsignado == 0 && $oPago)
					$oPagos->Delete($oPago->IdPago);
				elseif ($PagoAsignado > 0)
				{
					$create = false;
					if (!$oPago)
					{
						$oPago = new Pago();
						$create = true;
					}
					
					$oPago->IdMinuta = $oMinuta->IdMinuta;
					$oPago->Fecha = $oPagoMayorista->Fecha;
					$oPago->NumeroCheque = $oPagoMayorista->NumeroCheque;
					$oPago->BancoDesde = $oPagoMayorista->BancoDesde;
					$oPago->BancoDestino = $oPagoMayorista->BancoDestino;
					$oPago->Cliente = $oPagoMayorista->Cliente;
					$oPago->FechaEmision = $oPagoMayorista->FechaEmision;
					$oPago->FechaDeposito = $oPagoMayorista->FechaDeposito;
					$oPago->Importe = $PagoAsignado;
					$oPago->IdTipoPago = $oPagoMayorista->IdTipoPago;
					$oPago->Observaciones = $oPagoMayorista->Observaciones;
					$oPago->IdPagoMayorista = $oPagoMayorista->IdPagoMayorista;
					
					if ($create)
						$oPagos->Create($oPago);
					else
						$oPagos->Update($oPago);
				}
					
				
				
			}
		}
		if (!$errTotal && count($err) == 0)
		{		
			$oPagosMayorista->Update($oPagoMayorista);
			header('Location: pagos_pedidosmayorista.php' . $strParams);
			exit;
		}
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

function Next()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Next';	
	frmData.submit();
	
	return true;
}


$j(document).ready(function() {
	$j('.pago-parcial').keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
			 (event.keyCode == 190 || event.keyCode == 188 || event.keyCode == 110) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
	$j(".pago-parcial").on('input',function() {
		var Id = $j(this).attr("id-element");
		var Total = 0;
		$j('.pago-parcial').each(function() {
				Total += parseFloat($j(this).val().replace(',', '.'));
		});
		$j('#TotalAsignado').html(Total.toFixed(2));
		$j('#TotalAsignable').html(<?= $oPagoMayorista->Importe ?> - Total.toFixed(2));
	});
});

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdPagoMayorista" id="IdPagoMayorista" value="<?=$IdPagoMayorista?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Pagos Mayoristas - Distribuir</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
		<tr>
            <td>
				<table width="80%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td height="30" align="right"><strong>Importe del Pago:&nbsp;</strong></td>
						<td align="left">&nbsp;$ <?= number_format($oPagoMayorista->Importe, 2) ?></td>
					</tr>
					<tr>
						<td height="30" align="right"><strong>Importe Asignado:&nbsp;</strong></td>
						<td align="left">&nbsp;$ <label id="TotalAsignado"><?= number_format($oPagoMayorista->ImporteAsignado, 2) ?></label></td>
					</tr>
					<tr>
						<td height="30" align="right"><strong>Importe Libre:&nbsp;</strong></td>
						<td align="left">&nbsp;$ <label id="TotalAsignable"><?= number_format($oPagoMayorista->Importe - $oPagoMayorista->ImporteAsignado, 2) ?></label></td>
					</tr>
					<?php
					if ($errTotal)
					{
					?>
					<tr>
						<td colspan="2" height="30" align="center"><li style="color: red">Es posible asignar hasta $<?=number_format($oPagoMayorista->Importe, 2) ?>.</li></td>
					</tr>
					<?php
					}
					?>
				</table>
			</td>
        </tr>
		<tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Acreditado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Saldo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Asignar</strong></div></td>
                    </tr>
					<?php
					foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
					{
						$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
						$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oPago = $oPagos->GetByIdPagoMayoristaAndIdMinuta($IdPagoMayorista, $oMinuta->IdMinuta);
						$Pago = 0;
						if (isset($_REQUEST['PagoAsignado_' . $oMinuta->IdMinuta]))
							$Pago = $_REQUEST['PagoAsignado_' . $oMinuta->IdMinuta];
						elseif ($oPago)
							$Pago = $oPago->Importe;
					?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="125" height="25"><div id="margen"  align="center"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$ <?=number_format($oMinuta->GetCostoTotal(), 2, ',', '.')?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$ <?=number_format($oMinuta->GetTotalAcreditado(), 2, ',', '.')?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$ <?=number_format($oMinuta->GetTotalPendiente(), 2, ',', '.')?></div></td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChico pago-parcial" id-element="<?= $oMinuta->IdMinuta ?>" id="PagoAsignado_<?= $oMinuta->IdMinuta ?>" name="PagoAsignado_<?= $oMinuta->IdMinuta ?>" value="<?= number_format($Pago, 2, '.', '') ?>" />
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
				<?php
					if ($err[$oMinuta->IdMinuta] == 1)
					{
				?>
					<tr>
                        <td colspan="6"><li style="color:#FF0000;">Se debe asignar un monto menos o igual al total adeudado para la unidad.</li>
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
				  <?php
				  }
				  }
				  ?>
                
                </table>		
           	</td>
      	</tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <tr>
        	<td>
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="30">
                            <div align="center">
                            	<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'pagos_pedidosmayorista.php<?= $strParams ?>'" />
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Aceptar" onClick="javascript: Next();" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>