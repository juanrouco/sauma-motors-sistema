<?php 

require_once('class.dbaccess.php');
require_once('class.pagopv.php');
require_once('class.facturascompras.php');
require_once('class.filter.php');
require_once('class.page.php');

class PagosPV extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdFacturaPostVenta'])) && ($filter['IdFacturaPostVenta'] != ''))
			$sql.= " AND IdFacturaPostVenta = " . DB::Number($filter['IdFacturaPostVenta']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPago DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPagoPVPV = new PagoPV();
			$oPagoPVPV->ParseFromArray($oRow);
			
			array_push($arr, $oPagoPVPV);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosPV";
		$sql.= " WHERE IdPago = " . DB::Number($IdPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPagoPV = new PagoPV();
		$oPagoPV->ParseFromArray($oRow);
		
		return $oPagoPV;		
	}
	

	public function GetByIdFacturaPostVenta($IdFacturaPostVenta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_PagosPV fu";
		$sql.= " WHERE fu.IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPagoPV = new PagoPV();
			$oPagoPV->ParseFromArray($oRow);
			
			array_push($arr, $oPagoPV);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(PagoPV $oPagoPV)
	{
		$arr = array
		(
			'IdFacturaPostVenta' 	=> DB::Number($oPagoPV->IdFacturaPostVenta),
			'Fecha' 				=> DB::Date($oPagoPV->Fecha),
			'Importe' 				=> DB::Number($oPagoPV->Importe)
		);
		
		return $arr;
	}
	
	public function Create(PagoPV $oPagoPV)
	{
		$arr = $this->GetArrayDB($oPagoPV);
		
		if (!$this->Insert('TB_PagosPV', $arr))
			return false;

		/* asignamos el id generado */
		$oPagoPV->IdPago = DBAccess::GetLastInsertId();
			
		return $oPagoPV;
	}
	
	public function Update(PagoPV $oPagoPV)
	{
		$where = " IdPago = " . DB::Number($oPagoPV->IdPago);
		
		$arr = $this->GetArrayDB($oPagoPV);
		
		if (!DBAccess::Update('TB_PagosPV', $arr, $where))
			return false;
		
		return $oPagoPV;
	}
	
	
	public function Delete($IdPago)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPago = " . DB::Number($IdPago);

		if (!DBAccess::Delete('TB_PagosPV', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	
}

?>