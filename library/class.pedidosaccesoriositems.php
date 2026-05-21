<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.pedidoaccesorios.php');
require_once('class.pedidoaccesorioitem.php');

class PedidosAccesoriosItems extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesoriosItems";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPedidoAccesorio DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoAccesorioItem = new PedidoAccesorioItem();
			$oPedidoAccesorioItem->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoAccesorioItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByPedidoAccesorio($oPedidoAccesorio)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesoriosItems";
		$sql.= " WHERE IdPedidoAccesorio = " . DB::Number($oPedidoAccesorio->IdPedido);
	
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoAccesorioItem = new PedidoAccesorioItem();
			$oPedidoAccesorioItem->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoAccesorioItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesoriosItems";
		$sql.= " WHERE IdDetalle = " . DB::Number($IdDetalle);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPedidoAccesorioItem = new PedidoAccesorioItem();
		$oPedidoAccesorioItem->ParseFromArray($oRow);
		
		return $oPedidoAccesorioItem;		
	}
	

	public function Create(PedidoAccesorioItem $oPedidoAccesorioItem)
	{
		$arr = array
		(
			'IdPedidoAccesorio'	=> DB::Number($oPedidoAccesorioItem->IdPedidoAccesorio),
			'Detalle' 			=> DB::String($oPedidoAccesorioItem->Detalle),
			'Importe' 			=> DB::Number($oPedidoAccesorioItem->Importe),
			'IdArticulo' 		=> DB::Number($oPedidoAccesorioItem->IdArticulo)
		);
	
		if (!$this->Insert('TB_PedidosAccesoriosItems', $arr))
			return false;
			
		return $oPedidoAccesorioItem;
	}
	

	public function Delete($IdPedidoAccesorioItem)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoAccesorioItem = " . (int)$IdPedidoAccesorioItem;
		
		if ( !DBAccess::Delete('TB_PedidosAccesoriosItems', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteByPedidoAccesorio($IdPedido)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoAccesorio = " . (int)$IdPedido;
		
		if ( !DBAccess::Delete('TB_PedidosAccesoriosItems', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>