<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 			= intval($_REQUEST['IdFormulario']);
$IdSocio				= intval($_REQUEST['IdSocio']);
$ImprimeLeyenda 		= intval($_REQUEST['ImprimeLeyenda']);
$Observaciones			= $_REQUEST['Observaciones'];
$Nacionalidad			= $_REQUEST['Nacionalidad'];
$Email					= $_REQUEST['Email'];
$EmailCondominio		= $_REQUEST['EmailCondominio'];
$NacionalidadCondominio	= $_REQUEST['NacionalidadCondominio'];
$LocalidadNacimiento	= $_REQUEST['LocalidadNacimiento'];
$LocalidadNacimientoCondominio	= $_REQUEST['LocalidadNacimientoCondominio'];
$OffsetX 				= floatval($_REQUEST['OffsetX']);
$OffsetY 				= floatval($_REQUEST['OffsetY']);

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0;

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

if ($oClienteCondominio->DomicilioIdLocalidadPostal)
	$oLocalidadPostalCondominio = $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidadPostal);
else
	$oLocalidadPostalCondominio = $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);

//$oLocalidadPostalCondominio 	= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidadPostal);
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
$oPdf = new PDF_AutoPrint('P', 'cm', 'A4');
$oPdf->Open();

/* imprimimos la cantidad de copias necesarias */
$OffsetY-= 0.1;
for ($i=0; $i<1 /*$oTipoFormulario->CantidadCopias*/; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$OffsetY -= 1;
	
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 5, 'COMERCIANTE HABITUALISTA ' . $oDatosEmpresa->ComercianteHabitualista);
	$OffsetY += 0.3;
	/* identificacion del titular */
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 7.7, number_format($porcentajeTitularidad, 2));
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 8.3, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 8.9, $Nacionalidad);
	$oPdf->Text($OffsetX + 5.5, $OffsetY + 9.5, $Email);
	$oPdf->Text($OffsetX + 5.5, $OffsetY + 10.0, $oCliente->TelefonoCodigoArea . '-' . $oCliente->Telefono);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 10.6, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 4.8, $OffsetY + 11.4, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 7.0, $OffsetY + 11.4, $oCliente->DomicilioPiso);
	$oPdf->Text($OffsetX + 8.5, $OffsetY + 11.4, $oCliente->DomicilioDpto);
	if ($oCliente->DomicilioCodigoPostal)
		$oPdf->Text($OffsetX + 10, $OffsetY + 11.4, $oCliente->DomicilioCodigoPostal);
	else
		$oPdf->Text($OffsetX + 10, $OffsetY + 11.4, $oLocalidad->CodigoPostal);
	
	$OffsetY -= 0.3;
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 12.3, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 13, $oPartido->Nombre);
	$oPdf->Text($OffsetX + 9, $OffsetY + 13, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 13.6, ($oCliente->DomicilioCallePostal) ? $oCliente->DomicilioCallePostal : $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 4.8, $OffsetY + 14.4, ($oCliente->DomicilioNumeroPostal) ? $oCliente->DomicilioNumeroPostal : $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 7, $OffsetY + 14.4, ($oCliente->DomicilioPisoPostal) ? $oCliente->DomicilioPisoPostal : $oCliente->DomicilioPiso);
	$oPdf->Text($OffsetX + 8.5, $OffsetY + 14.4, ($oCliente->DomicilioDptoPostal) ? $oCliente->DomicilioDptoPostal : $oCliente->DomicilioDpto);
	if ($oCliente->DomicilioCodigoPostalPostal)
		$oPdf->Text($OffsetX + 10, $OffsetY + 14.4, $oCliente->DomicilioCodigoPostalPostal);
	elseif ($oCliente->DomicilioIdLocalidadPostal)
		$oPdf->Text($OffsetX + 10, $OffsetY + 14.4, $oLocalidadPostal->CodigoPostal);
	elseif ($oCliente->DomicilioCodigoPostal)
		$oPdf->Text($OffsetX + 10, $OffsetY + 14.4, $oCliente->DomicilioCodigoPostal);
	else
		$oPdf->Text($OffsetX + 10, $OffsetY + 14.4, $oLocalidad->CodigoPostal);
	
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.1, $oLocalidadPostal->Nombre);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 15.8, $oPartidoPostal->Nombre);
	$oPdf->Text($OffsetX + 9, $OffsetY + 15.8, $oProvinciaPostal->Nombre);
	
	
	/* personas fisicas */
	$OffsetY += 1;
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		$oPdf->Text($OffsetX + 7.6, $OffsetY + 15.6, $oProfesion->Nombre);
		if ($oNacionalidad->Current)
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 4.8, $OffsetY + 16.5 + 0.3, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LE)
				$oPdf->Text($OffsetX + 5.9, $OffsetY + 16.5 + 0.3, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::LC)
				$oPdf->Text($OffsetX + 7, $OffsetY + 16.5 + 0.3, 'X');
		}
		else
		{
			if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
				$oPdf->Text($OffsetX + 8.5, $OffsetY + 16.5 + 0.3, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::CI)
				$oPdf->Text($OffsetX + 9.6, $OffsetY + 16.5 + 0.3, 'X');
			if ($oCliente->DocumentoTipo == TipoDocumento::PA)
				$oPdf->Text($OffsetX + 10.7, $OffsetY + 16.5 + 0.3, 'X');
		}
		$oPdf->Text($OffsetX + 5, $OffsetY + 17.2 + 0.3, $oCliente->DocumentoNumero);
		$oPdf->Text($OffsetX + 8.5, $OffsetY + 17.2 + 0.3, $oCliente->DocumentoExpedido);
		$oPdf->Text($OffsetX + 6.5, $OffsetY + 17.9 + 0.3, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);
		$oPdf->Text($OffsetX + 7, $OffsetY + 18.6, $LocalidadNacimiento);
		
		if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
		{
			$oPdf->Text($OffsetX + 4.5, $OffsetY + 19.7 + 0.5, date("d", strtotime($oCliente->FechaNacimiento)));
			$oPdf->Text($OffsetX + 5.2, $OffsetY + 19.7 + 0.5, date("m", strtotime($oCliente->FechaNacimiento)));
			$oPdf->Text($OffsetX + 6, $OffsetY + 19.7 + 0.5, substr(date("Y", strtotime($oCliente->FechaNacimiento)), 2, 2));
			if ($oCliente->IdEstadoCivil == EstadoCivil::Soltero)
				$oPdf->Text($OffsetX + 7.3, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oCliente->IdEstadoCivil == EstadoCivil::Casado)
				$oPdf->Text($OffsetX + 8.3, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oCliente->IdEstadoCivil == EstadoCivil::Viudo)
				$oPdf->Text($OffsetX + 9.3, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oCliente->IdEstadoCivil == EstadoCivil::Divorciado)
				$oPdf->Text($OffsetX + 10.3, $OffsetY + 19.7 + 0.5, 'X');
			$oPdf->Text($OffsetX + 11.3, $OffsetY + 19.7 + 0.5, $oCliente->Nupcia);
			$oPdf->Text($OffsetX + 4.5, $OffsetY + 20.9 + 0.5, $oCliente->ConyugeApellido . ' ' . $oCliente->ConyugeNombre);
		}
	}
	elseif ($oCliente->IdTipoPersona == PersonaTipos::PersonaJuridica)
	{
		$oPdf->Text($OffsetX + 7.9, $OffsetY + 15.6, $oProfesion->Nombre);
		/* entes juridicos */
		if ($oCliente->EnteJuridicoOtorgacion)
		{
			$oPdf->Text($OffsetX +7.1, $OffsetY + 18.6, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);
			$oPdf->Text($OffsetX + 4.5, $OffsetY + 21.8, $oCliente->EnteJuridicoOtorgacion);
			$oPdf->Text($OffsetX + 4.5, $OffsetY + 22.9, $oCliente->EnteJuridicoDatosInscripcion);
			$oPdf->Text($OffsetX + 9.3, $OffsetY + 22.9, date("d", strtotime($oCliente->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 10.3, $OffsetY + 22.9, date("m", strtotime($oCliente->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 11.1, $OffsetY + 22.9, substr(date("Y", strtotime($oCliente->EnteJuridicoFechaInscripcion)), 2, 2));
		}
	}
	
	/* identificacion del condominio */
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$OffsetY -= 1;
		$OffsetY += 0.3;
		$OffsetX += 0.9;
		$oPdf->Text($OffsetX + 12, $OffsetY + 7.7, number_format((100 - $oGestoria->PorcentajeTitularidad), 2));
		$oPdf->Text($OffsetX + 12, $OffsetY + 8.3, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 12, $OffsetY + 8.9, $NacionalidadCondominio);
		$oPdf->Text($OffsetX + 13.5, $OffsetY + 9.5, $EmailCondominio);
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
		$oPdf->Text($OffsetX + 12, $OffsetY + 13.6, $oClienteCondominio->DomicilioCallePostal ? $oClienteCondominio->DomicilioCallePostal : $oClienteCondominio->DomicilioCalle);
		$oPdf->Text($OffsetX + 12, $OffsetY + 14.4, $oClienteCondominio->DomicilioNumeroPostal ? $oClienteCondominio->DomicilioNumeroPostal : $oClienteCondominio->DomicilioNumero);
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 14.4, $oClienteCondominio->DomicilioPisoPostal ? $oClienteCondominio->DomicilioPisoPostal : $oClienteCondominio->DomicilioPiso);
		$oPdf->Text($OffsetX + 16, $OffsetY + 14.4, $oClienteCondominio->DomicilioDptoPostal ? $oClienteCondominio->DomicilioDptoPostal : $oClienteCondominio->DomicilioDpto);
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
					$oPdf->Text($OffsetX + 12.3, $OffsetY + 16.5 + 0.3, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LE)
					$oPdf->Text($OffsetX + 13.4, $OffsetY + 16.5 + 0.3, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LC)
					$oPdf->Text($OffsetX + 14.5, $OffsetY + 16.5 + 0.3, 'X');
			}
			else
			{
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text($OffsetX + 16.1, $OffsetY + 16.5 + 0.3, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::CI)
					$oPdf->Text($OffsetX + 17.2, $OffsetY + 16.5 + 0.3, 'X');
				if ($oClienteCondominio->DocumentoTipo == TipoDocumento::PA)
					$oPdf->Text($OffsetX + 18.3, $OffsetY + 16.5 + 0.3, 'X');
			}
			
			$oPdf->Text($OffsetX + 12, $OffsetY + 17.2 + 0.3, $oClienteCondominio->DocumentoNumero);
			$oPdf->Text($OffsetX + 15, $OffsetY + 17.2 + 0.3, $oClienteCondominio->DocumentoExpedido);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 17.9 + 0.3, ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero);
			$oPdf->Text($OffsetX + 14.5, $OffsetY + 18.6, $LocalidadNacimientoCondominio);
			$oPdf->Text($OffsetX + 12.1, $OffsetY + 19.7 + 0.5, date("d", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 12.9, $OffsetY + 19.7 + 0.5, date("m", strtotime($oClienteCondominio->FechaNacimiento)));
			$oPdf->Text($OffsetX + 13.7, $OffsetY + 19.7 + 0.5, substr(date("Y", strtotime($oClienteCondominio->FechaNacimiento)), 2, 2));
			if ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Soltero)
				$oPdf->Text($OffsetX + 14.8, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado)
				$oPdf->Text($OffsetX + 15.8, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Viudo)
				$oPdf->Text($OffsetX + 16.8, $OffsetY + 19.7 + 0.5, 'X');
			elseif ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Divorciado)
				$oPdf->Text($OffsetX + 17.8, $OffsetY + 19.7 + 0.5, 'X');
			$oPdf->Text($OffsetX + 18.7, $OffsetY + 19.7 + 0.5, $oClienteCondominio->Nupcia);
			$oPdf->Text($OffsetX + 12, $OffsetY + 20.9 + 0.5, $oClienteCondominio->ConyugeApellido . ' ' . $oClienteCondominio->ConyugeNombre);
		}
		elseif ($oClienteCondominio->IdTipoPersona == PersonaTipos::PersonaJuridica)
		{
			$oPdf->Text($OffsetX + 12, $OffsetY + 21.7, $oClienteCondominio->EnteJuridicoOtorgacion);
			$oPdf->Text($OffsetX + 12, $OffsetY + 22.7, $oClienteCondominio->EnteJuridicoDatosInscripcion);
			$oPdf->Text($OffsetX + 16.6, $OffsetY + 22.7, date("d", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 17.6, $OffsetY + 22.7, date("m", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)));
			$oPdf->Text($OffsetX + 18.6, $OffsetY + 22.7, substr(date("Y", strtotime($oClienteCondominio->EnteJuridicoFechaInscripcion)), 2, 2));
		}
		$OffsetX -= 0.9;
	}
	
	/* identificacion del automotor */
	$OffsetY += 0.5;
	$oPdf->Text($OffsetX + 7, $OffsetY + 26 + 0.5, $oGestoria->NumeroCertificado);
	$oPdf->Text($OffsetX + 6, $OffsetY + 26.5 + 0.5, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 6, $OffsetY + 27 + 0.5, $oTipoModelo->Nombre);
	$oPdf->Text($OffsetX + 6, $OffsetY + 27.7 + 0.5, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.3 + 0.5, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.9 + 0.5, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 26.5 + 0.5, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 15, $OffsetY + 26.9 + 0.5, $oUnidad->NumeroChasis);
	$oPdf->Text($OffsetX + 14, $OffsetY + 27.6 + 0.5, UsoTipos::GetById($oGestoria->IdTipoUso));
	$oPdf->Text($OffsetX + 5.5, $OffsetY + 28.5 + 0.5, number_format($oFacturaUnidad->Total));
	$oPdf->Text($OffsetX + 9.7, $OffsetY + 28.5 + 0.5, date("d", strtotime($oFacturaUnidad->Fecha)));
	$oPdf->Text($OffsetX + 10.6, $OffsetY + 28.5 + 0.5, date("m", strtotime($oFacturaUnidad->Fecha)));
	$oPdf->Text($OffsetX + 11.4, $OffsetY + 28.5 + 0.5, substr(date("Y", strtotime($oFacturaUnidad->Fecha)), 2, 2));
	$oPdf->Text($OffsetX + 13, $OffsetY + 28.5 + 0.5, $Comprobante . ' ' . $oDatosEmpresa->RazonSocial);
}
$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();

?>