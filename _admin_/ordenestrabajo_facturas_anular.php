<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ORDE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFacturaPostVenta	= intval($_REQUEST['IdFacturaPostVenta']);
$IdComprobante		= intval($_REQUEST['IdComprobante']);
$NumeroComprobante	= strval($_REQUEST['NumeroComprobante']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oFacturasPostVentas	= new FacturasPostVentas();
$oComprobantes			= new Comprobantes();
$oNotaCredito 			= new NotaCredito();
$oNotasCredito			= new NotasCredito();
$oPagos				= new Pagos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta))
{
	header("Location: ordenestrabajo_facturacion.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante))
{
	header("Location: ordenestrabajo_facturacion.php" . $strParams);
	exit;
}
/* si el fomulario fue enviado */
if ($Submit)
{		
	if ($err == 0)
	{
		$oComprobante->IdEstado = ComprobanteEstados::Anulado;
		$oComprobante->FechaAnulada = date('d-m-Y');
		
		$oComprobante = $oComprobantes->Update($oComprobante);
		
		if (true)
		{
			$oComprobanteNC	= new Comprobante();
			if ($oComprobante->IdTipoComprobante == ComprobanteTipos::FacturaA)
				$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
			elseif ($oComprobante->IdTipoComprobante == ComprobanteTipos::FacturaB)
				$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
			$oComprobanteNC->Prefijo = str_pad(ConfiguracionFactura::PuntoVentaPV, 4, "0", STR_PAD_LEFT);
			$oComprobanteNC->Numero = '00000000';
			$oComprobanteNC->IdEstado = ComprobanteEstados::Libre;
				
			$oComprobanteNC = $oComprobantes->Create($oComprobanteNC);
			
			$oNotaCredito->IdCliente			= $oComprobante->IdCliente;
			$oNotaCredito->IdComprobante		= $oComprobanteNC->IdComprobante;
			$oNotaCredito->Fecha				= date('d-m-Y');
			$oNotaCredito->Comentarios 			= '';
			$oNotaCredito->Subtotal 			= $oFacturaPostVenta->ImporteNeto;
			$oNotaCredito->Iva10 				= $oFacturaPostVenta->Iva10;
			$oNotaCredito->Iva21 				= $oFacturaPostVenta->Iva21;
			$oNotaCredito->Importe 				= $oFacturaPostVenta->ImporteBruto;
			$oNotaCredito->PercepcionIIBB 		= $oFacturaPostVenta->PercepcionIIBB;
			$oNotaCredito->IdFactura			= $oComprobante->IdComprobante;
			
			$oNotasCredito->Create($oNotaCredito);
			
		
			$arrPagos = $oPagos->GetByIdFacturaPostVenta($oFacturaPostVenta->IdFacturaPostVenta);
			if ($arrPagos)
			{
				foreach ($arrPagos as $oPago)
				{
					$oPagos->Delete($oPago->IdPago);
				}
			}
			//$oFacturasPostVentas->EliminarMovimientoPago($oFacturaPostVenta->IdFacturaPostVenta);
			
			if ($oComprobanteNC = $oComprobantes->GetById($oComprobanteNC->IdComprobante))
			{
				$oComprobanteNC->IdEstado = ComprobanteEstados::Utilizado;
				if ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaA)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
				elseif ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaB)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
				
				$oComprobanteNC->Importe = $oFacturaPostVenta->ImporteBruto;
				$oComprobanteNC->Fecha = date('d-m-Y');
				$oComprobanteNC->ImporteIva10 = $oComprobante->ImporteIva10;
				$oComprobanteNC->ImporteIva21 = $oComprobante->ImporteIva21;
				$oComprobanteNC->PercepcionIIBB = $oComprobante->PercepcionIIBB;
				$oComprobanteNC->IdCliente = $oComprobante->IdCliente;
						
				$oComprobantes->Update($oComprobanteNC);
			}
		}
		header("Location: ordenestrabajo_factura_notascredito_afip.php?IdFactura=" . $oFacturaPostVenta->IdFacturaPostVenta);
		exit;
	}
}


/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">

var IdTipoComprobante = '';
var arrParams = new Array();

function GetNextFactura(IdTipoComprobante)
{
	var arr = new Array();
	var obj;
	var oComprobante;

	if ((IdTipoComprobante == '') || (IdTipoComprobante == '0'))
		return;
				
	arr['IdTipoComprobante'] = IdTipoComprobante;
	obj = SendXMLRequest('Comprobantes', 'GetNext', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oComprobante = obj.Response;

	return oComprobante;	
}

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	Get('IdComprobante').value 		= IdComprobante;
	Get('NumeroComprobante').value 	= NumeroComprobante;
}

$j(document).ready(function() {
	if ('<?= $oComprobante->IdTipoComprobante ?>'  == '<?=ComprobanteTipos::FacturaA?>')
	{		
		IdTipoComprobante = '<?=ComprobanteTipos::NotaCreditoA?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
	}
	else if ('<?= $oComprobante->IdTipoComprobante ?>' == '<?=ComprobanteTipos::FacturaB?>')
	{
		IdTipoComprobante = '<?=ComprobanteTipos::NotaCreditoB?>';
		arrParams['FilterIdTipoComprobante'] = IdTipoComprobante;
	}
});

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Orden de Trabajo N&deg; <?= $oFacturaPostVenta->IdOrdenTrabajo ?> - Anular Factura</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdFacturaPostVenta" id="IdFacturaPostVenta" value="<?=$IdFacturaPostVenta?>" />
			<div align="center">
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
									<td><div align="center" class="campoEliminar"><?= ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) ?> <?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
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
					
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'ordenestrabajo_facturacion.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					
				</table>
			</div>
			</form>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>