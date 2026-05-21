<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.notacredito.php');
require_once('class.notacreditodetalle.php');

class NotasCreditoDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasCreditoDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdNotaCredito DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oNotaCreditoDetalle = new NotaCreditoDetalle();
			$oNotaCreditoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oNotaCreditoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByNotaCredito(NotaCredito $oNotaCreditoDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasCreditoDetalles";
		$sql.= " WHERE IdNotaCredito = " . DB::Number($oNotaCreditoDetalle->IdNotaCredito);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oNotaCreditoDetalle = new NotaCreditoDetalle();
			$oNotaCreditoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oNotaCreditoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdNotaCreditoDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasCreditoDetalles";
		$sql.= " WHERE IdNotaCreditoDetalle = " . DB::Number($IdNotaCreditoDetalle);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oNotaCreditoDetalle = new NotaCreditoDetalle();
		$oNotaCreditoDetalle->ParseFromArray($oRow);
		
		return $oNotaCreditoDetalle;		
	}
	

	public function Create(NotaCreditoDetalle $oNotaCreditoDetalle)
	{
		$arr = array
		(
			'IdNotaCredito' 	=> DB::Number($oNotaCreditoDetalle->IdNotaCredito),
			'Detalle' 		=> DB::String($oNotaCreditoDetalle->Detalle),
			'IdIva' 	=> DB::Number($oNotaCreditoDetalle->IdIva),
			'Importe' 		=> DB::Number($oNotaCreditoDetalle->Importe)
		);
	
		if (!$this->Insert('TB_NotasCreditoDetalles', $arr))
			return false;
			
		return $oNotaCreditoDetalle;
	}
	

	public function Delete($IdNotaCreditoDetalle)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdNotaCreditoDetalle = " . (int)$IdNotaCreditoDetalle;
		
		if ( !DBAccess::Delete('TB_NotasCreditoDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>