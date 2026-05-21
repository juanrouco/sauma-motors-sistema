<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0;

$oFormularios 	= new Formularios();
$oGestorias 	= new Gestorias();
$oMinutas 		= new Minutas();
$oClientes 		= new Clientes();
$oUnidades 		= new Unidades();
$oModelos 		= new Modelos();
$oLocalidades 	= new Localidades();
$oColores 		= new Colores();
$oMarcas 		= new Marcas();
$oTiposModelo 	= new TiposModelo();

/* obtenemos los datos del formulario */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();

/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
	exit();

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

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos de la localidad fiscal */
$oLocalidadFiscal = $oLocalidades->GetById($oGestoria->DomicilioFiscalIdLocalidad);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

for ($i = 0; $i < 3; $i++)
{
	$oPdf->AddPage();

	$oPdf->SetFont('Arial', '', 8);

	/* Documento Titutlar */
	for ($i=0; $i<strlen($oCliente->DocumentoNumero); $i++)
		$oPdf->Text($OffsetX + 4.2 + ($i*0.5), $OffsetY + 5.4, $oCliente->DocumentoNumero[$i]);
	if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
		$oPdf->Text($OffsetX + 10.8, $OffsetY + 4.8, 'X');
	if ($oCliente->DocumentoTipo == TipoDocumento::LE)
		$oPdf->Text($OffsetX + 12.3, $OffsetY + 4.8, 'X');
	if ($oCliente->DocumentoTipo == TipoDocumento::LC)
		$oPdf->Text($OffsetX + 14,$OffsetY +  4.8, 'X');
	if ($oCliente->DocumentoTipo == TipoDocumento::CI)
		$oPdf->Text($OffsetX + 15.5, $OffsetY + 4.8, 'X');
	if ($oCliente->DocumentoTipo == TipoDocumento::PA)
		$oPdf->Text($OffsetX + 17.1, $OffsetY + 4.8, 'X');
	for ($i=0; $i<strlen($oCliente->ClaveFiscalNumero); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.5), $OffsetY + 7.1, $oCliente->ClaveFiscalNumero[$i]);

	/* Identificacion del Titular */
	for ($i=0; $i<strlen($oCliente->RazonSocial); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.3), $OffsetY + 11.2, $oCliente->RazonSocial[$i]);
	for ($i=0; $i<strlen($oGestoria->DomicilioFiscalCalle); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.3), $OffsetY + 13.8, $oGestoria->DomicilioFiscalCalle[$i]);
	for ($i=0; $i<strlen($oGestoria->DomicilioFiscalNumero); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.5), $OffsetY + 15.1, $oGestoria->DomicilioFiscalNumero[$i]);
	for ($i=0; $i<strlen($oGestoria->DomicilioFiscalPiso); $i++)
		$oPdf->Text($OffsetX + 9.2 + ($i*0.5), $OffsetY + 15.1, $oGestoria->DomicilioFiscalPiso[$i]);
	for ($i=0; $i<strlen($oGestoria->DomicilioFiscalDpto); $i++)
		$oPdf->Text($OffsetX + 12.2 + ($i*0.5), $OffsetY + 15.1, $oGestoria->DomicilioFiscalDpto[$i]);
		
	if ($oCliente->DomicilioCodigoPostal)
	{
		for ($i=0; $i<strlen($oCliente->DomicilioCodigoPostal); $i++)
			$oPdf->Text($OffsetX + 17.7 + ($i*0.5), $OffsetY + 15.1, $oCliente->DomicilioCodigoPostal[$i]);
			
	}
	else
	{
		for ($i=0; $i<strlen($oGestoria->DomicilioFiscalCodigoPostal); $i++)
		$oPdf->Text($OffsetX + 17.7 + ($i*0.5), $OffsetY + 15.1, $oGestoria->DomicilioFiscalCodigoPostal[$i]);
	}
		

	for ($i=0; $i<strlen($oCliente->DomicilioCalle); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.3), $OffsetY + 18, $oCliente->DomicilioCalle[$i]);
	for ($i=0; $i<strlen($oCliente->DomicilioNumero); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.5), $OffsetY + 19.4, $oCliente->DomicilioNumero[$i]);
	for ($i=0; $i<strlen($oCliente->DomicilioPiso); $i++)
		$oPdf->Text($OffsetX + 9.2 + ($i*0.5), $OffsetY + 19.4, $oCliente->DomicilioPiso[$i]);
	for ($i=0; $i<strlen($oCliente->DomicilioDpto); $i++)
		$oPdf->Text($OffsetX + 12.2 + ($i*0.5), $OffsetY + 19.4, $oCliente->DomicilioDpto[$i]);
	if ($oCliente->DomicilioCodigoPostal)
	{
		for ($i=0; $i<strlen($oCliente->DomicilioCodigoPostal); $i++)
			$oPdf->Text($OffsetX + 17.7 + ($i*0.5), $OffsetY + 19.4, $oCliente->DomicilioCodigoPostal[$i]);
	}
	else
	{
		for ($i=0; $i<strlen($oLocalidad->CodigoPostal); $i++)
			$oPdf->Text($OffsetX + 17.7 + ($i*0.5), $OffsetY + 19.4, $oLocalidad->CodigoPostal[$i]);
	}

	/* Identificacion del automotor */
	for ($i=0; $i<strlen($oMarcaVehiculo->Nombre); $i++)
		$oPdf->Text($OffsetX + 4.4 + ($i*0.5), $OffsetY + 26.2, $oMarcaVehiculo->Nombre[$i]);
	$oPdf->Text($OffsetX + 4.4, $OffsetY + 27.6, (string)$oUnidad->Anio[2]);
	$oPdf->Text($OffsetX + 4.9, $OffsetY + 27.6, (string)$oUnidad->Anio[3]);
	for ($i=0; $i<strlen((string)$oModelo->Peso); $i++)
		$oPdf->Text($OffsetX + 9.4 + ($i*0.5), $OffsetY + 27.6, (string)$oModelo->Peso[$i]);
	for ($i=0; $i<strlen($oUnidad->NumeroMotor); $i++)
		$oPdf->Text($OffsetX + 11.4 + ($i*0.5), $OffsetY + 27.6, $oUnidad->NumeroMotor[$i]);
}
/* generamos el archivo */
//$oPdf->Output('formulario_13a_capital.pdf', 'D');
$oPdf->AutoPrint(true);
$oPdf->Output();


?>