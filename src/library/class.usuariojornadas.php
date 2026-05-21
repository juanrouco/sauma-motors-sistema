<?php 

require_once('class.dbaccess.php');
require_once('class.usuariojornada.php');
require_once('class.filter.php');
require_once('class.page.php');

class UsuarioJornadas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= " AND uj.IdUsuario = " . DB::Number($filter['IdUsuario']);
		
		if (isset($filter['DiaSemana']) && $filter['DiaSemana'] != '')
			$sql.= " AND uj.DiaSemana = " . DB::Number($filter['DiaSemana']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE 1=1";
		
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
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE 1=1";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY uj.IdUsuario, uj.DiaSemana";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuarioJornada = new UsuarioJornada();
			$oUsuarioJornada->ParseFromArray($oRow);
			
			array_push($arr, $oUsuarioJornada);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdUsuarioJornada)
	{
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE uj.IdUsuarioJornada = " . DB::Number($IdUsuarioJornada);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUsuarioJornada = new UsuarioJornada();
		$oUsuarioJornada->ParseFromArray($oRow);
		
		return $oUsuarioJornada;		
	}
	
	public function GetByIdUsuario($IdUsuario)
	{
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE uj.IdUsuario = " . DB::Number($IdUsuario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuarioJornada = new UsuarioJornada();
			$oUsuarioJornada->ParseFromArray($oRow);
			
			array_push($arr, $oUsuarioJornada);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByDiaSemana($DiaSemana)
	{
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE uj.DiaSemana = " . DB::Number($DiaSemana);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuarioJornada = new UsuarioJornada();
			$oUsuarioJornada->ParseFromArray($oRow);
			
			array_push($arr, $oUsuarioJornada);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetByIdUsuarioAndDiaSemana($IdUsuario, $DiaSemana)
	{
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE uj.DiaSemana = " . DB::Number($DiaSemana);
		$sql.= " AND uj.IdUsuario = " . DB::Number($IdUsuario);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		if (!$oRow = $oRes->GetRow())	
			return false;

		$oUsuarioJornada = new UsuarioJornada();
		$oUsuarioJornada->ParseFromArray($oRow);
		
		return $oUsuarioJornada;
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT uj.*";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE 1=1";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY uj.IdUsuario, uj.DiaSemana";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetHorasPorSemana()
	{	
		$sql = "SELECT SUM(TIME_TO_SEC(TIMEDIFF(TIMEDIFF(uj.HoraFin, uj.HoraInicio),TIMEDIFF(uj.HoraAlmuerzoFin, uj.HoraAlmuerzoInicio))) / 3600) AS HourCount";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE 1=1";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['HourCount'];
		
		return $Count;
	}
	
	public function GetHorasPorDia($DiaSemana)
	{	
		$sql = "SELECT SUM(TIME_TO_SEC(TIMEDIFF(TIMEDIFF(uj.HoraFin, uj.HoraInicio),TIMEDIFF(uj.HoraAlmuerzoFin, uj.HoraAlmuerzoInicio))) / 3600) AS HourCount";
		$sql.= " FROM TB_UsuarioJornadas uj";
		$sql.= " WHERE uj.DiaSemana = " . DB::Number($DiaSemana);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['HourCount'];
		
		return $Count;
	}
	
	public function GetHorasEntreFechas($FechaInicio, $FechaFin)
	{
		$FechaInicio	 		= strtotime(DB::parseDate($FechaInicio, '%d-%m-%Y'));
		$FechaFin 				= strtotime(DB::parseDate($FechaFin, '%d-%m-%Y'));
		
		$CantidadDias 			= abs($FechaFin - $FechaInicio) / 86400;
		$CantidadSemanas		= floor($CantidadDias / 7);
		$HorasSemanales 		= $this->GetHorasPorSemana();
		
		$HorasAcumuladas 		= $HorasSemanales * $CantidadSemanas;

		$CantidadDiasSueltos	= $CantidadDias % 7;
		$DiaSemana = date('N', $FechaInicio);
		for($count = 0; $count < $CantidadDiasSueltos; $count++)
		{
			$DiaSemana += $count;
			$countDia = $this->GetHorasPorDia($DiaSemana);
			$HorasAcumuladas += $countDia;
		}
		
		return $HorasAcumuladas;
	}
	
	private function GetArrayDB(UsuarioJornada $oUsuarioJornada)
	{
		$arr = array
		(
			'IdUsuario' 			=> DB::Number($oUsuarioJornada->IdUsuario),
			'DiaSemana' 			=> DB::Number($oUsuarioJornada->DiaSemana),
			'HoraInicio' 			=> DB::String($oUsuarioJornada->HoraInicio),
			'HoraFin' 				=> DB::String($oUsuarioJornada->HoraFin),
			'HoraAlmuerzoInicio' 	=> DB::String($oUsuarioJornada->HoraAlmuerzoInicio),
			'HoraAlmuerzoFin' 		=> DB::String($oUsuarioJornada->HoraAlmuerzoFin)
		);
		
		return $arr;
	}
	
	public function Create(UsuarioJornada $oUsuarioJornada)
	{
		$arr = $this->GetArrayDB($oUsuarioJornada);
		
		if (!$this->Insert('TB_UsuarioJornadas', $arr))
			return false;
		$oUsuarioJornada->IdUsuarioJornada = DBAccess::GetLastInsertId();
			
		return $oUsuarioJornada;
	}
	
	
	public function Update(UsuarioJornada $oUsuarioJornada)
	{
		$where = " IdUsuarioJornada = " . DB::Number($oUsuarioJornada->IdUsuarioJornada);
		
		$arr = $this->GetArrayDB($oUsuarioJornada);
		
		if (!DBAccess::Update('TB_UsuarioJornadas', $arr, $where))
			return false;
		
		return $oUsuarioJornada;
	}

	public function Delete($IdUsuarioJornada)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsuarioJornada = " . DB::Number($IdUsuarioJornada);
		if (!DBAccess::Delete('TB_UsuarioJornadas', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteByIdUsuario($IdUsuario)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsuario = " . DB::Number($IdUsuario);
		if (!DBAccess::Delete('TB_UsuarioJornadas', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>