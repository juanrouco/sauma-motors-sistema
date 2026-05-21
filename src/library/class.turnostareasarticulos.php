<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.turnotareaarticulo.php');
require_once('class.filter.php');
require_once('class.page.php');

class TurnosTareasArticulos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_TurnosTareasArticulos tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_TurnosTareasArticulos tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tta.IdTurnoTarea";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoTareaArticulo = new TurnoTareaArticulo();
			$oTurnoTareaArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoTareaArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByTurnoTarea(TurnoTarea $oTurnoTarea)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TurnosTareasArticulos tta";
		$sql.= " WHERE tta.IdTurnoTarea = " . DB::Number($oTurnoTarea->IdTurnoTarea);
		$sql.= " GROUP BY tta.IdArticulo";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoTareaArticulo = new TurnoTareaArticulo();
			$oTurnoTareaArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoTareaArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdTurnoTarea, $IdArticulo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TurnosTareasArticulos tta";
		$sql.= " WHERE tta.IdTurnoTarea = " . DB::Number($IdTurnoTarea);	
		$sql.= " AND tta.IdArticulo = " . DB::Number($IdArticulo);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oTurnoTareaArticulo = new TurnoTareaArticulo();
		$oTurnoTareaArticulo->ParseFromArray($oRow);

		return $oTurnoTareaArticulo;		
	}


	public function Create(TurnoTareaArticulo $oTurnoTareaArticulo)
	{
		$arr = array
		(
			'IdTurnoTarea'	=> DB::Number($oTurnoTareaArticulo->IdTurnoTarea),
			'IdArticulo'	=> DB::Number($oTurnoTareaArticulo->IdArticulo),
			'Cantidad'		=> DB::Number($oTurnoTareaArticulo->Cantidad),
			'PrecioTotal'	=> DB::Number($oTurnoTareaArticulo->PrecioTotal)
		);

		if (!$this->Insert('TB_TurnosTareasArticulos', $arr))
			return false;
			
		return $oTurnoTareaArticulo;
	}
	
	public function Update(TurnoTareaArticulo $oTurnoTareaArticulo)
	{
		$where = " IdTurnoTarea = " . DB::Number($oTurnoTareaArticulo->IdTurnoTarea);
		$where.= " AND IdArticulo = " . DB::Number($oTurnoTareaArticulo->IdArticulo);
		
		$arr = array
		(	
			'Cantidad'		=> DB::Number($oTurnoTareaArticulo->Cantidad),
			'PrecioTotal'	=> DB::Number($oTurnoTareaArticulo->PrecioTotal)
		);

		if (!DBAccess::Update('TB_TurnosTareasArticulos', $arr, $where))
			return false;
			
		return $oTurnoTareaArticulo;
	}
	
		
	public function Delete($IdTurnoTarea, $IdArticulo)
	{
		$where = " IdTurnoTarea = " . DB::Number($IdTurnoTarea);
		$where.= " AND IdArticulo = " . DB::Number($IdArticulo);
		
		if (!DBAccess::Delete('TB_TurnosTareasArticulos', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByTurnoTarea(TurnoTarea $oTurnoTarea)
	{
		$where = " IdTurnoTarea = ".DB::Number($oTurnoTarea->IdTurnoTarea);
		
		if (!DBAccess::Delete('TB_TurnosTareasArticulos', $where))
			return false;
		
		return true;	
	}
}

?>