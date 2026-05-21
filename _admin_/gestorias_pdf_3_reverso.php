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
$Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oComprobante->Numero;

/* comenzamos la creacion del archivo pdf */
$oPdf = new FPDF('P', 'cm', 'A4');

$oPdf->AddPage();

$oPdf->SetFont('Arial', '', 8);

/* Titular */
if ($oMinuta->Condominio)
{
	/* obtenemos los datos del cliente */
	if (!$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio))
		$oPdf->Text($OffsetX + 3, $OffsetY + 9, $oCliente->RazonSocial);
	else
		$oPdf->Text($OffsetX + 3, $OffsetY + 9, $oCliente->RazonSocial . ' | ' . $oClienteCondominio->RazonSocial);
		
} 
else
{
	$oPdf->Text($OffsetX + 3, $OffsetY + 9, $oCliente->RazonSocial);
}

if ($Observaciones)
{
	$arrObservaciones = explode(' ', $Observaciones);
	$str = '';
	$count = 0;
	$rows = 0;
	foreach ($arrObservaciones as $oObs)
	{
		$count += strlen($oObs);
		if ($count > 80)
		{				
			$count = strlen($oObs);
			$oPdf->Text($OffsetX + 2, $OffsetY + 19.5 + ($rows * 0.3), $str);
			$str = '';
			$rows++;
		}
		$str .= $oObs . ' ';
	}
	$oPdf->Text($OffsetX + 2, $OffsetY + 19.5 + ($rows * 0.3), $str);
	
}

/* generamos el archivo */
$oPdf->Output('titulo_automotor.pdf', 'D');

?>