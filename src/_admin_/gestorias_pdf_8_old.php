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

/* obtenemos los datos del condominio */
$oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio);

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
	$PesosLetra = $oNumber->ValorEnLetras($oPrenda->FinanciacionCapital, "pesos");
	$PesosLetra = wordwrap($PesosLetra, 30, '\n');
	$arrPesosLetra = explode('\n', $PesosLetra);
	
	$oPdf->Text($OffsetX + 15.5, $OffsetY + 5.4, 'BS. AS.');
	$oPdf->Text($OffsetX + 17.2, $OffsetY + 5.4, date("d", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 18.5, $OffsetY + 5.4, date("m", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 20, $OffsetY + 5.4, date("Y", strtotime($oGestoria->FechaGestion)));
	$oPdf->Text($OffsetX + 6, $OffsetY + 6, number_format($oPrenda->FinanciacionCapital, 2));
	$oPdf->Text($OffsetX + 13, $OffsetY + 6.6, $arrPesosLetra[0]);
	if (count($arrPesosLetra) > 1) 
		$oPdf->Text($OffsetX + 5, $OffsetY + 6.6, $arrPesosLetra[1]);
	$oPdf->Text($OffsetX + 5, $OffsetY + 7.4, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 5.5, $OffsetY + 9, $oMarcaVehiculo->Nombre);
	$oPdf->Text($OffsetX + 9.1, $OffsetY + 9, $oTipoModelo->Nombre);
	$oPdf->Text($OffsetX + 12.7, $OffsetY + 9, $oModelo->DenominacionModelo);
	$oPdf->Text($OffsetX + 17, $OffsetY + 9, $oMarcaMotor->Nombre);
	$oPdf->Text($OffsetX + 3.7, $OffsetY + 9.4, $oUnidad->NumeroMotor);
	$oPdf->Text($OffsetX + 11, $OffsetY + 9.4, $oMarcaChasis->Nombre);
	$oPdf->Text($OffsetX + 17, $OffsetY + 9.4, $oUnidad->NumeroChasis);
	$oPdf->Text($OffsetX + 8.3, $OffsetY + 10.6, $oProvincia->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 10.6, $oPartido->Nombre);
	$oPdf->Text($OffsetX + 9, $OffsetY + 11.4, $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 12.5, $OffsetY + 11.4, $oCliente->DomicilioCalle);
	$oPdf->Text($OffsetX + 18, $OffsetY + 11.4, $oCliente->DomicilioNumero);
	$oPdf->Text($OffsetX + 3.5, $OffsetY + 12.9, 'ESTE CONTRATO Y SUS HOJAS CONTINUACION');
	$oPdf->Text($OffsetX + 14.3, $OffsetY + 13.3, $oPrenda->CantidadCuotas);
	$oPdf->Text($OffsetX + 3.3, $OffsetY + 13.7, number_format($oPrenda->ImporteCuota, 2));
	$oPdf->Text($OffsetX + 3.7, $OffsetY + 14.5, date("d", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 5.3, $OffsetY + 14.5, date("m", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 8.7, $OffsetY + 14.5, date("Y", strtotime($oPrenda->FechaVencimientoPrimeroCuota)));
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 15.7, $oPrenda->TasaNominal);
	$oPdf->Text($OffsetX + 15.8, $OffsetY + 15.7, $oPrenda->TasaEfectiva);
	$oPdf->Text($OffsetX + 5.7, $OffsetY + 16.1, $oPrenda->CostoFinancieroTotal);
	$oPdf->Text($OffsetX + 14.3, $OffsetY + 16.9, 'POLIZA EN TRAMITE');
	$oPdf->Text($OffsetX + 7, $OffsetY + 18.8, 'ACREEDOR');
	
	$oPdf->Text($OffsetX + 7, $OffsetY + 20.6, $oAcreedor->NumeroInscripcion);
	
	$oPdf->Text($OffsetX + 14.5, $OffsetY + 21, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 21.4, $oEstadoCivil->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 21.8, $oProfesion->Nombre);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 22.2, $oNacionalidad->Argentina);
	$oPdf->Text($OffsetX + 13.5, $OffsetY + 22.6, $oCliente->GetDomicilio());
	$oPdf->Text($OffsetX + 12, $OffsetY + 23, $oPartido->Nombre . ' - ' . $oLocalidad->Nombre);
	$oPdf->Text($OffsetX + 15, $OffsetY + 23.4, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
	$oPdf->Text($OffsetX + 11.5, $OffsetY + 23.8, 'PROVINCIA: ' . $oProvincia->Nombre);
}


/* Pagina 3: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 6.5, $OffsetY + 3.7, $oCliente->RazonSocial);
}


/* Pagina 5: Sin Impresion */


/* Pagina 7: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 5.5, $OffsetY + 9.8, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 4.5, $OffsetY + 10.3, $Domicilio);
	$oPdf->Text($OffsetX + 4, $OffsetY + 10.8, $oCliente->GetTelefono());
}


/* Pagina 9: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 9.5, $OffsetY + 3.5, $oCliente->RazonSocial);
}


/* Pagina 11: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPdf->Text($OffsetX + 9.5, $OffsetY + 3.5, $oCliente->RazonSocial);

	$y = 6.5;

	/* en caso de que el condominio este casado y no sea conyuge del titular... */
	if ((!$oGestoria->CondominioConyuge) && ($oClienteCondominio->IdEstadoCivil == EstadoCivil::Casado))
	{
		if ($oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeCondominio))
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

	/* observaciones */
	if ($oPrenda->Observaciones != '')
	{
		$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, 'OBSERVACIONES:');
		$y+=0.5;
		$oPdf->Text($OffsetX + 1.5, $OffsetY + $y, $oPrenda->Observaciones);
	}
}


