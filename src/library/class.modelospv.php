<?php 

require_once('class.dbaccess.php');
require_once('class.modelopv.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_reader/class.xlsreader.php');
require_once('excel_export/class.xlsexport.php');


class ModelosPV extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
		{
			$sql.= " AND (Modelo LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%'";
			$sql.= " OR Modelo IS NULL)";
		}

		if ((isset($filter['Disponible'])) && ($filter['Disponible'] != ''))
			$sql.= " AND Disponible = " . DB::Bool($filter['Disponible']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Modelo ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloPV = new ModeloPV();
			$oModeloPV->ParseFromArray($oRow);
			
			array_push($arr, $oModeloPV);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY Modelo";
		$sql.= " ORDER BY Modelo";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloPV = new ModeloPV();
			$oModeloPV->ParseFromArray($oRow);
			
			array_push($arr, $oModeloPV);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdModeloPV)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosPV";
		$sql.= " WHERE IdModeloPV = " . DB::Number($IdModeloPV);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModeloPV = new ModeloPV();
		$oModeloPV->ParseFromArray($oRow);
		
		return $oModeloPV;		
	}

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosPV";
		$sql.= " WHERE Modelo RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModeloPV = new ModeloPV();
		$oModeloPV->ParseFromArray($oRow);
		
		return $oModeloPV;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayForSql(ModeloPV $oModeloPV)
	{
		$arr = array
		(
			'Modelo' 					=> DB::String($oModeloPV->Modelo),
			'Disponible' 				=> DB::Bool($oModeloPV->Disponible),
			'Imagen' 					=> DB::String($oModeloPV->Imagen)
		);
		
		return $arr;
	}
	
	public function Create(ModeloPV $oModeloPV)
	{
		$arr = $this->GetArrayForSql($oModeloPV);
		
		if (!$this->Insert('TB_ModelosPV', $arr))
			return false;

		/* asignamos el id generado */
		$oModeloPV->IdModeloPV = DBAccess::GetLastInsertId();
			
		return $oModeloPV;
	}
	
	
	public function Update(ModeloPV $oModeloPV)
	{
		$where = " IdModeloPV = " . DB::Number($oModeloPV->IdModeloPV);
		
		$arr = $this->GetArrayForSql($oModeloPV);
		
		if (!DBAccess::Update('TB_ModelosPV', $arr, $where))
			return false;
		
		return $oModeloPV;
	}
	

	public function Delete($IdModeloPV)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdModeloPV = " . DB::Number($IdModeloPV);

		if (!DBAccess::Delete('TB_ModelosPV', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>