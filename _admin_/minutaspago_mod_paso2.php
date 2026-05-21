<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MINP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinutaPago			= intval($_REQUEST['IdMinutaPago']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$Action					= strval($_REQUEST['MainAction']);
$TotalAPagar_v			= floatval($_REQUEST['TotalAPagar_v']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= array();
$errL					= 0;
$oUnidades				= new Unidades();
$oPadronesArba			= new PadronesArbaRetenciones();
$oModelos				= new Modelos();
$oMinutasPago			= new MinutasPago();
$oMinutasPagoItems		= new MinutasPagoItems();
$oFacturasCompras		= new FacturasCompras();

$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe el recepcion */
if (!$oMinutaPago = $oMinutasPago->GetById($IdMinutaPago))
{	
	header("Location: minutaspago.php" . $strParams);
	exit();
}

/* si el formulario fue enviado... */
if ($Submit)
{
	
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Select':
		
			/* obtenemos los datos del detalle a agregar */
			$arrIdUnidad 	= $_REQUEST['IdUnidad'];
			
			$Observaciones = $oMinutaPago->Observaciones;
			foreach ($arrIdUnidad as $IdUnidad)
			{
				$err = 0;
				/* validaciones... */
				if ($IdUnidad == '')
					$err |= 1;
				elseif (!($oUnidad = $oUnidades->GetById($IdUnidad)))
					$err |= 2;
				elseif (($oMinutaPagoItem = $oMinutasPagoItems->GetByIdMinutaPagoAndIdUnidad($IdMinutaPago, $IdUnidad)))
					$err |= 2;
			
				/* si no hay errores... */
				if ($err == 0)
				{
					$TotalPagado = $oMinutasPagoItems->GetPagadoByIdUnidad($IdUnidad);
					$oFacturaCompra = $oFacturasCompras->GetByIdUnidad($IdUnidad);
					$oProveedor = $oProveedores->GetById($oUnidad->IdProveedor);
					$oMinutaPagoItem = new MinutaPagoItem();
					$oMinutaPagoItem->IdMinutaPago	 = $IdMinutaPago;
					$oMinutaPagoItem->IdUnidad		 = $IdUnidad;
					$oMinutaPagoItem->Neto			 = $oUnidad->ImporteCompraNeto;
					$oMinutaPagoItem->Importe		 = $oUnidad->ImporteNotaCredito - $TotalPagado;
					$oMinutaPagoItem->PagoParcial	 = $TotalPagado ? 1 : 0;
					$oMinutaPagoItem->Saldo			 = 0;
					$oMinutaPagoItem->Retencion		 = 0;
					//$oMinutaPagoItem->IdFacturaCompra	= $oFacturaCompra->IdFacturaCompra;
					$oMinutaPagoItem->Fecha				= $oMinutaPago->Fecha;
					$oMinutaPagoItem->IdProveedor		= $oProveedor->IdProveedor;
					$oMinutaPagoItem->Cuit				= $oProveedor->Cuit;
					
					
					if ($oMinutaPagoItem = $oMinutasPagoItems->Create($oMinutaPagoItem))
					{
						//$oUnidad->IdMinutaPago 	= $IdMinutaPago;
						//$oUnidad->Cancelada 	= 1;
						
						//$oUnidad = $oUnidades->Update($oUnidad);

						$Operation = Operaciones::Create;
						$Status = (($oUnidad) ? true : false);
					}
					
				}
			}
		
			break;
			
			case 'Add':
		
			/* obtenemos los datos del detalle a agregar */
			$NumeroVin 		= strval($_REQUEST['NumeroVin']);
			$IdUnidad 		= intval($_REQUEST['IdUnidad']);
			$VehiculoModelo	= strval($_REQUEST['VehiculoModelo']);
			$NumeroFactura	= strval($_REQUEST['NumeroFactura']);
			$FechaFactura	= strval($_REQUEST['FechaFactura']);
			$Neto			= str_replace(',', '.', $_REQUEST['Neto']);
			$Importe		= str_replace(',', '.', $_REQUEST['Importe']);
			
			/* validaciones... */
			if ($IdUnidad == '')
				$err |= 1;
			elseif (($oUnidad = $oUnidades->GetById($IdUnidad)) && ($oUnidad->IdMinutaPago != ''))
				$err |= 2;
		
			/* si no hay errores... */
			if ($err == 0)
			{
				$oPadronArba = $oPadronesArba->GetByCUIL('30662071680', $oMinutaPago->Fecha);
				$oFacturaCompra = $oFacturasCompras->GetByIdUnidad($IdUnidad);
				$oProveedor = $oProveedores->GetById($oUnidad->IdProveedor);
				$oMinutaPagoItem = new MinutaPagoItem();
				$oMinutaPagoItem->IdMinutaPago	 = $IdMinutaPago;
				$oMinutaPagoItem->IdUnidad		 = $IdUnidad;
				$oMinutaPagoItem->Neto			 = $Neto;
				$oMinutaPagoItem->Importe		 = $Importe;
				$oMinutaPagoItem->Saldo			 = 0;
				if ($oPadronArba)
					$oMinutaPagoItem->Retencion		 = $Neto * $oPadronArba->Retencion / 100;
				else
					$oMinutaPagoItem->Retencion		 = 0;
				//$oMinutaPagoItem->IdFacturaCompra	= $oFacturaCompra->IdFacturaCompra;
				$oMinutaPagoItem->Fecha				= $oMinutaPago->Fecha;
				$oMinutaPagoItem->IdProveedor		= $oProveedor->IdProveedor;
				$oMinutaPagoItem->Cuit				= $oProveedor->Cuit;
				
				if ($oMinutaPagoItem = $oMinutasPagoItems->Create($oMinutaPagoItem))
				{
					//$oUnidad->IdMinutaPago 	= $IdMinutaPago;
					//$oUnidad->Cancelada 	= 1;
					
					//$oUnidad = $oUnidades->Update($oUnidad);

					$Operation = Operaciones::Create;
					$Status = (($oUnidad) ? true : false);
				}
				
				/* seteamos variables en null */
				$NumeroVin 		= '';
				$IdUnidad 		= '';
				$VehiculoModelo = '';
				$NumeroFactura	= '';
				$FechaFactura	= '';
				$Neto			= '';
				$Importe		= '';
			}
		
			break;

		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdUnidad = intval($_REQUEST['Id']);
			
			$oMinutaPagoItem = $oMinutasPagoItems->GetByIdMinutaPagoAndIdUnidad($IdMinutaPago, $IdUnidad);
			$oMinutasPagoItems->Delete($oMinutaPagoItem->IdMinutaPagoItem);

			/* si no hay errores... */
			if ($oUnidad = $oUnidades->GetById($IdUnidad))
			{
				$oUnidad->IdMinutaPago 	= '';
				$oUnidad->Cancelada		= 0;

				$oUnidad = $oUnidades->Update($oUnidad);

				$Operation = Operaciones::Delete;
				$Status = (($oUnidad) ? true : false);
			}

			break;

		case 'Next':
			if (abs($TotalAPagar_v - $oMinutaPago->MontoDisponible) > 0.001)
				$errL = 1;
				
			$arrData = $oMinutasPagoItems->GetAllByIdMinutaPago($IdMinutaPago);
			$oMinutaPago->Observaciones = $Observaciones;
	
	$oMinutasPago->Update($oMinutaPago);
			foreach ($arrData as $oMinutaPagoItem)
			{
				$oUnidad = $oUnidades->GetById($oMinutaPagoItem->IdUnidad);
				$PagoParcial = intval($_REQUEST['PagoParcial_' . $oMinutaPagoItem->IdUnidad]);
				if ($PagoParcial)
				{
					$Importe = $_REQUEST['Importe_' . $oMinutaPagoItem->IdUnidad];
					$Importe = str_replace(',', '.', $Importe);
					if (abs($Importe - $oUnidad->ImporteNotaCredito) > 0.001 && $Importe > $oUnidad->ImporteNotaCredito)
						$err[$oMinutaPagoItem->IdMinutaPagoItem] = 1;
						
					$oUnidad->Cancelada = 0;
					$TotalPagado = $oMinutasPagoItems->GetPagadoByIdUnidad($oMinutaPagoItem->IdUnidad);
					
					$TotalPagado-= $oMinutaPagoItem->Importe;
					$TotalPagado+= $Importe;
					if (count($err) == 0 && abs($TotalPagado - $oUnidad->ImporteNotaCredito) > 0.001 && $TotalPagado > $oUnidad->ImporteNotaCredito)
						$err[$oMinutaPagoItem->IdMinutaPagoItem] = 2;
					elseif (count($err) == 0 && abs($TotalPagado - $oUnidad->ImporteNotaCredito) < 0.001)
					{
						$oUnidad->Cancelada = 1;
						$oPadronArba = $oPadronesArba->GetByCUIL('30662071680', $oMinutaPago->Fecha);
					
						if ($oPadronArba)
							$oMinutaPagoItem->Retencion		 = $oUnidad->ImporteCompraNeto * $oPadronArba->Retencion / 100;
						else
							$oMinutaPagoItem->Retencion		 = 0;
					}
					
						$oMinutaPagoItem->Saldo = $oUnidad->ImporteNotaCredito - $TotalPagado;
					$oMinutaPagoItem->Importe = $Importe;
				}
				else
				{
					$oMinutaPagoItem->Importe = $oUnidad->ImporteNotaCredito;
					$oUnidad->Cancelada = 1;
					$oPadronArba = $oPadronesArba->GetByCUIL('30662071680', $oMinutaPago->Fecha);
					
					if ($oPadronArba)
						$oMinutaPagoItem->Retencion		 = $oUnidad->ImporteCompraNeto * $oPadronArba->Retencion / 100;
					else
						$oMinutaPagoItem->Retencion		 = 0;
				}
				
				$oMinutaPagoItem->PagoParcial = $PagoParcial;
				
				$oUnidades->Update($oUnidad);
				$oMinutasPagoItems->Update($oMinutaPagoItem);
			}
			
			if ($errL == 0 && count($err) == 0)
			{
				header('Location: minutaspago_mod_paso3.php?IdMinutaPago=' . $IdMinutaPago);
				exit;
			}
			break;

		default:
			break;
	}
}
else{
}

/* obtenemos todos las unidades de la recepcion */
$arrData = $oMinutasPagoItems->GetAllByIdMinutaPago($IdMinutaPago);

foreach ($arrData as $oMinutaPagoItem)
{
	$TotalAPagar+= $oMinutaPagoItem->Importe;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterIdUnidad(IdUnidad, NumeroVin)
{	
	if ((IdUnidad == '') && (NumeroVin == ''))
	{
		Get('NumeroVin').value 		= '';
		Get('IdUnidad').value 		= '';
		Get('VehiculoModelo').value = '';
	}

	var oUnidad = GetUnidad(IdUnidad);
	if (!(oUnidad))
		return;

	var oModelo = GetModelo(oUnidad.IdModelo);
	if (!(oModelo))
		return;

	Get('NumeroVin').value 		= oUnidad.NumeroChasis;
	Get('IdUnidad').value 		= oUnidad.IdUnidad;
	Get('NumeroFactura').value 		= oUnidad.NumeroFacturaCompra;
	Get('FechaFactura').value 		= oUnidad.FechaFacturaCompra;
	Get('Neto').value 		= oUnidad.ImporteCompraNeto;
	Get('Importe').value 		= oUnidad.ImporteNotaCredito;
	Get('VehiculoModelo').value = oModelo.DenominacionModelo;
}

function Add()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Add';	
	frmData.submit();
	
	return true;
}

function Delete(IdUnidad)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	if (confirm('¿Desea realmente eliminar el registro?'))
	{
		MainAction.value = 'Delete';	
		IdField.value = IdUnidad;	
		frmData.submit();
	}
	
	return true;
}

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

