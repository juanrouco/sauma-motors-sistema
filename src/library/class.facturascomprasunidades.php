<?php 

require_once('class.dbaccess.php');
require_once('class.facturacompraunidad.php');
require_once('class.facturascompras.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.page.php');

class FacturasComprasUnidades extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_FacturasComprasUnidades mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompraUnidad = new FacturaCompraUnidad();
			$oFacturaCompraUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompraUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_FacturasComprasUnidades mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.NumeroRetencion, mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompraUnidad = new FacturaCompraUnidad();
			$oFacturaCompraUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompraUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdFacturaCompra($IdFacturaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasComprasUnidades";
		$sql.= " WHERE IdFacturaCompra = " . DB::Number($IdFacturaCompra);
		$sql.= " ORDER BY IdUnidad";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompraUnidad = new FacturaCompraUnidad();
			$oFacturaCompraUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompraUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdFacturaCompraOrdered($IdFacturaCompra)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_FacturasComprasUnidades mpi";
		$sql.= " INNER JOIN TB_Unidades u on mpi.IdUnidad = u.IdUnidad";
		$sql.= " WHERE mpi.IdFacturaCompra = " . DB::Number($IdFacturaCompra);
		$sql.= " ORDER BY mpi.Saldo ASC, IF (u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ", 0, IF (u.Pisado = 1, 1, u.IdUnidad)) ASC, IdUnidad";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompraUnidad = new FacturaCompraUnidad();
			$oFacturaCompraUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompraUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdUnidad($IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasComprasUnidades";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);
		$sql.= " ORDER BY IdFacturaCompra";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompraUnidad = new FacturaCompraUnidad();
			$oFacturaCompraUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompraUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdFacturaCompra($IdFacturaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasComprasUnidades";
		$sql.= " WHERE IdFacturaCompra = " . DB::Number($IdFacturaCompra);
		$sql.= " ORDER BY IdFacturaCompra";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))	
			return false;
		
		$oFacturaCompraUnidad = new FacturaCompraUnidad();
		$oFacturaCompraUnidad->ParseFromArray($oRow);
		
		return $oFacturaCompraUnidad;
	}

	public function GetById($IdFacturaCompraUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasComprasUnidades";
		$sql.= " WHERE IdFacturaCompraUnidad = " . DB::Number($IdFacturaCompraUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompraUnidad = new FacturaCompraUnidad();
		$oFacturaCompraUnidad->ParseFromArray($oRow);
		
		return $oFacturaCompraUnidad;		
	}
	
	
	public function GetByIdFacturaCompraAndIdUnidad($IdFacturaCompra, $IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasComprasUnidades";
		$sql.= " WHERE IdFacturaCompra = " . DB::Number($IdFacturaCompra);	
		$sql.= " AND IdUnidad = " . DB::Number($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompraUnidad = new FacturaCompraUnidad();
		$oFacturaCompraUnidad->ParseFromArray($oRow);
		
		return $oFacturaCompraUnidad;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_FacturasComprasUnidades mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(FacturaCompraUnidad $oFacturaCompraUnidad)
	{
		$arr = array
		(
			'IdFacturaCompra'	=> DB::Number($oFacturaCompraUnidad->IdFacturaCompra),
			'IdUnidad' 			=> DB::Number($oFacturaCompraUnidad->IdUnidad)
		);
		
		if (!$this->Insert('TB_FacturasComprasUnidades', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaCompraUnidad->IdFacturaCompra = DBAccess::GetLastInsertId();
			
		return $oFacturaCompraUnidad;
	}
	
	
	public function Update(FacturaCompraUnidad $oFacturaCompraUnidad)
	{
		$where = " IdFacturaCompraUnidad = " . DB::Number($oFacturaCompraUnidad->IdFacturaCompraUnidad);
		
		$arr = array
		(
			'IdFacturaCompra'	=> DB::Number($oFacturaCompraUnidad->IdFacturaCompra),
			'IdUnidad' 			=> DB::Number($oFacturaCompraUnidad->IdUnidad)
		);
		
		if (!DBAccess::Update('TB_FacturasComprasUnidades', $arr, $where))
			return false;
		
		return $oFacturaCompraUnidad;
	}
	

	public function Delete($IdFacturaCompraUnidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFacturaCompraUnidad = " . DB::Number($IdFacturaCompraUnidad);

		if (!DBAccess::Delete('TB_FacturasComprasUnidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	

	public function DeleteByFacturaCompra($IdFacturaCompra)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFacturaCompra = " . DB::Number($IdFacturaCompra);

		if (!DBAccess::Delete('TB_FacturasComprasUnidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>