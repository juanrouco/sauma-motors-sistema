<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$ImprimeLeyenda = intval($_REQUEST['ImprimeLeyenda']);
$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0;
$OffsetY += 0.1;

$oFormularios 		= new Formularios();
$oFacturaUnidades 	= new FacturaUnidades();
$oGestorias 		= new Gestorias();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposDocumento 	= new TiposDocumento();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oColores 			= new Colores();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();

/* obtenemos los datos del formulario */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();

/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
	exit();

/* obtenemos los datos de la factura */
$oFacturaUnidad = $oFacturaUnidades->GetById($oGestoria->IdMinuta);

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

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos del tipo de documento */
$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

$OffsetY = 0.4;

/* Identificacion del Automotor */
$oPdf->Text($OffsetX + 9, $OffsetY + 4.8 + 0.9, $oUnidad->Patente);
$oPdf->Text($OffsetX + 6.5, $OffsetY + 6 + 0.9, $oMarcaVehiculo->Nombre);
$oPdf->Text($OffsetX + 6.5, $OffsetY + 6.6 + 0.9, $oTipoModelo->Nombre);
$oPdf->Text($OffsetX + 7, $OffsetY + 7.2 + 0.9, $oModelo->DenominacionModelo);
$oPdf->Text($OffsetX + 8, $OffsetY + 7.9 + 0.9, $oMarcaMotor->Nombre);
$oPdf->Text($OffsetX + 8, $OffsetY + 8.5 + 0.9, $oUnidad->NumeroMotor);
$oPdf->Text($OffsetX + 9, $OffsetY + 9.2 + 0.9, $oMarcaChasis->Nombre);
$oPdf->Text($OffsetX + 8, $OffsetY + 9.8 + 0.9, $oUnidad->NumeroChasis);

$OffsetY+= 0.4;

/* Observaciones */
if ($ImprimeLeyenda)
{
	$oPdf->SetFont('Arial', '', 7);
	
	$oPdf->Text($OffsetX + 5.55, $OffsetY + 12.4 + 0.3, 'SIN OBSERVACIONES "HE VERIFICADO PERSONALMENTE LA AUTENTICIDAD');
	$oPdf->Text($OffsetX + 5.55, $OffsetY + 12.9 + 0.3, 'DE LOS DATOS DE ESTE FORMULARIO Y ME HAGO RESPONSABLE CIVIL');
	$oPdf->Text($OffsetX + 5.55, $OffsetY + 13.4 + 0.3, 'Y CRIMINALMENTE POR LOS ERRORES U OMISIONES EN QUE PUDIERA');
	$oPdf->Text($OffsetX + 5.55, $OffsetY + 13.9 + 0.3, 'INCURRIR SIN PERJUICIO DE LA QUE A LA EMPRESA LE CORRESPONDAN".');
}

$oPdf->SetFont('Arial', '', 8);

$oPdf->Text($OffsetX + 5, $OffsetY + 19.0 + 1.2, 'OLIVOS, BS. AS.');
//$oPdf->Text($OffsetX + 10.2, $OffsetY + 20.8, date("d", strtotime($oFacturaUnidad->Fecha)));
//$oPdf->Text($OffsetX + 11.2, $OffsetY + 20.8, date("m", strtotime($oFacturaUnidad->Fecha)));
//$oPdf->Text($OffsetX + 12.1, $OffsetY + 20.8, substr(date("Y", strtotime($oFacturaUnidad->Fecha)), 2, 2));
$oPdf->Text($OffsetX + 10.5, $OffsetY + 19.0 + 1.2, date("d"));
$oPdf->Text($OffsetX + 11.5, $OffsetY + 19.0 + 1.2, date("m"));
$oPdf->Text($OffsetX + 12.5, $OffsetY + 19.0 + 1.2, substr(date("Y"), 2, 2));

/* Identificacion del Solicitante */
$oPdf->Text($OffsetX + 10, $OffsetY + 22.4 + 0.4, $oCliente->RazonSocial);
if ($oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica)
	$oPdf->Text($OffsetX + 10.5, $OffsetY + 23 + 0.1, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ' ' . $oCliente->ClaveFiscalNumero);
else
	$oPdf->Text($OffsetX + 10.5, $OffsetY + 23.5 + 0.1, $oTipoDocumento->Codigo . ' ' . $oCliente->DocumentoNumero);
$oPdf->Text($OffsetX + 7, $OffsetY + 24.2, $oCliente->DomicilioCalle);
$oPdf->Text($OffsetX + 13, $OffsetY + 24.2, $oCliente->DomicilioNumero);
if (strlen($oLocalidad->Nombre . ', '  . $oCliente->GetPartido()) > 30)
	$oPdf->SetFont('Arial', '', 6);
$oPdf->Text($OffsetX + 15.0, $OffsetY + 24.2, $oLocalidad->Nombre . ', '  . $oCliente->GetPartido());
$oPdf->SetFont('Arial', '', 8);
//$oPdf->Text($OffsetX + 6.5, $OffsetY + 17.9, $oCliente->ClaveFiscalNumero);

/* generamos el archivo */
$oPdf->AutoPrint(true);
$oPdf->Output();

?>