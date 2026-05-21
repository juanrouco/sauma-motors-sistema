<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.tipomovimiento.php');
require_once('class.facturaitem.php');

class FacturasItems extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasItems";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFactura DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaItem = new FacturaItem();
			$oFacturaItem->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByFactura(FacturaPostVenta $oFacturaPostVenta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasItems";
		$sql.= " WHERE IdFactura = " . DB::Number($oFacturaPostVenta->IdFacturaPostVenta);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaItem = new FacturaItem();
			$oFacturaItem->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdFactura, $IdArticulo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasItems";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
		$sql.= " AND IdArticulo = " . DB::Number($IdArticulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oFacturaItem = new FacturaItem();
		$oFacturaItem->ParseFromArray($oRow);
		
		return $oFacturaItem;		
	}


	public function GetByIdIncrement($IdFacturaItem)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasItems";
		$sql.= " WHERE IdFacturaItem = " . DB::Number($IdFacturaItem);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oFacturaItem = new FacturaItem();
		$oFacturaItem->ParseFromArray($oRow);
		
		return $oFacturaItem;		
	}
	
	private function GetArrayDB(FacturaItem $oFacturaItem)
	{
		$arr = array
		(
			'IdFactura' 		=> DB::Number($oFacturaItem->IdFactura),
			'IdFactura' 		=> DB::Number($oFacturaItem->IdFactura),
			'Descripcion'	 	=> DB::String($oFacturaItem->Descripcion),
			'Cantidad' 			=> DB::Number($oFacturaItem->Cantidad),
			'ImporteNeto' 		=> DB::Number($oFacturaItem->ImporteNeto),
			'ImporteBruto' 		=> DB::Number($oFacturaItem->ImporteBruto),
			'IdIva' 			=> DB::Number($oFacturaItem->IdIva),
			'IvaAlicuota'		=> DB::Number($oFacturaItem->IvaAlicuota),
			'Iva21'				=> DB::Number($oFacturaItem->Iva21),
			'Iva10'				=> DB::Number($oFacturaItem->Iva10),
			'IdArticulo'		=> DB::Number($oFacturaItem->IdArticulo),
			'IdTarea'			=> DB::Number($oFacturaItem->IdTarea),
			'Interes'			=> DB::Bool($oFacturaItem->Interes)
		);
		return $arr;
	}
	
	public function Create(FacturaItem $oFacturaItem)
	{
		$arr = $this->GetArrayDB($oFacturaItem);
	
		if (!$this->Insert('TB_FacturasItems', $arr))
			return false;
			
		return $oFacturaItem;
	}
	

	public function Update(FacturaItem $oFacturaItem)
	{
		$where = " IdFacturaItem = " . (int)$oFacturaItem->IdFacturaItem;		

		$arr = $this->GetArrayDB($oFacturaItem);

		if (!DBAccess::Update('TB_FacturasItems', $arr, $where))
			return false;
			
		return $oFacturaItem;
	}

	
	public function Delete($IdFacturaItem)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFacturaItem = " . (int)$IdFacturaItem;		
		
		if ( !DBAccess::Delete('TB_FacturasItems', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>