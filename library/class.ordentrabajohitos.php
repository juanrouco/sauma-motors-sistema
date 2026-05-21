<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajohito.php');
require_once('class.filter.php');
require_once('class.page.php');

class OrdenTrabajoHitos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if (isset($filter['IdOrdenTrabajo']) && $filter['IdOrdenTrabajo'] != '')
			$sql.= " AND IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= " AND IdUsuario = " . DB::Number($filter['IdUsuario']);
		if (isset($filter['TipoHito']) && $filter['TipoHito'] != '')
			$sql.= " AND TipoHito = " . DB::Number($filter['TipoHito']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_OrdenTrabajoHitos";
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
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE 1=1";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY FechaHora";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetById($IdOrdenTrabajoHito)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE IdOrdenTrabajoHito = " . DB::Number($IdOrdenTrabajoHito);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoHito = new OrdenTrabajoHito();
		$oOrdenTrabajoHito->ParseFromArray($oRow);
		
		return $oOrdenTrabajoHito;		
	}

	public function ActualizarEstados()
	{
		$sql = "UPDATE TB_OrdenTrabajoHitos";
		$sql.= " SET TipoHito = " . DB::Number(OrdenTrabajoHito::FinalizarSistema);	
		$sql.= " WHERE TipoHito = " . DB::Number(OrdenTrabajoHito::Iniciar);	
		$sql.= " AND FechaHora < " . DB::Date(date('d-m-Y'));	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		return true;		
	}

	public function GetByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT oth.*";
		$sql.= " FROM TB_OrdenTrabajoHitos oth";
		$sql.= " INNER JOIN TB_Usuarios u ON oth.IdUsuario = u.IdUsuario";
		$sql.= " WHERE oth.IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " ORDER BY u.Apellido, u.Nombre, oth.FechaHora";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetByIdOrdenTrabajoAndIdUsuario($IdOrdenTrabajo, $IdUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " AND IdUsuario = " . DB::Number($IdUsuario);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdUsuarioAndTipoHitoAndNotIdOrdenTrabajo($IdUsuario, $TipoHito, $IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE TipoHito = " . DB::Number($TipoHito);
		$sql.= " AND IdUsuario = " . DB::Number($IdUsuario);
		$sql.= " AND IdOrdenTrabajo <> " . DB::Number($IdOrdenTrabajo);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdUsuarioAndTipoHitoAndIdOrdenTrabajo($IdUsuario, $TipoHito, $IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE TipoHito = " . DB::Number($TipoHito);
		$sql.= " AND IdUsuario = " . DB::Number($IdUsuario);
		$sql.= " AND IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetLastByIdOrdenTrabajoAndIdUsuario($IdOrdenTrabajo, $IdOrdenTrabajoTarea, $IdUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " AND IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);
		$sql.= " AND IdUsuario = " . DB::Number($IdUsuario);
		$sql.= " ORDER BY IdOrdenTrabajoHito DESC";
		$sql.= " LIMIT 1";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoHito = new OrdenTrabajoHito();
		$oOrdenTrabajoHito->ParseFromArray($oRow);
			
		return $oOrdenTrabajoHito;
	}
	
	public function GetLastByIdOrdenTrabajo($IdOrdenTrabajo, $IdOrdenTrabajoTarea)
	{
		$sql = "SELECT *";
		$sql.= " FROM tb_OrdenTrabajoHitos";
		$sql.= " WHERE IdOrdenTrabajoHito IN (";
		$sql.= " SELECT MAX(IdOrdenTrabajoHito)";
		$sql.= " FROM tb_OrdenTrabajoHitos";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " AND IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);
		$sql.= " GROUP BY IdUsuario";
		$sql.= ")";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenTrabajoHitos";
		$sql.= " WHERE 1=1";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY FechaHora";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	
	
	private function ActualizarTiempo(OrdenTrabajoHito $oOrdenTrabajoHito)
	{
		$sql = 'UPDATE tb_OrdenTrabajoHitos';
		$sql.= ' SET Tiempo = TIMEDIFF(FechaHoraFin, FechaHora)';
		$sql.= ' WHERE IdOrdenTrabajoHito = ' . DB::Number($oOrdenTrabajoHito->IdOrdenTrabajoHito);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		return true;
	}
	
	private function GetArrayDB(OrdenTrabajoHito $oOrdenTrabajoHito)
	{
		$arr = array
		(
			'IdOrdenTrabajo' 		=> DB::Number($oOrdenTrabajoHito->IdOrdenTrabajo),
			'IdOrdenTrabajoTarea' 	=> DB::Number($oOrdenTrabajoHito->IdOrdenTrabajoTarea),
			'IdUsuario' 			=> DB::Number($oOrdenTrabajoHito->IdUsuario),
			'FechaHora' 			=> DB::Date($oOrdenTrabajoHito->FechaHora),
			'TipoHito' 				=> DB::Number($oOrdenTrabajoHito->TipoHito),
			'FechaHoraFin' 			=> DB::Date($oOrdenTrabajoHito->FechaHoraFin)
		);
		
		return $arr;
	}
	
	
	public function Create(OrdenTrabajoHito $oOrdenTrabajoHito)
	{
		$arr = $this->GetArrayDB($oOrdenTrabajoHito);
		
		if (!$this->Insert('TB_OrdenTrabajoHitos', $arr))
			return false;
		$oOrdenTrabajoHito->IdOrdenTrabajoHito = DBAccess::GetLastInsertId();
		
		$this->ActualizarTiempo($oOrdenTrabajoHito);
		
		return $oOrdenTrabajoHito;
	}
	
	public function Update(OrdenTrabajoHito $oOrdenTrabajoHito)
	{
		$where = " IdOrdenTrabajoHito = " . DB::Number($oOrdenTrabajoHito->IdOrdenTrabajoHito);
		
		$arr = $this->GetArrayDB($oOrdenTrabajoHito);
		
		if (!DBAccess::Update('TB_OrdenTrabajoHitos', $arr, $where))
			return false;
		
		$this->ActualizarTiempo($oOrdenTrabajoHito);
		
		return $oOrdenTrabajoHito;
	}
	
	public function Delete($IdOrdenTrabajoHito)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajoHito = " . DB::Number($IdOrdenTrabajoHito);
		if (!DBAccess::Delete('TB_OrdenTrabajoHitos', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>