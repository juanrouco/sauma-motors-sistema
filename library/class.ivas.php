<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.iva.php');
require_once('class.filter.php');
require_once('class.page.php');

class Ivas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	
	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Ivas i";		
		$sql.= " WHERE 1";
				
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
			
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT i.*";
		$sql.= " FROM TB_Ivas i";		
		$sql.= " WHERE 1";

		$sql.= " ORDER BY i.Nombre";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oIva = new Iva();
			$oIva->ParseFromArray($oRow);
			
			
			array_push($arr, $oIva);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	public function GetById($IdIva)
	{
		$sql = " SELECT i.*";
		$sql.= " FROM TB_Ivas i";
		$sql.= " WHERE i.IdIva = " . DB::Number($IdIva);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oIva = new Iva();
		$oIva->ParseFromArray($oRow);
		
		return $oIva;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT i.*";
		$sql.= " FROM TB_Ivas i";
		$sql.= " WHERE 1";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	
}

?>