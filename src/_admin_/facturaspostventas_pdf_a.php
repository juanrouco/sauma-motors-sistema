<?php 
require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFacturaPostVenta = intval($_REQUEST['IdFacturaPostVenta']);

$oComprobantes 			= new Comprobantes();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oColores 				= new Colores();
$oMarcas 				= new Marcas();
$oTiposModelo 			= new TiposModelo();
$oNumber				= new Number(); 
$oTiposDocumento 		= new TiposDocumento();
$oOrdenesTrabajoTareas 	= new OrdenesTrabajoTareas();
$oCompras 				= new Compras();
$oArticulos				= new Articulos();
$oFacturasPostVentas	= new FacturasPostVentas();	
$oNotasCredito			= new NotasCredito();

/* obtenemos los datos del comprobante de pago */
if (!$oFacturaPostVenta = $oFacturasPostVentas->GetById($IdFacturaPostVenta))
	exit();
	
/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante))
	exit();

$oItems = $oFacturaPostVenta->GetAllItems();
	
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

$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

if (!$oNotaCredito)
	$oPdf->Text(17.2, 3.3, CambiarFecha($oComprobante->Fecha));
else
	$oPdf->Text(17.2, 3.3, CambiarFecha($oNotaCredito->Fecha));

/* datos del cliente */
$oPdf->Text(4, 5.3, $oCliente->RazonSocial);
$oPdf->Text(4, 6.1, $oCliente->GetDomicilio());
$oPdf->Text(12, 6.1, $oLocalidad->Nombre . ' - C.P.: ' . $oLocalidad->CodigoPostal);
$oPdf->Text(12, 6.45, $oProvincia->Nombre);
$oPdf->Text(4, 6.8, $oTipoIva->Nombre);
$oPdf->Text(11, 6.8, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);


$y=9;

$count = 1;

foreach ($oItems as $oItem) 
{
	$oPdf->Text(2.5, $y, $oItem->Cantidad);
	$oPdf->Text(5, $y, $oItem->Descripcion);
	$oPdf->Text(17.5, $y, number_format($oItem->ImporteNeto, 2));

	$y+=1;
	$count++;
}

if ($oFacturaPostVenta->Comentarios)
{
	$Comentarios = chunk_split($oFacturaPostVenta->Comentarios, 100);
	$arrComentarios = explode("\r\n", $Comentarios);
	foreach($arrComentarios as $comentario)
	{
		$oPdf->Text(2.5, $y + 2, $comentario);
		$y+= 0.5;
	}
}
$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';

/* importes */
$oPdf->Text(2.5, 23.5, number_format(($oComprobante->Importe - $oComprobante->PercepcionIIBB) / 1.21, 2));
$oPdf->Text(11, 23.5, number_format($oComprobante->ImporteIva10 + $oComprobante->ImporteIva21, 2));
$oPdf->Text(15, 23.5, number_format($oComprobante->PercepcionIIBB, 2));
$oPdf->Text(17.5, 23.5, number_format($oComprobante->Importe, 2));
$oPdf->Text(2.5, 25, $oNumber->ValorEnLetras($oComprobante->Importe, "pesos") . $Delimitador);

/* generamos el archivo */
/* generamos el archivo */
$oPdf->AutoPrint(true, true);
/* generamos el archivo */
$oPdf->Output();

?>