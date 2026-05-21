<?php
require_once('class.configuracionfactura.php');
require_once('class.comprobantesafip.php');
require_once('class.logfacturaelectronica.php');
require_once('class.logsfacturaelectronica.php');
require_once('class.facturaelectronica.php');
require_once('class.clientes.php');
require_once('class.number.php');
require_once('class.comprobantes.php');
require_once('class.minuta.php');
require_once('class.minutas.php');
require_once('class.modelos.php');
require_once('class.unidades.php');
require_once('class.marcas.php');
require_once('class.tipomodelo.php');
require_once('class.colores.php');
require_once('class.comprobantes.php');

class FacturaElectronicaPDF
{
	private $path;
	private $PyFEPDF;
	private $tra;
	private $ta;
	private $cms;
	private $WSFacturacion;
	private $PuntoVenta;
	private $Moneda;
	private $ComprobanteAfip;
	private $ComprobanteAfipAsociado;
	
	public function __construct(ComprobanteAfip $oComprobanteAfip, $oComprobanteAfipAsociado = null)
	{	
		$this->path = getcwd()  . '\..\facturaelectronica';
		$this->WSFacturacion = 'PyFEPDF';
		$this->PuntoVenta = $oComprobanteAfip->PuntoVenta;
		$this->Moneda = ConstantesFacturaElectronica::Pesos;
		$this->ComprobanteAfip = $oComprobanteAfip;
		$this->ComprobanteAfipAsociado = $oComprobanteAfipAsociado;
	}

	private function AgregarImpuesto($IdTributoTipo, $Descripcion, $BaseImponible, $Importe)
	{
		$Alicuota = $Importe * 100 / $BaseImponible;
		$Alicuota = number_format($Alicuota, 2, '.', '');
		$ok = $this->PyFEPDF->AgregarTributo($IdTributoTipo, $Descripcion, $BaseImponible, $Alicuota, $Importe);
	}
	
	private function AgregarIva($IdTipoIva, $ImporteIva)
	{
		if ($IdTipoIva == Iva::Iva21)
		{
			$ImporteNeto = $ImporteIva / 0.21;
			$ImporteNeto = number_format($ImporteNeto, 2, '.', '');
			$IdIva = ConstantesFacturaElectronica::Iva21;
		}
		elseif ($IdTipoIva == Iva::Iva10)
		{
			$ImporteNeto = $ImporteIva / 0.105;
			$ImporteNeto = number_format($ImporteNeto, 2, '.', '');
			$IdIva = ConstantesFacturaElectronica::Iva10;
		}
			
		$ok = $this->PyFEPDF->AgregarIva($IdIva, $ImporteNeto, $ImporteIva);
	}
	
