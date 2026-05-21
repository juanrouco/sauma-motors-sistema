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
$OffsetY =- 1.7;

$oFormularios 		= new Formularios();
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
$oCategoriasModelo 	= new CategoriasModelo();
$oPartidos			= new Partidos();

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

/* obtenemos los datos de la categoria del modelo */
if (!$oCategoriaModelo = $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos del tipo de documento */
$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
$oLocalidadPostal = $oLocalidades->GetById($oCliente->DomicilioIdLocalidadPostal);

/* obtenemos los datos de la localidad fiscal */
$oLocalidadFiscal = $oLocalidades->GetById($oGestoria->DomicilioFiscalIdLocalidad);
$oPartidoFiscal = $oPartidos->GetById($oLocalidadFiscal->IdPartido);
$oPartidoPostal = $oPartidos->GetById($oLocalidadPostal->IdPartido);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio 		= $oClientes->GetById($oGestoria->IdClienteCondominio);
$oTipoDocumentoCondominio 	= $oTiposDocumento->GetById($oClienteCondominio->DocumentoTipo);
$oLocalidadCondominio 		= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);
$OffsetY += 1;

/* identificacion del automotor */
$oPdf->Text($OffsetX + 5, $OffsetY + 5.7, 'X');
$oPdf->Text($OffsetX + 5, $OffsetY + 11.9, $oMarcaVehiculo->Nombre);
$oPdf->Text($OffsetX + 10, $OffsetY + 11.9, $oModelo->DenominacionModelo);
$oPdf->Text($OffsetX + 5, $OffsetY + 13.4, (string)$oUnidad->Anio[2]);
$oPdf->Text($OffsetX + 5.2, $OffsetY + 13.4, (string)$oUnidad->Anio[3]);
$oPdf->Text($OffsetX + 6.2, $OffsetY + 13.4, $oTipoModelo->Codigo);
$oPdf->Text($OffsetX + 7.6, $OffsetY + 13.4, '1');
$oPdf->Text($OffsetX + 8.9, $OffsetY + 13.4, substr(Origen::GetById($oModelo->Origen), 0, 3));
for ($i=0; $i<strlen((string)$oModelo->Peso); $i++)
	$oPdf->Text($OffsetX + 10.3 + ($i*0.5), $OffsetY + 13.4, (string)$oModelo->Peso[$i]);
$oPdf->Text($OffsetX + 5, $OffsetY + 14.9, $oMarcaMotor->Nombre);
for ($i=0; $i<strlen($oUnidad->NumeroMotor); $i++)
	$oPdf->Text($OffsetX + 10.2 + ($i*0.35), $OffsetY + 14.9, $oUnidad->NumeroMotor[$i]);
$oPdf->Text($OffsetX + 17, $OffsetY + 14.9, CombustibleTipos::GetById($oModelo->IdTipoCombustible));

/* identificacion del titular */
$oPdf->Text($OffsetX + 5, $OffsetY + 16.8, $oCliente->RazonSocial);
for ($i=0; $i<strlen($oCliente->DocumentoNumero); $i++)
	$oPdf->Text($OffsetX + 14.4 + ($i*0.39), $OffsetY + 16.8, $oCliente->DocumentoNumero[$i]);
$oPdf->Text($OffsetX + 18.8, $OffsetY + 16.8, $oTipoDocumento->Codigo);
$oPdf->Text($OffsetX + 14.4, $OffsetY + 17.5, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);
for ($i=0; $i<strlen($oLocalidadFiscal->CodigoPostal); $i++)
	$oPdf->Text($OffsetX + 11.2 + ($i*0.5), $OffsetY + 19, $oLocalidadFiscal->CodigoPostal[$i]);
$oPdf->Text($OffsetX + 14.5, $OffsetY + 18.7, $oLocalidadFiscal->Nombre);
$oPdf->Text($OffsetX + 14.5, $OffsetY + 19, $oPartidoFiscal->Nombre);
$oPdf->Text($OffsetX + 5, $OffsetY + 20.4, $oGestoria->DomicilioFiscalCalle);
for ($i=0; $i<strlen($oGestoria->DomicilioFiscalNumero); $i++)
	$oPdf->Text($OffsetX + 14 + ($i*0.45), $OffsetY + 20.4, $oGestoria->DomicilioFiscalNumero[$i]);
for ($i=0; $i<strlen($oGestoria->DomicilioFiscalPiso); $i++)
	$oPdf->Text($OffsetX + 15.8 + ($i*0.7), $OffsetY + 20.4, $oGestoria->DomicilioFiscalPiso[$i]);
for ($i=0; $i<strlen($oGestoria->DomicilioFiscalDpto); $i++)
	$oPdf->Text($OffsetX + 17.5 + ($i*0.6), $OffsetY + 20.4, $oGestoria->DomicilioFiscalDpto[$i]);
	
