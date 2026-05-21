<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCompra = intval($_REQUEST['IdCompra']);

$oCompras 			= new Compras();
$oComprobantes 		= new Comprobantes();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
$oNumber			= new Number();
$oArticulos			= new Articulos();

if (!$oCompra = $oCompras->GetById($IdCompra))
	exit();
$oCompra->LoadAllDetalles();

if (!$oComprobante = $oComprobantes->GetById($oCompra->IdFactura))
	exit();

if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
	exit();

if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$oPdf->Text(17, 3.8, CambiarFecha($oCompra->FechaCarga));
$oPdf->Text(4, 5.8, $oCliente->RazonSocial);
$oPdf->Text(4, 6.6, $oCliente->GetDomicilio());
$oPdf->Text(12, 6.6, $oLocalidad->Nombre . ' - C.P.: ' . $oLocalidad->CodigoPostal);
$oPdf->Text(4, 7.3, $oTipoIva->Nombre);
$oPdf->Text(11, 7.3, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

$count = 0;
foreach ($oCompra->CompraDetalles as $oCompraDetalle)
{
	$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
	$deltaY = $count * 2;	
	$oPdf->Text(4, 9.5 + $deltaY, $oArticulo->Codigo);
	$oPdf->Text(6.5, 9.5 + $deltaY, $oArticulo->Descripcion);
	$oPdf->Text(10, 10 + $deltaY, $oCompraDetalle->Cantidad);
	$oPdf->Text(13, 10 + $deltaY, $oCompraDetalle->ImporteUnidad);
	$oPdf->Text(17.5, 10 + $deltaY, $oCompraDetalle->ImporteCompraNeto);
}

$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';

/* importes */
$oPdf->Text(17.5, 12, number_format($oCompra->GetSubtotal(), 2));
$oPdf->Text(2.5, 25.5, number_format($oCompra->GetSubtotal(), 2));
$oPdf->Text(11, 25.5, number_format($oCompra->GetSubtotalIva(1) + $oCompra->GetSubtotalIva(2), 2));
$oPdf->Text(17.5, 25.5, number_format($oCompra->Total(), 2));
$oPdf->Text(2.5, 27, $oNumber->ValorEnLetras($oCompra->Total(), "pesos") . $Delimitador);

/* generamos el archivo */
$oPdf->Output('factura_a.pdf', 'D');

?>