<?php 

require_once('class.dbaccess.php');
require_once('class.minutausadofinanciacion.php');
require_once('class.filter.php');
require_once('class.page.php');

class MinutasUsadosFinanciacion extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND m.IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		if ((isset($filter['IdAcreedor'])) && ($filter['IdAcreedor'] != ''))
			$sql.= " AND pa.IdAcreedor = " . DB::Number($filter['IdAcreedor']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT pa.*";
		$sql.= " FROM TB_MinutasUsadosFinanciacion pa";
		$sql.= " INNER JOIN TB_MinutasUsados m ON pa.IdMinuta = m.IdMinuta";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY pa.IdMinutaFinanciacion DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaUsadoFinanciacion = new MinutaUsadoFinanciacion();
			$oMinutaUsadoFinanciacion->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsadoFinanciacion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdMinutaIdAcreedor($IdMinuta, $IdAcreedor)
	{
		$sql = "SELECT SUM(Importe) AS Total";
		$sql.= " FROM TB_MinutasUsadosFinanciacion";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND IdAcreedor = " . DB::Number($IdAcreedor);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];			
	}
	

	public function GetByMinuta(MinutaUsado $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsadosFinanciacion";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaUsadoFinanciacion = new MinutaUsadoFinanciacion();
			$oMinutaUsadoFinanciacion->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsadoFinanciacion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsadosFinanciacion";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaUsadoFinanciacion = new MinutaUsadoFinanciacion();
			$oMinutaUsadoFinanciacion->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsadoFinanciacion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}


	public function GetById($IdMinutaFinanciacion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsadosFinanciacion";
		$sql.= " WHERE IdMinutaFinanciacion = " . DB::Number($IdMinutaFinanciacion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaUsadoFinanciacion = new MinutaUsadoFinanciacion();
		$oMinutaUsadoFinanciacion->ParseFromArray($oRow);
		
		return $oMinutaUsadoFinanciacion;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT pa.*";
		$sql.= " FROM TB_MinutasUsadosFinanciacion pa";
		$sql.= " INNER JOIN TB_MinutasUsados m ON pa.IdMinuta = m.IdMinuta";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(MinutaUsadoFinanciacion $oMinutaFinanciacion)
	{
		$arr = array
		(
			'IdMinuta' 		=> DB::Number($oMinutaFinanciacion->IdMinuta),
			'IdAcreedor' 	=> DB::Number($oMinutaFinanciacion->IdAcreedor),
			'Importe' 		=> DB::Number($oMinutaFinanciacion->Importe),
			'Cuotas' 		=> DB::Number($oMinutaFinanciacion->Cuotas)
		);
		
		if (!$this->Insert('TB_MinutasUsadosFinanciacion', $arr))
			return false;

		/* asignamos el id generado */
		$oMinutaFinanciacion->IdMinutaFinanciacion = DBAccess::GetLastInsertId();
			
		return $oMinutaFinanciacion;
	}
	

	public function Update(MinutaUsadoFinanciacion $oMinutaFinanciacion)
	{
		$where = " IdMinutaFinanciacion = " . DB::Number($oMinutaFinanciacion->IdMinutaFinanciacion);
		
		$arr = array(
			'IdMinuta' 		=> DB::Number($oMinutaFinanciacion->IdMinuta),
			'IdAcreedor' 	=> DB::Number($oMinutaFinanciacion->IdAcreedor),
			'Importe' 		=> DB::Number($oMinutaFinanciacion->Importe),
			'Cuotas' 		=> DB::Number($oMinutaFinanciacion->Cuotas)
		);
		
		if (!DBAccess::Update('TB_MinutasUsadosFinanciacion', $arr, $where))
			return false;
		
		return $oSector;
	}

	
	public function Delete($IdMinutaFinanciacion)
	{
		if (!DBAccess::$db->Begin())
			return false;
		
		$where = " IdMinutaFinanciacion = " . DB::Number($IdMinutaFinanciacion);

		if (!DBAccess::Delete('TB_MinutasUsadosFinanciacion', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	

	
	public function DeleteByIdMinuta($IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
		
		$where = " IdMinuta = " . DB::Number($IdMinuta);

		if (!DBAccess::Delete('TB_MinutasUsadosFinanciacion', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>