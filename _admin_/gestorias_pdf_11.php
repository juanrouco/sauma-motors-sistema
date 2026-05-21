<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$IdSocio		= intval($_REQUEST['IdSocio']);
$ImprimeLeyenda = intval($_REQUEST['ImprimeLeyenda']);

$Embargos		= intval($_REQUEST['Embargos']);
$Levantamiento	= intval($_REQUEST['Levantamiento']);
$Inhibiciones	= intval($_REQUEST['Inhibiciones']);
$LevantamientoInhibiciones	= intval($_REQUEST['LevantamientoInhibiciones']);
$CertificadoDominio	= intval($_REQUEST['CertificadoDominio']);
$InformeDominio	= intval($_REQUEST['InformeDominio']);
$AnotacionComunicaciones	= intval($_REQUEST['AnotacionComunicaciones']);
$AnotacionComunicaciones2	= intval($_REQUEST['AnotacionComunicaciones2']);
$CertificadoTransferencia	= intval($_REQUEST['CertificadoTransferencia']);
$DuplicadoBajaVehiculo	= intval($_REQUEST['DuplicadoBajaVehiculo']);
$DuplicadoBajaMotor	= intval($_REQUEST['DuplicadoBajaMotor']);
$DuplicadoBajaChasis	= intval($_REQUEST['DuplicadoBajaChasis']);
$DuplicadoDenunciaRobo	= intval($_REQUEST['DuplicadoDenunciaRobo']);
$DuplicadoComunicacionRecupero	= intval($_REQUEST['DuplicadoComunicacionRecupero']);
$AsignacionCodificacion	= intval($_REQUEST['AsignacionCodificacion']);
$DuplicadoTitulo	= intval($_REQUEST['DuplicadoTitulo']);
$DuplicadoCedula	= intval($_REQUEST['DuplicadoCedula']);
$CambioUso	= intval($_REQUEST['CambioUso']);
$CertificadoConstanciasRegistrables	= intval($_REQUEST['CertificadoConstanciasRegistrables']);
$OtrosTramites	= intval($_REQUEST['OtrosTramites']);


$Declaraciones	= $_REQUEST['Declaraciones'];

$Declaraciones = iconv(mb_detect_encoding($Declaraciones),"UTF-8//IGNORE",$Declaraciones);


$oFormularios 		= new Formularios();
$oTiposFormulario 	= new TiposFormulario();
$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oGestorias 		= new Gestorias();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oTiposDocumento 	= new TiposDocumento();
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
$oGestoriaSocios 	= new GestoriaSocios();

/* obtenemos los datos del formulario */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();

/* obtenemos los datos del tipo de formulario */
if (!$oTipoFormulario = $oTiposFormulario->GetById($oFormulario->IdTipoFormulario))
	exit();

/* obtenemos los datos de la gestoria */
if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
	exit();

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
	exit();

/* obtenemos los datos de la factura */
$oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oGestoria->IdMinuta);

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

$porcentajeTitularidad = $oGestoria->PorcentajeTitularidad;
if ($oGestoria->SociedadHecho && $IdSocio)
{
	$oGestoriaSocio = $oGestoriaSocios->GetById($IdSocio);
	$porcentajeTitularidad = $oGestoriaSocio->Porcentaje;
	if (!$oCliente = $oClientes->GetById($oGestoriaSocio->IdCliente))
		exit();
}
else
{
	if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
		exit();
}

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);

/* obtenemos los datos de la prfesion del cliente */
$oProfesion = $oProfesiones->GetById($oCliente->IdProfesion);

/* obtenemos la nacionalidad */
$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
if ($oCliente->DomicilioIdLocalidadPostal)
	$oLocalidadPostal = $oLocalidades->GetById($oCliente->DomicilioIdLocalidadPostal);
else
	$oLocalidadPostal = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);
$oPartidoPostal = $oPartidos->GetById($oLocalidadPostal->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);
$oProvinciaPostal = $oProvincias->GetById($oLocalidadPostal->IdProvincia);

/* obtenemos los datos de la localidad de nacimiento */
$oLocalidadNacimiento = $oLocalidades->GetById($oCliente->NacimientoIdLocalidad);

