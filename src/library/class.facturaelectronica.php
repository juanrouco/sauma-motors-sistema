<?php
require_once('class.configuracionfactura.php');
require_once('class.comprobantesafip.php');
require_once('class.logfacturaelectronica.php');
require_once('class.logsfacturaelectronica.php');

class FacturaElectronica
{
	private $WsdlAutenticacion = '';
	private $WsdlFacturacion = '';
	private $path;
	private $Certificado = '';
	private $ClavePrivada = '';
	private $Cache = '';
	private $WSAA;
	private $tra;
	private $ta;
	private $cms;
	private $WSFacturacion;
	private $WSFEv1;
	private $PuntoVenta;
	private $Moneda;
	private $ComprobanteAfip;
	private $ComprobanteAfipAsociado;
	private $LogsFacturaElectronica;
	private $VencimientoCae;
	private $fileTA = 'ta.xml';
	private $Token;
	private $Sign;
	private $wrapper = ''; // "pycurl" para situaciones especiales o versiones anteriores (instalador < 2.7)
	private $proxy = ''; // formato usuario:clave@servidor:puerto
	private $cacert = "\conf\afip_ca_info.crt"; // Indicar ruta completa (directorio conf instalador > 2.7)
	
	public function __construct($oComprobanteAfip, $oComprobanteAfipAsociado = null)
	{
		if (ConfiguracionFactura::Testing)
		{
			$this->WsdlAutenticacion = ConfiguracionFactura::UrlAutenticacionTesting;
			$this->WsdlFacturacion = ConfiguracionFactura::UrlFacturacionTesting;
			$this->Certificado = ConfiguracionFactura::CertificadoHomologacion;
		}
		else
		{
			$this->WsdlAutenticacion = ConfiguracionFactura::UrlAutenticacionProduccion;
			$this->WsdlFacturacion = ConfiguracionFactura::UrlFacturacionProduccion;
			$this->Certificado = ConfiguracionFactura::CertificadoProduccion;
		}
		
		$this->path = getcwd()  . "\\..\\facturaelectronica\\";
		$this->ClavePrivada = ConfiguracionFactura::ClavePrivada;
		$this->WSFacturacion = 'WSFEv1';
		$this->PuntoVenta = $oComprobanteAfip->PuntoVenta;
		$this->Moneda = ConstantesFacturaElectronica::Pesos;
		$this->ComprobanteAfip = $oComprobanteAfip;
		$this->ComprobanteAfipAsociado = $oComprobanteAfipAsociado;
		if ($oComprobanteAfip)
		{
			$this->LogsFacturaElectronica = new LogsFacturaElectronica();
			$this->LogsFacturaElectronica->IniciarLog($oComprobanteAfip);
		}
	}

	private function TokenValido()
	{
		$crear = true;

		if (file_exists($this->path . $this->fileTA))
		{
			$ta = file_get_contents($this->path . $this->fileTA);

			if ($ta != '')
			{
				$this->WSAA->AnalizarXml($ta);

				if ($expiracion = $this->WSAA->ObtenerTagXml('expirationTime'))
				{
					$crear = $this->WSAA->Expirado($expiracion);
				}
			}
		}

		return !$crear;
	}
	
	public function AutenticarAfip()
	{
		// Crear objeto interface Web Service Autenticación y Autorización
		$this->WSAA = new COM('WSAA'); 

		// Generar un Ticket de Requerimiento de Acceso (TRA)
		if (!$this->TokenValido())
		{
			$this->tra = $this->WSAA->CreateTRA();
			// Generar el mensaje firmado (CMS)
			$this->cms = $this->WSAA->SignTRA($this->tra, $this->path . $this->Certificado, $this->path . $this->ClavePrivada);
			
			
			// Iniciar la conexión al webservice de autenticación
			$ok = $this->WSAA->Conectar(
				$this->Cache, 
				$this->WsdlAutenticacion, 
				$this->proxy,
				$this->wrapper,
				$this->WSAA->InstallDir . $this->cacert);
				
			// Llamar al web service para autenticar
			$this->ta = $this->WSAA->LoginCMS($this->cms);
	
			file_put_contents($this->path . $this->fileTA, $this->ta);
			$this->Token	= $this->WSAA->Token;
			$this->Sign		= $this->WSAA->Sign;
		}
		else
		{
			$this->Token	= $this->WSAA->ObtenerTagXml('token');
			$this->Sign		= $this->WSAA->ObtenerTagXml('sign');
		}
		
		// TODO: Loggear esto
		if ($this->ComprobanteAfip)
		{
			$this->LogsFacturaElectronica->AutenticacionLog($this->ComprobanteAfip, $this->WSAA);
		}
		
		return true;
	}
	
