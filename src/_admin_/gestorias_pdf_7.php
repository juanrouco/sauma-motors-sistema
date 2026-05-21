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
$OffsetY -= 0.4;

$oFormularios 		= new Formularios();
$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oGestorias 		= new Gestorias();
$oPrendas 			= new Prendas();
$oAcreedores 		= new Acreedores();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oProfesiones 		= new Profesiones();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
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

/* obtenemos los datos del comprobante de pago */
$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante);

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

/* obtenemos los datos de la prenda */
if (!$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
	exit();

/* obtenemos los datos del acreedor prendario */
if (!$oAcreedor = $oAcreedores->GetById($oPrenda->IdAcreedor))
	exit();

/* obtenemos la nacionalidad del acreedor */
$oNacionalidadAcreedor = $oPaises->GetById($oAcreedor->IdNacionalidad);

/* obtenemos los datos de la localidad del acreedor */
$oLocalidadAcreedor = $oLocalidades->GetById($oAcreedor->DomicilioIdLocalidad);

/* obtenemos los datos del partido del acreedor */
$oPartidoAcreedor = $oPartidos->GetById($oLocalidadAcreedor->IdPartido);

/* obtenemos los datos de la provincia del acreedor */
$oProvinciaAcreedor = $oProvincias->GetById($oLocalidadAcreedor->IdProvincia);

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos de la prfesion del cliente */
$oProfesion = $oProfesiones->GetById($oCliente->IdProfesion);

/* obtenemos la nacionalidad */
$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio 			= $oClientes->GetById($oGestoria->IdClienteCondominio);
$oNacionalidadCondominio 		= $oPaises->GetById($oClienteCondominio->IdNacionalidad);
$oProfesionCondominio 			= $oProfesiones->GetById($oClienteCondominio->IdProfesion);
$oLocalidadCondominio 			= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);
$oPartidoCondominio 			= $oPartidos->GetById($oLocalidadCondominio->IdPartido);
$oProvinciaCondominio 			= $oProvincias->GetById($oLocalidadCondominio->IdProvincia);

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

/* identificacion de la prenda */
$OffsetXDate = $OffsetX + 5.3;
$OffsetYDate = $OffsetY + 4.2;
$oPdf->Text($OffsetXDate, $OffsetYDate, date("d", strtotime($oGestoria->FechaGestion)));
$oPdf->Text($OffsetXDate + 0.8, $OffsetYDate, date("m", strtotime($oGestoria->FechaGestion)));
$oPdf->Text($OffsetXDate + 1.7, $OffsetYDate, substr(date("Y", strtotime($oGestoria->FechaGestion)), 2, 2));

$oPdf->Text($OffsetX + 5.5, $OffsetY + 5.1, '$ ' . number_format($oPrenda->FinanciacionCapital, 2, ',', ''));
$OffsetY += 0.2;
/* identificacion del acreedor */
$oPdf->Text($OffsetX + 7.7, $OffsetY + 6.6, $oAcreedor->NumeroInscripcion);

$OffsetYRazonSocial = $OffsetY + 7.3;
if (strlen($oAcreedor->RazonSocial) > 30)
	$oPdf->SetFont('Arial', '', 6);
	
$oPdf->Text($OffsetX + 4.7, $OffsetYRazonSocial, $oAcreedor->RazonSocial);

$telefono = '';

if ($oAcreedor->Telefono)
{
	if ($oAcreedor->TelefonoCodigoArea)
		$telefono = $oAcreedor->TelefonoCodigoArea . '-';
	$telefono = $oAcreedor->Telefono;
	
}
$oPdf->SetFont('Arial', '', 8);
if ($oAcreedor->Email)
{
	if ($telefono != '')
		$telefono.= ' / ' . $oAcreedor->Email;
}
$oPdf->Text($OffsetX + 4.7, $OffsetYRazonSocial + 0.45, $telefono);


