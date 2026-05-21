<?php 

require_once('class.dbaccess.php');
require_once('class.turnocomentario.php');
require_once('class.filter.php');
require_once('class.page.php');

class TurnosComentarios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE IdTurno = " . DB::Number($filter['IdTurno']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_TurnosComentarios";
		
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
		$sql.= " FROM TB_TurnosComentarios";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdTurnoComentario";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoComentario = new TurnoComentario();
			$oTurnoComentario->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoComentario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdTurnoComentario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosComentarios";
		$sql.= " WHERE IdTurnoComentario = " . DB::Number($IdTurnoComentario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurnoComentario = new TurnoComentario();
		$oTurnoComentario->ParseFromArray($oRow);
		
		return $oTurnoComentario;		
	}
	


	public function GetByIdTurno($IdTurno)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosComentarios";
		$sql.= " WHERE IdTurno = " . DB::Number($IdTurno);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoComentario = new TurnoComentario();
			$oTurnoComentario->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoComentario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetRechazoByIdTurno($IdTurno)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosComentarios";
		$sql.= " WHERE IdTurno = " . DB::Number($IdTurno);	
		$sql.= " AND IdTipoRechazo IS NOT NULL";	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurnoComentario = new TurnoComentario();
		$oTurnoComentario->ParseFromArray($oRow);
		
		return $oTurnoComentario;
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosComentarios";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdTurno";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TurnoComentario $oTurnoComentario)
	{
		$arr = array
		(
			'IdTurno' 			=> DB::String($oTurnoComentario->IdTurno),
			'Comentarios' 		=> DB::String($oTurnoComentario->Comentarios),
			'IdUsuario'			=> DB::Number($oTurnoComentario->IdUsuario),
			'IdTipoRechazo'		=> DB::Number($oTurnoComentario->IdTipoRechazo)
		);
		
		if (!$this->Insert('TB_TurnosComentarios', $arr))
			return false;
		$oTurnoComentario->IdTurnoComentario = DBAccess::GetLastInsertId();
			
		return $oTurnoComentario;
	}
	
	
	public function Update(TurnoComentario $oTurnoComentario)
	{
		$where = " IdTurnoComentario = " . DB::Number($oRubro->IdTurnoComentario);
		
		$arr = array
		(
			'IdTurno' 			=> DB::String($oTurnoComentario->IdTurno),
			'Comentarios' 		=> DB::String($oTurnoComentario->Comentarios),
			'IdUsuario'			=> DB::Number($oTurnoComentario->IdUsuario),
			'IdTipoRechazo'		=> DB::Number($oTurnoComentario->IdTipoRechazo)
		);
		
		if (!DBAccess::Update('TB_TurnosComentarios', $arr, $where))
			return false;
		
		return $oTurnoComentario;
	}
	

	public function Delete($IdTurnoComentario)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTurnoComentario = " . DB::Number($IdTurnoComentario);
		if (!DBAccess::Delete('TB_TurnosComentarios', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>