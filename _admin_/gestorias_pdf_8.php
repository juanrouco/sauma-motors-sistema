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

$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

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
//$oPdf = new FPDF('P', 'cm', 'LEGAL');
$oPdf = new PDF_AutoPrint('P', 'cm', 'Legal');
$oPdf->Open();

/* Pagina 1: imprimimos la cantidad de copias necesarias */

$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	$PesosLetra = $oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos");
    $PesosLetra = wordwrap($PesosLetra, 30, '\n');
    $arrPesosLetra = explode('\n', $PesosLetra);
	
	$oPdf->Text($OffsetX + 18.2, $OffsetY + 5, 'FIJA.');
	$oPdf->Text($OffsetX + 16, $OffsetY + 5.4, 'BS. AS.');
	$oPdf->Text($OffsetX + 17, $OffsetY + 5.4, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 17.8, $OffsetY + 5.4, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 19.7, $OffsetY + 5.4, date("Y", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 7, $OffsetY + 5.6, number_format($oPrenda->FinanciacionCapital, 2));
	$oPdf->Text($OffsetX + 15, $OffsetY + 6.6, $arrPesosLetra[0]);
	if (count($arrPesosLetra) > 1) 
		$oPdf->Text($OffsetX + 5, $OffsetY + 7.4, $arrPesosLetra[1]);
	$oPdf->Text($OffsetX + 13.8, $OffsetY + 7.4, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 5, $OffsetY + 10.5, 'UN AUTOMOTOR MARCA:');
	$oPdf->Text($OffsetX + 8.5, $OffsetY + 10.5, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 10.3, $OffsetY + 10.5, 'MODELO:');
	$oPdf->Text($OffsetX + 12, $OffsetY + 10.5, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 5, $OffsetY + 11, 'TIPO:');
	$oPdf->Text($OffsetX + 6, $OffsetY + 11, $oTipoModelo->Nombre);
	
	$oPdf->Text($OffsetX + 11.5, $OffsetY + 11, 'CHASIS MARCA:');
	$oPdf->Text($OffsetX + 13.8, $OffsetY + 11, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 15.8, $OffsetY + 11, 'N:');
	$oPdf->Text($OffsetX + 16.1, $OffsetY + 11, $oUnidad->NumeroChasis);
	$oPdf->Text($OffsetX + 5, $OffsetY + 11.5, 'MOTOR MARCA:');
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 11.5, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 11.5, 'N:');
	$oPdf->Text($OffsetX + 10.4, $OffsetY + 11.5, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 13, $OffsetY + 11.5, 'COLOR:');
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 11.5, $oColor->Nombre);
	$oPdf->Text($OffsetX + 5, $OffsetY + 12, 'USO PRIVADO');
	$OffsetY += 0.7;
	$oPdf->Text($OffsetX + 11.7, $OffsetY + 12, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 5, $OffsetY + 12.5, $oPartido->Nombre);
	$oPdf->Text($OffsetX + 18, $OffsetY + 12.9, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 10.5, $OffsetY + 13.3, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 18, $OffsetY + 13.3, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 5, $OffsetY + 14.1, 'NINGUNO DE NINGUNA NATURALEZA');
//	$oPdf->Text($OffsetX + 3.5, $OffsetY + 13.9, 'ESTE CONTRATO Y SUS HOJAS CONTINUACION');
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 14.4, $oPrenda->CantidadCuotas);
	$oPdf->Text($OffsetX + 16.2, $OffsetY + 14.4, 'CUOTAS IGUALES Y');
	$oPdf->Text($OffsetX + 5, $OffsetY + 14.9, 'CONSECUTIVAS DE');
	$oPdf->Text($OffsetX + 8, $OffsetY + 14.9, number_format($oPrenda->ImporteCuota, 2));
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 14.9, 'C/U');
/*	$oPdf->Text($OffsetX + 3.7, $OffsetY + 15.5, date("d", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 5.3, $OffsetY + 15.5, date("m", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 8.7, $OffsetY + 15.5, date("Y", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));*/
	$oPdf->Text($OffsetX + 9, $OffsetY + 15.3, 'LA PRIMERA VENCE EL');
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 15.3, date("d/m/y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 14, $OffsetY + 15.3, ' Y LAS DEMAS EL MISMO DIA');
	$oPdf->Text($OffsetX + 5, $OffsetY + 15.8, ' DE LOS MESES SUCESIVOS');
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 16.1, $oPrenda->TasaNominal);
/*	$oPdf->Text($OffsetX + 15.8, $OffsetY + 16.7, $oPrenda->TasaEfectiva);
	$oPdf->Text($OffsetX + 5.7, $OffsetY + 17.1, $oPrenda->CostoFinancieroTotal); */
	$OffsetY -= 0.5;
	$oPdf->Text($OffsetX + 17.6, $OffsetY + 17.6, 'EN TRAMITE');
	$oPdf->Text($OffsetX + 8.7, $OffsetY + 19.3, 'ACREEDOR'); 
	
