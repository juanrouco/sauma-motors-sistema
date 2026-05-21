<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaVarias = new FacturaVarias();
$oNotasCredito 	= new NotasCredito();
$oComprobantes 	= new Comprobantes();
$oClientes 		= new Clientes();
$oTiposIva 		= new TiposIva();
$oLocalidades 	= new Localidades();
$oPartidos 		= new Partidos();
$oProvincias 	= new Provincias();
$oPaises 		= new Paises();
$oNumber		= new Number(); 

/* obtenemos los datos de la factura */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
	exit();
	

	/* obtenemos los datos de la factura */
if (!$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaVaria->IdComprobante))
	exit();

/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oFacturaVaria->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos todos los detalles de la factura */
if (!$arrData = $oFacturaVaria->GetAllDetalles())
	exit();

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 12);
$yAlt = -0.5;

$Fecha = CambiarFecha($oNotaCredito->Fecha);
$arrFecha = explode('-', $Fecha);
$oPdf->Text(11.6, 2.45 + $yAlt, $arrFecha[0]);
$oPdf->Text(12.8, 2.45 + $yAlt, $arrFecha[1]);
$oPdf->Text(14, 2.45 + $yAlt, $arrFecha[2]);

/* datos del cliente */
$oPdf->Text(4, 4.8 + $yAlt, $oCliente->RazonSocial);
$oPdf->Text(4, 5.55 + $yAlt, $oCliente->GetDomicilio());
//$oPdf->Text(4, 7.25 + $yAlt, $oCliente->Telefono);
$CodigoPostal = $oLocalidad->CodigoPostal;
if ($oCliente->DomicilioCodigoPostal)
	$CodigoPostal = $oCliente->DomicilioCodigoPostal;
$oPdf->Text(12, 5.55 + $yAlt, $oLocalidad->Nombre . ' - C.P.: ' . $CodigoPostal);
//$oPdf->Text(12, 7.25 + $yAlt, $oProvincia->Nombre);
//$oPdf->Text(4, 7.6 + $yAlt, $oTipoIva->Nombre);
$oPdf->Text(11.5, 6.15 + $yAlt, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

/* datos de la facturacion */
$arrDetalles = explode(' ', $oFacturaVaria->Detalle);
$str = '';
$count = 0;
$rows = 0;
foreach ($arrDetalles as $oObs)
{
	$count += strlen($oObs);
	if ($count > 60)
	{				
		$count = strlen($oObs);
		$oPdf->Text(3, 8.3 + ($rows * 0.7), $str);
		$str = '';
		$rows++;
	}
	$str .= $oObs . ' ';
}
$oPdf->Text(3, 8 + ($rows * 0.7), $str);


$y=9.2;

$count = 1;
foreach ($arrData as $oFacturaVariaDetalle) 
{
	$Importe = $oFacturaVariaDetalle->Importe;
	
	if ($oFacturaVariaDetalle->IvaGravado)
	{
		if ($oFacturaVariaDetalle->IvaGravado == 1)
			$Importe = ($Importe / 1.21);
		else
			$Importe = ($Importe / 1.105);
	}
	
	$oPdf->Text(1.6, $y, $count);
	$oPdf->Text(3, $y, $oFacturaVariaDetalle->Detalle);
	$oPdf->Text(13.8, $y, number_format($Importe, 2));
	
	$y+=0.6;
	$count++;
}

$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';

/* importes */
$oPdf->Text(1.2, 19 - 1.5, number_format($oFacturaVaria->Subtotal, 2));
$oPdf->Text(9.1, 19 - 1.5, number_format($oFacturaVaria->Iva10 + $oFacturaVaria->Iva21, 2));
$oPdf->Text(13.8, 19 - 1.5, number_format($oFacturaVaria->Total, 2));

/* generamos el archivo */
$oPdf->Output('factura_a.pdf', 'D');

?>