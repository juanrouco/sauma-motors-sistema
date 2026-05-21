<?php 

require_once('class.dbaccess.php');
require_once('class.cajamovimientopago.php');
require_once('class.cajamovimiento.php');
require_once('class.filter.php');
require_once('class.Pago.php');
require_once('class.page.php');

class CajasMovimientosPagos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasMovimientosPagos mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->ParseFromArray($oRow);
			
			array_push($arr, $oCajaMovimientoPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasMovimientosPagos mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.NumeroRetencion, mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->ParseFromArray($oRow);
			
			array_push($arr, $oCajaMovimientoPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdCajaMovimiento($IdCajaMovimiento)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasMovimientosPagos";
		$sql.= " WHERE IdCajaMovimiento = " . DB::Number($IdCajaMovimiento);
		$sql.= " ORDER BY IdPago";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->ParseFromArray($oRow);
			
			array_push($arr, $oCajaMovimientoPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdCajaMovimientoOrdered($IdCajaMovimiento)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasMovimientosPagos mpi";
		$sql.= " INNER JOIN TB_Pagos u on mpi.IdPago = u.IdPago";
		$sql.= " WHERE mpi.IdCajaMovimiento = " . DB::Number($IdCajaMovimiento);
		$sql.= " ORDER BY mpi.Saldo ASC, IF (u.IdEstado = " . DB::Number(EstadoPago::Reservado) . ", 0, IF (u.Pisado = 1, 1, u.IdPago)) ASC, IdPago";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->ParseFromArray($oRow);
			
			array_push($arr, $oCajaMovimientoPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdPago($IdPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasMovimientosPagos";
		$sql.= " WHERE IdPago = " . DB::Number($IdPago);
		$sql.= " ORDER BY IdCajaMovimiento";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaMovimientoPago = new CajaMovimientoPago();
			$oCajaMovimientoPago->ParseFromArray($oRow);
			
			array_push($arr, $oCajaMovimientoPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdCajaMovimiento($IdCajaMovimiento)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasMovimientosPagos";
		$sql.= " WHERE IdCajaMovimiento = " . DB::Number($IdCajaMovimiento);
		$sql.= " ORDER BY IdCajaMovimiento";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))	
			return false;
		
		$oCajaMovimientoPago = new CajaMovimientoPago();
		$oCajaMovimientoPago->ParseFromArray($oRow);
		
		return $oCajaMovimientoPago;
	}

	public function GetById($IdCajaMovimientoPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasMovimientosPagos";
		$sql.= " WHERE IdCajaMovimientoPago = " . DB::Number($IdCajaMovimientoPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCajaMovimientoPago = new CajaMovimientoPago();
		$oCajaMovimientoPago->ParseFromArray($oRow);
		
		return $oCajaMovimientoPago;		
	}
	
	
	public function GetByIdCajaMovimientoAndIdPago($IdCajaMovimiento, $IdPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CajasMovimientosPagos";
		$sql.= " WHERE IdCajaMovimiento = " . DB::Number($IdCajaMovimiento);	
		$sql.= " AND IdPago = " . DB::Number($IdPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCajaMovimientoPago = new CajaMovimientoPago();
		$oCajaMovimientoPago->ParseFromArray($oRow);
		
		return $oCajaMovimientoPago;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_CajasMovimientosPagos mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(CajaMovimientoPago $oCajaMovimientoPago)
	{
		$arr = array
		(
			'IdCajaMovimiento'	=> DB::Number($oCajaMovimientoPago->IdCajaMovimiento),
			'IdPago' 			=> DB::Number($oCajaMovimientoPago->IdPago)
		);
		
		if (!$this->Insert('TB_CajasMovimientosPagos', $arr))
			return false;

		/* asignamos el id generado */
		$oCajaMovimientoPago->IdCajaMovimiento = DBAccess::GetLastInsertId();
			
		return $oCajaMovimientoPago;
	}
	
	
	public function Update(CajaMovimientoPago $oCajaMovimientoPago)
	{
		$where = " IdCajaMovimientoPago = " . DB::Number($oCajaMovimientoPago->IdCajaMovimientoPago);
		
		$arr = array
		(
			'IdCajaMovimiento'	=> DB::Number($oCajaMovimientoPago->IdCajaMovimiento),
			'IdPago' 			=> DB::Number($oCajaMovimientoPago->IdPago)
		);
		
		if (!DBAccess::Update('TB_CajasMovimientosPagos', $arr, $where))
			return false;
		
		return $oCajaMovimientoPago;
	}
	

	public function Delete($IdCajaMovimientoPago)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCajaMovimientoPago = " . DB::Number($IdCajaMovimientoPago);

		if (!DBAccess::Delete('TB_CajasMovimientosPagos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	

	public function DeleteByCajaMovimiento($IdCajaMovimiento)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCajaMovimiento = " . DB::Number($IdCajaMovimiento);

		if (!DBAccess::Delete('TB_CajasMovimientosPagos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>