	public function InicializarWSFacturacion($ChequearEstado = false)
	{
		// Crear objeto interface Web Service de Factura Electrónica v1 (version 2.5)
		$this->WSFEv1 = new COM($this->WSFacturacion);
		// Setear token y sign de autorización (pasos previos) Y CUIT del emisor
		$this->WSFEv1->Token = $this->Token;
		$this->WSFEv1->Sign = $this->Sign; 
		$this->WSFEv1->Cuit = ConfiguracionFactura::Cuit;
		
		// Iniciar la conexión al webservice de facturación
		$ok = $this->WSFEv1->Conectar(
			$this->Cache,
			$this->WsdlFacturacion, 
			$this->proxy,
			$this->wrapper,
			$this->WSAA->InstallDir . $this->cacert, 
			300); 

		// Llamo a un servicio nulo, para obtener el estado del servidor (opcional)
		$this->WSFEv1->Dummy();
		
		if ($ChequearEstado)
		{	
			$AppServerStatus = $this->WSFEv1->AppServerStatus;
			$DbServerStatus = $this->WSFEv1->DbServerStatus;
			$AuthServerStatus = $this->WSFEv1->AuthServerStatus;
			echo "appserver status $AppServerStatus <br />";
			echo "dbserver status $DbServerStatus <br />";
			echo "authserver status $AuthServerStatus <br />";
		}
		else
		{
			// Todo: Loggear esto
			$this->LogsFacturaElectronica->InicioFacturacionLog($this->ComprobanteAfip, $this->WSFEv1);
		}
	}
	
	public function ObtenerUltimoComprobante($IdTipoComprobanteAfip)
	{
		// Recupero œltimo nœmero de comprobante para un punto venta/tipo (opcional)
		$ult = $this->WSFEv1->CompUltimoAutorizado($IdTipoComprobanteAfip, $this->PuntoVenta);
		
		$this->LogsFacturaElectronica->UltimoComprobanteLog($this->ComprobanteAfip, $this->WSFEv1, $IdTipoComprobanteAfip, $this->PuntoVenta);
		
		return $ult;
	}
	
	public function ConsultarComprobante($IdTipoComprobanteAfip, $PuntoVenta, $Numero)
	{
		// Recupero œltimo nœmero de comprobante para un punto venta/tipo (opcional)
		$ult = $this->WSFEv1->CompConsultar($IdTipoComprobanteAfip, $PuntoVenta, $Numero);
		
		$this->LogsFacturaElectronica->ConsultarComprobanteLog($this->ComprobanteAfip, $this->WSFEv1, $IdTipoComprobanteAfip, $this->PuntoVenta);
		
		return $ult;
	}
	
	public function AsignarNumero($UltimoNumero = null)
	{
		$NumeroComprobante = $this->ObtenerUltimoComprobante($this->ComprobanteAfip->IdTipoComprobanteAfip);
			
		$this->ComprobanteAfip->Numero = $NumeroComprobante + 1;
		$this->LogsFacturaElectronica->AsignarNumeroLog($this->ComprobanteAfip, $this->WSFEv1);
		
		if ($NumeroComprobante > $UltimoNumero)
			throw new Exception($UltimoNumero . " El número de comprobante no es correlativo.\n");
	}
	
	private function AgregarImpuesto($IdTributoTipo, $Descripcion, $BaseImponible, $Importe)
	{
		$Alicuota = $Importe * 100 / $BaseImponible;
		$Alicuota = number_format($Alicuota, 2, '.', '');
		$ok = $this->WSFEv1->AgregarTributo($IdTributoTipo, $Descripcion, number_format($BaseImponible, 2, '.', ''), $Alicuota, number_format($Importe, 2, '.', ''));
		
		$this->LogsFacturaElectronica->AgregarImpuestoLog($this->ComprobanteAfip, $this->WSFEv1, $IdTributoTipo, $Descripcion, $BaseImponible, $Alicuota, $Importe);
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
			
		$ok = $this->WSFEv1->AgregarIva($IdIva, $ImporteNeto, number_format($ImporteIva, 2, '.', ''));
		
		$this->LogsFacturaElectronica->AgregarImpuestoLog($this->ComprobanteAfip, $this->WSFEv1, $IdIva, 'IVA', $ImporteNeto, '', $ImporteIva);
	
	}
	
