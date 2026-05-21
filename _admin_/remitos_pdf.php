<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdRemito = intval($_REQUEST['IdRemito']);

$oRemitos 				= new Remitos();
$oComprobantes 			= new Comprobantes();
$oGestorias 			= new Gestorias();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oProfesiones 			= new Profesiones();
$oUnidades 				= new Unidades();
$oModelos 				= new Modelos();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oColores 				= new Colores();
$oMarcas 				= new Marcas();
$oTiposModelo 			= new TiposModelo();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* obtenemos los datos de la factura */
if (!$oRemito = $oRemitos->GetById($IdRemito))
	exit();

/* obtenemos los datos del comprobante de pago */
if (!$oComprobante = $oComprobantes->GetById($oRemito->IdComprobante))
	exit();
/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oRemito->IdMinuta))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);

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
$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);

/* obtenemos los datos de la planilla de recepcion */
$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);

/* determinamos el codigo de llaves si la planilla de recepcion se encuentra aprobada */
$CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : '';

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');



$oPdf->SetFont('Arial', '', 10);
$y = -0.5;
for ($i = 0; $i < 2; $i++)
{
	$oPdf->AddPage();
	//$oPdf->Text(17.5, 3.5 + $y, CambiarFecha($oRemito->Fecha));
	$Fecha = CambiarFecha($oRemito->Fecha);
	$arrFecha = explode('-', $Fecha);
	$oPdf->Text(15.6, 3.4 + $yAlt, $arrFecha[0]);
	$oPdf->Text(16.9, 3.4 + $yAlt, $arrFecha[1]);
	$oPdf->Text(18, 3.4 + $yAlt, $arrFecha[2]);

	/* datos del cliente */
	
	$oPdf->Text(2.9, 4.8 + 2.2 + $y, $oCliente->RazonSocial);
	$oPdf->Text(2.9,  4.6 + 3.4 + $y, $oCliente->GetDomicilio());
	//$oPdf->Text(4, 7.25 + $y, $oCliente->Telefono);
	$CodigoPostal = $oLocalidad->CodigoPostal;
	if ($oCliente->DomicilioCodigoPostal)
		$CodigoPostal = $oCliente->DomicilioCodigoPostal;
	$oPdf->Text(10.5 + 2.9 + 0.5, 4.6 + 3.4 + $y, $oLocalidad->Nombre . ' - C.P.: ' . $CodigoPostal);
	//$oPdf->Text(12, 7.25 + $y, $oProvincia->Nombre);
	//$oPdf->Text(4, 7.6 + $y, $oTipoIva->Nombre);
	$oPdf->Text(10.5 + 2.9 + 0.5,  4.6 + 4.6 + $y, ClaveFiscalTipos::GetById($oCliente->ClaveFiscalTipo) . ': ' . $oCliente->ClaveFiscalNumero);

	/* datos del automotor */
	$oPdf->Text(1.6, 8.3 + 3.5 + $y, '1');
	
	if ($oModelo->IdCategoriaModelo >= 57 && $oModelo->IdCategoriaModelo <= 62)
		$oPdf->Text(3, 8.3 + 3.5 + $y, 'PRODUCTO DE FUERZA MARCA ' . $oMarcaVehiculo->Nombre);
	else
		$oPdf->Text(3, 8.3 + 3.5 + $y, 'MOTOCICLETA MARCA ' . $oMarcaVehiculo->Nombre . ' 0KM');
	$oPdf->Text(3, 9 + 3.5 + $y, 'Modelo:');
	$oPdf->Text(6.2, 9 + 3.5 + $y, $oModelo->DenominacionModelo);
	$oPdf->Text(3, 9.7 + 3.5 + $y, 'Nro. de Motor:');
	$oPdf->Text(6.2, 9.7 + 3.5 + $y, $oUnidad->NumeroMotor);
	//$oPdf->Text(11, 10.5 + $y, ' - ' . $oMarcaMotor->Nombre);
	$oPdf->Text(3, 10.4 + 3.5 + $y, 'Nro. de Chasis:');
	$oPdf->Text(6.2, 10.4 + 3.5 + $y, $oUnidad->NumeroChasis);
	//$oPdf->Text(11, 11.5 + $y, ' - ' . $oMarcaChasis->Nombre);
	$oPdf->Text(3, 11 + 3.5 + $y, utf8_decode('Modelo Año: ' . $oUnidad->Anio));
	//$oPdf->Text(3, 12 + $y, 'Color:');
	//$oPdf->Text(5.5, 12 + $y, $oColor->Nombre);
	//$oPdf->Text(13, 12 + $y, $oColor->Codigo);
	$oPdf->Text(3, 11.65 + 3.5 + $y, 'Nro. Int: ' . $oUnidad->IdUnidad);
	//$oPdf->Text(3, 12.5 + $y, 'Equipo:');
	//$oPdf->Text(5.5, 12.5 + $y, $oModelo->DenominacionComercial);

	$oPdf->SetFont('Arial', '', 8);
	/* texto aclarativo */
	
	if ($oModelo->IdCategoriaModelo >= 57 && $oModelo->IdCategoriaModelo <= 62)
	{
		$oPdf->Text(3, 13 + 3.5 + $y, utf8_decode('Recibo de conformidad el producto de fuerza en perfecto estado haciéndome responsable civil'));
		$oPdf->Text(3, 13.5 + 3.5 + $y, utf8_decode('y criminalmente, a partir de la fecha y hora más abajo indicadas, por cualquier'));
		$oPdf->Text(3, 14 + 3.5 + $y, utf8_decode('accidente, daño o perjuicio que pudiera ocasionar el automotor referido.'));
		$oPdf->Text(3, 14.5 + 3.5 + $y, utf8_decode('Dejando constancia que la Unidad no presenta detalles de chapa y / o pintura.'));
		$oPdf->Text(3, 15 + 3.5 + $y, utf8_decode('Asimismo, declaro conocer y aceptar los términos de la garantía que me fueron'));
		$oPdf->Text(3, 15.5 + 3.5 + $y, utf8_decode('informados.'));
	}
	else
	{
		$oPdf->Text(3, 13 + 3.5 + $y, utf8_decode('Recibo de conformidad el vehículo en perfecto estado haciéndome responsable civil'));
		$oPdf->Text(3, 13.5 + 3.5 + $y, utf8_decode('y criminalmente, a partir de la fecha y hora más abajo indicadas, por cualquier'));
		$oPdf->Text(3, 14 + 3.5 + $y, utf8_decode('accidente, daño o perjuicio que pudiera ocasionar el automotor referido.'));
		$oPdf->Text(3, 14.5 + 3.5 + $y, utf8_decode('Dejando constancia que la Unidad no presenta detalles de chapa y / o pintura.'));
		$oPdf->Text(3, 15 + 3.5 + $y, utf8_decode('Asimismo, declaro conocer y aceptar los términos de la garantía que me fueron'));
		$oPdf->Text(3, 15.5 + 3.5 + $y, utf8_decode('informados.'));/*
		$oPdf->Text(3, 16 + 3.5 + $y, utf8_decode('La moto se entrega con un voucher por el valor de un casco que el cliente retira'));
		$oPdf->Text(3, 16.5 + 3.5 + $y, utf8_decode('en este acto.'));*/
	}

	$oPdf->SetFont('Arial', '', 10);

	/* informacion del trasnporte */
	$oPdf->Text(11.95, 16.9 + 7.3 + $y, $oRemito->Transporte);
	$oPdf->Text(11.95, 17.9 + 7.8 + $y, ClaveFiscalTipos::GetById($oRemito->TransporteClaveFiscalTipo) . ': ' . utf8_decode($oRemito->TransporteClaveFiscalNumero));
}
/* generamos el archivo */
$oPdf->Output('remito.pdf', 'D');

?>