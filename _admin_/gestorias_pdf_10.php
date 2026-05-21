<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 			= intval($_REQUEST['IdFormulario']);
$OffsetX 				= floatval($_REQUEST['OffsetX']);
$OffsetY 				= floatval($_REQUEST['OffsetY']);
$InteresesPunitorios	= floatval($_REQUEST['InteresesPunitorios']);
$Valor94				= floatval($_REQUEST['Valor94']);
$Nacionalidad			= strval($_REQUEST['Nacionalidad']);
$NacionalidadConyuge	= strval($_REQUEST['NacionalidadConyuge']);
$SalvarInformacion 	= strval($_REQUEST['SalvarInformacion']);
$Observaciones 	= strval($_REQUEST['Observaciones']);

//$Observaciones = urldecode($Observaciones);
$remove = array("\n", "\r\n", "\r");
$Observaciones = str_replace($remove, ' ', $Observaciones);

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0;

$oFormularios 		= new Formularios();
$oTiposFormulario 	= new TiposFormulario();
$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oGestorias 		= new Gestorias();
$oPrendas 			= new Prendas();
$oPrendaConyuges 	= new PrendaConyuges();
$oAcreedores 		= new Acreedores();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oTiposIva 			= new TiposIva();
$oTiposDocumento 	= new TiposDocumento();
$oEstadosCiviles 	= new EstadosCiviles();
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
$oNumber			= new Number(); 

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

/* obtenemos los datos del estado civil del cliente */
$oEstadoCivil = $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);

/* obtenemos los datos del tipo de documento del cliente */
$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);

/* obtenemos la nacionalidad */
$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos los datos del condominio */
$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);


/* obtenemos los datos del tipo de documento del cliente */
$oTipoDocumentoCondominio = $oTiposDocumento->GetById($oClienteCondominio->DocumentoTipo);

function CalculaEdad( $fecha ) {
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}

$edad = CalcularEdad($oCliente->FechaNacimiento);

/* obentemos el listado de fiadores */
$arrFiadores = $oPrenda->GetAllFiadores();

/* determinamos el domicilio */
$Domicilio = '';	
$Domicilio.= $oCliente->GetDomicilio();	
$Domicilio.= ' - ';	
$Domicilio.= $oLocalidad->Nombre;	
$Domicilio.= ', ';	
$Domicilio.= $oProvincia->Nombre;	

/* comenzamos la creacion del archivo pdf */
//$oPdf = new FPDF('P', 'cm', 'Legal');
$oPdf = new PDF_AutoPrint('P', 'cm', 'Legal');
$oPdf->Open();

