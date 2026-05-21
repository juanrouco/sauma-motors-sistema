<?php 

require_once('class.dbaccess.php');
require_once('class.cajadetalledefault.php');
require_once('class.filter.php');
require_once('class.page.php');

class CajasDetallesDefault extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['IdTipoPago']) && $filter['IdTipoPago'] != '')
			$sql.= ' AND cdd.IdTipoPago = ' . DB::Number($filter['IdTipoPago']);
		
		if (isset($filter['IdUbicacion']) && $filter['IdUbicacion'] != '')
			$sql.= ' AND cdd.IdUbicacion = ' . DB::Number($filter['IdUbicacion']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT cdd.*";
		$sql.= " FROM TB_CajasDetallesDefault cdd";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY cdd.IdUbicacion DESC, cdd.IdTipoPago DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaDetalleDefault = new CajaDetalleDefault();
			$oCajaDetalleDefault->ParseFromArray($oRow);
			
			array_push($arr, $oCajaDetalleDefault);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdTipoPago, $IdUbicacion)
	{
		$sql = "SELECT cdd.*";
		$sql.= " FROM TB_CajasDetallesDefault cdd";
		$sql.= " WHERE IdTipoPago = " . DB::Number($IdTipoPago);
		$sql.= " AND IdUbicacion = " . DB::Number($IdUbicacion);
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCajaDetalleDefault = new CajaDetalleDefault();
		$oCajaDetalleDefault->ParseFromArray($oRow);
		
		return $oCajaDetalleDefault;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT cdd.*";
		$sql.= " FROM TB_CajasDetallesDefault cdd";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(CajaDetalleDefault $oCajaDetalleDefault)
	{
		$filter = array
		(
			'IdTipoPago'	=> $oCajaDetalleDefault->IdTipoPago,
			'IdUbicacion'	=> $oCajaDetalleDefault->IdUbicacion
		);
		if ($this->GetCountRows($filter) > 0)
			return false;
		
		$arr = array
		(
			'IdTipoPago'			=> DB::Number($oCajaDetalleDefault->IdTipoPago),
			'IdUbicacion'			=> DB::Number($oCajaDetalleDefault->IdUbicacion),
			'IdCajaAdministracion' 	=> DB::Number($oCajaDetalleDefault->IdCajaAdministracion),
			'IdCajaTaller' 			=> DB::Number($oCajaDetalleDefault->IdCajaTaller),
			'IdCajaRepuestos'		=> DB::Number($oCajaDetalleDefault->IdCajaRepuestos)
		);
		
		if (!$this->Insert('TB_CajasDetallesDefault', $arr))
			return false;
		
		return $oCajaDetalleDefault;
	}
	
	
	public function Update(CajaDetalleDefault $oCajaDetalleDefault)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$where = " IdTipoPago = " . DB::Number($oCajaDetalleDefault->IdTipoPago) . " AND IdUbicacion = " . DB::Number($oCajaDetalleDefault->IdUbicacion);
		
		$arr = array
		(
			'IdCajaAdministracion' 	=> DB::Number($oCajaDetalleDefault->IdCajaAdministracion),
			'IdCajaTaller' 			=> DB::Number($oCajaDetalleDefault->IdCajaTaller),
			'IdCajaRepuestos'		=> DB::Number($oCajaDetalleDefault->IdCajaRepuestos)
		);
		
		if (!DBAccess::Update('TB_cajasdetallesdefault', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return $oCajaDetalleDefault;
	}
	

	public function Delete(CajaDetalleDefault $oCajaDetalleDefault)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where  = " IdTipoPago = " . DB::Number($oCajaDetalleDefault->IdTipoPago);
		$where .= " IdUbicacion = " . DB::Number($oCajaDetalleDefault->IdUbicacion);

		if (!DBAccess::Delete('TB_CajasDetallesDefault', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>