<?php
require_once('class.configuracionfactura.php');
require_once('class.comprobantesafip.php');
require_once('class.comprobantes.php');
require_once('class.ifacturaelectronica.php');
require_once('class.facturaelectronica.php');

class FacturaElectronicaService
{
	private $Factura;
	
	public function __construct(IFacturaElectronica $oFactura)
	{
		$this->Factura = $oFactura;
		//print_r('Facturacion interrumpida por mantenimiento.');exit;
	}
	
	public function Procesar($Testing = false)
	{
		try 
		{
			$oComprobantes			= new Comprobantes();
			$oComprobantesAfip		= new ComprobantesAfip();
			
			if (!$oComprobante = $this->Factura->ObtenerComprobante())
			{
				print_r('El comprobante a enviar no ha sido encontrado.');
				exit;
			}
			
			if ($oComprobante->Numero && $oComprobante->Numero != '00000000')
			{
				print_r('El comprobante a enviar ya ha sido enviado.');
				exit;
			}
			
			$oComprobanteAfip = $oComprobantesAfip->GetByIdComprobante($oComprobante->IdComprobante);
			
			if ($oComprobanteAfip && $oComprobanteAfip->IdEstado != 3 && $oComprobanteAfip->Numero && $oComprobanteAfip->Numero != '00000000')
			{
				print_r('El comprobante a enviar ya ha sido enviado.');
				exit;
			}
			elseif ($oComprobanteAfip->IdEstado = 3)
			{
				$oComprobantesAfip->Delete($oComprobanteAfip->IdComprobanteAfip);
				$oComprobanteAfip = null;
			}
			
			if (!$oComprobanteAfip)
			{	
				$oComprobanteAfip = new ComprobanteAfip();
				$oComprobanteAfip->CreateFromComprobante($oComprobante);
				$oComprobantesAfip->Create($oComprobanteAfip);
			}
			
			$oLast = $oComprobantes->GetLastPrefijo($oComprobante->IdTipoComprobante, $oComprobante->Prefijo);
			$oFacturaElectronica	= new FacturaElectronica($oComprobanteAfip, $this->Factura->ObtenerComprobanteAfipAsociado(), $Testing);
			
			print_r('PASO 1: Conectando y autenticando a AFIP...<br /><br />');
			
			if ($oFacturaElectronica->AutenticarAfip())
			{	
				$oFacturaElectronica->InicializarWSFacturacion();
				print_r('PASO 2: Generando factura electronica...<br /><br />');
				$oFacturaElectronica->AsignarNumero(intval($oLast->Numero));
				$oComprobanteAfip = $oFacturaElectronica->AsignarCae();
				
				$oComprobantesAfip->Update($oComprobanteAfip);
				print_r('PASO 3: Recibiendo informaci&oacute;n...<br /><br />');
				
				$oComprobante->Numero = str_pad($oComprobanteAfip->Numero, 8, "0", STR_PAD_LEFT);
				$oComprobante->Fecha = date('d-m-Y');
				$oComprobante->Cae = $oComprobanteAfip->Cae;
				$oComprobantes->Update($oComprobante);
				
				$this->Factura->SetNumeroComprobante(str_pad($oComprobanteAfip->Numero, 8, "0", STR_PAD_LEFT));
				$this->Factura->SetFechaComprobante(date('d-m-Y'));
				$this->Factura->ActualizarFactura();
				
				return true;
			}

		} catch (Exception $e) {
			//$oComprobantesAfip->Delete($oComprobanteAfip->IdComprobanteAfip);
			print_r('ERROR: Se ha producido un error inesperado. Por favor revise su conexi&oacute;n a internet y reintente.<br />');
			print_r('En caso de que el problema persista, contactese con el administrador del sistema.<br /><br />');
			$oFacturaElectronica->LogError($e);
			return false;
		}
	}
}

?>