$oPdf->AddPage();
	
	$OffsetX = 0;
	$OffsetY = -3;
	
	$oPdf->SetFont('Arial', '', 8);
	$PesosLetra = $oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos");
    $PesosLetra = wordwrap($PesosLetra, 50, '\n');
    $arrPesosLetra = explode('\n', $PesosLetra);
	
	//$oPdf->Text($OffsetX + 18.2, $OffsetY + 5.4, 'FIJA.');
	$lineaFecha = 9;
	$oPdf->Text($OffsetX + 14.8, $OffsetY + $lineaFecha, 'BS. AS.');
	$oPdf->Text($OffsetX + 16.5, $OffsetY + $lineaFecha, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 17.9, $OffsetY + $lineaFecha, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 19.6, $OffsetY + $lineaFecha, date("Y", strtotime($oGestoria->FechaGestion)));
	
	$oPdf->Text($OffsetX + 5.7, $OffsetY + 9.8, number_format($oPrenda->FinanciacionCapital, 2));
	$oPdf->Text($OffsetX + 10.4, $OffsetY + 10.4, $arrPesosLetra[0]);
	if (count($arrPesosLetra) > 1) 
		$oPdf->Text($OffsetX + 3.5, $OffsetY + 10.8, $arrPesosLetra[1]);
	
	$lineaNombre = 11.2;
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaNombre, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
	else
		$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaNombre, $oCliente->RazonSocial);
	
	$lineaMarca = 12.9;
	//$oPdf->Text($OffsetX + 5, $OffsetY + 10.5, 'UN AUTOMOTOR MARCA:');
	$oPdf->Text($OffsetX + 6.3, $OffsetY + $lineaMarca, $oMarcaVehiculo->Nombre);
	//$oPdf->Text($OffsetX + 5, $OffsetY + 11, 'TIPO:');
	$oPdf->Text($OffsetX + 9.5, $OffsetY + $lineaMarca, $oTipoModelo->Nombre);
	//$oPdf->Text($OffsetX + 9.5, $OffsetY + 10.5, 'MODELO:');
	$oPdf->Text($OffsetX + 13.4, $OffsetY + $lineaMarca, $oModelo->DenominacionModelo);
	//$oPdf->Text($OffsetX + 5, $OffsetY + 11.5, 'MOTOR MARCA:');
	$oPdf->Text($OffsetX + 17.7, $OffsetY + $lineaMarca, $oMarcaMotor->Nombre);
	
	$lineaNumeroMotor = 13.3;
	//$oPdf->Text($OffsetX + 8.5, $OffsetY + 10, 'N:');
	$oPdf->Text($OffsetX + 4.2, $OffsetY + $lineaNumeroMotor, $oUnidad->NumeroMotor);
	//$oPdf->Text($OffsetX + 11.5, $OffsetY + 11, 'CHASIS MARCA:');
	$oPdf->Text($OffsetX + 11.8, $OffsetY + $lineaNumeroMotor, $oMarcaChasis->Nombre);
	//$oPdf->Text($OffsetX + 15, $OffsetY + 11, 'N:');
	$oPdf->Text($OffsetX + 17.8, $OffsetY + $lineaNumeroMotor, $oUnidad->NumeroChasis);
	
	$oPdf->Text($OffsetX + 13, $OffsetY + 13.6, 'LEASE UNIDAD 0KM ' . date("Y") . ', USO PRIVADO');
	$oPdf->Text($OffsetX + 13, $OffsetY + 13.9, $SalvarInformacion);
	/*$oPdf->Text($OffsetX + 12, $OffsetY + 11.5, 'COLOR:');
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 11.5, $oColor->Nombre);*/
	
	$lineaProvincia = 14.5;
	$oPdf->Text($OffsetX + 8.9, $OffsetY + $lineaProvincia, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + $lineaProvincia, $oPartido->Nombre);
	
	//$oPdf->Text($OffsetX + 6.3, $OffsetY + 11.7, $oLocalidad->Nombre);
	
	$lineaLocalidad = 15.3;
	$oPdf->Text($OffsetX + 9.7, $OffsetY + $lineaLocalidad, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 13.2, $OffsetY + $lineaLocalidad, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 19, $OffsetY + $lineaLocalidad, $oCliente->DomicilioNumero);
	
	//$oPdf->Text($OffsetX + 3, $OffsetY + 13, 'NINGUNO DE NINGUNA NATURALEZA');
	
	$oPdf->Text($OffsetX + 3.5, $OffsetY + 17, 'EL PRESENTE CONTRATO Y SUS HOJAS A CONTINUACION');
	
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 17.4, $oPrenda->CantidadCuotas);
	
	//$oPdf->Text($OffsetX + 16.2, $OffsetY + 14.8, 'CUOTAS IGUALES Y');
	//$oPdf->Text($OffsetX + 5, $OffsetY + 15.2, 'CONSECUTIVAS DE');
	$oPdf->Text($OffsetX + 3.65, $OffsetY + 17.8, number_format($oPrenda->ImporteCuota, 2));
	//$oPdf->Text($OffsetX + 9.5, $OffsetY + 15.2, 'C/U');
	$lineaVencimiento = 18.2;
	$oPdf->Text($OffsetX + 13.5, $OffsetY + $lineaVencimiento, date("d", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 15, $OffsetY + $lineaVencimiento, date("m", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 18, $OffsetY + $lineaVencimiento, date("Y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	/*$oPdf->Text($OffsetX + 9, $OffsetY + 15.5, 'LA PRIMERA VENCE EL');
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 15.5, date("d/m/y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 14, $OffsetY + 15.5, ' Y LAS DEMAS EL MISMO DIA');
	$oPdf->Text($OffsetX + 5, $OffsetY + 16.0, ' DE LOS MESES SUCESIVOS');*/
	$lineaTasaNominal = 19.9;
	$oPdf->Text($OffsetX + 10.5, $OffsetY + $lineaTasaNominal, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 16.9, $OffsetY + $lineaTasaNominal, $oPrenda->TasaEfectiva);
	
	$oPdf->Text($OffsetX + 6.5, $OffsetY + 20.3, $oPrenda->CostoFinancieroTotal); 
	
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 21.8, 'POLIZA EN TRAMITE');
	
	$OffsetY -= 0.5;
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 24.6, 'ACREEDOR'); 
	
	$oPdf->Text($OffsetX + 7.7, $OffsetY + 25.3, $oAcreedor->NumeroInscripcion);
	$OffsetY += 0.3;
	$OffsetX += 0.5;
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.3, $oCliente->RazonSocial);
	else
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.3, $oCliente->RazonSocial);
		
	$oPdf->Text($OffsetX + 13.9, $OffsetY + 25.7, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 26.1, $oProfesion->Nombre);
	if ($oCliente->IdTipoPersona != PersonaTipos::PersonaJuridica)
	{
		$oPdf->Text($OffsetX + 13.9, $OffsetY + 26.5, $Nacionalidad);
		$oPdf->Text($OffsetX + 17.8, $OffsetY + 26.5, $edad);
	}
	$oPdf->SetFont('Arial', '', 6);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 26.9, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 27.3, $oPartido->Nombre . ' , ' . $oLocalidad->Nombre . ', ' . $oProvincia->Nombre);
	
	$oPdf->SetFont('Arial', '', 8);
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
		$oPdf->Text($OffsetX + 15.2, $OffsetY + 27.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	else
		$oPdf->Text($OffsetX + 15.2, $OffsetY + 27.8, $oCliente->ClaveFiscalNumero);
//$OffsetY -= 0.5;


/* Pagina 1: imprimimos la cantidad de copias necesarias */
for ($i=1; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$OffsetX = 0;
	$OffsetY = -3;
	
	$oPdf->SetFont('Arial', '', 8);
	$PesosLetra = $oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos");
    $PesosLetra = wordwrap($PesosLetra, 50, '\n');
    $arrPesosLetra = explode('\n', $PesosLetra);
	
	//$oPdf->Text($OffsetX + 18.2, $OffsetY + 5.4, 'FIJA.');
	$lineaFecha = 8.9;
	$oPdf->Text($OffsetX + 14.8, $OffsetY + $lineaFecha, 'BS. AS.');
	$oPdf->Text($OffsetX + 16.5, $OffsetY + $lineaFecha, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 17.9, $OffsetY + $lineaFecha, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 19.6, $OffsetY + $lineaFecha, date("Y", strtotime($oGestoria->FechaGestion)));
	
	$oPdf->Text($OffsetX + 5.7, $OffsetY + 9.8, number_format($oPrenda->FinanciacionCapital, 2));
	
	$oPdf->Text($OffsetX + 10.4, $OffsetY + 10.3, $arrPesosLetra[0]);
	if (count($arrPesosLetra) > 1) 
		$oPdf->Text($OffsetX + 3.5, $OffsetY + 10.7, $arrPesosLetra[1]);
		
	$lineaNombre = 11.1;
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaNombre, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
	else
		$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaNombre, $oCliente->RazonSocial);
	
	$lineaMarca = 12.8;
	//$oPdf->Text($OffsetX + 5, $OffsetY + 10.5, 'UN AUTOMOTOR MARCA:');
	$oPdf->Text($OffsetX + 6.3, $OffsetY + $lineaMarca, $oMarcaVehiculo->Nombre);
	//$oPdf->Text($OffsetX + 5, $OffsetY + 11, 'TIPO:');
	$oPdf->Text($OffsetX + 9.5, $OffsetY + $lineaMarca, $oTipoModelo->Nombre);
	//$oPdf->Text($OffsetX + 9.5, $OffsetY + 10.5, 'MODELO:');
	$oPdf->Text($OffsetX + 13.2, $OffsetY + $lineaMarca, $oModelo->DenominacionModelo);
	//$oPdf->Text($OffsetX + 5, $OffsetY + 11.5, 'MOTOR MARCA:');
	$oPdf->Text($OffsetX + 17.5, $OffsetY + $lineaMarca, $oMarcaMotor->Nombre);
	
	$lineaNumeroMotor = 13.3;
	//$oPdf->Text($OffsetX + 8.5, $OffsetY + 10, 'N:');
	$oPdf->Text($OffsetX + 4, $OffsetY + $lineaNumeroMotor, $oUnidad->NumeroMotor);
	//$oPdf->Text($OffsetX + 11.5, $OffsetY + 11, 'CHASIS MARCA:');
	$oPdf->Text($OffsetX + 11.8, $OffsetY + $lineaNumeroMotor, $oMarcaChasis->Nombre);
	//$oPdf->Text($OffsetX + 15, $OffsetY + 11, 'N:');
	$oPdf->Text($OffsetX + 17.8, $OffsetY + $lineaNumeroMotor, $oUnidad->NumeroChasis);
	
	$oPdf->Text($OffsetX + 13, $OffsetY + 13.6, 'LEASE UNIDAD 0KM ' . date("Y") . ', USO PRIVADO');
	$oPdf->Text($OffsetX + 13, $OffsetY + 13.9, $SalvarInformacion);
	/*$oPdf->Text($OffsetX + 12, $OffsetY + 11.5, 'COLOR:');
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 11.5, $oColor->Nombre);*/
	
	$lineaProvincia = 14.4;
	$oPdf->Text($OffsetX + 8.9, $OffsetY + $lineaProvincia, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + $lineaProvincia, $oPartido->Nombre);
	
	//$oPdf->Text($OffsetX + 6.3, $OffsetY + 11.7, $oLocalidad->Nombre);
	
	$lineaLocalidad = 15.2;
	$oPdf->Text($OffsetX + 9.7, $OffsetY + $lineaLocalidad, $oLocalidad->Nombre);
	
	$oPdf->Text($OffsetX + 13.2, $OffsetY + $lineaLocalidad, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 19, $OffsetY + $lineaLocalidad, $oCliente->DomicilioNumero);
	
	//$oPdf->Text($OffsetX + 3, $OffsetY + 13, 'NINGUNO DE NINGUNA NATURALEZA');
	
	$oPdf->Text($OffsetX + 3.5, $OffsetY + 17, 'EL PRESENTE CONTRATO Y SUS HOJAS A CONTINUACION');
	
	$oPdf->Text($OffsetX + 13.7, $OffsetY + 17.2, $oPrenda->CantidadCuotas);
	
	//$oPdf->Text($OffsetX + 16.2, $OffsetY + 14.8, 'CUOTAS IGUALES Y');
	//$oPdf->Text($OffsetX + 5, $OffsetY + 15.2, 'CONSECUTIVAS DE');
	$oPdf->Text($OffsetX + 3.7, $OffsetY + 17.7, number_format($oPrenda->ImporteCuota, 2));
	//$oPdf->Text($OffsetX + 9.5, $OffsetY + 15.2, 'C/U');
	$lineaVencimiento = 18.05;
	$oPdf->Text($OffsetX + 13.5, $OffsetY + $lineaVencimiento, date("d", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 15, $OffsetY + $lineaVencimiento, date("m", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 18, $OffsetY + $lineaVencimiento, date("Y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	
	/*$oPdf->Text($OffsetX + 4.5, $OffsetY + 18.2, date("d", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 6, $OffsetY + 18.2, date("m", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 9.3, $OffsetY + 18.2, date("Y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 9, $OffsetY + 15.5, 'LA PRIMERA VENCE EL');
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 15.5, date("d/m/y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 14, $OffsetY + 15.5, ' Y LAS DEMAS EL MISMO DIA');
	$oPdf->Text($OffsetX + 5, $OffsetY + 16.0, ' DE LOS MESES SUCESIVOS');*/
	
	$lineaTasaNominal = 19.7;
	$oPdf->Text($OffsetX + 10.4, $OffsetY + $lineaTasaNominal, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 16.9, $OffsetY + $lineaTasaNominal, $oPrenda->TasaEfectiva);
	$oPdf->Text($OffsetX + 6.4, $OffsetY + 20.2, $oPrenda->CostoFinancieroTotal); 
	
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 21.8, 'POLIZA EN TRAMITE');
	
	$OffsetY -= 0.5;
	$oPdf->Text($OffsetX + 7.4, $OffsetY + 24.6, 'ACREEDOR'); 
	
	$oPdf->Text($OffsetX + 7.4, $OffsetY + 25.2, $oAcreedor->NumeroInscripcion);
	$OffsetY += 0.3;
	$OffsetX += 0.5;
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.3, $oCliente->RazonSocial);
	else
		$oPdf->Text($OffsetX + 14.5, $OffsetY + 25.3, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 13.9, $OffsetY + 25.7, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 26.1, $oProfesion->Nombre);
	if ($oCliente->IdTipoPersona != PersonaTipos::PersonaJuridica)
	{
		$oPdf->Text($OffsetX + 13.9, $OffsetY + 26.5, $oNacionalidad->Nombre);
		$oPdf->Text($OffsetX + 17.8, $OffsetY + 26.5, $edad);
	}
	$oPdf->SetFont('Arial', '', 6);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 26.9, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 27.3, $oPartido->Nombre . ' , ' . $oLocalidad->Nombre . ', ' . $oProvincia->Nombre);
	
	$oPdf->SetFont('Arial', '', 8);
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
		$oPdf->Text($OffsetX + 15.2, $OffsetY + 27.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	else
		$oPdf->Text($OffsetX + 15.2, $OffsetY + 27.8, $oCliente->ClaveFiscalNumero);
//$OffsetY -= 0.5;

	
	
}

$OffsetY = 0;
/* Pagina 9: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 6.4, $OffsetY + 2.9, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
	else
		$oPdf->Text($OffsetX + 6.4, $OffsetY + 2.9, $oCliente->RazonSocial);
	
	if ($InteresesPunitorios > 0)
		$oPdf->Text($OffsetX + 5.6, $OffsetY + 15, $InteresesPunitorios);
}

/* Pagina 9: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	if ($Valor94 > 0)
		$oPdf->Text($OffsetX + 12.5, $OffsetY + 5.6, $Valor94);
}

/* Pagina 3: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	$lineaNombre = 8.2;
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 6.3, $OffsetY + $lineaNombre, $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial);
	else
		$oPdf->Text($OffsetX + 6.3, $OffsetY + $lineaNombre, $oCliente->RazonSocial);
	$domicilio = $oCliente->DomicilioCalle . ' ' . $oCliente->DomicilioNumero . ', ' . $oLocalidad->Nombre . ', ' .$oProvincia->Nombre;
	$oPdf->Text($OffsetX + 5, $OffsetY + 8.7, $domicilio);
	$telefono = '';
	if ($oCliente->TelefonoCodigoArea)
		$telefono .= $oCliente->TelefonoCodigoArea . '-';
	if ($oCliente->Telefono)
		$telefono .= $oCliente->Telefono;
	$oPdf->Text($OffsetX + 5, $OffsetY + 9.3, $telefono);
}

/* Pagina 9: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 8.5, $OffsetY + 3.25, $oCliente->RazonSocial);
	
	$arrObservaciones = explode(' ', $Observaciones);
	$str = '';
	$count = 0;
	$rows = 0;
	foreach ($arrObservaciones as $oObs)
	{
		$count += strlen($oObs);
		if ($count > 50)
		{				
			$count = strlen($oObs);
			$oPdf->Text($OffsetX + 2, $OffsetY + 6 + ($rows * 0.3), $str);
			$str = '';
			$rows++;
		}
		$str .= $oObs . ' ';
	}
	$oPdf->Text($OffsetX + 2, $OffsetY + 6 + ($rows * 0.3), $str);


}*/

$OffsetY -= 0.3;
$OffsetX += 0.7;

for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage('P', 'Legal');
	
	$oPdf->SetFont('Arial', '', 8);
	$lineaFechaG = 30.2 - 3.9 - 1.5;
	$oPdf->Text($OffsetX + 3.5, $OffsetY + $lineaFechaG, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaFechaG, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 12.2, $OffsetY + $lineaFechaG, date("Y", strtotime($oGestoria->FechaGestion)));
	
	$oPdf->Text($OffsetX + 11.9, $OffsetY + 30.2, $oCliente->RazonSocial);
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$oPdf->Text($OffsetX + 9.7, $OffsetY + 30.8, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 9.9, $OffsetY + 32, $oTipoDocumentoCondominio->Codigo . ' - ' . $oClienteCondominio->DocumentoNumero);
	}
	$oPdf->Text($OffsetX + 9.9, $OffsetY + 31.4, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage('P', 'Legal');
	
	$oPdf->SetFont('Arial', '', 8);
	$lineaFechaG = 30.2 - 3.9 - 1.5;
	$oPdf->Text($OffsetX + 3.5, $OffsetY + $lineaFechaG, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaFechaG, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 12.2, $OffsetY + $lineaFechaG, date("Y", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 13, $OffsetY + 30.1, $oCliente->RazonSocial);
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$oPdf->Text($OffsetX + 11.5, $OffsetY + 30.7, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 9.9, $OffsetY + 32, $oTipoDocumentoCondominio->Codigo . ' - ' . $oClienteCondominio->DocumentoNumero);
	}
	$oPdf->Text($OffsetX + 11.7, $OffsetY + 31.3, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	/*$oPdf->Text($OffsetX + 14, $OffsetY + 30.3, $oCliente->RazonSocial);
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 12, $OffsetY + 31, $oClienteCondominio->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 31.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);*/
}

for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage('P', 'Legal');
	
	$oPdf->SetFont('Arial', '', 8);
	$lineaFechaG = 30.2 - 3.9 - 1.5;
	$oPdf->Text($OffsetX + 3.5, $OffsetY + $lineaFechaG, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 5.5, $OffsetY + $lineaFechaG, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 12.2, $OffsetY + $lineaFechaG, date("Y", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 11.5, $OffsetY + 30.4, $oCliente->RazonSocial);
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$oPdf->Text($OffsetX + 10.3, $OffsetY + 31.1, $oClienteCondominio->RazonSocial);
		$oPdf->Text($OffsetX + 9.9, $OffsetY + 32.2, $oTipoDocumentoCondominio->Codigo . ' - ' . $oClienteCondominio->DocumentoNumero);
	}
	$oPdf->Text($OffsetX + 10.5, $OffsetY + 31.7, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	
	
	/*$oPdf->Text($OffsetX + 14, $OffsetY + 30.3, $oCliente->RazonSocial);
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
		$oPdf->Text($OffsetX + 12, $OffsetY + 31, $oClienteCondominio->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 31.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);*/
}


/* Pagina 2: imprimimos la cantidad de copias necesarias */
for ($i=1; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge) && !$oGestoria->CondominioConyuge)
	{
		/* obtenemos informacion del conyuge */
		$oEstadoCivilConyuge 	= $oEstadosCiviles->GetById($oPrendaConyuge->IdEstadoCivil);
		$oTipoDocumentoConyuge 	= $oTiposDocumento->GetById($oPrendaConyuge->DocumentoTipo);
		$oNacionalidadConyuge 	= $oPaises->GetById($oPrendaConyuge->IdNacionalidad);
		$oLocalidadConyuge 		= $oLocalidades->GetById($oPrendaConyuge->DomicilioIdLocalidad);
		$oPartidoConyuge 		= $oPartidos->GetById($oLocalidadConyuge->IdPartido);
		$oProvinciaConyuge 		= $oProvincias->GetById($oLocalidadConyuge->IdProvincia);

		$DomicilioConyuge = '';	
		$DomicilioConyuge.= ($oPrendaConyuge) ? $oPrendaConyuge->GetDomicilio() : '';
		if (($oLocalidadConyuge) && ($oProvinciaConyuge))
		{
			$DomicilioConyuge.= ' - ';	
			$DomicilioConyuge.= $oLocalidadConyuge->Nombre;	
			$DomicilioConyuge.= ', ';	
			$DomicilioConyuge.= $oProvinciaConyuge->Nombre;	
		}
		
		$oPdf->Text($OffsetX + 2.2, $OffsetY + 1.6, $oPrendaConyuge->RazonSocial);
		$oPdf->Text($OffsetX + 8.3, $OffsetY + 1.6, $oEstadoCivilConyuge->Nombre);
		$oPdf->Text($OffsetX + 12.5, $OffsetY + 1.6, $NacionalidadConyuge ? $NacionalidadConyuge : $oNacionalidadConyuge->Nombre);
		$oPdf->Text($OffsetX + 15.5, $OffsetY + 1.6, CalcularEdad($oPrendaConyuge->FechaNacimiento));
		$oPdf->Text($OffsetX + 1.9, $OffsetY + 2, $DomicilioConyuge);
		$oPdf->Text($OffsetX + 1, $OffsetY + 3.1, $oTipoDocumentoConyuge->Codigo . ' ' . $oPrendaConyuge->DocumentoNumero);
		//$oPdf->Text($OffsetX + 8.5, $OffsetY + 2.2, );
	}
	
	/* imprimimos los datos de los fiadores */
	if ($arrFiadores)
	{
		for ($j=0; $j<count($arrFiadores); $j++)
		{
			if ($j<2)
			{
				$oFiador = $arrFiadores[$j];
				
				
				
				if ($j==0) $y=5.9;
				if ($j==0) $y2=6.3;
				if ($j==0) $y3=6.7;
				if ($j==1) $y=11.9;
				if ($j==1) $y2=12.3;
				if ($j==1) $y3=12.7;
				
				if ($oFiador->Posicion == 2 && $j == 0)
				{
					$y=11.1;
					$y2=11.5;
					$y3=11.9;
				}
		
				/* obtenemos informacion del fiador */
				$oProfesionFiador 		= $oProfesiones->GetById($oFiador->IdProfesion);
				$oEstadoCivilFiador 	= $oEstadosCiviles->GetById($oFiador->IdEstadoCivil);
				$oTipoDocumentoFiador 	= $oTiposDocumento->GetById($oFiador->DocumentoTipo);
				$oNacionalidadFiador 	= $oPaises->GetById($oFiador->IdNacionalidad);
				$oLocalidadFiador 		= $oLocalidades->GetById($oFiador->DomicilioIdLocalidad);
				$oPartidoFiador 		= $oPartidos->GetById($oLocalidadFiador->IdPartido);
				$oProvinciaFiador 		= $oProvincias->GetById($oLocalidadFiador->IdProvincia);
				
				$DomicilioFiador = '';	
				$DomicilioFiador.= (($oFiador) ? $oFiador->GetDomicilio() : '');
				if (($oLocalidadFiador) && ($oProvinciaFiador))
				{
					$DomicilioFiador.= ' - ';	
					$DomicilioFiador.= $oLocalidadFiador->Nombre;	
					$DomicilioFiador.= ', ';	
					$DomicilioFiador.= $oProvinciaFiador->Nombre;	
				}
	
				if ($oFiador->Descripcion != '')
					$oPdf->Text($OffsetX + 1.9, $OffsetY + $y, $oFiador->RazonSocial . " (" . $oFiador->Descripcion . ")");
				else
					$oPdf->Text($OffsetX + 1.9, $OffsetY + $y, $oFiador->RazonSocial);
				$oPdf->Text($OffsetX + 13.9, $OffsetY + $y, $oEstadoCivilFiador->Nombre);
				
				$NacionalidadFiador = $_REQUEST['NacionalidadFiador' . $j];
				$oPdf->Text($OffsetX + 1.9, $OffsetY + $y2, $oProfesionFiador->Nombre);
				$oPdf->Text($OffsetX + 8.4, $OffsetY + $y2, $NacionalidadFiador ? $NacionalidadFiador : $oNacionalidadFiador->Nombre);
				$oPdf->Text($OffsetX + 12.3, $OffsetY + $y2, CalcularEdad($oFiador->FechaNacimiento));
		
				$oPdf->Text($OffsetX + 1.1, $OffsetY + $y3, $DomicilioFiador);
				$oPdf->Text($OffsetX + 13.1, $OffsetY + $y3, $oTipoDocumentoFiador->Codigo . ' ' . $oFiador->DocumentoNumero);
			}
		}
	}
} 

