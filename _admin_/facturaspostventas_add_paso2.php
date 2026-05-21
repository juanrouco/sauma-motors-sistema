<?php
require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACTPV_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFacturaPostVenta	= intval($_REQUEST['IdFacturaPostVenta']);
$Detalle			= strval($_REQUEST['Detalle']);
$Action				= strval($_REQUEST['MainAction']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$Subtotal				= 0;
$Iva10					= 0;
$Iva21					= 0;
$Total					= 0;
$oClientes				= new Clientes();
$oTiposIva				= new TiposIva();
$oFacturasPostVentas	= new FacturasPostVentas();
$oFacturaItem			= new FacturaItem();
$oFacturasItems			= new FacturasItems();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe la factura */
if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta))
{	
	header("Location: facturaspostventas.php" . $strParams);
	exit();
}

/* verificamos si existe el cliente */
if (!$oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente))
{	
	header("Location: facturaspostventas.php" . $strParams);
	exit();
}

/* verificamos si existe el tipo de iva */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: facturaspostventas.php" . $strParams);
	exit();
}

/* obtenemos todos los detalles de la factura */
$arrData = $oFacturaPostVenta->GetAllItems();

/* si el formulario fue enviado... */
if ($Submit)
{	
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Add':
		
			/* obtenemos los datos del detalle a agregar */
			$DetalleFactura 	= strval($_REQUEST['DetalleFactura']);
			$Importe			= floatval($_REQUEST['Importe']);

			/* validaciones... */
			if ($DetalleFactura == '')
				$err |= 1;
			if ($Importe == '')
				$err |= 4;
				
			/* si no hay errores... */
			if ($err == 0)
			{
				$oFacturaItem = new FacturaItem();
				
				$oFacturaItem->IdFactura 	= $IdFacturaPostVenta;
				$oFacturaItem->Descripcion 	= $DetalleFactura;
				$oFacturaItem->Cantidad 	= 1;
				$oFacturaItem->ImporteNeto	= $Importe / 1.21;
				$oFacturaItem->ImporteBruto	= $Importe;
				$oFacturaItem->IdIva		= Iva::Iva21;
				$oFacturaItem->IvaAlicuota	= 21;
				$oFacturaItem->Iva21		= ($Importe / 1.21) * 0.21;
				
				
				$oFacturaItem = $oFacturasItems->Create($oFacturaItem);

				$Operation = Operaciones::Create;
				$Status = (($oFacturaItem) ? true : false);
				
				/* seteamos variables en null */
				$DetalleFactura 	= '';
				$Importe 			= '';
			}
		
			break;

		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdDetalle = intval($_REQUEST['Id']);

			/* si no hay errores... */
			if ($oFacturasItems->GetByIdIncrement($IdDetalle))
			{
				$oFacturaItem = $oFacturasItems->Delete($IdDetalle);

				$Operation = Operaciones::Delete;
				$Status = (($oFacturaItem) ? true : false);
			}

			break;

		case 'Finish':
		
			print_r("<script>window.open('facturaspostventas_imprimir.php?IdFacturaPostVenta=" . $oFacturaPostVenta->IdFacturaPostVenta . "');window.location.href='facturaspostventas.php" . $strParams . "';</script>");
			exit;
		
			break;

		default:
			break;
	}
	
	
	$arrData = $oFacturaPostVenta->GetAllItems();
	
	foreach ($arrData as $oFacturaItem) 
	{
		$Importe = $oFacturaItem->Importe;

		if (true || $oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA)
		{
			$Importe = $oFacturaItem->ImporteNeto;
			
			$Subtotal += $Importe;
			$Iva21 += $oFacturaItem->Iva21;
		}
		
		$Total += $oFacturaItem->ImporteBruto;
	}
	
	/* actualizamos detalle e importes de la factura */
	$oFacturaPostVenta->ImporteNeto		= $Subtotal;
	$oFacturaPostVenta->Iva10 			= $Iva10;
	$oFacturaPostVenta->Iva21 			= $Iva21;
	$oFacturaPostVenta->PercepcionIIBB 	= $oFacturaPostVenta->ImporteNeto * $oCliente->PercepcionIIBB / 100;
	$oFacturaPostVenta->ImporteBruto	= $Total + $oFacturaPostVenta->PercepcionIIBB;
	
	$oFacturasPostVentas->Update($oFacturaPostVenta);
}