for ($i=0; $i<strlen($oLocalidadPostal->CodigoPostal); $i++)
	$oPdf->Text($OffsetX + 11.2 + ($i*0.5), $OffsetY + 22.3, $oLocalidadPostal->CodigoPostal[$i]);
$oPdf->Text($OffsetX + 14.5, $OffsetY + 18.7 + 3.3, $oLocalidadPostal->Nombre);
$oPdf->Text($OffsetX + 14.5, $OffsetY + 19 + 3.3, $oPartidoPostal->Nombre);
$oPdf->Text($OffsetX + 5, $OffsetY + 20.4 + 3.3, $oCliente->DomicilioCallePostal);
for ($i=0; $i<strlen($oCliente->DomicilioNumeroPostal); $i++)
	$oPdf->Text($OffsetX + 14 + ($i*0.45), $OffsetY + 20.4 + 3.3, $oCliente->DomicilioNumeroPostal[$i]);
for ($i=0; $i<strlen($oCliente->DomicilioPisoPostal); $i++)
	$oPdf->Text($OffsetX + 15.8 + ($i*0.7), $OffsetY + 20.4 + 3.3, $oCliente->DomicilioPisoPostal[$i]);
for ($i=0; $i<strlen($oCliente->DomicilioDptoPostal); $i++)
	$oPdf->Text($OffsetX + 17.5 + ($i*0.6), $OffsetY + 20.4 + 3.3, $oCliente->DomicilioDptoPostal[$i]);
/*for ($i=0; $i<strlen($oLocalidadPostal->CodigoPostal); $i++)
	$oPdf->Text($OffsetX + 10.9 + ($i*0.6), $OffsetY + 22.1, $oLocalidadPostal->CodigoPostal[$i]);
$oPdf->Text($OffsetX + 15, $OffsetY + 22.1, $oLocalidad->Nombre);
$oPdf->Text($OffsetX + 5, $OffsetY + 23.5, $oCliente->DomicilioCallePostal);
for ($i=0; $i<strlen($oCliente->DomicilioNumeroPostal); $i++)
	$oPdf->Text($OffsetX + 14 + ($i*0.45), $OffsetY + 23.5, $oCliente->DomicilioNumeroPostal[$i]);
for ($i=0; $i<strlen($oCliente->DomicilioPisoPostal); $i++)
	$oPdf->Text($OffsetX + 15.8 + ($i*0.7), $OffsetY + 23.5, $oCliente->DomicilioPisoPostal[$i]);
for ($i=0; $i<strlen($oCliente->DomicilioDptoPostal); $i++)
	$oPdf->Text($OffsetX + 17.5 + ($i*0.6), $OffsetY + 23.5, $oCliente->DomicilioDptoPostal[$i]);*/
if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
{
	$oPdf->Text($OffsetX + 5, $OffsetY + 25.7, $oClienteCondominio->RazonSocial);
	for ($i=0; $i<strlen($oClienteCondominio->DocumentoNumero); $i++)
		$oPdf->Text($OffsetX + 5 + ($i*0.39), $OffsetY + 27.3, $oClienteCondominio->DocumentoNumero[$i]);
	$oPdf->Text($OffsetX + 9, $OffsetY + 27.3, $oTipoDocumentoCondominio->Codigo);
	$oPdf->Text($OffsetX + 5, $OffsetY + 28, ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero);
	if ($oClienteCondominio->DomicilioCodigoPostal)
	{
		for ($i=0; $i<strlen($oClienteCondominio->DomicilioCodigoPostal); $i++)
			$oPdf->Text($OffsetX + 11.1 + ($i*0.6), $OffsetY + 27.3, $oClienteCondominio->DomicilioCodigoPostal[$i]);
	}
	else
	{
		for ($i=0; $i<strlen($oLocalidadCondominio->CodigoPostal); $i++)
			$oPdf->Text($OffsetX + 11.1 + ($i*0.6), $OffsetY + 27.3, $oLocalidadCondominio->CodigoPostal[$i]);
	}
	$oPdf->Text($OffsetX + 14.2, $OffsetY + 27.3, $oLocalidadCondominio->Nombre);
	$oPdf->Text($OffsetX + 5, $OffsetY + 28.5, $oClienteCondominio->DomicilioCalle);
	for ($i=0; $i<strlen($oClienteCondominio->DomicilioNumero); $i++)
		$oPdf->Text($OffsetX + 14.2 + ($i*0.45), $OffsetY + 28.5, $oClienteCondominio->DomicilioNumero[$i]);
	for ($i=0; $i<strlen($oClienteCondominio->DomicilioPiso); $i++)
		$oPdf->Text($OffsetX + 15.8 + ($i*0.7), $OffsetY + 28.5, $oClienteCondominio->DomicilioPiso[$i]);
	
}

/* generamos el archivo */
//$oPdf->Output('formulario_13a_provincia.pdf', 'D');
$oPdf->AutoPrint(true);
$oPdf->Output();

?>