<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.unidadarreglo.php');

class UnidadesArreglos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UnidadesArreglos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdUnidad DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadArreglo = new UnidadArreglo();
			$oUnidadArreglo->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadArreglo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByUnidad($oUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UnidadesArreglos";
		$sql.= " WHERE IdUnidad = " . DB::Number($oUnidad->IdUnidad);
	
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadArreglo = new UnidadArreglo();
			$oUnidadArreglo->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadArreglo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdUnidadArreglo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UnidadesArreglos";
		$sql.= " WHERE IdUnidadArreglo = " . DB::Number($IdUnidadArreglo);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUnidadArreglo = new UnidadArreglo();
		$oUnidadArreglo->ParseFromArray($oRow);
		
		return $oUnidadArreglo;		
	}
	

	public function Create(UnidadArreglo $oUnidadArreglo)
	{
		$arr = array
		(
			'IdUnidad'	=> DB::Number($oUnidadArreglo->IdUnidad),
			'Detalle' 	=> DB::String($oUnidadArreglo->Detalle),
			'Importe' 	=> DB::Number($oUnidadArreglo->Importe)
		);
	
		if (!$this->Insert('TB_UnidadesArreglos', $arr))
			return false;
			
		return $oUnidadArreglo;
	}
	

	public function Delete($IdUnidadArreglo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUnidadArreglo = " . (int)$IdUnidadArreglo;
		
		if ( !DBAccess::Delete('TB_UnidadesArreglos', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteByUnidad($IdUnidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUnidad = " . (int)$IdUnidad;
		
		if ( !DBAccess::Delete('TB_UnidadesArreglos', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>