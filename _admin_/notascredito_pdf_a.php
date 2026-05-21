<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdNotaCredito = intval($_REQUEST['IdNotaCredito']);

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
if (!$oNotaCredito = $oNotasCredito->GetById($IdNotaCredito))
	exit();

/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oNotaCredito->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);

/* obtenemos todos los detalles de la factura */
if (!$arrData = $oNotaCredito->GetAllDetalles())
	exit();

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$oPdf->Text(17.2, 3.8, CambiarFecha($oNotaCredito->Fecha));

/* datos del cliente */
$oPdf->Text(4, 5.8, $oCliente->RazonSocial);
$oPdf->Text(4, 6.6, $oCliente->GetDomicilio());
$oPdf->Text(12, 6.6, $oLocalidad->Nombre . ' - C.P.: ' . $oLocalidad->CodigoPostal);
$oPdf->Text(4, 7.3, $oTipoIva->Nombre);
$oPdf->Text(11, 7.3, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

/* datos de la facturacion */
$oPdf->Text(5, 9.5, $oNotaCredito->Comentarios);


$y=13;

$count = 1;
foreach ($arrData as $oNotaCreditoDetalle) 
{
	$Importe = $oNotaCreditoDetalle->Importe;
	
	if ($oNotaCreditoDetalle->IdIva == Iva::Iva21)
	{
		$Importe = ($Importe / 1.21);
	}
	else
	{
		$Importe = ($Importe / 1.105);
	}
	
	$oPdf->Text(2.5, $y, 'ITEM ' . $count);
	$oPdf->Text(5, $y, $oNotaCreditoDetalle->Detalle);
	$oPdf->Text(17.5, $y, number_format($Importe, 2));
	
	$y+=1;
	$count++;
}

$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';

/* importes */
$oPdf->Text(2.5, 23.5, number_format($oNotaCredito->Importe - $oNotaCredito->Iva10 - $oNotaCredito->Iva21, 2));
$oPdf->Text(11, 23.5, number_format($oNotaCredito->Iva10 + $oNotaCredito->Iva21, 2));
$oPdf->Text(17.5, 23.5, number_format($oNotaCredito->Importe, 2));
$oPdf->Text(2.5, 25, $oNumber->ValorEnLetras($oNotaCredito->Importe, "pesos") . $Delimitador);

/* generamos el archivo */
$oPdf->Output('nota_credito_a.pdf', 'D');

?>