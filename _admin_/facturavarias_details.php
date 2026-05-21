<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACV_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura	= intval($_REQUEST['IdFactura']);

/* declaracion de variables */
$oClientes				= new Clientes();
$oTiposIva				= new TiposIva();
$oFacturaVarias			= new FacturaVarias();
$oFacturaVariaDetalles	= new FacturaVariaDetalles();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe la factura */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
{	
	header("Location: facturavarias.php" . $strParams);
	exit();
}

/* verificamos si existe el cliente */
if (!$oCliente = $oClientes->GetById($oFacturaVaria->IdCliente))
{	
	header("Location: facturavarias.php" . $strParams);
	exit();
}

/* verificamos si existe el tipo de iva */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
{	
	header("Location: facturavarias.php" . $strParams);
	exit();
}

/* obtenemos todos los detalles de la factura */
$arrData = $oFacturaVaria->GetAllDetalles();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="IdFactura" id="IdFactura" value="<?=$IdFactura?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Ver Detalle</span></td>
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
                        <td height="25"><strong>N&uacute;mero Factura: </strong></td>
                        <td height="25"><?=$oFacturaVaria->NumeroComprobante?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Fecha: </strong></td>
                        <td height="25"><?=CambiarFecha($oFacturaVaria->Fecha)?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Detalle: </strong></td>
                        <td height="25"><?=$oFacturaVaria->Detalle?></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Iva Gravado</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe</strong></div></td>
                    </tr>
          
                <?php 
				$count = 1;
				foreach ($arrData as $oFacturaVariaDetalle) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="116" height="25"><div id="margen" align="center"><?=$count?></div></td>
                        <td width="416" height="25"><div id="margen"><?=$oFacturaVariaDetalle->Detalle?></div></td>
                        <td width="100" height="25"><div id="margen" align="center"><?=($oFacturaVariaDetalle->IvaGravado == '1') ? 'SI' : 'NO'?></div></td>
                        <td width="100" height="25"><div id="margen" align="center">$ <?=number_format($oFacturaVariaDetalle->Importe, 2)?></div></td>
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
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="5" cellspacing="5" class="bordeGris">

                <?php if ($oTipoIva->FacturaTipo == ComprobanteTipos::FacturaA) { ?>
                    <tr>
                        <td width="90%" align="right"><b>SubTotal Neto:</b></td>
                        <td width="10%" align="right">$ <?=number_format($oFacturaVaria->Subtotal, 2)?></td>
                    </tr>
                    <tr>
                        <td align="right"><b>IVA 21%:</b></td>
                        <td align="right">$ <?=number_format($oFacturaVaria->Iva21, 2)?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <td width="90%" align="right"><b>Total:</b></td>
                        <td width="10%" align="right">$ <?=number_format($oFacturaVaria->Total, 2)?></td>
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
                            	<input type="button" name="btnVolver" class="botonBasico" id="btnVolver" value="volver" onClick="javascript: window.location.href='facturavarias.php<?=$strParams?>';" />
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