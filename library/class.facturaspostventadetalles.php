<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.facturapostventa.php');
require_once('class.facturapostventadetalle.php');

class FacturaPostVentDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentaDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFacturaPostVenta DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacutraPostVentaDetalle new FacutraPostVentaDetalle();
			$oFacutraPostVentaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oFacutraPostVentaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByFacturaPostVenta(FacturaPostVenta $oFacturaPostVenta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentaDetalles";
		$sql.= " WHERE IdFacturaPostVenta = " . DB::Number($oFacturaPostVenta->IdFacturaPostVenta);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacutraPostVentaDetalle new FacutraPostVentaDetalle();
			$oFacutraPostVentaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oFacutraPostVentaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdFacturaPostVentaDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentaDetalles";
		$sql.= " WHERE IdFacturaPostVentaDetalle = " . DB::Number($IdFacturaPostVentaDetalle);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oFacutraPostVentaDetalle new FacutraPostVentaDetalle();
		$oFacutraPostVentaDetalle->ParseFromArray($oRow);
		
		return $oFacutraPostVentaDetalle;		
	}
	

	public function Create(FacutraPostVentaDetalle $oFacutraPostVentaDetalle)
	{
		$arr = array
		(
			'IdFacturaPostVenta' 	=> DB::Number($oFacutraPostVentaDetalle->IdFacturaPostVenta),
			'Detalle' 		=> DB::String($oFacutraPostVentaDetalle->Detalle),
			'IvaGravado' 	=> DB::Bool($oFacutraPostVentaDetalle->IvaGravado),
			'Importe' 		=> DB::Number($oFacutraPostVentaDetalle->Importe)
		);
	
		if (!$this->Insert('TB_FacturasPostVentaDetalles', $arr))
			return false;
			
		return $oFacutraPostVentaDetalle;
	}
	

	public function Delete($IdFacturaPostVentaDetalle)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFacturaPostVentaDetalle = " . (int)$IdFacturaPostVentaDetalle;
		
		if ( !DBAccess::Delete('TB_FacturasPostVentaDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>