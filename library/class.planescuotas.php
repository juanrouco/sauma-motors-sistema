<?php 

require_once('class.dbaccess.php');
require_once('class.formapago.php');
require_once('class.plancuota.php');
require_once('class.filter.php');
require_once('class.page.php');

class PlanesCuotas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE Disponible = 1';
		
		if (isset($filter['Nombre']) && $filter['Nombre'] != '')
			$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
			
		if (isset($filter['IdFormaPago']) && $filter['IdFormaPago'] != '')
			$sql.= " AND IdFormaPago = " . DB::Number($filter['IdFormaPago']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanesCuotas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPlanCuota";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPlanCuota = new PlanCuota();
			$oPlanCuota->ParseFromArray($oRow);
			
			array_push($arr, $oPlanCuota);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByFormaPago(FormaPago $oFormaPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanesCuotas";
		$sql.= " WHERE IdFormaPago = " . DB::Number($oFormaPago->IdFormaPago);
		$sql.= " ORDER BY Nombre";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPlanCuota = new PlanCuota();
			$oPlanCuota->ParseFromArray($oRow);
			
			array_push($arr, $oPlanCuota);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdPlanCuota)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanesCuotas";
		$sql.= " WHERE IdPlanCuota = " . DB::Number($IdPlanCuota);	
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPlanCuota = new PlanCuota();
		$oPlanCuota->ParseFromArray($oRow);
		
		return $oPlanCuota;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanesCuotas";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPlanCuota = new PlanCuota();
		$oPlanCuota->ParseFromArray($oRow);
		
		return $oPlanCuota;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanesCuotas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PlanCuota $oPlanCuota)
	{
		$arr = array
		(
			'Nombre'		=> DB::String($oPlanCuota->Nombre),
			'IdFormaPago'	=> DB::Number($oPlanCuota->IdFormaPago),
			'Interes'		=> DB::Number($oPlanCuota->Interes),
			'Coeficiente'	=> DB::Number($oPlanCuota->Coeficiente),
			'Disponible'	=> DB::Bool($oPlanCuota->Disponible)
		);
		
		if (!$this->Insert('TB_PlanesCuotas', $arr))
			return false;

		/* asignamos el id generado */
		$oPlanCuota->IdPlanCuota = DBAccess::GetLastInsertId();
			
		return $oPlanCuota;
	}
	
	
	public function Update(PlanCuota $oPlanCuota)
	{
		$where = " IdPlanCuota = " . DB::Number($oPlanCuota->IdPlanCuota);
		
		$arr = array
		(
			'Nombre'		=> DB::String($oPlanCuota->Nombre),
			'IdFormaPago'	=> DB::Number($oPlanCuota->IdFormaPago),
			'Interes'		=> DB::Number($oPlanCuota->Interes),
			'Coeficiente'	=> DB::Number($oPlanCuota->Coeficiente),
			'Disponible'	=> DB::Bool($oPlanCuota->Disponible)
		);
		
		if (!DBAccess::Update('TB_PlanesCuotas', $arr, $where))
			return false;
		
		return $oPlanCuota;
	}
	

	public function Delete($IdPlanCuota)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPlanCuota = " . DB::Number($IdPlanCuota);

		if (!DBAccess::Delete('TB_PlanesCuotas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>