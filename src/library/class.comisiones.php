<?php 

require_once('class.dbaccess.php');
require_once('class.comision.php');
require_once('class.filter.php');
require_once('class.page.php');

class Comisiones extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comisiones";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdComision DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComision = new Comision();
			$oComision->ParseFromArray($oRow);
			
			array_push($arr, $oComision);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdComision)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comisiones";
		$sql.= " WHERE IdComision = " . DB::Number($IdComision);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComision = new Comision();
		$oComision->ParseFromArray($oRow);
		
		return $oComision;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_Comisiones fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComision = new Comision();
		$oComision->ParseFromArray($oRow);
		
		return $oComision;		
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comisiones";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComision = new Comision();
		$oComision->ParseFromArray($oRow);
		
		return $oComision;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comisiones";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Comision $oComision)
	{
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oComision->IdMinuta),
			'IndiceComision' 	=> DB::Number($oComision->IndiceComision)
		);
		
		if (!$this->Insert('TB_Comisiones', $arr))
			return false;

		/* asignamos el id generado */
		$oComision->IdComision = DBAccess::GetLastInsertId();
			
		return $oComision;
	}
	
	public function Update(Comision $oComision)
	{
		$where = " IdComision = " . DB::Number($oComision->IdComision);
		
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oComision->IdMinuta),
			'IndiceComision' 	=> DB::Number($oComision->IndiceComision)
		);
		
		if (!DBAccess::Update('TB_Comisiones', $arr, $where))
			return false;
		
		return $oComision;
	}
	
	
	public function Delete($IdComision)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdComision = " . DB::Number($IdComision);

		if (!DBAccess::Delete('TB_Comisiones', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	
}

?>