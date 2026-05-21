<?php 

require_once('class.dbaccess.php');
require_once('class.sector.php');
require_once('class.filter.php');
require_once('class.page.php');

class Sectores extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Sectores";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oSector = new Sector();
			$oSector->ParseFromArray($oRow);
			
			array_push($arr, $oSector);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdSector)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Sectores";
		$sql.= " WHERE IdSector = " . DB::Number($IdSector);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oSector = new Sector();
		$oSector->ParseFromArray($oRow);
		
		return $oSector;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Sectores";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oSector = new Sector();
		$oSector->ParseFromArray($oRow);
		
		return $oSector;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Sectores";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Sector $oSector)
	{
		$arr = array
		(
			'Nombre' => DB::String($oSector->Nombre),
			'Codigo' => DB::String($oSector->Codigo)
		);
		
		if (!$this->Insert('TB_Sectores', $arr))
			return false;

		/* asignamos el id generado */
		$oSector->IdSector = DBAccess::GetLastInsertId();
			
		return $oSector;
	}
	
	
	public function Update(Sector $oSector)
	{
		$where = " IdSector = " . DB::Number($oSector->IdSector);
		
		$arr = array
		(
			'Nombre' => DB::String($oSector->Nombre),
			'Codigo' => DB::String($oSector->Codigo)
		);
		
		if (!DBAccess::Update('TB_Sectores', $arr, $where))
			return false;
		
		return $oSector;
	}
	

	public function Delete($IdSector)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdSector = " . DB::Number($IdSector);

		if (!DBAccess::Delete('TB_Sectores', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>