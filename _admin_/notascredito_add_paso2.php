<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();


/* obtiene datos enviados */
$IdNotaCredito	= intval($_REQUEST['IdNotaCredito']);
$Detalle	= strval($_REQUEST['Detalle']);
$Action		= strval($_REQUEST['MainAction']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$Subtotal				= 0;
$Iva10					= 0;
$Iva21					= 0;
$Total					= 0;
$oClientes				= new Clientes();
$oComprobantes			= new Comprobantes();
$oTiposIva				= new TiposIva();
$oNotasCredito			= new NotasCredito();
$oNotaCreditoDetalle	= new NotaCreditoDetalle();
$oNotasCreditoDetalles	= new NotasCreditoDetalles();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe la factura */
if (!$oNotaCredito = $oNotasCredito->GetById($IdNotaCredito))
{	
	header("Location: notascredito.php" . $strParams);
	exit();
}

if (!$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante))
{	
	header("Location: notascredito.php" . $strParams);
	exit();
}

/* verificamos si existe el cliente */
if (!$oCliente = $oClientes->GetById($oNotaCredito->IdCliente))
{	
	header("Location: notascredito.php" . $strParams);
	exit();
}

/* verificamos si existe el tipo de iva */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: notascredito.php" . $strParams);
	exit();
}

/* obtenemos todos los detalles de la factura */
$arrData = $oNotaCredito->GetAllDetalles();

/* si el formulario fue enviado... */
if ($Submit)
{
	foreach ($arrData as $oNotaCreditoDetalle) 
	{
		$Importe = $oNotaCreditoDetalle->Importe;

		if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA || true)
		{
			if ($oNotaCreditoDetalle->IdIva == Iva::Iva21)
			{
				$Importe = ($Importe / 1.21);
				$Iva21 += $oNotaCreditoDetalle->Importe - $Importe;
			}
			else
			{
				$Importe = ($Importe / 1.105);
				$Iva10 += $oNotaCreditoDetalle->Importe - $Importe;
			}
			
			$Subtotal += $Importe;
						
		}
		
		$Total += $oNotaCreditoDetalle->Importe;
	}
	
	/* actualizamos detalle e importes de la factura */
	$oNotaCredito->Comentarios 	= $Detalle;
	$oNotaCredito->Subtotal 	= $Subtotal;
	$oNotaCredito->Iva10 		= $Iva10;
	$oNotaCredito->Iva21 		= $Iva21;
	$oNotaCredito->Importe 		= $Total;
	
	$oNotasCredito->Update($oNotaCredito);
	
	if ($oComprobante = $oComprobantes->GetById($IdComprobante))
	{
		$oComprobante->Importe = $Importe;
		$oComprobante->ImporteIva10 = $Iva10;
		$oComprobante->ImporteIva21 = $Iva21;
				
		$oComprobantes->Update($oComprobante);
	}
	
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Add':
		
			/* obtenemos los datos del detalle a agregar */
			$DetalleItem 	= strval($_REQUEST['DetalleItem']);
			$IdIva 			= strval($_REQUEST['IdIva']);
			$Importe		= floatval($_REQUEST['Importe']);

			/* validaciones... */
			if ($DetalleItem == '')
				$err |= 1;
			if ($IdIva == '')
				$err |= 2;
			if ($Importe == '')
				$err |= 4;
		
			/* si no hay errores... */
			if ($err == 0)
			{
				$oNotaCreditoDetalle = new NotaCreditoDetalle();
				
				$oNotaCreditoDetalle->IdNotaCredito = $IdNotaCredito;
				$oNotaCreditoDetalle->Detalle 		= $DetalleItem;
				$oNotaCreditoDetalle->IdIva 		= $IdIva;
				$oNotaCreditoDetalle->Importe		= $Importe;
				
				$oNotaCreditoDetalle = $oNotasCreditoDetalles->Create($oNotaCreditoDetalle);

				$Operation = Operaciones::Create;
				$Status = (($oNotaCreditoDetalle) ? true : false);
				
				/* seteamos variables en null */
				$DetalleItem 	= '';
				$IdIva		 	= '';
				$Importe 		= '';
			}
		
			break;

		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdDetalle = intval($_REQUEST['Id']);

			/* si no hay errores... */
			if ($oNotasCreditoDetalles->GetById($IdDetalle))
			{
				$oNotaCreditoDetalle = $oNotasCreditoDetalles->Delete($IdDetalle);

				$Operation = Operaciones::Delete;
				$Status = (($oNotaCreditoDetalle) ? true : false);
			}

			break;

		case 'Finish':
		
			header('Location: notascredito.php' . $strParams);
			exit;
		
			break;

		default:
			break;
	}
}

