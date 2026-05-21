<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdPago = intval($_REQUEST['IdPago']);

$oPagos			 	= new Pagos();
$oMinutas 			= new MinutasUsados();
$oClientes 			= new Clientes();
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
$oTiposIva 			= new TiposIva();

/* obtenemos los datos de la factura */
if (!$oPago = $oPagos->GetById($IdPago))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oPago->IdMinutaUsado))
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

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$yAlt = -0.5;

$oPdf->Image('images/logo_tolosa.jpg', 2.5, 2);
$oPdf->Text(4, 4.5 + $yAlt, 'Av. Del Libertador 14099 - Martinez - Buenos Aires (B)');

$oPdf->Text(17.2, 3.8 + $yAlt, CambiarFecha($oPago->Fecha));

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
	if ($oFacturaUnidad->OtrosTitulares != '')
	{
		$oPdf->Text(4, 6.1 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oFacturaUnidad->OtrosTitulares);
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
$oPdf->Text(3, 9.5 + $yAlt, 'Recibimos la suma de: $ ' . number_format($oPago->Importe, 2, ',', '.'));
$oPdf->Text(3, 10 + $yAlt, 'En concepto de pago de usado: ' . $oUsado->Dominio);
$oPdf->Text(3, 10.5 + $yAlt, 'En ' . TipoPago::GetById($oPago->IdTipoPago) . ($oPago->IdTipoPago == TipoPago::Cheque ? ' N: ' . $oPago->NumeroCheque . ' c/Banco ' . $oPago->BancoDesde : ''));
$oPdf->Text(3, 11.5 + $yAlt,iconv('UTF-8', 'windows-1252', 'Observaciones: ' . $oPago->Observaciones));

$yAlt+= 12;


$oPdf->Image('images/logo_tolosa.jpg', 2.5, 2);
$oPdf->Text(4, 4.5 + $yAlt, 'Av. Del Libertador 2275 - Buenos Aires (B)');

$oPdf->Text(17.2, 3.8 + $yAlt, CambiarFecha($oPago->Fecha));

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
	if ($oFacturaUnidad->OtrosTitulares != '')
	{
		$oPdf->Text(4, 6.1 + $yAlt, $oCliente->RazonSocial . ' Y ' . $oFacturaUnidad->OtrosTitulares);
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
$oPdf->Text(3, 9.5 + $yAlt, 'Recibimos la suma de: $ ' . number_format($oPago->Importe, 2, ',', '.'));
$oPdf->Text(3, 10 + $yAlt, 'En concepto de pago de usado: ' . $oUsado->Dominio);
$oPdf->Text(3, 10.5 + $yAlt, 'En ' . TipoPago::GetById($oPago->IdTipoPago) . ($oPago->IdTipoPago == TipoPago::Cheque ? ' N: ' . $oPago->NumeroCheque . ' c/Banco ' . $oPago->BancoDesde : ''));
$oPdf->Text(3, 11.5 + $yAlt,iconv('UTF-8', 'windows-1252', 'Observaciones: ' . $oPago->Observaciones));

/* generamos el archivo */
$oPdf->Output('recibo pago.pdf', 'D');
//$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();

?>