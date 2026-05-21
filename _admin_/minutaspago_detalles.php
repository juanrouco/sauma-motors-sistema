<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MINP_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinutaPago			= intval($_REQUEST['IdMinutaPago']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oMinutasPago			= new MinutasPago();
$oMinutasPagoItems		= new MinutasPagoItems();
$oUnidades				= new Unidades();
$oModelos				= new Modelos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe el recepcion */
if (!$oMinutaPago = $oMinutasPago->GetById($IdMinutaPago))
{	
	header("Location: minutaspago.php" . $strParams);
	exit();
}

/* obtenemos todos las unidades del recepcion */
$arrData = $oMinutasPagoItems->GetAllByIdMinutaPago($IdMinutaPago);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
</head>
<body>

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Pago</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                        <td width="129">&nbsp;</td>
                        <td width="644">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="9">&nbsp;</td>
                        <td height="25"><strong>Fecha: </strong></td>
                        <td height="25"><?=CambiarFecha($oMinutaPago->Fecha)?></td>
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
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Importe</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Pago Parcial</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Pago</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Saldo</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oMinutaPagoItem) { ?>
                    <?php $oUnidad = $oUnidades->GetById($oMinutaPagoItem->IdUnidad); ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="241" height="25"><div id="margen"><?=$oUnidad->NumeroFacturaCompra?></div></td>
                        <td width="241" height="25"><div id="margen"><?=CambiarFecha($oUnidad->FechaFacturaCompra)?></div></td>
                        <td width="241" height="25"><div id="margen">$<?=number_format($oUnidad->ImporteNotaCredito, 2)?></div></td>
                        <td width="241" height="25"><div id="margen"><?=$oMinutaPagoItem->PagoParcial ? ' Si' : 'No' ?></div></td>
                        <td width="241" height="25"><div id="margen">$<?=number_format($oMinutaPagoItem->Importe, 2)?></div></td>
                        <td width="241" height="25"><div id="margen">$<?=number_format($oMinutaPagoItem->Saldo, 2)?></div></td>
                    </tr>
                    <tr>
                        <td colspan="7">
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
        <tr>
        	<td align="center">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left"><strong>Observaciones:</strong></td>
					</tr>
					<tr>
						<td align="left"><?= $oMinutaPago->Observaciones ?></td>
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
                            	<input type="button" onclick="javascript: window.location.href='minutaspago.php<?=$strParams?>';" name="btnConfirmar" class="botonBasico" id="btnConfirmar" value="Volver" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } ?>  
    
        <tr>
        	<td>&nbsp;</td>
        </tr>
    </table>

</body>
</html>