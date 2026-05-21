<?php 

require_once('../inc_library.php');
require_once('../library/fpdf/fpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);
$Observaciones	= $_REQUEST['Observaciones'];

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0;

$oFormularios 		= new Formularios();
$oFacturaUnidades 	= new FacturaUnidades();
$oComprobantes 		= new Comprobantes();
$oGestorias 		= new Gestorias();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oLocalidades 		= new Localidades();
$oPartidos 			= new Partidos();
$oProvincias 		= new Provincias();
$oPaises 			= new Paises();
$oMarcas 			= new Marcas();
$oTiposModelo 		= new TiposModelo();
$oPrendas 			= new Prendas();

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
$oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oGestoria->IdMinuta);

/* obtenemos los datos del comprobante de pago */
$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante);

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
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

/* obtenemos la nacionalidad */
$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);

/* obtenemos los datos de la localidad */
$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

/* obtenemos los datos del partido */
$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);

/* obtenemos los datos de la provincia */
$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);

/* obtenemos informacion del condominio en caso de que existiera */
$oClienteCondominio 	= $oClientes->GetById($oGestoria->IdClienteCondominio);
$oLocalidadCondominio 	= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);
$oPartidoCondominio 	= $oPartidos->GetById($oLocalidadCondominio->IdPartido);
$oProvinciaCondominio 	= $oProvincias->GetById($oLocalidadCondominio->IdProvincia);

/* obtenemos los datos de la prenda en caso de que existiera */
$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria);

/* armamos el detalle del comprobante */
$Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oFacturaUnidad->NumeroComprobante;

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

/* Identificacion del Titular */
$oPdf->Text($OffsetX + 1.5, $OffsetY + 11.5, $oCliente->RazonSocial);
$oPdf->Text($OffsetX + 5.5, $OffsetY + 13, number_format($oGestoria->PorcentajeTitularidad, 2));
$oPdf->Text($OffsetX + 9, $OffsetY + 13, $oNacionalidad->Nombre);
$oPdf->Text($OffsetX + 4.5, $OffsetY + 14.2, $oCliente->DocumentoNumero);
$oPdf->Text($OffsetX + 9.5, $OffsetY + 14.2, $oCliente->DocumentoExpedido);
$oPdf->Text($OffsetX + 17.3, $OffsetY + 14.2, date("d", strtotime($oCliente->FechaNacimiento)));
$oPdf->Text($OffsetX + 18.2, $OffsetY + 14.2, date("m", strtotime($oCliente->FechaNacimiento)));
$oPdf->Text($OffsetX + 19.1, $OffsetY + 14.2, substr(date("Y", strtotime($oCliente->FechaNacimiento)), 2, 2));
$oPdf->Text($OffsetX + 3, $OffsetY + 15.2, $oCliente->EnteJuridicoOtorgacion . ' | ' . $oCliente->EnteJuridicoDatosInscripcion);
$oPdf->Text($OffsetX + 3, $OffsetY + 16.4, $oCliente->DomicilioCalle);
$oPdf->Text($OffsetX + 18, $OffsetY + 16.4, $oCliente->DomicilioNumero);
$oPdf->Text($OffsetX + 3.3, $OffsetY + 17.5, $oCliente->DomicilioPiso);
$oPdf->Text($OffsetX + 4.8, $OffsetY + 17.5, $oCliente->DomicilioDpto);
$oPdf->Text($OffsetX + 7, $OffsetY + 17.5, $oLocalidad->Nombre);
$oPdf->Text($OffsetX + 3, $OffsetY + 18.5, $oPartido->Nombre);
if ($oCliente->DomicilioCodigoPostal)
	$oPdf->Text($OffsetX + 11, $OffsetY + 18.5, $oCliente->DomicilioCodigoPostal);
else
	$oPdf->Text($OffsetX + 11, $OffsetY + 18.5, $oLocalidad->CodigoPostal);
	
$oPdf->Text($OffsetX + 13.5, $OffsetY + 18.5, $oProvincia->Nombre);
$oPdf->Text($OffsetX + 2.9, $OffsetY + 19, $oCliente->Nupcia);
$oPdf->Text($OffsetX + 4.5, $OffsetY + 19, $oCliente->ConyugeApellido . ' ' . $oCliente->ConyugeNombre);

if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
{
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 12.6, 'X');
}
else
{
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 13.1, 'X');
}

if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 15, 'X');
if ($oCliente->DocumentoTipo == TipoDocumento::LE)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 15.5, 'X');
if ($oCliente->DocumentoTipo == TipoDocumento::CI)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 16, 'X');
if ($oCliente->DocumentoTipo == TipoDocumento::LC)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 16.5, 'X');
if ($oCliente->DocumentoTipo == TipoDocumento::PA)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 17, 'X');

if ($oCliente->IdEstadoCivil == EstadoCivil::Soltero)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 18, 'X');
elseif ($oCliente->IdEstadoCivil == EstadoCivil::Casado)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 18.5, 'X');
elseif ($oCliente->IdEstadoCivil == EstadoCivil::Viudo)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 19, 'X');
elseif ($oCliente->IdEstadoCivil == EstadoCivil::Divorciado)
	$oPdf->Text($OffsetX + 0.5, $OffsetY + 19.5, 'X');

/* forma de adquisicion */
$oPdf->Text($OffsetX + 6.7, $OffsetY + 21, '1');
if ($oPrenda)
{
	$oPdf->Text($OffsetX + 16.5, $OffsetY + 21, 'X');
}
else
{
	$oPdf->Text($OffsetX + 17, $OffsetY + 21, 'X');
}
$oPdf->Text($OffsetX + 3, $OffsetY + 22, number_format($oFacturaUnidad->Total));
$oPdf->Text($OffsetX + 7.5, $OffsetY + 22, CambiarFecha($oFacturaUnidad->Fecha));
$oPdf->Text($OffsetX + 10.5, $OffsetY + 22, $Comprobante . ' ' . $oDatosEmpresa->RazonSocial);

if ($oPrenda)
{
	$oPdf->Text($OffsetX + 3, $OffsetY + 23.1, number_format($oPrenda->FinanciacionCapital));
	$oPdf->Text($OffsetX + 7.5, $OffsetY + 23.1, $oPrenda->CantidadCuotas);
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 23.1, CambiarFecha($oPrenda->FechaVencimientoPrimerCuota));
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 23.1, number_format($oPrenda->ImporteCuota, 2));
}

/* generamos el archivo */
$oPdf->Output('titulo_automotor.pdf', 'D');

?>