$oPdf->Text($OffsetX + 12.5, $OffsetYRazonSocial, $oCliente->RazonSocial);
if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 8.2, 'Y OTRO');
$OffsetYClaveFiscal = $OffsetY + 8.7;
$oPdf->Text($OffsetX + 4.7, $OffsetYClaveFiscal, ClaveFiscalTipos::GetById($oAcreedor->ClaveFiscalTipo) . ': ' . $oAcreedor->ClaveFiscalNumero);
$oPdf->Text($OffsetX + 12.5, $OffsetYClaveFiscal, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);
$oPdf->Text($OffsetX + 16.5, $OffsetYClaveFiscal, $oProfesion->Nombre);

$OffsetYDomicilioCalle = $OffsetY + 9.5;
$oPdf->Text($OffsetX + 4.7, $OffsetYDomicilioCalle, $oAcreedor->DomicilioCalle);
$oPdf->Text($OffsetX + 12.5, $OffsetYDomicilioCalle, $oCliente->DomicilioCalle);

$OffsetYDomicilioNumero = $OffsetY + 10.4;
$oPdf->Text($OffsetX + 4.7, $OffsetYDomicilioNumero, $oAcreedor->DomicilioNumero);
$oPdf->Text($OffsetX + 6.7, $OffsetYDomicilioNumero, $oAcreedor->DomicilioPiso);
$oPdf->Text($OffsetX + 8.3, $OffsetYDomicilioNumero, $oAcreedor->DomicilioDpto);
if ($oAcreedor->DomicilioCodigoPostal)
	$oPdf->Text($OffsetX + 10, $OffsetYDomicilioNumero, $oAcreedor->DomicilioCodigoPostal);
else
	$oPdf->Text($OffsetX + 10, $OffsetYDomicilioNumero, $oLocalidadAcreedor->CodigoPostal);
	
$oPdf->Text($OffsetX + 12.5, $OffsetYDomicilioNumero, $oCliente->DomicilioNumero);
$oPdf->Text($OffsetX + 14.5, $OffsetYDomicilioNumero, $oCliente->DomicilioPiso);
$oPdf->Text($OffsetX + 16, $OffsetYDomicilioNumero, $oCliente->DomicilioDpto);
if ($oCliente->DomicilioCodigoPostal)
	$oPdf->Text($OffsetX + 17.8, $OffsetYDomicilioNumero, $oCliente->DomicilioCodigoPostal);
else
	$oPdf->Text($OffsetX + 17.8, $OffsetYDomicilioNumero, $oLocalidad->CodigoPostal);

$OffsetYLocalidad = $OffsetY + 11.3;
if ($oAcreedor->IdAcreedor != 3 && $oAcreedor->IdAcreedor != 4)
	$oPdf->Text($OffsetX + 4.7, $OffsetYLocalidad, $oLocalidadAcreedor->Nombre);
else
	$oPdf->Text($OffsetX + 4.7, $OffsetYLocalidad, "CAPITAL FEDERAL");
$oPdf->Text($OffsetX + 12.5, $OffsetYLocalidad, $oLocalidad->Nombre);

$OffsetYPartido = $OffsetY + 12.1;
if ($oAcreedor->IdAcreedor != 3 && $oAcreedor->IdAcreedor != 4)
	$oPdf->Text($OffsetX + 4.7, $OffsetYPartido, $oPartidoAcreedor->Nombre);
else
	$oPdf->Text($OffsetX + 4.7, $OffsetYPartido, 'CAPITAL FEDERAL');
if ($oAcreedor->IdAcreedor != 3 && $oAcreedor->IdAcreedor != 4)
	$oPdf->Text($OffsetX + 8.8, $OffsetYPartido, $oProvinciaAcreedor->Nombre);
else
	$oPdf->Text($OffsetX + 8.8, $OffsetYPartido, 'BUENOS AIRES');

if ($oPartido->Nombre != 'CIUDAD AUTONOMA DE BS AS')
	$oPdf->Text($OffsetX + 12.5, $OffsetYPartido, $oPartido->Nombre);
