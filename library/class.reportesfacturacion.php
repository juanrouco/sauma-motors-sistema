<?php 

require_once('class.dbaccess.php');
require_once('class.reportefacturacion.php');
require_once('class.filter.php');
require_once('class.page.php');

class ReportesFacturacion extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdReporteFacturacion'])) && ($filter['IdReporteFacturacion'] != ''))
			$sql.= " AND IdReporteFacturacion = " . DB::Number($filter['IdReporteFacturacion']);

		if ((isset($filter['FechaReporte'])) && ($filter['FechaReporte'] != ''))
			$sql.= " AND FechaReporte = " . DB::Date($filter['FechaReporte']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ReportesFacturacion";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdReporteFacturacion DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oReporteFacturacion = new ReporteFacturacion();
			$oReporteFacturacion->ParseFromArray($oRow);
			
			array_push($arr, $oReporteFacturacion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdReporteFacturacion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ReportesFacturacion";
		$sql.= " WHERE IdReporteFacturacion = " . DB::Number($IdReporteFacturacion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oReporteFacturacion = new ReporteFacturacion();
		$oReporteFacturacion->ParseFromArray($oRow);
		
		return $oReporteFacturacion;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ReportesFacturacion";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create()
	{
		$oUnidades = new Unidades();
		
		$oReporteFacturacion = new ReporteFacturacion();
		$oReporteFacturacion->FechaReporte = date('Y-m-d');
		
		$arr = array('FechaReporte' => DB::String($oReporteFacturacion->FechaReporte));
		
		if (!$this->Insert('TB_ReportesFacturacion', $arr))
			return false;

		/* asignamos el id generado */
		$oReporteFacturacion->IdReporteFacturacion = DBAccess::GetLastInsertId();
		
		/* obtenemos las unidades facturadas que aun no estan informadas */
		$filter = array();
		$filter['IdEstado'] = EstadoUnidad::Facturado;
		$arrUnidades = $oUnidades->GetAll($filter);
		
		foreach ($arrUnidades as $oUnidad)
		{
			if ($oUnidad->IdReporteFacturacion == '')
			{
				$oUnidad->IdReporteFacturacion = $oReporteFacturacion->IdReporteFacturacion;

				$oUnidades->Update($oUnidad);
			}
		}
			
		return true;
	}
}

?>