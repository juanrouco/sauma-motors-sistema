<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajocomentario.php');
require_once('class.filter.php');
require_once('class.page.php');

class OrdenTrabajoComentarios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_OrdenTrabajoComentarios";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['Count'];
		
		return ceil($Count);
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoComentarios";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdOrdenTrabajoComentario";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoComentario = new OrdenTrabajoComentario();
			$oOrdenTrabajoComentario->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoComentario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdOrdenTrabajoComentario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoComentarios";
		$sql.= " WHERE IdOrdenTrabajoComentario = " . DB::Number($IdOrdenTrabajoComentario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoComentario = new OrdenTrabajoComentario();
		$oOrdenTrabajoComentario->ParseFromArray($oRow);
		
		return $oOrdenTrabajoComentario;		
	}
	


	public function GetByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoComentarios";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoComentario = new OrdenTrabajoComentario();
			$oOrdenTrabajoComentario->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoComentario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoComentarios";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdOrdenTrabajo";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(OrdenTrabajoComentario $oOrdenTrabajoComentario)
	{
		$arr = array
		(
			'IdOrdenTrabajo' 	=> DB::String($oOrdenTrabajoComentario->IdOrdenTrabajo),
			'Comentarios' 		=> DB::String($oOrdenTrabajoComentario->Comentarios),
			'IdUsuario'			=> DB::Number($oOrdenTrabajoComentario->IdUsuario),
			'IdTipoRechazo'		=> DB::Number($oOrdenTrabajoComentario->IdTipoRechazo)
		);
		
		if (!$this->Insert('TB_OrdenTrabajoComentarios', $arr))
			return false;
		$oOrdenTrabajoComentario->IdOrdenTrabajoComentario = DBAccess::GetLastInsertId();
			
		return $oOrdenTrabajoComentario;
	}
	
	
	public function Update(OrdenTrabajoComentario $oOrdenTrabajoComentario)
	{
		$where = " IdOrdenTrabajoComentario = " . DB::Number($oRubro->IdOrdenTrabajoComentario);
		
		$arr = array
		(
			'IdOrdenTrabajo' 	=> DB::String($oOrdenTrabajoComentario->IdOrdenTrabajo),
			'Comentarios' 		=> DB::String($oOrdenTrabajoComentario->Comentarios),
			'IdUsuario'			=> DB::Number($oOrdenTrabajoComentario->IdUsuario),
			'IdTipoRechazo'		=> DB::Number($oOrdenTrabajoComentario->IdTipoRechazo)
		);
		
		if (!DBAccess::Update('TB_OrdenTrabajoComentarios', $arr, $where))
			return false;
		
		return $oOrdenTrabajoComentario;
	}
	

	public function Delete($IdOrdenTrabajoComentario)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajoComentario = " . DB::Number($IdOrdenTrabajoComentario);
		if (!DBAccess::Delete('TB_OrdenTrabajoComentarios', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>