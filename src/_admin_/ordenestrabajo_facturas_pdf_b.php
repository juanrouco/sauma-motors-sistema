<?php

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFacturaPostVenta = intval($_REQUEST['IdFacturaPostVenta']);

$oOrdenesTrabajo 		= new OrdenesTrabajo();
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
/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$oPdf->Text(17.2, 3.8, CambiarFecha($oComprobante->Fecha));

/* datos del cliente */
$oPdf->Text(4, 5.8, $oCliente->RazonSocial);
$oPdf->Text(4, 6.6, $oCliente->GetDomicilio());
$oPdf->Text(12, 6.6, $oLocalidad->Nombre . ' - C.P.: ' . $oLocalidad->CodigoPostal);
$oPdf->Text(12, 6.95, $oProvincia->Nombre);
$oPdf->Text(4, 7.3, $oTipoIva->Nombre);
$oPdf->Text(11, 7.3, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);


$y=13;

$count = 1;
foreach ($oItems as $oItem) 
{
	$oPdf->Text(2.5, $y, $oItem->Cantidad);
	$oPdf->Text(5, $y, $oItem->Descripcion);
	$oPdf->Text(17.5, $y, number_format($oItem->ImporteBruto, 2));

	$y+=1;
	$count++;
}
$Delimitador = '';
for ($i=0; $i<=20; $i++)
	$Delimitador.= '-';

/* importes */
$oPdf->Text(17.5, 23.5, number_format($oComprobante->Importe, 2));
$oPdf->Text(2.5, 25, $oNumber->ValorEnLetras($oComprobante->Importe, "pesos") . $Delimitador);

/* generamos el archivo */
$oPdf->Output('factura_b.pdf', 'D');

?>