<?php 

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.page.php');

class CostosManoObra extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1=1';
		return $sql;
	}
	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CostosManoObra";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";		
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
		return false;
		
		return $oRow['Costo'];
	}
	
	public function GetLast()
	{
		return $this->GetAll();
	}
	
	public function Update($Costo)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array('Costo' => $Costo);		
		
		$where = " 1 = 1";
		
		if (!DBAccess::Update('TB_CostosManoObra', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $Costo;
	}
}

?>