/* obtenemos los datos del partido de nacimiento */
$oPartidoNacimiento = $oPartidos->GetById($oLocalidadNacimiento->IdPartido);

/* obtenemos los datos de la provincia de nacimiento */
$oProvinciaNacimiento = $oProvincias->GetById($oLocalidadNacimiento->IdProvincia);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio 			= $oClientes->GetById($oGestoria->IdClienteCondominio);
$oNacionalidadCondominio 		= $oPaises->GetById($oClienteCondominio->IdNacionalidad);
$oProfesionCondominio 			= $oProfesiones->GetById($oClienteCondominio->IdProfesion);
$oLocalidadCondominio 			= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);
$oLocalidadPostalCondominio 	= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidadPostal);
$oPartidoCondominio 			= $oPartidos->GetById($oLocalidadCondominio->IdPartido);
$oPartidoPostalCondominio 		= $oPartidos->GetById($oLocalidadPostalCondominio->IdPartido);
$oProvinciaCondominio 			= $oProvincias->GetById($oLocalidadCondominio->IdProvincia);
$oProvinciaPostalCondominio 	= $oProvincias->GetById($oLocalidadPostalCondominio->IdProvincia);
$oLocalidadCondominioNacimiento = $oLocalidades->GetById($oClienteCondominio->NacimientoIdLocalidad);
$oPartidoCondominioNacimiento 	= $oPartidos->GetById($oLocalidadCondominioNacimiento->IdPartido);
$oProvinciaCondominioNacimiento = $oProvincias->GetById($oLocalidadCondominioNacimiento->IdProvincia);

/* obtenemos el listado de cedulas azules, en caso que se hayan solicitado */
$arrGestoriaCedulas = $oGestoria->GetAllCedulas();

/* armamos el detalle del comprobante */
$Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oComprobante->Numero;

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'A4');
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