$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge) && !$oGestoria->CondominioConyuge)
	{
		/* obtenemos informacion del conyuge */
		$oEstadoCivilConyuge 	= $oEstadosCiviles->GetById($oPrendaConyuge->IdEstadoCivil);
		$oTipoDocumentoConyuge 	= $oTiposDocumento->GetById($oPrendaConyuge->DocumentoTipo);
		$oNacionalidadConyuge 	= $oPaises->GetById($oPrendaConyuge->IdNacionalidad);
		$oLocalidadConyuge 		= $oLocalidades->GetById($oPrendaConyuge->DomicilioIdLocalidad);
		$oPartidoConyuge 		= $oPartidos->GetById($oLocalidadConyuge->IdPartido);
		$oProvinciaConyuge 		= $oProvincias->GetById($oLocalidadConyuge->IdProvincia);

		$DomicilioConyuge = '';	
		$DomicilioConyuge.= ($oPrendaConyuge) ? $oPrendaConyuge->GetDomicilio() : '';
		if (($oLocalidadConyuge) && ($oProvinciaConyuge))
		{
			$DomicilioConyuge.= ' - ';	
			$DomicilioConyuge.= $oLocalidadConyuge->Nombre;	
			$DomicilioConyuge.= ', ';	
			$DomicilioConyuge.= $oProvinciaConyuge->Nombre;	
		}
		
		$oPdf->Text($OffsetX + 2.2, $OffsetY + 1.6, $oPrendaConyuge->RazonSocial);
		$oPdf->Text($OffsetX + 8.3, $OffsetY + 1.6, $oEstadoCivilConyuge->Nombre);
		$oPdf->Text($OffsetX + 12.5, $OffsetY + 1.6, $NacionalidadConyuge ? $NacionalidadConyuge : $oNacionalidadConyuge->Nombre);
		$oPdf->Text($OffsetX + 15.5, $OffsetY + 1.6, CalcularEdad($oPrendaConyuge->FechaNacimiento));
		$oPdf->Text($OffsetX + 1.9, $OffsetY + 2, $DomicilioConyuge);
		$oPdf->Text($OffsetX + 1, $OffsetY + 3.1, $oTipoDocumentoConyuge->Codigo . ' ' . $oPrendaConyuge->DocumentoNumero);
		//$oPdf->Text($OffsetX + 8.5, $OffsetY + 2.2, );
	}
	
	/* imprimimos los datos de los fiadores */
	if ($arrFiadores)
	{
		for ($j=0; $j<count($arrFiadores); $j++)
		{
			if ($j<2)
			{
				$oFiador = $arrFiadores[$j];
				
				
				
				if ($j==0) $y=5.7;
				if ($j==0) $y2=6.1;
				if ($j==0) $y3=6.5;
				if ($j==1) $y=11.9;
				if ($j==1) $y2=12.3;
				if ($j==1) $y3=12.7;
				
				if ($oFiador->Posicion == 2 && $j == 0)
				{
					$y=11.1;
					$y2=11.5;
					$y3=11.9;
				}
		
				/* obtenemos informacion del fiador */
				$oProfesionFiador 		= $oProfesiones->GetById($oFiador->IdProfesion);
				$oEstadoCivilFiador 	= $oEstadosCiviles->GetById($oFiador->IdEstadoCivil);
				$oTipoDocumentoFiador 	= $oTiposDocumento->GetById($oFiador->DocumentoTipo);
				$oNacionalidadFiador 	= $oPaises->GetById($oFiador->IdNacionalidad);
				$oLocalidadFiador 		= $oLocalidades->GetById($oFiador->DomicilioIdLocalidad);
				$oPartidoFiador 		= $oPartidos->GetById($oLocalidadFiador->IdPartido);
				$oProvinciaFiador 		= $oProvincias->GetById($oLocalidadFiador->IdProvincia);
				
				$DomicilioFiador = '';	
				$DomicilioFiador.= (($oFiador) ? $oFiador->GetDomicilio() : '');
				if (($oLocalidadFiador) && ($oProvinciaFiador))
				{
					$DomicilioFiador.= ' - ';	
					$DomicilioFiador.= $oLocalidadFiador->Nombre;	
					$DomicilioFiador.= ', ';	
					$DomicilioFiador.= $oProvinciaFiador->Nombre;	
				}
	
				if ($oFiador->Descripcion != '')
					$oPdf->Text($OffsetX + 1.9, $OffsetY + $y, $oFiador->RazonSocial . " (" . $oFiador->Descripcion . ")");
				else
					$oPdf->Text($OffsetX + 1.9, $OffsetY + $y, $oFiador->RazonSocial);
				$oPdf->Text($OffsetX + 13.9, $OffsetY + $y, $oEstadoCivilFiador->Nombre);
				
				$NacionalidadFiador = $_REQUEST['NacionalidadFiador' . $j];
				$oPdf->Text($OffsetX + 1.9, $OffsetY + $y2, $oProfesionFiador->Nombre);
				$oPdf->Text($OffsetX + 8.4, $OffsetY + $y2, $NacionalidadFiador ? $NacionalidadFiador : $oNacionalidadFiador->Nombre);
				$oPdf->Text($OffsetX + 12.3, $OffsetY + $y2, CalcularEdad($oFiador->FechaNacimiento));
		
				$oPdf->Text($OffsetX + 1.1, $OffsetY + $y3, $DomicilioFiador);
				$oPdf->Text($OffsetX + 13.1, $OffsetY + $y3, $oTipoDocumentoFiador->Codigo . ' ' . $oFiador->DocumentoNumero);
			}
		}
	}