function VerificarPagoParcial(IdUnidad)
{
	if ($j('#PagoParcial_' + IdUnidad).is(':checked'))
	{
		$j('#Importe_' + IdUnidad).attr('readonly', false);
		$j('#Importe_' + IdUnidad).css('background-color', '#FFFFFF');
	}
	else
	{
		$j('#Importe_' + IdUnidad).attr('readonly', true);
		$j('#Importe_' + IdUnidad).css('background-color', '#DFDFDF');
	}
	
}

$j(document).ready(function() {
	$j('.pago-parcial').keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
			 (event.keyCode == 190 || event.keyCode == 188) || 
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
		var Total = 0;
		$j('.pago-parcial').each(function() {
			Total += parseFloat($j(this).val().replace(',', '.'));
		});
		$j('#TotalAPagar').html(Total.toFixed(2));
		$j('#TotalAPagar_v').html(Total.toFixed(2));
	});
});


</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdMinutaPago" id="IdMinutaPago" value="<?=$IdMinutaPago?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Pago - Modificar Unidades</span></td>
                    </tr>
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
                        <td width="33%">&nbsp;</td>
                        <td width="34%">&nbsp;</td>
                        <td width="33%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25" align="center"><strong>Fecha de la Minuta de Pago: </strong><?=CambiarFecha($oMinutaPago->Fecha)?></td>
                        <td height="25"></td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25" align="center"><strong>Monto Disponible: $</strong><?=number_format($oMinutaPago->MontoDisponible, 2, '.', ',')?></td>
                        <td height="25"></td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25" align="center"><strong>Monto A Pagar: $</strong>
							<label id="TotalAPagar"><?=number_format($TotalAPagar, 2, '.', ',')?></label>
							<input type="hidden" id="TotalAPagar_v" name="TotalAPagar_v" value="<?=$TotalAPagar?>" />
						</td>
                        <td height="25"></td>
                    </tr>
					<tr>
                        <td>&nbsp;</td>
                        <td><?php if ($errL) { ?><li style="color:red">El monto disponible es menor al monto a pagar</li><?php } ?>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
    <?php if ($arrData != NULL) { ?>
            
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Fecha Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Importe</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Pago Parcial</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Pago</strong></div></td>
                        <td width="85" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oMinutaPagoItem) { ?>
                    <?php $oUnidad = $oUnidades->GetById($oMinutaPagoItem->IdUnidad); ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="125" height="25"><div id="margen"  align="center"><?=$oUnidad->NumeroFacturaCompra?></div></td>
                        <td width="100" height="25"><div id="margen"  align="center"><?=CambiarFecha($oUnidad->FechaFacturaCompra)?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$<?=number_format($oUnidad->ImporteNotaCredito, 2)?></div></td>
                        <td width="100" height="25">
							<div id="margen" align="center">
								<input type="checkbox" id="PagoParcial_<?= $oUnidad->IdUnidad ?>" name="PagoParcial_<?= $oUnidad->IdUnidad ?>" value="1" onclick="VerificarPagoParcial('<?= $oUnidad->IdUnidad ?>');" <?= $oMinutaPagoItem->PagoParcial ? 'checked="checked"' : '' ?> />
							</div>
						</td>
						<td width="100" height="25">
							<div id="margen"  align="center"> 
								$<input type="text" class="camporFormularioChicoSuggest pago-parcial" <?= $oMinutaPagoItem->PagoParcial ? 'style="background-color: #FFFFFF"' : 'readonly="true"' ?> id="Importe_<?= $oUnidad->IdUnidad ?>" name="Importe_<?= $oUnidad->IdUnidad ?>" value="<?= number_format($oMinutaPagoItem->Importe, 2, '.', '') ?>" />
							</div>
						</td>
                        <td width="85" height="25">
                            <div align="center"> 
                                <a href="javascript: void(0);" onclick="Delete('<?=$oUnidad->IdUnidad?>');"><img src="images/iconos/del.gif" border="0" /></a>
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
				<?php
					if ($err[$oMinutaPagoItem->IdMinutaPagoItem])
					{
				?>
					<tr>
                        <td colspan="8">
                            <?php if ($err[$oMinutaPagoItem->IdMinutaPagoItem] & 1) { ?><li style="color:#FF0000;">El importe ingresado es mayor al bruto de la factura.</li><?php } ?>
                            <?php if ($err[$oMinutaPagoItem->IdMinutaPagoItem] & 2) { ?><li style="color:#FF0000;">El importe ingresado, junto a los pagos parciales ya efecturados, son mayor al bruto de la factura.</li><?php } ?>
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
				<?php
					}
				?>
          
                <?php } ?>      
                
                </table>		
           	</td>
      	</tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <tr>
        	<td align="center">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left">Observaciones:</td>
					</tr>
					<tr>
						<td align="left"><textarea id="Observaciones" name="Observaciones" class="camporFormularioSimple" style="height: 75px"><?= $Observaciones ?></textarea></td>
					</tr>

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
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Siguiente" onClick="javascript: Next();" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>La recepci&oacute;n no posee unidades cargadas.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
          
    <?php } ?>
    
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>