<?php 

require_once('class.dbaccess.php');
require_once('class.facturausado.php');
require_once('class.minutasusados.php');
require_once('class.clientes.php');
require_once('class.usados.php');
require_once('class.modelos.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.clientetipos.php');
require_once('class.comprobantes.php');
require_once('class.filter.php');
require_once('class.page.php');

class FacturaUsados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['NumeroComprobante'])) && ($filter['NumeroComprobante'] != ''))
			$sql.= " AND NumeroComprobante LIKE '%" . DB::StringUnquoted($filter['NumeroComprobante']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUsados";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFactura DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaUsado = new FacturaUsado();
			$oFacturaUsado->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUsados";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUsado = new FacturaUsado();
		$oFacturaUsado->ParseFromArray($oRow);
		
		return $oFacturaUsado;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUsados fu";
		$sql.= " INNER JOIN TB_Comprobantes c on fu.IdComprobante = c.IdComprobante";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND c.IdEstado <> " . DB::Number(3);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUsado = new FacturaUsado();
		$oFacturaUsado->ParseFromArray($oRow);
		
		return $oFacturaUsado;		
	}


	public function GetByMinuta(MinutaUsado $oMinutaUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUsados";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinutaUsado->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUsado = new FacturaUsado();
		$oFacturaUsado->ParseFromArray($oRow);
		
		return $oFacturaUsado;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUsados";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(FacturaUsado $oFacturaUsado)
	{
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oFacturaUsado->IdMinuta),
			'IdComprobante' 	=> DB::Number($oFacturaUsado->IdComprobante),
			'NumeroComprobante' => DB::String($oFacturaUsado->NumeroComprobante),
			'Fecha' 			=> DB::Date($oFacturaUsado->Fecha),
			'Subtotal' 			=> DB::Number($oFacturaUsado->Subtotal),
			'Iva10' 			=> DB::Number($oFacturaUsado->Iva10),
			'Iva21' 			=> DB::Number($oFacturaUsado->Iva21),
			'ImpuestoInterno'	=> DB::Number($oFacturaUsado->ImpuestoInterno),
			'Total' 			=> DB::Number($oFacturaUsado->Total),
			'OtrosTitulares' 	=> DB::String($oFacturaUsado->OtrosTitulares),
			'Observaciones' 	=> DB::String($oFacturaUsado->Observaciones)
		);
		
		if (!$this->Insert('TB_FacturaUsados', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaUsado->IdFactura = DBAccess::GetLastInsertId();
			
		return $oFacturaUsado;
	}
	
	
	public function Delete($IdFactura)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFactura = " . DB::Number($IdFactura);

		if (!DBAccess::Delete('TB_FacturaUsados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	private function Completar($valor, $caracter, $largo)
	{
		$relleno = '';
     
		if (strlen($valor) < $largo)
		{
			$l = intval($largo) - strlen($valor);
			$relleno = str_repeat($caracter, $l);
		}
		return substr($relleno . $valor, 0, $largo);
	}
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$oComprobantes = new Comprobantes();
		$oMinutaUsados = new MinutasUsados();
		$oClientes = new Clientes();
		$oUsados = new Usados();
		
		$arrFacturas = $this->GetAll($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIT", 
			"NETO", 
			'IVA 10.5%', 
			'IVA 21%',
			'IMPUESTO INTERNO',
			'TOTAL',
			'NRO VIN',
			'DESCRIPCION DEL MODELO',
			'COSTO');
		
		foreach ($arrFacturas as $oFactura)
		{
			$oComprobante = $oComprobantes->GetById($oFactura->IdComprobante);
			$oMinutaUsado = $oMinutaUsados->GetById($oFactura->IdMinuta);
			$oCliente = $oClientes->GetById($oMinutaUsado->IdCliente);
			$oUsado = $oUsados->GetById($oMinutaUsado->IdUsado);
			
			$subtotal = $oFactura->Subtotal;
			$iva10 = $oFactura->Iva10;
			$iva21 = $oFactura->Iva21;
			if (($oFactura->Iva10 == 0 || $oFactura->Iva10 == '' || !$oFactura->Iva10) && ($oFactura->Iva21 == 0 || $oFactura->Iva21 == '' || !$oFactura->Iva21))
			{
				if (($oFactura->Iva10 == 0 || $oFactura->Iva10 == '' || !$oFactura->Iva10) && $oModelo->Iva == 10.5)
				{
					$subtotal = $subtotal / 1.105;
					$iva10 = $subtotal * 0.105;
				}
				elseif (($oFactura->Iva21 == 0 || $oFactura->Iva21 == '' || !$oFactura->Iva21) && $oModelo->Iva == 21)
				{
					$subtotal = $subtotal / 1.21;
					$iva21 = $subtotal * 0.21;
				}
			}
			
			
			$arrData[] = array(
				trim(CambiarFecha($oFactura->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				
				
					
				trim(number_format($subtotal, 2, ',', '.')),
				trim(number_format($iva10, 2, ',', '.')),
				trim(number_format($iva21, 2, ',', '.')),
				trim(number_format($oFactura->ImpuestoInterno, 2, ',', '.')),
				trim(number_format($oFactura->Total, 2, ',', '.')),
				trim($oUsado->NumeroVin),
				trim($oUsado->Modelo),
				trim(number_format($oUsado->Valuacion, 2, ',', '.'))
				);
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'factura_usados';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function GetByIdComprobante($IdComprobante)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUsados fu";
		$sql.= " INNER JOIN TB_Comprobantes c on fu.IdComprobante = c.IdComprobante";
		$sql.= " WHERE fu.IdComprobante = " . DB::Number($IdComprobante);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUsado = new FacturaUsado();
		$oFacturaUsado->ParseFromArray($oRow);
		
		return $oFacturaUsado;		
	}
}

?>