/* Pagina 2: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);

	$oPrendaConyuge = $oPrendaConyuges->GetByKey($oPrenda->IdPrenda, GestoriaCreate::ConyugeTitular);

	if (($oCliente->IdEstadoCivil == EstadoCivil::Casado) && ($oPrendaConyuge))
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
		
		$oPdf->Text($OffsetX + 2.7, $OffsetY + 1.9, $oPrendaConyuge->RazonSocial);
		$oPdf->Text($OffsetX + 9, $OffsetY + 1.9, $oEstadoCivilConyuge->Nombre);
		$oPdf->Text($OffsetX + 12.8, $OffsetY + 1.9, $oNacionalidadConyuge->Nombre);
		$oPdf->Text($OffsetX + 15.7, $OffsetY + 1.9, CalcularEdad($oPrendaConyuge->FechaNacimiento));
		$oPdf->Text($OffsetX + 2.5, $OffsetY + 2.2, $DomicilioConyuge);
		$oPdf->Text($OffsetX + 8.5, $OffsetY + 2.2, $oTipoDocumentoConyuge->Codigo . ' ' . $oPrendaConyuge->DocumentoNumero);
	}
	
	/* imprimimos los datos de los fiadores */
	if ($arrFiadores)
	{
		for ($j=0; $j<count($arrFiadores); $j++)
		{
			if ($j<2)
			{
				$oFiador = $arrFiadores[$j];
				
				if ($j==0) $y=5.2;
				if ($j==1) $y=10.6;
		
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
}

/* Pagina 4: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 29.5, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 30.9, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}


/* Pagina 6: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 29.8, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 31.3, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}


/* Pagina 8: imprimimos la cantidad de copias necesarias */
for ($i=0; $i<$oTipoFormulario->CantidadCopias; $i++)
{
	$oPdf->AddPage();
	
	$oPdf->SetFont('Arial', '', 8);
	
	$oPdf->Text($OffsetX + 14, $OffsetY + 24.5, $oCliente->RazonSocial);
	$oPdf->Text($OffsetX + 12, $OffsetY + 25.9, $oTipoDocumento->Codigo . ' - ' . $oCliente->DocumentoNumero);
}


/* generamos el archivo */
$oPdf->Output('contrato_prenda.pdf', 'D');

?>