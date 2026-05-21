<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oNumber			= new Number(); 

/* obtenemos los datos de la factura */
if (!$oFacturaUnidad = $oFacturaUnidades->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oFacturaUnidad->IdMinuta))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oComprobante->IdCliente))
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

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
	exit();

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUnidad->IdColor))
	exit();

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
	exit();

/* obtenemos los datos de la marca del motor */
if (!$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor))
	exit();

/* obtenemos los datos de la marca del chasis */
if (!$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis))
	exit();

/* obtenemos los datos del tipo de modelo */
if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
	exit();

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 10);

$yAlt = -0.5;

$Fecha = CambiarFecha($oFacturaUnidad->Fecha);
$arrFecha = explode('-', $Fecha);
$oPdf->Text(15.6, 3.95 + $yAlt, $arrFecha[0]);
$oPdf->Text(16.9, 3.95 + $yAlt, $arrFecha[1]);
$oPdf->Text(18, 3.95 + $yAlt, $arrFecha[2]);

if ($oMinuta->Condominio)
{
	/* obtenemos los datos del cliente */
	if (!$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio))
	exit();
	$oPdf->Text(2.9, 4.8 + 2.6 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
}
else
{
	if ($oFacturaUnidad->OtrosTitulares != '')
	{
		$oPdf->Text(2.9, 4.8 + 2.2 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oFacturaUnidad->OtrosTitulares);
	}
	else
	{
		$oPdf->Text(2.9, 4.6 + 2.2 + $yAlt, $oCliente->RazonSocial);
	}
}
$oPdf->Text(2.9,  4.6 + 3.4 + $yAlt, $oCliente->GetDomicilio());
//$oPdf->Text(4, 7.25 + $yAlt, $oCliente->Telefono);
$CodigoPostal = $oLocalidad->CodigoPostal;
if ($oCliente->DomicilioCodigoPostal)
	$CodigoPostal = $oCliente->DomicilioCodigoPostal;

$oPdf->SetFont('Arial', '', 10);
$oPdf->Text(10.5 + 2.9 + 0.5, 4.6 + 3.4 + $yAlt, $oLocalidad->Nombre . ' - C.P.: ' . $CodigoPostal);

//$oPdf->Text(12, 7.25 + $yAlt, $oProvincia->Nombre);
//$oPdf->Text(4, 7.6 + $yAlt, $oTipoIva->Nombre);
$oPdf->Text(10.5 + 2.9 + 0.5,  4.6 + 4.6 +$yAlt, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

/* datos del automotor */
$oPdf->Text(1.6, 8.3 + 3.5 + $yAlt, '1');
$oPdf->Text(3, 8.3 + 3.5 + $yAlt, 'MOTOCICLETA MARCA ' . $oMarcaVehiculo->Nombre . ' 0KM');
$oPdf->Text(3, 9 + $yAlt, 'Modelo:');
$oPdf->Text(6.2, 9 + 3.5 + $yAlt, $oModelo->DenominacionModelo);
$oPdf->Text(3, 9.7 + 3.5 + $yAlt, 'Nro. de Motor:');
$oPdf->Text(6.2, 9.7 + 3.5 + $yAlt, $oUnidad->NumeroMotor);
//$oPdf->Text(11, 10.5 + $yAlt, ' - ' . $oMarcaMotor->Nombre);
$oPdf->Text(3, 10.4 + 3.5 + $yAlt, 'Nro. de Chasis:');
$oPdf->Text(6.2, 10.4 + 3.5 + $yAlt, $oUnidad->NumeroChasis);
//$oPdf->Text(11, 11.5 + $yAlt, ' - ' . $oMarcaChasis->Nombre);
$oPdf->Text(3, 11 + 3.5 + $yAlt, utf8_decode('Modelo Año: ' . $oUnidad->Anio));
//$oPdf->Text(3, 12 + $yAlt, 'Color:');
//$oPdf->Text(5.5, 12 + $yAlt, $oColor->Nombre);
//$oPdf->Text(13, 12 + $yAlt, $oColor->Codigo);
$oPdf->Text(3, 11.65 + 3.5 + $yAlt, 'Nro. Int: ' . $oUnidad->IdUnidad);
//$oPdf->Text(3, 12.5 + $yAlt, 'Equipo:');
//$oPdf->Text(5.5, 12.5 + $yAlt, $oModelo->DenominacionComercial);
if ($oModelo->Iva == '10.5')
{
	$oPdf->Text(3, 13.5 + 3.5 + $yAlt, 'IVA:');
	$oPdf->Text(5.5, 13.5 + 3.5 + $yAlt, '10,5%');
}
if ($oFacturaUnidad->Observaciones != '')
{
	$oPdf->Text(3, 14.5 + 3.5 + $yAlt, 'Observaciones:');
	$oPdf->Text(3, 15 + 3.5 + $yAlt, $oFacturaUnidad->Observaciones);
}

$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';
$yAlt = -1.5;
/* importes */
$oPdf->Text(13.8 + 2.5, 9.3 + 3.5 + $yAlt, number_format($oFacturaUnidad->Subtotal, 2));
if ($oFacturaUnidad->ImpuestoInterno && $oFacturaUnidad->ImpuestoInterno != 0 && false)
{
	$oPdf->Text(3, 14 + 3.5 + $yAlt, 'Impuesto Interno');
	$oPdf->Text(13.8 + 2.5, 14 + 3.5 + $yAlt, number_format($oComprobante->ImpuestoInterno, 2));
}
$oPdf->Text(1.2, 19 + 8.4 + $yAlt, number_format($oFacturaUnidad->Subtotal, 2));
$oPdf->Text(4.5, 19 + 8.4 + $yAlt, number_format($oFacturaUnidad->ImpuestoInterno, 2));
$oPdf->Text(11.1, 19 + 8.4 + $yAlt, number_format($oFacturaUnidad->Iva10 + $oFacturaUnidad->Iva21, 2));
$oPdf->Text(13.8 + 2.5, 19 + 8.4 + $yAlt, number_format($oFacturaUnidad->Total, 2));

/* generamos el archivo */
//$oPdf->Output('factura_a.pdf', 'D');
$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();

?>