/* obtenemos todos los detalles de la factura */
$arrData = $oFacturaPostVenta->GetAllItems();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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

function Delete(IdDetalle)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	if (confirm('¿Desea realmente eliminar el registro?'))
	{
		MainAction.value = 'Delete';	
		IdField.value = IdDetalle;
		frmData.submit();
	}
	
	return true;
}

function Finish()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Finish';	
	frmData.submit();
	
	return true;
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdFacturaPostVenta" id="IdFacturaPostVenta" value="<?=$IdFacturaPostVenta?>" />
	

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Post Venta - Agregar Detalles</span></td>
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
                        <td height="25"><strong>Factura Tipo: </strong></td>
                        <td height="25"><?=ComprobanteTipos::GetDescripcionById($oTipoIva->FacturaTipo)?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Fecha: </strong></td>
                        <td height="25"><?=CambiarFecha($oFacturaPostVenta->Fecha)?></td>
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
        <tr>
            <td>
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="2%">&nbsp;</td>
                                    <td width="50%"><div id="margen" align="left">Detalle:</div></td>
                                    <td width="40%"><div id="margen" align="left">Importe C/IVA:</div></td>									
                                    <td width="8%"><div id="margen" align="left">&nbsp;</div></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div id="margen" align="left">
				                        	<input type="text" name="DetalleFactura" id="DetalleFactura" class="camporFormularioSimple" value="<?= $DetalleFactura ?>" />
                                        </div>
                                    </td>
									<td valign="top">
                                        <div id="margen" align="left">
                                            <input type="text" name="Importe" id="Importe" class="camporFormularioMediano" maxlength="128" value="<?=$Importe?>" />
                                        </div>
                                    </td>
                                    <td valign="top" align="center"><input type="button" name="btnAgregar" value="Agregar" class="botonBasico" onClick="javascript: Add();" /></td>
                                </tr>
                                
                            <?php if (($err != 0)) { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el detalle a facturar</li><?php } ?></td>
                                    <td align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el importe a facturar</li><?php } ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php } ?>
                                
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>IVA</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Bruto</strong></div></td>
                        <td width="86" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
				$count = 1;
				$Total = 0;
				$Iva21 = 0;
				$Subtotal = 0;
				foreach ($arrData as $oFacturaItem) { ?>
                	<?php 
					$Importe = $oFacturaItem->ImporteNeto;
					$Subtotal += $oFacturaItem->ImporteNeto;
					$Iva21 += $oFacturaItem->Iva21;
					$Total += $oFacturaItem->ImporteBruto;
					?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="116" height="25"><div id="margen" align="center"><?=$count?></div></td>
                        <td width="416" height="25"><div id="margen"><?=$oFacturaItem->Descripcion?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->ImporteNeto, 2)?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->Iva21, 2)?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaItem->ImporteBruto, 2)?></div></td>
                        <td width="86" height="25">
                            <div align="center"> 
                                <a href="javascript: void(0);" onclick="Delete('<?=$oFacturaItem->IdFacturaItem?>');"><img src="images/iconos/del.gif" border="0" /></a>
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
					$count++;
				} 
				?>      
                
                </table>		
           	</td>
      	</tr>
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="5" cellspacing="5" class="bordeGris">

                <?php if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA) { ?>
                    <tr>
                        <td width="90%" align="right"><b>SubTotal Neto:</b></td>
                        <td width="10%" align="right">$ <?=number_format($Subtotal, 2)?></td>
                    </tr>
                    <tr>
                        <td align="right"><b>IVA 21%:</b></td>
                        <td align="right">$ <?=number_format($Iva21, 2)?></td>
                    </tr>

                    <tr>
                        <td align="right"><b>Perc. IIBB:</b></td>
                        <td align="right">$ <?=number_format($Subtotal  * $oCliente->PercepcionIIBB / 100, 2)?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <td align="right"><b>Total:</b></td>
                        <td align="right">$ <?=number_format($Total + $Subtotal  * $oCliente->PercepcionIIBB / 100, 2)?></td>
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
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Finalizar" onClick="javascript: Finish();" />
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