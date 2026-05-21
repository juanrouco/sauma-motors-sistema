<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_STOCK_CREATE))
	Session::NoPerm();

$IdCompra				= intval($_REQUEST['IdCompra']);
$Importe				= floatval($_REQUEST['Importe']);
$Cliente				= strval($_REQUEST['Cliente']);
$IdCliente				= intval($_REQUEST['IdCliente']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$IdComprobante			= intval($_REQUEST['IdComprobante']);
$NumeroComprobante		= strval($_REQUEST['NumeroComprobante']);
$IdFormaPago			= intval($_REQUEST['IdFormaPago']);
$IdPlanCuota			= intval($_REQUEST['IdPlanCuota']);
$Descuento				= floatval($_REQUEST['Descuento']);
$Interes				= floatval($_REQUEST['Interes']);
$Comentarios			= strval($_REQUEST['Comentarios']);
$Submit					= (isset($_REQUEST['Submitted']));

$err						= 0;
$oCompraFactura				= new CompraFactura();
$oCompras					= new Compras();
$oClientes					= new Clientes();
$oComprobantes				= new Comprobantes();
$oFormasPago				= new FormasPago();
$oPlanesCuotas				= new PlanesCuotas();
$oOrdenesTrabajoFranquicias	= new OrdenesTrabajoFranquicias();
$oTiposIva					= new TiposIva();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$oCompra = $oCompras->GetById($IdCompra);

$TotalFranquicia = 0;

$arrFormasPago = $oFormasPago->GetAll();

if ($Submit)
{	
		
	if ($IdCliente == '' || $IdCliente == 0)
		$err |= 4;
	
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oCliente = $oClientes->GetById($IdCliente);
		$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
		$oComprobante = $oComprobantes->GetById($IdComprobante);
		$oComprobante->IdEstado = ComprobanteEstados::Utilizado;
		$oComprobante->IdCompra = $oCompra->IdCompra;
		$oComprobante->Fecha = date('d-m-Y');
		$oComprobante->IdCliente = $oCliente->IdCliente;
		//print_r($oComprobante);exit;
		
		$oFacturaPostVenta = $oCompraFactura->GenerarFacturaSinGuardar($oCompra, $oCliente, $oComprobante, $IdPlanCuota, $Descuento, $Comentarios, $Interes);
		
		$arrData = $oCompraFactura->arrItems;
	}
}
else
{
	$oCliente = $oClientes->GetById($oCompra->IdCliente);
	$IdCliente = $oCliente->IdCliente;
	$Cliente = $oCliente->RazonSocial;
	$IdFormaPago = FormaPago::Efectivo;
	$IdPlanCuota = 1;
	$IdComprobante = $oCompra->IdComprobante;
	$Descuento = 0;
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="IdFacturaPostVenta" id="IdFacturaPostVenta" value="<?=$IdFacturaPostVenta?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Ver Vista Prev&iacute;a</span></td>
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
                        <td>&nbsp;</td>
                        <td width="124">&nbsp;</td>
                        <td width="709">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Cliente: </strong></td>
                        <td height="25"><?=$oCliente->RazonSocial?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Factura: </strong></td>
                        <td height="25"><?=ComprobanteTipos::GetDescripcionById($oTipoIva->FacturaTipo)?> <?= $oFacturaPostVenta->NumeroFactura ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
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
                        <td width="116" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Item</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Detalle</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Neto</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Iva 21%</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Iva 10,5%</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Bruto</strong></div></td>
                    </tr>
          
                <?php 
				$count = 1;
				foreach ($arrData as $oFacturaItem) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="116" height="25"><div id="margen" align="center"><?=$count?></div></td>
                        <td width="416" height="25"><div id="margen"><?=$oFacturaItem->Descripcion?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->ImporteNeto, 2)?></div></td>
						<td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->Iva21, 2)?></div></td>
						<td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->Iva10, 2)?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->ImporteBruto, 2)?></div></td>
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
					$count++;
				} ?>      
                
                </table>		
           	</td>
      	</tr>
		<?php
		if ($oFacturaPostVenta->Comentarios)
		{
		?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
                <table width="90%" align="center" cellpadding="5" cellspacing="5">
					<tr>
						<td>
							<strong>Comentarios: </strong><?= $oFacturaPostVenta->Comentarios ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<?php
		}
		?>
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="5" cellspacing="5" class="bordeGris">

                <?php if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA) { ?>
                    <tr>
                        <td width="90%" align="right"><b>SubTotal Neto:</b></td>
                        <td width="10%" align="right">$ <?=number_format($oFacturaPostVenta->ImporteNeto, 2)?></td>
                    </tr>
                    <tr>
                        <td align="right"><b>IVA 21%:</b></td>
                        <td align="right">$ <?=number_format($oFacturaPostVenta->Iva21, 2)?></td>
                    </tr>
					
                    <tr>
                        <td align="right"><b>IVA 10,5%:</b></td>
                        <td align="right">$ <?=number_format($oFacturaPostVenta->Iva10, 2)?></td>
                    </tr>
                    <tr>
                        <td align="right"><b>Perc. IIBB:</b></td>
                        <td align="right">$ <?=number_format($oFacturaPostVenta->PercepcionIIBB, 2)?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <td width="90%" align="right"><b>Total:</b></td>
                        <td width="10%" align="right">$ <?=number_format($oFacturaPostVenta->ImporteBruto, 2)?></td>
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
                            	<input type="button" name="btnVolver" class="botonBasico" id="btnVolver" value="volver" onClick="javascript: var win = window.open('', '_self');win.close();return false;" />
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
                        <td><div align="center"><strong>La recepci&oacute;n no posee detalles cargados.</strong></div></td>
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