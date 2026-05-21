<?php 

require_once('class.dbaccess.php');
require_once('class.tipocosto.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposCosto extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposCosto";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoCosto = new TipoCosto();
			$oTipoCosto->ParseFromArray($oRow);
			
			array_push($arr, $oTipoCosto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdTipoCosto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCosto";
		$sql.= " WHERE IdTipoCosto = " . DB::Number($IdTipoCosto);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoCosto = new TipoCosto();
		$oTipoCosto->ParseFromArray($oRow);
		
		return $oTipoCosto;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCosto";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoCosto $oTipoCosto)
	{
		$arr = array
		(
			'Nombre' => DB::String($oTipoCosto->Nombre)
		);
		
		if (!$this->Insert('TB_TiposCosto', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoCosto->IdTipoCosto = DBAccess::GetLastInsertId();
			
		return $oTipoCosto;
	}
	
	
	public function Update(TipoCosto $oTipoCosto)
	{
		$where = " IdTipoCosto = " . DB::Number($oTipoCosto->IdTipoCosto);
		
		$arr = array
		(
			'Nombre' => DB::String($oTipoCosto->Nombre)
		);
		
		if (!DBAccess::Update('TB_TiposCosto', $arr, $where))
			return false;
		
		return $oTipoCosto;
	}
	

	public function Delete($IdTipoCosto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoCosto = " . DB::Number($IdTipoCosto);

		if (!DBAccess::Delete('TB_TiposCosto', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>