/* Pagina 2: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 6.5, $OffsetY + 5.5, $oCliente->RazonSocial);
/*	if ($i <= 0) {
		$oPdf->Text($OffsetX + 6, $OffsetY + 29.2, $oCliente->DomicilioCalle);
		$oPdf->Text($OffsetX + 17, $OffsetY + 29.2, $oCliente->DomicilioNumero);
		$oPdf->Text($OffsetX + 4, $OffsetY + 29.7, $oLocalidad->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 29.7, $oProvincia->Nombre);
		
		
		
		
	} 
}*/

/* Pagina 4: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 13.3, $OffsetY + 2, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 2.5, $oPrenda->TasaEfectiva);
}

/*



/* Pagina 11: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPdf->Text($OffsetX + 9.5, $OffsetY + 3.5, $oCliente->RazonSocial);

	$y = 6.5;

	/* en caso de que el condominio este casado y no sea conyuge del titular... 
	if ((!$oGestoria->CondominioConyuge) && ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado))
	{
		if ($oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeCondominio))
		{
			/* obtenemos informacion del conyuge 
			$oEstadoCivilConyuge 	= $oEstadosCiviles->GetById($oPrendaConyuge->IdEstadoCivil);
			$oTipoDocumentoConyuge 	= $oTiposDocumento->GetById($oPrendaConyuge->DocumentoTipo);
			$oNacionalidadConyuge 	= $oPaises->GetById($oPrendaConyuge->IdNacionalidad);
			$oLocalidadConyuge 		= $oLocalidades->GetById($oPrendaConyuge->DomicilioIdLocalidad);
			$oPartidoConyuge 		= $oPartidos->GetById($oLocalidadConyuge->IdPartido);
			$oProvinciaConyuge 		= $oProvincias->GetById($oLocalidadConyuge->IdProvincia);
	
			$DomicilioConyuge = '';	
			$DomicilioConyuge.= ($oPrendaConyuge) ? $oPrendaConyuge->GetDomicilio() : '';
			if (($oLocalidadConyuge) && ($oProvinciaConyuge))
			{
				$DomicilioConyuge.= ' - ';	
				$DomicilioConyuge.= $oLocalidadConyuge->Nombre;	
				$DomicilioConyuge.= ', ';	
				$DomicilioConyuge.= $oProvinciaConyuge->Nombre;	
			}

			$Linea1 = utf8_decode("EL/LA Sr./Sra. ") . $oPrendaConyuge->RazonSocial;
			$Linea1.= utf8_decode(" Estado Civil ") . $oEstadoCivilConyuge->Nombre;
			$Linea1.= utf8_decode(" Nacionalidad ") . $oNacionalidadConyuge->Nombre;
			$Linea1.= utf8_decode(" Edad ") . CalcularEdad($oPrendaConyuge->FechaNacimiento);
			
			$Linea2 = utf8_decode("Domicilio ") . $DomicilioConyuge;
			$Linea2.= utf8_decode(", quien declara ser cónyuge del deudor");
			
			$Linea3 = utf8_decode("prendario presta su expreso consentimiento para la constitución de esta garantía,");
			$Linea3.= utf8_decode(" en los términos del rtículo 1277");
			
			$Linea4 = utf8_decode(" del Código Civil, declarando que conoce y acepta todas");
			$Linea4 = utf8_decode(" las obligaciones asumidas por el Deudor Prendario.");

			$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, "CONSENTIMIENTO CONYUGAL CONDOMINIO");
			$y+=1;
			$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $Linea1);
			$y+=0.3;
			$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $Linea2);
			$y+=0.3;
			$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $Linea3);
			$y+=0.3;
			$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $Linea4);
			$y+=2;
		}
	}

	/* observaciones 
	if ($oPrenda->Observaciones != '')
	{
		$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, 'OBSERVACIONES:');
		$y+=0.5;
		$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $oPrenda->Observaciones);
	}
}
*/
/*

/*
/* Pagina 4: imprimimos la cantidad de copias necesarias */
/* Pagina 4: imprimimos la cantidad de copias necesarias */

/* Pagina 4: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage('P', 'Legal');
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 31.4, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 32.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}
*/

/*
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
}*/

/* Pagina 6: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 29.8, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 31.3, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}


/* Pagina 8: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 24.5, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 25.9, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
} */


/* generamos el archivo */
$oPdf->AutoPrint(true, true);
/* generamos el archivo */
$oPdf->Output();


?>