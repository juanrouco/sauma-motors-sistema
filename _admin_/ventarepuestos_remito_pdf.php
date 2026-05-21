<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCompra = intval($_REQUEST['IdCompra']);

$oCompras				= new Compras();
$oComprobantes 			= new Comprobantes();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oArticulos				= new Articulos();
$oTallerUnidades		= new TallerUnidades();

if (!$oCompra = $oCompras->GetById($IdCompra))
	exit();
$oCompra->LoadAllDetalles();

if (!$oComprobante = $oComprobantes->GetById($oCompra->IdRemito))
	exit();

if ($oCompra->IdCliente)
{
	if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
	{	
		header("Location: ventarepuestos.php" . $strParams);
		exit();
	}
}
else
{
	$oTallerUnidad = $oTallerUnidades->GetById($oCompra->IdTallerUnidad);
	if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
	{	
		header("Location: ventarepuestos.php" . $strParams);
		exit();
	}
}

if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$oPdf->Text(17.5, 3.5, CambiarFecha($oCompra->FechaCarga));

$oPdf->Text(4, 5.8, $oCliente->RazonSocial);
$oPdf->Text(4, 6.6, $oCliente->GetDomicilio());
$oPdf->Text(12, 6.6, $oLocalidad->Nombre . ' - C.P.: ' . $oLocalidad->CodigoPostal);
$oPdf->Text(4, 7.3, $oTipoIva->Nombre);
$oPdf->Text(11, 7.3, $oCliente->ClaveFiscalNumero);

$count = 0;
foreach ($oCompra->CompraDetalles as $oCompraDetalle)
{
	$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
	$deltaY = $count;	
	$oPdf->Text(4, 9 + $deltaY, $oCompraDetalle->Cantidad);
	$oPdf->Text(6.5, 9 + $deltaY, $oArticulo->Codigo . ' - ' . $oArticulo->Descripcion);
	$count++;
}

if ($oCompra->Transporte)
{
	$oPdf->Text(4, 9 + $deltaY + 5, 'TRANSPORTE: ' . $oCompra->Transporte . ' ' . ClaveFiscalTipos::GetById($oCompra->TransporteClaveFiscalTipo) . ' ' . $oCompra->TransporteClaveFiscalNumero);
}

/* generamos el archivo */
$oPdf->Output('remito.pdf', 'D');

?>