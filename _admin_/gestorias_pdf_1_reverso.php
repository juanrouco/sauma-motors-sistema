<?php 

require_once('../inc_library.php');
//require_once('../library/fpdf/fpdf.php');
require_once('../library/class.pdf_javascript.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 	= intval($_REQUEST['IdFormulario']);
$ImprimeLeyenda = intval($_REQUEST['ImprimeLeyenda']);
$Observaciones	= $_REQUEST['Observaciones'];
$OffsetX 		= floatval($_REQUEST['OffsetX']);
$OffsetY 		= floatval($_REQUEST['OffsetY']);

$OffsetX = ($OffsetX != '') ? $OffsetX : 0;
$OffsetY = ($OffsetY != '') ? $OffsetY : 0.3;

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

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

/* obtenemos los datos de condicion de iva del cliente */
$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);

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
$oPartidoCondominio 			= $oPartidos->GetById($oLocalidadCondominio->IdPartido);
$oProvinciaCondominio 			= $oProvincias->GetById($oLocalidadCondominio->IdProvincia);
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

for ($i=0; $i<1 /*$oTipoFormulario->CantidadCopias*/; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	$OffsetY -= 0.5;
	$oPdf->Text($OffsetX + 1.5, $OffsetY + 3.3, $oCliente->RazonSocial . ($oClienteCondominio ? ' Y ' . $oClienteCondominio->RazonSocial : ''));
	
	/* Observaciones */
	if ($ImprimeLeyenda)
	{
		$oPdf->SetFont('Arial', '', 7);
		$OffsetY -= 0.5;
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 5, utf8_decode('SI LA INSCRIPCION DEL DOMINIO DE ESTE'));
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 5.3, utf8_decode('AUTOMOTOR SE PRODUJERA EN EL AÑO ANTERIOR'));
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 5.6, utf8_decode('AL CONSIGNADO EN EL PRESENTE CERTIFICADO DE'));
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 5.9, utf8_decode('FABRICA COMO MODELO - AÑO, REGIRA A ESTOS'));
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 6.2, utf8_decode('EFECTOS EL AÑO DE SU INSCRIPCION TAL COMO LO'));
		$oPdf->Text($OffsetX + 1.8, $OffsetY + 6.5, utf8_decode('ESTABLECE LA RESOLUCION EX SIM 416/82.'));
		$OffsetY += 0.5;
	}
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 2, $OffsetY + 7.4, CambiarFecha($oFacturaUnidad->Fecha));
	
	/* titular */
	$OffsetY += 0.5;
	if ($oCliente->IdTipoPersona == PersonaTipos::PersonaFisica)
	{
		$oPdf->Text($OffsetX + 2, $OffsetY + 11.6, $oCliente->RazonSocial);
		if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 5.2, $OffsetY + 12.6, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 5.8, $OffsetY + 12.6, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 6.4, $OffsetY + 12.6, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 7.0, $OffsetY + 12.6, 'X');
		if ($oCliente->DocumentoTipo == TipoDocumento::PA)
			$oPdf->Text($OffsetX + 7.6, $OffsetY + 12.6, 'X');
		$oPdf->Text($OffsetX + 2, $OffsetY + 13.4, $oCliente->DocumentoNumero);
		$oPdf->Text($OffsetX + 5, $OffsetY + 13.4, $oCliente->DocumentoExpedido);
	}
	else
	{
		$oPdf->Text($OffsetX + 2, $OffsetY + 11.6, $oCliente->RepresentanteRazonSocial);
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 5.2, $OffsetY + 12.6, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 5.8, $OffsetY + 12.6, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 6.4, $OffsetY + 12.6, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 7.0, $OffsetY + 12.6, 'X');
		if ($oCliente->RepresentanteDocumentoTipo == TipoDocumento::PA)
			$oPdf->Text($OffsetX + 7.6, $OffsetY + 12.6, 'X');
		$oPdf->Text($OffsetX + 2, $OffsetY + 13.4, $oCliente->RepresentanteDocumentoNumero);
		$oTipoDocumento = $oTiposDocumento->GetById($oCliente->RepresentanteDocumentoTipo);
		$oPdf->Text($OffsetX + 5, $OffsetY + 13.4, $oTipoDocumento->Expedido);
	}
	
	/* condominio */
	if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
	{
		$oPdf->Text($OffsetX + 9.5, $OffsetY + 11.6, $oClienteCondominio->RazonSocial);
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::DNI)
			$oPdf->Text($OffsetX + 13.2, $OffsetY + 12.6, 'X');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LE)
			$oPdf->Text($OffsetX + 14, $OffsetY + 12.6, 'X');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::LC)
			$oPdf->Text($OffsetX + 14.6, $OffsetY + 12.6, 'X');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::CI)
			$oPdf->Text($OffsetX + 15.2, $OffsetY + 12.6, 'X');
		if ($oClienteCondominio->DocumentoTipo == TipoDocumento::PA)
			$oPdf->Text($OffsetX + 15.8, $OffsetY + 12.6, 'X');
		$oPdf->Text($OffsetX + 9.5, $OffsetY + 13.4, $oClienteCondominio->DocumentoNumero);
		$oPdf->Text($OffsetX + 13, $OffsetY + 13.4, $oClienteCondominio->DocumentoExpedido);
	}
	
	if ($Observaciones)
	{
		//$Observaciones = urldecode($Observaciones);
		$remove = array("\n", "\r\n", "\r");
		$Observaciones = str_replace($remove, ' ', $Observaciones);
		
		
	$oPdf->SetFont('Arial', '', 10);
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
				$oPdf->Text($OffsetX + 2, $OffsetY + 17.3 + ($rows * 0.3), $str);
				$str = '';
				$rows++;
			}
			$str .= $oObs . ' ';
		}
		$oPdf->Text($OffsetX + 2, $OffsetY + 17.3 + ($rows * 0.3), $str);
		$oPdf->SetFont('Arial', '', 8);
	}
	
	/* solicitud de cedulas azules */
	if ($arrGestoriaCedulas)
	{
		$x = 0.5;
		$oPdf->Text($OffsetX + 2, $OffsetY + 17.5, 'Se solicita cedula para autorizado a conducir a favor de:');
		foreach ($arrGestoriaCedulas as $oGestoriaCedula)
		{
			$oTipoDocumento = $oTiposDocumento->GetById($oGestoriaCedula->DocumentoTipo);
			
			$Data = '';
			$Data.= $oGestoriaCedula->Nombre . ' ' . $oGestoriaCedula->Apellido;
			$Data.= ' - ';
			$Data.= $oTipoDocumento->Codigo . ' - ' . $oGestoriaCedula->DocumentoNumero;
			
			$oPdf->Text($OffsetX + 2.5, $OffsetY + 17.5 + $x, $Data);
			$x += 0.5;
		}
	}
}

/* generamos el archivo */
//$oPdf->Output('formulario_01_importado_reverso.pdf', 'D');
$oPdf->AutoPrint(true);
/* generamos el archivo */
$oPdf->Output();


?>