else
	$oPdf->Text($OffsetX + 12.5, $OffsetYPartido, 'CABA');
if ($oProvincia->Nombre != 'CIUDAD AUTONOMA DE BS AS')
	$oPdf->Text($OffsetX + 16.9, $OffsetYPartido, $oProvincia->Nombre);
else
	$oPdf->Text($OffsetX + 16.9, $OffsetYPartido, 'BUENOS AIRES');
	
	
/* personas fisicas */
if ($oAcreedor->IdTipoPersona == PersonaTipos::PersonaFisica)
{
	if ($oNacionalidadAcreedor->Current)
	{
		if ($oAcreedor->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 4.9, $OffsetY + 13.8, 'X');
		if ($oAcreedor->DocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 5.9, $OffsetY + 13.8, 'X');
		if ($oAcreedor->DocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 6.9, $OffsetY + 13.8, 'X');
	}
	else
	{
		if ($oAcreedor->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 8.7, $OffsetY + 13.8, 'X');
		if ($oAcreedor->DocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 9.7, $OffsetY + 13.8, 'X');
		if ($oAcreedor->DocumentoTipo == TipoDocumento::PA)
			$oPdf->Text($OffsetX + 10.7, $OffsetY + 13.8, 'X');
	}
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 14.6, $oAcreedor->DocumentoNumero);
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 14.6, $oAcreedor->DocumentoExpedido);
	$oPdf->Text($OffsetX + 4.4, $OffsetY + 16.3, date("d", strtotime($oAcreedor->FechaNacimiento)));
	$oPdf->Text($OffsetX + 5.3, $OffsetY + 16.3, date("m", strtotime($oAcreedor->FechaNacimiento)));
	$oPdf->Text($OffsetX + 6.2, $OffsetY + 16.3, substr(date("Y", strtotime($oAcreedor->FechaNacimiento)), 2, 2));
	if ($oAcreedor->IdEstadoCivil == EstadoCivil::Soltero)
		$oPdf->Text($OffsetX + 7.2, $OffsetY + 16.3, 'X');
	elseif ($oAcreedor->IdEstadoCivil == EstadoCivil::Casado)
		$oPdf->Text($OffsetX + 8.2, $OffsetY + 16.3, 'X');
	elseif ($oAcreedor->IdEstadoCivil == EstadoCivil::Viudo)
		$oPdf->Text($OffsetX + 9.2, $OffsetY + 16.3, 'X');
	elseif ($oAcreedor->IdEstadoCivil == EstadoCivil::Divorciado)
		$oPdf->Text($OffsetX + 10.2, $OffsetY + 16.3, 'X');
	$oPdf->Text($OffsetX + 11.2, $OffsetY + 16.3, $oAcreedor->Nupcia);
}
elseif ($oAcreedor->IdTipoPersona == PersonaTipos::PersonaJuridica)
{
	/* entes juridicos */
	$oPdf->Text($OffsetX + 4.7, $OffsetY + 18.2, $oAcreedor->EnteJuridicoOtorgacion);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 19.6, $oAcreedor->EnteJuridicoDatosInscripcion);
	$oPdf->Text($OffsetX + 9.2, $OffsetY + 19.6, date("d", strtotime($oAcreedor->EnteJuridicoFechaInscripcion)));
	$oPdf->Text($OffsetX + 10, $OffsetY + 19.6, date("m", strtotime($oAcreedor->EnteJuridicoFechaInscripcion)));
	$oPdf->Text($OffsetX + 10.9, $OffsetY + 19.6, substr(date("Y", strtotime($oAcreedor->EnteJuridicoFechaInscripcion)), 2, 2));
}

