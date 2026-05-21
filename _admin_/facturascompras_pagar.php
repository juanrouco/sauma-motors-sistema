<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MINP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFacturaCompra	= intval($_REQUEST['IdFacturaCompra']);
$Fecha				= strval($_REQUEST['Fecha']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err							= 0;
$oMinutaPagoItem 			= new MinutaPagoItem();
$oMinutasPagoItems			= new MinutasPagoItems();
$oFacturasCompras			= new FacturasCompras();
$oUnidadesFacturasCompras	= new UnidadesFacturasCompras();
$oUnidades					= new Unidades();
$oProveedores				= new Proveedores();
$oPadronesArba				= new PadronesArbaRetenciones();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oFacturaCompra = $oFacturasCompras->GetById($IdFacturaCompra))
{
	header('Location: facturascompras.php' . $strParams);
	exit;
}

if (!$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor))
{
	header('Location: facturascompras.php' . $strParams);
	exit;
}

/* si el formulario fue enviado... */
if ($Submit)
{
	/* validaciones... */
	if ($Fecha == '')
		$err |= 1;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$MontoDisponible = str_replace(',', '.', $MontoDisponible);
		$oMinutaPagoItem->Fecha				= $Fecha;
		$oMinutaPagoItem->IdMinutaPago	 	= $IdMinutaPago;
		$oMinutaPagoItem->IdFacturaCompra	= $IdFacturaCompra;
		$oMinutaPagoItem->Neto				= $oFacturaCompra->ImporteNeto;
		$oMinutaPagoItem->Importe			= $oFacturaCompra->ImporteNeto;
		$oMinutaPagoItem->Saldo				 = 0;
		
		$oPadronArba = $oPadronesArba->GetByCUIL(str_replace('-', '', $oFacturaCompra->Cuit), $oMinutaPagoItem->Fecha);
		if ($oPadronArba)
			$oMinutaPagoItem->Retencion		 = $oFacturaCompra->ImporteNeto * $oPadronArba->Retencion / 100;
		else
			$oMinutaPagoItem->Retencion		 = $oFacturaCompra->ImporteNeto * 4 / 100;
		$oMinutaPagoItem->IdFacturaCompra	= $oFacturaCompra->IdFacturaCompra;
		$oMinutaPagoItem->IdProveedor		= $oFacturaCompra->IdProveedor;
		$oMinutaPagoItem->Cuit				= $oFacturaCompra->Cuit;
		
		$arrUnidadesFacturasCompras = $oUnidadesFacturasCompras->GetAllByFacturaCompra($oFacturaCompra);
		
		if ($arrUnidadesFacturasCompras)
		{
			foreach ($arrUnidadesFacturasCompras as $oUnidadFacturaCompra)
			{
				$oUnidad = $oUnidades->GetById($oUnidadFacturaCompra->IdUnidad);
				$oUnidad->Cancelada	= 1;
				$oUnidades->Update($oUnidad);
			}
		}
				
		if ($oMinutaPagoItem = $oMinutasPagoItems->Create($oMinutaPagoItem))
		{
			header('Location: facturascompras.php' . $strParams);
			exit;
		}
	}
}
else
{
	/* determinamos como fecha de recepcion a la fecha de ayer */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
}

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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Compras - Realizar Pago</span></td>
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
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data" >
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
					<input type="hidden" name="IdFacturaCompra" id="IdFacturaCompra" value="<?=$IdFacturaCompra?>" />
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Fecha:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                </script>
                                            </div>
                                        </td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la fecha de la minuta de pago</li><?php } ?></td>
                                    </tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="80%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'facturascompras.php<?=$strParams?>';" value="Cancelar" />
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