/* obtenemos todos los detalles de la factura */
$arrData = $oNotaCredito->GetAllDetalles();

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
    <input type="hidden" name="IdNotaCredito" id="IdNotaCredito" value="<?=$IdNotaCredito?>" />
	
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Notas de Cr&eacute;dito - Agregar Detalles</span></td>
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
                        <td height="25"><strong>Nota de Cr&eacute;dito Tipo: </strong></td>
                        <td height="25"><?=ComprobanteTipos::GetDescripcionById($oTipoIva->FacturaTipo)?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Nota de Cr&eacute;dito: </strong></td>
                        <td height="25"><?=$oComprobante->Prefijo?>-<?=$oComprobante->Numero?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Fecha: </strong></td>
                        <td height="25"><?=CambiarFecha($oNotaCredito->Fecha)?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Detalle: </strong></td>
                        <td height="25">
                        	<textarea name="Detalle" id="Detalle" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$Detalle?></textarea>
                        </td>
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
                                    <td width="30%"><div id="margen" align="left">Detalle:</div></td>
									<td width="30%"><div id="margen" align="left">Iva:</div></td>
                                    <td width="30%"><div id="margen" align="left">Importe C/IVA:</div></td>									
                                    <td width="8%"><div id="margen" align="left">&nbsp;</div></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div id="margen" align="left">
				                        	<textarea name="DetalleItem" id="DetalleItem" class="camporFormularioMultiline" onkeyup="javascript: StrToUpper(this.id);"><?=$DetalleItem?></textarea>
                                        </div>
                                    </td>
									<td valign="top">
                                        <div id="margen" align="left">
                                            <select name="IdIva" id="IdIva" class="camporFormularioMediano">
												<option value="">Seleccione el Iva</option>
												<option value="1" <?= $IdIva == '1' ? 'selected="selected"' : '' ?>>21%</option>
												<option value="2" <?= $IdIva == '2' ? 'selected="selected"' : '' ?>>10,5%</option>
											</select>
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
									<td align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione el Tipo de Iva</li><?php } ?></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Iva</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe</strong></div></td>
                        <td width="86" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
				$count = 1;
				$Iva10 = 0;
				$Iva21 = 0;
				foreach ($arrData as $oNotaCreditoDetalle) { ?>
                	<?php 
					$Iva = 0;
					$Importe = $oNotaCreditoDetalle->Importe;
					if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA)
					{
						if ($oNotaCreditoDetalle->IdIva == Iva::Iva21)
						{
							$Importe = ($Importe / 1.21);
							$Iva21 += $oNotaCreditoDetalle->Importe - $Importe;
							$Iva = $oNotaCreditoDetalle->Importe - $Importe;
						}
						else
						{
							$Importe = ($Importe / 1.105);
							$Iva10 += $oNotaCreditoDetalle->Importe - $Importe;
							$Iva = $oNotaCreditoDetalle->Importe - $Importe;
						}
						
						$Subtotal += $Importe;
						
					}
					
					$Total += $oNotaCreditoDetalle->Importe;
					?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="116" height="25"><div id="margen" align="center"><?=$count?></div></td>
                        <td width="416" height="25"><div id="margen"><?=$oNotaCreditoDetalle->Detalle?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($Iva, 2)?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($Importe, 2)?></div></td>
                        <td width="86" height="25">
                            <div align="center"> 
                                <a href="javascript: void(0);" onclick="Delete('<?=$oNotaCreditoDetalle->IdNotaCreditoDetalle?>');"><img src="images/iconos/del.gif" border="0" /></a>
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
                        <td align="right"><b>IVA 10,5%:</b></td>
                        <td align="right">$ <?=number_format($Iva10, 2)?></td>
                    </tr>
					<tr>
                        <td align="right"><b>IVA 21%:</b></td>
                        <td align="right">$ <?=number_format($Iva21, 2)?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <td align="right"><b>Total:</b></td>
                        <td align="right">$ <?=number_format($Total, 2)?></td>
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