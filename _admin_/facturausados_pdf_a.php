<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFactura = intval($_REQUEST['IdFactura']);

$oFacturaUsados 	= new FacturaUsados();
$oComprobantes 		= new Comprobantes();
$oMinutas 			= new MinutasUsados();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oUsados 			= new Usados();
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
if (!$oFacturaUsado = $oFacturaUsados->GetById($IdFactura))
	exit();

/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oFacturaUsado->IdComprobante))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oFacturaUsado->IdMinuta))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
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
if (!$oUsado = $oUsados->GetById($oMinuta->IdUsado))
	exit();

/* obtenemos los datos del color */
if (!$oColor = $oColores->GetById($oUsado->IdColor))
	exit();

/* obtenemos los datos de la marca del vehiculo */
if (!$oMarcaVehiculo = $oMarcas->GetById($oUsado->IdMarca))
	exit();

/* obtenemos los datos de la marca del motor */
$oMarcaMotor = $oMarcas->GetById($oUsado->IdMarcaMotor);

/* obtenemos los datos de la marca del chasis */
$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis);

/* obtenemos los datos del tipo de modelo */
$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$yAlt = -0.5;

$oPdf->Text(17.2, 3.8 + $yAlt, CambiarFecha($oFacturaUsado->Fecha));

/* datos del cliente */
if ($oMinuta->Condominio)
{
	/* obtenemos los datos del cliente */
	if (!$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio))
	exit();
	$oPdf->Text(4, 6.1 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
}
else
{
	if ($oFacturaUsado->OtrosTitulares != '')
	{
		$oPdf->Text(4, 6.1 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oFacturaUsado->OtrosTitulares);
	}
	else
	{
		$oPdf->Text(4, 6.1 + $yAlt, $oCliente->RazonSocial);
	}
}
$oPdf->Text(4, 6.9 + $yAlt, $oCliente->GetDomicilio());
$oPdf->Text(4, 7.25 + $yAlt, $oCliente->Telefono);
$CodigoPostal = $oLocalidad->CodigoPostal;
if ($oCliente->DomicilioCodigoPostal)
	$CodigoPostal = $oCliente->DomicilioCodigoPostal;
$oPdf->Text(12, 6.9 + $yAlt, $oLocalidad->Nombre . ' - C.P.: ' . $CodigoPostal);
$oPdf->Text(12, 7.25 + $yAlt, $oProvincia->Nombre);
$oPdf->Text(4, 7.6 + $yAlt, $oTipoIva->Nombre);
$oPdf->Text(11, 7.6 + $yAlt, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

/* datos del automotor */
$oPdf->Text(3, 9.5 + $yAlt, 'Marca:');
$oPdf->Text(5.5, 9.5 + $yAlt, $oMarcaVehiculo->Nombre);
$oPdf->Text(3, 10 + $yAlt, 'Tipo:');
$oPdf->Text(5.5, 10 + $yAlt, $oTipoModelo->Nombre);
$oPdf->Text(13, 10 + $yAlt, utf8_decode('Año: ' . $oUsado->ModeloAnio));
$oPdf->Text(3, 10.5 + $yAlt, 'Modelo:');
$oPdf->Text(5.5, 10.5 + $yAlt, $oUsado->Modelo);
$oPdf->Text(3, 11 + $yAlt, 'Nro. de Motor:');
$oPdf->Text(5.5, 11 + $yAlt, $oUsado->NumeroMotor);
$oPdf->Text(11, 11 + $yAlt, ' - ' . $oMarcaMotor->Nombre);
$oPdf->Text(3, 11.5 + $yAlt, 'Nro. de Chasis:');
$oPdf->Text(5.5, 11.5 + $yAlt, $oUsado->NumeroChasis);
$oPdf->Text(11, 11.5 + $yAlt, ' - ' . $oMarcaChasis->Nombre);
$oPdf->Text(3, 12 + $yAlt, 'Color:');
$oPdf->Text(5.5, 12 + $yAlt, $oColor->Nombre);
$oPdf->Text(13, 12 + $yAlt, $oColor->Codigo);
$oPdf->Text(14, 12 + $yAlt, 'Nro. Int: ' . $oUsado->IdUsado);
$oPdf->Text(3, 12.5 + $yAlt, 'Equipo:');
$oPdf->Text(5.5, 12.5 + $yAlt, $oUsado->Modelo);
if ($oFacturaUsado->Observaciones != '')
{
	$oPdf->Text(3, 14 + $yAlt, 'Observaciones:');
	$oPdf->Text(3, 14.5 + $yAlt, $oFacturaUsado->Observaciones);
}

$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';
$yAlt = -1.5;
/* importes */
$oPdf->Text(17, 12 + $yAlt, number_format($oFacturaUsado->Subtotal, 2));
$oPdf->Text(2.5, 25.5 + $yAlt, number_format($oFacturaUsado->Subtotal, 2));
$oPdf->Text(7.5, 25.5 + $yAlt, number_format($oFacturaUsado->ImpuestoInterno, 2));
$oPdf->Text(11, 25.5 + $yAlt, number_format($oFacturaUsado->Iva10 + $oFacturaUsado->Iva21, 2));
$oPdf->Text(17, 25.5 + $yAlt, number_format($oFacturaUsado->Total, 2));
$oPdf->Text(2.5, 27 + $yAlt, $oNumber->ValorEnLetras($oFacturaUsado->Total, "pesos") . $Delimitador);

/* generamos el archivo */
//$oPdf->Output('factura_a.pdf', 'D');
$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();

?>