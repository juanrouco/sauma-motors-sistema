<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.tareatrabajoarticulo.php');
require_once('class.filter.php');
require_once('class.page.php');

class TareasTrabajoArticulos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoArticulos tta";
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
		$sql.= " FROM TB_TareasTrabajoArticulos tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tta.IdTareaTrabajo";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajoArticulo = new TareaTrabajoArticulo();
			$oTareaTrabajoArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajoArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByTareaTrabajo(TareaTrabajo $oTareaTrabajo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoArticulos tta";
		$sql.= " WHERE tta.IdTareaTrabajo = " . DB::Number($oTareaTrabajo->IdTareaTrabajo);
		$sql.= " GROUP BY tta.IdArticulo";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajoArticulo = new TareaTrabajoArticulo();
			$oTareaTrabajoArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajoArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdTareaTrabajo, $IdArticulo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoArticulos tta";
		$sql.= " WHERE tta.IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);	
		$sql.= " AND tta.IdArticulo = " . DB::Number($IdArticulo);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oTareaTrabajoArticulo = new TareaTrabajoArticulo();
		$oTareaTrabajoArticulo->ParseFromArray($oRow);

		return $oTareaTrabajoArticulo;		
	}


	public function Create(TareaTrabajoArticulo $oTareaTrabajoArticulo)
	{
		$arr = array
		(
			'IdTareaTrabajo'	=> DB::Number($oTareaTrabajoArticulo->IdTareaTrabajo),
			'IdArticulo'		=> DB::Number($oTareaTrabajoArticulo->IdArticulo),
			'Cantidad'		=> DB::Number($oTareaTrabajoArticulo->Cantidad)
		);

		if (!$this->Insert('TB_TareasTrabajoArticulos', $arr))
			return false;
			
		return $oTareaTrabajoArticulo;
	}
	
	public function Update(TareaTrabajoArticulo $oTareaTrabajoArticulo)
	{
		$where = " IdTareaTrabajo = " . DB::Number($oTareaTrabajoArticulo->IdTareaTrabajo);
		$where.= " AND IdArticulo = " . DB::Number($oTareaTrabajoArticulo->IdArticulo);
		
		$arr = array
		(
			'Cantidad'		=> DB::Number($oTareaTrabajoArticulo->Cantidad)
		);

		if (!DBAccess::Update('TB_TareasTrabajoArticulos', $arr, $where))
			return false;
			
		return $oTareaTrabajoArticulo;
	}
	
		
	public function Delete($IdTareaTrabajo, $IdArticulo)
	{
		$where = " IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);
		$where.= " AND IdArticulo = " . DB::Number($IdArticulo);
		
		if (!DBAccess::Delete('TB_TareasTrabajoArticulos', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByPerfil(TareaTrabajoArticulo $oTareaTrabajoArticulo)
	{
		$where = " IdTareaTrabajo = ".DB::Number($oTareaTrabajoArticulo->IdTareaTrabajo);
		
		if (!DBAccess::Delete('TB_TareasTrabajoArticulos', $where))
			return false;
		
		return true;	
	}
}

?>