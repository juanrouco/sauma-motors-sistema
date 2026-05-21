<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACV_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura	= intval($_REQUEST['IdFactura']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oFacturaVarias	= new FacturaVarias();
$oComprobantes	= new Comprobantes();
$oNotaCredito 	= new NotaCredito();
$oNotasCredito	= new NotasCredito();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
{
	header("Location: facturavarias.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaVaria->IdComprobante))
{
	header("Location: facturavarias.php" . $strParams);
	exit;
}

/* si el fomulario fue enviado */
if ($Submit)
{		
	$oComprobante->IdEstado = ComprobanteEstados::Anulado;
	$oComprobante->FechaAnulada = date('d-m-Y');
	
	$oComprobante = $oComprobantes->Update($oComprobante);
	
	$oComprobanteNC	= new Comprobante();
	if ($oComprobante->IdTipoComprobante == ComprobanteTipos::FacturaA)
		$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
	elseif ($oComprobante->IdTipoComprobante == ComprobanteTipos::FacturaB)
		$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
	$oComprobanteNC->Prefijo = str_pad(ConfiguracionFactura::PuntoVenta, 4, "0", STR_PAD_LEFT);
	$oComprobanteNC->Numero = '00000000';
	$oComprobanteNC->IdEstado = ComprobanteEstados::Libre;
		
	$oComprobanteNC = $oComprobantes->Create($oComprobanteNC);
	
	$oNotaCredito->IdCliente			= $oFacturaVaria->IdCliente;
	$oNotaCredito->IdComprobante		= $oComprobanteNC->IdComprobante;
	$oNotaCredito->Fecha				= date('d-m-Y');
	$oNotaCredito->Comentarios 			= '';
	$oNotaCredito->Subtotal 			= $oFacturaVaria->Subtotal;
	$oNotaCredito->Iva10 				= $oFacturaVaria->Iva10;
	$oNotaCredito->Iva21 				= $oFacturaVaria->Iva21;
	$oNotaCredito->Importe 				= $oFacturaVaria->Total;
	$oNotaCredito->IdFactura			= $oComprobante->IdComprobante;
	$oNotasCredito->Create($oNotaCredito);
			
	if ($oComprobanteNC = $oComprobantes->GetById($oComprobanteNC->IdComprobante))
	{
		$oComprobanteNC->IdEstado = ComprobanteEstados::Utilizado;
				
		$oComprobanteNC->Importe = $oComprobante->Importe;
		$oComprobanteNC->Fecha = date('d-m-Y');
		$oComprobanteNC->ImporteIva10 = $oComprobante->ImporteIva10;
		$oComprobanteNC->ImporteIva21 = $oComprobante->ImporteIva21;
		$oComprobanteNC->ImpuestoInterno = $oComprobante->ImpuestoInterno;
		$oComprobanteNC->IdCliente = $oComprobante->IdCliente;
						
		$oComprobantes->Update($oComprobanteNC);
	}

	header("Location: facturavarias_notascredito_afip.php?IdFactura=" . $oFacturaVaria->IdFactura);
	exit;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>


<script type="text/javascript">


</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Anular</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="center"><strong>&iquest;Esta seguro que desea anular la siguiente factura?</strong></div></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="center" class="campoEliminar"><?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table width="60%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="1"><div align="center"></div></td>
						</tr>
					</table>
					<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						
							<input type="hidden" name="Submitted" id="Submitted" value="1" />
							<input type="hidden" name="IdFactura" id="IdFactura" value="<?=$IdFactura?>" />
							<tr>
								<td height="30">
									<div align="center">
										<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
										<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'facturavarias.php<?=$strParams?>';">
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