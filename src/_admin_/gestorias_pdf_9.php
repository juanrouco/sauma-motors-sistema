<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

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
$oPdf = new FPDF('P', 'cm', 'LEGAL');

/* Pagina 1: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 17.5, $OffsetY + 3.1, 'XXX');
//	$oPdf->Text($OffsetX + 16.5, $OffsetY + 5.4, 'XXXXX');
//	$oPdf->Text($OffsetX + 16.5, $OffsetY + 5.8, 'BS. AS.');
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 6.1, number_format($oPrenda->FinanciacionCapital, 2));
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 7, substr($oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos"), 0, 30));
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 7.8, substr($oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos"), 30, strlen($oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos"))));
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 7.9, $oCliente->RazonSocial);

	$Linea1 = "UN AUTOMOVIL, MARCA: " . $oMarcaVehiculo->Nombre;
	$Linea1.= "     ";
	$Linea1.= "MODELO: " . $oModelo->DenominacionModelo;
	$Linea2 = "MOTOR MARCA: " . $oMarcaMotor->Nombre;
	$Linea2.= "     ";
	$Linea2.= "NRO: " . $oUnidad->NumeroMotor;
	$Linea2.= "     ";
	$Linea2.= "MARCA CHASIS: " . $oMarcaChasis->Nombre;
	$Linea3 = "NRO: " . $oUnidad->NumeroChasis;
	$Linea3.= "     ";
	$Linea3.= "USO " . UsoTipos::GetById($oGestoria->IdTipoUso);
	$Linea3.= ", EN PERFECTO ESTADO DE FUNCIONAMIENTO: UNIDAD 0KM";
	$Linea4 = "TIPO: " . $oTipoModelo->Nombre;

	$oPdf->Text($OffsetX + 5, $OffsetY + 9.9, $Linea1);
	$oPdf->Text($OffsetX + 5, $OffsetY + 10.3, $Linea2);
	$oPdf->Text($OffsetX + 5, $OffsetY + 10.7, $Linea3);
	$oPdf->Text($OffsetX + 5, $OffsetY + 11.1, $Linea4);
	$oPdf->Text($OffsetX + 12, $OffsetY + 12.55, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 5, $OffsetY + 12.95, $oPartido->Nombre);
	$oPdf->Text($OffsetX + 18.5, $OffsetY + 13.3, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 13.75, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 18.5, $OffsetY + 13.7, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 5, $OffsetY + 14.5, "NINGUNO DE NINGUNA NATURALEZA");
	$oPdf->Text($OffsetX + 16, $OffsetY + 14.9, "EL PRESENTE CONTRATO");
	$oPdf->Text($OffsetX + 5, $OffsetY + 15.3, "Y SUS HOJAS CONTINUACION");
	$oPdf->Text($OffsetX + 9, $OffsetY + 15.65, "EN " . $oPrenda->CantidadCuotas . " CUOTAS MENSUALES Y CONSECUTIVAS DE  $ " . number_format($oPrenda->ImporteCuota, 2));
	$oPdf->Text($OffsetX + 5, $OffsetY + 16, utf8_decode("VENCIMIENTO 1° CUOTA ") . CambiarFecha($oPrenda->FechaVencimientoPrimerCuota));
	$oPdf->Text($OffsetX + 13, $OffsetY + 16.4, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 17.5, $OffsetY + 17.6, "EN TRAMITE");
	$oPdf->Text($OffsetX + 11, $OffsetY + 18.8, "EN TRAMITE");
	$oPdf->Text($OffsetX + 9, $OffsetY + 19.2, "ACREEDOR");
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 19.6, "ART 5 INC A");
	
	$oPdf->Text($OffsetX + 16, $OffsetY + 19.9, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 14.7, $OffsetY + 20.3, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 18.3, $OffsetY + 20.3, $oProfesion->Nombre);
	$oPdf->Text($OffsetX + 15, $OffsetY + 20.65, $oNacionalidad->Argentina);
	$oPdf->Text($OffsetX + 19.3, $OffsetY + 20.65, CalcularEdad($oCliente->FechaNacimiento));
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 21.1, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 17, $OffsetY + 21.5, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	$oPdf->Text($OffsetX + 15, $OffsetY + 21.9, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 22.3, "Localidad: " . $oLocalidad->Nombre . ' - ' . $oPartido->Nombre);
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 22.7, "Provincia: " . $oProvincia->Nombre);
}


/* Pagina 3: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 12);
	
	$oPdf->Text($OffsetX + 2.5, $OffsetY + 5.5, $oCliente->RazonSocial);

	$oPdf->SetFont('Arial', '', 8);

	$oPdf->Text($OffsetX + 6, $OffsetY + 29.8, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 16.5, $OffsetY + 29.7, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 3, $OffsetY + 30.3, $oLocalidad->Nombre . ' - ' . $oPartido->Nombre);
	$oPdf->Text($OffsetX + 11, $OffsetY + 30.2, $oProvincia->Nombre);
}


/* Pagina 2: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPdf->Text($OffsetX + 13, $OffsetY + 2, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 1.5, $OffsetY + 2.6, $oPrenda->TasaEfectiva);
	$oPdf->Text($OffsetX + 14, $OffsetY + 2.4, $oPrenda->CostoFinancieroTotal);
	$oPdf->Text($OffsetX + 16, $OffsetY + 6.1, "EN TRAMITE");
	
	$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge))
	{
		/* obtenemos informacion del conyuge */
		$oLocalidadConyuge 	= $oLocalidades->GetById($oPrendaConyuge->DomicilioIdLocalidad);
		$oPartidoConyuge 	= $oPartidos->GetById($oLocalidadConyuge->IdPartido);
		$oProvinciaConyuge 	= $oProvincias->GetById($oLocalidadConyuge->IdProvincia);

		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 14.3, $OffsetY + 18.2, 'XXX');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 15, $OffsetY + 18.2, 'XXX');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 15.7, $OffsetY + 18.2, 'XXX');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 16.5, $OffsetY + 18.2, 'XXX');

		$oPdf->Text($OffsetX + 18, $OffsetY + 18.2, $oPrendaConyuge->DocumentoNumero);
		$oPdf->Text($OffsetX + 4, $OffsetY + 18.6, $oPrendaConyuge->DocmicilioCalle);
		$oPdf->Text($OffsetX + 8.7, $OffsetY + 18.6, $oPrendaConyuge->DocmicilioNumero);
		$oPdf->Text($OffsetX + 11.5, $OffsetY + 18.6, $oLocalidadConyuge->Nombre . ' - ' . $oPartidoConyuge->Nombre);
		$oPdf->Text($OffsetX + 17, $OffsetY + 18.6, $oProvinciaConyuge->Nombre);
	}
}


/* generamos el archivo */
$oPdf->Output('contrato_standard_bank.pdf', 'D');

?>