//	$oPdf->Text($OffsetX + 7, $OffsetY + 20.6, $oAcreedor->NumeroInscripcion);
	$OffsetY += 0.3;
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 19.8, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 20.6, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 18, $OffsetY + 20.6, $oProfesion->Nombre);
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 21.0, $oNacionalidad->Nombre);
	$oPdf->Text($OffsetX + 19, $OffsetY + 21.0, $edad);
	$oPdf->SetFont('Arial', '', 4);
	$oPdf->Text($OffsetX + 14.1, $OffsetY + 21.3, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 13.9, $OffsetY + 21.5, $oPartido->Nombre . ' , ' . $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 15.9, $OffsetY + 21.5, ', ' . $oProvincia->Nombre);
	$oPdf->SetFont('Arial', '', 8);
	$oPdf->Text($OffsetX + 16.5, $OffsetY + 21.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	$oPdf->Text($OffsetX + 15, $OffsetY + 22.2, $oCliente->ClaveFiscalNumero);
$OffsetY -= 0.5;
for ($i=1; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	$PesosLetra = $oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos");
    $PesosLetra = wordwrap($PesosLetra, 30, '\n');
    $arrPesosLetra = explode('\n', $PesosLetra);
	
	$oPdf->Text($OffsetX + 18.2, $OffsetY + 5.4 - 0.5, 'FIJA.');
	$oPdf->Text($OffsetX + 16, $OffsetY + 5.8 - 0.5, 'BS. AS.');
	$oPdf->Text($OffsetX + 17, $OffsetY + 5.8 - 0.5, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 17.8, $OffsetY + 5.8 - 0.5, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 19.5, $OffsetY + 5.8 - 0.5, date("Y", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 7, $OffsetY + 6 - 0.5, number_format($oPrenda->FinanciacionCapital, 2));
	$oPdf->Text($OffsetX + 15, $OffsetY + 7 - 0.5, $arrPesosLetra[0]);
	if (count($arrPesosLetra) > 1) 
		$oPdf->Text($OffsetX + 5, $OffsetY + 7.8 - 0.5, $arrPesosLetra[1]);
	$oPdf->Text($OffsetX + 13.8, $OffsetY + 7.8 - 0.5, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 5, $OffsetY + 10.5 - 0.5, 'UN AUTOMOTOR MARCA:');
	$oPdf->Text($OffsetX + 8.5, $OffsetY + 10.5 - 0.5, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 10.3, $OffsetY + 10.5 - 0.5, 'MODELO:');
	$oPdf->Text($OffsetX + 12, $OffsetY + 10.5 - 0.5, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 5, $OffsetY + 11 - 0.5, 'TIPO:');
	$oPdf->Text($OffsetX + 6, $OffsetY + 11 - 0.5, $oTipoModelo->Nombre);
	
	$oPdf->Text($OffsetX + 11.5, $OffsetY + 11 - 0.5, 'CHASIS MARCA:');
	$oPdf->Text($OffsetX + 13.8, $OffsetY + 11 - 0.5, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 15.8, $OffsetY + 11 - 0.5, 'N:');
	$oPdf->Text($OffsetX + 16.1, $OffsetY + 11 - 0.5, $oUnidad->NumeroChasis);
	$oPdf->Text($OffsetX + 5, $OffsetY + 11.5 - 0.5, 'MOTOR MARCA:');
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 11.5 - 0.5, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 11.5 - 0.5, 'N:');
	$oPdf->Text($OffsetX + 	10, $OffsetY + 11.5 - 0.5, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 12, $OffsetY + 11.5 - 0.5, 'COLOR:');
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 11.5 - 0.5, $oColor->Nombre);
	$oPdf->Text($OffsetX + 55, $OffsetY + 12 - 0.5, 'USO PRIVADO');
	
	$oPdf->Text($OffsetX + 11.7, $OffsetY + 12.1, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 5, $OffsetY + 13, $oPartido->Nombre);
	$oPdf->Text($OffsetX + 18, $OffsetY + 13, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 10.5, $OffsetY + 13.5, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 18, $OffsetY + 13.5, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 5, $OffsetY + 14.5, 'NINGUNO DE NINGUNA NATURALEZA');
//	$oPdf->Text($OffsetX + 3.5, $OffsetY + 13.9, 'ESTE CONTRATO Y SUS HOJAS CONTINUACION');
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 14.8, $oPrenda->CantidadCuotas);
	$oPdf->Text($OffsetX + 16.2, $OffsetY + 14.8, 'CUOTAS IGUALES Y');
	$oPdf->Text($OffsetX + 5, $OffsetY + 15.2, 'CONSECUTIVAS DE');
	$oPdf->Text($OffsetX + 8, $OffsetY + 15.2, number_format($oPrenda->ImporteCuota, 2));
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 15.2, 'C/U');
/*	$oPdf->Text($OffsetX + 3.7, $OffsetY + 15.5, date("d", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 5.3, $OffsetY + 15.5, date("m", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 8.7, $OffsetY + 15.5, date("Y", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));*/
	$oPdf->Text($OffsetX + 9, $OffsetY + 15.5, 'LA PRIMERA VENCE EL');
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 15.5, date("d/m/y", strtotime($oPrenda->FechaVencimientoPrimerCuota)));
	$oPdf->Text($OffsetX + 14, $OffsetY + 15.5, ' Y LAS DEMAS EL MISMO DIA');
	$oPdf->Text($OffsetX + 5, $OffsetY + 16.0, ' DE LOS MESES SUCESIVOS');
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 16.3, $oPrenda->TasaNominal);
/*	$oPdf->Text($OffsetX + 15.8, $OffsetY + 16.7, $oPrenda->TasaEfectiva);
	$oPdf->Text($OffsetX + 5.7, $OffsetY + 17.1, $oPrenda->CostoFinancieroTotal); */
	$oPdf->Text($OffsetX + 17.6, $OffsetY + 17.6, 'EN TRAMITE');
	$oPdf->Text($OffsetX + 8.7, $OffsetY + 19.1, 'ACREEDOR'); 
	
