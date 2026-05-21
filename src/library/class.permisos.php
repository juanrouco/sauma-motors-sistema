<?php

require_once('class.dbaccess.php');
require_once('class.permiso.php');
require_once('class.filter.php');
require_once('class.page.php');

class Permisos extends DBAccess
{
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT p.*";
		$sql.= " FROM TB_Permisos p";
		$sql.= " WHERE p.Visible = '1'";		
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetAll(Page $oPage = NULL)
	{
		$sql = "SELECT p.*";
		$sql.= " FROM TB_Permisos p";
		$sql.= " WHERE p.Visible = '1'";		
		$sql.= " ORDER BY IdPermiso";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPermiso = new Permiso();
			$oPermiso->ParseFromArray($oRow);
			
			array_push($arr, $oPermiso);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetLastInsertId()
	{
		$sql = "SELECT IdPermiso";
		$sql.= " FROM TB_Permisos p";
		$sql.= " ORDER BY IdPermiso DESC";
		$sql.= " LIMIT 1";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$LastInsertId = $oRow['IdPermiso'];

		return $LastInsertId;
	}


	public function GetById($IdPermiso)
	{
		$sql = "SELECT p.*";
		$sql.= " FROM TB_Permisos p";
		$sql.= " WHERE p.IdPermiso = ".DB::Number($IdPermiso);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPermiso = new Permiso();
		$oPermiso->ParseFromArray($oRow);
		
		return $oPermiso;
	}
}
?>