/* imprimimos la cantidad de copias necesarias */
for ($i=0; $i< $oTipoFormulario->CantidadCopias; $i++)
{

$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

$OffsetX = 0;
$OffsetY = 0;

	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	if ($Embargos)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 6.6, 'X');
		
	if ($Levantamiento)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 7.5, 'X');
		
	if ($Inhibiciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 8.5, 'X');
		
	if ($LevantamientoInhibiciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 9.1, 'X');
		
	if ($CertificadoDominio)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 10, 'X');
		
	if ($InformeDominio)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 10.7, 'X');
	
	if ($AnotacionComunicaciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 11.7, 'X');
		
	if ($AnotacionComunicaciones2)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 12, 'X');
		
	if ($CertificadoTransferencia)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 12.4, 'X');
	
	if ($DuplicadoBajaVehiculo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.1, 'X');
	
	if ($DuplicadoBajaMotor)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.5, 'X');
	
	if ($DuplicadoBajaChasis)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.9, 'X');
	
	if ($DuplicadoDenunciaRobo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 14.5, 'X');
	
	if ($DuplicadoComunicacionRecupero)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.1, 'X');
	
	if ($AsignacionCodificacion)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.8, 'X');
		
	if ($DuplicadoTitulo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 16.4, 'X');
		
	if ($DuplicadoCedula)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 17.1, 'X');
		
	if ($CambioUso)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 17.7, 'X');
		
	if ($CertificadoConstanciasRegistrables)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 18.1, 'X');
		
	if ($OtrosTramites)
		$oPdf->Text($OffsetX + 4.8, $OffsetY + 18.4, 'X');
	
	$textoCount = 0;
	$arrTexto = explode(' ', $Declaraciones);
	$OffsetTexto = 0;
	
	$textoImprimir = '';
	
	foreach ($arrTexto as $oPalabra)
	{
		$textoImprimir .= $oPalabra . ' ';
		$textoCount += strlen($oPalabra) + 1;
		
		if ($textoCount >= 56)
		{
			$oPdf->Text($OffsetX + 8.5, $OffsetY + 19.6 + $OffsetTexto, $textoImprimir);
			$textoImprimir = '';
			$textoCount = 0;
			$OffsetTexto += 0.4;
		}
	}
	
	if (strlen($textoImprimir) > 0)
	{
		$oPdf->Text($OffsetX + 8.5, $OffsetY + 19.6 + $OffsetTexto, $textoImprimir);		
	}
	
	/* identificacion del titular */
	
	
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 21.7, $oCliente->RazonSocial);
		if ($oNacionalidad->Current)
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 5.1, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LE)
				$oPdf->Text($OffsetX + 6.1, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LC)
				$oPdf->Text($OffsetX + 7.1, $OffsetY + 25.4, 'X');
		}
		else
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 9.5, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::CI)
				$oPdf->Text($OffsetX + 10.5, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::PA)
				$oPdf->Text($OffsetX + 11.5, $OffsetY + 25.4, 'X');
		}
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 26, $oCliente->DocumentoNumero);
		$oPdf->Text($OffsetX + 8.7, $OffsetY + 26, $oCliente->DocumentoExpedido);
	}
	elseif ($oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica)
	{
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 21.7, $oCliente->RepresentanteRazonSocial);
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 5.1, $OffsetY + 25.4, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 6.1, $OffsetY + 25.4, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 7.1, $OffsetY + 25.4, 'X');
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 25.9, $oCliente->RepresentanteDocumentoNumero);
	}
	
	/* identificacion del condominio 
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$OffsetY -= 1;
		$OffsetY += 0.3;
		$oPdf->Text($OffsetX + 12, $OffsetY + 7.7, number_format((100 - $oGestoria->PorcentajeTitularidad), 2));
		$oPdf->Text($OffsetX + 12, $OffsetY + 8.3, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 13.5, $OffsetY + 9.5, $oClienteCondominio->Email);
		$oPdf->Text($OffsetX + 13.5, $OffsetY + 10.0, $oClienteCondominio->TelefonoCodigoArea . '-' . $oClienteCondominio->Telefono);
		$oPdf->Text($OffsetX + 12, $OffsetY + 10.6, $oClienteCondominio->DomicilioCalle);
		$oPdf->Text($OffsetX + 12, $OffsetY + 11.4, $oClienteCondominio->DomicilioNumero);
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 11.4, $oClienteCondominio->DomicilioPiso);
		$oPdf->Text($OffsetX + 16, $OffsetY + 11.4, $oClienteCondominio->DomicilioDpto);
		if ($oClienteCondominio->DomicilioCodigoPostal)
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 11.4, $oClienteCondominio->DomicilioCodigoPostal);
		else
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 11.4, $oLocalidadCondominio->CodigoPostal);
			
		$OffsetY -= 0.3;
		$oPdf->Text($OffsetX + 12, $OffsetY + 12.1, $oLocalidadCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 12.8, $oPartidoCondominio->Nombre);
		$oPdf->Text($OffsetX + 16.5, $OffsetY + 12.8, $oProvinciaCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 13.6, $oClienteCondominio->DomicilioCallePostal);
		$oPdf->Text($OffsetX + 12, $OffsetY + 14.4, $oClienteCondominio->DomicilioNumeroPostal);
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 14.4, $oClienteCondominio->DomicilioPisoPostal);
		$oPdf->Text($OffsetX + 16, $OffsetY + 14.4, $oClienteCondominio->DomicilioDptoPostal);
		if ($oClienteCondominio->DomicilioCodigoPostalPostal)
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 14.4, $oClienteCondominio->DomicilioCodigoPostalPostal);
		else
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 14.4, $oLocalidadPostalCondominio->CodigoPostal);
		$oPdf->Text($OffsetX + 12, $OffsetY + 15.1, $oLocalidadPostalCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 15.8, $oPartidoPostalCondominio->Nombre);
		$oPdf->Text($OffsetX + 16.5, $OffsetY + 15.8, $oProvinciaPostalCondominio->Nombre);
		
		
		
		$OffsetY += 1;
		if ($oClienteCondominio->IdTipoPersona == PersonaTipos::PersonaFisica)
		{
			$oPdf->Text($OffsetX + 15.5, $OffsetY + 15.6, $oProfesionCondominio->Nombre);
			if ($oNacionalidadCondominio->Current)
			{
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text($OffsetX + 12.3, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LE)
					$oPdf->Text($OffsetX + 13.4, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LC)
					$oPdf->Text($OffsetX + 14.5, $OffsetY + 16.5, 'X');
			}
			else
			{
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text($OffsetX + 16.1, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::CI)
					$oPdf->Text($OffsetX + 17.2, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::PA)
					$oPdf->Text($OffsetX + 18.3, $OffsetY + 16.5, 'X');
			}
			$oPdf->Text($OffsetX + 12, $OffsetY + 17.2, $oClienteCondominio->DocumentoNumero);
			$oPdf->Text($OffsetX + 15, $OffsetY + 17.2, $oClienteCondominio->DocumentoExpedido);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 17.9, ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 18.6, $oLocalidadCondominioNacimiento->Nombre . ', ' . $oProvinciaCondominioNacimiento->Nombre);
			$oPdf->Text($OffsetX + 12.1, $OffsetY + 19.7, date("d", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 12.9, $OffsetY + 19.7, date("m", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 13.7, $OffsetY + 19.7, substr(date("Y", strtotime($oClienteCondominio->FechaNacimiento)), 2, 2));
			if ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Soltero)
				$oPdf->Text($OffsetX + 14.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)
				$oPdf->Text($OffsetX + 15.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Viudo)
				$oPdf->Text($OffsetX + 16.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Divorciado)
				$oPdf->Text($OffsetX + 17.8, $OffsetY + 19.7, 'X');
			$oPdf->Text($OffsetX + 18.7, $OffsetY + 19.7, $oClienteCondominio->Nupcia);
			$oPdf->Text($OffsetX + 12, $OffsetY + 20.9, $oClienteCondominio->ConyugeApellido . ' ' . $oClienteCondominio->ConyugeNombre);
		}
		elseif ($oClienteCondominio->IdTipoPersona == PersonaTipos::PersonaJuridica)
		{
			$oPdf->Text($OffsetX + 12, $OffsetY + 21.7, $oClienteCondominio->EnteJuridicoOtorgacion);
			$oPdf->Text($OffsetX + 12, $OffsetY + 22.7, $oClienteCondominio->EnteJuridicoDatosInscripcion);
			$oPdf->Text($OffsetX + 16.6, $OffsetY + 22.7, date("d", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 17.6, $OffsetY + 22.7, date("m", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 18.6, $OffsetY + 22.7, substr(date("Y", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)), 2, 2));
		}
	}
	
	/* identificacion del automotor */
	//$OffsetY += 0.5;
	//$oPdf->Text($OffsetX + 7, $OffsetY + 25.9, $oGestoria->NumeroCertificado);
	$OffsetX += 1;
	$OffsetY += 0.7;
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 25.1, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 25.5, $oTipoModelo->Nombre);
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 25.9, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 26.3, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 26.7, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 27.1, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 27.5, $oUnidad->NumeroChasis);
	
}