/* personas fisicas */
if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
{
	if ($oNacionalidad->Current)
	{
		if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 12.9, $OffsetY + 13.9, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 14, $OffsetY + 13.9, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 15.1, $OffsetY + 13.9, 'X');
	}
	else
	{
		if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 16.2, $OffsetY + 13.9, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 17.3, $OffsetY + 13.9, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::PA)
			$oPdf->Text($OffsetX + 18.4, $OffsetY + 13.9, 'X');
	}
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 14.6, $oCliente->DocumentoNumero);
	$oPdf->Text($OffsetX + 15.3, $OffsetY + 14.6, $oCliente->DocumentoExpedido);
	$oPdf->Text($OffsetX + 12.4, $OffsetY + 16.3, date("d", strtotime($oCliente->FechaNacimiento)));
	$oPdf->Text($OffsetX + 13.2, $OffsetY + 16.3, date("m", strtotime($oCliente->FechaNacimiento)));
	$oPdf->Text($OffsetX + 14.1, $OffsetY + 16.3, substr(date("Y", strtotime($oCliente->FechaNacimiento)), 2, 2));
	if ($oCliente->IdEstadoCivil == EstadoCivil::Soltero)
		$oPdf->Text($OffsetX + 15, $OffsetY + 16.3, 'X');
	elseif ($oCliente->IdEstadoCivil == EstadoCivil::Casado)
		$oPdf->Text($OffsetX + 16, $OffsetY + 16.3, 'X');
	elseif ($oCliente->IdEstadoCivil == EstadoCivil::Viudo)
		$oPdf->Text($OffsetX + 17, $OffsetY + 16.3, 'X');
	elseif ($oCliente->IdEstadoCivil == EstadoCivil::Divorciado)
		$oPdf->Text($OffsetX + 18, $OffsetY + 16.3, 'X');
	$oPdf->Text($OffsetX + 18.7, $OffsetY + 16.3, $oCliente->Nupcia);
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 17.1, $oCliente->ConyugeApellido . ' ' . $oCliente->ConyugeNombre);
}
elseif ($oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica)
{
	/* entes juridicos */
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 18, $oCliente->EnteJuridicoOtorgacion);
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 19.4, $oCliente->EnteJuridicoDatosInscripcion);
	$oPdf->Text($OffsetX + 16.6, $OffsetY + 19.4, date("d", strtotime($oCliente->EnteJuridicoFechaInscripcion)));
	$oPdf->Text($OffsetX + 17.5, $OffsetY + 19.4, date("m", strtotime($oCliente->EnteJuridicoFechaInscripcion)));
	$oPdf->Text($OffsetX + 18.4, $OffsetY + 19.4, substr(date("Y", strtotime($oCliente->EnteJuridicoFechaInscripcion)), 2, 2));
}
$OffsetX += 0.2;
/* identificacion del automotor */
$oPdf->Text($OffsetX + 8, $OffsetY + 23.7, $oUnidad->Patente);
$oPdf->Text($OffsetX + 5.5, $OffsetY + 24.4, $oMarcaVehiculo->Nombre);
$oPdf->Text($OffsetX + 5, $OffsetY + 25.1, $oTipoModelo->Nombre);
$oPdf->Text($OffsetX + 5.5, $OffsetY + 25.8, $oModelo->DenominacionModelo);
$oPdf->Text($OffsetX + 6.5, $OffsetY + 26.4, $oMarcaMotor->Nombre);
$oPdf->Text($OffsetX + 6.5, $OffsetY + 27.2, $oUnidad->NumeroMotor);
$oPdf->Text($OffsetX + 7, $OffsetY + 27.9, $oMarcaChasis->Nombre);
$oPdf->Text($OffsetX + 6.5, $OffsetY + 28.6, $oUnidad->NumeroChasis);

/* modalidad de contrato */
$oPdf->Text($OffsetX + 16.4, $OffsetY + 23.9, 'X');
$oPdf->Text($OffsetX + 18.6, $OffsetY + 27, 'X');
$oPdf->Text($OffsetX + 18.6, $OffsetY + 28.3, 'X');
$oPdf->Text($OffsetX + 13.3, $OffsetY + 27.4, '1');

/* generamos el archivo */
//$oPdf->Output('formulario_03.pdf', 'D');
$oPdf->AutoPrint(true);
$oPdf->Output();

?>