<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.facturavaria.php');
require_once('class.facturavariadetalle.php');

class FacturaVariaDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVariaDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFactura DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaVariaDetalle = new FacturaVariaDetalle();
			$oFacturaVariaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaVariaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByFacturaVaria(FacturaVaria $oFacturaVaria)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVariaDetalles";
		$sql.= " WHERE IdFactura = " . DB::Number($oFacturaVaria->IdFactura);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaVariaDetalle = new FacturaVariaDetalle();
			$oFacturaVariaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaVariaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVariaDetalles";
		$sql.= " WHERE IdDetalle = " . DB::Number($IdDetalle);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oFacturaVariaDetalle = new FacturaVariaDetalle();
		$oFacturaVariaDetalle->ParseFromArray($oRow);
		
		return $oFacturaVariaDetalle;		
	}
	

	public function Create(FacturaVariaDetalle $oFacturaVariaDetalle)
	{
		$arr = array
		(
			'IdFactura' 	=> DB::Number($oFacturaVariaDetalle->IdFactura),
			'Detalle' 		=> DB::String($oFacturaVariaDetalle->Detalle),
			'IvaGravado' 	=> DB::Bool($oFacturaVariaDetalle->IvaGravado),
			'Importe' 		=> DB::Number($oFacturaVariaDetalle->Importe)
		);
	
		if (!$this->Insert('TB_FacturaVariaDetalles', $arr))
			return false;
			
		return $oFacturaVariaDetalle;
	}
	

	public function Delete($IdDetalle)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdDetalle = " . (int)$IdDetalle;
		
		if ( !DBAccess::Delete('TB_FacturaVariaDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	

	public function GetAllByFacturaVariaAndConcepto(FacturaVaria $oFacturaVaria, $Concepto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVariaDetalles";
		$sql.= " WHERE IdFactura = " . DB::Number($oFacturaVaria->IdFactura);
		$sql.= " AND Detalle = " . DB::String($Concepto);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oFacturaVariaDetalle = new FacturaVariaDetalle();
		$oFacturaVariaDetalle->ParseFromArray($oRow);
		
		return $oFacturaVariaDetalle;		
	}
}

?>