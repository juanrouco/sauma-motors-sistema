<?php 

require_once('class.dbaccess.php');
require_once('class.acreedor.php');
require_once('class.acreedorcuota.php');
require_once('class.filter.php');
require_once('class.page.php');

class AcreedoresCuotas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE Disponible = 1';
		
		if (isset($filter['Cuotas']) && $filter['Cuotas'] != '')
			$sql.= " AND Cuotas = " . DB::Number($filter['Cuotas']);
			
		if (isset($filter['IdAcreedor']) && $filter['IdAcreedor'] != '')
			$sql.= " AND IdAcreedor = " . DB::Number($filter['IdAcreedor']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdAcreedorCuota";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oAcreedorCuota = new AcreedorCuota();
			$oAcreedorCuota->ParseFromArray($oRow);
			
			array_push($arr, $oAcreedorCuota);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByAcreedor(Acreedor $oAcreedor)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= " WHERE IdAcreedor = " . DB::Number($oAcreedor->IdAcreedor);
		$sql.= " ORDER BY Cuotas";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oAcreedorCuota = new AcreedorCuota();
			$oAcreedorCuota->ParseFromArray($oRow);
			
			array_push($arr, $oAcreedorCuota);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByAcreedorAndCuota(Acreedor $oAcreedor, $Cuota)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= " WHERE IdAcreedor = " . DB::Number($oAcreedor->IdAcreedor);
		$sql.= " AND Cuotas = " . DB::Number($Cuota);
		$sql.= " AND Disponible = 1";
		$sql.= " ORDER BY Cuotas";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oAcreedorCuota = new AcreedorCuota();
		$oAcreedorCuota->ParseFromArray($oRow);
		
		return $oAcreedorCuota;	
	}
	

	public function GetById($IdAcreedorCuota)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= " WHERE IdAcreedorCuota = " . DB::Number($IdAcreedorCuota);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oAcreedorCuota = new AcreedorCuota();
		$oAcreedorCuota->ParseFromArray($oRow);
		
		return $oAcreedorCuota;		
	}
	

	public function GetByCuotas($Cuotas)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= " WHERE Cuotas = " . DB::Numer($Cuotas);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oAcreedorCuota = new AcreedorCuota();
		$oAcreedorCuota->ParseFromArray($oRow);
		
		return $oAcreedorCuota;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_AcreedoresCuotas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Cuotas";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(AcreedorCuota $oAcreedorCuota)
	{
		$arr = array
		(
			'Cuotas'		=> DB::Number($oAcreedorCuota->Cuotas),
			'IdAcreedor'	=> DB::Number($oAcreedorCuota->IdAcreedor),
			'Interes'		=> DB::Number($oAcreedorCuota->Interes),
			'Coeficiente'	=> DB::Number($oAcreedorCuota->Coeficiente),
			'Disponible'	=> DB::Bool($oAcreedorCuota->Disponible)
		);
		
		if (!$this->Insert('TB_AcreedoresCuotas', $arr))
			return false;

		/* asignamos el id generado */
		$oAcreedorCuota->IdAcreedorCuota = DBAccess::GetLastInsertId();
			
		return $oAcreedorCuota;
	}
	
	
	public function Update(AcreedorCuota $oAcreedorCuota)
	{
		$where = " IdAcreedorCuota = " . DB::Number($oAcreedorCuota->IdAcreedorCuota);
		
		$arr = array
		(
			'Cuotas'		=> DB::Number($oAcreedorCuota->Cuotas),
			'IdAcreedor'	=> DB::Number($oAcreedorCuota->IdAcreedor),
			'Interes'		=> DB::Number($oAcreedorCuota->Interes),
			'Coeficiente'	=> DB::Number($oAcreedorCuota->Coeficiente),
			'Disponible'	=> DB::Bool($oAcreedorCuota->Disponible)
		);
		
		if (!DBAccess::Update('TB_AcreedoresCuotas', $arr, $where))
			return false;
		
		return $oAcreedorCuota;
	}
	

	public function Delete($IdAcreedorCuota)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdAcreedorCuota = " . DB::Number($IdAcreedorCuota);

		if (!DBAccess::Delete('TB_AcreedoresCuotas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>