//	$oPdf->Text($OffsetX + 7, $OffsetY + 20.6, $oAcreedor->NumeroInscripcion);
	
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 19.8, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 20.6, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 18, $OffsetY + 20.6, $oProfesion->Nombre);
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 21.0, $oNacionalidad->Nombre);
	$oPdf->Text($OffsetX + 19, $OffsetY + 21.0, $edad);
	$oPdf->SetFont('Arial', '', 4);
	$oPdf->Text($OffsetX + 14.1, $OffsetY + 21.3, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 13.9, $OffsetY + 21.5, $oPartido->Nombre . ' , ' . $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 15.9, $OffsetY + 21.5, ', ' . $oProvincia->Nombre);
	$oPdf->SetFont('Arial', '', 8);
	$oPdf->Text($OffsetX + 16.5, $OffsetY + 21.8, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	$oPdf->Text($OffsetX + 15, $OffsetY + 22.2, $oCliente->ClaveFiscalNumero);
	
}

/* Pagina 3: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 6.5, $OffsetY + 5.1, $oCliente->RazonSocial);
	
		$oPdf->Text($OffsetX + 6, $OffsetY + 29.5, $oCliente->DomicilioCalle);
		$oPdf->Text($OffsetX + 17, $OffsetY + 29.5, $oCliente->DomicilioNumero);
		$oPdf->Text($OffsetX + 4, $OffsetY + 30, $oLocalidad->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 30, $oProvincia->Nombre);
}



/* Pagina 4: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 13.15, $OffsetY + 1.5, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 1.9, $oPrenda->CostoFinancieroTotal);
	$oPdf->Text($OffsetX + 1.3, $OffsetY + 1.9, $oPrenda->TasaEfectiva);
	
	/* imprimimos los datos de los fiadores */
	if ($arrFiadores)
	{
		for ($j=0; $j<1; $j++)
		{
			if ($j<2)
			{
				$oFiador = $arrFiadores[$j];
				
				
				
				$y=18.3;
				$y2=18.7;
				
		
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
	
				$oPdf->Text($OffsetX + 8.5, $OffsetY + $y, $oFiador->RazonSocial);
				$oPdf->Text($OffsetX + 18.3, $OffsetY + $y, $oFiador->DocumentoNumero);
				$oPdf->Text($OffsetX + 3.9, $OffsetY + $y2, $oFiador->DomicilioCalle);
				$oPdf->Text($OffsetX + 8.6, $OffsetY + $y2, $oFiador->DomicilioNumero);
				$oPdf->Text($OffsetX + 11.6, $OffsetY + $y2, $oLocalidadFiador->Nombre);
				$oPdf->Text($OffsetX + 17.1, $OffsetY + $y2, $oProvinciaFiador->Nombre);
				/*$oPdf->Text($OffsetX + 13.9, $OffsetY + $y, $oEstadoCivilFiador->Nombre);
				
				$oPdf->Text($OffsetX + 1.9, $OffsetY + $y2, $oProfesionFiador->Nombre);
				$oPdf->Text($OffsetX + 8.4, $OffsetY + $y2, $oNacionalidadFiador->Nombre);
				$oPdf->Text($OffsetX + 12.6, $OffsetY + $y2, CalcularEdad($oFiador->FechaNacimiento));*/
		
				//$oPdf->Text($OffsetX + 1.1, $OffsetY + $y3, $DomicilioFiador);
				
			}
		}
	}
}