	public function CrearFactura(Cliente $oCliente, $arrDetalles, $Detalle = "")
	{
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->PyFEPDF = new COM($this->WSFacturacion); 
		print_R(1);
		// CUIT del emisor
		$this->PyFEPDF->CUIT = ConfiguracionFactura::Cuit;
		
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
    
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->PyFEPDF->CrearFactura(
			$this->ComprobanteAfip->IdConcepto, 
			$this->ComprobanteAfip->TipoDocumento, 
			$this->ComprobanteAfip->NumeroDocumento, 
			$this->ComprobanteAfip->IdTipoComprobanteAfip, 
			$this->ComprobanteAfip->PuntoVenta, 
			$this->ComprobanteAfip->Numero,
			$oComprobante->Importe,
			$this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->TotalGravado + $this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->ImporteIva,
			$this->ComprobanteAfip->ImporteImpuestos,
			$this->ComprobanteAfip->ImporteExento,
			str_replace('-', '', $this->ComprobanteAfip->Fecha),
			'', //Fecha Vencimiento de pago
			'', //Fecha Servicios desde
			'', //Fecha Servicios hasta
			$this->Moneda, 
			"1.000", // (deshabilitado por AFIP)
			$this->ComprobanteAfip->Cae,
			$this->ComprobanteAfip->VencimientoCae,
			$oCliente->GetIva(),    # usar categoria IVA factura A/B/C
			$oCliente->RazonSocial,
			$oCliente->GetDomicilio(),
			16, // código para exportación
			$Detalle, 
			'', //Observaciones generales
			'', // Forma de Pago
			"FOB", // termino de comercio exterior para exportación
			1, // Idioma comprobante
			'', 
			0 // Descuento
			);
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA  || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->PyFEPDF->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero
					);
			}
		}
			
		// Agrego impuestos percepción IIBB
		if ($this->ComprobanteAfip->ImportePercepcionIIBB && $this->ComprobanteAfip->ImportePercepcionIIBB > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosProvinciales, 'Perc. IIBB:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImportePercepcionIIBB);

		// Agrego impuestos internos
		if ($this->ComprobanteAfip->ImporteImpuestoInterno && $this->ComprobanteAfip->ImporteImpuestoInterno > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosInternos, 'Impuesto Interno:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImporteImpuestoInterno);

		// Agrego tasas de IVA
		if ($this->ComprobanteAfip->ImporteIva21 && $this->ComprobanteAfip->ImporteIva21 > 0)
			$this->AgregarIva(Iva::Iva21, $this->ComprobanteAfip->ImporteIva21);
		
		if ($this->ComprobanteAfip->ImporteIva10 && $this->ComprobanteAfip->ImporteIva10 > 0)
			$this->AgregarIva(Iva::Iva10, $this->ComprobanteAfip->ImporteIva10);
		
		foreach ($arrDetalles as $oDetalle)
		{
			// Agrego detalles de cada item de la factura:
			$Precio = 0;
			$IdIva = null;
			$ImporteIva = 0;
			if (true || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::FacturaA || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA)
			{
				if ($oDetalle->IvaGravado == 1)
					$Precio = $oDetalle->Importe / 1.21;               # precio neto (A) o iva incluido (B)
				elseif ($oDetalle->IvaGravado == 2)
					$Precio = $oDetalle->Importe / 1.105;               # precio neto (A) o iva incluido (B)
				else
					$Precio = $oDetalle->Importe;               # precio neto (A) o iva incluido (B)
			}
			else
				$Precio = $oDetalle->Importe;
			if ($oDetalle->IvaGravado == 1)
			{
				$IdIva = ConstantesFacturaElectronica::Iva21;                 # código para alícuota del 21%
				$ImporteIva = $Precio * 0.21;
			}
			elseif ($oDetalle->IvaGravado == 2)
			{
				$IdIva = ConstantesFacturaElectronica::Iva10;                 # código para alícuota del 21%
				$ImporteIva = $Precio * 0.105;
			}
			else
			{
				$IdIva = ConstantesFacturaElectronica::Iva0;                 # código para alícuota del 21%
				$ImporteIva = 0;
			}
			
			$importe = $oDetalle->Importe;              # importe total del item
			$importe = $oDetalle->Importe;              # importe total del item
			$despacho = "";     # numero de despacho de importación
			$DatoA = "";          # primer dato adicional del item
			$DatoB = "";
			$DatoC = "";
			$DatoD = "";
			$DatoE = "";           # ultimo dato adicional del item
			$resultado = $this->PyFEPDF->AgregarDetalleItem(
				1,  //Unidades
				"", // Codigo de barras
				"", // Codigo
				$oDetalle->Detalle, 
				1, //Cantidad
				7, //Codigo de medida (7 para unidades)
				$Precio, 
				0, //Bonificacion
				$IdIva, 
				$ImporteIva, 
				$oDetalle->Importe, // Total item
				"", // Despacho
				$DatoA, 
				$DatoB, 
				$DatoC,
				$DatoD, 
				$DatoE
				);
		}
		
		// Agrego datos adicionales fijos:
		$ok = $this->PyFEPDF->AgregarDato("logo", $this->path . '\plantillas\logo.png');
		$ok = $this->PyFEPDF->AgregarDato("EMPRESA", ConfiguracionFactura::RazonSocial);
		if ($this->ComprobanteAfip->PuntoVenta == 5) {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::DireccionAlt);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::DireccionAlt2);
		} else {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::Direccion);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::Direccion2);
		}
		$ok = $this->PyFEPDF->AgregarDato("MEMBRETE3", ConfiguracionFactura::Fax);
		$ok = $this->PyFEPDF->AgregarDato("CUIT", "CUIT: " . ConfiguracionFactura::CuitLetras);
		$ok = $this->PyFEPDF->AgregarDato("IIBB", "I. Brutos: " . ConfiguracionFactura::IIBB);
		$ok = $this->PyFEPDF->AgregarDato("IVA", "IVA Responsable Inscripto");
		$ok = $this->PyFEPDF->AgregarDato("INICIO", "Inicio de Actividades: " . ConfiguracionFactura::FechaInicioActividad);
		$ok = $this->PyFEPDF->AgregarDato("ClienteLocalidad", $oCliente->GetLocalidad());
		$ok = $this->PyFEPDF->AgregarDato("ClienteProvincia", $oCliente->GetProvincia());
		$ok = $this->PyFEPDF->AgregarDato("ClienteTelefono", $oCliente->GetTelefono());

		if ($oCliente->IdTipoIva == TipoIva::MO)
		{
			$Comentarios = wordwrap(utf8_decode(ConfiguracionFactura::LeyendaMonotributo), 80, '\n');
			$arrComentarios = explode('\n', $Comentarios);
			$ok = $this->PyFEPDF->AgregarDato("Comentarios", $arrComentarios[0]);
			for ($i = 1; $i < count($arrComentarios); $i++)
			{
				$j = $i + 1;
				$ok = $this->PyFEPDF->AgregarDato("Comentarios" . $j, $arrComentarios[$i]);
			}
		}
		$oNumber = new Number();
		$ok = $this->PyFEPDF->AgregarDato("TotalLetras", "Son pesos: ". $oNumber->ValorEnLetras($this->ComprobanteAfip->Total, "pesos"));
		// Cargo el formato desde el archivo CSV (opcional)
		// (carga todos los campos a utilizar desde la planilla)
		$ok = $this->PyFEPDF->CargarFormato($this->path . '\plantillas\factura.csv');
		                
		// Creo plantilla para esta factura (papel A4 vertical):
		$ok = $this->PyFEPDF->CrearPlantilla("A4", "portrait");
		
		// Proceso la plantilla
		$ok = $this->PyFEPDF->ProcesarPlantilla(2, 24, "izq");
		// Genero el PDF de salida según la plantilla procesada
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		$FileName = 'factura ' . $oComprobante->IdComprobante  . '.pdf';
		$Salida = getcwd()  . Comprobante::PathFileFisico . '\\' . $FileName;
		$ok = $this->PyFEPDF->GenerarPDF($Salida);
		
		$oComprobante->Archivo = $FileName;
		$oComprobantes->Update($oComprobante);
		
		// Abro el visor de PDF y muestro lo generado
		// (es necesario tener instalado Acrobat Reader o similar)
		$imprimir = false; # cambiar a True para que lo envie directo a la impresora
		$ok = $this->PyFEPDF->MostrarPDF($Salida, $imprimir);
	}
	
	public function CrearFacturaMinuta(Minuta $oMinuta, $oFacturaUnidad)
	{
		$oClientes		= new Clientes();
		$oUnidades		= new Unidades();
		$oModelos		= new Modelos();
		$oMarcas		= new Marcas();
		$oTiposModelo	= new TiposModelo();
		$oColores		= new Colores();
		
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->PyFEPDF = new COM($this->WSFacturacion); 
		
		// CUIT del emisor
		$this->PyFEPDF->CUIT = ConfiguracionFactura::Cuit;
		
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		
		$oCliente = $oClientes->GetById($oComprobante->IdCliente);
		$NombreCliente = $oCliente->RazonSocial;
		$NumeroDocumento = $this->ComprobanteAfip->NumeroDocumento;
		if ($oMinuta->Condominio)
		{
			/* obtenemos los datos del cliente */
			$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
			$NombreCliente = $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial;
			$NumeroDocumento .= ' / ' . ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero;
		}
		elseif ($oFacturaUnidad->OtrosTitulares != '')
		{
			$NombreCliente = $oCliente->RazonSocial . ' Y ' . $oFacturaUnidad->OtrosTitulares;
		}
    
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->PyFEPDF->CrearFactura(
			$this->ComprobanteAfip->IdConcepto, 
			$this->ComprobanteAfip->TipoDocumento, 
			$NumeroDocumento, 
			$this->ComprobanteAfip->IdTipoComprobanteAfip, 
			$this->ComprobanteAfip->PuntoVenta, 
			$this->ComprobanteAfip->Numero,
			$oComprobante->Importe,
			$this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->TotalGravado + $this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->ImporteIva,
			$this->ComprobanteAfip->ImporteImpuestos,
			$this->ComprobanteAfip->ImporteExento,
			str_replace('-', '', $this->ComprobanteAfip->Fecha),
			'', //Fecha Vencimiento de pago
			'', //Fecha Servicios desde
			'', //Fecha Servicios hasta
			$this->Moneda, 
			"1.000", // (deshabilitado por AFIP)
			$this->ComprobanteAfip->Cae,
			$this->ComprobanteAfip->VencimientoCae,
			$oCliente->GetIva(),    # usar categoria IVA factura A/B/C
			$NombreCliente,
			$oCliente->GetDomicilio(),
			16, // código para exportación
			$oFacturaUnidad->Observaciones,
			$oFacturaUnidad->Observaciones, //Observaciones generales
			'', // Forma de Pago
			"FOB", // termino de comercio exterior para exportación
			1, // Idioma comprobante
			'', 
			0 // Descuento
			);
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA  || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->PyFEPDF->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero
					);
			}
		}
			
		// Agrego impuestos percepción IIBB
		if ($this->ComprobanteAfip->ImportePercepcionIIBB && $this->ComprobanteAfip->ImportePercepcionIIBB > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosProvinciales, 'Perc. IIBB:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImportePercepcionIIBB);

		// Agrego impuestos internos
		if ($this->ComprobanteAfip->ImporteImpuestoInterno && $this->ComprobanteAfip->ImporteImpuestoInterno > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosInternos, 'Impuesto Interno:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImporteImpuestoInterno);

		// Agrego tasas de IVA
		if ($this->ComprobanteAfip->ImporteIva21 && $this->ComprobanteAfip->ImporteIva21 > 0)
			$this->AgregarIva(Iva::Iva21, $this->ComprobanteAfip->ImporteIva21);
		
		if ($this->ComprobanteAfip->ImporteIva10 && $this->ComprobanteAfip->ImporteIva10 > 0)
			$this->AgregarIva(Iva::Iva10, $this->ComprobanteAfip->ImporteIva10);
		
		$oUnidad	= $oUnidades->GetById($oMinuta->IdUnidad);
		$oModelo	= $oModelos->GetById($oUnidad->IdModelo);
		$oMarca		= $oMarcas->GetById($oModelo->IdMarcaVehiculo);
		
		// Agrego detalles de cada item de la factura:
		$IdIva = null;
		$ImporteIva = 0;
		
		if (true || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::FacturaA || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA)
			$Precio = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno;
		else
			$Precio = $oComprobante->Subtotal - $oFacturaUnidad->ImpuestoInterno;
		
		if ($oModelo->Iva == 21)
		{
			$IdIva = ConstantesFacturaElectronica::Iva21;                 # código para alícuota del 21%
			$ImporteIva = $oComprobante->ImporteIva21;
		}
		elseif ($oModelo->Iva == 10.4)
		{
			$IdIva = ConstantesFacturaElectronica::Iva10;                 # código para alícuota del 10.5%
			$ImporteIva = $oComprobante->ImporteIva10;
		}
		else
		{
			$IdIva = ConstantesFacturaElectronica::Iva0;                 # código para alícuota del 21%
			$ImporteIva = 0;
		}
			
		$importe = $oComprobante->Importe - $oComprobante->ImpuestoInterno;              # importe total del item
		$despacho = "";     # numero de despacho de importación
		$DatoA = "";          # primer dato adicional del item
		$DatoB = "";
		$DatoC = "";
		$DatoD = "";
		$DatoE = "";           # ultimo dato adicional del item
		
		$oTipoModelo = $oModelo->GetTipoModelo();
		$TituloItem = $oTipoModelo->Nombre . " Marca " . $oMarca->Nombre . " 0KM";
		
		if (false)
		{
			$resultado = $this->PyFEPDF->AgregarDetalleItem(
				1,  //Unidades
				"", // Codigo de barras
				"", // Codigo
				$TituloItem, 
				1, //Cantidad
				7, //Codigo de medida (7 para unidades)
				82000, 
				0, //Bonificacion
				$IdIva, 
				$ImporteIva, 
				82000, // Total item
				"", // Despacho
				$DatoA, 
				$DatoB, 
				$DatoC,
				$DatoD, 
				$DatoE
				);
			$ok = $this->PyFEPDF->AgregarDato("Item_Precio09", '4.100,00');
			$ok = $this->PyFEPDF->AgregarDato("Item_Descripcion09", 'FORMULARIOS PARA PATENTAMIENTO');
		}
		else
		{
			$resultado = $this->PyFEPDF->AgregarDetalleItem(
				1,  //Unidades
				"", // Codigo de barras
				"", // Codigo
				$TituloItem, 
				1, //Cantidad
				7, //Codigo de medida (7 para unidades)
				$Precio, 
				0, //Bonificacion
				$IdIva, 
				$ImporteIva, 
				$oDetalle->Importe, // Total item
				"", // Despacho
				$DatoA, 
				$DatoB, 
				$DatoC,
				$DatoD, 
				$DatoE
				);
		}
		
		$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);
		$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor);
		$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis);
		$oColor = $oColores->GetById($oUnidad->IdColor);
		$ok = $this->PyFEPDF->AgregarDato("Descripcion2", 'Modelo: ' . $oModelo->DenominacionModelo);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion2", 'Tipo:         ' . $oTipoModelo->Nombre);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion22", $oUnidad->Anio);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion3", 'Modelo:       ' . $oModelo->DenominacionModelo);
		$ok = $this->PyFEPDF->AgregarDato("Descripcion3", 'Marca de Motor: ' . $oMarcaMotor->Nombre . ' - Nro. de Motor:       ' . $oUnidad->NumeroMotor);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion42", '- ' . $oMarcaMotor->Nombre);
		$ok = $this->PyFEPDF->AgregarDato("Descripcion4", 'Marca de Chasis: ' . $oMarcaChasis->Nombre . ' - Nro. de Chasis:       ' . $oUnidad->NumeroChasis);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion52", '- ' . $oMarcaChasis->Nombre);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion6", 'Color:       ' . $oColor->Nombre);
		$ok = $this->PyFEPDF->AgregarDato("Descripcion5", 'Nro. Int: ' . $oUnidad->IdUnidad);
		//$ok = $this->PyFEPDF->AgregarDato("Descripcion6", 'Modelo:       ' . $oModelo->DenominacionComercial);
		
		if ($this->ComprobanteAfip->ImporteImpuestoInterno && $this->ComprobanteAfip->ImporteImpuestoInterno > 0)
		{
			$ok = $this->PyFEPDF->AgregarDato("ImpuestoInternoL", 'Impuesto Interno:');
			$ok = $this->PyFEPDF->AgregarDato("ImpuestoInterno", number_format($this->ComprobanteAfip->ImporteImpuestoInterno, 2, ',', '.'));
		}
		
		
		// Agrego datos adicionales fijos:
		$ok = $this->PyFEPDF->AgregarDato("logo", $this->path . '\plantillas\logo.png');
		$ok = $this->PyFEPDF->AgregarDato("EMPRESA", ConfiguracionFactura::RazonSocial);
		if ($this->ComprobanteAfip->PuntoVenta == 5) {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::DireccionAlt);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::DireccionAlt2);
		} else {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::Direccion);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::Direccion2);
		}
		$ok = $this->PyFEPDF->AgregarDato("MEMBRETE3", ConfiguracionFactura::Fax);
		$ok = $this->PyFEPDF->AgregarDato("CUIT", "CUIT: " . ConfiguracionFactura::CuitLetras);
		$ok = $this->PyFEPDF->AgregarDato("IIBB", "I. Brutos: " . ConfiguracionFactura::IIBB);
		$ok = $this->PyFEPDF->AgregarDato("IVA", "IVA Responsable Inscripto");
		$ok = $this->PyFEPDF->AgregarDato("INICIO", "Inicio de Actividades: " . ConfiguracionFactura::FechaInicioActividad);
		$ok = $this->PyFEPDF->AgregarDato("ClienteLocalidad", $oCliente->GetLocalidad());
		$ok = $this->PyFEPDF->AgregarDato("Partido", $oCliente->GetPartido());
		$ok = $this->PyFEPDF->AgregarDato("ClienteProvincia", $oCliente->GetProvincia());
		$ok = $this->PyFEPDF->AgregarDato("ClienteTelefono", $oCliente->GetTelefono());
		
		if ($oCliente->IdTipoIva == TipoIva::MO)
		{
			$Comentarios = wordwrap(utf8_decode(ConfiguracionFactura::LeyendaMonotributo), 80, '\n');
			$arrComentarios = explode('\n', $Comentarios);
			$ok = $this->PyFEPDF->AgregarDato("Comentarios", $arrComentarios[0]);
			for ($i = 1; $i < count($arrComentarios); $i++)
			{
				$j = $i + 1;
				$ok = $this->PyFEPDF->AgregarDato("Comentarios" . $j, $arrComentarios[$i]);
			}
		}
		$oNumber = new Number();
		$ok = $this->PyFEPDF->AgregarDato("TotalLetras", "Son pesos: ". $oNumber->ValorEnLetras($this->ComprobanteAfip->Total, "pesos"));
		// Cargo el formato desde el archivo CSV (opcional)
		// (carga todos los campos a utilizar desde la planilla)
		$ok = $this->PyFEPDF->CargarFormato($this->path . '\plantillas\facturaunidad.csv');
		                
		// Creo plantilla para esta factura (papel A4 vertical):
		$ok = $this->PyFEPDF->CrearPlantilla("A4", "portrait");
		
		// Proceso la plantilla
		$ok = $this->PyFEPDF->ProcesarPlantilla(2, 24, "izq");
		// Genero el PDF de salida según la plantilla procesada
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		$FileName = 'factura ' . $oComprobante->IdComprobante  . '.pdf';
		$Salida = getcwd()  . Comprobante::PathFileFisico . '\\' . $FileName;
		$ok = $this->PyFEPDF->GenerarPDF($Salida);
		
		$oComprobante->Archivo = $FileName;
		$oComprobantes->Update($oComprobante);
		
		// Abro el visor de PDF y muestro lo generado
		// (es necesario tener instalado Acrobat Reader o similar)
		$imprimir = false; # cambiar a True para que lo envie directo a la impresora
		$ok = $this->PyFEPDF->MostrarPDF($Salida, $imprimir);
	}

	public function CrearFacturaMinutaAnulado(Minuta $oMinuta, $oFacturaUnidad)
	{
		$oClientes		= new Clientes();
		$oUnidades		= new Unidades();
		$oModelos		= new Modelos();
		$oMarcas		= new Marcas();
		$oTiposModelo	= new TiposModelo();
		$oColores		= new Colores();
		
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->PyFEPDF = new COM($this->WSFacturacion); 
		
		// CUIT del emisor
		$this->PyFEPDF->CUIT = ConfiguracionFactura::Cuit;
		
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		
		$oCliente = $oClientes->GetById($oComprobante->IdCliente);
		$NombreCliente = $oCliente->RazonSocial;
		$NumeroDocumento = $this->ComprobanteAfip->NumeroDocumento;
		if ($oMinuta->Condominio)
		{
			/* obtenemos los datos del cliente */
			$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
			$NombreCliente = $oCliente->RazonSocial . ' Y ' . $oClienteCondominio->RazonSocial;
			$NumeroDocumento .= ' / ' . ClaveFiscalTipos::GetById($oClienteCondominio->ClaveFiscalTipo) . ': ' . $oClienteCondominio->ClaveFiscalNumero;
		}
		elseif ($oFacturaUnidad->OtrosTitulares != '')
		{
			$NombreCliente = $oCliente->RazonSocial . ' Y ' . $oFacturaUnidad->OtrosTitulares;
		}
    
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->PyFEPDF->CrearFactura(
			$this->ComprobanteAfip->IdConcepto, 
			$this->ComprobanteAfip->TipoDocumento, 
			$NumeroDocumento, 
			$this->ComprobanteAfip->IdTipoComprobanteAfip, 
			$this->ComprobanteAfip->PuntoVenta, 
			$this->ComprobanteAfip->Numero,
			0,
			0,
			0,
			0,
			0,
			0,
			str_replace('-', '', $this->ComprobanteAfip->Fecha),
			'', //Fecha Vencimiento de pago
			'', //Fecha Servicios desde
			'', //Fecha Servicios hasta
			$this->Moneda, 
			"1.000", // (deshabilitado por AFIP)
			$this->ComprobanteAfip->Cae,
			$this->ComprobanteAfip->VencimientoCae,
			$oCliente->GetIva(),    # usar categoria IVA factura A/B/C
			$NombreCliente,
			$oCliente->GetDomicilio(),
			16, // código para exportación
			$Detalle, 
			'', //Observaciones generales
			'', // Forma de Pago
			"FOB", // termino de comercio exterior para exportación
			1, // Idioma comprobante
			'', 
			0 // Descuento
			);
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA  || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->PyFEPDF->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero
					);
			}
		}
		
		$oUnidad	= $oUnidades->GetById($oMinuta->IdUnidad);
		$oModelo	= $oModelos->GetById($oUnidad->IdModelo);
		$oMarca		= $oMarcas->GetById($oModelo->IdMarcaVehiculo);
		
		// Agrego detalles de cada item de la factura:
		$IdIva = null;
		$ImporteIva = 0;
		
		// Agrego datos adicionales fijos:
		$ok = $this->PyFEPDF->AgregarDato("logo", $this->path . '\plantillas\logo.png');
		$ok = $this->PyFEPDF->AgregarDato("EMPRESA", ConfiguracionFactura::RazonSocial);
		if ($this->ComprobanteAfip->PuntoVenta == 5) {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::DireccionAlt);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::DireccionAlt2);
		} else {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::Direccion);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::Direccion2);
		}
		$ok = $this->PyFEPDF->AgregarDato("MEMBRETE3", ConfiguracionFactura::Fax);
		$ok = $this->PyFEPDF->AgregarDato("CUIT", "CUIT: " . ConfiguracionFactura::CuitLetras);
		$ok = $this->PyFEPDF->AgregarDato("IIBB", "I. Brutos: " . ConfiguracionFactura::IIBB);
		$ok = $this->PyFEPDF->AgregarDato("IVA", "IVA Responsable Inscripto");
		$ok = $this->PyFEPDF->AgregarDato("INICIO", "Inicio de Actividades: " . ConfiguracionFactura::FechaInicioActividad);
		$ok = $this->PyFEPDF->AgregarDato("ClienteLocalidad", $oCliente->GetLocalidad());
		$ok = $this->PyFEPDF->AgregarDato("ClienteProvincia", $oCliente->GetProvincia());
		$ok = $this->PyFEPDF->AgregarDato("ClienteTelefono", $oCliente->GetTelefono());
		
		if ($oCliente->IdTipoIva == TipoIva::MO)
		{
			$Comentarios = wordwrap(utf8_decode(ConfiguracionFactura::LeyendaMonotributo), 80, '\n');
			$arrComentarios = explode('\n', $Comentarios);
			$ok = $this->PyFEPDF->AgregarDato("Comentarios", $arrComentarios[0]);
			for ($i = 1; $i < count($arrComentarios); $i++)
			{
				$j = $i + 1;
				$ok = $this->PyFEPDF->AgregarDato("Comentarios" . $j, $arrComentarios[$i]);
			}
		}
		// Cargo el formato desde el archivo CSV (opcional)
		// (carga todos los campos a utilizar desde la planilla)
		$ok = $this->PyFEPDF->CargarFormato($this->path . '\plantillas\facturaunidad.csv');
		                
		// Creo plantilla para esta factura (papel A4 vertical):
		$ok = $this->PyFEPDF->CrearPlantilla("A4", "portrait");
		
		// Proceso la plantilla
		$ok = $this->PyFEPDF->ProcesarPlantilla(2, 24, "izq");
		// Genero el PDF de salida según la plantilla procesada
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		$FileName = 'factura ' . $oComprobante->IdComprobante  . '.pdf';
		$Salida = getcwd()  . Comprobante::PathFileFisico . '\\' . $FileName;
		$ok = $this->PyFEPDF->GenerarPDF($Salida);
		
		$oComprobante->Archivo = $FileName;
		$oComprobantes->Update($oComprobante);
		
		// Abro el visor de PDF y muestro lo generado
		// (es necesario tener instalado Acrobat Reader o similar)
		$imprimir = false; # cambiar a True para que lo envie directo a la impresora
		$ok = $this->PyFEPDF->MostrarPDF($Salida, $imprimir);
	}
	
	public function CrearFacturaPostVenta(Cliente $oCliente, $oFacturaPostVenta)
	{
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->PyFEPDF = new COM($this->WSFacturacion); 
		print_R(1);
		// CUIT del emisor
		$this->PyFEPDF->CUIT = ConfiguracionFactura::Cuit;
		
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
    
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->PyFEPDF->CrearFactura(
			$this->ComprobanteAfip->IdConcepto, 
			$this->ComprobanteAfip->TipoDocumento, 
			$this->ComprobanteAfip->NumeroDocumento, 
			$this->ComprobanteAfip->IdTipoComprobanteAfip, 
			$this->ComprobanteAfip->PuntoVenta, 
			$this->ComprobanteAfip->Numero,
			$oComprobante->Importe,
			$this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->TotalGravado + $this->ComprobanteAfip->TotalNoGravado,
			$this->ComprobanteAfip->ImporteIva,
			$this->ComprobanteAfip->ImporteImpuestos,
			$this->ComprobanteAfip->ImporteExento,
			str_replace('-', '', $this->ComprobanteAfip->Fecha),
			'', //Fecha Vencimiento de pago
			'', //Fecha Servicios desde
			'', //Fecha Servicios hasta
			$this->Moneda, 
			"1.000", // (deshabilitado por AFIP)
			$this->ComprobanteAfip->Cae,
			$this->ComprobanteAfip->VencimientoCae,
			$oCliente->GetIva(),    # usar categoria IVA factura A/B/C
			$oCliente->RazonSocial,
			$oCliente->GetDomicilio(),
			16, // código para exportación
			$oFacturaPostVenta->Comentarios, 
			'', //Observaciones generales
			'', // Forma de Pago
			"FOB", // termino de comercio exterior para exportación
			1, // Idioma comprobante
			'', 
			0 // Descuento
			);
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA  || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->PyFEPDF->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero
					);
			}
		}
			
		// Agrego impuestos percepción IIBB
		if ($this->ComprobanteAfip->ImportePercepcionIIBB && $this->ComprobanteAfip->ImportePercepcionIIBB > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosProvinciales, 'Perc. IIBB:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImportePercepcionIIBB);

		// Agrego impuestos internos
		if ($this->ComprobanteAfip->ImporteImpuestoInterno && $this->ComprobanteAfip->ImporteImpuestoInterno > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosInternos, 'Impuesto Interno:', $this->ComprobanteAfip->TotalGravado, $this->ComprobanteAfip->ImporteImpuestoInterno);

		// Agrego tasas de IVA
		if ($this->ComprobanteAfip->ImporteIva21 && $this->ComprobanteAfip->ImporteIva21 > 0)
			$this->AgregarIva(Iva::Iva21, $this->ComprobanteAfip->ImporteIva21);
		
		if ($this->ComprobanteAfip->ImporteIva10 && $this->ComprobanteAfip->ImporteIva10 > 0)
			$this->AgregarIva(Iva::Iva10, $this->ComprobanteAfip->ImporteIva10);
		
		$arrDetalles = $oFacturaPostVenta->GetAllItems();
		foreach ($arrDetalles as $oDetalle)
		{
			// Agrego detalles de cada item de la factura:
			$Precio = 0;
			$IdIva = null;
			$ImporteIva = 0;
			if (true || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::FacturaA || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA)
			{
				$Precio = $oDetalle->ImporteNeto;               # precio neto (A) o iva incluido (B)
			}
			else
				$Precio = $oDetalle->ImporteBruto;
			if ($oDetalle->IdIva == Iva::Iva21)
			{
				$IdIva = ConstantesFacturaElectronica::Iva21;                 # código para alícuota del 21%
				$ImporteIva = $oDetalle->Iva21;
			}
			if ($oDetalle->IdIva == Iva::Iva10)
			{
				$IdIva = ConstantesFacturaElectronica::Iva10;                 # código para alícuota del 21%
				$ImporteIva = $oDetalle->Iva10;
			}
			else
			{
				$IdIva = ConstantesFacturaElectronica::Iva0;                 # código para alícuota del 21%
				$ImporteIva = 0;
			}
			
			$importe = $oDetalle->ImporteBruto;              # importe total del item
			$importe = $oDetalle->ImporteBruto;              # importe total del item
			$despacho = "";     # numero de despacho de importación
			$DatoA = "";          # primer dato adicional del item
			$DatoB = "";
			$DatoC = "";
			$DatoD = "";
			$DatoE = "";           # ultimo dato adicional del item
			$resultado = $this->PyFEPDF->AgregarDetalleItem(
				$oDetalle->Cantidad,  //Unidades
				"", // Codigo de barras
				"", // Codigo
				$oDetalle->Descripcion, 
				$oDetalle->Cantidad, //Cantidad
				7, //Codigo de medida (7 para unidades)
				$Precio, 
				0, //Bonificacion
				$IdIva, 
				$ImporteIva, 
				$oDetalle->ImporteBruto, // Total item
				"", // Despacho
				$DatoA, 
				$DatoB, 
				$DatoC,
				$DatoD, 
				$DatoE
				);
		}
		
		// Agrego datos adicionales fijos:
		$ok = $this->PyFEPDF->AgregarDato("logo", $this->path . '\plantillas\logo.png');
		$ok = $this->PyFEPDF->AgregarDato("EMPRESA", ConfiguracionFactura::RazonSocial);
		if ($this->ComprobanteAfip->PuntoVenta == 5) {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::DireccionAlt);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::DireccionAlt2);
		} else {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::Direccion);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::Direccion2);
		}
		$ok = $this->PyFEPDF->AgregarDato("MEMBRETE3", ConfiguracionFactura::Fax);
		$ok = $this->PyFEPDF->AgregarDato("CUIT", "CUIT: " . ConfiguracionFactura::CuitLetras);
		$ok = $this->PyFEPDF->AgregarDato("IIBB", "I. Brutos: " . ConfiguracionFactura::IIBB);
		$ok = $this->PyFEPDF->AgregarDato("IVA", "IVA Responsable Inscripto");
		$ok = $this->PyFEPDF->AgregarDato("INICIO", "Inicio de Actividades: " . ConfiguracionFactura::FechaInicioActividad);
		$ok = $this->PyFEPDF->AgregarDato("ClienteLocalidad", $oCliente->GetLocalidad());
		$ok = $this->PyFEPDF->AgregarDato("ClienteProvincia", $oCliente->GetProvincia());
		$ok = $this->PyFEPDF->AgregarDato("ClienteTelefono", $oCliente->GetTelefono());
		if ($oCliente->IdTipoIva == TipoIva::MO)
		{
			$Comentarios = wordwrap(utf8_decode(ConfiguracionFactura::LeyendaMonotributo), 80, '\n');
			$arrComentarios = explode('\n', $Comentarios);
			$ok = $this->PyFEPDF->AgregarDato("Comentarios", $arrComentarios[0]);
			for ($i = 1; $i < count($arrComentarios); $i++)
			{
				$j = $i + 1;
				$ok = $this->PyFEPDF->AgregarDato("Comentarios" . $j, $arrComentarios[$i]);
			}
		}
		$oNumber = new Number();
		$ok = $this->PyFEPDF->AgregarDato("TotalLetras", "Son pesos: ". $oNumber->ValorEnLetras($this->ComprobanteAfip->Total, "pesos"));
		// Cargo el formato desde el archivo CSV (opcional)
		// (carga todos los campos a utilizar desde la planilla)
		$ok = $this->PyFEPDF->CargarFormato($this->path . '\plantillas\factura.csv');
		                
		// Creo plantilla para esta factura (papel A4 vertical):
		$ok = $this->PyFEPDF->CrearPlantilla("A4", "portrait");
		
		// Proceso la plantilla
		$ok = $this->PyFEPDF->ProcesarPlantilla(2, 24, "izq");
		// Genero el PDF de salida según la plantilla procesada
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		$FileName = 'factura ' . $oComprobante->IdComprobante  . '.pdf';
		$Salida = getcwd()  . Comprobante::PathFileFisico . '\\' . $FileName;
		$ok = $this->PyFEPDF->GenerarPDF($Salida);
		
		$oComprobante->Archivo = $FileName;
		$oComprobantes->Update($oComprobante);
		
		// Abro el visor de PDF y muestro lo generado
		// (es necesario tener instalado Acrobat Reader o similar)
		$imprimir = false; # cambiar a True para que lo envie directo a la impresora
		$ok = $this->PyFEPDF->MostrarPDF($Salida, $imprimir);
	}
	
	public function CrearFacturaPostVentaAnulada(Cliente $oCliente, $oFacturaPostVenta)
	{
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->PyFEPDF = new COM($this->WSFacturacion); 
		print_R(1);
		// CUIT del emisor
		$this->PyFEPDF->CUIT = ConfiguracionFactura::Cuit;
		
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
    
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->PyFEPDF->CrearFactura(
			$this->ComprobanteAfip->IdConcepto, 
			$this->ComprobanteAfip->TipoDocumento, 
			$this->ComprobanteAfip->NumeroDocumento, 
			$this->ComprobanteAfip->IdTipoComprobanteAfip, 
			$this->ComprobanteAfip->PuntoVenta, 
			$this->ComprobanteAfip->Numero,
			0,
			0,
			0,
			0,
			0,
			0,
			str_replace('-', '', $this->ComprobanteAfip->Fecha),
			'', //Fecha Vencimiento de pago
			'', //Fecha Servicios desde
			'', //Fecha Servicios hasta
			$this->Moneda, 
			"1.000", // (deshabilitado por AFIP)
			$this->ComprobanteAfip->Cae,
			$this->ComprobanteAfip->VencimientoCae,
			$oCliente->GetIva(),    # usar categoria IVA factura A/B/C
			$oCliente->RazonSocial,
			$oCliente->GetDomicilio(),
			16, // código para exportación
			$Detalle, 
			'', //Observaciones generales
			'', // Forma de Pago
			"FOB", // termino de comercio exterior para exportación
			1, // Idioma comprobante
			'', 
			0 // Descuento
			);
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA  || $this->ComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->PyFEPDF->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero
					);
			}
		}
			
		
		
		// Agrego datos adicionales fijos:
		$ok = $this->PyFEPDF->AgregarDato("logo", $this->path . '\plantillas\logo.png');
		$ok = $this->PyFEPDF->AgregarDato("EMPRESA", ConfiguracionFactura::RazonSocial);
		if ($this->ComprobanteAfip->PuntoVenta == 5) {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::DireccionAlt);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::DireccionAlt2);
		} else {
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE1", ConfiguracionFactura::Direccion);
			$ok = $this->PyFEPDF->AgregarDato("MEMBRETE2", ConfiguracionFactura::Direccion2);
		}
		$ok = $this->PyFEPDF->AgregarDato("MEMBRETE3", ConfiguracionFactura::Fax);
		$ok = $this->PyFEPDF->AgregarDato("CUIT", "CUIT: " . ConfiguracionFactura::CuitLetras);
		$ok = $this->PyFEPDF->AgregarDato("IIBB", "I. Brutos: " . ConfiguracionFactura::IIBB);
		$ok = $this->PyFEPDF->AgregarDato("IVA", "IVA Responsable Inscripto");
		$ok = $this->PyFEPDF->AgregarDato("INICIO", "Inicio de Actividades: " . ConfiguracionFactura::FechaInicioActividad);
		$ok = $this->PyFEPDF->AgregarDato("ClienteLocalidad", $oCliente->GetLocalidad());
		$ok = $this->PyFEPDF->AgregarDato("ClienteProvincia", $oCliente->GetProvincia());
		$ok = $this->PyFEPDF->AgregarDato("ClienteTelefono", $oCliente->GetTelefono());

		if ($oCliente->IdTipoIva == TipoIva::MO)
		{
			$Comentarios = wordwrap(utf8_decode(ConfiguracionFactura::LeyendaMonotributo), 80, '\n');
			$arrComentarios = explode('\n', $Comentarios);
			$ok = $this->PyFEPDF->AgregarDato("Comentarios", $arrComentarios[0]);
			for ($i = 1; $i < count($arrComentarios); $i++)
			{
				$j = $i + 1;
				$ok = $this->PyFEPDF->AgregarDato("Comentarios" . $j, $arrComentarios[$i]);
			}
		}
		$oNumber = new Number();
		$ok = $this->PyFEPDF->AgregarDato("TotalLetras", "Son pesos: ". $oNumber->ValorEnLetras($this->ComprobanteAfip->Total, "pesos"));
		// Cargo el formato desde el archivo CSV (opcional)
		// (carga todos los campos a utilizar desde la planilla)
		$ok = $this->PyFEPDF->CargarFormato($this->path . '\plantillas\factura.csv');
		                
		// Creo plantilla para esta factura (papel A4 vertical):
		$ok = $this->PyFEPDF->CrearPlantilla("A4", "portrait");
		
		// Proceso la plantilla
		$ok = $this->PyFEPDF->ProcesarPlantilla(2, 24, "izq");
		// Genero el PDF de salida según la plantilla procesada
		$oComprobantes = new Comprobantes();
		$oComprobante = $oComprobantes->GetById($this->ComprobanteAfip->IdComprobante);
		$FileName = 'factura ' . $oComprobante->IdComprobante  . '.pdf';
		$Salida = getcwd()  . Comprobante::PathFileFisico . '\\' . $FileName;
		$ok = $this->PyFEPDF->GenerarPDF($Salida);
		
		$oComprobante->Archivo = $FileName;
		$oComprobantes->Update($oComprobante);
		
		// Abro el visor de PDF y muestro lo generado
		// (es necesario tener instalado Acrobat Reader o similar)
		$imprimir = false; # cambiar a True para que lo envie directo a la impresora
		$ok = $this->PyFEPDF->MostrarPDF($Salida, $imprimir);
	}
}

?>