<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCajaMovimiento = intval($_REQUEST['IdCajaMovimiento']);

$oCajasMovimientos	= new CajasMovimientos();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
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
$oTiposIva 			= new TiposIva();
$oCajasDetalles 	= new CajasDetalles();
$oUsuarios		 	= new Usuarios();

/* obtenemos los datos de la factura */
if (!$oCajaMovimiento = $oCajasMovimientos->GetById($IdCajaMovimiento))
	exit();

$oCajaOrigen = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
$oCajaDestino = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDestino);


/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 12);

$yAlt = -0.5;

$oPdf->Image('images/logo_tolosa.jpg', 2.5, 2);
$oPdf->Text(2.5, 4.7 + $yAlt, 'Action Motosports S.R.L / Av. Del Libertador 2275 - 1636 Olivos (B)');

$oPdf->Text(17.2, 5 + $yAlt, CambiarFecha($oCajaMovimiento->Fecha));
$txt = '';
if ($oCajaMovimiento->IdConcepto == 7)
{
	$oUsuario = $oUsuarios->GetById($oCajaMovimiento->IdUsuario);
	$txt = '  ' . $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
}
/* datos del automotor */
if ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja)
	$oPdf->Text(2, 7 + $yAlt, 'Transferencia por la suma de: $ ' . number_format(abs($oCajaMovimiento->Total), 2, ',', '.') . ' desde ' . $oCajaOrigen->Nombre . ' a ' . $oCajaDestino->Nombre);
else
	$oPdf->Text(2, 7 + $yAlt, 'Recibi de Action Motorsports S.R.L. la suma de: $ ' . number_format(abs($oCajaMovimiento->Total), 2, ',', '.') . '. En concepto de ' . ConceptosCajas::GetById($oCajaMovimiento->IdConcepto) . $txt . '.');
$oPdf->Text(2, 7.5 + $yAlt,  $oCajaMovimiento->Comentarios);
$oPdf->Text(13.5, 12 + $yAlt,  '------------------------------');
$oPdf->Text(15, 12.5 + $yAlt,  'Recibe');

$yAlt+= 12;


$oPdf->Image('images/logo_tolosa.jpg', 2.5, 14);
$oPdf->Text(2.5, 4.7 + $yAlt, 'Action Motosports S.R.L. / Av. Del Liberador 2275 - 1636 Olivos (B)');

$oPdf->Text(17.2, 5 + $yAlt, CambiarFecha($oCajaMovimiento->Fecha));


/* datos del automotor */
if ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja)
	$oPdf->Text(2, 7 + $yAlt, 'Transferencia por la suma de: $ ' . number_format(abs($oCajaMovimiento->Total), 2, ',', '.') . ' desde ' . $oCajaOrigen->Nombre . ' a ' . $oCajaDestino->Nombre);
else
	$oPdf->Text(2, 7 + $yAlt, 'Recibi de Action Motosports S.R.L. la suma de: $ ' . number_format(abs($oCajaMovimiento->Total), 2, ',', '.') . '. En concepto de ' . ConceptosCajas::GetById($oCajaMovimiento->IdConcepto) . $txt . '.');
$oPdf->Text(2, 7.5 + $yAlt, $oCajaMovimiento->Comentarios);
$oPdf->Text(13.5, 12 + $yAlt,  '------------------------------');
$oPdf->Text(15, 12.5 + $yAlt,  'Recibe');


/* generamos el archivo */
$oPdf->Output('recibo.pdf', 'D');
//$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();

?>