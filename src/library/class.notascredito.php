<?php 

require_once('class.dbaccess.php');
require_once('class.comprobante.php');
require_once('class.comprobanteestados.php');
require_once('class.comprobantetipos.php');
require_once('class.operaciontipos.php');
require_once('class.notacredito.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class NotasCredito extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdTipoComprobante'])) && ($filter['IdTipoComprobante'] != ''))
			$sql.= " AND c.IdTipoComprobante = " . DB::Number($filter['IdTipoComprobante']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND c.Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND cl.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND nc.Fecha >=" . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND nc.Fecha <=" . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_NotasCredito nc";
		$sql.= " INNER JOIN TB_Comprobantes c ON nc.IdComprobante = c.IdComprobante";
		$sql.= " INNER JOIN TB_Clientes cl ON nc.IdCliente = cl.IdCliente";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY c.Numero, nc.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oNotaCredito = new NotaCredito();
			$oNotaCredito->ParseFromArray($oRow);
			
			array_push($arr, $oNotaCredito);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdNotaCredito)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasCredito";
		$sql.= " WHERE IdNotaCredito = " . DB::Number($IdNotaCredito);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oNotaCredito = new NotaCredito();
		$oNotaCredito->ParseFromArray($oRow);
		
		return $oNotaCredito;		
	}
	
	public function GetByIdFactura($IdFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasCredito";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oNotaCredito = new NotaCredito();
		$oNotaCredito->ParseFromArray($oRow);
		
		return $oNotaCredito;		
	}
	
	public function GetByNumero($Numero)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_NotasCredito nc";
		$sql.= " INNER JOIN TB_Comprobantes c ON nc.IdComprobante = c.IdComprobante";
		$sql.= " WHERE c.Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oNotaCredito = new NotaCredito();
		$oNotaCredito->ParseFromArray($oRow);
		
		return $oNotaCredito;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_NotasCredito nc";
		$sql.= " INNER JOIN TB_Comprobantes c ON nc.IdComprobante = c.IdComprobante";
		$sql.= " INNER JOIN TB_Clientes cl ON nc.IdCliente = cl.IdCliente";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	
	
	private function GetArrayDB(NotaCredito $oNotaCredito)
	{
		$arr = array
		(
			'IdComprobante' 	=> DB::Number($oNotaCredito->IdComprobante),
			'IdCliente' 		=> DB::Number($oNotaCredito->IdCliente),
			'Importe' 			=> DB::Number($oNotaCredito->Importe),
			'Comentarios' 		=> DB::String($oNotaCredito->Comentarios),
			'Fecha'				=> DB::Date($oNotaCredito->Fecha),
			'IdFactura'			=> DB::Number($oNotaCredito->IdFactura),
			'Iva10'				=> DB::Number($oNotaCredito->Iva10),
			'Iva21'				=> DB::Number($oNotaCredito->Iva21),
			'ImpuestoInterno'	=> DB::Number($oNotaCredito->ImpuestoInterno),
			'IdMinuta'			=> DB::Number($oNotaCredito->IdMinuta),
			'Subtotal'			=> DB::Number($oNotaCredito->Subtotal),
			'PercepcionIIBB'	=> DB::Number($oNotaCredito->PercepcionIIBB)
		);
		
		return $arr;
	}
	
	public function Create(NotaCredito $oNotaCredito)
	{
		$arr = $this->GetArrayDB($oNotaCredito);
		
		if (!$this->Insert('TB_NotasCredito', $arr))
			return false;

		/* asignamos el id generado */
		$oNotaCredito->IdNotaCredito = DBAccess::GetLastInsertId();
			
		return $oNotaCredito;
	}
	
	
	public function Update(NotaCredito $oNotaCredito)
	{
		$where = " IdNotaCredito = " . DB::Number($oNotaCredito->IdNotaCredito);
		
		$arr = $this->GetArrayDB($oNotaCredito);
		
		if (!DBAccess::Update('TB_NotasCredito', $arr, $where))
			return false;
		
		return $oNotaCredito;
	}
	

	public function ExportXls(array $filter = NULL)
	{
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("Comprobante");
				
		foreach ($arrComprobantes as $oNotaCredito)
		{	
			/* almacenamos el registro */
			$arrData[] = array(trim($oNotaCredito->Nombre));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'comprobantes';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$oClientes = new Clientes();
		$oComprobantes = new Comprobantes();
	
		/* obtenemos el listado de datos a exportar */			
		$arrNotasCredito = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("NUMERO", "TIPO", "Factura", "FECHA", "IMPORTE", "CLIENTE", "CUIL");
				
		foreach ($arrNotasCredito as $oNotaCredito)
		{	
			$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante);
			$oFactura = $oComprobantes->GetById($oNotaCredito->IdFactura);
			$oCliente = $oClientes->GetById($oNotaCredito->IdCliente);
			/* almacenamos el registro */
			$arrData[] = array(trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero), trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)), trim($oFactura->Prefijo . ' - ' . $oFactura->Numero), trim(CambiarFecha($oNotaCredito->Fecha)), trim(number_format($oNotaCredito->Importe, 2, ',', '.')), trim($oCliente->RazonSocial), trim($oCliente->ClaveFiscalNumero));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'contabilidad_reporte';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
}

?>