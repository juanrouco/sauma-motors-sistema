<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.usados.php');
require_once('class.usadoarreglo.php');

class UsadosArreglos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UsadosArreglos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdUsado DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsadoArreglo = new UsadoArreglo();
			$oUsadoArreglo->ParseFromArray($oRow);
			
			array_push($arr, $oUsadoArreglo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByUsado($oUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UsadosArreglos";
		$sql.= " WHERE IdUsado = " . DB::Number($oUsado->IdUsado);
	
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsadoArreglo = new UsadoArreglo();
			$oUsadoArreglo->ParseFromArray($oRow);
			
			array_push($arr, $oUsadoArreglo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdUsadoArreglo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UsadosArreglos";
		$sql.= " WHERE IdUsadoArreglo = " . DB::Number($IdUsadoArreglo);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUsadoArreglo = new UsadoArreglo();
		$oUsadoArreglo->ParseFromArray($oRow);
		
		return $oUsadoArreglo;		
	}
	

	public function Create(UsadoArreglo $oUsadoArreglo)
	{
		$arr = array
		(
			'IdUsado'	=> DB::Number($oUsadoArreglo->IdUsado),
			'Detalle' 	=> DB::String($oUsadoArreglo->Detalle),
			'Importe' 	=> DB::Number($oUsadoArreglo->Importe)
		);
	
		if (!$this->Insert('TB_UsadosArreglos', $arr))
			return false;
			
		return $oUsadoArreglo;
	}
	

	public function Delete($IdUsadoArreglo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsadoArreglo = " . (int)$IdUsadoArreglo;
		
		if ( !DBAccess::Delete('TB_UsadosArreglos', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteByUsado($IdUsado)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsado = " . (int)$IdUsado;
		
		if ( !DBAccess::Delete('TB_UsadosArreglos', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>