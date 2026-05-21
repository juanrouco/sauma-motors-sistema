<?php
require_once('class.dbaccess.php');
require_once('class.cajamovimiento.php');
require_once('class.filter.php');
require_once('class.cajasdetalles.php');
require_once('class.cajas.php');
require_once('class.conceptoscajas.php');
require_once('class.tiposmovimientoscaja.php');
require_once('class.pagos.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.cuentasgestoria.php');
require_once('class.usuarios.php');
require_once('excel_export/class.xlsexport.php');
require_once('excel_reader/class.xlsreader.php');

class CajasMovimientos extends DBAccess
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '')
			$sql.= ' AND Fecha >= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '')
			$sql.= ' AND Fecha <= ' . DB::Date($filter['FechaHasta'] . ' 23:59:00');
			
		if (isset($filter['IdTipoMovimiento']) && $filter['IdTipoMovimiento'] != '')
			$sql.= ' AND IdTipoMovimiento = ' . DB::Number($filter['IdTipoMovimiento']);
			
		if (isset($filter['IdCajaDetalle']) && $filter['IdCajaDetalle'] != '')
			$sql.= ' AND IdCajaDetalle = ' . DB::Number($filter['IdCajaDetalle']);
			
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= ' AND IdUsuario = ' . DB::Number($filter['IdUsuario']);
			
		if (isset($filter['IdConcepto']) && $filter['IdConcepto'] != '')
			$sql.= ' AND IdConcepto = ' . DB::Number($filter['IdConcepto']);
		
		if (isset($filter['Ingresos']) && $filter['Ingresos'] == '1')
			$sql.= ' AND Total > 0';
		
		if (isset($filter['Egresos']) && $filter['Egresos'] == '1')
			$sql.= ' AND Total < 0';
		
		return $sql;
	}	
	
	public function GetById($IdCajaMovimiento)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE cd.IdCajaMovimiento = ".DB::Number($IdCajaMovimiento);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCajaMovimiento = new CajaMovimiento();
		$oCajaMovimiento->ParseFromArray($oRow);
		
		return $oCajaMovimiento;			
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM tb_CajasMovimientos c";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetCountRowsEgresos(array $filter = NULL)
	{
		$sql = "SELECT Aux.*";
		$sql.= " FROM (SELECT cd.Total, cd.Fecha, IF(Total > 0, 'INGRESOS', 'EGRESOS') AS TipoMovimiento";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC) AS Aux";
		$sql.= " GROUP BY DATE(Aux.Fecha), Aux.TipoMovimiento";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function GetAllEgresos(array $filter = NULL)
	{   
		$sql = "SELECT SUM(Aux.Total) AS Total, Aux.Fecha, Aux.TipoMovimiento";
		$sql.= " FROM (SELECT cd.Total, DATE(cd.Fecha) AS Fecha, IF(Total > 0, 'INGRESOS', 'EGRESOS') AS TipoMovimiento";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC) AS Aux";
		$sql.= " GROUP BY DATE(Aux.Fecha), Aux.TipoMovimiento";
		$sql.= " ORDER BY Aux.Fecha DESC";
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new stdClass();
						
			$oCajaMovimiento->Fecha = $oRow['Fecha'];
			$oCajaMovimiento->Total = $oRow['Total'];
			$oCajaMovimiento->TipoMovimiento = $oRow['TipoMovimiento'];
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	
	public function GetAllEgresosSenias(array $filter = NULL)
	{   
		$sql = "SELECT SUM(Aux.Total) AS Total, Aux.Fecha, Aux.TipoMovimiento";
		$sql.= " FROM (SELECT cd.Total, DATE(cd.Fecha) AS Fecha, 'INGRESOS' AS TipoMovimiento";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC) AS Aux";
		$sql.= " GROUP BY DATE(Aux.Fecha), Aux.TipoMovimiento";
		$sql.= " ORDER BY Aux.Fecha DESC";
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new stdClass();
						
			$oCajaMovimiento->Fecha = $oRow['Fecha'];
			$oCajaMovimiento->Total = $oRow['Total'];
			$oCajaMovimiento->TipoMovimiento = $oRow['TipoMovimiento'];
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	public function GetAllFechasMovimiento(array $filter = NULL, Page $oPage = NULL)
	{   
		$sql = "SELECT SUM(Total) AS Total, cd.Fecha";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY DATE(Fecha)";
		$sql.= " ORDER BY DATE(Fecha) DESC";
			
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new stdClass();
						
			$oCajaMovimiento->Fecha = $oRow['Fecha'];
			$oCajaMovimiento->Total = $oRow['Total'];
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	public function GetCountRowsFechasMovimiento(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY DATE(Fecha)";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetAll(array $filter = NULL)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha ASC";
			
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new CajaMovimiento();
						
			$oCajaMovimiento->ParseFromArray($oRow);
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	
	public function GetAllByIdCajaDetalle($IdCajaDetalle)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE cd.IdCajaDetalle = " . DB::Number($IdCajaDetalle);
		$sql.= " ORDER BY cd.Fecha ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new CajaMovimiento();
						
			$oCajaMovimiento->ParseFromArray($oRow);
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	public function GetByIdEntidad($IdTipoMovimiento, $IdEntidad, $IdCajaDetalle = null)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE cd.IdEntidad = " . DB::Number($IdEntidad);
		$sql.= " AND cd.IdTipoMovimiento = " . DB::Number($IdTipoMovimiento);
		if ($IdCajaDetalle)
			$sql.= " AND cd.IdCajaDetalle = " . DB::Number($IdCajaDetalle);
		$sql.= " ORDER BY cd.IdCajaMovimiento DESC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
	
		if (!$oRow = $oRes->GetRow())	
			return false;
			
			$oCajaMovimiento = new CajaMovimiento();
						
			$oCajaMovimiento->ParseFromArray($oRow);
		
		return $oCajaMovimiento;			
	}
	
	public function GetAllByIdEntidad($IdTipoMovimiento, $IdEntidad)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE cd.IdEntidad = " . DB::Number($IdEntidad);
		$sql.= " AND cd.IdTipoMovimiento = " . DB::Number($IdTipoMovimiento);
		$sql.= " ORDER BY cd.IdCajaMovimiento DESC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimiento = new CajaMovimiento();
						
			$oCajaMovimiento->ParseFromArray($oRow);
			array_push($arr, $oCajaMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetByIdCajaDetalleIdEntidad($IdCajaDetalle, $IdTipoMovimiento, $IdEntidad)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasMovimientos cd";
		$sql.= " WHERE cd.IdEntidad = " . DB::Number($IdEntidad);
		$sql.= " AND cd.IdTipoMovimiento = " . DB::Number($IdTipoMovimiento);
		$sql.= " AND cd.IdCajaDetalle = " . DB::Number($IdCajaDetalle);
		$sql.= " ORDER BY cd.IdCajaMovimiento DESC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
	
		if (!$oRow = $oRes->GetRow())	
			return false;
			
			$oCajaMovimiento = new CajaMovimiento();
						
			$oCajaMovimiento->ParseFromArray($oRow);
		
		return $oCajaMovimiento;			
	}

	private function GetArrayDB(CajaMovimiento $oCajaMovimiento)
	{
		$arr = array
		(
			'IdCajaDetalle'		=> DB::Number($oCajaMovimiento->IdCajaDetalle),
			'IdTipoMovimiento' 	=> DB::Number($oCajaMovimiento->IdTipoMovimiento),
			'IdCajaDestino' 	=> DB::Number($oCajaMovimiento->IdCajaDestino),
			'IdCajaOrigen' 		=> DB::Number($oCajaMovimiento->IdCajaOrigen),
			'Fecha' 			=> DB::Date($oCajaMovimiento->Fecha),
			'Total' 			=> DB::Number($oCajaMovimiento->Total),
			'IdEntidad' 		=> DB::Number($oCajaMovimiento->IdEntidad),
			'IdConcepto' 		=> DB::Number($oCajaMovimiento->IdConcepto),
			'Comentarios'		=> DB::String($oCajaMovimiento->Comentarios),
			'IdUsuario'			=> DB::Number($oCajaMovimiento->IdUsuario)
		);
		
		return $arr;
	}

	public function Create(CajaMovimiento $oCajaMovimiento)
	{	
		$arr = $this->GetArrayDB($oCajaMovimiento);
		
		if ( !$this->Insert('tb_CajasMovimientos', $arr) )
			return false;
		
		
		$oCajaMovimiento->IdCajaMovimiento = DBAccess::GetLastInsertId();
			
		return $oCajaMovimiento;
	}
	
	public function Update(CajaMovimiento $oCajaMovimiento)
	{
		$where = " IdCajaMovimiento = " . (int)$oCajaMovimiento->IdCajaMovimiento;
		

		$arr = $this->GetArrayDB($oCajaMovimiento);
				
		if ( !DBAccess::Update('tb_CajasMovimientos', $arr, $where) )
			return false;
			
		return $oCajaMovimiento;
	}

	public function Delete($IdCajaMovimiento)
	{	
		$where = " IdCajaMovimiento = " . (int)$IdCajaMovimiento;

		if ( !DBAccess::Delete('tb_CajasMovimientos', $where) )
			return false;
		
		return true;
	}	
	

	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oPagos				= new Pagos();
		$oMinutas			= new Minutas();
		$oClientes			= new Clientes();
		$oUnidades			= new Unidades();
		$oModelos			= new Modelos();
		$oCuentasGestorias	= new CuentasGestoria();
		$oUsuarios			= new Usuarios();

		/* obtenemos el listado de datos a exportar */			
		$arrCajaDetalles = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
			(
				"FECHA",
				"TIPO MOVIMIENTO",
				"CONCEPTO",
				"USUARIO",
				"DETALLE",
				"INGRESO",
				"EGRESO"
			);
				
		foreach ($arrCajaDetalles as $oCajaMovimiento)
		{	
			$Ingreso = 0;
			$Egreso = 0;
			$Concepto = 'N/A';
			if ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Egreso || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Gastos)
				$Concepto = ConceptosCajas::GetById($oCajaMovimiento->IdConcepto);
			
			$Observaciones = $oCajaMovimiento->Comentarios;
			if  ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Pago)
			{
				$oPago = $oPagos->GetById($oCajaMovimiento->IdEntidad);
				$oMinuta = $oMinutas->GetById($oPago->IdMinuta);
				$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
				$oModelo = $oModelos->GetById($oUnidad->IdModelo);
				$oCliente = $oClientes->GetById($oMinuta->IdCliente);
				
				$Observaciones =  'Pago Unidad: ' . $oPago->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' - ' . $oPago->Observaciones . ' ' . $oCajaMovimiento->Comentarios; 
			}
			elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
			{
				$oCuentaGestoria = $oCuentasGestorias->GetById($oCajaMovimiento->IdEntidad);
				$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
				$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
				$oModelo = $oModelos->GetById($oUnidad->IdModelo);
				$oCliente = $oClientes->GetById($oMinuta->IdCliente);
				
				$Observaciones =  'Nro. Carpeta: ' . $oMinuta->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' ' . $oCajaMovimiento->Comentarios; 
			}
			elseif  ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::PagoPV)
			{
				$Observaciones =  $oCajaMovimiento->GetDetalle() . ' ' . $oCajaMovimiento->Comentarios; 
			}
			
			$Ingreso = $oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0;
			$Egreso = $oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0;
			
			$oUsuario = $oUsuarios->GetById($oCajaMovimiento->IdUsuario);
			if ($oUsuario)
				$Usuario = $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
			else
				$Usuario = 'N/A';
			
			/* almacenamos el registro */
			$arrData[] = array
				(
					trim(CambiarFechaHora($oCajaMovimiento->Fecha)),
					trim(TiposMovimientosCaja::GetById($oCajaMovimiento->IdTipoMovimiento)),
					trim($Concepto),
					trim($Usuario),
					trim($Observaciones),
					trim(number_format($Ingreso, 2, '.', '')),
					trim(number_format($Egreso, 2, '.', ''))
				);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'caja';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>