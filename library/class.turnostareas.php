<?php 

require_once('class.dbaccess.php');
require_once('class.turnotarea.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class TurnosTareas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['PalabraClave'])) && ($filter['PalabraClave'] != ''))
		{
			$sql.= " AND (tt.Titulo LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%'";
			$sql.= " OR tt.Descripcion LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%')";
		}
		
		if ((isset($filter['IdTurno'])) && ($filter['IdTurno'] != ''))
		{
			$sql.= " AND tt.IdTurno = " . DB::Number($filter['IdTurno']);
		}

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT tt.*";
		$sql.= " FROM TB_TurnosTareas tt";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY tt.IdTurnoTarea DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoTarea = new TurnoTarea();
			$oTurnoTarea->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByTurno(Turno $oTurno)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosTareas";
		$sql.= " WHERE IdTurno = " . DB::Number($oTurno->IdTurno);
		$sql.= " ORDER BY IdTurnoTarea ASC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurnoTarea = new TurnoTarea();
			$oTurnoTarea->ParseFromArray($oRow);
			
			array_push($arr, $oTurnoTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdTurno, $IdTareaTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosTareas";
		$sql.= " WHERE IdTurno = " . DB::Number($IdTurno);	
		$sql.= " AND IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurnoTarea = new TurnoTarea();
		$oTurnoTarea->ParseFromArray($oRow);
		
		return $oTurnoTarea;		
	}
	
	public function GetByIdIncrement($IdTurnoTarea)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosTareas";
		$sql.= " WHERE IdTurnoTarea = " . DB::Number($IdTurnoTarea);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurnoTarea = new TurnoTarea();
		$oTurnoTarea->ParseFromArray($oRow);
		
		return $oTurnoTarea;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TurnosTareas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(TurnoTarea $oTurnoTarea)
	{
		$arr = array
		(
			'IdTurno'				=> DB::Number($oTurnoTarea->IdTurno),
			'Importe' 				=> DB::Number($oTurnoTarea->Importe),
			'Titulo' 				=> DB::String($oTurnoTarea->Titulo),
			'Descripcion' 			=> DB::String($oTurnoTarea->Descripcion),
			'HorasEstimadas' 		=> DB::Number($oTurnoTarea->HorasEstimadas),
			'IdTareaTrabajo'		=> DB::Number($oTurnoTarea->IdTareaTrabajo),
			'IdTipoVenta'			=> DB::Number($oTurnoTarea->IdTipoVenta),
			'IdEstado'				=> DB::Number($oTurnoTarea->IdEstado),
			'IdCodigoTrabajo'		=> DB::Number($oTurnoTarea->IdCodigoTrabajo),
			'IdFacturaCompra'		=> DB::Number($oTurnoTarea->IdFacturaCompra)
		);
		return $arr;
	}
	
	public function Create(TurnoTarea $oTurnoTarea)
	{
		$oTurnoTarea->IdEstado = TurnoTarea::IdEstadoActivo;
		$arr = $this->GetArrayDB($oTurnoTarea);
		
		if (!$this->Insert('TB_TurnosTareas', $arr))
			return false;

		/* asignamos el id generado */
		$oTurnoTarea->IdTurnoTarea = DBAccess::GetLastInsertId();
			
		return $oTurnoTarea;
	}
	
	
	public function Update(TurnoTarea $oTurnoTarea)
	{
		$where = " IdTurnoTarea = " . DB::Number($oTurnoTarea->IdTurnoTarea);
		
		$arr = $this->GetArrayDB($oTurnoTarea);
		
		if (!DBAccess::Update('TB_TurnosTareas', $arr, $where))
			return false;
		
		return $oTurnoTarea;
	}

	public function Delete($IdTurno, $IdTareaTrabajo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTurno = " . DB::Number($IdTurno);
		$where .= " AND IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);

		if (!DBAccess::Delete('TB_TurnosTareas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteIncrement($IdTurnoTarea)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTurnoTarea = " . DB::Number($IdTurnoTarea);

		if (!DBAccess::Delete('TB_TurnosTareas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>