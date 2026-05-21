<?php 

require_once('class.dbaccess.php');
require_once('class.minutapago.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.page.php');

class MinutasPago extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if ($filter['IdMinutaPago'])
			$sql.= ' AND IdMinutaPago = ' . DB::Number($filter['IdMinutaPago']);
			
		if ($filter['IdUnidad'])
			$sql.= ' AND IdMinutaPago IN (SELECT IdMinutaPago FROM TB_MinutasPagoItems WHERE IdUnidad = ' . DB::Number($filter['IdUnidad']) . ' )';
			
		if ($filter['IdProveedor'])
			$sql.= ' AND IdMinutaPago IN (SELECT mpi.IdMinutaPago FROM TB_MinutasPagoItems mpi INNER JOIN TB_Unidades u ON u.IdUnidad = mpi.IdUnidad WHERE u.IdProveedor = ' . DB::Number($filter['IdProveedor']) . ' )';
			
		if ($filter['Fecha'])
			$sql.= ' AND Fecha = ' . DB::Date($filter['Fecha']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPago";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdMinutaPago";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPago = new MinutaPago();
			$oMinutaPago->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdMinutaPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPago";
		$sql.= " WHERE IdMinutaPago = " . DB::Number($IdMinutaPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaPago = new MinutaPago();
		$oMinutaPago->ParseFromArray($oRow);
		
		return $oMinutaPago;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPago";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdMinutaPago";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(MinutaPago $oMinutaPago)
	{
		$arr = array
		(
			'Fecha'  			=> DB::Date($oMinutaPago->Fecha),
			'IdEstado'			=> DB::Number($oMinutaPago->IdEstado),
			'MontoDisponible' 	=> DB::Number($oMinutaPago->MontoDisponible),
			'Observaciones' 	=> DB::String($oMinutaPago->Observaciones)
		);
		
		if (!$this->Insert('TB_MinutasPago', $arr))
			return false;

		/* asignamos el id generado */
		$oMinutaPago->IdMinutaPago = DBAccess::GetLastInsertId();
			
		return $oMinutaPago;
	}
	
	
	public function Update(MinutaPago $oMinutaPago)
	{
		$where = " IdMinutaPago = " . DB::Number($oMinutaPago->IdMinutaPago);
		
		$arr = array
		(
			'Fecha'  			=> DB::Date($oMinutaPago->Fecha),
			'IdEstado'			=> DB::Number($oMinutaPago->IdEstado),
			'MontoDisponible' 	=> DB::Number($oMinutaPago->MontoDisponible),
			'Observaciones' 	=> DB::String($oMinutaPago->Observaciones)
		);
		
		if (!DBAccess::Update('TB_MinutasPago', $arr, $where))
			return false;
		
		return $oMinutaPago;
	}
	

	public function Delete($IdMinutaPago)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMinutaPago = " . DB::Number($IdMinutaPago);

		if (!DBAccess::Delete('TB_MinutasPago', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>