	public function CrearFactura(ComprobanteAfip $oComprobanteAfip)
	{
		// Inicializo la factura interna con los datos de la cabecera
		$resultado = $this->WSFEv1->CrearFactura(
			$oComprobanteAfip->IdConcepto, 
			$oComprobanteAfip->TipoDocumento, 
			$oComprobanteAfip->NumeroDocumento, 
			$oComprobanteAfip->IdTipoComprobanteAfip, 
			$oComprobanteAfip->PuntoVenta, 
			$oComprobanteAfip->Numero, 
			$oComprobanteAfip->Numero,
			number_format($oComprobanteAfip->Total, 2, '.', ''),
			number_format($oComprobanteAfip->TotalNoGravado, 2, '.', ''),
			number_format($oComprobanteAfip->TotalGravado, 2, '.', ''),
			number_format($oComprobanteAfip->ImporteIva, 2, '.', ''),
			number_format($oComprobanteAfip->ImporteImpuestos, 2, '.', ''),
			number_format($oComprobanteAfip->ImporteExento, 2, '.', ''),
			str_replace('-', '', $oComprobanteAfip->Fecha),
			$oComprobanteAfip->FechaVencimientoPago, //Fecha Vencimiento de pago
			$oComprobanteAfip->FechaServicioDesde, //Fecha Servicios desde
			$oComprobanteAfip->FechaServicioHasta, //Fecha Servicios hasta
			$this->Moneda, 
			"1.000" // (deshabilitado por AFIP)
			);
			
		$this->WSFEv1->EstablecerCampoFactura('condicion_iva_receptor_id', $oComprobanteAfip->CodigoTipoIva);
			
		$this->LogsFacturaElectronica->CrearFacturaLog($oComprobanteAfip, $this->WSFEv1, $resultado);

		if ($oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::FacturaElectronicaA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::FacturaElectronicaB)
		{
			$this->WSFEv1->AgregarOpcional(27, 'SCA');
			$this->WSFEv1->AgregarOpcional(2101, $oComprobanteAfip->CBU);			
		}

		if ($oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoElectronicaA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoElectronicaB ||
			$oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaDebitoElectronicaA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaDebitoElectronicaB)
		{
			$this->WSFEv1->AgregarOpcional(22, "S");			
		}
			
		// Agrego los comprobantes asociados (solo para notas de crédito y débito):
		if ($oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoB ||
			$oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoElectronicaA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaCreditoElectronicaB ||
			$oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaDebitoElectronicaA || $oComprobanteAfip->IdTipoComprobanteAfip == ConstantesFacturaElectronica::NotaDebitoElectronicaB)
		{
			if ($this->ComprobanteAfipAsociado)
			{
				$ok = $this->WSFEv1->AgregarCmpAsoc(
					$this->ComprobanteAfipAsociado->IdTipoComprobanteAfip, 
					$this->ComprobanteAfipAsociado->PuntoVenta, 
					$this->ComprobanteAfipAsociado->Numero,
					ConfiguracionFactura::Cuit,
					$this->ComprobanteAfipAsociado->Fecha
					);
			}
		}
			
		// Agrego impuestos percepción IIBB
		if ($oComprobanteAfip->ImportePercepcionIIBB && $oComprobanteAfip->ImportePercepcionIIBB > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosProvinciales, 'Perc. IIBB', $oComprobanteAfip->TotalGravado, $oComprobanteAfip->ImportePercepcionIIBB);


		// Agrego impuestos internos
		if ($oComprobanteAfip->ImporteImpuestoInterno && $oComprobanteAfip->ImporteImpuestoInterno > 0)
			$this->AgregarImpuesto(ConstantesFacturaElectronica::ImpuestosInternos, 'Impuesto Interno', $oComprobanteAfip->TotalGravado, $oComprobanteAfip->ImporteImpuestoInterno);

		// Agrego tasas de IVA
		if ($oComprobanteAfip->ImporteIva21 && $oComprobanteAfip->ImporteIva21 > 0)
			$this->AgregarIva(Iva::Iva21, $oComprobanteAfip->ImporteIva21);
		
		if ($oComprobanteAfip->ImporteIva10 && $oComprobanteAfip->ImporteIva10 > 0)
			$this->AgregarIva(Iva::Iva10, $oComprobanteAfip->ImporteIva10);
		
		// Habilito reprocesamiento automático (predeterminado):
		$this->WSFEv1->Reprocesar = true;
        
		// Llamo al WebService de Autorizaci—n para obtener el CAE
		$cae = $this->WSFEv1->CAESolicitar();
		$this->VencimientoCae = $this->WSFEv1->Vencimiento;
		
		// TODO: Loggear esto
		$this->LogsFacturaElectronica->ObtenerCaeLog($oComprobanteAfip, $this->WSFEv1, $cae);
		
		// Verifico que no haya rechazo o advertencia al generar el CAE
		if ($cae=="") {
			throw new Exception(utf8_decode("ERROR AFIP: ") . $this->WSFEv1->Obs);
		} elseif ($cae=="NULL" || $this->WSFEv1->Resultado!="A") {
			throw new Exception("No se asignó CAE (Rechazado). Motivos: $this->WSFEv1->Motivo \n");
		} elseif ($this->WSFEv1->Obs!="") {
			//echo "Se asignó CAE pero con advertencias. Motivos: $WSFEv1->Obs \n";
		}
		return $cae;
	}
	
	public function AsignarCae()
	{
		$this->ComprobanteAfip->Cae = $this->CrearFactura($this->ComprobanteAfip);
		$this->ComprobanteAfip->VencimientoCae = $this->VencimientoCae;
		$this->ComprobanteAfip->IdEstado = ComprobantesAfipEstados::Procesado;
		
		return $this->ComprobanteAfip;
	}
	
	public function LogError(Exception $e)
	{
		$this->LogsFacturaElectronica->LogError($this->ComprobanteAfip, $this->WSAA, $this->WSFEv1, $e);
		$oComprobantesAfip = new ComprobantesAfip();
		$this->ComprobanteAfip->IdEstado = ComprobantesAfipEstados::Rechazado;
		$oComprobantesAfip->Update($this->ComprobanteAfip);
	}
}

?>