if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
{
	$oCliente = $oClientes->GetById($oGestoria->IdClienteCondominio);
	for ($i=0; $i< $oTipoFormulario->CantidadCopias; $i++)
	{
		$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

$OffsetX = 0;
$OffsetY = 0;

	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	if ($Embargos)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 6.6, 'X');
		
	if ($Levantamiento)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 7.5, 'X');
		
	if ($Inhibiciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 8.5, 'X');
		
	if ($LevantamientoInhibiciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 9.1, 'X');
		
	if ($CertificadoDominio)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 10, 'X');
		
	if ($InformeDominio)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 10.7, 'X');
	
	if ($AnotacionComunicaciones)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 11.7, 'X');
		
	if ($AnotacionComunicaciones2)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 12, 'X');
		
	if ($CertificadoTransferencia)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 12.4, 'X');
	
	if ($DuplicadoBajaVehiculo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.1, 'X');
	
	if ($DuplicadoBajaMotor)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.5, 'X');
	
	if ($DuplicadoBajaChasis)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.9, 'X');
	
	if ($DuplicadoDenunciaRobo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 14.5, 'X');
	
	if ($DuplicadoComunicacionRecupero)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.1, 'X');
	
	if ($AsignacionCodificacion)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.8, 'X');
		
	if ($DuplicadoTitulo)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 16.4, 'X');
		
	if ($DuplicadoCedula)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 17.1, 'X');
		
	if ($CambioUso)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 17.7, 'X');
		
	if ($CertificadoConstanciasRegistrables)
		$oPdf->Text($OffsetX + 4.5, $OffsetY + 18.1, 'X');
		
	if ($OtrosTramites)
		$oPdf->Text($OffsetX + 4.8, $OffsetY + 18.4, 'X');
	
	$textoCount = 0;
	$arrTexto = explode(' ', $Declaraciones);
	$OffsetTexto = 0;
	
	$textoImprimir = '';
	
	foreach ($arrTexto as $oPalabra)
	{
		$textoImprimir .= $oPalabra . ' ';
		$textoCount += strlen($oPalabra) + 1;
		
		if ($textoCount >= 56)
		{
			$oPdf->Text($OffsetX + 8.5, $OffsetY + 19.6 + $OffsetTexto, $textoImprimir);
			$textoImprimir = '';
			$textoCount = 0;
			$OffsetTexto += 0.4;
		}
	}
	
	if (strlen($textoImprimir) > 0)
	{
		$oPdf->Text($OffsetX + 8.5, $OffsetY + 19.6 + $OffsetTexto, $textoImprimir);		
	}
	
	/* identificacion del titular */
	
	
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 21.7, $oCliente->RazonSocial);
		if ($oNacionalidad->Current)
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 5.1, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LE)
				$oPdf->Text($OffsetX + 6.1, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LC)
				$oPdf->Text($OffsetX + 7.1, $OffsetY + 25.4, 'X');
		}
		else
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 9.5, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::CI)
				$oPdf->Text($OffsetX + 10.5, $OffsetY + 25.4, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::PA)
				$oPdf->Text($OffsetX + 11.5, $OffsetY + 25.4, 'X');
		}
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 26, $oCliente->DocumentoNumero);
		$oPdf->Text($OffsetX + 8.7, $OffsetY + 26, $oCliente->DocumentoExpedido);
	}
	elseif ($oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica)
	{
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 21.7, $oCliente->RepresentanteRazonSocial);
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 5.1, $OffsetY + 25.4, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 6.1, $OffsetY + 25.4, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 7.1, $OffsetY + 25.4, 'X');
		$oPdf->Text($OffsetX + 4.9, $OffsetY + 25.9, $oCliente->RepresentanteDocumentoNumero);
	}
	
	/* identificacion del condominio 
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$OffsetY -= 1;
		$OffsetY += 0.3;
		$oPdf->Text($OffsetX + 12, $OffsetY + 7.7, number_format((100 - $oGestoria->PorcentajeTitularidad), 2));
		$oPdf->Text($OffsetX + 12, $OffsetY + 8.3, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 13.5, $OffsetY + 9.5, $oClienteCondominio->Email);
		$oPdf->Text($OffsetX + 13.5, $OffsetY + 10.0, $oClienteCondominio->TelefonoCodigoArea . '-' . $oClienteCondominio->Telefono);
		$oPdf->Text($OffsetX + 12, $OffsetY + 10.6, $oClienteCondominio->DomicilioCalle);
		$oPdf->Text($OffsetX + 12, $OffsetY + 11.4, $oClienteCondominio->DomicilioNumero);
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 11.4, $oClienteCondominio->DomicilioPiso);
		$oPdf->Text($OffsetX + 16, $OffsetY + 11.4, $oClienteCondominio->DomicilioDpto);
		if ($oClienteCondominio->DomicilioCodigoPostal)
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 11.4, $oClienteCondominio->DomicilioCodigoPostal);
		else
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 11.4, $oLocalidadCondominio->CodigoPostal);
			
		$OffsetY -= 0.3;
		$oPdf->Text($OffsetX + 12, $OffsetY + 12.1, $oLocalidadCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 12.8, $oPartidoCondominio->Nombre);
		$oPdf->Text($OffsetX + 16.5, $OffsetY + 12.8, $oProvinciaCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 13.6, $oClienteCondominio->DomicilioCallePostal);
		$oPdf->Text($OffsetX + 12, $OffsetY + 14.4, $oClienteCondominio->DomicilioNumeroPostal);
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 14.4, $oClienteCondominio->DomicilioPisoPostal);
		$oPdf->Text($OffsetX + 16, $OffsetY + 14.4, $oClienteCondominio->DomicilioDptoPostal);
		if ($oClienteCondominio->DomicilioCodigoPostalPostal)
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 14.4, $oClienteCondominio->DomicilioCodigoPostalPostal);
		else
			$oPdf->Text($OffsetX + 17.7, $OffsetY + 14.4, $oLocalidadPostalCondominio->CodigoPostal);
		$oPdf->Text($OffsetX + 12, $OffsetY + 15.1, $oLocalidadPostalCondominio->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 15.8, $oPartidoPostalCondominio->Nombre);
		$oPdf->Text($OffsetX + 16.5, $OffsetY + 15.8, $oProvinciaPostalCondominio->Nombre);
		
		
		
		$OffsetY += 1;
		if ($oClienteCondominio->IdTipoPersona == PersonaTipos::PersonaFisica)
		{
			$oPdf->Text($OffsetX + 15.5, $OffsetY + 15.6, $oProfesionCondominio->Nombre);
			if ($oNacionalidadCondominio->Current)
			{
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text($OffsetX + 12.3, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LE)
					$oPdf->Text($OffsetX + 13.4, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LC)
					$oPdf->Text($OffsetX + 14.5, $OffsetY + 16.5, 'X');
			}
			else
			{
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text($OffsetX + 16.1, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::CI)
					$oPdf->Text($OffsetX + 17.2, $OffsetY + 16.5, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::PA)
					$oPdf->Text($OffsetX + 18.3, $OffsetY + 16.5, 'X');
			}
			$oPdf->Text($OffsetX + 12, $OffsetY + 17.2, $oClienteCondominio->DocumentoNumero);
			$oPdf->Text($OffsetX + 15, $OffsetY + 17.2, $oClienteCondominio->DocumentoExpedido);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 17.9, ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 18.6, $oLocalidadCondominioNacimiento->Nombre . ', ' . $oProvinciaCondominioNacimiento->Nombre);
			$oPdf->Text($OffsetX + 12.1, $OffsetY + 19.7, date("d", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 12.9, $OffsetY + 19.7, date("m", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 13.7, $OffsetY + 19.7, substr(date("Y", strtotime($oClienteCondominio->FechaNacimiento)), 2, 2));
			if ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Soltero)
				$oPdf->Text($OffsetX + 14.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)
				$oPdf->Text($OffsetX + 15.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Viudo)
				$oPdf->Text($OffsetX + 16.8, $OffsetY + 19.7, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Divorciado)
				$oPdf->Text($OffsetX + 17.8, $OffsetY + 19.7, 'X');
			$oPdf->Text($OffsetX + 18.7, $OffsetY + 19.7, $oClienteCondominio->Nupcia);
			$oPdf->Text($OffsetX + 12, $OffsetY + 20.9, $oClienteCondominio->ConyugeApellido . ' ' . $oClienteCondominio->ConyugeNombre);
		}
		elseif ($oClienteCondominio->IdTipoPersona == PersonaTipos::PersonaJuridica)
		{
			$oPdf->Text($OffsetX + 12, $OffsetY + 21.7, $oClienteCondominio->EnteJuridicoOtorgacion);
			$oPdf->Text($OffsetX + 12, $OffsetY + 22.7, $oClienteCondominio->EnteJuridicoDatosInscripcion);
			$oPdf->Text($OffsetX + 16.6, $OffsetY + 22.7, date("d", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 17.6, $OffsetY + 22.7, date("m", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 18.6, $OffsetY + 22.7, substr(date("Y", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)), 2, 2));
		}
	}
	
	/* identificacion del automotor */
	//$OffsetY += 0.5;
	//$oPdf->Text($OffsetX + 7, $OffsetY + 25.9, $oGestoria->NumeroCertificado);
	$OffsetX += 1;
	$OffsetY += 0.7;
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 25.1, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 25.5, $oTipoModelo->Nombre);
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 25.9, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 26.3, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 26.7, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 27.1, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 14.8, $OffsetY + 27.5, $oUnidad->NumeroChasis);
		
	}
}

/* generamos el archivo */
//$oPdf->Output('formulario_02.pdf', 'D');
$oPdf->AutoPrint(true, false);
/* generamos el archivo */
$oPdf->Output();


?>