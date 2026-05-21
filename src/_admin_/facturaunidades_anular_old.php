<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura			= intval($_REQUEST['IdFactura']);
$IdComprobante		= intval($_REQUEST['IdComprobante']);
$NumeroComprobante	= strval($_REQUEST['NumeroComprobante']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oFacturaUnidades	= new FacturaUnidades();
$oComprobantes		= new Comprobantes();
$oMinutas			= new Minutas();
$oUnidades			= new Unidades();
$oNotaCredito 	= new NotaCredito();
$oNotasCredito	= new NotasCredito();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
{
	header("Location: facturaunidades.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante))
{
	header("Location: facturaunidades.php" . $strParams);
	exit;
}

/* si el fomulario fue enviado */
if ($Submit)
{
	if ($NumeroComprobante == '')
		$err |= 2;
		
	if ($err == 0)
	{
		$oComprobante->IdEstado = ComprobanteEstados::Anulado;
		$oComprobante->FechaAnulada = date('d-m-Y');
		
		$oComprobante = $oComprobantes->Update($oComprobante);
		
		$oMinuta = $oMinutas->GetById($oFacturaUnidad->IdMinuta);
		$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
		$oUnidad->IdEstado = EstadoUnidad::Reservado;
		$oUnidades->Update($oUnidad);
		if ($NumeroComprobante != '')
		{
			$oNotaCredito->IdCliente			= $oMinuta->IdCliente;
			$oNotaCredito->IdComprobante		= $IdComprobante;
			$oNotaCredito->Fecha				= date('d-m-Y');
			$oNotaCredito->Comentarios 			= '';
			$oNotaCredito->Subtotal 			= $oFacturaUnidad->Subtotal;
			$oNotaCredito->Iva10 				= $oFacturaUnidad->Iva10;
			$oNotaCredito->Iva21 				= $oFacturaUnidad->Iva21;
			$oNotaCredito->Importe 				= $oFacturaUnidad->Total;
			$oNotaCredito->ImpuestoInterno 		= $oFacturaUnidad->ImpuestoInterno;
			$oNotaCredito->IdMinuta				= $oFacturaUnidad->IdMinuta;
			$oNotaCredito->IdFactura			= $oFacturaUnidad->IdComprobante;
			$oNotasCredito->Create($oNotaCredito);
			
			if ($oComprobanteNC = $oComprobantes->GetById($IdComprobante))
			{
				$oComprobanteNC->IdEstado = ComprobanteEstados::Utilizado;
				if ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaA)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoA;
				elseif ($oComprobanteNC->IdTipoComprobante == ComprobanteTipos::FacturaB)
					$oComprobanteNC->IdTipoComprobante = ComprobanteTipos::NotaCreditoB;
				
				$oComprobanteNC->Importe = $oFacturaUnidad->Total;
				$oComprobanteNC->Fecha = date('d-m-Y');
				$oComprobanteNC->ImporteIva10 = $oFacturaUnidad->Iva10;
				$oComprobanteNC->ImporteIva21 = $oFacturaUnidad->Iva21;
				$oComprobanteNC->ImpuestoInterno = $oFacturaUnidad->ImpuestoInterno;
						
				$oComprobantes->Update($oComprobanteNC);
			}
		}
		header("Location: facturaunidades.php" . $strParams);
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
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas de Unidades - Anular</span></td>
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
						<input type="hidden" name="IdFactura" id="IdFactura" value="<?=$IdFactura?>" />
						<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?=$oComprobante->IdComprobante?>" />
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
									<td><div align="center" class="campoEliminar"><?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="left"><strong>Nro. Nota de Cr&eacute;dito:</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>
										<div align="left">
											<input type="text" name="NumeroComprobante" id="NumeroComprobante" class="camporFormularioSimple" maxlength="8" value="<?=$oComprobanteNC->Numero?>" />
											<script language="javascript">
												arrParams['FilterIdEstado'] = '<?=ComprobanteEstados::Libre?>';
												SUGGESTRequest('Comprobantes', 'GetAll', 'NumeroComprobante', 'SetNumeroComprobante', 'IdComprobante', 'Numero', 'FilterNumero', arrParams);
											</script>
										</div>
									</td>
								</tr>
								<tr>
									<td<?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el nro. de nota de cr&eacute;dito</li><?php } ?>>&nbsp;</td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'facturaunidades.php<?=$strParams?>';">
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