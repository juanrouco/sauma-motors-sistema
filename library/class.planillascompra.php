<?php 

require_once('class.dbaccess.php');
require_once('class.planillacompra.php');
require_once('class.filter.php');
require_once('class.page.php');

class PlanillasCompra extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['IdPlanillaCompra'])) && ($filter['IdPlanillaCompra'] != ''))
			$sql.= " AND IdPlanillaCompra = " . DB::Number($filter['IdPlanillaCompra']);

		if ((isset($filter['FechaCarga'])) && ($filter['FechaCarga'] != ''))
			$sql.= " AND FechaCarga = " . DB::Date($filter['FechaCarga']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasCompra";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPlanillaCompra DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPlanillaCompra = new PlanillaCompra();
			$oPlanillaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oPlanillaCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdPlanillaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasCompra";
		$sql.= " WHERE IdPlanillaCompra = " . DB::Number($IdPlanillaCompra);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPlanillaCompra = new PlanillaCompra();
		$oPlanillaCompra->ParseFromArray($oRow);
		
		return $oPlanillaCompra;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasCompra";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Generate()
	{
		$oPlanillaCompra = new PlanillaCompra();
		$oPlanillaCompra->FechaCarga = date('Y-m-d');
		
		$arr = array('FechaCarga' => DB::Date($oPlanillaCompra->FechaCarga));
		
		if (!$this->Insert('TB_PlanillasCompra', $arr))
			return false;

		/* asignamos el id generado */
		$oPlanillaCompra->IdPlanillaCompra = DBAccess::GetLastInsertId();
			
		return $oPlanillaCompra;
	}
	
	
	public function Delete($IdPlanillaCompra)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPlanillaCompra = " . DB::Number($IdPlanillaCompra);

		$arr = array
		(
			'IdPlanillaCompra' 	=> NULL,
			'ImporteCompraNeto' => NULL
		);
		
		if (!DBAccess::Update('TB_Unidades', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_PlanillasCompra', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>