<?php 

require_once('class.dbaccess.php');
require_once('class.cajagestoria.php');
require_once('class.tiposmovimientoscaja.php');
require_once('class.filter.php');
require_once('class.page.php');

class CajasGestoria extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '' && $filter['FechaDesde'])
			$sql.= ' AND mpi.Fecha <= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '' && $filter['FechaHasta'])
			$sql.= ' AND mpi.Fecha >= ' . DB::Date($filter['FechaHasta']);
			
		if (isset($filter['IdTipo']) && $filter['IdTipo'] != '')
			$sql.= ' AND mpi.IdTipo = ' . DB::Number($filter['IdTipo']);
			
		if (isset($filter['IdMinuta']) && $filter['IdMinuta'] != '')
			$sql.= ' AND mpi.IdEntidad IN (SELECT IdCuentaGestoria FROM TB_CuentasGestoria WHERE IdMinuta = ' . DB::Number($filter['IdMinuta']) . ')';
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasGestoria mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Fecha DESC, mpi.IdCajaGestoria DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaGestoria = new CajaGestoria();
			$oCajaGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oCajaGestoria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function ActualizarDisponibles($Fecha)
	{
		$sql = "SET @csum := 0;";
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		$sql = " UPDATE TB_CajasGestoria";
		$sql.= " SET Disponible = (@csum := @csum + Monto)";
		//$sql.= " WHERE Fecha >= SUBDATE(" . DB::Date($Fecha) . ", 1)";
		$sql.= " ORDER BY Fecha, IdCajaGestoria;";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		return true;
	}
	
	public function GetById($IdCajaGestoria)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasGestoria";
		$sql.= " WHERE IdCajaGestoria = " . DB::Number($IdCajaGestoria);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCajaGestoria = new CajaGestoria();
		$oCajaGestoria->ParseFromArray($oRow);
		
		return $oCajaGestoria;		
	}
	
	public function GetByEntidad($IdTipoMovimiento, $IdEntidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasGestoria";
		$sql.= " WHERE IdTipoMovimiento = " . DB::Number($IdTipoMovimiento);	
		$sql.= " AND IdEntidad = " . DB::Number($IdEntidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCajaGestoria = new CajaGestoria();
		$oCajaGestoria->ParseFromArray($oRow);
		
		return $oCajaGestoria;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasGestoria mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		//$sql.= " ORDER BY mpi.Fecha";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(CajaGestoria $oCajaGestoria)
	{
		$arr = array
		(
			'Fecha'				=> DB::Date($oCajaGestoria->Fecha),
			'Monto' 			=> DB::Number($oCajaGestoria->Monto),
			'Disponible'		=> DB::Number($oCajaGestoria->Disponible),
			'IdTipoMovimiento'	=> DB::Number($oCajaGestoria->IdTipoMovimiento),
			'IdUsuario'			=> DB::Number($oCajaGestoria->IdUsuario),
			'IdEntidad'			=> DB::Number($oCajaGestoria->IdEntidad),
			'Observaciones'		=> DB::String($oCajaGestoria->Observaciones)
		);
		
		if (!$this->Insert('TB_CajasGestoria', $arr))
			return false;
		$this->ActualizarDisponibles($oCajaGestoria->Fecha);
		/* asignamos el id generado */
		$oCajaGestoria->IdCajaGestoria = DBAccess::GetLastInsertId();
			
		return $oCajaGestoria;
	}
	
	
	public function Update(CajaGestoria $oCajaGestoria)
	{
		$where = " IdCajaGestoria = " . DB::Number($oCajaGestoria->IdCajaGestoria);
		
		$arr = array
		(
			'Fecha'				=> DB::Date($oCajaGestoria->Fecha),
			'Monto' 			=> DB::Number($oCajaGestoria->Monto),
			'Disponible'		=> DB::Number($oCajaGestoria->Disponible),
			'IdTipoMovimiento'	=> DB::Number($oCajaGestoria->IdTipoMovimiento),
			'IdUsuario'			=> DB::Number($oCajaGestoria->IdUsuario),
			'IdEntidad'			=> DB::Number($oCajaGestoria->IdEntidad),
			'Observaciones'		=> DB::String($oCajaGestoria->Observaciones)
		);
		
		if (!DBAccess::Update('TB_CajasGestoria', $arr, $where))
			return false;
		
		$this->ActualizarDisponibles($oCajaGestoria->Fecha);
		
		return $oCajaGestoria;
	}
	

	public function Delete($IdCajaGestoria)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$oCajaGestoria = $this->GetById($IdCajaGestoria);
			
		$where = " IdCajaGestoria = " . DB::Number($IdCajaGestoria);

		if (!DBAccess::Delete('TB_CajasGestoria', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		$this->ActualizarDisponibles($oCajaGestoria->Fecha);
		
		return true;	
	}
}

?>