/* Pagina 2: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	/*$oPdf->Text($OffsetX + 6.5, $OffsetY + 5.5, $oCliente->RazonSocial);
/*	if ($i <= 0) {
		$oPdf->Text($OffsetX + 6, $OffsetY + 29.2, $oCliente->DomicilioCalle);
		$oPdf->Text($OffsetX + 17, $OffsetY + 29.2, $oCliente->DomicilioNumero);
		$oPdf->Text($OffsetX + 4, $OffsetY + 29.7, $oLocalidad->Nombre);
		$oPdf->Text($OffsetX + 12, $OffsetY + 29.7, $oProvincia->Nombre);
		
		
		
		
	} */
}


/*
/* Pagina 9: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 3.5, $oCliente->RazonSocial);
}


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
/* Pagina 2: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge))
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
		
		$oPdf->Text($OffsetX + 2.7, $OffsetY + 1.9, $oPrendaConyuge->RazonSocial);
		$oPdf->Text($OffsetX + 9, $OffsetY + 1.9, $oEstadoCivilConyuge->Nombre);
		$oPdf->Text($OffsetX + 12.8, $OffsetY + 1.9, $oNacionalidadConyuge->Nombre);
		$oPdf->Text($OffsetX + 15.7, $OffsetY + 1.9, CalcularEdad($oPrendaConyuge->FechaNacimiento));
		$oPdf->Text($OffsetX + 2.5, $OffsetY + 2.2, $DomicilioConyuge);
		$oPdf->Text($OffsetX + 8.5, $OffsetY + 2.2, $oTipoDocumentoConyuge->Codigo . ' ' . $oPrendaConyuge->DocumentoNumero);
	}
	*/
	/* imprimimos los datos de los fiadores 
	if ($arrFiadores)
	{
		for ($j=0; $j<count($arrFiadores); $j++)
		{
			if ($j<2)
			{
				$oFiador = $arrFiadores[$j];
				
				if ($j==0) $y=5.2;
				if ($j==1) $y=10.6;
		
				/* obtenemos informacion del fiador
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
					$oPdf->Text($OffsetX + 4.5, $OffsetY + $y, "(" . $oFiador->Descripcion . ")");
				
				$y+=0.7;
				$oPdf->Text($OffsetX + 2, $OffsetY + $y, $oFiador->RazonSocial);
				$oPdf->Text($OffsetX + 14.2, $OffsetY + $y, $oEstadoCivilFiador->Nombre);
				
				$y+=0.3;
				$oPdf->Text($OffsetX + 2.5, $OffsetY + $y, $oProfesionFiador->Nombre);
				$oPdf->Text($OffsetX + 9.2, $OffsetY + $y, $oNacionalidadFiador->Nombre);
				$oPdf->Text($OffsetX + 13, $OffsetY + $y, CalcularEdad($oFiador->FechaNacimiento));
		
				$y+=0.3;
				$oPdf->Text($OffsetX + 1, $OffsetY + $y, $DomicilioFiador);
				$oPdf->Text($OffsetX + 13.8, $OffsetY + $y, $oTipoDocumentoFiador->Codigo . ' ' . $oFiador->DocumentoNumero);
			}
		}
	}
} */
/*
/* Pagina 4: imprimimos la cantidad de copias necesarias 
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 29.5, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 30.9, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}


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
//$oPdf->Output('contrato_prenda.pdf', 'D');
$oPdf->AutoPrint(true, true);
/* generamos el archivo */
$oPdf->Output();

?>