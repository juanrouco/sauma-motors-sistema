<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.ordentrabajotareaarticulo.php');
require_once('class.filter.php');
require_once('class.page.php');

class OrdenesTrabajoTareasArticulos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_OrdenesTrabajoTareasArticulos tta";
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
		$sql.= " FROM TB_OrdenesTrabajoTareasArticulos tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tta.IdOrdenTrabajoTarea";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoTareaArticulo = new OrdenTrabajoTareaArticulo();
			$oOrdenTrabajoTareaArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoTareaArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByOrdenTrabajoTarea(OrdenTrabajoTarea $oOrdenTrabajoTarea)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_OrdenesTrabajoTareasArticulos tta";
		$sql.= " WHERE tta.IdOrdenTrabajoTarea = " . DB::Number($oOrdenTrabajoTarea->IdOrdenTrabajoTarea);
		$sql.= " GROUP BY tta.IdArticulo";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoTareaArticulo = new OrdenTrabajoTareaArticulo();
			$oOrdenTrabajoTareaArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoTareaArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdOrdenTrabajoTarea, $IdArticulo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_OrdenesTrabajoTareasArticulos tta";
		$sql.= " WHERE tta.IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);	
		$sql.= " AND tta.IdArticulo = " . DB::Number($IdArticulo);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oOrdenTrabajoTareaArticulo = new OrdenTrabajoTareaArticulo();
		$oOrdenTrabajoTareaArticulo->ParseFromArray($oRow);

		return $oOrdenTrabajoTareaArticulo;		
	}


	public function Create(OrdenTrabajoTareaArticulo $oOrdenTrabajoTareaArticulo)
	{
		$arr = array
		(
			'IdOrdenTrabajoTarea'	=> DB::Number($oOrdenTrabajoTareaArticulo->IdOrdenTrabajoTarea),
			'IdArticulo'			=> DB::Number($oOrdenTrabajoTareaArticulo->IdArticulo),
			'Cantidad'				=> DB::Number($oOrdenTrabajoTareaArticulo->Cantidad),
			'PrecioTotal'			=> DB::Number($oOrdenTrabajoTareaArticulo->PrecioTotal)
		);

		if (!$this->Insert('TB_OrdenesTrabajoTareasArticulos', $arr))
			return false;
			
		return $oOrdenTrabajoTareaArticulo;
	}
	
	public function Update(OrdenTrabajoTareaArticulo $oOrdenTrabajoTareaArticulo)
	{
		$where = " IdOrdenTrabajoTarea = " . DB::Number($oOrdenTrabajoTareaArticulo->IdOrdenTrabajoTarea);
		$where.= " AND IdArticulo = " . DB::Number($oOrdenTrabajoTareaArticulo->IdArticulo);
		
		$arr = array
		(
			'Cantidad'				=> DB::Number($oOrdenTrabajoTareaArticulo->Cantidad),
			'PrecioTotal'			=> DB::Number($oOrdenTrabajoTareaArticulo->PrecioTotal)
		);

		if (!DBAccess::Update('TB_OrdenesTrabajoTareasArticulos', $arr, $where))
			return false;
			
		return $oOrdenTrabajoTareaArticulo;
	}
	
		
	public function Delete($IdOrdenTrabajoTarea, $IdArticulo)
	{
		$where = " IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);
		$where.= " AND IdArticulo = " . DB::Number($IdArticulo);
		
		if (!DBAccess::Delete('TB_OrdenesTrabajoTareasArticulos', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByOrdenTrabajoTarea(OrdenTrabajoTarea $oOrdenTrabajoTarea)
	{
		$where = " IdOrdenTrabajoTarea = ".DB::Number($oOrdenTrabajoTareaArticulo->IdOrdenTrabajoTarea);
		
		if (!DBAccess::Delete('TB_OrdenesTrabajoTareasArticulos', $where))
			return false;
		
		return true;	
	}
}

?>