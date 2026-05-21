<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.tareatrabajocodigotrabajo.php');
require_once('class.filter.php');
require_once('class.page.php');

class TareasTrabajoCodigosTrabajo extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoCodigosTrabajo tta";
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
		$sql.= " FROM TB_TareasTrabajoCodigosTrabajo tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tta.IdTareaTrabajo";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajoCodigoTrabajo = new TareaTrabajoCodigoTrabajo();
			$oTareaTrabajoCodigoTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajoCodigoTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByTareaTrabajo(TareaTrabajo $oTareaTrabajo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoCodigosTrabajo tta";
		$sql.= " WHERE tta.IdTareaTrabajo = " . DB::Number($oTareaTrabajo->IdTareaTrabajo);
		$sql.= " GROUP BY tta.IdCodigoTrabajo";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajoCodigoTrabajo = new TareaTrabajoCodigoTrabajo();
			$oTareaTrabajoCodigoTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajoCodigoTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdTareaTrabajo, $IdCodigoTrabajo)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_TareasTrabajoCodigosTrabajo tta";
		$sql.= " WHERE tta.IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);	
		$sql.= " AND tta.IdCodigoTrabajo = " . DB::Number($IdCodigoTrabajo);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oTareaTrabajoCodigoTrabajo = new TareaTrabajoCodigoTrabajo();
		$oTareaTrabajoCodigoTrabajo->ParseFromArray($oRow);

		return $oTareaTrabajoCodigoTrabajo;		
	}


	public function Create(TareaTrabajoCodigoTrabajo $oTareaTrabajoCodigoTrabajo)
	{
		$arr = array
		(
			'IdTareaTrabajo'	=> DB::Number($oTareaTrabajoCodigoTrabajo->IdTareaTrabajo),
			'IdCodigoTrabajo'		=> DB::Number($oTareaTrabajoCodigoTrabajo->IdCodigoTrabajo)
		);

		if (!$this->Insert('TB_TareasTrabajoCodigosTrabajo', $arr))
			return false;
			
		return $oTareaTrabajoCodigoTrabajo;
	}
	
	public function Update(TareaTrabajoCodigoTrabajo $oTareaTrabajoCodigoTrabajo)
	{
		$where = " IdTareaTrabajo = " . DB::Number($oTareaTrabajoCodigoTrabajo->IdTareaTrabajo);
		$where.= " AND IdCodigoTrabajo = " . DB::Number($oTareaTrabajoCodigoTrabajo->IdCodigoTrabajo);
		

		if (!DBAccess::Update('TB_TareasTrabajoCodigosTrabajo', $arr, $where))
			return false;
			
		return $oTareaTrabajoCodigoTrabajo;
	}
	
		
	public function Delete($IdTareaTrabajo, $IdCodigoTrabajo)
	{
		$where = " IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);
		$where.= " AND IdCodigoTrabajo = " . DB::Number($IdCodigoTrabajo);
		
		if (!DBAccess::Delete('TB_TareasTrabajoCodigosTrabajo', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByPerfil(TareaTrabajoCodigoTrabajo $oTareaTrabajoCodigoTrabajo)
	{
		$where = " IdTareaTrabajo = ".DB::Number($oTareaTrabajoCodigoTrabajo->IdTareaTrabajo);
		
		if (!DBAccess::Delete('TB_TareasTrabajoCodigosTrabajo', $where))
			return false;
		
		return true;	
	}
}

?>