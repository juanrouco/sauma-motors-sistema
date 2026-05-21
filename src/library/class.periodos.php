<?php 

require_once('class.dbaccess.php');
require_once('class.periodo.php');
require_once('class.filter.php');
require_once('class.page.php');


class Periodos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND FechaInicio <= " . DB::Date($filter['FechaDesde']);
		
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND FechaFin <= " . DB::Date($filter['FechaHasta']);

		if ((isset($filter['Cerrado'])) && ($filter['Cerrado'] != ''))
			$sql.= " AND Cerrado = " . DB::Number($filter['Cerrado']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Periodos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY FechaInicio";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPeriodo = new Periodo();
			$oPeriodo->ParseFromArray($oRow);
			
			array_push($arr, $oPeriodo);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetById($IdPeriodo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Periodos";
		$sql.= " WHERE IdPeriodo = " . DB::Number($IdPeriodo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPeriodo = new Periodo();
		$oPeriodo->ParseFromArray($oRow);
		
		return $oPeriodo;		
	}
	
	public function GetPeriodoCorrespondienteAbierto($Fecha)
	{
		$encontrado = true;
		$sql = "SELECT *";
		$sql.= " FROM TB_Periodos";
		$sql.= " WHERE FechaInicio <= " . DB::Date($Fecha);	
		$sql.= " AND FechaFin >= " . DB::Date($Fecha);	
		$sql.= " AND Cerrado = 0";	
			
		if (!($oRes = $this->GetQuery($sql)))
			$encontrado = false;
			
		if (!($oRow = $oRes->GetRow()))
			$encontrado = false;
		
		if (!$encontrado)
		{
			$sql = "SELECT *";
			$sql.= " FROM TB_Periodos";
			$sql.= " WHERE FechaFin >= " . DB::Date($Fecha);	
			$sql.= " AND Cerrado = 0";	
			$sql.= " ORDER BY FechaInicio ASC";	
			$sql.= " LIMIT 1";	
				
			if (!($oRes = $this->GetQuery($sql)))
				return false;
				
			if (!($oRow = $oRes->GetRow()))
				return false;
		}
		
		$oPeriodo = new Periodo();
		$oPeriodo->ParseFromArray($oRow);
		
		return $oPeriodo;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Periodos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(Periodo $oPeriodo)
	{
		$arr = array
		(
			'FechaInicio' 		=> DB::Date($oPeriodo->FechaInicio),
			'FechaFin' 			=> DB::Date($oPeriodo->FechaFin),
			'Cerrado' 			=> DB::Bool($oPeriodo->Cerrado),
			'Nombre' 			=> DB::String($oPeriodo->Nombre)
		);
		
		if (!$this->Insert('TB_Periodos', $arr))
			return false;

		/* asignamos el id generado */
		$oPeriodo->IdPeriodo = DBAccess::GetLastInsertId();
			
		return $oPeriodo;
	}
	
	
	public function Update(Periodo $oPeriodo)
	{
		$where = " IdPeriodo = " . DB::Number($oPeriodo->IdPeriodo);
		
		$arr = array
		(
			'FechaInicio' 		=> DB::Date($oPeriodo->FechaInicio),
			'FechaFin' 			=> DB::Date($oPeriodo->FechaFin),
			'Cerrado' 			=> DB::Bool($oPeriodo->Cerrado),
			'Nombre' 			=> DB::String($oPeriodo->Nombre)
		);
		
		if (!DBAccess::Update('TB_Periodos', $arr, $where))
			return false;
		
		return $oPeriodo;
	}
}

?>