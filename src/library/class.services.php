<?php 

require_once('class.dbaccess.php');
require_once('class.service.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_reader/class.xlsreader.php');
require_once('excel_export/class.xlsexport.php');


class Services extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
		{
			$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		}

		if ((isset($filter['Disponible'])) && ($filter['Disponible'] != ''))
			$sql.= " AND Disponible = " . DB::Bool($filter['Disponible']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Services";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Orden ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oService = new Service();
			$oService->ParseFromArray($oRow);
			
			array_push($arr, $oService);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Services";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY Nombre";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oService = new Service();
			$oService->ParseFromArray($oRow);
			
			array_push($arr, $oService);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdService)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Services";
		$sql.= " WHERE IdService = " . DB::Number($IdService);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oService = new Service();
		$oService->ParseFromArray($oRow);
		
		return $oService;		
	}

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Services";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oService = new Service();
		$oService->ParseFromArray($oRow);
		
		return $oService;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Services";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdNombre DESC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayForSql(Service $oService)
	{
		$arr = array
		(
			'Nombre' 					=> DB::String($oService->Nombre),
			'Disponible' 				=> DB::Bool($oService->Disponible),
			'Imagen' 					=> DB::String($oService->Imagen)
		);
		
		return $arr;
	}
	
	public function Create(Service $oService)
	{
		$arr = $this->GetArrayForSql($oService);
		
		if (!$this->Insert('TB_Services', $arr))
			return false;

		/* asignamos el id generado */
		$oService->IdService = DBAccess::GetLastInsertId();
			
		return $oService;
	}
	
	
	public function Update(Service $oService)
	{
		$where = " IdService = " . DB::Number($oService->IdService);
		
		$arr = $this->GetArrayForSql($oService);
		
		if (!DBAccess::Update('TB_Services', $arr, $where))
			return false;
		
		return $oService;
	}
	

	public function Delete($IdService)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdService = " . DB::Number($IdService);

		if (!DBAccess::Delete('TB_Services', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>