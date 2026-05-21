<?php 

require_once('class.dbaccess.php');
require_once('class.logfacturaelectronica.php');
require_once('class.comprobanteafip.php');
require_once('class.session.php');
require_once('class.filter.php');
require_once('class.page.php');

class LogsFacturaElectronica extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = 'WHERE 1';
		
		if (isset($filter['IdComprobante']) && $filter['IdComprobante'] != '')
			$sql.= " AND IdComprobante = " . DB::Number($filter['IdComprobante']);
		
		if (isset($filter['IdComprobanteAfip']) && $filter['IdComprobanteAfip'] != '')
		$sql.= " AND IdComprobanteAfip = " . DB::Number($filter['IdComprobanteAfip']);
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
		$sql.= " AND IdUsuario = " . DB::Number($filter['IdUsuario']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_LogsFacturaElectronica";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['Count'];
		
		return ceil($Count);
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_LogsFacturaElectronica";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY Fecha";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oLogFacturaElectronica = new LogFacturaElectronica();
			$oLogFacturaElectronica->ParseFromArray($oRow);
			
			array_push($arr, $oLogFacturaElectronica);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdLogFacturaElectronica)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_LogsFacturaElectronica";
		$sql.= " WHERE IdLogFacturaElectronica = " . DB::Number($IdLogFacturaElectronica);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oLogFacturaElectronica = new LogFacturaElectronica();
		$oLogFacturaElectronica->ParseFromArray($oRow);
		
		return $oLogFacturaElectronica;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_LogsFacturaElectronica";

		if ($filter)
			$sql.= $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(LogFacturaElectronica $oLogFacturaElectronica)
	{
		$arr = array
		(
			'Fecha' 			=> DB::Date($oLogFacturaElectronica->Fecha),
			'IdUsuario' 		=> DB::Number($oLogFacturaElectronica->IdUsuario),
			'IdComprobante' 	=> DB::Number($oLogFacturaElectronica->IdComprobante),
			'IdComprobanteAfip'	=> DB::Number($oLogFacturaElectronica->IdComprobanteAfip),
			'Comentarios' 		=> DB::String($oLogFacturaElectronica->Comentarios),
			'XmlRequest' 		=> DB::String($oLogFacturaElectronica->XmlRequest),
			'XmlResponse' 		=> DB::String($oLogFacturaElectronica->XmlResponse)
		);
		
		if (!$this->Insert('TB_LogsFacturaElectronica', $arr))
			return false;
		$oLogFacturaElectronica->IdLogFacturaElectronica = DBAccess::GetLastInsertId();
			
		return $oLogFacturaElectronica;
	}
	
	private function GetIdUsuario()
	{
		$oUsuario = Session::GetCurrentUser();
		if ($oUsuario)
			return $oUsuario->IdUsuario;
		return 0;
	}
	
	public function IniciarLog(ComprobanteAfip $oComprobanteAfip)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
		$oLog->Comentarios = 'Se inicializa el log para envio de factura a AFIP.';
		
		$this->Create($oLog);
	}
	
	public function AutenticacionLog(ComprobanteAfip $oComprobanteAfip, $WSAA)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$Token = $WSAA->Token;
		$Sign = $WSAA->Sign;
		
		$oLog->Comentarios = "Se autentica contra el WS de AFIP. <br />";
		$oLog->Comentarios.= "Token de Acceso: $Token <br />";
		$oLog->Comentarios.= "Sign de Acceso: $Sign <br />";
		
		$oLog->XmlRequest.= $WSAA->XmlRequest;
		$oLog->XmlResponse.= $WSAA->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function InicioFacturacionLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se inicia la conexion con el WS de Facturas Electronicas. <br />";
		$oLog->Comentarios.= "appserver status $WSFEv1->AppServerStatus <br />";
		$oLog->Comentarios.= "dbserver status $WSFEv1->DbServerStatus <br />";
		$oLog->Comentarios.= "authserver status $WSFEv1->AuthServerStatus <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function UltimoComprobanteLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1, $IdTipoComprobanteAfip, $PuntoVenta)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se obtiene el numero del ultimo comprobante procesado. <br />";
		$oLog->Comentarios.= "Tipo de comprobante: $IdTipoComprobanteAfip <br />";
		$oLog->Comentarios.= "Punto de Venta: $PuntoVenta <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function ConsultarComprobanteLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1, $IdTipoComprobanteAfip, $PuntoVenta, $NumeroFactura)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		//$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		//$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se obtienen los datos del comprobante. <br />";
		$oLog->Comentarios.= "Tipo de comprobante: $IdTipoComprobanteAfip <br />";
		$oLog->Comentarios.= "Punto de Venta: $PuntoVenta <br />";
		$oLog->Comentarios.= "Numero: $NumeroFactura <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function AsignarNumeroLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se asigna el Numero de comprobante: $oComprobanteAfip->Numero <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function CrearFacturaLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1, $resultado)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se crea la factura a partir de los datos del comprobante. <br />";
		$oLog->Comentarios.= "Resultado: $resultado <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function AgregarImpuestoLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1, $IdTributoTipo, $Descripcion, $BaseImponible, $Alicuota, $Importe)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se Agrega impuesto a la factura la factura a partir de los datos del comprobante. <br />";
		$oLog->Comentarios.= "Tipo tributo: $IdTributoTipo <br />";
		$oLog->Comentarios.= "Descripcion: $Descripcion <br />";
		$oLog->Comentarios.= "Base imponible: $BaseImponible <br />";
		$oLog->Comentarios.= "Alicuota: $Alicuota <br />";
		$oLog->Comentarios.= "Importe: $Importe <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function ObtenerCaeLog(ComprobanteAfip $oComprobanteAfip, $WSFEv1, $Cae)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
		
		$resultado = $WSFEv1->Resultado;
		$CbteNro = $WSFEv1->CbteNro;
		$Vencimiento = $WSFEv1->Vencimiento;
		$EmisionTipo = $WSFEv1->EmisionTipo;
		$Reproceso = $WSFEv1->Reproceso;
		$ErrMsg = $WSFEv1->ErrMsg;
	
		$oLog->Comentarios = "Se obtiene el CAE del WS de facturas electronicas. <br />";
		$oLog->Comentarios.= "Resultado: $Resultado <br />";
		$oLog->Comentarios.= "Nro. Comprobante: $CbteNro <br />";
		$oLog->Comentarios.= "CAE: $Cae <br />";
		$oLog->Comentarios.= "Vencimiento: $Vencimiento <br />";
		$oLog->Comentarios.= "Tipo Emision: $EmisionTipo <br />";
		$oLog->Comentarios.= "Reproceso: $Reproceso <br />";
		$oLog->Comentarios.= "Errores: $ErrMsg <br />";
		
		$oLog->XmlRequest.= $WSFEv1->XmlRequest;
		$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		
		$this->Create($oLog);
	}
	
	public function LogError(ComprobanteAfip $oComprobanteAfip, $WSAA, $WSFEv1, Exception $e)
	{
		$oLog = new LogFacturaElectronica();
		$oLog->Fecha = date('d-m-Y H:i:s');
		$oLog->IdUsuario = $this->GetIdUsuario();
		$oLog->IdComprobante = $oComprobanteAfip->IdComprobante;
		$oLog->IdComprobanteAfip = $oComprobanteAfip->IdComprobanteAfip;
	
		$oLog->Comentarios = "Se ha producido un error durante el envio de la factura a AFIP. <br />";
		$oLog->Comentarios.= "Mensaje: " . $e->getMessage() . " <br />";
		if (isset($WSAA)) {
			$oLog->Comentarios.= "WSAA.Excepcion: $WSAA->Excepcion <br />";
			$oLog->Comentarios.= "WSAA.Traceback: $WSAA->Traceback <br />";
			$oLog->XmlRequest.= $WSAA->XmlRequest;
			$oLog->XmlResponse.= $WSAA->XmlResponse;
		}
		if (isset($WSFEv1)) {
			$Excepcion = $WSFEv1->Excepcion;
			//$Exception = $WSFEv1->Exception;
			$Traceback = $WSFEv1->Traceback;
			$oLog->Comentarios.= "WSFEv1.Excepcion: $Excepcion <br />";
			//$oLog->Comentarios.= "WSFEv1.Excepcion: $Exception <br />";
			$oLog->Comentarios.= "WSFEv1.Traceback: $Traceback <br />";
			$oLog->XmlRequest.= $WSFEv1->XmlRequest;
			$oLog->XmlResponse.= $WSFEv1->XmlResponse;
		}
		
		$this->Create($oLog);
	}
}

?>